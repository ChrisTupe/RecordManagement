@extends('layouts.app')

@section('title', 'Transactions - Record Management System')

@section('breadcrumbs')
    <li class="breadcrumb-item active">
        <i class="bi bi-arrow-left-right"></i> Transactions
    </li>
@endsection

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="bi bi-arrow-left-right"></i> Transaction Management</h1>
                <p class="text-muted mb-0">Track and manage document transactions across all stages</p>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('transactions.index') }}" id="filterForm" class="d-flex gap-2 align-items-end flex-wrap">
                <input type="hidden" name="filter" id="filterValue" value="{{ $filter }}">

                {{-- Search --}}
                <div style="width: 380px;">
                    <label class="form-label small mb-1 text-muted">Search</label>
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

                {{-- Date From --}}
                <div>
                    <label for="date_from" class="form-label small mb-1 text-muted">Date From</label>
                    <input type="date"
                           id="date_from"
                           name="date_from"
                           class="form-control"
                           value="{{ $dateFrom ?? '' }}"
                           onchange="document.getElementById('filterForm').submit()">
                </div>

                {{-- Date To --}}
                <div>
                    <label for="date_to" class="form-label small mb-1 text-muted">Date To</label>
                    <input type="date"
                           id="date_to"
                           name="date_to"
                           class="form-control"
                           value="{{ $dateTo ?? '' }}"
                           onchange="document.getElementById('filterForm').submit()">
                </div>

                {{-- Status Filter --}}
                <div>
                    <label class="form-label small mb-1 text-muted">Status</label>
                    <select id="statusFilter" class="form-select" onchange="updateStatusFilter()" style="max-width: 160px;">
                        <option value="">All Statuses</option>
                        <option value="in-progress" @if($filter === 'in-progress') selected @endif>In Progress</option>
                        <option value="completed" @if($filter === 'completed') selected @endif>Completed</option>
                    </select>
                </div>

                {{-- Month Filter --}}
                <div>
                    <label class="form-label small mb-1 text-muted">Month</label>
                    <select name="month" class="form-select" onchange="this.form.submit()" style="max-width: 140px;">
                        <option value="">All Months</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" @if($month == $i) selected @endif>
                                {{ \Carbon\Carbon::createFromFormat('m', $i)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- Clear Filters --}}
                @if($search || ($filter && $filter !== 'all') || $month || ($dateFrom ?? '') || ($dateTo ?? ''))
                    <div class="mt-auto">
                        <a href="{{ route('transactions.index', ['filter' => 'all']) }}"
                           class="btn btn-outline-secondary"
                           title="Clear all filters">
                            <i class="bi bi-x-circle"></i> Clear
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <script>
        function updateStatusFilter() {
            const status = document.getElementById('statusFilter').value;
            document.getElementById('filterValue').value = status || 'all';
            document.getElementById('filterForm').submit();
        }
    </script>

    @if($documents->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox" style="font-size: 48px; color: #ccc;"></i>
                <p class="text-muted mt-3">No documents found for this filter.</p>
                <a href="{{ route('documents.create') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-plus-circle"></i> Create First Document
                </a>
            </div>
        </div>
    @else

        {{-- Active Filter Summary --}}
        @if(($dateFrom ?? '') || ($dateTo ?? ''))
            <div class="alert alert-info d-flex align-items-center gap-2 mb-3 py-2">
                <i class="bi bi-calendar-range"></i>
                <span>
                    Showing transactions
                    @if(($dateFrom ?? '') && ($dateTo ?? ''))
                        from <strong>{{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }}</strong>
                        to <strong>{{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}</strong>
                    @elseif($dateFrom ?? '')
                        from <strong>{{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }}</strong> onwards
                    @elseif($dateTo ?? '')
                        up to <strong>{{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}</strong>
                    @endif
                    &mdash; Export below will reflect this same date scope.
                </span>
            </div>
        @endif

        <!-- Export Button — scoped to current filters -->
        <div class="mb-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i class="bi bi-file-earmark-arrow-down"></i> Export Transactions
                        @if(($dateFrom ?? '') || ($dateTo ?? ''))
                            <span class="badge bg-info ms-2" style="font-size: 0.75rem; font-weight: normal;">
                                Scoped to current date filter
                            </span>
                        @else
                            <span class="badge bg-secondary ms-2" style="font-size: 0.75rem; font-weight: normal;">
                                All dates (no date filter applied)
                            </span>
                        @endif
                    </h6>
                    <form action="{{ route('transactions.export') }}" method="GET" class="row g-3 align-items-end">
                        {{-- Pass current filters into export --}}
                        <input type="hidden" name="filter" value="{{ $filter }}">
                        <input type="hidden" name="search" value="{{ $search }}">
                        <input type="hidden" name="month" value="{{ $month }}">

                        <div class="col-md-3">
                            <label for="export_start_date" class="form-label">Start Date</label>
                            <input type="date"
                                   class="form-control"
                                   id="export_start_date"
                                   name="start_date"
                                   value="{{ $dateFrom ?? '' }}"
                                   required>
                        </div>
                        <div class="col-md-3">
                            <label for="export_end_date" class="form-label">End Date</label>
                            <input type="date"
                                   class="form-control"
                                   id="export_end_date"
                                   name="end_date"
                                   value="{{ $dateTo ?? '' }}"
                                   required>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary text-white">
                                <i class="bi bi-download"></i> Export to Excel
                            </button>
                            @if(($dateFrom ?? '') || ($dateTo ?? ''))
                                <small class="text-muted ms-2">
                                    Dates pre-filled from your current filter.
                                </small>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>MISD Code</th>
                            <th>Subject</th>
                            <th>Current Stage</th>
                            <th>Transactions</th>
                            <th>Status</th>
                            <th>Last Activity</th>
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
                                <td>
                                    {{ \Str::limit($document->subject, 50) }}
                                </td>
                                <td>
                                    @php
                                        if ($document->current_stage === 'Not Started') {
                                            $badgeColor = '#6c757d';
                                            $badgeText = '#fff';
                                        } elseif (strpos($document->current_stage, 'incoming') !== false) {
                                            $badgeColor = '#17a2b8';
                                            $badgeText = '#fff';
                                        } elseif (strpos($document->current_stage, 'outgoing') !== false) {
                                            $badgeColor = '#6f42c1';
                                            $badgeText = '#fff';
                                        } else {
                                            $badgeColor = '#6c757d';
                                            $badgeText = '#fff';
                                        }
                                    @endphp
                                    <span class="badge" style="background-color: {{ $badgeColor }}; color: {{ $badgeText }};">
                                        <i class="bi bi-arrow-left-right"></i> {{ $document->current_stage }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ $document->transaction_count }}
                                        {{ $document->transaction_count === 1 ? 'transaction' : 'transactions' }}
                                    </span>
                                </td>
                                <td>
                                    @if($document->is_complete)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Complete
                                        </span>
                                    @elseif($document->transaction_count > 0)
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-clock"></i> In Progress
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Not Started</span>
                                    @endif
                                </td>
                                <td style="font-size: 0.875rem;">
                                    @if($document->last_transaction)
                                        {{ $document->last_transaction->created_at->format('M d, H:i') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('documents.show', $document) }}"
                                           class="btn btn-sm btn-primary"
                                           title="View Document Timeline">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('documents.transactions.create', $document) }}"
                                           class="btn btn-sm btn-primary"
                                           title="Add Transaction">
                                            <i class="bi bi-plus-circle"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination (preserve all filter params) -->
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
                    {{ $documents->appends(request()->query())->links('pagination::bootstrap-4') }}
                </nav>
            </div>
        </div>
    @endif
@endsection

<style>
    .btn-group .btn {
        transition: all 0.3s ease;
    }

    .btn-group .btn.active {
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
    }

    .action-buttons {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
    }

    table tbody tr {
        padding: 12px 0;
    }

    table tbody td {
        padding: 16px 12px !important;
        vertical-align: middle;
    }

    table tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
</style>