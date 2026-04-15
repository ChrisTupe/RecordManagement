<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserManagementController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $totalUsers = User::count();
        $admins = User::where('role', 'admin')->count();
        $totalDocuments = \App\Models\Document::count();
        $totalTransactions = \App\Models\Transaction::count();
        $recentUsers = User::orderBy('created_at', 'desc')->limit(5)->get();

        // Get users per department
        $usersPerDepartment = Department::withCount('users')
            ->where('is_active', true)
            ->orderBy('users_count', 'desc')
            ->limit(10)
            ->get();

        // Get documents per department
        $documentsPerDepartment = Department::withCount('documents')
            ->where('is_active', true)
            ->orderBy('documents_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('totalUsers', 'admins', 'totalDocuments', 'totalTransactions', 'recentUsers', 'usersPerDepartment', 'documentsPerDepartment'));
    }

    /**
     * Show all users with management options
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show create user form
     */
    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('admin.users.form', compact('departments'));
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:user,admin',
            'department_id' => 'nullable|exists:departments,id',
            'image' => 'nullable|image|mimes:jpeg,png,gif,webp|max:2048',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'department_id' => $validated['department_id'] ?? null,
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if ($file->isValid()) {
                try {
                    $path = $file->store('profile-images', 'public');
                    if ($path) {
                        $data['image'] = $path;
                    }
                } catch (\Exception $e) {
                    return redirect()->route('admin.users.create')->with('error', 'Failed to upload image: ' . $e->getMessage());
                }
            }
        }

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }

    /**
     * Show edit user form
     */
    public function edit(User $user)
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('admin.users.form', compact('user', 'departments'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:user,admin',
            'department_id' => 'nullable|exists:departments,id',
            'image' => 'nullable|image|mimes:jpeg,png,gif,webp|max:2048',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'department_id' => $validated['department_id'] ?? null,
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
                    return redirect()->route('admin.users.edit', $user)->with('error', 'Failed to upload image: ' . $e->getMessage());
                }
            }
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account!');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "User '{$userName}' has been deleted.");
    }

    /**
     * Bulk update user roles
     */
    public function bulkUpdateRole(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'role' => 'required|in:user,admin',
        ]);

        // Prevent removing yourself as admin
        if ($request->role === 'user' && in_array(auth()->id(), $validated['user_ids'])) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot remove your own admin privileges!');
        }

        User::whereIn('id', $validated['user_ids'])->update(['role' => $validated['role']]);

        $count = count($validated['user_ids']);
        return redirect()->route('admin.users.index')->with('success', "{$count} user(s) role has been updated.");
    }

    /**
     * Export users to CSV
     */
    public function export()
    {
        $users = User::select('id', 'name', 'email', 'role', 'department_id', 'created_at')->get();

        $filename = 'users_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');
            // Header
            fputcsv($file, ['ID', 'Name', 'Email', 'Role', 'Department ID', 'Created At']);
            // Data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role,
                    $user->department_id,
                    $user->created_at,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
