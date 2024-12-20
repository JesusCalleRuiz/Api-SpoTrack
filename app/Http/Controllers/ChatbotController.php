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
        $response = $client->completions()->create([
            'model' => 'text-davinci-003', //modelo de lenguaje
            'prompt' => "Eres un asistente para una aplicación deportiva. Responde basándote en estas preguntas frecuentes:\n\n" .
                "1. ¿Cómo grabo una ruta?\n" .
                "2. ¿Cómo consulto mis estadísticas?\n" .
                "3. ¿Qué rutas recomiendas?\n\n" .
                "Usuario: {$userMessage}\nAsistente:",
            'max_tokens' => 150,
            'temperature' => 0.7,
        ]);

        $reply = trim($response['choices'][0]['text']);
        return response()->json(['reply' => $reply]);
    }
}
