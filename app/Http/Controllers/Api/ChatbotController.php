<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
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
            $session = Str::uuid()->toString();
            $request->session()->put('chat_session_id', $session);
            return $session;
        }

        // For guest users
        $session = $request->session()->get('chat_session_id');
        if (!$session) {
            $session = Str::uuid()->toString();
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
        $userText = trim($request->input('message'));

        // Save user message
        $userMessage = Message::create([
            'session_id' => $session,
            'user_id' => auth()->check() ? auth()->id() : null,
            'sender' => 'user',
            'message' => $userText,
            'meta' => $request->input('meta', null),
        ]);

        // Broadcast user message
        broadcast(new MessageSent($userMessage))->toOthers();

        // Process message via Gemini
        $aiResponseData = $this->generateGeminiResponse($session, $userText);

        // Save AI reply
        $aiMessage = Message::create([
            'session_id' => $session,
            'sender' => 'ai',
            'message' => $aiResponseData['message'],
            'requires_admin' => $aiResponseData['requires_admin'] ?? false,
            'meta' => [
                'intent' => $aiResponseData['intent'] ?? 'booking',
                'quick_replies' => $aiResponseData['quick_replies'] ?? [],
            ],
        ]);

        // Broadcast AI message
        broadcast(new MessageSent($aiMessage))->toOthers();

        return response()->json([
            'success' => true,
            'messages' => [$userMessage, $aiMessage],
        ]);
    }

    // POST /api/chat/quick-reply
    public function quickReply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'option' => 'required|string|max:200',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $session = $this->ensureSession($request);
        $option = trim($request->input('option'));

        // Save selection as user message
        $userMessage = Message::create([
            'session_id' => $session,
            'user_id' => auth()->check() ? auth()->id() : null,
            'sender' => 'user',
            'message' => $option,
            'meta' => $request->input('meta', null),
        ]);

        // Broadcast selection
        broadcast(new MessageSent($userMessage))->toOthers();

        // Process option via Gemini
        $aiResponseData = $this->generateGeminiResponse($session, $option);

        // Save AI response
        $aiMessage = Message::create([
            'session_id' => $session,
            'sender' => 'ai',
            'message' => $aiResponseData['message'],
            'requires_admin' => $aiResponseData['requires_admin'] ?? false,
            'meta' => [
                'intent' => $aiResponseData['intent'] ?? 'booking',
                'quick_replies' => $aiResponseData['quick_replies'] ?? [],
            ],
        ]);

        // Broadcast AI reply
        broadcast(new MessageSent($aiMessage))->toOthers();

        return response()->json([
            'success' => true,
            'messages' => [$userMessage, $aiMessage],
        ]);
    }

    /**
     * Highly optimized Gemini 2.5 Pro response generator with caching,
     * sentiment analysis, guardrails, and real-time database context.
     */
    protected function generateGeminiResponse($session, $userText)
    {
        // 1. Check local query cache to optimize performance and reduce Gemini costs
        $cacheKey = 'chatbot_gemini_' . md5($session . '_' . strtolower($userText));
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // 2. Perform local fast sentiment check (angry terms trigger frontdesk attention)
        $frustrationPattern = '/(angry|sucks|terrible|scam|worst|crap|stole|refund me|call manager|legal|police)/i';
        $requiresAdmin = preg_match($frustrationPattern, $userText) === 1;

        // 3. Compile Real-Time Database Room Context (Cached for 15 minutes)
        $roomsContext = Cache::remember('chatbot_rooms_context', 900, function () {
            try {
                $rooms = \App\Models\Room::select('room_name', 'price', 'accommodates', 'beds')->get();
                if ($rooms->isEmpty()) {
                    return "No rooms are defined in the database.";
                }
                $text = "Available Room Types & Prices:\n";
                foreach ($rooms as $r) {
                    $price = is_numeric($r->price) ? 'PHP ' . number_format($r->price, 2) : 'Pricing not set';
                    $text .= "- {$r->room_name} (Accommodates: {$r->accommodates} guests, Beds: {$r->beds}) price: {$price}\n";
                }
                return $text;
            } catch (\Throwable $e) {
                Log::error('Chatbot room query failed: ' . $e->getMessage());
                return "Resort Rooms: Standard Kubo, Deluxe Kubo, Beachfront Cabana.";
            }
        });

        // 4. Compile Dynamic Guest Reservation Context
        $bookingContext = "";
        if (auth()->check()) {
            try {
                $booking = \App\Models\Booking::where('email', auth()->user()->email)
                    ->whereIn('status', ['confirmed', 'checked_in', 'waiting', 'approved', 'pending'])
                    ->latest()
                    ->first();
                if ($booking) {
                    $roomName = $booking->room->room_name ?? 'Room';
                    $bookingContext = "\nAuthenticated Guest Info & Current Confirmed Booking:\n" .
                        "- Guest Name: " . auth()->user()->name . "\n" .
                        "- Booking Status: " . ucfirst($booking->status) . "\n" .
                        "- Room Reserved: " . $roomName . "\n" .
                        "- Check-in Date: " . $booking->start_date . "\n" .
                        "- Check-out Date: " . $booking->end_date . "\n" .
                        "- Total Amount Paid: PHP " . number_format($booking->total_amount, 2) . "\n";
                }
            } catch (\Throwable $e) {
                Log::error('Chatbot booking context failed: ' . $e->getMessage());
            }
        }

        // 5. Structure System Instructions (Safety Guardrails, Dynamic Features, and JSON Schema)
        $systemPrompt = "You are a professional booking assistant for Cabanas Beach Resort.

Your tasks:
- Help guests book rooms
- Suggest room types
- Explain pricing
- Be friendly and concise
- Encourage reservations
- Ask follow-up questions

AI SAFETY RULES (GUARDRAILS):
- No Fake Prices: You MUST only state prices explicitly provided in the 'Available Room Types & Prices' context. If a price is not set or defined, politely direct the user to contact the front desk.
- No Phantom Availability: Never guarantee or promise a specific room availability unless it is explicitly active in the database.
- Private Data Shield: Do not under any circumstances share or reveal user passwords, payment checkout links, or other guests' private details.

DYNAMIC SMART CAPABILITIES:
- Room Recommendations: Suggest the best matching room based on guest count, beds, and vacation context (e.g. couples, honeymoons, family group).
- Itinerary building: If the guest asks for local activities, suggest an elegant 3-day itinerary centered on Tambobong Beach, Dasol, Pangasinan.
- FAQ Assistant: Instantly answer standard inquiries (Check-in time is 2:00 PM, Check-out is 12:00 PM, pets are welcome).
- Booking Summaries: When referencing their current confirmed booking context, generate a beautifully bulleted luxury summary.

RESPONSE SCHEMATIC FORMAT:
You MUST respond with a valid JSON object matching this schema exactly:
{
  \"message\": \"Your warm, luxury human response written in plain text or markdown formatting.\",
  \"intent\": \"The classified user intent from ['booking', 'pricing', 'cancellation', 'amenities', 'location', 'schedule'].\",
  \"quick_replies\": [\"Reply Button Option 1\", \"Reply Button Option 2\", \"Reply Button Option 3\"],
  \"requires_admin\": false
}

Note:
- Provide exactly three highly contextual suggested quick reply phrases in the array (e.g. if inquiring about booking, suggest quick check-ins or details).
- If the user expresses extreme anger, frustration, or demands direct manual human support, set 'requires_admin' to true.";

        // 6. Compile Conversation History (Conversational Memory)
        try {
            $pastMessages = Message::where('session_id', $session)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->reverse();

            $contents = [];
            foreach ($pastMessages as $msg) {
                $role = $msg->sender === 'user' ? 'user' : 'model';
                $contents[] = [
                    'role' => $role,
                    'parts' => [
                        ['text' => $msg->message]
                    ]
                ];
            }

            // Fallback if no history yet
            if (empty($contents)) {
                $contents[] = [
                    'role' => 'user',
                    'parts' => [['text' => $userText]]
                ];
            }
        } catch (\Throwable $e) {
            Log::error('History compilation failed: ' . $e->getMessage());
            $contents = [
                [
                    'role' => 'user',
                    'parts' => [['text' => $userText]]
                ]
            ];
        }

        // 7. Dispatch Gemini API request with robust connection error handling
        $apiKey = config('services.gemini.key');
        $model = config('services.gemini.model', 'gemini-2.5-pro');

        if (empty($apiKey)) {
            Log::warning('Gemini API key is not configured. Falling back to default responses.');
            return $this->getFallbackResponse($userText, $requiresAdmin);
        }

        $rawText = '';
        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
            
            $response = Http::timeout(15)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'contents' => $contents,
                    'systemInstruction' => [
                        'parts' => [['text' => $systemPrompt . "\n\nReal-time Database Context:\n" . $roomsContext . "\n" . $bookingContext]]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json'
                    ]
                ]);

            if ($response->successful()) {
                $rawResult = $response->json();
                $rawText = $rawResult['candidates'][0]['content']['parts'][0]['text'] ?? '';
            } else {
                Log::error("Gemini API request failed for model {$model}. Status: " . $response->status() . " Response: " . $response->body());
            }
        } catch (\Throwable $e) {
            Log::error("Exception occurred while calling model {$model}: " . $e->getMessage());
        }

        if (empty($rawText)) {
            return $this->getFallbackResponse($userText, $requiresAdmin);
        }

        try {
            $parsedData = json_decode($rawText, true);

            if (!isset($parsedData['message'])) {
                throw new \Exception("Missing message key in Gemini response JSON");
            }

            // Enforce local sentiment override
            if ($requiresAdmin) {
                $parsedData['requires_admin'] = true;
            }

            // Cache dynamic answer for 5 minutes
            Cache::put($cacheKey, $parsedData, 300);

            return $parsedData;

        } catch (\Throwable $e) {
            Log::error('Gemini parsing exception: ' . $e->getMessage() . ' for response: ' . $rawText);
            return $this->getFallbackResponse($userText, $requiresAdmin);
        }
    }

    /**
     * Rock-solid fallback response handler in case of API limits or down status
     */
    protected function getFallbackResponse($userText, $requiresAdmin = false)
    {
        $message = "Thank you for messaging us. Our booking system is processing your inquiry. Please wait for the admin or front desk team to assist you further.";
        $intent = "booking";
        $quickReplies = ["Check Rooms", "Resort Location", "Contact Support"];

        if (preg_match('/(price|pricing|cost|how much)/i', $userText)) {
            $message = "Our deluxe Kubos start at PHP 3,500.00 and Beachfront Cabanas start at PHP 5,000.00. Please let me know how many guests you are planning to bring so I can recommend the best rates!";
            $intent = "pricing";
            $quickReplies = ["Deluxe Kubo", "Beachfront Cabana", "Book Stay"];
        } elseif (preg_match('/(amenit|pool|beach|food)/i', $userText)) {
            $message = "Cabanas Beach Resort features pristine white sand beachfront access, premium infinity swimming pools, local restaurants, and custom guided boat tours in Tambobong Beach.";
            $intent = "amenities";
            $quickReplies = ["Boat Tours", "Pool Access", "View Rooms"];
        }

        return [
            'message' => $message,
            'intent' => $intent,
            'quick_replies' => $quickReplies,
            'requires_admin' => $requiresAdmin || true, // default fallback requires admin check
        ];
    }
}
