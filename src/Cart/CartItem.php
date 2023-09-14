<?php

namespace App\Cart;

use App\Entity\Product;

class CartItem
{
    protected $product;
    protected $qty;

    public function __construct(Product $product, int $qty)
    {       
        $this->product = $product;
        $this->qty = $qty; 
    }

    public function getTotal(): int
    {
        return $this->product->getPrice() * $this->qty;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function getQty()
    {
        return $this->qty;
    }
}