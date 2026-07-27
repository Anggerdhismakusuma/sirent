<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    /**
     * Toggle follow/unfollow for a store owner.
     */
    public function toggle(Request $request, User $user): JsonResponse
    {
        $currentUser = $request->user();

        if ($currentUser->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot follow yourself.',
            ], 422);
        }

        $isFollowing = $currentUser->following()->where('followee_id', $user->id)->exists();

        if ($isFollowing) {
            $currentUser->following()->detach($user->id);
            $action = 'unfollowed';
        } else {
            $currentUser->following()->attach($user->id);
            $action = 'followed';
        }

        $followerCount = $user->followers()->count();

        return response()->json([
            'success'       => true,
            'action'        => $action,
            'is_following'  => ! $isFollowing,
            'follower_count' => $followerCount,
        ]);
    }
}
