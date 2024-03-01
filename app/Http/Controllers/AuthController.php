<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'login' => 'required',
            'password' => 'required',
        ]);

        if ($v->fails()) {
            return $this->validationError($v->errors());
        }

        $token = Str::uuid();

        $user = User::query()
            ->where("login", $request->login)
            ->where("password", $request->password)
            ->first();

        if (!$user) {
            return $this->baseError("Authentication error", 401);
        }

        $user->update([
            'token' => $token,
        ]);

        return response()->json([
            'data' => [
                'token' => $token,
                'is_admin' => $user->is_admin,
            ],
        ]);
    }

    public function register(Request $request): JsonResponse
    {
//        Первая маленькая, две больших, два спецсимвола, латиница
//        /^(?=.*[A-Z].*[A-Z])(?=.*[!@#$%^&*].*[!@#$%^&*]).*[a-z].*$/i
        $v = Validator::make($request->all(), [
            'login' => 'required|string|regex:/^[A-Z]+$/i',
            'password' => [
                'required',
                'string',
                'min:6',
                'regex:/^(.*[!@#$%^&*].*)(.*[A-Za-z].*){0,}$/iu',
                'confirmed',
            ]]);


        foreach (str_split($request->password) as $char) {
            if (!preg_match('/[A-Za-z]/u', $char) && !preg_match('/[!@#$%^&*]/iu', $char)) {
                $v->errors()->add('password', 'The password field format is invalid.');
                return $this->validationError($v->errors());
            }
        }

        if ($v->fails()) {
            return $this->validationError($v->errors());
        }

        $user = User::query()->create($request->only(['login', 'password']));

        return response()->json([
            'data' => $user,
        ]);
    }

    public function logout(): JsonResponse
    {
        Auth::user()->update([
            'token' => ''
        ]);

        return response()->json([
            'data' => [
                'logout' => 'ok',
            ],
        ]);
    }
}
