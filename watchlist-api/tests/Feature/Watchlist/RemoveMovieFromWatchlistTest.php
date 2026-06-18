<?php

namespace Tests\Feature\Watchlist;

use App\Models\Movie;
use App\Models\User;
use App\Models\WatchlistItem;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RemoveMovieFromWatchlistTest extends TestCase
{

    public function test_user_can_remove_watchlist_item(): void
    {
        $user = User::factory()->create();
        $watchlistItem = $this->createWatchlistItem($user);

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/watchlist/{$watchlistItem->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('watchlist_items', ['id' => $watchlistItem->id]);
    }

    public function test_user_cannot_remove_another_users_item(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $watchlistItem = $this->createWatchlistItem($owner);

        Sanctum::actingAs($otherUser);

        $response = $this->deleteJson("/api/watchlist/{$watchlistItem->id}");

        $response->assertNotFound();
        $this->assertDatabaseHas('watchlist_items', ['id' => $watchlistItem->id]);
    }

    public function test_remove_returns_success_message(): void
    {
        $user = User::factory()->create();
        $watchlistItem = $this->createWatchlistItem($user);

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/watchlist/{$watchlistItem->id}");

        $response->assertOk()
            ->assertJson(['message' => 'Movie removed from watchlist']);
    }

    public function test_guest_cannot_remove_watchlist_item(): void
    {
        $watchlistItem = $this->createWatchlistItem(User::factory()->create());

        $response = $this->deleteJson("/api/watchlist/{$watchlistItem->id}");

        $response->assertUnauthorized();
    }

    private function createWatchlistItem(User $user): WatchlistItem
    {
        $movie = Movie::create([
            'title' => 'Test Movie',
            'external_id' => 111161,
            'year' => '1994',
            'genre' => 'Drama',
            'poster' => '',
            'plot' => '',
            'runtime' => '142 min',
            'imb_rating' => '9.3',
            'status' => 'movie',
        ]);

        return WatchlistItem::create([
            'movie_id' => $movie->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'rating' => '9.3',
        ]);
    }
}
