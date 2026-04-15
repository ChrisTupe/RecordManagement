@extends('layouts.app')

@section('title', 'Edit Incoming Transaction - ' . $document->misd_code)

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('transactions.index') }}" style="text-decoration: none;">Transactions</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('documents.show', $document) }}" style="text-decoration: none;">{{ $document->misd_code }}</a>
    </li>
    <li class="breadcrumb-item active">Edit Incoming Transaction</li>
@endsection

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-pencil"></i> Edit Incoming Transaction</h1>
        <p class="text-muted">Document: <strong>{{ $document->misd_code }}</strong></p>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('documents.transactions.update', [$document, $transaction]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="stage" class="form-label">Transaction Stage *</label>
                            <select class="form-select @error('stage') is-invalid @enderror" 
                                    id="stage" name="stage" required>
                                <option value="">-- Select Stage --</option>
                                @php
                                    $ordinalSuffixes = [1 => 'st', 2 => 'nd', 3 => 'rd'];
                                    for ($i = 1; $i <= 10; $i++) {
                                        $suffix = $ordinalSuffixes[$i] ?? 'th';
                                        $stageName = $i === 1 ? 'incoming' : $i . $suffix . ' incoming';
                                        $stageLabel = $i === 1 ? '1st Incoming' : $i . $suffix . ' Incoming';
                                        $selected = old('stage', $transaction->stage) == $stageName ? 'selected' : '';
                                        echo "<option value=\"$stageName\" $selected>$stageLabel</option>";
                                    }
                                @endphp
                            </select>
                            @error('stage')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="department" class="form-label">From Department *</label>
                            <select class="form-select @error('department') is-invalid @enderror" 
                                    id="department" name="department" required>
                                <option value="">-- Select Department --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->name }}" {{ old('department', $transaction->department) == $dept->name ? 'selected' : '' }}>
                                        {{ $dept->name }}{{ $dept->code ? ' (' . $dept->code . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="date_in" class="form-label">Date/Time In</label>
                            <input type="datetime-local" class="form-control @error('date_in') is-invalid @enderror" 
                                   id="date_in" name="date_in" 
                                   value="{{ old('date_in', $transaction->date_in?->format('Y-m-d\\TH:i')) }}">
                            @error('date_in')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
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

                        <div class="mb-4">
                            <label for="attachments" class="form-label">Attachments</label>
                            <input type="file" class="form-control @error('attachments.*') is-invalid @enderror" 
                                   id="attachments" name="attachments[]" multiple accept="*/*">
                            <small class="text-muted d-block mt-1" style="font-size: 0.85rem;">Upload files related to this transaction (Max 10MB per file). Multiple files can be selected.</small>
                            @error('attachments.*')
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
                    <p><strong>Subject:</strong><br>
                        <span class="text-muted">{{ $document->subject }}</span>
                    </p>
                    <hr class="my-2">
                    <p><strong>Created:</strong> {{ $transaction->created_at->format('M d, Y H:i') }}</p>
                    <p><strong>Last Updated:</strong> {{ $transaction->updated_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection