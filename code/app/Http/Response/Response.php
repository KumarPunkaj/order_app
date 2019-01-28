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

    /**
     * @param string  $message
     * @param int  $responseCode
     * @param boolean $translateMessage
     *
     * @return JsonResponse
     */
    public function sendResponseAsError($message, $responseCode = JsonResponse::HTTP_BAD_REQUEST, $translateMessage = true)
    {
        if (true === $translateMessage) {
            $message = $this->messageHelper->getMessage($message)?:$message;
        }

        $response = ['error' => $message];

        return response()->json($response, $responseCode);
    }

    /**
     * @param string  $message
     * @param int  $responseCode
     * @param boolean $translateMessage
     *
     * @return JsonResponse
     */
    public function sendResponseAsSuccess($message, $responseCode = JsonResponse::HTTP_OK, $translateMessage = true)
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

    public function formatOrderAsResponse(\App\Http\Models\Order $order)
    {
        return [
            'id' => $order->id,
            'distance' => $order->getDistanceValue(),
            'status' => $order->status
        ];
    }
}
