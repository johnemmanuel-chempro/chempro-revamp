<?php

namespace App\Models;

class Setting extends OpenCartModel
{
    protected $table = 'setting';

    protected $primaryKey = 'setting_id';

    protected $casts = [
        'serialized' => 'boolean',
        'store_id' => 'integer',
    ];
}
