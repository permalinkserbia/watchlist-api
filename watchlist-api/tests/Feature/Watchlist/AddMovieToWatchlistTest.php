<?php

namespace Tests\Feature\Watchlist;

use App\Models\Movie;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AddMovieToWatchlistTest extends TestCase
{

    public function test_user_can_add_movie_to_watchlist(): void
    {
        $this->fakeOmdbApi();
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/watchlist', [
            'imdb_id' => 'tt0111161',
            'status' => 'pending',
            'notes' => 'Must watch',
        ]);

        $response->assertOk()
            ->assertJsonPath('status', 'pending')
            ->assertJsonPath('rating', '9.3')
            ->assertJsonPath('notes', 'Must watch')
            ->assertJsonPath('movie.title', 'The Shawshank Redemption');

        $this->assertDatabaseHas('movies', ['external_id' => 111161]);
        $this->assertDatabaseHas('watchlist_items', ['status' => 'pending', 'rating' => '9.3']);
    }

    public function test_add_movie_reuses_existing_movie_in_database(): void
    {
        $user = User::factory()->create();
        Movie::create([
            'title' => 'Existing Movie',
            'external_id' => 111161,
            'year' => '1994',
            'genre' => 'Drama',
            'poster' => '',
            'plot' => '',
            'runtime' => '142 min',
            'imb_rating' => '8.0',
            'status' => 'movie',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/watchlist', [
            'imdb_id' => 'tt0111161',
            'status' => 'pending',
        ]);

        $response->assertOk()
            ->assertJsonPath('rating', '8.0')
            ->assertJsonPath('movie.title', 'Existing Movie');

        $this->assertDatabaseCount('movies', 1);
    }

    public function test_add_movie_validates_imdb_id_format(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/watchlist', [
            'imdb_id' => 'invalid-id',
            'status' => 'pending',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['imdb_id']);
    }

    public function test_guest_cannot_add_movie(): void
    {
        $response = $this->postJson('/api/watchlist', [
            'imdb_id' => 'tt0111161',
            'status' => 'pending',
        ]);

        $response->assertUnauthorized();
    }
}
