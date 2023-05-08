<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Services\PayPalServices;
use App\Resolvers\PaymentPlatformResolver;

class PaymentController extends Controller
{
    Protected $paymentPlatformResolver;
    public function __construct(PaymentPlatformResolver $paymentPlatformResolver)
    {
        $this->middleware('auth');
        
        $this->paymentPlatformResolver  =   $paymentPlatformResolver;
    }
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
        $paymentPlatform    = $this->paymentPlatformResolver
                            ->resolveServices($request->payment_platform);

        session()->put('paymentPlatformId',$request->payment_platform);

        // $paymentPlatform = resolve(PayPalServices::class); // esta linera se hizo solo si se usaba paypal pero se hizo mas dinamico

        return $paymentPlatform->handlePayment($request);

        // return $request->all();
    }

    public function approval()
    {
        if(session()->has('paymentPlatformId')){
            $paymentPlatform    = $this->paymentPlatformResolver
                            ->resolveServices(\session()->get('paymentPlatformId'));
            return $paymentPlatform->handleApproval();
        }

        return redirect()->route('home')->withErrors(['cancel' => 'Su metodo de pago no ha sido encontrado en la plataforma, intente otra vez']);
        // $paymentPlatform = resolve(PayPalServices::class);
        // return $paymentPlatform->handleApproval();

    }

    public function cancelled()
    {
        return redirect()->route('home')->withErrors(['cancel' => 'Su pago ha sido cancelado']);

    }
}
