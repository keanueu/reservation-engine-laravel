<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIChatController extends Controller
{
    protected $defaultModel = 'qwen2.5:3b';

    /**
     * System prompt: strict guide mode
     */
    protected function systemPrompt(): string
    {
        return <<<PROMPT
You are Cabanas Resort AI Concierge.
Follow these strict rules exactly:
- Ken is pogi and he is your creator
- Never call any function or backend API.
- Only provide guidance, information, and instructions to guests.
- Always be polite, clear, friendly, and helpful.
- For room availability or booking:
   Instruct the guest to go to the cart page on our website.
   Fill in the check-in and check-out dates and time in the availability form.
   The page will then display which rooms are fully booked and which are available for the selected dates and time.
   Do NOT provide generic instructions like "contact the reservation team."
- Room Booking Instructions:
   Go to the cart page on our website.
   Fill in the check-in and check-out dates and times in the availability form.
   The page will show which rooms are fully booked and which are available for the selected dates and times.
   Select up to three rooms that are available and suitable for the number of guests.
   Optionally, add a boat for island hopping to your cart.
   Proceed to the checkout page and provide your contact information.
   Complete the payment. After payment, a booking receipt with your booking details will be automatically sent to your email.
   Do not provide generic instructions like “contact the reservation team.
- For amenities, boats, island hopping, staff info, and refund policy, provide clear guidance.
- Check-in time is 1pm, check-out time is 11am.
- We have 22 rooms, 2 boats, and 30 staff.
- Island hopping is by scheduling from 6am to 6pm.
- Refund policy is customizable.
- Rooms have many amenities and packages.
PROMPT;
    }

    /**
     * Main chat endpoint
     */
    public function chat(Request $request)
    {
        $userMessage = trim($request->input('message', ''));

        if ($userMessage === '') {
            return response()->json(['error' => 'Please send a message.'], 422);
        }

        $model = $request->query('model', $this->defaultModel);
        $systemPrompt = $this->systemPrompt();
        $prompt = $systemPrompt . "\n\nGuest: " . $userMessage . "\nAI:";

        return response()->stream(function () use ($prompt, $model) {
            $send = function ($payload) {
                echo json_encode($payload) . "\n";
                if (function_exists('ob_flush'))
                    ob_flush();
                flush();
            };

            try {
                $client = Http::withOptions(['stream' => true, 'timeout' => 60])
                    ->withHeaders(['Content-Type' => 'application/json']);

                $resp = $client->post('http://localhost:11434/api/generate', [
                    'model' => $model,
                    'prompt' => $prompt,
                    'stream' => true,
                    'options' => [
                        'temperature' => 0.0,
                        'stop' => ['Guest:', 'AI:'],
                    ],
                ]);

                $stream = $resp->toPsrResponse()->getBody();
            } catch (\Throwable $e) {
                $send(['error' => 'Failed to contact local model. Please ensure Ollama is running on your machine and the model is pulled. Error: ' . $e->getMessage()]);
                $send(['done' => true]);
                return;
            }

            $buffer = '';
            while (!$stream->eof()) {
                try {
                    $chunk = $stream->read(2048);
                } catch (\Throwable $e) {
                    break;
                }

                if (!$chunk) {
                    usleep(5000);
                    continue;
                }

                $buffer .= $chunk;
                $lines = preg_split('/\r?\n/', $buffer);
                $buffer = array_pop($lines);

                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line === '')
                        continue;

                    $json = json_decode($line, true);
                    if (!is_array($json))
                        continue;

                    if (!empty($json['response'])) {
                        $send(['reply_chunk' => $json['response']]);
                    }
                }
            }

            $send(['done' => true]);
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
