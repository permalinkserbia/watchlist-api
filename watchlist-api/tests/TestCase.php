<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function fakeOmdbApi(string $imdbId = 'tt0111161'): void
    {
        Http::fake([
            'www.omdbapi.com/*' => Http::response([
                'Response' => 'True',
                'Title' => 'The Shawshank Redemption',
                'Year' => '1994',
                'Genre' => 'Drama',
                'Poster' => 'https://example.com/poster.jpg',
                'Plot' => 'Two imprisoned men bond over a number of years.',
                'Runtime' => '142 min',
                'imdbRating' => '9.3',
                'imdbID' => $imdbId,
                'Type' => 'movie',
            ]),
        ]);
    }
}
