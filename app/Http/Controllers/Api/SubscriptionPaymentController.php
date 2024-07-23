<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Carbon\Carbon;
use Razorpay\Api\Errors;
use Illuminate\Support\Facades\Auth;


class SubscriptionPaymentController extends Controller
{
    public function createSubcription()
    {
        // Initialize Razorpay API
        $api = new Api(env('RZP_KEY'), env('RZP_SECRET'));
       
        //dd($api);
        // Create a subscription plan
        $createRzpPlan = $api->plan->create([
            'period' => "monthly",
            'interval' => "30",
            'item' => [
                'name' => "Speedysite",
                'description' => '',
                'amount' => "200",
                'currency' => 'INR'
            ],
            'notes' => [
                'key1' => 'value3',
                'key2' => 'value2'
            ]
        ]);
       // dd($createRzpPlan);

        // Get plan ID
        $planID = $createRzpPlan->id;

        // Subscription count in months
        $subs_time = "1";

        // Subscription date expired 
        $expired_date = Carbon::now();

        // Date to unix date 
        $expired_unix = strtotime("+" . $subs_time, strtotime($expired_date));

        // Create the subscription
        $createRzpsubscription = $api->subscription->create([
            'plan_id' => $planID,
            'customer_notify' => 1,
            'quantity' => 1,
            'total_count' => $subs_time,
            'notes' => [
                'key1' => 'value3',
                'key2' => 'value2'
            ],
            'notify_info' => [
                'notify_phone' => '+918867898919',
                'notify_email' => 'developersolutions2023@gmail.com'
            ]
        ]);

        // Prepare options for Razorpay checkout
        $options = [
            "key" => env('RZP_KEY'),
            "name" => "Developer Solutions", // Your business name
            "description" => "Test Transaction",
            "image" => "logo.png",
            "subscription_id" => $createRzpsubscription->id, // Subscription ID
            "prefill" => [
                "name" => "Test", // Customer's name
                "email" => "test@example.com",
                "contact" => "9000090000" // Customer's phone number
            ],
            "notes" => [
                "address" => "Razorpay Corporate Office"
            ],
            "theme" => [
                "color" => "#3399cc"
            ]
        ];

        // Return JSON response with checkout options
        return response()->json(['checkoutData' => $options, 'status' => true]);
    }

    public function fetchsubscription()
{
    // Initialize Razorpay API
    $api = new Api(env('RZP_KEY'), env('RZP_SECRET'));
    
    // Fetch subscription details
    $subscriptionId = "sub_O8llmaPwKMxona";
    $subscriptionDetails = $api->subscription->fetch($subscriptionId);

    // Create a new order for the subscription
    $order = $api->order->create([
        
        'payment_capture' => 1 // Auto capture payment
    ]);

    // Prepare options for Razorpay checkout
    $options = [
        "key" => env('RZP_KEY'),
        "name" => "Developer Solutions", // Your business name
        "description" => "Subscription Payment",
        "image" => "logo.png",
        "order_id" => $order->id, // Order ID
        "prefill" => [
            "name" => "Test", // Customer's name
            "email" => "test@example.com",
            "contact" => "9000090000" // Customer's phone number
        ],
        "notes" => [
            "address" => "Razorpay Corporate Office"
        ],
        "theme" => [
            "color" => "#3399cc"
        ]
    ];

    // Return JSON response with checkout options
    return response()->json(['checkoutData' => $options]);
}

public function payment()
{
    $razorpayPaymentId = "plan_O8lfhiPWTWRDKJ";
    $subscriptionId = "sub_O8llmaPwKMxona";
    $secret = 'RAzI1Ia1iMTTPj20SllxF98z'; // Replace this with your actual secret key
    $amount = 100; // Replace this with the actual amount to be charged

    // Generate signature
    $generatedSignature = hash_hmac('sha256', $razorpayPaymentId . "|" . $subscriptionId, $secret);

    // Get signature from request
   // $razorpaySignature = $request->input('razorpay_signature');

    // Compare signatures
    if ($generatedSignature) {
        // Signature verification successful, process payment
        $paymentStatus = $this->processPayment($subscriptionId, $amount);

        if ($paymentStatus === 'success') {
            // Payment is successful
            return "Payment is successful";
        } else {
            // Payment processing failed
            return "Payment processing failed";
        }
    } else {
        // Payment verification failed
        return "Payment verification failed";
    }
}




public function processPayment(Request $request)
{
    $subscriptionId = "sub_O8llmaPwKMxona";
    $amount = 600; 
    $keyId = 'rzp_test_aQGyKg1uq47vTL'; 
    $keySecret = 'YxO6nzm4In7udAoDltqB7LIq'; 

    try {
        $api = new Api($keyId, $keySecret);
        
        // Prepare order data
        $orderData = [
            'amount'         => $amount * 100, // Amount in smallest currency unit (e.g., paisa for INR)
            'currency'       => 'INR',
            'receipt'        => 'receipt_order_' . uniqid(),
            'payment_capture' => 1 // Automatically capture payment when it succeeds
        ];

        // Create an order
        $order = $api->order->create($orderData);

        // Check if order creation was successful
        if ($order->id) {
            // If order creation was successful, redirect the user to the payment page
            return redirect()->to($order->url);
        } else {
            return 'failure: Unable to create Razorpay order';
        }
    } catch (\Exception $e) {
        return 'failure: ' . $e->getMessage();
    }
}

public function handlePaymentNotification(Request $request)
{
    // Verify the request to ensure it comes from Razorpay
    $razorpaySignature = $request->header('X-Razorpay-Signature');

    try {
        // Your webhook secret
        $webhookSecret = 'your_webhook_secret';

        $api = new Api($keyId, $keySecret);

        // Verify the signature
        $api->utility->verifyWebhookSignature($request->getContent(), $razorpaySignature, $webhookSecret);

        // Payment successful, update your database or perform any other necessary actions
        $paymentId = $request->input('payload')['payment']['entity']['id'];
        // Update your database or perform other actions based on the payment status
        // For example, you could update the subscription status for the user

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        // Handle verification failure or any other errors
        return response()->json(['error' => $e->getMessage()], 400);
    }
}

public function subscriptionPayment()
{
    if (Auth::check())
    {
        dd('qsdew');
    }
    
    $key_id = "rzp_test_3kOO5za17PvQpv";
    $secret ="KPSpuvIAjaDal7tEkodFBlJw";
    $api = new Api($key_id, $secret);
    $paymentId = "pay_OAmq4jxyrjVcjO";
    $paymentDetails = $api->payment->fetch($paymentId)->expandedDetails(["expand[]"=> "card"]);
  
   
    $data = array(
        "payment_id" =>  $paymentDetails['id'],
        'amount'     => $paymentDetails['amount'],
        'status'     => $paymentDetails['status'],
        'order_id'   => $paymentDetails['order_id'],
        'method'     => $paymentDetails['method'],
        'email'      => $paymentDetails['email'],
        'contact'    => $paymentDetails['contact'],
        'card_id'    => $paymentDetails['card']['id'],
        'last4didits'=> $paymentDetails['card']['last4'],
        'type'       => $paymentDetails['card']['type'],

    );
    echo "<pre>";
    print_r($data);
 
}





}


