<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ConvertRequest extends Model
{
    use SoftDeletes;
    /*
     * Types
     */
    public const TYPE_YOUTUBE = 'youtube';
    public const TYPE_YOUTUBE_TEXT = 'Youtube';
    public const TYPE_FACEBOOK = 'facebook';
    public const TYPE_FACEBOOK_TEXT = 'Facebook';
    public const TYPE_INSTAGRAM = 'instagram';
    public const TYPE_INSTAGRAM_TEXT = 'Instagram';
    /*
     * Methods
     */
    public const METHOD_DIRECT = 'direct';
    public const METHOD_DIRECT_TEXT = 'Direct';
    public const METHOD_CONVERSION = 'conversion';
    public const METHOD_CONVERSION_TEXT = 'Conversion';
    
    protected $casts = [
        'details' => 'array',
    ];

    protected $fillable = [
        'id',
        'external_id',
        'url',
        'type',
        'method',
        'details',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            if (empty($model->external_id)) {
                $model->external_id = (string) Str::uuid();
            }
        });
    }
}
