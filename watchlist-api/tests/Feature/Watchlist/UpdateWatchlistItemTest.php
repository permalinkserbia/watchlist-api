<?php

namespace Tests\Feature\Watchlist;

use App\Models\Movie;
use App\Models\User;
use App\Models\WatchlistItem;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateWatchlistItemTest extends TestCase
{

    public function test_user_can_update_watchlist_item(): void
    {
        $user = User::factory()->create();
        $watchlistItem = $this->createWatchlistItem($user);

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/watchlist/{$watchlistItem->id}", [
            'status' => 'watched',
            'notes' => 'Great movie',
        ]);

        $response->assertOk()
            ->assertJsonPath('status', 'watched')
            ->assertJsonPath('notes', 'Great movie');

        $this->assertDatabaseHas('watchlist_items', [
            'id' => $watchlistItem->id,
            'status' => 'watched',
            'notes' => 'Great movie',
        ]);
    }

    public function test_user_cannot_update_another_users_item(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $watchlistItem = $this->createWatchlistItem($owner);

        Sanctum::actingAs($otherUser);

        $response = $this->putJson("/api/watchlist/{$watchlistItem->id}", [
            'status' => 'watched',
        ]);

        $response->assertNotFound();
    }

    public function test_update_requires_valid_status(): void
    {
        $user = User::factory()->create();
        $watchlistItem = $this->createWatchlistItem($user);

        Sanctum::actingAs($user);

        $response = $this->putJson("/api/watchlist/{$watchlistItem->id}", [
            'status' => 'invalid-status',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }

    public function test_guest_cannot_update_watchlist_item(): void
    {
        $watchlistItem = $this->createWatchlistItem(User::factory()->create());

        $response = $this->putJson("/api/watchlist/{$watchlistItem->id}", [
            'status' => 'watched',
        ]);

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
