<?php
namespace Omnipay\Pergo\Message;

use Omnipay\Pergo\Message\AbstractRequest;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

/**
 * pergo Authorize Request
 */
class OffsiteAuthorizeRequest extends OffsiteAbstractRequest
{

    /**
     * sendData function. In this case, where the browser is to be directly it constructs and returns a response object
     * @param mixed $data
     * @return \Omnipay\Common\Message\ResponseInterface|OffsiteAuthorizeResponse
     */
    public function sendData($data)
    {

   /*     $data =str_replace("\n", '', '{
  "PayerAccountId":2498355927655035,
 "MerchantProfileId":"' . $this->getMerchantProfileId() . '",
 "Amount":100,
 "CurrencyCode":"USD",
 "InvoiceNumber":"Test Invoice",
 "Comment1":"Test Comment 1",
 "Comment2":"Test comment 2",
 "CardHolderNameRequirementType":1,
 "SecurityCodeRequirementType":1,
 "AvsRequirementType":1,
 "AuthOnly":true,
 "ProcessCard":true,
 "StoreCard":true,
 "OnlyStoreCardOnSuccessfulProcess":true,
 "CssUrl":"https://protectpaytest.propay.com/hpp/css/pmi.css",
 "Address1":"123 ABC St",
 "Address2":"Apt A",
 "City":"Faloola",
 "Country":"USA",
 "Description":"My Visa",
 "Name":"John Smith",
 "State":"UT",
 "ZipCode":"12345",
 "BillerIdentityId":null,
 "CreationDate":null,
 "HostedTransactionIdentifier":null,
 "PaymentTypeId":"0",
 "Protected”:"false"}');
   */
        $auth = 'Basic ' . base64_encode($this->getBillerAccountId() . ':' . $this->getAuthenticationToken());
        $httpRequest = $this->httpClient->put($this->getEndpoint(), [
            'json' => $data,
            'debug' => TRUE,
        ], null, array('json' => $data));

        $httpRequest->setBody($data);
        try {
            $httpResponse = $httpRequest->send();
        }
          catch (\Exception $e) {
            $c = 4;
            }



        $b = $httpResponse->getBody(TRUE);
        return $this->response = new OffsiteAuthorizeResponse($this, $data, $this->getEndpoint());
    }

    /**
     * Get an array of the required fields for the core gateway
     * @return array
     */
    public function getRequiredCoreFields()
    {
        return array
        (
            'amount',
            'currency',
        );
    }

    /**
     * get an array of the required 'card' fields (personal information fields)
     * @return array
     */
    public function getRequiredCardFields()
    {
        return array
        (
            'email',
        );
    }

    /**
     * Map Omnipay normalised fields to gateway defined fields.
     *
     * @return array
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getTransactionData()
    {
        return array
        (
            'InvoiceNumber' => $this->getTransactionId(),
            'Amount' => $this->getAmountInteger(),
            'CurrencyCode' => $this->getCurrencyNumeric(),
            // Loose AVS is 2 - review this.
            'AvsRequirementType' => 2,
            // 1 is required. ie. card name must be filled in.
            'CardHolderNameRequirementType' => 1,
            'OnlyStoreCardOnSuccessfulProcess' => True,
            'PaymentTypeId' => 0,
            'ReturnURL' => $this->getReturnUrl(),
            'SecurityCodeRequirementType' => 1,
            'PayerAccountId' => rand(1,1000),
        );
    }

    /**
     * @return array
     * Get data that is common to all requests - generally aut
     */
    public function getBaseData()
    {
        return array(
            'AuthOnly ' => ($this->getTransactionType() === 'Purchase' ? False: True),
            'AuthenticationToken' => $this->getAuthenticationToken(),
            'BillerAccountId' => $this->getBillerAccountId(),
            'MerchantProfileId ' => $this->getMerchantProfileId(),
        );
    }

    /**
     * this is the url provided by your payment processor. Github is standing in for the real url here
    * @return string
    */
    public function getEndpoint()
    {
        return 'https://xmltestapi.propay.com/protectpay/HostedTransactions/';
        return 'https://protectpaytest.propay.com/hpp/v2/[hostedtransactionidentifier';
    }

    public function getTransactionType()
    {
        return 'Authorize';
    }
}
