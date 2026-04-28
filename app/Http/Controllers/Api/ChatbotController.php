<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Events\MessageSent; 

class ChatbotController extends Controller
{
    // ensure session_id exists
  protected function ensureSession(Request $request)
{
    if (auth()->check()) {
        // If the user is logged in, use their existing chat session if available
        $existing = Message::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->first();

        if ($existing) {
            return $existing->session_id;
        }

        // Otherwise, create a new one for this user
        $session = \Str::uuid()->toString();
        $request->session()->put('chat_session_id', $session);
        return $session;
    }

    // For guest users
    $session = $request->session()->get('chat_session_id');
    if (!$session) {
        $session = \Str::uuid()->toString();
        $request->session()->put('chat_session_id', $session);
    }
    return $session;
}


    // GET /api/chat/fetch
    public function fetchMessages(Request $request)
    {
        $session = $this->ensureSession($request);
        $messages = Message::where('session_id', $session)
            ->orderBy('created_at')
            ->get();

        return response()->json(['session_id' => $session, 'messages' => $messages]);
    }

    // POST /api/chat/send
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:2000',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $session = $this->ensureSession($request);

        //  Create user message
        $userMessage = Message::create([
            'session_id' => $session,
            'user_id' => auth()->check() ? auth()->id() : null,
            'sender' => 'user',
            'message' => $request->input('message'),
            'meta' => $request->input('meta', null),
        ]);

        // Broadcast user message
        broadcast(new MessageSent($userMessage))->toOthers();

        // Re-create a single, static acknowledgment message so users receive an
        // immediate confirmation when they send a message. This replaces the
        // earlier rule-based auto-replies and avoids generating multiple AI
        // responses. The message is marked `requires_admin` so it can be surfaced
        // to staff if needed.
        $ackText = 'Thank you for messaging us. Please wait for the admin to be online to assist you.';

        $aiAck = Message::create([
            'session_id' => $session,
            'sender' => 'ai',
            'message' => $ackText,
            'requires_admin' => true,
        ]);

        // Broadcast both messages so connected clients (and admins) see them in real-time.
        broadcast(new MessageSent($aiAck))->toOthers();

        return response()->json([
            'success' => true,
            'messages' => [$userMessage, $aiAck],
        ]);
    }

    // Optional: quickReply endpoint
    public function quickReply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'option' => 'required|string|max:200',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $option = $request->input('option');
        $session = $this->ensureSession($request);

        // Persist the user's quick selection as a message
        $userMessage = Message::create([
            'session_id' => $session,
            'user_id' => auth()->check() ? auth()->id() : null,
            'sender' => 'user',
            'message' => $option,
            'meta' => $request->input('meta', null),
        ]);

        broadcast(new MessageSent($userMessage))->toOthers();

        // Prepare an AI reply that mirrors the quick option so it is stored server-side
        $replyText = null;
        // Book A Room: list first few rooms
        if (preg_match('/book/i', $option)) {
            $rooms = \App\Models\Room::orderBy('id')->limit(6)->get();
            if ($rooms->count()) {
                $replyText = "Available rooms:\n";
                foreach ($rooms as $i => $r) {
                    $price = is_numeric($r->price) ? 'PHP ' . number_format($r->price, 2) : 'Price not set';
                    $replyText .= ($i+1) . ") {$r->room_name} — {$price}\n";
                }
            } else {
                $replyText = "No rooms found at the moment. Please try again later.";
            }
        } elseif (preg_match('/amenit/i', $option)) {
            // Fallback amenities: try to fetch unique amenities if model exists
            try {
                $amenities = \App\Models\Amenity::pluck('name')->unique()->take(10)->toArray();
            } catch (\Throwable $e) {
                $amenities = [];
            }
            if (!empty($amenities)) {
                $replyText = "Amenities:\n";
                foreach ($amenities as $k => $v) {
                    $replyText .= ($k + 1) . ") " . $v . "\n";
                }
            } else {
                $replyText = "We offer beach access, swimming pool, restaurant, and guided tours. Tell me which you'd like to know more about.";
            }
        } elseif (preg_match('/front/i', $option) || preg_match('/contact/i', $option)) {
            // Contact info from settings or fallback
            try {
                $phone = \App\Models\Setting::get('contact_phone');
                $email = \App\Models\Setting::get('contact_email');
            } catch (\Throwable $e) {
                $phone = null; $email = null;
            }
            $phone = $phone ?: 'No phone set';
            $email = $email ?: 'No email set';
            $replyText = "Front Desk Contact:\nPhone: {$phone}\nEmail: {$email}\n\nYou can call or email our front desk, or tell me here what you need help with.";
        }

        // Default acknowledgment if no specialized reply
        if (empty($replyText)) {
            $replyText = 'Thank you for your message. Please wait while we connect you with the front desk.';
        }

        $aiMessage = Message::create([
            'session_id' => $session,
            'sender' => 'ai',
            'message' => $replyText,
            'requires_admin' => true,
        ]);

        broadcast(new MessageSent($aiMessage))->toOthers();

        return response()->json([
            'success' => true,
            'messages' => [$userMessage, $aiMessage],
        ]);
    }
}
