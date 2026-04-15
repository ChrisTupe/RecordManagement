<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        'document_id',
        'transaction_id',
        'file_name',
        'file_path',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
