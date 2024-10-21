<?php

namespace App\Http\Controllers;

use App\Models\ProspectStatus;
use App\Models\Request as mRequest;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="CRM API ",
 *     version="1.0",
 *     description="L5 Swagger OpenApi para la interface de recepción de estados de prospecto/agenda de Turpin.",
 *     @OA\Contact(
 *          email="alberto.iriberri@securitasdirect.es"
 *     ),
 * )
 */

class ProspectStatusController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/prospect-status",
     *     tags={"Prospect Status"},
     *     summary="Reportar el estado de un prospecto / instalación",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={
     *                          "prospect", "status", "changed_status_at",
     *                      },
     *            @OA\Property(property="prospect", type="integer", example="65432154"),
     *            @OA\Property(property="status", type="string", minLength=1, maxLength=32, example="Installed"),
     *            @OA\Property(property="changed_at", type="timestamp", example="2024-07-15 11:25:35"),
     *         ),
     *      ),
     *     @OA\Response(
     *         response=201,
     *         description="Resultado del registro del estado del prospecto",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="error", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Prospect status change has been recorded"),
     *          )
     *     ),
     *     @OA\Response(
     *         response=400,
     *          description="Error asociado al registro del estado del prospecto",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="error", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Missing required parameter prospect"),
     *           )
     *     )
     * )
     */
    public function change(Request $r):JsonResponse {
        $statusCode = 201;
        //Grabar los datos de la petición por si hay error
        $this->saveRequest($r);

        $prospect = $r->get('prospect');
        if($prospect == null){
            return response()->json(['success'=>false, 'error'=>true, 'message'=>'Missing required parameter prospect'], 400);
        }
        $status = $r->get('status');
        if($status == null){
            return response()->json(['success'=>false, 'error'=>true, 'message'=>'Missing required parameter status'], 400);
        }
        $changedAt = $r->get('changed_at');
        if($changedAt == null){
            return response()->json(['success'=>false, 'error'=>true, 'message'=>'Missing required parameter changed_at'], 400);
        }
        try{
            Carbon::createFromFormat('Y-m-d H:i:s', $changedAt);
        }catch(InvalidFormatException $e){
            return response()->json(['success'=>false, 'error'=>true, 'message'=>'Invalid changed_at format (YYYY-MM-DD HH:MM:SS)'], 400);
        }
        $ps = new ProspectStatus();
        $ps->prospect = $prospect;
        $ps->status = $status;
        $ps->changed_at = $changedAt;
        $ps->save();

        return response()->json(['success'=>true, 'error'=>false, 'message'=>'Prospect status change has been recorded'], 201);
    }

    private function saveRequest(Request $r):void {
        $jRequest = json_encode($r->all());
        mRequest::create(['body'=>$jRequest]);
    }
}
