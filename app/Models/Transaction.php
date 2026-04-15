<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'document_id',
        'stage',
        'department',
        'date_out',
        'received_by',
        'status',
        'date_in',
        'updates',
    ];

    protected $casts = [
        'date_out' => 'datetime',
        'date_in' => 'datetime',
    ];

    // Department choices
    public const DEPARTMENTS = [
        'Office of the President and CEO',
        'Office of the Vice-President for Admin and Finance Group',
        'Office of the Vice-President for Operation and Business Devt Group',
        'Office of the Assistant Vice-President for Legal Services',
        'Strategy and Corporate Management Dept.',
        'Management System Information Div',
        'Management Information System Dept',
        'Records Management Div',
        'Finance Dept.',
        'New Business Venture Unit',
        'Admin. Dept.',
        'Marketing Dept.',
        'Engineering Dept.',
        'Security Dept.',
        'Public Affairs Div.',
        'HRD',
        'COA'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
