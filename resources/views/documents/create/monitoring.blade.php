@extends('layouts.app')

@section('title', 'Create Document')

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('documents.index') }}" style="text-decoration: none;">Documents</a>
    </li>
    <li class="breadcrumb-item active">Create New</li>
@endsection

@section('content')
    <div class="page-header">
        <h1><i class="bi bi-file-earmark-plus"></i> Create New Document</h1>
    </div>

    <div class="row" style="position: relative;">
        <div class="col-md-12">
            <button class="btn btn-link p-1" type="button" onclick="document.getElementById('quick-help-doc-content').style.display = document.getElementById('quick-help-doc-content').style.display === 'none' ? 'block' : 'none'; return false;" style="position: fixed; right: 0; top: 50%; transform: translateY(-50%); text-decoration: none; color: white; border: none; background: linear-gradient(135deg, #007BFF 0%, #0056b3 100%); z-index: 10; font-size: 1.5rem; padding: 12px 8px !important; border-radius: 8px 0 0 8px; box-shadow: -2px 4px 8px rgba(0, 0, 0, 0.2);" title="Toggle Quick Help">
                <i class="bi bi-lightbulb"></i>
            </button>
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Document Information -->
                        <div class="mb-4">
                            <h5 class="card-title mb-3"><i class="bi bi-file-earmark"></i> Document Information</h5>
                        </div>

                        <div class="mb-4">
                            <label for="subject" class="form-label">Subject *</label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                   id="subject" name="subject" placeholder="Enter document subject" 
                                   value="{{ old('subject') }}" required>
                            @error('subject')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-text mb-4">
                            <i class="bi bi-info-circle"></i> <strong>MISD Code</strong> will be automatically generated upon saving.
                        </div>

                        <!-- First Incoming Transaction -->
                        <hr class="my-4">

                        <div class="mb-4">
                            <h5 class="card-title mb-3"><i class="bi bi-arrow-down-left"></i> 1st Incoming Transaction</h5>
                        </div>

                        <div class="mb-4">
                            <label for="department" class="form-label">From Department *</label>
                            <select class="form-control @error('department') is-invalid @enderror" 
                                    id="department" name="department" required>
                                <option value="">-- Select Department --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->name }}" {{ old('department') == $dept->name ? 'selected' : '' }}>
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
                                   value="{{ old('date_in') }}">
                            @error('date_in')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
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
                                      id="updates" name="updates" rows="3" 
                                      placeholder="Add any notes or updates about this transaction">{{ old('updates') }}</textarea>
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
                                <i class="bi bi-save"></i> Create Document
                            </button>
                            <a href="{{ route('documents.list') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="quick-help-doc-content" style="display: none; position: fixed; bottom: 20px; right: 20px; width: 320px; max-height: 450px; overflow-y: auto; animation: slideInUp 0.3s ease-out;">
        <style>
            @keyframes slideInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            #quick-help-doc-content .card {
                border: none;
                border-radius: 12px;
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
                overflow: hidden;
                background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            }
            #quick-help-doc-content .card-header {
                background: linear-gradient(135deg, #007BFF 0%, #0056b3 100%);
                color: white;
                border: none;
                padding: 12px 16px;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            #quick-help-doc-content .card-header h6 {
                margin: 0;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            #quick-help-doc-content .close-help {
                background: none;
                border: none;
                color: white;
                font-size: 1.2rem;
                cursor: pointer;
                padding: 0;
                opacity: 0.8;
                transition: opacity 0.2s;
            }
            #quick-help-doc-content .close-help:hover {
                opacity: 1;
            }
            #quick-help-doc-content .card-body {
                padding: 16px;
            }
            #quick-help-doc-content p {
                margin-bottom: 12px;
                font-size: 0.9rem;
                line-height: 1.5;
                color: #333;
            }
            #quick-help-doc-content p:last-child {
                margin-bottom: 0;
            }
            #quick-help-doc-content strong {
                color: #007BFF;
                font-weight: 600;
            }
            #quick-help-doc-content .text-muted {
                color: #999 !important;
                font-size: 0.85rem;
                font-style: italic;
                border-top: 1px solid #e0e0e0;
                padding-top: 12px;
                margin-top: 12px !important;
            }
        </style>
        <div class="card">
            <div class="card-header">
                <h6>
                    <i class="bi bi-lightbulb-fill"></i> Quick Help
                </h6>
                <button class="close-help" onclick="document.getElementById('quick-help-doc-content').style.display = 'none'; return false;" type="button">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <div class="card-body">
                <p><strong>Subject:</strong> Enter a clear and descriptive title for this document.</p>
                <p><strong>MISD Code:</strong> Automatically generated after document creation.</p>
                <p><strong>From Department:</strong> Which department is sending this document.</p>
                <p><strong>Date/Time In:</strong> When the document was received (optional).</p>
                <p><strong>Received By:</strong> Name of the person who received it.</p>
                <p><strong>Status:</strong> Mark as Pending or Complete (optional).</p>
                <p><strong>Updates/Notes:</strong> Add any notes or comments about this transaction (optional).</p>
                <p><strong>Attachments:</strong> Upload related files (Max 10MB per file).</p>
                <p class="text-muted mb-0">* = Required field</p>
            </div>
        </div>
    </div>
@endsection
