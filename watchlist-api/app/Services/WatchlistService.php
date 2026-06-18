<?php

namespace App\Services;

use App\Models\User;
use App\Models\WatchlistItem;
use App\Repositories\WatchlistItemRepository;
use Illuminate\Database\Eloquent\Collection;

class WatchlistService
{
    public function __construct(private WatchlistItemRepository $watchlistItemRepository) {}

    public function getWatchlist(User $user, ?string $status = null): Collection
    {
        return $this->watchlistItemRepository->getForUser($user, $status);
    }

    public function updateWatchlistItem(User $user, int $id, array $data): WatchlistItem
    {
        return $this->watchlistItemRepository->updateForUser($user, $id, $data);
    }

    public function removeFromWatchlist(User $user, int $id): void
    {
        $this->watchlistItemRepository->deleteForUser($user, $id);
    }
}
