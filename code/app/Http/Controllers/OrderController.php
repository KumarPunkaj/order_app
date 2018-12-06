<?php

namespace App\Http\Controllers;

use App\Http\Models\Distance;
use App\Http\Models\Order;
use App\Http\Requests\OrderIndexRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Http\Response\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery\Exception;
use Validator;

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
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $distanceModel = new Distance();
        $validator = Validator::make($request->all(), $distanceModel->rules());

        try {
            if ($validator->fails()) {
                return $this->response->setError('INVALID_PARAMETERS', JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

            if ($model = $this->orderService->createOrder($request)) {
                $formattedResponse = $this->response->formatOrderAsResponse($model);

                return $this->response->setSuccessResponse($formattedResponse);
            } else {
                $messages = $this->orderService->error;
                $errorCode = $this->orderService->errorCode;

                return $this->response->setError($messages, $errorCode);
            }

        } catch (Exception $e) {
            return $this->response->sendError($e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
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
            if(!is_numeric($id)) {
                return $this->response->setError('Invalid Order Id', JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

            $order = Order::findOrFail($id);

            if ($order->status !== Order::UNASSIGNED_ORDER_STATUS) {
                return $this->response->setError('Order already Taken', JsonResponse::HTTP_CONFLICT);
            }

            $order->exists = true;
            $order->id = $id;
            $order->status = Order::ASSIGNED_ORDER_STATUS;
            $order->save();

            return $this->response->setSuccess('SUCCESS', JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->response->setError('Invalid Order Id', JsonResponse::HTTP_EXPECTATION_FAILED);
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
                return $this->response->setError('No Content Found', JsonResponse::HTTP_NO_CONTENT);
            }
        } catch (Exception $exception) {
            return $this->response->setError($exception->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
