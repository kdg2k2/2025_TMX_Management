<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficialDocumentType extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function officialDocuments()
    {
        return $this->hasMany(OfficialDocument::class, 'official_document_type_id');
    }

    public function incomingOfficialDocuments()
    {
        return $this->hasMany(IncomingOfficialDocument::class, 'official_document_type_id');
    }
}
