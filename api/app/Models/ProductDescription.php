<?php

namespace App\Models;

class ProductDescription extends OpenCartModel
{
    protected $table = 'product_description';

    protected $primaryKey = 'product_id';

    public $incrementing = false;
}
