<?php

namespace App\Http\Controllers;

use App\Http\Models\Order;
use App\Http\Requests\OrderCreateRequest;
use App\Http\Requests\OrderIndexRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Http\Response\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * @var \App\Http\Services\Order
     */
    protected $orderService;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @param \App\Http\Services\Order $orderService
     * @param Response                 $response
     */
    public function __construct(
        \App\Http\Services\Order $orderService,
        Response $response
    ) {
        $this->orderService = $orderService;
        $this->response = $response;
    }

    /**
     * Places a new order
     *
     * @param OrderCreateRequest $request
     *
     * @return JsonResponse
     */
    public function create(OrderCreateRequest $request)
    {
        try {
            if ($model = $this->orderService->createOrder($request)) {
                $formattedResponse = $this->response->formatOrderAsResponse($model);

                return $this->response->setSuccessResponse($formattedResponse);
            } else {
                $messages = $this->orderService->error;
                $errorCode = $this->orderService->errorCode;

                return $this->response->sendResponseAsError($messages, $errorCode);
            }
        } catch (\Exception $e) {
            return $this->response->sendResponseAsError($e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Updates an order
     *
     * @param OrderUpdateRequest $request
     * @param int                $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(OrderUpdateRequest $request, $id)
    {
        try {
            $order = Order::findOrFail($id);

            if (false === $order->takeOrder($id)) {
                return $this->response->sendResponseAsError('order_taken', JsonResponse::HTTP_CONFLICT);
            }

            return $this->response->sendResponseAsSuccess('success', JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->response->sendResponseAsError('invalid_id', JsonResponse::HTTP_EXPECTATION_FAILED);
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return JsonResponse
     */
    public function index(OrderIndexRequest $request)
    {
        try {
            $page = (int) $request->get('page', 1);
            $limit = (int) $request->get('limit', 1);

            $records = $this->orderService->getList($page, $limit);

            if (!empty($records)) {
                $orders = [];

                foreach ($records as $record) {
                    $orders[] = $this->response->formatOrderAsResponse($record);
                }

                return $this->response->setSuccessResponse($orders);
            } else {
                return $this->response->sendResponseAsError('NO_DATA_FOUND', JsonResponse::HTTP_NO_CONTENT);
            }
        } catch (\Exception $exception) {
            return $this->response->sendResponseAsError($exception->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
