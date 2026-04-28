<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\User;
use App\Events\MessageSent;
class ChatController extends Controller
{

    public function index()
    {
        return view('admin.chat.index');
    }


    // GET /admin/api/sessions
    public function sessions()
    {
        $latestMessageIds = Message::whereNotNull('user_id')
            ->selectRaw('MAX(id) as id')
            ->groupBy('user_id');

        $latestMessages = Message::with('user:id,name,email')
            ->whereIn('id', $latestMessageIds)
            ->latest('created_at')
            ->get();

        $result = $latestMessages->map(function ($message) {
            $u = $message->user;
            return [
                'user_id' => $message->user_id,
                'session_id' => $message->session_id,
                'last_message_at' => $message->created_at,
                'user' => $u ? ['id' => $u->id, 'name' => $u->name, 'email' => $u->email] : null,
                'last_message' => [
                    'id' => $message->id,
                    'sender' => $message->sender,
                    'message' => $message->message,
                    'created_at' => $message->created_at,
                ],
            ];
        })->values();

        return response()->json($result);
    }

    // GET /admin/api/users-with-messages
    public function usersWithMessages()
    {
        $userIds = Message::whereNotNull('user_id')->distinct()->pluck('user_id');
        $users = User::whereIn('id', $userIds)->get();
        return response()->json($users);
    }

    // POST /admin/api/start-session
    public function startSession(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        // Generate a new session_id for this user
        $session_id = uniqid('admin_', true);
        // Optionally, create a welcome message
        $msg = Message::create([
            'session_id' => $session_id,
            'user_id' => $request->user_id,
            'sender' => 'admin',
            'message' => 'Hello! This is the admin. How can I help you?',
        ]);
        return response()->json(['session_id' => $session_id, 'message' => $msg]);
    }

    // GET /admin/api/session/{session_id}
    public function sessionMessages($session_id)
    {
        $messages = Message::where('session_id', $session_id)->orderBy('created_at')->get();
        return response()->json($messages);
    }

    public function reply(Request $request, $session_id)
    {
        $request->validate(['message' => 'required|string|max:2000']);

        // Attempt to link this admin reply to the user_id for the session so
        // the user's ensureSession() and fetch endpoints can easily discover
        // the correct session. If no user_id can be found for the session we
        // leave it null (backwards compatible).
        $userId = Message::where('session_id', $session_id)->whereNotNull('user_id')->value('user_id');

        $msg = Message::create([
            'session_id' => $session_id,
            'user_id' => $userId,
            'sender' => 'admin',
            'message' => $request->input('message'),
            'admin_id' => auth()->id(),
        ]);

        // broadcast so frontend receives it in real time
        broadcast(new MessageSent($msg))->toOthers();

        return response()->json([
            'success' => true,
            'message' => $msg
        ]);
    }

}
