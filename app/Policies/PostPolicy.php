<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     * Can anyone
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Can anyone
     */
    public function view(User $user, Post $post): bool
    {
        return true;
    }

    /**
     * Can anyone
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * only owner can update
     */
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * only owner can delete
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Post $post): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        return false;
    }
}
