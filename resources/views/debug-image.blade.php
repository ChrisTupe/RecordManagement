@extends('layouts.app')

@section('title', 'Image Debug - Record Management System')

@section('content')
<div class="card">
    <div class="card-body">
        <h2>Image Debug Info</h2>
        
        <p><strong>Image in DB:</strong> {{ auth()->user()->image }}</p>
        <p><strong>Asset URL:</strong> {{ asset('storage/' . auth()->user()->image) }}</p>
        <p><strong>File Exists (via symlink):</strong> {{ file_exists(public_path('storage/' . auth()->user()->image)) ? 'Yes' : 'No' }}</p>
        <p><strong>File Exists (direct):</strong> {{ file_exists(storage_path('app/public/' . auth()->user()->image)) ? 'Yes' : 'No' }}</p>
        
        <h3 style="margin-top: 30px;">Image Preview</h3>
        @if(auth()->user()->image)
            <img src="{{ asset('storage/' . auth()->user()->image) }}" alt="Profile" style="max-width: 200px; border: 1px solid #ccc;">
        @else
            <p>No image</p>
        @endif
    </div>
</div>
@endsection
