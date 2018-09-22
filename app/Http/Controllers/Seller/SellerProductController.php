<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Product;
use App\Seller;
use App\User;
use Illuminate\Http\Request;


class SellerProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @param Seller $seller
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        $products = $seller->products;

        return $this->showAll($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param User $seller
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $seller)
    {
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'image' => 'required|image',
            'quantity' => 'required|integer|min:1'
        ];

        $this->validate($request, $rules);

        $data = $request->all();
        $data['status'] = Product::PRODUCT_NOT_AVAILABLE;
        $data['seller_id'] = $seller->id;
        $data['image'] = 'default_1.jpg';

        $product = Product::create($data);

        return $this->showOne($product, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Seller $seller
     * @param Product $product
     * @return void
     */
    public function update(Request $request, Seller $seller, Product $product)
    {
        $rules = [
            'quantity' => 'integer|min:1',
            'image' => 'image',
            'status' => 'in:' . Product::PRODUCT_NOT_AVAILABLE . ',' . Product::PRODUCT_AVAILABLE
        ];

        $this->validate($request, $rules);

        if ($seller->id != $product->seller_id) {
            return $this->errorResponse('El vendedor especificado no es el vendedor real del producto', 422);
        }

        $product->fill($request->only([
            'name',
            'description',
            'quantity'
        ]));

        if($request->has('status')) {
            $product->status = $request->status;

            if($product->isAvailable() && $product->categories()->count() == 0) {
                return $this->errorResponse('Un producto activo debe tener al menos una categoria', 409);
            }
        }

        if ($product->isClean()) {
            return $this->errorResponse('Se debe especificar al menos un valor diferente para actualizar', 422);
        }

        $product->save();

        return $this->showOne($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller)
    {
        //
    }
}
