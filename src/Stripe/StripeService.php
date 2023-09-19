<?php 

namespace App\Stripe;

use App\Entity\Purchase;

class StripeService
{

    public function __construct(protected string $secretKey, protected string $publicKey)
    {
        //dd($secretKey, $publicKey);
    }

    public function getPublicKey(){
        return $this->publicKey;
    }

    public function getPaymentIntent(Purchase $purchase){
        \Stripe\Stripe::setApiKey('sk_test_51NrgibEKNo3hE9TKIPJTlUURTySw6EdfzRUBcipfzYIZ0BPEOqmjU6gpG4SHBVGJT4S3vnWZDBK6zeU45Mx5lDUP00jgFj3YA4');
        
        return \Stripe\PaymentIntent::create([
            'amount' => $purchase->getTotal(),
            'currency' => 'eur',
            // In the latest version of the API, specifying the `automatic_payment_methods` parameter is optional because Stripe enables its functionality by default.
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);
    }
}