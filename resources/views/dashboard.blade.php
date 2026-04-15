@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumbs')
    <li class="breadcrumb-item active">
        <i class="bi bi-house-door"></i> Dashboard
    </li>
@endsection

@section('content')
    <div class="page-header mb-4">
        <h1><i class="bi bi-graph-up"></i> Dashboard</h1>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card" style="border-left: 4px solid #191e61;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Documents</p>
                            <h2 class="mb-0" style="color: #191e61; font-weight: bold;">{{ $totalDocuments }}</h2>
                        </div>
                        <div style="font-size: 40px; color: #191e61; opacity: 0.2;">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card" style="border-left: 4px solid #28a745;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Complete Documents</p>
                            <h2 class="mb-0" style="color: #28a745; font-weight: bold;">{{ $completeDocuments }}</h2>
                        </div>
                        <div style="font-size: 40px; color: #28a745; opacity: 0.2;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card" style="border-left: 4px solid #191e61;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Transactions</p>
                            <h2 class="mb-0" style="color: #191e61; font-weight: bold;">{{ $totalTransactions }}</h2>
                        </div>
                        <div style="font-size: 40px; color: #191e61; opacity: 0.2;">
                            <i class="bi bi-arrow-left-right"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card" style="border-left: 4px solid #191e61;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Attachments</p>
                            <h2 class="mb-0" style="color: #191e61; font-weight: bold;">{{ $totalAttachments }}</h2>
                        </div>
                        <div style="font-size: 40px; color: #191e61; opacity: 0.2;">
                            <i class="bi bi-file-earmark-arrow-down"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Status and Breakdown -->
    <div class="row mb-4">
        <!-- Transaction Status -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-clipboard-check"></i> Transaction Status
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-clock-history"></i> Pending</span>
                            <strong style="color: #dc3545;">{{ $pendingCount }}</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" style="background: #dc3545; width: {{ $totalTransactions > 0 ? ($pendingCount / $totalTransactions) * 100 : 0 }}%;" role="progressbar"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-check-circle"></i> Completed</span>
                            <strong style="color: #28a745;">{{ $completedCount }}</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" style="background: #28a745; width: {{ $totalTransactions > 0 ? ($completedCount / $totalTransactions) * 100 : 0 }}%;" role="progressbar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction Breakdown -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-pie-chart"></i> Transaction Breakdown</span>
                    <small class="text-muted"><i class="bi bi-info-circle"></i> Total: {{ $totalTransactions }}</small>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-arrow-down-circle"></i> Incoming</span>
                            <strong style="color: #17a2b8;">{{ $incomingCount }}</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" style="background: #17a2b8; width: {{ $totalTransactions > 0 ? ($incomingCount / $totalTransactions) * 100 : 0 }}%;" role="progressbar"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-arrow-up-circle"></i> Outgoing</span>
                            <strong style="color: #6f42c1;">{{ $outgoingCount }}</strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar" style="background: #6f42c1; width: {{ $totalTransactions > 0 ? ($outgoingCount / $totalTransactions) * 100 : 0 }}%;" role="progressbar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Status -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-file-earmark-check"></i> Document Status
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span><i class="bi bi-hourglass-split"></i> In Progress</span>
                                    <strong style="color: #ffc107;">{{ $inProgressDocuments }}</strong>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" style="background: #ffc107; width: {{ $totalDocuments > 0 ? ($inProgressDocuments / $totalDocuments) * 100 : 0 }}%;" role="progressbar"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span><i class="bi bi-check-circle"></i> Completed</span>
                                    <strong style="color: #28a745;">{{ $completeDocuments }}</strong>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" style="background: #28a745; width: {{ $totalDocuments > 0 ? ($completeDocuments / $totalDocuments) * 100 : 0 }}%;" role="progressbar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <!-- Monthly Documents Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-graph-up"></i> Monthly Documents
                </div>
                <div class="card-body">
                    <canvas id="monthlyDocumentsChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Transactions per Department Chart -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-building"></i> Transactions per Department
                </div>
                <div class="card-body">
                    <canvas id="departmentChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Documents -->
    <div class="card" id="recent">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-clock-history"></i> Recent Documents</span>
            <a href="{{ route('documents.list') }}" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body p-0">
            @if($recentDocuments->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.3;"></i>
                    <p class="mt-3">No documents yet. Create your first document to get started.</p>
                    <a href="{{ route('documents.create') }}" class="btn btn-primary mt-2">
                        <i class="bi bi-plus-circle"></i> Create Document
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>MISD Code</th>
                                <th>Subject</th>
                                <th>Created Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentDocuments as $document)
                                <tr>
                                    <td>
                                        <span class="badge bg-info">{{ $document->misd_code }}</span>
                                    </td>
                                    <td>{{ $document->subject }}</td>
                                    <td>{{ $document->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly Documents Chart
    const monthlyCtx = document.getElementById('monthlyDocumentsChart').getContext('2d');
    const monthlyChart = new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: @json($monthlyLabels),
            datasets: [{
                label: 'Documents Created',
                data: @json($monthlyDocuments),
                borderColor: '#191e61',
                backgroundColor: 'rgba(25, 30, 97, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#191e61',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Transactions per Department Chart
    const deptCtx = document.getElementById('departmentChart').getContext('2d');
    const departmentChart = new Chart(deptCtx, {
        type: 'bar',
        data: {
            labels: @json($departmentLabels),
            datasets: [{
                label: 'Number of Transactions',
                data: @json($departmentCounts),
                backgroundColor: [
                    '#191e61',
                    '#764ba2',
                    '#f093fb',
                    '#4facfe',
                    '#00f2fe',
                    '#43e97b',
                    '#fa709a',
                    '#fee140',
                    '#30b0fe',
                    '#a8edea'
                ],
                borderColor: '#fff',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endsection

@endsection