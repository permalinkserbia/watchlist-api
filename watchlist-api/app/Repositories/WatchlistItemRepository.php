<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\WatchlistItem;
use Illuminate\Database\Eloquent\Collection;

class WatchlistItemRepository
{
    public function getForUser(User $user, ?string $status = null): Collection
    {
        return $user->watchlistItems()
            ->with('movie')
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->get();
    }

    public function findForUserOrFail(User $user, int $id): WatchlistItem
    {
        return $user->watchlistItems()->findOrFail($id);
    }

    public function create(array $data): WatchlistItem
    {
        return WatchlistItem::create($data)->load('movie');
    }

    public function updateForUser(User $user, int $id, array $data): WatchlistItem
    {
        $watchlistItem = $this->findForUserOrFail($user, $id);
        $watchlistItem->update($data);

        return $watchlistItem->load('movie');
    }

    public function deleteForUser(User $user, int $id): void
    {
        $this->findForUserOrFail($user, $id)->delete();
    }
}
