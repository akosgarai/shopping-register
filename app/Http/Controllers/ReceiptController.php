<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Basket;

class ReceiptController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the list of the current users receipes.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $baskets = Basket::where('user_id', auth()->user()->id)->get();
        return view('receipts', ['baskets' => $baskets]);
    }
    /**
     * Show the details of the given receipt.
     */
    public function view($id)
    {
        $basket = Basket::where('id', '=', $id)->where('user_id', auth()->user()->id)->first();
        return view('receipt', ['basket' => $basket]);
    }
}
