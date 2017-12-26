<?php
return array(
	//----------------------------
	// set your paypal credential 
	//----------------------------

	'client_id' =>env('PAYPAL_CLIENT_ID'),
	'secret' => env('PAYPAL_SECRET'),

	//----------------
	// SDK Setup
	//---------------

	'settings' => array(
	
	// Set Paypal Mode 2 option 'Live' or 'sandbox'

	'mode' => 'sandbox',

	// Set maximum request time

	'http.ConnectionTimeOut' => 1000,
	
	// Set log Enabled or not

	'log.LogEnabled' => true,

	// Specify the file that want to write on

	'log.FileName' => storage_path() . '/logs/paypal.log',

	//-----------------------------------------------------------------
	//Available option 'FINE', 'INFO', 'WARN' or 'ERROR'
	//Logging is most verbose in the 'FINE' level and decreases as you
	//-----------------------------------------------------------------

	'log.LogLevel' => 'FINE'
	),
);
?>