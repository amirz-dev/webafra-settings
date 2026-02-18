<?php

namespace Webafra\LaraSetting\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // default constants
    public const GROUP_SITE    = 'site';
    public const GROUP_EMAIL   = 'email';
    public const GROUP_PAYMENT = 'payment';
    public const GROUP_CUSTOM  = 'custom';

    protected $fillable = [
        'key',
        'value',
        'is_primary',
        'group'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    
    /* ================= Scopes ================= */
    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }
}
