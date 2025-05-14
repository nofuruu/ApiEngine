<?php

namespace App\Http\Controllers\Api;

use App\Models\deliveries;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeliveryController extends BaseApiController
{

    protected $user;
    protected $model;
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->model = new Deliveries();
    }

    protected function validateToken(Request $request)
    {
        try {
            $token = $request->header('Authorization');
            if (!$token) {
                return $this->errorResponse('Token not provided', 401);
            }

            $token = str_replace('Bearer ', '', $token);
            JWTAuth::setToken($token);

            if (!$this->user = JWTAuth::authenticate()) {
                return $this->errorResponse('Invalid Token', 401);
            }
            return true;
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMEssage(), 401);
        }
    }

    protected function successResponse($data, $message = null, $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function errorResponse($message, $code)
    {
        return response()->json([
            'status' => false,
            'message' => $message
        ], $code);
    }

    public function datatable(Request $request)
    {
        $tokenValidation = $this->validateToken($request);
        if ($tokenValidation !== true) return $tokenValidation;

        try {
            $draw = intval($request->get('draw'));
            $start = intval($request->get('start'));
            $length = intval($request->get('length'));

            $query = $this->model->orderBy('id', 'asc');
            $total = $query->count();

            $data = $query->skip($start)->take($length)->get();

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error fetching deliveries'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $tokenValidation = $this->validateToken($request);
        if ($tokenValidation !== true) return $tokenValidation;

        try {
            $validator = Validator::make($request->all(), [
                'sender_name' => 'required|string|max:255',
                'sender_address' => 'required|string|max:255',
                'recipient_address' => 'required|string|max:255',
                'recipient_name' => 'required|string|max:255',
                'sender_phone' => 'required|digits_between:10,15',
                'recipient_phone' => 'required|digits_between:10,15'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors(), 422);
            }

            $data = $validator->validated();
            $data['status'] = 'pending';

            $deliveries = $this->model->create($data);

            return $this->successResponse($deliveries, 'Deliveries Request Created', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Error creating deliveries', 500);
        }
    }


    public function show(Request $request, string $id)
    {
        $tokenValidation = $this->validateToken($request);
        if ($tokenValidation !== true) return $tokenValidation;

        try {
            $deliveries = $this->model->findOrFail($id);
            return $this->successResponse($deliveries);
        } catch (\Exception $e) {
            return $this->errorResponse('deliveries not found', 404);
        }
    }

    public function update(Request $request, string $id)
    {
        $tokenValidation = $this->validateToken($request);
        if ($tokenValidation !== true) return $tokenValidation;

        try {
            $deliveries = $this->model->findOrFail($id);

            $data = [
                'sender_name' => $request->input('sender_name'),
                'sender_address' => $request->input('sender_address'),
                'rercipient_address' => $request->input('recipient_address'),
                'recipient_name' => $request->input('recipient_name'),
                'recipient_phone' => $request->input('recipient_phone'),
                'sender_phone' => $request->input('sender_phone'),
            ];

            $validator = Validator::make($data, [
                'sender_name' => 'required|string|max:255',
                'sender_address' => 'required|string|max:255',
                'recipient_address' => 'required|string|max:255',
                'recipient_name' => 'required|string|max:255',
                'sender_phone' => 'required|string|max:255',
                'recipient_phone' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors(), 422);
            }

            $deliveries->update($validator->validated());
            return $this->successResponse($deliveries, 'deliveries updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Error updating deliveries', 500);
        }
    }


    public function destroy(Request $request, string $id)
    {
        $tokenValidation = $this->validateToken($request);
        if ($tokenValidation !== true) return $tokenValidation;

        try {
            $deliveries = $this->model->findOrFail($id);
            $deliveries->delete();
            return $this->successResponse(null, 'deliveries deleted successfully', 204);
        } catch (\Exception $e) {
            return $this->errorResponse('Error deleting deliveries', 500);
        }
    }
}
