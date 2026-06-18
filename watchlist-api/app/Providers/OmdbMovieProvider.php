<?php

namespace App\Providers;

use App\MovieProvider;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OmdbMovieProvider implements MovieProvider
{
    public function findByImdbId(string $imdbId): array
    {
        $response = Http::get('https://www.omdbapi.com/', [
            'i' => $imdbId,
            'apikey' => config('services.omdb.api_key'),
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('Failed to fetch movie from OMDB.');
        }

        $data = $response->json();

        if (($data['Response'] ?? 'False') !== 'True') {
            throw new RuntimeException($data['Error'] ?? 'Movie not found.');
        }

        return [
            'title' => $data['Title'],
            'external_id' => (int) filter_var($data['imdbID'], FILTER_SANITIZE_NUMBER_INT),
            'year' => $data['Year'],
            'genre' => $data['Genre'],
            'poster' => ($data['Poster'] ?? 'N/A') !== 'N/A' ? $data['Poster'] : '',
            'plot' => ($data['Plot'] ?? 'N/A') !== 'N/A' ? $data['Plot'] : '',
            'runtime' => ($data['Runtime'] ?? 'N/A') !== 'N/A' ? $data['Runtime'] : '',
            'imb_rating' => ($data['imdbRating'] ?? 'N/A') !== 'N/A' ? $data['imdbRating'] : '',
            'status' => $data['Type'] ?? 'movie',
        ];
    }
}
