<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\User;
use App\Models\WatchlistItem;
use App\MovieProvider;
use App\Repositories\MovieRepository;
use App\Repositories\WatchlistItemRepository;

class OmdbMovieService
{
    public function __construct(
        private MovieProvider $movieProvider,
        private MovieRepository $movieRepository,
        private WatchlistItemRepository $watchlistItemRepository,
    ) {}

    public function findOrCreateByImdbId(string $imdbId): Movie
    {
        $movie = $this->movieRepository->findByImdbId($imdbId);

        if ($movie) {
            return $movie;
        }

        return $this->movieRepository->create(
            $this->movieProvider->findByImdbId($imdbId),
        );
    }

    public function addMovieToWatchlist(User $user, string $imdbId, string $status, ?string $notes): WatchlistItem
    {
        $movie = $this->findOrCreateByImdbId($imdbId);

        return $this->watchlistItemRepository->create([
            'movie_id' => $movie->id,
            'user_id' => $user->id,
            'status' => $status,
            'rating' => $movie->imb_rating,
            'notes' => $notes,
        ]);
    }
}
