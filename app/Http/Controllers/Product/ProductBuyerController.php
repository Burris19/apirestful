<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Product;

class ProductBuyerController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param Product $product
     * @return void
     */
    public function index(Product $product)
    {
        $buyers = $product
            ->transactions()
            ->with('buyer')
            ->get()
            ->pluck('buyer')
            ->unique('id')
            ->values();

        return $this->showAll($buyers);
    }

}
