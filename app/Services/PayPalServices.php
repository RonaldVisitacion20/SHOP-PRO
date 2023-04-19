<?php
namespace App\Services;

use App\Traits\ConsumeExternalServices;
use Illuminate\Http\Request;

class PayPalServices
{
    use ConsumeExternalServices;
    protected $baseUri;
    protected $clientId;

    protected $clientSecret;
    public function __construct()
    {
        $this->baseUri = \config('services.paypal.base_uri');
        $this->clientId = \config('services.paypal.client_id');
        $this->clientSecret = \config('services.paypal.client_secret');

    }
    public function resolveAuthorization(&$queryParmas, &$formParams, &$headers)
    {
        $headers['Authorization'] = $this->resolveAccessToken();
    }

    public function decodeResponse($response)
    {
        return json_decode($response);
    }

    public function resolveAccessToken()
    {
        $credentials = base64_encode("{$this->clientId}:$this->clientSecret");
        return "Basic {$credentials}";
    }

    public function handlePayment(Request $request)
    {
        $order = $this->createOrder($request->value, $request->currency);
        $orderLinks = collect($order->links);
        $approve = $orderLinks->where('rel', 'payer-action')->first();
        session()->put('approvalId', $order->id);
        // dd($orderLinks);
        return redirect($approve->href);
    }
    public function handleApproval()
    {
        if (session()->has('approvalId')) {
            $approvalId = session()->get('approvalId');
            $payment = $this->capturePayment($approvalId);
            // dd($payment);
            $name = $payment->payer->name->given_name;
            $payment = $payment->purchase_units[0]->payments->captures[0]->amount;
            $amount = $payment->value;
            $currency = $payment->currency_code;
            return redirect()
                ->route('home')
                ->withSuccess(['payment' => "Thanks,{$name},We recivided your {$amount}{$currency} payment."]);

        }
        return \redirect()->route('home')->whitErrors('We cannot capture you payment');

    }
    public function createOrder($value, $currency)
    {
        // dd($currency);
        $paypalRequestId = uniqid('', true);
        $PayPal_Request_Id = rand(1000000000, 9999999999);
        return $this->makeRequest(
            'POST', //1
            'https://api-m.sandbox.paypal.com/v2/checkout/orders', // 2
            [], // 3
            [ // 4

                'intent' => 'CAPTURE',
                'purchase_units' => [
                    0 => [
                        'amount' => [
                            'currency_code' => strtoupper($currency),
                            'value' => round($value * $factor = $this->resolveFactor($currency)) / $factor,
                        ],
                    ],
                ],
                'payment_source' => [
                    'paypal' => [
                        'experience_context' => [
                            "payment_method_preference" => "IMMEDIATE_PAYMENT_REQUIRED",
                            "payment_method_selected" => "PAYPAL",
                            "brand_name" => config('app.name'),
                            "locale" => "en-US",
                            "landing_page" => "LOGIN",
                            "shipping_preference" => "NO_SHIPPING",
                            "user_action" => "PAY_NOW",
                            "return_url" => route('approval'),
                            "cancel_url" => route('cancelled'),
                        ],
                    ],
                ],

            ],
            // ["PayPal-Request-Id" => "7b92603e-77ed-4896-8e78-5dea2050476d"],
            ["PayPal-Request-Id" => $PayPal_Request_Id],

            $isJsonRequest = true,

        );
    }

    public function capturePayment($approvaId)
    {
        return $this->makeRequest(
            'POST',
            "https://api-m.sandbox.paypal.com/v2/checkout/orders/{$approvaId}/capture",
            [],
            [],
            [
                'Content-Type' => 'application/json',
            ],

        );
    }
    public function resolveFactor($currency)
    {
        $zeroDecimalCurrencies = ['JPY'];
        if (in_array(strtoupper($currency), $zeroDecimalCurrencies)) {
            return 1;
        }
        return 100;
    }

}

// curl -v -X POST https://api-m.sandbox.paypal.com/v2/checkout/orders/5O190127TN364715T/capture \
// -H "Content-Type: application/json" \
// -H "Authorization: Bearer <Access-Token>" \
// -H "PayPal-Request-Id: 7b92603e-77ed-4896-8e78-5dea2050476a"

// curl -v -X POST https://api-m.sandbox.paypal.com/v2/checkout/orders \
// -H "Content-Type: application/json" \
// -H "Authorization: Bearer <Access-Token>" \
// -H "PayPal-Request-Id: 7b92603e-77ed-4896-8e78-5dea2050476a" \
// -d '{
//   "intent": "CAPTURE",
//   "purchase_units": [
//     {
//       "reference_id": "d9f80740-38f0-11e8-b467-0ed5f89f718b",
//       "amount": {
//         "currency_code": "USD",
//         "value": "100.00"
//       }
//     }
//   ],
//   "payment_source": {
//     "paypal": {
//       "experience_context": {
//         "payment_method_preference": "IMMEDIATE_PAYMENT_REQUIRED",
//         "payment_method_selected": "PAYPAL",
//         "brand_name": "EXAMPLE INC",
//         "locale": "en-US",
//         "landing_page": "LOGIN",
//         "shipping_preference": "SET_PROVIDED_ADDRESS",
//         "user_action": "PAY_NOW",
//         "return_url": "https://example.com/returnUrl",
//         "cancel_url": "https://example.com/cancelUrl"
//       }
//     }
//   }
// }'
