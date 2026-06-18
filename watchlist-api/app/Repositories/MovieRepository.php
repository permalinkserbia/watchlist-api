<?php

namespace App\Repositories;

use App\Models\Movie;

class MovieRepository
{
    public function parseExternalId(string $imdbId): int
    {
        return (int) filter_var($imdbId, FILTER_SANITIZE_NUMBER_INT);
    }

    public function findByExternalId(int $externalId): ?Movie
    {
        return Movie::where('external_id', $externalId)->first();
    }

    public function findByImdbId(string $imdbId): ?Movie
    {
        return $this->findByExternalId($this->parseExternalId($imdbId));
    }

    public function create(array $data): Movie
    {
        return Movie::create($data);
    }
}
