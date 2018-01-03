<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//---------------------------
// route for view/blade file for paypal
//---------------------------
Route::get('paywithpaypal','PaymentController@addPayment')->name('paywithpaypal');

//-------------------------
// route for post request
//-------------------------
Route::post('paypal', 'PaymentController@postPaymentWithpaypal')->name('paypal');

//---------------------------------
// route for check status responce
//---------------------------------
Route::get('paypal','PaymentController@getPaymentStatus')->name('status');

//-----------------------------
// route for view/blade file for strip payment
//-----------------------------------
Route::get('paywithstripe','PaymentController@stripePayment')->name('paywithstripe');

//-------------------------
// route for post request
//-------------------------
//Route::post('stripe', 'PaymentController@postPaymentWithStrip')->name('stripe');

Route::post ( '/stripe', function (Illuminate\Http\Request $request) {
    //here we put secret test key
    \Stripe\Stripe::setApiKey ( 'sk_test_te1TbGkKA02mahPAlxBCbB74' );
    try {
        \Stripe\Charge::create ( array (
                "amount" => 300 * 100, //multiplying by 100 to convert to usd because default is cent 
                "currency" => "usd",
                "customer" => "cus_C4E0hEz9sg5PTE",
                "source" => $request->input ( 'stripeToken' ), // obtained with Stripe.js
                "description" => "Test payment." 
        ) );
        Session::flash ( 'success-message', 'Payment done successfully !' );
        
    } catch ( \Exception $e ) {
        dd($e);
        Session::flash ( 'fail-message', "Error! Please Try again." );
       
    }

     return back ();
} );

//---------------------------------
// route for check status responce
