<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Product;
use App\Seller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;


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
        $data['image'] = $request->image->store('');

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

        $this->verifySeller($seller, $product);

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
     * @param  \App\Seller $seller
     * @param Product $product
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Seller $seller, Product $product)
    {
        $this->verifySeller($seller, $product);

        Storage::delete($product->image);

        $product->delete();

        return $this->showOne($product);

    }

    /**
     * @param Seller $seller
     * @param Product $product
     */
    protected function  verifySeller(Seller $seller, Product $product) {
        if ($seller->id != $product->seller_id) {
            throw new HttpException(422, 'El vendedor especificado no es el vendedor real del producto');
        }
    }

}
