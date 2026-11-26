<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $genres = Genre::where('user_id', $request->user()->id)
            ->withCount('gameLibraries')
            ->latest()
            ->get();

        return response()->json([
            'data' => $genres,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $genre = Genre::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
        ]);

        return response()->json([
            'message' => 'Genre berhasil ditambahkan.',
            'data' => $genre,
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $genre = Genre::where('user_id', $request->user()->id)
            ->withCount('gameLibraries')
            ->findOrFail($id);

        return response()->json([
            'data' => $genre,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $genre = Genre::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $genre->update([
            'name' => $validated['name'],
        ]);

        return response()->json([
            'message' => 'Genre berhasil diperbarui.',
            'data' => $genre,
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $genre = Genre::where('user_id', $request->user()->id)->findOrFail($id);
        $genre->delete();

        return response()->json([
            'message' => 'Genre berhasil dihapus.',
        ]);
    }
}