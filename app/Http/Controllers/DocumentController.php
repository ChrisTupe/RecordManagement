<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Transaction;
use App\Models\Attachment;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    // Dashboard - Show statistics
    public function index()
    {
        // Filter by user's department
        $userDepartmentId = auth()->user()->department_id;
        
        $totalDocuments = Document::where('department_id', $userDepartmentId)->count();
        $totalTransactions = Transaction::whereIn('document_id', Document::where('department_id', $userDepartmentId)->pluck('id'))->count();
        $totalAttachments = Attachment::whereIn('document_id', Document::where('department_id', $userDepartmentId)->pluck('id'))->count();
        
        $recentDocuments = Document::where('department_id', $userDepartmentId)->orderBy('created_at', 'desc')->take(5)->get();
        
        // Incoming documents = transactions with any incoming stage in user's department
        $incomingCount = Transaction::whereIn('document_id', Document::where('department_id', $userDepartmentId)->pluck('id'))
            ->whereIn('stage', ['incoming', '2nd incoming', '3rd incoming'])->count();
        
        // Outgoing documents = transactions with any outgoing stage in user's department
        $outgoingCount = Transaction::whereIn('document_id', Document::where('department_id', $userDepartmentId)->pluck('id'))
            ->whereIn('stage', ['outgoing', '2nd outgoing', '3rd outgoing'])->count();
        
        // Pending and Completed transactions in user's department
        $pendingCount = Transaction::whereIn('document_id', Document::where('department_id', $userDepartmentId)->pluck('id'))
            ->where('status', 'pending')->count();
        $completedCount = Transaction::whereIn('document_id', Document::where('department_id', $userDepartmentId)->pluck('id'))
            ->where('status', 'complete')->count();
        
        // Complete documents = documents where all transactions are complete in user's department
        $completeDocuments = Document::where('department_id', $userDepartmentId)
            ->with('transactions')
            ->get()
            ->filter(function ($doc) {
                return $doc->transactions->count() > 0 && 
                       $doc->transactions->every(fn ($t) => $t->status === 'complete');
            })
            ->count();
        
        // In progress documents = documents with at least one transaction but not all complete
        $inProgressDocuments = $totalDocuments - $completeDocuments;
        
        // Get monthly documents data (last 12 months) for user's department
        $monthlyDocuments = [];
        $monthlyLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyLabels[] = $date->format('M Y');
            $count = Document::where('department_id', $userDepartmentId)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $monthlyDocuments[] = $count;
        }
        
        // Get transactions per department (only user's department for now)
        $departmentTransactions = Transaction::whereIn('document_id', Document::where('department_id', $userDepartmentId)->pluck('id'))
            ->select('department', DB::raw('count(*) as count'))
            ->groupBy('department')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
        
        $departmentLabels = $departmentTransactions->pluck('department')->toArray();
        $departmentCounts = $departmentTransactions->pluck('count')->toArray();
        
        return view('dashboard', compact(
            'totalDocuments',
            'totalTransactions', 
            'totalAttachments',
            'recentDocuments',
            'incomingCount',
            'outgoingCount',
            'pendingCount',
            'completedCount',
            'completeDocuments',
            'inProgressDocuments',
            'monthlyDocuments',
            'monthlyLabels',
            'departmentLabels',
            'departmentCounts'
        ));
    }

    // List all documents (incoming documents)
    public function listDocuments(Request $request)
    {
        $search = $request->query('search', '');
        
        // Filter by user's department
        $query = Document::where('department_id', auth()->user()->department_id);
        
        // Search by MISD code or subject
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('misd_code', 'like', '%' . $search . '%')
                  ->orWhere('subject', 'like', '%' . $search . '%');
            });
        }
        
        $documents = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('documents.index', compact('documents', 'search'));
    }

    // Show document detail page
    public function show(Document $document)
    {
        $transactions = $document->transactions()->orderBy('created_at', 'desc')->get();
        $attachments = $document->attachments()->orderBy('created_at', 'desc')->get();
        return view('documents.show', compact('document', 'transactions', 'attachments'));
    }

    // Show create form
    public function create()
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('documents.create.monitoring', compact('departments'));
    }

    // Store new document
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'date_in' => 'nullable|date_format:Y-m-d\TH:i',
            'received_by' => 'nullable|string|max:255',
            'status' => 'required|in:pending,complete',
            'updates' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // Max 10MB per file
        ]);

        // Create document with user's department
        $document = Document::create([
            'subject' => $validated['subject'],
            'department_id' => auth()->user()->department_id,
        ]);

        // Create incoming transaction
        $transaction = $document->transactions()->create([
            'stage' => 'incoming',
            'department' => $validated['department'],
            'date_in' => $validated['date_in'],
            'received_by' => $validated['received_by'],
            'status' => $validated['status'],
            'updates' => $validated['updates'],
        ]);

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments/' . $document->id . '/' . $transaction->id, 'public');
                
                Attachment::create([
                    'document_id' => $document->id,
                    'transaction_id' => $transaction->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        return redirect()->route('documents.list')->with('success', 'Document created successfully.');
    }

    // Show edit form
    public function edit(Document $document)
    {
        return view('documents.edit', compact('document'));
    }

    // Update document
    public function update(Request $request, Document $document)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
        ]);

        $document->update($validated);

        return redirect()->route('documents.show', $document)->with('success', 'Document updated successfully.');
    }

    // Delete document
    public function destroy(Document $document)
    {
        $document->delete();
        return redirect()->route('documents.index')->with('success', 'Document deleted successfully.');
    }

    // Export document transactions to CSV
    public function exportTransactions(Document $document)
    {
        $transactions = $document->transactions()->with('attachments')->get();
        
        $filename = "transactions_" . $document->misd_code . "_" . now()->format('Y-m-d_H-i-s') . ".csv";
        
        $headers = array(
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        
        $callback = function() use ($document, $transactions) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, array(
                'Document MISD Code',
                'Document Subject',
                'Stage',
                'Department',
                'Status',
                'Date In',
                'Date Out',
                'Received By',
                'Updates',
                'Number of Attachments',
                'Created At'
            ));
            
            fputcsv($file, array($document->misd_code, $document->subject, '', '', '', '', '', '', '', '', ''));
            fputcsv($file, array('', '', '', '', '', '', '', '', '', '', ''));
            
            // Transactions data
            foreach($transactions as $transaction) {
                fputcsv($file, array(
                    '',
                    '',
                    ucfirst($transaction->stage),
                    $transaction->department,
                    ucfirst($transaction->status),
                    $transaction->date_in ? $transaction->date_in->format('Y-m-d H:i') : '',
                    $transaction->date_out ? $transaction->date_out->format('Y-m-d H:i') : '',
                    $transaction->received_by ?? '',
                    $transaction->updates ?? '',
                    $transaction->attachments()->count(),
                    $transaction->created_at->format('Y-m-d H:i')
                ));
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
