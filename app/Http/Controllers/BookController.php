<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BookController extends Controller
{
    public function getSelected()
    {
        $books = Auth::user()->books()
            ->where('is_selected', true)
            ->with('authors')
            ->with('genres')
            ->with('users')
            ->get();

        return response()->json([
            'data' => $books,
        ]);
    }

    public function select(Book $book): JsonResponse
    {
        $user = Auth::user();
        $userBook = $user->books()->find($book);

        if (!$userBook) {
            $user->books()->attach($book);
            $userBook = $user->books()->find($book);
        }

        $userBook->pivot->update([
            'is_selected' => true
        ]);

        $userBook->save();

        return response()->json([
            'data' => $userBook->pivot,
        ]);
    }

    public function unselect(Book $book): JsonResponse
    {
        $user = Auth::user();
        $userBook = $user->books()->find($book);

        if (!$userBook) {
            $user->books()->attach($book);
            $userBook = $user->books()->find($book);
        }

        $userBook->pivot->update([
            'is_selected' => false
        ]);

        $userBook->save();

        return response()->json([
            'data' => $userBook->pivot,
        ]);
    }

    public function unselectAll(): JsonResponse
    {
        $user = Auth::user();
        $userBook = $user->books()->update([
            'is_selected' => false
        ]);

        return response()->json([
            'data' => [
                'message' => 'Unselected successfully'
            ]
        ]);
    }

    public function rate(Request $request, Book $book): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'rating' => 'required|min:1|max:5|numeric',
        ]);

        if ($v->fails()) {
            return $this->validationError($v->errors());
        }

        $user = Auth::user();
        $userBook = $user->books()->find($book);

        if (!$userBook) {
            $user->books()->attach($book);
            $userBook = $user->books()->find($book);
        }

        $userBook->pivot->update([
            'rating' => $request->rating
        ]);

        $userBook->save();

        return response()->json([
            'data' => $userBook->pivot,
        ]);
    }


    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $books = Book::query()->with('authors')->with('genres')->with('users')->get();

        return response()->json([
            'data' => $books,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string|max:300',
            'image' => 'required|image|mimes:png,jpg',
            'genres' => 'required|array',
            'genres.*' => 'exists:genres,id',
            'authors' => 'required|array',
            'authors.*' => 'exists:authors,id',
        ]);

        if ($v->fails()) {
            return $this->validationError($v->errors());
        }

        $book = Book::query()->create($request->only(['title', 'description']));

        $image = $request->file('image');
        $imageName = Str::uuid() . '.' . $image->extension();
        $image->move('images', $imageName);

        $book->update(['image' => $imageName]);

        $book->genres()->attach($request->genres);
        $book->authors()->attach($request->authors);

        return response()->json([
            'data' => $book,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book): JsonResponse
    {
        $book = $book
            ->load('authors')
            ->load('genres')
            ->load('users');


        return response()->json([
            'data' => $book,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string|max:300',
            'image' => 'required|image|mimes:png,jpg',
            'genres' => 'required|array',
            'genres.*' => 'exists:genres,id',
            'authors' => 'required|array',
            'authors.*' => 'exists:authors,id',
        ]);

        if ($v->fails()) {
            return $this->validationError($v->errors());
        }

        $book->update($request->only(['title', 'description']));

        $image = $request->file('image');
        $imageName = Str::uuid() . '.' . $image->extension();
        $image->move('/images', $imageName);

        $book->update(['image' => $imageName]);

        $book->genres()->sync($request->genres);
        $book->authors()->sync($request->authors);

        $book->save();

        return response()->json([
            'data' => [
                'message' => 'Updated successfully'
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book): JsonResponse
    {
        $book->delete();

        return response()->json([
            'data' => [
                'message' => 'Deleted successfully'
            ]
        ]);
    }
}
