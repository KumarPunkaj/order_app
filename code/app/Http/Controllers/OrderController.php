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
     * Create a new order, given origin and destination coordinates in proper format
     * Validate incoming requests and raises 422 error in case of request expection doesnot got matched.
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
     * Updates an order, providing valid orderID in params
     * If order is already being taken send response as 417 with proper message
     *
     * @param OrderUpdateRequest $request
     * @param int                $id
     *
     * @return JsonResponse
     */
    public function update(OrderUpdateRequest $request, $id)
    {
        try {
            $order = $this->orderService->getOrderById($id);

            if (false === $this->orderService->takeOrder($id)) {
                return $this->response->sendResponseAsError('order_taken', JsonResponse::HTTP_CONFLICT);
            }

            return $this->response->sendResponseAsSuccess('success', JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->response->sendResponseAsError('invalid_id', JsonResponse::HTTP_EXPECTATION_FAILED);
        }
    }

    /**
     * List down orders available in system, provided proper $page and $limit variables in params
     * In case of invalid request parameters send proper message with 422 status code
     *
     * @param OrderIndexRequest $request
     *
     * @return JsonResponse
     */
    public function index(OrderIndexRequest $request)
    {
        try {
            $page = (int) $request->get('page', 1);
            $limit = (int) $request->get('limit', 1);

            $records = $this->orderService->getList($page, $limit);

            if ($records && $records->count() > 0) {
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
