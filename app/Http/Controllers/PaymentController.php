<?php

namespace App\Http\Controllers;

use App\Services\PayPalServices;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function pay(Request $request)
    {
        // dd($request->payment_platform);
        $rules = [
            'value' => ['required', 'numeric', 'min:1'],
            'currency' => ['required', 'exists:currencies,iso'],
            'payment_platform' => ['required', 'exists:payment_platforms,id'],
        ];
        // dd($request->validate($rules));

        $request->validate($rules); // esta peticion se esta inyectando enla variable $request
        $paymentPlatform = resolve(PayPalServices::class);
        return $paymentPlatform->handlePayment($request);

        // return $request->all();
    }

    public function approval()
    {
        $paymentPlatform = resolve(PayPalServices::class);
        return $paymentPlatform->handleApproval();

    }

    public function cancelled()
    {
        return redirect()->route('home')->withErrors(['cancel' => 'You cancelled the payment']);

        // return redirect()->route('home')
        //     ->withErrors('You cancelled the payment');
    }
}
