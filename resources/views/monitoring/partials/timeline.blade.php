<!-- Transaction Timeline Partial - Dynamic -->
@php
    // Helper function to get ordinal suffix
    $getOrdinalSuffix = function($num) {
        if ($num === 1) return 'st';
        if ($num === 2) return 'nd';
        if ($num === 3) return 'rd';
        return 'th';
    };
    
    // Helper function to generate stage name (e.g., "incoming", "2nd incoming", "3rd outgoing")
    $generateStageName = function($number, $type) use ($getOrdinalSuffix) {
        if ($number === 1) {
            return $type; // "incoming" or "outgoing"
        } else {
            $suffix = $getOrdinalSuffix($number);
            return $number . $suffix . ' ' . $type; // "2nd incoming", "3rd outgoing", etc.
        }
    };
    
    // Helper function to get stage label for display
    $getStageLabelText = function($number, $type) use ($getOrdinalSuffix) {
        $typeLabel = $type === 'incoming' ? 'Incoming' : 'Outgoing';
        if ($number === 1) {
            return "1st $typeLabel";
        } else {
            $suffix = $getOrdinalSuffix($number);
            return $number . $suffix . ' ' . $typeLabel;
        }
    };
    
    // Count transactions by type
    $incomingTransactions = $document->transactions->filter(function($t) {
        return strpos($t->stage, 'incoming') !== false;
    });
    $outgoingTransactions = $document->transactions->filter(function($t) {
        return strpos($t->stage, 'outgoing') !== false;
    });
    
    $incomingCount = $incomingTransactions->count();
    $outgoingCount = $outgoingTransactions->count();
    
    // Find the latest transaction (current stage)
    $latestTransaction = $document->transactions->sortByDesc('created_at')->first();
    $currentStage = $latestTransaction?->stage;
    
    // Generate stages to display (1 more than max to show "Add Next")
    $maxCycles = max($incomingCount, $outgoingCount) + 1;
    $stages = [];
    
    for ($i = 1; $i <= $maxCycles; $i++) {
        $stages[] = ['name' => $generateStageName($i, 'incoming'), 'type' => 'incoming', 'number' => $i];
        $stages[] = ['name' => $generateStageName($i, 'outgoing'), 'type' => 'outgoing', 'number' => $i];
    }
@endphp

