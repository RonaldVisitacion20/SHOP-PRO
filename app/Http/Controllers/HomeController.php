<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currency;
use App\Models\PaymentPlatform;
class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $currencies=Currency::all();
        $paymentPlatform=PaymentPlatform::all();
        return view('home')->with([
            'currencies'=>$currencies,
            'PaymentPlatforms'=>$paymentPlatform,
        ]);
    }
}
