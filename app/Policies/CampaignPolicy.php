<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Campaign;
use App\Models\User;

class CampaignPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->currentBrand !== null;
    }

    public function view(User $user, Campaign $campaign): bool
    {
        return $campaign->brand_id === $user->current_brand_id;
    }

    public function create(User $user): bool
    {
        return $user->currentBrand !== null;
    }

    public function update(User $user, Campaign $campaign): bool
    {
        return $campaign->brand_id === $user->current_brand_id;
    }

    public function delete(User $user, Campaign $campaign): bool
    {
        return $campaign->brand_id === $user->current_brand_id && $campaign->isEditable();
    }
}
