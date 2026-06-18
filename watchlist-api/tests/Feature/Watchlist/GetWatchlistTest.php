<?php

namespace Tests\Feature\Watchlist;

use App\Models\Movie;
use App\Models\User;
use App\Models\WatchlistItem;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GetWatchlistTest extends TestCase
{

    public function test_authenticated_user_can_get_watchlist(): void
    {
        $user = User::factory()->create();
        $movie = Movie::create($this->movieData());
        WatchlistItem::create([
            'movie_id' => $movie->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'rating' => '9.3',
            'notes' => 'Must watch',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/watchlist');

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonStructure([
                '*' => ['id', 'status', 'rating', 'notes', 'movie' => ['title', 'year']],
            ]);
    }

    public function test_watchlist_can_be_filtered_by_status(): void
    {
        $user = User::factory()->create();
        $movie = Movie::create($this->movieData());

        WatchlistItem::create([
            'movie_id' => $movie->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'rating' => '9.3',
        ]);

        WatchlistItem::create([
            'movie_id' => $movie->id,
            'user_id' => $user->id,
            'status' => 'watched',
            'rating' => '9.3',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/watchlist?status=pending');

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.status', 'pending');
    }

    public function test_guest_cannot_get_watchlist(): void
    {
        $response = $this->getJson('/api/watchlist');

        $response->assertUnauthorized();
    }

    public function test_empty_watchlist_returns_empty_array(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/api/watchlist');

        $response->assertOk()
            ->assertExactJson([]);
    }

    /**
     * @return array<string, mixed>
     */
    private function movieData(): array
    {
        return [
            'title' => 'The Shawshank Redemption',
            'external_id' => 111161,
            'year' => '1994',
            'genre' => 'Drama',
            'poster' => 'https://example.com/poster.jpg',
            'plot' => 'A plot',
            'runtime' => '142 min',
            'imb_rating' => '9.3',
            'status' => 'movie',
        ];
    }
}
