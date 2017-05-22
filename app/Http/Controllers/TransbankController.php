<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Freshwork\Transbank\CertificationBagFactory;
use Freshwork\Transbank\TransbankServiceFactory;
use Freshwork\Transbank\RedirectorHelper;
use Freshwork\Transbank\Log\LoggerFactory;
use Freshwork\Transbank\Log\TransbankCertificationLogger;

LoggerFactory::setLogger(new TransbankCertificationLogger(storage_path().'/transbank/logs/'));


class TransbankController extends Controller
{
    private function errorResponse($errorMsg){
        $response=array("status"=>"Fail","msg" => $errorMsg,"data" => new \stdClass());
        return $this->JSONResponse($response,400);
    }
    private function successResponse($response,$msg){
        $successResponse=array("status"=>"Success","msg"=>$msg,"data" => $response);
        return $this->JSONResponse($successResponse,200);
    }
    private function JSONResponse($response,$statusCode){
        return response(\GuzzleHttp\json_encode($response))
            ->withHeaders([
                'Content-Type' => 'application/json',
                'status-code' => $statusCode,
            ]);
    }
    public function initInscriptionOneClick(Request $request){

        $params=$request->all();

        if (!isset($params['email'], $params['username'])){
            return $this->errorResponse("Invalid parameters");
        }

        $username = $params['username'];
        $email = $params['email'];

        $certificationBag = CertificationBagFactory::integrationOneClick();
        $oneClick = TransbankServiceFactory::oneclick($certificationBag);
        $response = $oneClick->initInscription($username, $email, url('/api/transbank/finish'));

        echo RedirectorHelper::redirectHTML($response->urlWebpay, $response->token);
    }
    public function finishInscriptionOneClick(Request $request){

        $params=$request->all();

        if (!isset($params['TBK_TOKEN'])){
            return $this->errorResponse("Invalid parameters");
        }

        $certificationBag = CertificationBagFactory::integrationOneClick();
        $oneClick = TransbankServiceFactory::oneclick($certificationBag);

        $token = $params['TBK_TOKEN'];
        $response = $oneClick->finishInscription($token);

        return $this->successResponse($response,"Inscripcion exitosa");
    }

    public function reverseOneClick(Request $request){
        $params=$request->all();

        if (!isset($params['buyorder'])){
            return $this->errorResponse("Invalid parameters");
        }

        $certificationBag = CertificationBagFactory::integrationOneClick();
        $oneClick = TransbankServiceFactory::oneclick($certificationBag);

        $buyorder =$params['buyorder'];

        $response = $oneClick->codeReverseOneClick($buyorder);
        return $this->successResponse($response,"Reversa exitosa");
    }

    public function removeUserOneClick(Request $request){
        $params=$request->all();

        if (!isset($params['tbkToken'],$params['username'])){
            return $this->errorResponse("Invalid parameters");
        }

        $certificationBag = CertificationBagFactory::integrationOneClick();
        $oneClick = TransbankServiceFactory::oneclick($certificationBag);

        $tbkToken =$params['tbkToken'];
        $username =$params['username'];

        $response = $oneClick->removeUser($tbkToken, $username);
        return $this->successResponse($response,"Usurario removido exitosamente");
    }

    public function oneClickPayment(Request $request){

        $params=$request->all();

        if (!isset($params['amount'],$params['tbkToken'],$params['username'],$params['buyorder'])){
            return $this->errorResponse("Invalid parameters");
        }

        $certificationBag = CertificationBagFactory::integrationOneClick();
        $oneClick = TransbankServiceFactory::oneclick($certificationBag);

        $amount =intval($params['amount']);
        $tbkToken =$params['tbkToken'];
        $username =$params['username'];
        $buyorder =$params['buyorder'];

        $response = $oneClick->authorize($amount, $buyorder, $username, $tbkToken);

        return $this->successResponse($response,"Pago exitoso");

    }

    public function webpayInit(Request $request){
        $params=$request->all();

        if (!isset($params['amount'],$params['buyorder'])){
            return $this->errorResponse("Invalid parameters");
        }
        $amount=$params['amount'];
        $buyorder=$params['buyorder'];
        $bag = CertificationBagFactory::integrationWebpayNormal();

        $plus = TransbankServiceFactory::normal($bag);

        $plus->addTransactionDetail($amount, $buyorder);

        $response = $plus->initTransaction(url('/api/transbank/payment'), url('/api/transbank/success'));

        echo RedirectorHelper::redirectHTML($response->url, $response->token);
    }
    public function webpayPayment(){

        $bag = CertificationBagFactory::integrationWebpayNormal();
        $plus = TransbankServiceFactory::normal($bag);

        $response = $plus->getTransactionResult();
        $plus->acknowledgeTransaction();

        return RedirectorHelper::redirectBackNormal($response->urlRedirection);
    }
    public  function success(){
        $response = array("status"=>"success");
        echo \GuzzleHttp\json_encode($response);
    }
}
