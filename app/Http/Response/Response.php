<?php

namespace App\Http\Response;

use Illuminate\Http\JsonResponse;

class Response
{
    public function setError($message, $responseCode = 400)
    {
        $response = ['error' => $message];

        return response()->json($response, $responseCode);
    }

    public function setSuccess($message, $responseCode = 200)
    {
        $response = ['success' => $message];

        return response()->json($response, $responseCode);
    }

    /**
     * @param array $response
     */
    public function setSuccessResponse($response)
    {
        return response()->json($response, JsonResponse::HTTP_OK);
    }

    public function formatOrderAsResponse($order)
    {
        return [
            'id' => $order->id,
            'distance' => $order->getDistanceValue(),
            'status' => $order->status
        ];
    }
}
