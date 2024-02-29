<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Genre;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $authors = Author::all();

        return response()->json([
            'data' => $authors,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($v->fails()) {
            return $this->validationError($v->errors());
        }

        $author = Author::query()->create($request->only(['name']));

        return response()->json([
            'data' => $author,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Author $author): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($v->fails()) {
            return $this->validationError($v->errors());
        }

        $author->update($request->only(['name']));

        return response()->json([
            'data' => [
                'message' => 'Updated successfully'
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Author $author): JsonResponse
    {
        $author->delete();

        return response()->json([
            'data' => [
                'message' => 'Deleted successfully'
            ]
        ]);
    }
}
