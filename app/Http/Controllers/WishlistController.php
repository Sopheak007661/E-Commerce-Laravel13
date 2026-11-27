<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facades\Cart;

class WishlistController extends Controller
{
    public function index()
    {
        return view('wishlist.index');
    }
    
    public function add_to_wishlist(Request $request){
        $request ->validate([
            'product_id' => 'required',
            'name' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        Cart::instance('wishlist')->add($request->product_id , $request->name , $request->quantity, $request->price)
            -> associate('\App\Models\Product');
        return back() -> with('success' , 'Product added to wishlist successfully');
    }

    public function remove_from_wishlist(string $rowId){
        Cart::instance('wishlist')->remove($rowId);
        return back() -> with('success', 'Product removed from wishlist successfully!');
    }

    public function clear_wishlist()
    {
        Cart::instance('wishlist') -> destroy() ;
        return back()->with('success', 'wishlist cleared successfully!');
    }

}
