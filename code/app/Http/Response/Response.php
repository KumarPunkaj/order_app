<?php

namespace App\Http\Response;

use Illuminate\Http\JsonResponse;

class Response
{
    /**
     * @var App\Helpers\MessageHelper
     */
    protected $messageHelper;

    /**
     * @param \App\Helpers\MessageHelper $messageHelper
     */
    public function __construct(\App\Helpers\MessageHelper $messageHelper)
    {
        $this->messageHelper = $messageHelper;
    }

    public function setError($message, $responseCode = 400, $translateMessage = true)
    {
        if (true === $translateMessage) {
            $message = $this->messageHelper->getMessage($message)?:$message;
        }

        $response = ['error' => $message];

        return response()->json($response, $responseCode);
    }

    public function setSuccess($message, $responseCode = 200, $translateMessage = true)
    {
        if (true === $translateMessage) {
            $message = $this->messageHelper->getMessage($message)?:$message;
        }

        $response = ['status' => $message];

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
