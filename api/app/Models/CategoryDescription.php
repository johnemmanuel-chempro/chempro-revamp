<?php

namespace App\Models;

class CategoryDescription extends OpenCartModel
{
    protected $table = 'category_description';

    protected $primaryKey = 'category_id';

    public $incrementing = false;
}
