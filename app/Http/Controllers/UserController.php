<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $users = User::all();

        return response()->json([
            'data' => $users,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'login' => 'required|string|regex:/^[A-Z]+$/i',
            'password' => 'required|string|min:6|regex:/^[A-Z]+$/i',
        ]);

        if ($v->fails()) {
            return $this->validationError($v->errors());
        }

        $user = User::query()->create($request->merge(['is_admin' => boolval($request->is_admin)])->only(['login', 'password', 'is_admin']));

        return response()->json([
            'data' => $user,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        if (Auth::id() !== $user->id) {
            $v = Validator::make($request->all(), [
                'login' => 'required|string|regex:/^[A-Z]+$/i',
                'password' => 'required|string|min:6|regex:/^[A-Z]+$/i',
            ]);

            if ($v->fails()) {
                return $this->validationError($v->errors());
            }

            $user->update($request->merge(['is_admin' => boolval($request->is_admin)])->only(['login', 'password', 'is_admin']));

            return response()->json([
                'data' => [
                    'message' => 'Updated successfully'
                ]
            ]);
        }

        return $this->baseError("Access denied", 403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): JsonResponse
    {
        if (Auth::id() !== $user->id) {
            $user->delete();

            return response()->json([
                'data' => [
                    'message' => 'Deleted successfully'
                ]
            ]);
        }

        return $this->baseError("Access denied", 403);
    }
}
