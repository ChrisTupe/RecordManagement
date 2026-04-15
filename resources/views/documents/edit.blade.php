@extends('layouts.app')

@section('title', 'Edit Document - ' . $document->misd_code)

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('documents.index') }}" style="text-decoration: none;">Documents</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('documents.show', $document) }}" style="text-decoration: none;">{{ $document->misd_code }}</a>
    </li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-pencil"></i> Edit Document</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('documents.update', $document) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="misd_code" class="form-label">MISD Code</label>
                    <input type="text" class="form-control" id="misd_code" value="{{ $document->misd_code }}" disabled>
                    <small class="text-muted d-block mt-1">Auto-generated code (read-only)</small>
                </div>

                <div class="mb-4">
                    <label for="subject" class="form-label">Subject *</label>
                    <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                           id="subject" name="subject" placeholder="Enter document subject" 
                           value="{{ old('subject', $document->subject) }}" required>
                    @error('subject')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                    <a href="{{ route('documents.show', $document) }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
