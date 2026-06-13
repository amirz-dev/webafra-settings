<?php

namespace Webafra\LaraSetting\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'group',
        'key',
        'value',
        'is_primary',
        'group'
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }
}
