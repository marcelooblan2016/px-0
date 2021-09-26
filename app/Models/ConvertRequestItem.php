<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ConvertRequestItem extends Model
{
    /*
     * File Types
     */
    public const FILE_TYPE_MP4 = 'youtube';
    public const FILE_TYPE_MP3 = 'mp3';
    /*
     * Status
     */
    public const STATUS_PROCESSING = 0;
    public const STATUS_DOWNLOADED = 1;
    public const STATUS_CONVERTED = 2;
    public const STATUS_JOB_ERROR_BAD_REQUEST = 400;
    public const STATUS_JOB_ERROR_TIMEOUT = 408;
    
    protected $casts = [
        'details' => 'array',
    ];

    protected $fillable = [
        'convert_request_id',
        'format_id',
        'file_id',
        'file_type',
        'quality',
        'status',
        'details',
        'path',
        'url',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            if (empty($model->file_id)) {
                $model->file_id = (string) Str::uuid();
            }
        });
    }

    public function convertRequest()
    {
        return $this->belongsTo(ConvertRequest::class);
    }
}
