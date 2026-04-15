<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // Show user profile
    public function show()
    {
        $user = auth()->user();
        return view('profile.show', compact('user'));
    }

    // Show edit profile form
    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    // Update user profile
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'image' => 'nullable|image|mimes:jpeg,png,gif,webp|max:2048',
        ]);

        $user = auth()->user();
        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if ($file->isValid()) {
                try {
                    // Delete old image if exists
                    if ($user->image && Storage::disk('public')->exists($user->image)) {
                        Storage::disk('public')->delete($user->image);
                    }

                    // Store new image
                    $path = $file->store('profile-images', 'public');
                    if ($path) {
                        $data['image'] = $path;
                    }
                } catch (\Exception $e) {
                    return redirect()->route('profile.edit')->with('error', 'Failed to upload image: ' . $e->getMessage());
                }
            }
        }

        // Always update these fields, image only if present
        $user->update($data);

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
    }

    // Serve profile image
    public function image($filename)
    {
        $path = 'profile-images/' . $filename;
        
        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->response($path);
    }
}
