<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(?User $user = null): bool
    {
        return true;
    }

    public function view(?User $user, Product $product): bool
    {
        return $product->is_active || (bool) $user?->can('manage products');
    }

    public function create(User $user): bool
    {
        return $user->can('manage products');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->can('manage products');
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->can('manage products');
    }
}
