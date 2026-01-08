<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingOfficialDocumentUser extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function incomingOfficialDocument()
    {
        return $this->belongsTo(IncomingOfficialDocument::class, 'incoming_official_document_id');
    }
}
