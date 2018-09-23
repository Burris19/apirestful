<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Product;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductBuyerTransactionController extends ApiController
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product, User $buyer)
    {
        $rules = [
            'quantity' => 'required|min:1'
        ];

        $this->validate($request, $rules);

        if ($buyer->id === $product->seller->id) {
            return $this->errorResponse('El comprador debe ser diferente del vendedor', 409);
        }

        if (!$buyer->isVerificated()) {
            return $this->errorResponse('El comprador debe ser un usuario verificado', 409);
        }

        if (!$product->seller->isVerificated()) {
            return $this->errorResponse('El vendedor debe ser un usuario verificado', 409);
        }

        if (!$product->isAvailable()) {
            return $this->errorResponse('El producto no esta disponible para esta transaccion', 409);
        }

        if ($product->quantity < $request->quantity) {
            return $this->errorResponse('El producto no tiene la cantidad disponible requerida para esta transaccion', 409);
        }

        return DB::transaction(function () use($request, $product, $buyer) {
            $product->quantity -= $request->quantity;
            $product->save();

            $transaction = Transaction::create([
                'quantity' => $request->quantity,
                'buyer_id' => $buyer->id,
                'product_id' => $product->id,
            ]);

            return $this->showOne($transaction, 201);
        });
    }
}
