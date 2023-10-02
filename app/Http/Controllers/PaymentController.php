<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Nafezly\Payments\Classes\PaytabsPayment;

class PaymentController extends Controller
{

    public function payWithPaytabs(Request $request)
    {
        $amount = $request->amount;
        $id = $request->user_id;
        $first_name = $request->user_first_name;
        $email = $request->user_email;
        $phone = $request->user_phone;

        $payment = new PaytabsPayment();
        $response = $payment
            ->setAmount($amount)
            ->setUserId($id)
            ->setUserFirstName($first_name)
            ->setUserEmail($email)
            ->setUserPhone($phone)
            ->pay();


        return redirect($response['redirect_url']);
    }

    public function verifyWithPaytabs(Request $request)
    {
        $request['tranRef'] = Cache::get($request->payment_id);
        $payment = new PaytabsPayment();
        $response = $payment->verify($request);

        dd($response);
    }
}
