<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficialDocumentEmail extends Model
{
    use HasFactory;

    protected $table = 'official_document_emails';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
