<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;


class PeminjamanKendaraan extends Controller
{
    protected $user;
    protected $model;
    public function __construct() {}

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
            $deliveriess = $this->model->orderBy('id', 'asc')->get();
            return $this->successResponse($deliveriess);
        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching deliveriess', 500);
        }
    }

    public function store() {}

    public function destroy() {}

    public function update() {}

    public function show() {}
}
