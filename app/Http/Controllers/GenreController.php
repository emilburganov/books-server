<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $genres = Genre::all();

        return response()->json([
            'data' => $genres,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'title' => 'required'
        ]);

        if ($v->fails()) {
            return $this->validationError($v->errors());
        }

        $genre = Genre::query()->create($request->only(['title']));

        return response()->json([
            'data' => $genre,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Genre $genre): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'title' => 'required'
        ]);

        if ($v->fails()) {
            return $this->validationError($v->errors());
        }

        $genre->update($request->only(['title']));

        return response()->json([
            'data' => [
                'message' => 'Updated successfully'
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Genre $genre): JsonResponse
    {
        $genre->delete();

        return response()->json([
            'data' => [
                'message' => 'Deleted successfully'
            ]
        ]);
    }
}
