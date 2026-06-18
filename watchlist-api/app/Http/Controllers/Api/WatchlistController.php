<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddMovieToWatchlistRequest;
use App\Http\Requests\GetWatchlistRequest;
use App\Http\Requests\RemoveMovieFromWatchlistRequest;
use App\Http\Requests\UpdateWatchlistItemRequest;
use App\Services\OmdbMovieService;
use App\Services\WatchlistService;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class WatchlistController extends Controller
{
    public function __construct(
        private OmdbMovieService $omdbMovieService,
        private WatchlistService $watchlistService,
    ) {}

    public function getWatchlist(GetWatchlistRequest $request): JsonResponse
    {
        return response()->json(
            $this->watchlistService->getWatchlist(
                $request->user(),
                $request->validated('status'),
            ),
        );
    }

    public function addMovieToWatchlist(AddMovieToWatchlistRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $watchlistItem = $this->omdbMovieService->addMovieToWatchlist(
                $request->user(),
                $validated['imdb_id'],
                $validated['status'],
                $validated['notes'] ?? null,
            );
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }

        return response()->json($watchlistItem);
    }

    public function updateWatchlistItem(UpdateWatchlistItemRequest $request, int $id): JsonResponse
    {
        return response()->json(
            $this->watchlistService->updateWatchlistItem(
                $request->user(),
                $id,
                $request->validated(),
            ),
        );
    }

    public function removeMovieFromWatchlist(RemoveMovieFromWatchlistRequest $request, int $id): JsonResponse
    {
        $this->watchlistService->removeFromWatchlist($request->user(), $id);

        return response()->json(['message' => 'Movie removed from watchlist']);
    }
}
