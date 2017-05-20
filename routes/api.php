<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['prefix'=>'transbank'],function (){
    Route::get('inscription',[
        'uses'  =>'TransbankController@initInscriptionOneClick',
        'as'    =>'inscription'
    ]);
    Route::post('finish',[
        'uses'  =>'TransbankController@finishInscriptionOneClick',
        'as'    =>'finish'
    ]);
    Route::get('oneClickPayment',[
        'uses'  =>'TransbankController@oneClickPayment',
        'as'    =>'finish'
    ]);

    Route::get('webpay',[
        'uses'  =>'TransbankController@webpayInit',
        'as'    =>'webpayplus'
    ]);
    Route::post('payment',[
        'uses'  =>'TransbankController@webpayPayment',
        'as'    =>'webpayplusResponse'
    ]);
    Route::post('success',[
        'uses'  =>'TransbankController@success',
        'as'    =>'success'
    ]);


});