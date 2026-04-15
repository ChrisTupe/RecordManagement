<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Transaction;
use App\Models\Attachment;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color as XlsxColor;

class TransactionController extends Controller
{
    // Show all transactions with document list
    public function index(Request $request)
    {
        $filter   = $request->query('filter', 'all');
        $search   = $request->query('search', '');
        $month    = $request->query('month', '');
        $dateFrom = $request->query('date_from', '');
        $dateTo   = $request->query('date_to', '');

        // Filter documents by the user's department
        $query = Document::where('department_id', auth()->user()->department_id)
            ->with(['transactions' => function ($q) {
                $q->orderBy('created_at', 'desc');
            }]);

        // Apply status filters
        if ($filter === 'in-progress') {
            $query->whereHas('transactions', function ($q) {
                $q->where('status', 'pending');
            });
        } elseif ($filter === 'completed') {
            $query->whereHas('transactions', function ($q) {
                $q->where('status', 'complete');
            });
        } elseif ($filter === '2nd-stage') {
            $query->whereHas('transactions', function ($q) {
                $q->where(function ($subQ) {
                    $subQ->where('stage', 'like', '2%incoming')
                         ->orWhere('stage', 'like', '2%outgoing');
                });
            });
        } elseif ($filter === '3rd-stage') {
            $query->whereHas('transactions', function ($q) {
                $q->where(function ($subQ) {
                    $subQ->where('stage', 'like', '3%incoming')
                         ->orWhere('stage', 'like', '3%outgoing');
                });
            });
        }

        // Search by MISD code or subject
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('misd_code', 'like', '%' . $search . '%')
                  ->orWhere('subject', 'like', '%' . $search . '%');
            });
        }

        // Filter by month
        if (!empty($month)) {
            $query->whereMonth('created_at', $month);
        }

        // Filter by date range
        if (!empty($dateFrom)) {
            $query->whereHas('transactions', function ($q) use ($dateFrom) {
                $q->whereDate('created_at', '>=', $dateFrom);
            });
        }

        if (!empty($dateTo)) {
            $query->whereHas('transactions', function ($q) use ($dateTo) {
                $q->whereDate('created_at', '<=', $dateTo);
            });
        }

        $documents = $query->latest('created_at')->paginate(10);

        $documents->getCollection()->transform(function ($document) {
            $document->transaction_count = $document->transactions->count();
            $document->last_transaction  = $document->transactions->first();
            $document->current_stage     = $document->last_transaction?->stage ?? 'Not Started';
            $document->is_complete       = $document->transactions->where('status', 'complete')->count() === $document->transactions->count()
                                           && $document->transactions->count() > 0;
            return $document;
        });

        return view('monitoring.index', compact('documents', 'filter', 'search', 'month', 'dateFrom', 'dateTo'));
    }

    // Show create transaction form
    public function create(Document $document, Request $request)
    {
        $stage       = $request->query('stage');
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        $isOutgoing = $stage && (strpos($stage, 'outgoing') !== false);

        if ($isOutgoing) {
            return view('monitoring.create-outgoing', compact('document', 'stage', 'departments'));
        }

        return view('monitoring.create-incoming', compact('document', 'stage', 'departments'));
    }

    // Store transaction
    public function store(Request $request, Document $document)
    {
        $validated = $request->validate([
            'stage'         => 'required|string',
            'department'    => 'required|string|max:255',
            'date_out'      => 'nullable|date_format:Y-m-d\TH:i',
            'received_by'   => 'nullable|string|max:255',
            'status'        => 'required|in:pending,complete',
            'date_in'       => 'nullable|date_format:Y-m-d\TH:i',
            'updates'       => 'nullable|string',
            'attachments'   => 'nullable|array',
            'attachments.*' => 'file|max:10240',
        ]);

        $transaction = $document->transactions()->create($validated);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments/' . $document->id . '/' . $transaction->id, 'public');

                Attachment::create([
                    'document_id'    => $document->id,
                    'transaction_id' => $transaction->id,
                    'file_path'      => $path,
                    'file_name'      => $file->getClientOriginalName(),
                ]);
            }
        }

        return redirect()->route('documents.show', $document)->with('success', 'Transaction added successfully.');
    }

    // Show edit form
    public function edit(Document $document, Transaction $transaction)
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $isOutgoing  = strpos($transaction->stage, 'outgoing') !== false;

        if ($isOutgoing) {
            return view('monitoring.edit-outgoing', compact('document', 'transaction', 'departments'));
        }

        return view('monitoring.edit-incoming', compact('document', 'transaction', 'departments'));
    }

    // Update transaction
    public function update(Request $request, Document $document, Transaction $transaction)
    {
        $validated = $request->validate([
            'stage'         => 'required|string',
            'department'    => 'required|string|max:255',
            'date_out'      => 'nullable|date_format:Y-m-d\TH:i',
            'received_by'   => 'nullable|string|max:255',
            'status'        => 'required|in:pending,complete',
            'date_in'       => 'nullable|date_format:Y-m-d\TH:i',
            'updates'       => 'nullable|string',
            'attachments'   => 'nullable|array',
            'attachments.*' => 'file|max:10240',
        ]);

        $transaction->update($validated);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments/' . $document->id . '/' . $transaction->id, 'public');

                Attachment::create([
                    'document_id'    => $document->id,
                    'transaction_id' => $transaction->id,
                    'file_path'      => $path,
                    'file_name'      => $file->getClientOriginalName(),
                ]);
            }
        }

        return redirect()->route('documents.show', $document)->with('success', 'Transaction updated successfully.');
    }

    // Delete transaction
    public function destroy(Document $document, Transaction $transaction)
    {
        $transaction->delete();
        return redirect()->route('documents.show', $document)->with('success', 'Transaction deleted successfully.');
    }

    // Export
    public function exportAll(Request $request)
    {
        // ── 1. Gather & validate filters ──────────────────────────────────────
        $start_date = $request->query('start_date');
        $end_date   = $request->query('end_date');
        $filter     = $request->query('filter', 'all');
        $search     = $request->query('search', '');
        $month      = $request->query('month', '');

        if (!$start_date || !$end_date) {
            return redirect()->route('transactions.index');
        }

        // ── 2. Fetch transactions ─────────────────────────────────────────────
        $query = Transaction::with('document.transactions', 'document.attachments')
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->whereHas('document', function ($q) use ($filter, $search, $month) {
                $q->where('department_id', auth()->user()->department_id);

                if ($filter === 'in-progress') {
                    $q->whereHas('transactions', fn($s) => $s->where('status', 'pending'));
                } elseif ($filter === 'completed') {
                    $q->whereHas('transactions', fn($s) => $s->where('status', 'complete'));
                } elseif ($filter === '2nd-stage') {
                    $q->whereHas('transactions', fn($s) =>
                        $s->where('stage', 'like', '2%incoming')
                          ->orWhere('stage', 'like', '2%outgoing'));
                } elseif ($filter === '3rd-stage') {
                    $q->whereHas('transactions', fn($s) =>
                        $s->where('stage', 'like', '3%incoming')
                          ->orWhere('stage', 'like', '3%outgoing'));
                }

                if (!empty($search)) {
                    $q->where(fn($s) =>
                        $s->where('misd_code', 'like', "%$search%")
                          ->orWhere('subject', 'like', "%$search%"));
                }

                if (!empty($month)) {
                    $q->whereMonth('created_at', $month);
                }
            });

        $allTransactions = $query->get();

        // ── 3. Stage number helper ────────────────────────────────────────────
        $getStageNumber = function ($s) {
            if (in_array($s, ['incoming', 'outgoing'])) return 1;
            preg_match('/^(\d+)/', $s, $m);
            return (int) ($m[1] ?? 1);
        };

        // ── 4. Group: document_id → stage_number → incoming/outgoing ─────────
        $grouped = [];
        foreach ($allTransactions as $txn) {
            $docId    = $txn->document_id;
            $stageNum = $getStageNumber($txn->stage);
            $side     = str_contains($txn->stage, 'outgoing') ? 'outgoing' : 'incoming';
            $grouped[$docId][$stageNum][$side] = $txn;
        }

        // Collect document models for sorting
        $docIds   = array_keys($grouped);
        $docsById = \App\Models\Document::whereIn('id', $docIds)->get()->keyBy('id');

        uksort($grouped, fn($a, $b) =>
            strcmp($docsById[$a]->misd_code ?? '', $docsById[$b]->misd_code ?? '')
        );

        // ── 5. Determine max stage across all documents ───────────────────────
        $maxStage = 1;
        foreach ($grouped as $stages) {
            $maxStage = max($maxStage, max(array_keys($stages)));
        }

        // ── 6. Column layout ──────────────────────────────────────────────────
        // Base columns A–H (stage 1): MISD | DATE IN | FROM DEPT | SUBJECT | DEPT | DATE OUT | RECEIVED BY | PDF LINK
        // Each extra stage (2, 3, …) appends 7 columns:
        //   FROM | Nth DATE AND TIME IN | UPDATES | DEPT | DATE AND TIME OUT | RECEIVED BY | STATUS
        //
        // Helper: convert column index (1-based) to letter(s)
        $colLetter = function (int $idx): string {
            $letters = '';
            while ($idx > 0) {
                $idx--;
                $letters = chr(65 + ($idx % 26)) . $letters;
                $idx     = intdiv($idx, 26);
            }
            return $letters;
        };

        // Base columns end at H (index 8). Extra stages start at index 9.
        $extraStageStartIdx = 9; // I = column 9

        // ── 7. Palette ────────────────────────────────────────────────────────
        $blackText        = '000000';
        $blueText         = '0070C0';
        $redText          = 'FF0000';
        $teal             = '215967';
        $tamarine         = '963634';
        $borderColor      = '000000';

        // Stage-1 base colors
        $white            = 'ebf1de';   // incoming side (A–C)
        $incomingHeaderBg = 'DDEBF7';   // subject col D
        $outgoingHeaderBg = 'fcd5b4';   // outgoing header band
        $outgoingRowBg    = 'fcd5b4';   // E–G outgoing cells
        $red              = 'e6b8b7';   // H (PDF link)

        // Extra-stage colors (matching screenshot)
        $extraIncomingBg  = 'D9EAD3';   // green tint — FROM + DATE IN cols
        $extraUpdatesBg   = 'BDD7EE';   // light blue  — UPDATES col
        $extraOutgoingBg  = 'fcd5b4';   // peach       — DEPT + DATE OUT + RECEIVED BY
        $extraRemarksBg   = 'e6b8b7';   // rose/pink   — STATUS col

        // ── 8. Build Spreadsheet ──────────────────────────────────────────────
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Transactions');
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(11);

        // ── 8a. Column widths ─────────────────────────────────────────────────
        // Base stage widths
        $baseWidths = [
            1 => 18,  // A  MISD
            2 => 26,  // B  Date & Time In
            3 => 22,  // C  From Dept
            4 => 65,  // D  Subject
            5 => 14,  // E  Dept
            6 => 26,  // F  Date & Time Out
            7 => 26,  // G  Received By
            8 => 55,  // H  PDF Link
        ];
        foreach ($baseWidths as $idx => $w) {
            $sheet->getColumnDimension($colLetter($idx))->setWidth($w);
        }

        // Extra stage widths (7 cols each)
        $extraWidths = [22, 26, 30, 14, 26, 26, 30]; // FROM | DATE IN | UPDATES | DEPT | DATE OUT | RECEIVED BY | STATUS
        for ($s = 2; $s <= $maxStage; $s++) {
            $startIdx = $extraStageStartIdx + ($s - 2) * 7;
            foreach ($extraWidths as $offset => $w) {
                $sheet->getColumnDimension($colLetter($startIdx + $offset))->setWidth($w);
            }
        }

        // ── 8b. Helper: apply style block ─────────────────────────────────────
        $applyStyle = function (string $range, array $style) use ($sheet) {
            $sheet->getStyle($range)->applyFromArray($style);
        };

        $borderMedium = ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF' . $borderColor]]];

        // ── 8c. Row 1 — Section header bands ──────────────────────────────────
        // Base stage: A1:C1 = INCOMING, D1 blank, E1:H1 = OUTGOING
        $sheet->mergeCells('A1:C1');
        $sheet->mergeCells('E1:H1');
        $sheet->setCellValue('A1', 'INCOMING');
        $sheet->setCellValue('E1', 'OUTGOING');

        $applyStyle('A1:C1', [
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF' . $blackText]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $white]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => $borderMedium,
        ]);
        $applyStyle('D1', [
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $incomingHeaderBg]],
            'borders' => $borderMedium,
        ]);
        $applyStyle('E1:H1', [
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF' . $blackText]],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $outgoingHeaderBg]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => $borderMedium,
        ]);

        // Extra stage header bands
        $ordinals = ['', '', '2ND', '3RD', '4TH', '5TH', '6TH', '7TH', '8TH', '9TH'];
        for ($s = 2; $s <= $maxStage; $s++) {
            $startIdx  = $extraStageStartIdx + ($s - 2) * 7;
            $endIdx    = $startIdx + 6;
            $startCol  = $colLetter($startIdx);
            $endCol    = $colLetter($endIdx);
            $label     = ($ordinals[$s] ?? "{$s}TH") . ' OUTGOING';

            $sheet->mergeCells("{$startCol}1:{$endCol}1");
            $sheet->setCellValue("{$startCol}1", $label);
            $applyStyle("{$startCol}1:{$endCol}1", [
                'font'      => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF' . $blackText]],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $outgoingHeaderBg]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => $borderMedium,
            ]);
        }

        $sheet->getRowDimension(1)->setRowHeight(30);

        // ── 8d. Row 2 — Column labels ──────────────────────────────────────────
        // Base labels
        $baseLabels = [
            1 => ['MISD',              $blackText, $white],
            2 => ['DATE AND TIME IN',  $teal,      $white],
            3 => ['FROM (DEPT)',        $blueText,  $white],
            4 => ['SUBJECT',           $blackText, $incomingHeaderBg],
            5 => ['DEPT',              $tamarine,  $outgoingRowBg],
            6 => ['DATE AND TIME OUT', $redText,   $outgoingRowBg],
            7 => ['RECEIVED BY',       $blackText, $outgoingRowBg],
            8 => ['PDF LINK',          $blackText, $red],
        ];
        foreach ($baseLabels as $idx => [$label, $textColor, $bgColor]) {
            $cell = $colLetter($idx) . '2';
            $sheet->setCellValue($cell, $label);
            $applyStyle($cell, [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FF' . $textColor]],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $bgColor]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders'   => $borderMedium,
            ]);
        }

        // Extra stage labels — 7 cols each
        // Colors per column position within extra stage block:
        // [0]=FROM(green), [1]=DATE IN(green), [2]=UPDATES(blue), [3]=DEPT(peach), [4]=DATE OUT(peach), [5]=RECEIVED BY(peach), [6]=STATUS(rose)
        $extraLabelDefs = [
            // [label, textColor, bgColor]
            0 => ['FROM',                 $tamarine,  $extraIncomingBg],
            1 => ['DATE AND TIME IN',     $redText,   $extraIncomingBg],
            2 => ['UPDATES',              $blackText, $extraUpdatesBg],
            3 => ['DEPT',                 $tamarine,  $extraOutgoingBg],
            4 => ['DATE AND TIME OUT',    $redText,   $extraOutgoingBg],
            5 => ['RECEIVED BY',          $blackText, $extraOutgoingBg],
            6 => ['STATUS',               $blackText, $extraRemarksBg],
        ];

        for ($s = 2; $s <= $maxStage; $s++) {
            $startIdx = $extraStageStartIdx + ($s - 2) * 7;
            $ordStr   = $ordinals[$s] ?? "{$s}TH";
            foreach ($extraLabelDefs as $offset => [$label, $textColor, $bgColor]) {
                // Prefix DATE AND TIME IN with ordinal
                $displayLabel = ($offset === 1) ? "{$ordStr} {$label}" : $label;
                $cell         = $colLetter($startIdx + $offset) . '2';
                $sheet->setCellValue($cell, $displayLabel);
                $applyStyle($cell, [
                    'font'      => ['bold' => true, 'color' => ['argb' => 'FF' . $textColor]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $bgColor]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders'   => $borderMedium,
                ]);
            }
        }

        $sheet->getRowDimension(2)->setRowHeight(40);

        // ── 8e. Data rows ──────────────────────────────────────────────────────
        $rowNum = 3;

        foreach ($grouped as $docId => $stages) {
            $doc        = $docsById[$docId] ?? null;
            $isFirstRow = true;

            ksort($stages);

            // We output ONE row per document (stage 1 incoming+outgoing side by side,
            // then stage 2+ appended as extra column groups on the SAME row).
            // If a document has multiple stage-1 pairs (unlikely but possible), each
            // stage-1 pair gets its own row with stage 2+ columns blank except the first.

            // Collect stage-1 pairs and higher
            $stage1 = $stages[1] ?? [];
            $higherStages = array_filter($stages, fn($k) => $k > 1, ARRAY_FILTER_USE_KEY);
            ksort($higherStages);

            // Determine how many rows this document needs
            // (one row per stage-1 pair; higher stages fill the first row's extra columns)
            $incoming1 = $stage1['incoming'] ?? null;
            $outgoing1 = $stage1['outgoing'] ?? null;

            // PDF link for base stage
            $pdfLink = '';
            $srcTxn  = $outgoing1 ?? $incoming1;
            if ($srcTxn && $doc && $doc->attachments) {
                $att = $doc->attachments->where('transaction_id', $srcTxn->id)->first();
                if ($att) $pdfLink = Storage::url($att->file_path);
            }

            // ── Base columns A–H ──────────────────────────────────────────────
            $sheet->setCellValue("A{$rowNum}", $doc->misd_code ?? '');
            $sheet->setCellValue("B{$rowNum}", $incoming1 && $incoming1->date_in  ? $incoming1->date_in->format('M/d/Y g:i A')  : '');
            $sheet->setCellValue("C{$rowNum}", $incoming1 ? $incoming1->department : '');
            $sheet->setCellValue("D{$rowNum}", $doc->subject ?? '');
            $sheet->setCellValue("E{$rowNum}", $outgoing1 ? $outgoing1->department : '');
            $sheet->setCellValue("F{$rowNum}", $outgoing1 && $outgoing1->date_out ? $outgoing1->date_out->format('M/d/Y g:i A') : '');
            $sheet->setCellValue("G{$rowNum}", $outgoing1 ? ($outgoing1->received_by ?? '') : '');
            $sheet->setCellValue("H{$rowNum}", $pdfLink);

            // A–C style
            $applyStyle("A{$rowNum}:C{$rowNum}", [
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $white]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders'   => $borderMedium,
            ]);
            // D style
            $applyStyle("D{$rowNum}", [
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $incomingHeaderBg]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true],
                'borders'   => $borderMedium,
            ]);
            // E–G style
            $applyStyle("E{$rowNum}:G{$rowNum}", [
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $outgoingRowBg]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders'   => $borderMedium,
            ]);
            // H style
            $applyStyle("H{$rowNum}", [
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $red]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                'borders'   => $borderMedium,
            ]);

            // Per-column fonts (base)
            $sheet->getStyle("A{$rowNum}")->getFont()->setBold(true);
            $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("B{$rowNum}")->getFont()->setBold(false)->setColor(new XlsxColor('FF' . $teal));
            $sheet->getStyle("B{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("C{$rowNum}")->getFont()->setBold(true)->setColor(new XlsxColor('FF' . $blueText));
            $sheet->getStyle("C{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("D{$rowNum}")->getFont()->setColor(new XlsxColor('FF' . $blackText));

            $sheet->getStyle("E{$rowNum}")->getFont()->setBold(true)->setColor(new XlsxColor('FF' . $tamarine));
            $sheet->getStyle("E{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("F{$rowNum}")->getFont()->setBold(true)->setColor(new XlsxColor('FF' . $redText));
            $sheet->getStyle("F{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("G{$rowNum}")->getFont()->setColor(new XlsxColor('FF' . $blackText));
            $sheet->getStyle("G{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if (!empty($pdfLink)) {
                $sheet->getCell("H{$rowNum}")->getHyperlink()->setUrl($pdfLink);
                $sheet->getStyle("H{$rowNum}")->getFont()->setUnderline(true)->setColor(new XlsxColor('FF0563C1'));
            }

            // ── Extra stage columns (I onwards) ───────────────────────────────
            // Fill columns for stages 2..maxStage; blank if this doc has no such stage
            for ($s = 2; $s <= $maxStage; $s++) {
                $startIdx = $extraStageStartIdx + ($s - 2) * 7;

                $inTxn  = $higherStages[$s]['incoming'] ?? null;
                $outTxn = $higherStages[$s]['outgoing'] ?? null;

                // Column positions within this extra block
                $cFrom      = $colLetter($startIdx + 0);
                $cDateIn    = $colLetter($startIdx + 1);
                $cUpdates   = $colLetter($startIdx + 2);
                $cDept      = $colLetter($startIdx + 3);
                $cDateOut   = $colLetter($startIdx + 4);
                $cRecvBy    = $colLetter($startIdx + 5);
                $cStatus    = $colLetter($startIdx + 6);

                // FROM = incoming department for this stage
                $sheet->setCellValue("{$cFrom}{$rowNum}",    $inTxn  ? $inTxn->department  : '');
                // DATE IN = incoming date_in
                $sheet->setCellValue("{$cDateIn}{$rowNum}",  $inTxn  && $inTxn->date_in  ? $inTxn->date_in->format('M/d/Y g:i A')   : '');
                // UPDATES = incoming updates field
                $sheet->setCellValue("{$cUpdates}{$rowNum}", $inTxn  ? ($inTxn->updates ?? '')  : '');
                // DEPT = outgoing department
                $sheet->setCellValue("{$cDept}{$rowNum}",    $outTxn ? $outTxn->department : '');
                // DATE OUT = outgoing date_out
                $sheet->setCellValue("{$cDateOut}{$rowNum}", $outTxn && $outTxn->date_out ? $outTxn->date_out->format('M/d/Y g:i A') : '');
                // RECEIVED BY = outgoing only
                $sheet->setCellValue("{$cRecvBy}{$rowNum}",  $outTxn ? ($outTxn->received_by ?? '') : '');
                // STATUS = outgoing status
                $sheet->setCellValue("{$cStatus}{$rowNum}",  $outTxn ? ($outTxn->status ?? '') : '');

                // Styles
                // FROM + DATE IN — green
                $applyStyle("{$cFrom}{$rowNum}:{$cDateIn}{$rowNum}", [
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $extraIncomingBg]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders'   => $borderMedium,
                ]);
                $sheet->getStyle("{$cFrom}{$rowNum}")->getFont()->setBold(true)->setColor(new XlsxColor('FF' . $tamarine));
                $sheet->getStyle("{$cDateIn}{$rowNum}")->getFont()->setBold(false)->setColor(new XlsxColor('FF' . $redText));

                // UPDATES — light blue
                $applyStyle("{$cUpdates}{$rowNum}", [
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $extraUpdatesBg]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders'   => $borderMedium,
                ]);
                $sheet->getStyle("{$cUpdates}{$rowNum}")->getFont()->setColor(new XlsxColor('FF' . $blackText));

                // DEPT + DATE OUT + RECEIVED BY — peach
                $applyStyle("{$cDept}{$rowNum}:{$cRecvBy}{$rowNum}", [
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $extraOutgoingBg]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders'   => $borderMedium,
                ]);
                $sheet->getStyle("{$cDept}{$rowNum}")->getFont()->setBold(true)->setColor(new XlsxColor('FF' . $tamarine));
                $sheet->getStyle("{$cDateOut}{$rowNum}")->getFont()->setBold(true)->setColor(new XlsxColor('FF' . $redText));
                $sheet->getStyle("{$cRecvBy}{$rowNum}")->getFont()->setColor(new XlsxColor('FF' . $blackText));

                // STATUS — rose/pink
                $applyStyle("{$cStatus}{$rowNum}", [
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF' . $extraRemarksBg]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders'   => $borderMedium,
                ]);
                $sheet->getStyle("{$cStatus}{$rowNum}")->getFont()->setColor(new XlsxColor('FF' . $blackText));
            }

            $sheet->getRowDimension($rowNum)->setRowHeight(80);
            $rowNum++;
        }

        // ── 8f. Freeze panes ───────────────────────────────────────────────────
        $sheet->freezePane('B3');

        // ── 8g. Outer border ───────────────────────────────────────────────────
        $lastRow    = $rowNum - 1;
        $lastColIdx = 8 + ($maxStage - 1) * 7;
        $lastCol    = $colLetter($lastColIdx);

        if ($lastRow >= 3) {
            $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
                'borders' => [
                    'outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => 'FF' . $borderColor]],
                ],
            ]);
        }

        // ── 9. Stream the .xlsx response ───────────────────────────────────────
        $filename = 'transactions_export_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}