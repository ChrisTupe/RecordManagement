@extends('layouts.app')

@section('title', 'My Profile - Record Management System')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Profile</li>
@endsection

@section('styles')
<style>
    .profile-card {
        width: 100%;
    }

    .profile-section {
        margin-bottom: 30px;
    }

    .profile-section h3 {
        font-size: 16px;
        color: #333;
        margin-bottom: 15px;
        font-weight: 600;
        border-bottom: 2px solid #191e61;
        padding-bottom: 10px;
    }

    .profile-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 12px;
        color: #999;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .info-value {
        font-size: 15px;
        color: #333;
        font-weight: 500;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }

    .btn {
        padding: 10px 25px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }

    .btn-primary {
        background: linear-gradient(135deg, #191e61 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
    }

    @media (max-width: 600px) {
        .profile-info {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn {
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="bi bi-person-circle"></i> My Profile</h1>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card profile-card">
    <div class="card-body">
        <div class="profile-section">
            <h3><i class="bi bi-person"></i> Personal Information</h3>
            <div class="profile-info">
                <div class="info-item">
                    <div class="info-label">Full Name</div>
                    <div class="info-value">{{ $user->name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email Address</div>
                    <div class="info-value">{{ $user->email }}</div>
                </div>
            </div>
        </div>

        <div class="profile-section">
            <h3><i class="bi bi-building"></i> Department Information</h3>
            <div class="profile-info">
                <div class="info-item">
                    <div class="info-label">Department</div>
                    <div class="info-value">
                        @if ($user->department)
                            {{ $user->department->name }}
                        @else
                            <span style="color: #999;">Not assigned</span>
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Department Code</div>
                    <div class="info-value">
                        @if ($user->department)
                            {{ $user->department->code ?? 'N/A' }}
                        @else
                            <span style="color: #999;">N/A</span>
                        @endif
                    </div>
                </div>
            </div>
            @if ($user->department && $user->department->description)
                <div class="info-item" style="margin-top: 15px;">
                    <div class="info-label">Description</div>
                    <div class="info-value">{{ $user->department->description }}</div>
                </div>
            @endif
        </div>

        <div class="profile-section">
            <h3><i class="bi bi-calendar-event"></i> Account Details</h3>
            <div class="profile-info">
                <div class="info-item">
                    <div class="info-label">Member Since</div>
                    <div class="info-value">{{ $user->created_at->format('F j, Y') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Last Updated</div>
                    <div class="info-value">{{ $user->updated_at->format('F j, Y \a\t g:i A') }}</div>
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                <i class="bi bi-pencil-square"></i> Edit Profile
            </a>
        </div>
    </div>
</div>
@endsection
