<?php

namespace App\Models;

use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomingOfficialDocument extends Model
{
    use HasFactory, GetValueFromArrayByKeyTraits;

    protected $guarded = [];

    protected const STATUS = [
        'new' => [
            'original' => 'new',
            'converted' => 'Chưa giao',
            'color' => 'primary'
        ],
        'in_progress' => [
            'original' => 'in_progress',
            'converted' => 'Đang xử lý',
            'color' => 'warning'
        ],
        'completed' => [
            'original' => 'completed',
            'converted' => 'Đã hoàn thành',
            'color' => 'success'
        ],
    ];

    protected const PROGRAM_TYPE = [
        'contract' => [
            'original' => 'contract',
            'converted' => 'Hợp đồng',
        ],
        'orther' => [
            'original' => 'orther',
            'converted' => 'Khác',
        ],
    ];

    public function getProgramType($key)
    {
        return $this->getValueFromArrayByKey(self::PROGRAM_TYPE, $key);
    }

    public function getStatus($key)
    {
        return $this->getValueFromArrayByKey(self::STATUS, $key);
    }

    public function incomingOfficialDocumentUsers()
    {
        return $this->hasMany(IncomingOfficialDocumentUser::class, 'incoming_official_document_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, IncomingOfficialDocumentUser::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function officialDocumentType()
    {
        return $this->belongsTo(OfficialDocumentType::class, 'official_document_type_id');
    }

    public function taskAssignee()
    {
        return $this->belongsTo(User::class, 'task_assignee_id');
    }

    public function assingedBy()
    {
        return $this->belongsTo(User::class, 'assinged_by');
    }
}
