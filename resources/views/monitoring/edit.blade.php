@extends('layouts.app')

@section('title', 'Edit Transaction - ' . $document->misd_code)

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('documents.index') }}" style="text-decoration: none;">Documents</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('documents.show', $document) }}" style="text-decoration: none;">{{ $document->misd_code }}</a>
    </li>
    <li class="breadcrumb-item active">Edit Transaction</li>
@endsection

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-pencil"></i> Edit Transaction</h1>
        <p class="text-muted">Document: <strong>{{ $document->misd_code }}</strong></p>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('documents.transactions.update', [$document, $transaction]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="stage" class="form-label">Transaction Stage *</label>
                            <select class="form-select @error('stage') is-invalid @enderror" 
                                    id="stage" name="stage" required>
                                <option value="">-- Select Stage --</option>
                                <option value="incoming" {{ old('stage', $transaction->stage) == 'incoming' ? 'selected' : '' }}>Incoming</option>
                                <option value="2nd incoming" {{ old('stage', $transaction->stage) == '2nd incoming' ? 'selected' : '' }}>2nd Incoming</option>
                                <option value="3rd incoming" {{ old('stage', $transaction->stage) == '3rd incoming' ? 'selected' : '' }}>3rd Incoming</option>
                                <option value="outgoing" {{ old('stage', $transaction->stage) == 'outgoing' ? 'selected' : '' }}>Outgoing</option>
                                <option value="2nd outgoing" {{ old('stage', $transaction->stage) == '2nd outgoing' ? 'selected' : '' }}>2nd Outgoing</option>
                                <option value="3rd outgoing" {{ old('stage', $transaction->stage) == '3rd outgoing' ? 'selected' : '' }}>3rd Outgoing</option>
                            </select>
                            @error('stage')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="department" class="form-label">Department *</label>
                            <input type="text" class="form-control @error('department') is-invalid @enderror" 
                                   id="department" name="department" placeholder="Enter department name" 
                                   value="{{ old('department', $transaction->department) }}" required>
                            @error('department')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="date_out" class="form-label">Date/Time Out</label>
                                <input type="datetime-local" class="form-control @error('date_out') is-invalid @enderror" 
                                       id="date_out" name="date_out" 
                                       value="{{ old('date_out', $transaction->date_out?->format('Y-m-d\\TH:i')) }}">
                                @error('date_out')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="date_in" class="form-label">Date/Time In</label>
                                <input type="datetime-local" class="form-control @error('date_in') is-invalid @enderror" 
                                       id="date_in" name="date_in" 
                                       value="{{ old('date_in', $transaction->date_in ? $transaction->date_in->format('Y-m-d\TH:i') : '') }}">
                                @error('date_in')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="received_by" class="form-label">Received By</label>
                            <input type="text" class="form-control @error('received_by') is-invalid @enderror" 
                                   id="received_by" name="received_by" placeholder="Name of person who received" 
                                   value="{{ old('received_by', $transaction->received_by) }}">
                            @error('received_by')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="">-- Select Status --</option>
                                <option value="pending" {{ old('status', $transaction->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="complete" {{ old('status', $transaction->status) == 'complete' ? 'selected' : '' }}>Complete</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="updates" class="form-label">Updates/Notes</label>
                            <textarea class="form-control @error('updates') is-invalid @enderror" 
                                      id="updates" name="updates" rows="4" 
                                      placeholder="Add any notes or updates about this transaction">{{ old('updates', $transaction->updates) }}</textarea>
                            @error('updates')
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
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Transaction Info
                </div>
                <div class="card-body">
                    <p><strong>Created:</strong> {{ $transaction->created_at->format('M d, Y H:i') }}</p>
                    <p><strong>Last Updated:</strong> {{ $transaction->updated_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
