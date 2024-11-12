<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="SPOTRACK API",
 *     version="1.0",
 *     description="L5 Swagger OpenApi para la interface de recepcion de rutas generadas por SpoTrack",
 *     @OA\Contact(
 *          email="jesus.calle.ruiz8@gmail.com"
 *     ),
 * )
 */

class RouteController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/route",
     *     tags={"Route"},
     *     summary="Almacenar ruta de un usuario",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={
     *                          "user_id", "name", "distance","duration","path","average_speed","max_speed"
     *                      },
     *            @OA\Property(property="user_id", type="integer", example="1"),
     *            @OA\Property(property="name", type="string", minLength=1, maxLength=32, example="Mi primera ruta"),
     *            @OA\Property(property="distance", type="string", minLength=1, maxLength=32, example="10km"),
     *            @OA\Property(property="duration", type="string", minLength=1, maxLength=32, example="1h 30m"),
     *            @OA\Property(property="path", type="string", minLength=1, maxLength=32, example="?"),
     *            @OA\Property(property="average_speed", type="string", minLength=1, maxLength=32, example="10km/h"),
     *            @OA\Property(property="max_speed", type="string", minLength=1, maxLength=32, example="30km/h"),
     *         ),
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Resultado del registro de lal ruta",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="error", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Route has been recorded"),
     *          )
     *     ),
     *     @OA\Response(
     *         response=400,
     *          description="Error asociado al registro de la ruta",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="error", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Missing required parameter route"),
     *           )
     *     )
     * )
     */
    public function store(Request $r):JsonResponse {
        $statusCode = 201;

        $name = $r->get('name');
        if($name == null){
            return response()->json(['success'=>false, 'error'=>true, 'message'=>'Missing required parameter route'], 400);
        }

        $path = $r->get('path');
        if($path == null){
            return response()->json(['success'=>false, 'error'=>true, 'message'=>'Missing required parameter path'], 400);
        }

        $distance = $r->get('distance');
        if($distance == null){
            return response()->json(['success'=>false, 'error'=>true, 'message'=>'Missing required parameter distance'], 400);
        }

        $duration = $r->get('duration');
        if($duration== null){
            return response()->json(['success'=>false, 'error'=>true, 'message'=>'Missing required parameter duration'], 400);
        }

        $average_speed = $r->get('average_speed');
        if($average_speed == null){
            return response()->json(['success'=>false, 'error'=>true, 'message'=>'Missing required parameter average_speed'], 400);
        }

        $max_speed = $r->get('max_speed');
        if($max_speed == null){
            return response()->json(['success'=>false, 'error'=>true, 'message'=>'Missing required parameter max_speed'], 400);
        }

        $nr = new Route();
        $nr->name = $name;
        $nr->user_id = auth()->id();
        $nr->path = $path;
        $nr->distance = $distance;
        $nr->duration = $duration;
        $nr->average_speed = $average_speed;
        $nr->max_speed = $max_speed;
        $nr->save();

        return response()->json(['success'=>true, 'error'=>false, 'message'=>'Route has been save'], 201);
    }
    /**
     * @OA\Get(
     *     path="/api/route/{user_id}",
     *     tags={"Route"},
     *     summary="Obtener todas las rutas de un usuario",
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID del usuario para filtrar las rutas"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Listado de rutas del usuario",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Mi primera ruta"),
     *                 @OA\Property(property="distance", type="string", example="10km"),
     *                 @OA\Property(property="duration", type="string", example="1h 30m"),
     *                 @OA\Property(
     *                      property="path",
     *                      type="array",
     *                      @OA\Items(
     *                          type="object",
     *                          @OA\Property(property="lat", type="number", format="float", example=40.4639),
     *                          @OA\Property(property="lng", type="number", format="float", example=-3.8095)
     *                      )
     *                  ),
     *                 @OA\Property(property="average_speed", type="string", example="10km/h"),
     *                 @OA\Property(property="max_speed", type="string", example="30km/h")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error al obtener las rutas",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Error retrieving routes")
     *         )
     *     )
     * )
     */
    public function get(Request $request): JsonResponse
    {

        try {
            $userId = auth()->id();
            $routes = Route::where('user_id', $userId)->get();

            if ($routes->isEmpty()) {
                return response()->json(['success' => true, 'error' => false, 'data' => [], 'message' => 'No routes for this user'], 201);
            }

            return response()->json(['success' => true, 'error' => false, 'data' => $routes], 201);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => true, 'message' => 'Error retrieving routes: ' . $e->getMessage()], 400);
        }
    }
}
