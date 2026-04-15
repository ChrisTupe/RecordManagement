<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['subject', 'department_id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            // Year and month
            $year = now()->format('y');   // e.g. 26 for 2026
            $month = now()->format('m');  // e.g. 02 for February

            // Find last document for this year/month
            $lastDoc = Document::whereYear('created_at', now()->year)
                               ->whereMonth('created_at', now()->month)
                               ->orderBy('id', 'desc')
                               ->first();

            // Increment number
            $increment = $lastDoc
                ? intval(substr($lastDoc->misd_code, -2)) + 1
                : 1;

            // Format MISD code
            $document->misd_code = "MISD {$year}-{$month}-" . str_pad($increment, 2, '0', STR_PAD_LEFT);
        });
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
