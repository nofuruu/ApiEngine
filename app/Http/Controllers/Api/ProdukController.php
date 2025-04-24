<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;


class ProdukController extends BaseApiController
{
    protected $user;
    protected $model;
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->model = new Product();
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

    public function index(Request $request)
    {
        $tokenValidation = $this->validateToken($request);
        if ($tokenValidation !== true) return $tokenValidation;

        try {
            $products = $this->model->orderBy('id', 'asc')->get();
            return $this->successResponse($products);
        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching products', 500);
        }
    }

    public function store(Request $request)
    {
        $tokenValidation = $this->validateToken($request);
        if ($tokenValidation !== true) return $tokenValidation;

        try {
            $request->merge([
                'harga' => str_replace(',', '', $request->harga)
            ]);

            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'harga' => 'required|numeric|max:9999999',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors(), 422);
            }

            $product = $this->model->create($validator->validated());
            return $this->successResponse($product, 'Product created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Error creating product', 500);
        }
    }


    public function show(Request $request, string $id)
    {
        $tokenValidation = $this->validateToken($request);
        if ($tokenValidation !== true) return $tokenValidation;

        try {
            $product = $this->model->findOrFail($id);
            return $this->successResponse($product);
        } catch (\Exception $e) {
            return $this->errorResponse('Product not found', 404);
        }
    }

    public function update(Request $request, string $id)
    {
        $tokenValidation = $this->validateToken($request);
        if ($tokenValidation !== true) return $tokenValidation;

        try {
            $product = $this->model->findOrFail($id);

            $data = [
                'nama' => $request->input('nama'),
                'deskripsi' => $request->input('deskripsi'),
                'harga' => str_replace(',', '', $request->input('harga')),
            ];

            $validator = Validator::make($data, [
                'nama' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'harga' => 'required|numeric|max:9999999',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse($validator->errors(), 422);
            }

            $product->update($validator->validated());
            return $this->successResponse($product, 'Product updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Error updating product', 500);
        }
    }


    public function destroy(Request $request, string $id)
    {
        $tokenValidation = $this->validateToken($request);
        if ($tokenValidation !== true) return $tokenValidation;

        try {
            $product = $this->model->findOrFail($id);
            $product->delete();
            return $this->successResponse(null, 'Product deleted successfully', 204);
        } catch (\Exception $e) {
            return $this->errorResponse('Error deleting product', 500);
        }
    }

    public function search(Request $request)
    {
        $tokenValidation = $this->validateToken($request);
        if ($tokenValidation !== true) return $tokenValidation;

        try {
            $query = $request->input('q');
            $products = $this->model->where('nama', 'ilike', "%{$query}%")
                ->orWhere('deskripsi', 'ilike', "%{$query}%")
                ->orderBy('id', 'asc')
                ->get();

            return $this->successResponse($products);
        } catch (\Exception $e) {
            return $this->errorResponse('Error searching products', 500);
        }
    }
}
