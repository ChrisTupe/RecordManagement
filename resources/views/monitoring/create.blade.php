@extends('layouts.app')

@section('title', 'Add Transaction - ' . $document->misd_code)

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('documents.index') }}" style="text-decoration: none;">Documents</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('documents.show', $document) }}" style="text-decoration: none;">{{ $document->misd_code }}</a>
    </li>
    <li class="breadcrumb-item active">Add Transaction</li>
@endsection

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-arrow-left-right"></i> Add Transaction</h1>
        <p class="text-muted">Document: <strong>{{ $document->misd_code }}</strong> - {{ $document->subject }}</p>
    </div>

    <div class="row" style="position: relative;">
        <div class="col-md-12">
            <button class="btn btn-link p-1" type="button" onclick="document.getElementById('quick-help-tx-content').style.display = document.getElementById('quick-help-tx-content').style.display === 'none' ? 'block' : 'none'; return false;" style="position: fixed; right: 0; top: 50%; transform: translateY(-50%); text-decoration: none; color: white; border: none; background: linear-gradient(135deg, #007BFF 0%, #0056b3 100%); z-index: 10; font-size: 1.5rem; padding: 12px 8px !important; border-radius: 8px 0 0 8px; box-shadow: -2px 4px 8px rgba(0, 0, 0, 0.2);" title="Toggle Quick Help">
                <i class="bi bi-lightbulb"></i>
            </button>
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('documents.transactions.store', $document) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="stage" class="form-label">Transaction Stage *</label>
                            <select class="form-select @error('stage') is-invalid @enderror" 
                                    id="stage" name="stage" required>
                                <option value="">-- Select Stage --</option>
                                <option value="incoming" {{ old('stage') == 'incoming' ? 'selected' : '' }}>Incoming</option>
                                <option value="2nd incoming" {{ old('stage') == '2nd incoming' ? 'selected' : '' }}>2nd Incoming</option>
                                <option value="3rd incoming" {{ old('stage') == '3rd incoming' ? 'selected' : '' }}>3rd Incoming</option>
                                <option value="outgoing" {{ old('stage') == 'outgoing' ? 'selected' : '' }}>Outgoing</option>
                                <option value="2nd outgoing" {{ old('stage') == '2nd outgoing' ? 'selected' : '' }}>2nd Outgoing</option>
                                <option value="3rd outgoing" {{ old('stage') == '3rd outgoing' ? 'selected' : '' }}>3rd Outgoing</option>
                            </select>
                            @error('stage')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="department" class="form-label">Department *</label>
                            <input type="text" class="form-control @error('department') is-invalid @enderror" 
                                   id="department" name="department" placeholder="Enter department name" 
                                   value="{{ old('department') }}" required>
                            @error('department')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="date_out" class="form-label">Date/Time Out</label>
                                <input type="datetime-local" class="form-control @error('date_out') is-invalid @enderror" 
                                       id="date_out" name="date_out" 
                                       value="{{ old('date_out') }}">
                                @error('date_out')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="date_in" class="form-label">Date/Time In</label>
                                <input type="datetime-local" class="form-control @error('date_in') is-invalid @enderror" 
                                       id="date_in" name="date_in" 
                                       value="{{ old('date_in') }}">
                                @error('date_in')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="received_by" class="form-label">Received By</label>
                            <input type="text" class="form-control @error('received_by') is-invalid @enderror" 
                                   id="received_by" name="received_by" placeholder="Name of person who received" 
                                   value="{{ old('received_by') }}">
                            @error('received_by')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="">-- Select Status --</option>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="complete" {{ old('status') == 'complete' ? 'selected' : '' }}>Complete</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="updates" class="form-label">Updates/Notes</label>
                            <textarea class="form-control @error('updates') is-invalid @enderror" 
                                      id="updates" name="updates" rows="4" 
                                      placeholder="Add any notes or updates about this transaction">{{ old('updates') }}</textarea>
                            @error('updates')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Transaction
                            </button>
                            <a href="{{ route('documents.show', $document) }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="quick-help-tx-content" style="display: none; position: fixed; bottom: 20px; right: 20px; width: 300px; max-height: 400px; overflow-y: auto;">
        <div class="card" style="box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <div class="card-body">>
                    <p><strong>Stage:</strong> Select the type of transaction (incoming, outgoing, etc.)</p>
                    <p><strong>Department:</strong> Which department is handling this stage.</p>
                    <p><strong>Date Out:</strong> When the document left the previous location.</p>
                    <p><strong>Date In:</strong> When the document arrived at this department (optional).</p>
                    <p><strong>Received By:</strong> Name of the person who received it.</p>
                    <p><strong>Status:</strong> Mark as Pending or Complete (optional).</p>
                    <p><strong>Updates/Notes:</strong> Add any notes or comments about this transaction (optional).</p>
                    <p class="text-muted mb-0">* = Required field</p>
                </div>
            </div>
        </div>
    </div>
@endsection
