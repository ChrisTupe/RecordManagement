@extends('layouts.app')

@section('title', $document->misd_code . ' - Document Details')

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('transactions.index') }}" style="text-decoration: none;">Transactions</a>
    </li>
    <li class="breadcrumb-item active">{{ $document->misd_code }}</li>
@endsection

@section('content')
    <div class="row">
        <!-- Document Details -->
        <div class="col-12">
            <div class="page-header mb-4">
                <div>
                    <h1><i class="bi bi-file-earmark"></i> {{ $document->misd_code }}</h1>
                    <p class="text-muted mb-0">{{ $document->subject }}</p>
                </div>
            </div>

            <!-- Document Info Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Document Information
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>MISD Code:</strong>
                            <p class="text-muted">{{ $document->misd_code }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Subject:</strong>
                            <p class="text-muted">{{ $document->subject }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Created Date:</strong>
                            <p class="text-muted">{{ $document->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tabs-container">
                <ul class="nav nav-tabs" id="documentTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions" type="button">
                            <i class="bi bi-arrow-left-right"></i> Transactions ({{ $transactions->count() }})
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="attachments-tab" data-bs-toggle="tab" data-bs-target="#attachments" type="button">
                            <i class="bi bi-file-earmark-arrow-down"></i> Attachments ({{ $attachments->count() }})
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Transactions Tab -->
                    <div class="tab-pane fade show active" id="transactions">
                        <!-- Transaction Timeline -->
                        <div class="mb-4">
                            <h6 class="mb-3">Transaction Pipeline</h6>
                            @include('monitoring.partials.timeline')
                        </div>

                        <hr class="my-4">

                        <!-- Transaction Summary -->
                        <h6 class="mb-3">Transaction Summary</h6>
                        @if($transactions->isEmpty())
                            <div class="alert alert-info text-center py-4">
                                <i class="bi bi-info-circle"></i> No transactions yet. Add one to track the document's movement.
                            </div>
                        @else

                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 12%;">Stage</th>
                                            <th style="width: 16%;">Department</th>
                                            <th style="width: 14%;">Subject</th>
                                            <th style="width: 14%;">Updates</th>
                                            <th style="width: 12%;">Date In/Out</th>
                                            <th style="width: 10%;">Received By</th>
                                            <th style="width: 8%;">Attachments</th>
                                            <th style="width: 8%;">Timer</th>
                                            <th style="width: 6%;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $getStageNumber = function($stageName) {
                                                if ($stageName === 'incoming' || $stageName === 'outgoing') {
                                                    return 1;
                                                }
                                                if (preg_match('/^(\d+)/', $stageName, $matches)) {
                                                    return (int)$matches[1];
                                                }
                                                return 1;
                                            };

                                            $getStageLabel = function($stageName) use ($getStageNumber) {
                                                $isIncoming = strpos($stageName, 'incoming') !== false;
                                                $number = $getStageNumber($stageName);
                                                $suffixes = [1 => 'st', 2 => 'nd', 3 => 'rd'];
                                                $suffix = $suffixes[$number] ?? 'th';
                                                $type = $isIncoming ? 'Incoming' : 'Outgoing';
                                                return $number . $suffix . ' ' . $type;
                                            };

                                            $incomingCount = $transactions->filter(function($t) {
                                                return strpos($t->stage, 'incoming') !== false;
                                            })->count();
                                            $outgoingCount = $transactions->filter(function($t) {
                                                return strpos($t->stage, 'outgoing') !== false;
                                            })->count();

                                            $latestTransaction = $transactions->sortByDesc('created_at')->first();
                                            $currentStage = $latestTransaction?->stage;

                                            $maxCycles = max($incomingCount, $outgoingCount);
                                            $stageOrder = [];

                                            for ($i = 1; $i <= $maxCycles; $i++) {
                                                $stageName = $i === 1 ? 'incoming' : $i . ($i === 2 ? 'nd' : ($i === 3 ? 'rd' : 'th')) . ' incoming';
                                                $stageOrder[] = $stageName;

                                                $stageNameOut = $i === 1 ? 'outgoing' : $i . ($i === 2 ? 'nd' : ($i === 3 ? 'rd' : 'th')) . ' outgoing';
                                                $stageOrder[] = $stageNameOut;
                                            }
                                        @endphp

                                        @foreach($stageOrder as $stage)
                                            @php
                                                $transaction = $transactions->where('stage', $stage)->first();
                                                $isCurrent = $stage === $currentStage;
                                                $processingTime = '-';

                                                if (strpos($stage, 'outgoing') !== false) {
                                                    $stageNumber = $getStageNumber($stage);
                                                    $incomingStage = $stageNumber === 1 ? 'incoming' : $stageNumber . ($stageNumber === 2 ? 'nd' : ($stageNumber === 3 ? 'rd' : 'th')) . ' incoming';
                                                    $incomingTransaction = $transactions->where('stage', $incomingStage)->first();

                                                    if ($transaction && $incomingTransaction && $incomingTransaction->date_in && $transaction->date_out) {
                                                        $diffMinutes = $incomingTransaction->date_in->diffInMinutes($transaction->date_out);
                                                        $weeks = floor($diffMinutes / (7 * 24 * 60));
                                                        $days = floor(($diffMinutes % (7 * 24 * 60)) / (24 * 60));
                                                        $hours = floor(($diffMinutes % (24 * 60)) / 60);
                                                        $processingTime = ($weeks > 0 ? $weeks . 'w ' : '') . ($days > 0 ? $days . 'd ' : '') . $hours . 'h';
                                                    }
                                                }
                                            @endphp
                                            <tr class="{{ $transaction ? ($transaction->status === 'complete' ? 'table-success' : 'table-warning') : 'table-light' }} @if($isCurrent) table-active @endif"
                                                @if($isCurrent) style="font-weight: bold; box-shadow: inset 3px 0 0 0 #0d6efd;" @endif>

                                                <td>
                                                    <span class="badge" style="background: {{ strpos($stage, 'incoming') !== false ? '#28a745' : '#007bff' }};">
                                                        {{ $getStageLabel($stage) }}
                                                        @if($isCurrent)
                                                            <i class="bi bi-star-fill ms-2" title="Current Active Stage"></i>
                                                        @endif
                                                    </span>
                                                </td>

                                                <td>{{ $transaction ? $transaction->department : '-' }}</td>

                                                {{-- Subject from document --}}
                                                <td>
                                                    <small>{{ $document->subject }}</small>
                                                </td>

                                                {{-- Updates --}}
                                                <td>
                                                    @if($transaction && $transaction->updates)
                                                        <small>{{ $transaction->updates }}</small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    @if($transaction)
                                                        @if($transaction->date_out)
                                                            <small>{{ $transaction->date_out->format('M d, Y H:i') }}</small>
                                                        @elseif($transaction->date_in)
                                                            <small>{{ $transaction->date_in->format('M d, Y H:i') }}</small>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>

                                                <td>{{ $transaction ? ($transaction->received_by ?? '-') : '-' }}</td>

                                                <td>
                                                    @if($transaction && $transaction->attachments()->count() > 0)
                                                        <span class="badge bg-info">{{ $transaction->attachments()->count() }} file(s)</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    @if($processingTime !== '-')
                                                        <small class="text-success" title="Time from incoming to outgoing"><strong>{{ $processingTime }}</strong></small>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    @if($transaction)
                                                        <span class="badge" style="background: {{ $transaction->status == 'complete' ? '#28a745' : '#ffc107' }};">
                                                            {{ ucfirst($transaction->status) }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">Pending</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <!-- Attachments Tab -->
                    <div class="tab-pane fade" id="attachments">
                        <div class="my-3">
                            <h5><i class="bi bi-file-earmark-arrow-down"></i> Attachments</h5>
                            
                            <!-- Upload Form -->
                            @php
                                $outgoingTransactions = $document->transactions()
                                    ->whereIn('stage', ['outgoing', '2nd outgoing', '3rd outgoing'])
                                    ->orderBy('created_at', 'desc')
                                    ->get();
                            @endphp
                            <form action="{{ route('attachments.store', $document) }}" method="POST" enctype="multipart/form-data" class="mb-4">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="file" class="form-label">Select File</label>
                                        <input type="file" class="form-control" id="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" required>
                                        <small class="text-muted d-block mt-1">Accepted: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG (Max 10MB)</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="transaction_id" class="form-label">Attach to Outgoing Transaction (Optional)</label>
                                        <select class="form-select" id="transaction_id" name="transaction_id">
                                            <option value="">-- Not attached to any transaction --</option>
                                            @forelse($outgoingTransactions as $trans)
                                                <option value="{{ $trans->id }}">
                                                    {{ ucfirst($trans->stage) }} - {{ $trans->department }}
                                                    @if($trans->date_out)
                                                        ({{ $trans->date_out->format('M d, Y') }})
                                                    @endif
                                                </option>
                                            @empty
                                                <option value="" disabled>No outgoing transactions yet</option>
                                            @endforelse
                                        </select>
                                        <small class="text-muted d-block mt-1">Optional: Associate this attachment with a specific outgoing stage</small>
                                    </div>
                                </div>
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-upload"></i> Upload
                                </button>
                            </form>
                        </div>

                        @if($attachments->isEmpty())
                            <div class="alert alert-info text-center py-4">
                                <i class="bi bi-info-circle"></i> No attachments yet. Upload files to attach to this document.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>File Name</th>
                                            <th>Outgoing Stage</th>
                                            <th>Uploaded Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($attachments as $attachment)
                                            <tr>
                                                <td>
                                                    <i class="bi bi-file-earmark"></i> {{ $attachment->file_name }}
                                                </td>
                                                <td>
                                                    @if($attachment->transaction)
                                                        <span class="badge bg-info">{{ ucfirst($attachment->transaction->stage) }}</span>
                                                    @else
                                                        <span class="text-muted">--</span>
                                                    @endif
                                                </td>
                                                <td>{{ $attachment->created_at->format('M d, Y H:i') }}</td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <button class="btn btn-sm btn-secondary" title="View" data-bs-toggle="modal" data-bs-target="#viewModal{{ $attachment->id }}" onclick="viewAttachment('{{ route('attachments.view', [$document, $attachment]) }}', '{{ $attachment->file_name }}', '#viewModal{{ $attachment->id }}')">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <a href="{{ route('attachments.download', [$document, $attachment]) }}" class="btn btn-sm btn-info text-white" title="Download">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                        <form action="{{ route('attachments.destroy', [$document, $attachment]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
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

                            <!-- View File Modal -->
                            @foreach($attachments as $attachment)
                                <div class="modal fade" id="viewModal{{ $attachment->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" style="max-width: 90vw;">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    <i class="bi bi-file-earmark"></i> {{ $attachment->file_name }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body" id="fileViewerBody{{ $attachment->id }}" style="max-height: 75vh; overflow-y: auto;">
                                                <div class="text-center">
                                                    <div class="spinner-border" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents List Sidebar -->
        <div class="col-12 d-none">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list"></i> Documents</span>
                    <a href="{{ route('documents.create') }}" class="btn btn-sm btn-success" title="Add new">
                        <i class="bi bi-plus"></i>
                    </a>
                </div>
                <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                    @php
                        $documents = \App\Models\Document::orderBy('created_at', 'desc')->get();
                    @endphp
                    @if($documents->isEmpty())
                        <p class="text-muted p-3 mb-0">No documents yet.</p>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($documents as $doc)
                                <a href="{{ route('documents.show', $doc) }}" 
                                   class="list-group-item list-group-item-action {{ $doc->id === $document->id ? 'active' : '' }}"
                                   style="border-radius: 0;">
                                    <div style="font-size: 12px; font-weight: 600; color: {{ $doc->id === $document->id ? 'white' : '#191e61' }};">
                                        {{ $doc->misd_code }}
                                    </div>
                                    <div style="font-size: 13px; margin-top: 4px; {{ $doc->id === $document->id ? 'color: rgba(255,255,255,0.9)' : 'color: #333' }};">
                                        {{ Str::limit($doc->subject, 25) }}
                                    </div>
                                    <small style="font-size: 11px; {{ $doc->id === $document->id ? 'color: rgba(255,255,255,0.7)' : 'color: #999' }};">
                                        {{ $doc->created_at->format('M d, Y') }}
                                    </small>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-lightning"></i> Actions
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('documents.edit', $document) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('documents.destroy', $document) }}" method="POST" onsubmit="return confirm('Delete this document?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger w-100">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                        <a href="{{ route('documents.index') }}" class="btn btn-sm btn-secondary">
                            <i class="bi bi-arrow-left"></i> All Documents
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        if (typeof Str === 'undefined') {
            Str = {
                limit: function(str, limit) {
                    return str.length > limit ? str.substring(0, limit) + '...' : str;
                }
            };
        }

        function viewAttachment(fileUrl, fileName, modalSelector) {
            const fileExtension = fileName.split('.').pop().toLowerCase();
            const fileViewerBodyElement = document.querySelector(modalSelector + ' .modal-body');
            
            if (!fileViewerBodyElement) return;

            // Determine file type and display appropriately
            if (['pdf'].includes(fileExtension)) {
                // PDF - embed using iframe
                fileViewerBodyElement.innerHTML = `
                    <iframe src="${fileUrl}" style="width: 100%; height: 600px; border: none;"></iframe>
                `;
            } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension)) {
                // Images - display with img tag
                fileViewerBodyElement.innerHTML = `
                    <div style="text-align: center;">
                        <img src="${fileUrl}" style="max-width: 100%; max-height: 600px; border-radius: 8px;">
                    </div>
                `;
            } else if (['doc', 'docx', 'xls', 'xlsx'].includes(fileExtension)) {
                // Office documents - use Google Docs viewer
                const viewerUrl = `https://docs.google.com/gview?url=${encodeURIComponent(fileUrl)}&embedded=true`;
                fileViewerBodyElement.innerHTML = `
                    <iframe src="${viewerUrl}" style="width: 100%; height: 600px; border: none;"></iframe>
                `;
            } else {
                // Other files - show download prompt
                fileViewerBodyElement.innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-file-earmark" style="font-size: 48px; color: #ccc;"></i>
                        <p class="mt-3 text-muted">This file type cannot be previewed in the browser.</p>
                        <a href="${fileUrl}" class="btn btn-primary" download>
                            <i class="bi bi-download"></i> Download File
                        </a>
                    </div>
                `;
            }
        }
    </script>
@endsection
