@extends('layouts.app')

@section('title', 'Documents - Record Management System')

@section('breadcrumbs')
    <li class="breadcrumb-item active">
        <i class="bi bi-file-earmark-text"></i> Documents
    </li>
@endsection

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-file-earmark-text"></i> Documents</h1>
        <a href="{{ route('documents.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Document
        </a>
    </div>

    <!-- Search Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('documents.list') }}" class="d-flex gap-2 align-items-center">
                <div style="width: 380px;">
                    <div class="input-group">
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Search MISD Code or Subject..."
                               value="{{ $search }}">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($documents->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox" style="font-size: 48px; color: #ccc;"></i>
                <p class="text-muted mt-3">No documents found. Create your first document to get started.</p>
                <a href="{{ route('documents.create') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-plus-circle"></i> Create Document
                </a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>MISD Code</th>
                            <th>Subject</th>
                            <th>Created Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $document)
                            <tr>
                                <td>{{ ($documents->currentPage() - 1) * $documents->perPage() + $loop->iteration }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $document->misd_code }}</span>
                                </td>
                                <td>{{ $document->subject }}</td>
                                <td>{{ $document->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('documents.edit', $document) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('documents.destroy', $document) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this document?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="text-muted">
                    Showing <strong>{{ ($documents->currentPage() - 1) * $documents->perPage() + 1 }}</strong> 
                    to <strong>{{ min($documents->currentPage() * $documents->perPage(), $documents->total()) }}</strong> 
                    of <strong>{{ $documents->total() }}</strong> records
                </div>
            </div>
            <div class="d-flex justify-content-center">
                <nav aria-label="Page navigation">
                    {{ $documents->links('pagination::bootstrap-4') }}
                </nav>
            </div>
        </div>
    @endif
@endsection
