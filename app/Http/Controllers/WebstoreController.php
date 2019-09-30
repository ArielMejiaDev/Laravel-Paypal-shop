<?php

namespace App\Http\Controllers;

use App\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;

class WebstoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();
        return view('home')->with('products', $products);
    }

    /**
     * Not Good Practice
     * TODO
     * change this method to other controller
     */
    public function addToCart(Product $product)
    {
        Cart::setGlobalTax(12);
        Cart::add($product->id, $product->name, 1, $product->price);
        return redirect('/');
    }

    /**
     * REMOVE FROM CART METHOD NOT BEST PRACTICE HERE
     * TODO
     * move to Cart controller
     */
    public function removeProductFromCart($productId)
    {
        Cart::remove($productId);
        return redirect('/home');
    }

    /**
     * DESTROY THE CART NOT BEST PRACTICE HERE
     * TODO
     * MOVE TO CART CONTROLLER
     */
    public function destroyCart()
    {
        Cart::destroy();
        return redirect('/home');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
