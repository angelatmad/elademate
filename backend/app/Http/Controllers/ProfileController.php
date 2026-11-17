<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();

        return view('settings.index', [
            'user' => $user,
            'totalTasks' => $user->tasks()->count(),
            'completedTasks' => $user->tasks()->where('status', TaskStatus::Completed->value)->count(),
            'pendingTasks' => $user->tasks()->where('status', '!=', TaskStatus::Completed->value)->count(),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
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

        return redirect()->route('settings')->with('success', 'Your profile was updated successfully.');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->validated('password'),
        ]);

        return redirect()->route('settings')->with('success', 'Your password was changed successfully.');
    }
}
