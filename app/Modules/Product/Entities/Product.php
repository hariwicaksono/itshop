<?php

namespace App\Modules\Product\Entities;

use CodeIgniter\Entity\Entity;

class Product extends Entity
{
    protected $casts = [
        'product_id' => 'integer',
        'product_name' => 'string',
        'product_price' => 'integer',
        'product_price_normal' => 'integer',
        'discount' => 'integer',
        'discount_percent' => 'integer',
        'stock' => 'integer',
        'stock_min' => 'integer',
        'views' => 'integer',
    ];

    public function __get($key)
    {
        // Paksa return string untuk created_at
        if (in_array($key, ['created_at', 'updated_at', 'deleted_at'])) {
            return isset($this->attributes[$key])
                ? (string) $this->attributes[$key]
                : null;
        }

        return parent::__get($key); // default behavior
    }
}
