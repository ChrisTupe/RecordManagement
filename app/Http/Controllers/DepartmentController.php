<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Show all departments
     */
    public function index(Request $request)
    {
        $query = Department::query();

        // Search by name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $departments = $query->orderBy('name')->paginate(15);

        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Show create department form
     */
    public function create()
    {
        return view('admin.departments.form');
    }

    /**
     * Store new department
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'code' => 'nullable|string|max:50|unique:departments,code',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|in:0,1',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        Department::create($validated);

        return redirect()->route('admin.departments.index')->with('success', 'Department created successfully!');
    }

    /**
     * Show edit department form
     */
    public function edit(Department $department)
    {
        return view('admin.departments.form', compact('department'));
    }

    /**
     * Update department
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $department->id,
            'code' => 'nullable|string|max:50|unique:departments,code,' . $department->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|in:0,1',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $department->update($validated);

        return redirect()->route('admin.departments.index')->with('success', 'Department updated successfully!');
    }

    /**
     * Delete department
     */
    public function destroy(Department $department)
    {
        // Prevent deletion if department has users
        if ($department->users()->count() > 0) {
            return redirect()->route('admin.departments.index')->with('error', 'Cannot delete department with assigned users. Please reassign users first.');
        }

        // Prevent deletion if department has documents
        if ($department->documents()->count() > 0) {
            return redirect()->route('admin.departments.index')->with('error', 'Cannot delete department with associated documents. Please reassign documents first.');
        }

        $departmentName = $department->name;
        $department->delete();

        return redirect()->route('admin.departments.index')->with('success', "Department '{$departmentName}' has been deleted.");
    }
}
