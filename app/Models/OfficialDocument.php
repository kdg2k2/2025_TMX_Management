<?php

namespace App\Models;

use App\Traits\GetValueFromArrayByKeyTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficialDocument extends Model
{
    use HasFactory, GetValueFromArrayByKeyTraits;

    protected $guarded = [];

    protected const STATUS = [
        'pending_review' => [
            'original' => 'pending_review',
            'converted' => 'Chờ kiểm tra',
            'color' => 'warning',
            'icon' => 'ti ti-file-search',
        ],
        'reviewed' => [
            'original' => 'reviewed',
            'converted' => 'Đã kiểm tra',
            'color' => 'primary',
            'icon' => 'ti ti-clipboard-check',
        ],
        'approved' => [
            'original' => 'approved',
            'converted' => 'Đã duyệt',
            'color' => 'info',
            'icon' => 'ti ti-file-certificate',
        ],
        'rejected' => [
            'original' => 'rejected',
            'converted' => 'Bị từ chối',
            'color' => 'danger',
            'icon' => 'ti ti-file-x',
        ],
        'released' => [
            'original' => 'released',
            'converted' => 'Đã phát hành',
            'color' => 'success',
            'icon' => 'ti ti-file-export',
        ],
    ];

    protected const PROGRAM_TYPE = [
        'incoming' => [
            'original' => 'incoming',
            'converted' => 'Nhiệm vụ văn bản đến',
            'color' => 'info',
            'icon' => 'ti ti-inbox',
        ],
        'contract' => [
            'original' => 'contract',
            'converted' => 'Hợp đồng',
            'color' => 'success',
            'icon' => 'ti ti-file-text',
        ],
        'orther' => [
            'original' => 'orther',
            'converted' => 'Khác',
            'color' => 'secondary',
            'icon' => 'ti ti-dots-circle-horizontal',
        ],
    ];

    protected const RELEASE_TYPE = [
        'new' => [
            'original' => 'new',
            'converted' => 'Phát hành mới',
            'color' => 'success',
            'icon' => 'ti ti-file-plus',
        ],
        'revision' => [
            'original' => 'revision',
            'converted' => 'Phát hành lại',
            'color' => 'warning',
            'icon' => 'ti ti-file-pencil',
        ],
        'reply' => [
            'original' => 'reply',
            'converted' => 'Công văn trả lời',
            'color' => 'info',
            'icon' => 'ti ti-message-reply',
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

    public function getReleaseType($key)
    {
        return $this->getValueFromArrayByKey(self::RELEASE_TYPE, $key);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function officialDocumentType()
    {
        return $this->belongsTo(OfficialDocumentType::class);
    }

    public function officialDocumentSector()
    {
        return $this->belongsTo(OfficialDocumentSector::class);
    }

    public function users()
    {
        return $this
            ->belongsToMany(User::class, OfficialDocumentEmail::class)
            ->withTimestamps();
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function signedBy()
    {
        return $this->belongsTo(User::class, 'signed_by');
    }
}
