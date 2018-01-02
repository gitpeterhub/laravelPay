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
Route::post('stripe', 'PaymentController@postPaymentWithStrip')->name('stripe');

//---------------------------------
// route for check status responce
