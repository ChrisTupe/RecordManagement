@extends('layouts.app')

@section('title', 'Add Transaction - ' . $document->misd_code)

@section('breadcrumbs')
    <li class="breadcrumb-item">
        <a href="{{ route('transactions.index') }}" style="text-decoration: none;">Transactions</a>
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

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card text-center h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <i class="bi bi-arrow-down-left" style="font-size: 3em; color: #0d6efd; margin-bottom: 20px;"></i>
                    <h4 class="card-title">Incoming Transaction</h4>
                    <p class="card-text text-muted mb-4">Document is coming to your department</p>
                    <a href="{{ route('documents.transactions.create', array_merge(['document' => $document->id], ['type' => 'incoming'])) }}" 
                       class="btn btn-primary mt-auto">
                        <i class="bi bi-plus-circle"></i> Create Incoming
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card text-center h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <i class="bi bi-arrow-up-right" style="font-size: 3em; color: #198754; margin-bottom: 20px;"></i>
                    <h4 class="card-title">Outgoing Transaction</h4>
                    <p class="card-text text-muted mb-4">Document is leaving your department</p>
                    <a href="{{ route('documents.transactions.create', array_merge(['document' => $document->id], ['type' => 'outgoing'])) }}" 
                       class="btn btn-success mt-auto">
                        <i class="bi bi-plus-circle"></i> Create Outgoing
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <a href="{{ route('documents.show', $document) }}" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> Cancel
            </a>
        </div>
    </div>
@endsection
