<?php

namespace App\Http\Controllers\Api;

use App\Enums\TaskStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'user' => new UserResource($request->user()),
                'stats' => $this->stats($request),
            ],
        ]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->safe()->only(['name', 'email']);

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $data['profile_photo'] = $request->file('profile_photo')->store('avatars', 'public');
        } elseif ($request->boolean('remove_photo') && $user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
            $data['profile_photo'] = null;
        }

        $user->update($data);

        return response()->json([
            'message' => 'Your profile was updated successfully.',
            'data' => [
                'user' => new UserResource($user->refresh()),
                'stats' => $this->stats($request),
            ],
        ]);
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $request->user()->update([
            'password' => $request->validated('password'),
        ]);

        return response()->json([
            'message' => 'Your password was changed successfully.',
        ]);
    }

    private function stats(Request $request): array
    {
        $tasks = $request->user()->tasks();

        return [
            'total' => (clone $tasks)->count(),
            'completed' => (clone $tasks)->where('status', TaskStatus::Completed->value)->count(),
            'pending' => (clone $tasks)->where('status', '!=', TaskStatus::Completed->value)->count(),
        ];
    }
}
