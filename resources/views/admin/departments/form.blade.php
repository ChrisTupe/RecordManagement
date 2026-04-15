@extends('layouts.app')

@section('title', isset($department) ? 'Edit Department - ' . $department->name : 'Create Department')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.departments.index') }}">Manage Departments</a></li>
    <li class="breadcrumb-item active">{{ isset($department) ? 'Edit' : 'New Department' }}</li>
@endsection

@section('styles')
<style>
    .form-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 30px;
        max-width: 800px;
        margin: 0 auto;
    }

    .form-section {
        margin-bottom: 30px;
    }

    .form-section h3 {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        display: block;
    }

    .form-control, .form-select {
        border-radius: 6px;
        border: 1px solid #dee2e6;
        padding: 10px 15px;
        width: 100%;
        font-size: 14px;
    }

    .form-control:focus, .form-select:focus {
        border-color: #191e61;
        box-shadow: 0 0 0 0.2rem rgba(25, 30, 97, 0.25);
        outline: none;
    }

    .error-message {
        color: #dc3545;
        font-size: 13px;
        margin-top: 5px;
    }

    .help-text {
        font-size: 13px;
        color: #6c757d;
        margin-top: 5px;
        display: block;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #dee2e6;
    }

    .form-actions button,
    .form-actions a {
        flex: 1;
        padding: 12px;
        text-align: center;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        border: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #191e61 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: #e0e0e0;
        color: #333;
    }

    .btn-secondary:hover {
        background: #d0d0d0;
    }

    .status-info {
        background: #f8f9fa;
        padding: 12px;
        border-radius: 6px;
        font-size: 13px;
        color: #6c757d;
        margin-top: 10px;
    }

    .form-check {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-check input {
        margin: 0;
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1>
        <i class="bi bi-diagram-3"></i> 
        {{ isset($department) ? 'Edit Department: ' . $department->name : 'Create New Department' }}
    </h1>
</div>

<div class="form-card">
    <form method="POST" action="{{ isset($department) ? route('admin.departments.update', $department) : route('admin.departments.store') }}">
        @csrf
        @if(isset($department))
            @method('PUT')
        @endif

        <!-- Department Information -->
        <div class="form-section">
            <h3>Department Information</h3>

            <div class="form-group">
                <label for="name" class="form-label">Department Name <span style="color: #dc3545;">*</span></label>
                <input 
                    type="text" 
                    class="form-control @error('name') is-invalid @enderror"
                    id="name" 
                    name="name"
                    value="{{ old('name', $department->name ?? '') }}"
                    placeholder="e.g., Human Resources"
                    required
                >
                <span class="help-text">The official name of the department</span>
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="code" class="form-label">Department Code</label>
                <input 
                    type="text" 
                    class="form-control @error('code') is-invalid @enderror"
                    id="code" 
                    name="code"
                    value="{{ old('code', $department->code ?? '') }}"
                    placeholder="e.g., HR"
                    maxlength="50"
                >
                <span class="help-text">Short code for the department (optional)</span>
                @error('code')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea 
                    class="form-control @error('description') is-invalid @enderror"
                    id="description" 
                    name="description"
                    rows="4"
                    placeholder="Enter department description (optional)"
                    maxlength="1000"
                >{{ old('description', $department->description ?? '') }}</textarea>
                <span class="help-text">Brief description of the department and its responsibilities</span>
                @error('description')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Status -->
        <div class="form-section">
            <h3>Status</h3>

            <div class="form-group">
                <div class="form-check">
                    <input 
                        type="checkbox" 
                        class="form-check-input @error('is_active') is-invalid @enderror"
                        id="is_active" 
                        name="is_active"
                        value="1"
                        {{ old('is_active', isset($department) ? $department->is_active : true) ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="is_active">
                        <strong>Active Department</strong>
                    </label>
                </div>
                <div class="status-info">
                    When active, this department is available for assignment to users and documents. Uncheck to deactivate the department.
                </div>
            </div>
        </div>

        <!-- Department Stats (if editing) -->
        @if(isset($department))
            <div class="form-section">
                <h3>Department Statistics</h3>
                <div class="status-info">
                    <p><strong>Users Assigned:</strong> {{ $department->users()->count() }} user(s)</p>
                    <p><strong>Documents:</strong> {{ $department->documents()->count() }} document(s)</p>
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> {{ isset($department) ? 'Update Department' : 'Create Department' }}
            </button>
            <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Cancel
            </a>
        </div>
    </form>
</div>

<script>
    // Character count for textarea
    const textarea = document.getElementById('description');
    if (textarea) {
        const updateCount = () => {
            const remaining = textarea.maxLength - textarea.value.length;
            // Optional: Show character count
        };
        textarea.addEventListener('input', updateCount);
    }
</script>
@endsection
