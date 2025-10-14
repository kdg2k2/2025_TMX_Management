<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractFileTypeExtension extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function extension()
    {
        return $this->belongsTo(FileExtension::class, 'extension_id');
    }
}
