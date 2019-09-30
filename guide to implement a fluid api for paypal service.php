<?php

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payer;
use PayPal\Api\ItemList;
use PayPal\Api\InputFields;
use PayPal\Api\WebProfile;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;


class PayPalGateway 
{

    private $apiContext;
    private $payer;
    private $itemList;
    private $inputFields;
    private $webProfile;
    private $profile;
    private $amount;
    private $transaction;
    private $redirectURLs;

    public $redirectSuccessAction;
    public $redirectFailAction;

    //estos podrian ir en otra clase paypaylServiceChecker
    public $total_transaction;
    public $currency_transaction;
    public $payer_email;
    public $payer_id;
    public $payer_country_code;
    public $payment_id;

    public function __construct()
    {
        $this->apiContext = new ApiContext(new OAuthTokenCredential(
                config('paypal.client_id'),
                config('paypal.secret'))
        );

        $this->apiContext->setConfig(config('paypal.settings'));
        $this->setPayer();//el constructor lo ejecuta con su valor por defecto si se desea cambiar la pasarela de pago a otro servicio de paypal como xoom se debe usar el setter setPayer('xoom')
    }

    public function setPayer($platform = 'paypal')
    {
        // $payer = new Payer();
        // $payer->setPaymentMethod('paypal');
        //propuesto
        $this->payer = new Payer();
        $this->payer->setPaymentMethod($platform);
        return $this;
    }

    public function addItems(iterable $items, $total, $currency = null)//el iterable cubre un array de arrays una collection de arrays o una colleciton de modelos o un array de modelos
    {
        // $itemList = new ItemList();
        // $itemList->setItems($items);
        //propuesta
        $this->itemList = new ItemList();
        $this->itemList->setItems($items);

        $this->setIrrelevantAspectsForPayment();
        $this->addAmount($total, $currency);
        
        return $this;
    }

    public function setIrrelevantAspectsForPayment()
    {
        $this->inputFields = new InputFields();
        $this->inputFields->setAllowNote(true)->setNoShipping(1)->setAddressOverride(0);
        $this->webProfile = new WebProfile();
        $this->webProfile->setName(uniqid())->setInputFields($this->inputFields)->setTemporary(true);
        $this->profile = $this->webProfile->create($this->apiContext);
    }

    public function addAmount($total, $currency = 'USD')
    {
        $this->amount = new Amount();
        $this->amount->setCurrency($currency)->setTotal($total);
        $this->setTransaction();
        $this->setUrls();
        // $this->setPayment(); //problamente me equivoque porque aqui no debo invocar el metodo pay() y a set pay le camgien el nombre a pay
    }

    public function setTransaction()
    {
        $this->transaction = new Transaction();
        $this->transaction->setAmount($this->amount);
        $this->transaction->setItemList($this->itemList)->setDescription('Paypal Transaction ....');
    }

    public function setUrls()
    {
        //TODO 
        // ESTA PARTE LA DEJAREMOS AL FINAL PARA QUE AL VER LA IMPLEMENTACION ANALIZEMOS CUAL ES EL MEJOR APROACH PARA ESTA PARTE SI ENVIAR UN HELPER O NEVIAR UN STRING O DEJAR AMBAS OPCIONES
        $this->redirectURLs = new RedirectUrls();
        $this->redirectURLs->setReturnUrl(URL::to('status'))->setCancelUrl(URL::to('status'));///TODO tendria que dejar abierta la opcion de cambiar las urls de redireccion
    }

    public function pay()
    {
        $payment = new Payment();
        $payment->setIntent('Sale')->setPayer($this->payer)->setRedirectUrls($this->redirectURLs)->setTransactions(array($this->transaction));//SI CAMBIA EL METODO SetUrls definitivamente aqui puede haber uncambio probar siempre
        $payment->setExperienceProfileId($this->createProfile->getId());
        $payment->create($this->apiContext);

        foreach ($payment->getLinks() as $link) {
            if ($link->getRel() == 'approval_url') {
                $redirectURL = $link->getHref();
                break;
            }
        }

        # We store the payment ID into the session
        Session::put('paypalPaymentId', $payment->getId());

        $this->redirectToGateway($redirectURL);
    }

    public function redirectToGateway($redirectURL)
    {
        if (isset($redirectURL)) {
            return redirect()->to($redirectURL);//este helper no se modifica ya que es el que arma la url a la que se va dirijir la app
        }

        return $this->redirectFailAction;

    }


    //este metodo podria ir en otra clase paypalServiceChecker
    public function checkTransactionStatus()
    {

        $paymentId = Session::get('paypalPaymentId');
        # We now erase the payment ID from the session to avoid fraud
        Session::forget('paypalPaymentId');
        $payment = Payment::get($paymentId, $this->apiContext);
        $execution = new PaymentExecution();
        $execution->setPayerId(request()->get('PayerID'));

        $result = $payment->execute($execution, $this->apiContext);


        $this->total_transaction = $result->transactions[0]->getAmount()->getTotal();
        $this->currency_transaction = $result->transactions[0]->getAmount()->getCurrency();
        $this->payer_email = $result->getPayer()->getPayerInfo()->getEmail();
        $this->payer_id = $result->getPayer()->getPayerInfo()->getPayerId();
        $this->payer_country_code = $result->getPayer()->getPayerInfo()->getCountryCode();
        $this->payment_id = $result->getId();


        return $result->getState() == 'approved';

    }

}



//HOW TO USE THE API

//THIS IS A WISHFUL THINKING GUIDE TO CREATE THE IMPLEMENTATION

//wishful thinking
$items = [];
foreach (Cart::content() as $item) {
    $items[] = (new Item())->setName($item->name)->setCurrency('USD')->setQuantity($item->qty)->setPrice($item->price);
}
$paypalService = new PayPalService;
$paypalService->redirectSuccessAction = redirect()->to('/home')->with('success', 'payment accepted');
$paypalService->redirectFailAction    = redirect()->route('home')->with('error', 'Error in request try again');//back()->wutg('error', 'Error in request try again later ...')
$paypalService->addItems($items, $total)->pay();

//to validate the process
//create a paypalServiceChecker to check the transaction status and ultimatly the result if $transaction->result == 'approved' or $transaction->approved

//get data from the process
$transaction = new PayPalServiceChecker;
$transaction->checkTransactionStatus();
$transaction->total;
$transaction->currency;
$transaction->payerEmail;
$transaction->payerId;
$transaction->payerCountryCode;
$transaction->paymentId;

//if you need more info it gets more info about all the process
$transaction->result; 