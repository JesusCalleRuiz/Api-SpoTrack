<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI;
class ChatbotController extends Controller
{
    public function handleMessage(Request $request)
    {
        $userMessage = $request->input('message');

        $client = OpenAI::client(env('OPENAI_API_KEY'));
        try {
            $response = $client->chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un asistente para una aplicación deportiva llamada Spotrack. Ayudas con soporte técnico y recomendaciones de rutas.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $userMessage,
                    ],
                ],
                'max_tokens' => 150,
                'temperature' => 0.7,
            ]);

            $reply = $response['choices'][0]['message']['content'];
            return response()->json(['reply' => $reply]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Hubo un problema al procesar tu mensaje.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
