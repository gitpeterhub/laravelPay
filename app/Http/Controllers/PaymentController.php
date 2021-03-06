<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Validator;
use URL;
use Session;
use Redirect;
use Input;
use DB;

//-------------------------
//All Paypal Details class
//-------------------------
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ExecutePayment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;

class PaymentController extends Controller
{
    private $_api_context;
    public function __construct()
    {
        //------------------------   
        //setup PayPal api context
        //------------------------
        $paypal_conf = \Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential($paypal_conf['client_id'], $paypal_conf['secret']));
        $this->_api_context->setConfig($paypal_conf['settings']);
    }

    public function addPayment()
    {
        return view('addPayment');
    }

    public function postPaymentWithpaypal(Request $request)
    {
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

    	$item_1 = new Item();

        $item_1->setName('Item 1') //item name
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice($request->get('amount')); //unit price

        $item_list = new ItemList();
        $item_list->setItems(array($item_1));

        $amount = new Amount();
        $amount->setCurrency('USD')
            ->setTotal($request->get('amount'));

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription('Your transaction description');

        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(URL::route('status')) //Specify return URL
            ->setCancelUrl(URL::route('status'));

        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));

        try 
        {
            $payment->create($this->_api_context);
        } 
        catch (\PayPal\Exception\PPConnectionException $ex) 
        {
            if (\Config::get('app.debug')) 
            {
                \Session::put('error','Connection timeout');
                return Redirect::route('paywithpaypal');
            } 
            else 
            {
                \Session::put('error','Some error occur, sorry for inconvenient');
                return Redirect::route('paywithpaypal');
            }
        }

        foreach($payment->getLinks() as $link) 
        {
            if($link->getRel() == 'approval_url') 
            {
                $redirect_url = $link->getHref();
                break;
            }
        }

        //--------------------------
        // add payment ID to session
        //--------------------------
        Session::put('paypal_payment_id', $payment->getId());


        //--------------------------
        // add payment amount to session
        //--------------------------
        //Session::put('paypal_paid_amount', $payment->getAmount());

        if(isset($redirect_url)) 
        {
            //-------------------
            // redirect to paypal
            //-------------------
            return Redirect::away($redirect_url);
        }

        \Session::put('error','Unknown error occurred');
    	return Redirect::route('paywithpaypal');
    }

    public function getPaymentStatus(Request $request)
    {
        //----------------------------------------
        // Get the payment ID before session clear
        //----------------------------------------
        $payment_id = Session::get('paypal_payment_id');

        //-----------------------------
        // clear the session payment ID
        //-----------------------------
        Session::forget('paypal_payment_id');
        if (empty($request->get('PayerID')) || empty($request->get('token'))) 
        {
            \Session::put('error','Payment failed');
            return Redirect::route('paywithpaypal');
        }
        $payment = Payment::get($payment_id, $this->_api_context);

        $execution = new PaymentExecution();
        $execution->setPayerId($request->get('PayerID'));

        //---Execute the payment ---//
        $result = $payment->execute($execution, $this->_api_context);

        if ($result->getState() == 'approved') { 
            
            //----------------
            // Here Write your database logic like that insert record or value in database if you want 
            //----------------

            \Session::put('success','Payment success');
            return Redirect::route('paywithpaypal');
        }
        \Session::put('error','Payment failed');

		return Redirect::route('paywithpaypal');
    }
}