<div class="transaction-timeline">
    <div class="row g-2 mb-4">
        @foreach($stages as $stage)
            @php
                $transaction = $document->transactions->where('stage', $stage['name'])->first();
                $isCompleted = $transaction && $transaction->status === 'complete';
                $isPending = $transaction && $transaction->status === 'pending';
                $isCurrent = $stage['name'] === $currentStage;
                $icon = $stage['type'] === 'incoming' ? 'bi-arrow-down-circle' : 'bi-arrow-up-circle';
                $label = $getStageLabelText($stage['number'], $stage['type']);
            @endphp
            
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <div class="stage-card @if($isCompleted) completed @elseif($isPending) pending @else empty @endif @if($isCurrent) current @endif" 
                     data-stage="{{ $stage['name'] }}"
                     @if($transaction) 
                        data-toggle="tooltip" 
                        title="{{ $transaction->department }} - {{ $transaction->date_out?->format('M d, Y H:i') ?? 'No date' }}"
                     @endif>
                    
                    <div class="stage-header">
                        <i class="bi {{ $icon }}"></i>
                    </div>
                    
                    <div class="stage-body">
                        <p class="stage-label">{{ $label }}
                            @if($isCurrent)
                                <span class="badge bg-primary ms-2" title="Current Active Stage">Current</span>
                            @endif
                        </p>
                        
                        @if($transaction)
                            <div class="stage-content">
                                <p class="department-name">{{ $transaction->department }}</p>

                                {{-- Subject from document --}}
                                <small class="d-block mb-1" style="font-size: 0.78rem; color: #6c757d;">
                                    <i class="bi bi-file-earmark-text"></i> {{ $document->subject }}
                                </small>

                                {{-- Updates/Notes --}}
                                @if($transaction->updates)
                                    <small class="d-block updates-text mb-1">
                                        <i class="bi bi-chat-left-text"></i> {{ $transaction->updates }}
                                    </small>
                                @endif

                                <small class="text-muted d-block">
                                    <i class="bi bi-person"></i> {{ $transaction->received_by ?? 'Not specified' }}
                                </small>
                                @if($transaction->date_out)
                                    <small class="text-muted d-block">
                                        <i class="bi bi-calendar"></i> {{ $transaction->date_out->format('M d, Y') }}
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="bi bi-clock"></i> {{ $transaction->date_out->format('H:i') }}
                                    </small>
                                @elseif($transaction->date_in)
                                    <small class="text-muted d-block">
                                        <i class="bi bi-calendar"></i> {{ $transaction->date_in->format('M d, Y') }}
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="bi bi-clock"></i> {{ $transaction->date_in->format('H:i') }}
                                    </small>
                                @else
                                    <small class="text-muted d-block">
                                        <i class="bi bi-calendar"></i> No date set
                                    </small>
                                @endif
                            </div>
                            
                            <div class="stage-status">
                                @if($isCompleted)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle"></i> Complete
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-clock"></i> Pending
                                    </span>
                                @endif
                            </div>
                            
                            <div class="stage-actions mt-2">
                                <a href="{{ route('documents.transactions.edit', [$document, $transaction]) }}" 
                                   class="btn btn-xs btn-outline-primary"
                                   title="Edit Transaction">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('documents.transactions.destroy', [$document, $transaction]) }}" 
                                      method="POST" style="display:inline;"
                                      onsubmit="return confirm('Delete this transaction?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="stage-content text-muted text-center">
                                <p>Not added yet</p>
                                <a href="{{ route('documents.transactions.create', ['document' => $document->id, 'stage' => $stage['name']]) }}" 
                                   class="btn btn-sm btn-outline-primary"
                                   title="Add this stage">
                                    <i class="bi bi-plus-circle"></i> Add
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    .stage-card {
        border: 2px solid #dee2e6;
        border-radius: 8px;
        padding: 12px;
        transition: all 0.3s ease;
        background-color: #f8f9fa;
        min-height: 280px;
        display: flex;
        flex-direction: column;
    }
    
    .stage-card.completed {
        border-color: #28a745;
        background: linear-gradient(135deg, #28a74510 0%, #28a74505 100%);
    }
    
    .stage-card.pending {
        border-color: #ffc107;
        background: linear-gradient(135deg, #ffc10710 0%, #ffc10705 100%);
    }
    
    .stage-card.empty {
        border-color: #dee2e6;
        opacity: 0.7;
    }

    .stage-card.current {
        border-width: 3px;
        box-shadow: 0 0 15px rgba(0, 123, 255, 0.5);
    }
    
    .stage-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    .stage-header {
        text-align: center;
        margin-bottom: 10px;
        font-size: 28px;
    }
    
    .stage-card.completed .stage-header {
        color: #28a745;
    }
    
    .stage-card.pending .stage-header {
        color: #ffc107;
    }
    
    .stage-card.empty .stage-header {
        color: #6c757d;
    }
    
    .stage-label {
        font-weight: 600;
        text-align: center;
        margin-bottom: 10px;
        font-size: 0.9rem;
    }
    
    .stage-content {
        flex: 1;
        padding: 8px 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        margin-bottom: 8px;
    }
    
    .department-name {
        font-weight: 600;
        font-size: 0.85rem;
        margin-bottom: 6px;
    }

    .updates-text {
        font-size: 0.78rem;
        color: #495057;
        font-style: italic;
        background-color: rgba(0, 0, 0, 0.04);
        border-left: 3px solid #adb5bd;
        padding: 3px 6px;
        border-radius: 0 4px 4px 0;
        margin-bottom: 4px;
    }

    .stage-card.completed .updates-text {
        border-left-color: #28a745;
    }

    .stage-card.pending .updates-text {
        border-left-color: #ffc107;
    }
    
    .stage-status {
        text-align: center;
        padding: 4px 0;
    }
    
    .stage-actions {
        display: flex;
        gap: 4px;
        justify-content: center;
    }
    
    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.7rem;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .stage-card {
            min-height: auto;
            padding: 10px;
        }
        
        .stage-header {
            font-size: 24px;
        }
        
        .stage-label {
            font-size: 0.8rem;
        }
    }
</style>