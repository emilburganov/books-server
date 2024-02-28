<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'login' => 'required|string|regex:/^[A-Za-z]+$/i',
            'password' => 'required|string|min:6|regex:/^[A-Za-z\.\\\!\/\_\,]+$/i|confirmed',
        ]);

        if ($v->fails()) {
            return $this->validationError($v->errors());
        }

        $user = User::query()->create($request->only(['login', 'password']));

        return response()->json([
            'data' => $user,
        ]);
    }
}
