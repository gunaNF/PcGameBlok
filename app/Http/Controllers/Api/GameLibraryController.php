<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GameLibrary;
use App\Models\Genre;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GameLibraryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = GameLibrary::where('user_id', $request->user()->id)
            ->with('genre:id,name')
            ->latest();

        if ($request->filled('genre_id')) {
            $query->where('genre_id', $request->integer('genre_id'));
        }

        return response()->json([
            'data' => $query->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'genre_id' => [
                'required',
                'integer',
                Rule::exists('genres', 'id')->where(
                    fn ($q) => $q->where('user_id', $request->user()->id)
                ),
            ],
            'title' => ['required', 'string', 'max:255'],
            'thumbnail' => ['nullable', 'string', 'max:2048'],
            'platform' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $game = GameLibrary::create([
            'user_id' => $request->user()->id,
            'genre_id' => $validated['genre_id'],
            'title' => $validated['title'],
            'thumbnail' => $validated['thumbnail'] ?? null,
            'platform' => $validated['platform'] ?? 'PC (Windows)',
            'status' => $validated['status'] ?? 'Backlog',
            'notes' => $validated['notes'] ?? null,
        ]);

        $game->load('genre:id,name');

        return response()->json([
            'message' => 'Game berhasil ditambahkan ke koleksi.',
            'data' => $game,
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $game = GameLibrary::where('user_id', $request->user()->id)
            ->with('genre:id,name')
            ->findOrFail($id);

        return response()->json([
            'data' => $game,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $game = GameLibrary::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'genre_id' => [
                'required',
                'integer',
                Rule::exists('genres', 'id')->where(
                    fn ($q) => $q->where('user_id', $request->user()->id)
                ),
            ],
            'title' => ['required', 'string', 'max:255'],
            'thumbnail' => ['nullable', 'string', 'max:2048'],
            'platform' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $game->update([
            'genre_id' => $validated['genre_id'],
            'title' => $validated['title'],
            'thumbnail' => array_key_exists('thumbnail', $validated)
                ? $validated['thumbnail']
                : $game->thumbnail,
            'platform' => $validated['platform'] ?? $game->platform,
            'status' => $validated['status'] ?? $game->status,
            'notes' => array_key_exists('notes', $validated)
                ? $validated['notes']
                : $game->notes,
        ]);

        $game->load('genre:id,name');

        return response()->json([
            'message' => 'Game di koleksi berhasil diperbarui.',
            'data' => $game,
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $game = GameLibrary::where('user_id', $request->user()->id)->findOrFail($id);
        $game->delete();

        return response()->json([
            'message' => 'Game berhasil dihapus dari koleksi.',
        ]);
    }
}