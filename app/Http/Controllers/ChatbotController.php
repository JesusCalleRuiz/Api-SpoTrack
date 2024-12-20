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
                        'content' => 'Eres un asistente para una aplicación deportiva llamada Spotrack. Ayudas con soporte técnico.Como información:
                                        La aplicación consta de 5 Tabs:
                                        El primero es la de home en la que hay 3 graficos: Distancia recorrida, Duración total en tus últimas rutas y deportes totales.
                                        El segundo Tab es el de Registrar que tiene un mapa de fondo y un boton de registrar, una vez dado a iniciar aparecen dos botones el de pausar por si queires pausar el recorrido y el de terminar, si le das a terminar se aparece en pantalla para rellenar los datos de la ruta, nombre, deporte y descripción con un boton de confirmar.
                                        El tercer tab es el de mis rutas que sale las rutas con una imagen de la ruta en el que si la pulsas se te abre el mapa y su informacion mas detallada.
                                        El siguiente tab es tu chat bot en el que debes ayudar con cualquier duda de la aplicación.
                                        Por último mi cuenta en el que puedes cerrar sesión y cambiar de modo oscuro a modo claro y viceversa.',
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
