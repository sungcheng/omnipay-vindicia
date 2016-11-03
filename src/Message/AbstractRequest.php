<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestableSoapClient;
use Omnipay\Vindicia\VindiciaItemBag;
use Omnipay\Vindicia\AttributeBag;
use Omnipay\Vindicia\NameValue;
use Omnipay\Vindicia\PriceBag;
use Omnipay\Common\Exception\InvalidRequestException;
use SoapFault;
use stdClass;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Guzzle\Http\ClientInterface;

/**
 * Vindicia Abstract Request
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    /**
     * True if this is the update version of a request rather than the create
     * version. (They're essentially the same but some validation logic is
     * slightly different.
     *
     * @var bool
     */
    protected $isUpdate;

    const API_VERSION = '18.0';
    const LIVE_ENDPOINT = 'https://soap.vindicia.com';
    const TEST_ENDPOINT = 'https://soap.prodtest.sj.vindicia.com';

    /**
     * Object names used in the Vindicia API
     */
    protected static $TRANSACTION_OBJECT = 'Transaction';
    protected static $SUBSCRIPTION_OBJECT = 'AutoBill';
    protected static $CUSTOMER_OBJECT = 'Account';
    protected static $PAYMENT_METHOD_OBJECT = 'PaymentMethod';
    protected static $REFUND_OBJECT = 'Refund';
    protected static $CHARGEBACK_OBJECT = 'Chargeback';
    protected static $PLAN_OBJECT = 'BillingPlan';
    protected static $PRODUCT_OBJECT = 'Product';
    protected static $WEB_SESSION_OBJECT = 'WebSession';

    /**
     * Default amount of time to wait for connection or response, in seconds
     *
     * @var int
     */
    const DEFAULT_TIMEOUT = 120;

    /**
     * Default tax classification for products.
     *
     * @var string
     */
    const DEFAULT_TAX_CLASSIFICATION = 'TaxExempt';

    /**
     * Payment method types
     *
     * @var string
     */
    const PAYMENT_METHOD_PAYPAL = 'PayPal';
    const PAYMENT_METHOD_CREDIT_CARD = 'CreditCard';

    /**
     * If chargeback probabilty from risk scoring is greater than this,
     * the transaction will fail. By default, every transaction will
     * succeed.
     *
     * @var int
     */
    const DEFAULT_MIN_CHARGEBACK_PROBABILITY = 100;

    /**
     * Create a new Request
     *
     * @param ClientInterface $httpClient  A Guzzle client to make API calls with
     * @param HttpRequest     $httpRequest A Symfony HTTP request object
     * @param bool            $isUpdate    True if this is an update request rather than a create (default false)
     */
    public function __construct(ClientInterface $httpClient, HttpRequest $httpRequest, $isUpdate = false)
    {
        parent::__construct($httpClient, $httpRequest);

        $this->isUpdate = $isUpdate;
    }

    public function initialize(array $parameters = array())
    {
        if (!array_key_exists('timeout', $parameters)) {
            $parameters['timeout'] = self::DEFAULT_TIMEOUT;
        }
        if (!array_key_exists('taxClassification', $parameters)) {
            $parameters['taxClassification'] = self::DEFAULT_TAX_CLASSIFICATION;
        }

        parent::initialize($parameters);

        return $this;
    }

    public function getUsername()
    {
        return $this->getParameter('username');
    }

    public function setUsername($value)
    {
        return $this->setParameter('username', $value);
    }

    public function getPassword()
    {
        return $this->getParameter('password');
    }

    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    public function getIp()
    {
        return $this->getParameter('ip');
    }

    public function setIp($value)
    {
        return $this->setParameter('ip', $value);
    }

    public function getCustomerId()
    {
        return $this->getParameter('customerId');
    }

    public function setCustomerId($value)
    {
        return $this->setParameter('customerId', $value);
    }

    public function getCustomerReference()
    {
        return $this->getParameter('customerReference');
    }

    public function setCustomerReference($value)
    {
        return $this->setParameter('customerReference', $value);
    }

    /**
     * Gets the customer's name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getParameter('name');
    }

    /**
     * Sets the customer's name
     *
     * @param string $value
     * @return static
     */
    public function setName($value)
    {
        return $this->setParameter('name', $value);
    }

    /**
     * Gets the customer's email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->getParameter('email');
    }

    /**
     * Sets the customer's email
     *
     * @param string $value
     * @return static
     */
    public function setEmail($value)
    {
        return $this->setParameter('email', $value);
    }

    public function getRefundId()
    {
        return $this->getParameter('refundId');
    }

    public function setRefundId($value)
    {
        return $this->setParameter('refundId', $value);
    }

    public function getPaymentMethodId()
    {
        return $this->getParameter('paymentMethodId');
    }

    public function setPaymentMethodId($value)
    {
        return $this->setParameter('paymentMethodId', $value);
    }

    public function getPaymentMethodReference()
    {
        return $this->getParameter('paymentMethodReference');
    }

    public function setPaymentMethodReference($value)
    {
        return $this->setParameter('paymentMethodReference', $value);
    }

    public function getTaxClassification()
    {
        return $this->getParameter('taxClassification');
    }

    public function getSubscriptionId()
    {
        return $this->getParameter('subscriptionId');
    }

    public function setSubscriptionId($value)
    {
        return $this->setParameter('subscriptionId', $value);
    }

    public function getSubscriptionReference()
    {
        return $this->getParameter('subscriptionReference');
    }

    public function setSubscriptionReference($value)
    {
        return $this->setParameter('subscriptionReference', $value);
    }

    public function getPlanId()
    {
        return $this->getParameter('planId');
    }

    public function setPlanId($value)
    {
        return $this->setParameter('planId', $value);
    }

    public function getPlanReference()
    {
        return $this->getParameter('planReference');
    }

    public function setPlanReference($value)
    {
        return $this->setParameter('planReference', $value);
    }

    public function getProductId()
    {
        return $this->getParameter('productId');
    }

    public function setProductId($value)
    {
        return $this->setParameter('productId', $value);
    }

    public function getProductReference()
    {
        return $this->getParameter('productReference');
    }

    public function setProductReference($value)
    {
        return $this->setParameter('productReference', $value);
    }

    public function setTaxClassification($value)
    {
        return $this->setParameter('taxClassification', $value);
    }

    /**
     * Set the items in this order
     *
     * @param VindiciaItemBag|array $items
     * @return static
     */
    public function setItems($items)
    {
        if ($items && !$items instanceof VindiciaItemBag) {
            $items = new VindiciaItemBag($items);
        }

        return $this->setParameter('items', $items);
    }

    /**
     * A list of attributes
     *
     * @return AttributeBag|null
     */
    public function getAttributes()
    {
        return $this->getParameter('attributes');
    }

    /**
     * Set the attributes in this order
     *
     * @param AttributeBag|array $attributes
     * @return static
     */
    public function setAttributes($attributes)
    {
        if ($attributes && !$attributes instanceof AttributeBag) {
            $attributes = new AttributeBag($attributes);
        }

        return $this->setParameter('attributes', $attributes);
    }

    /**
     * Get amount of time to wait for connection or response, in seconds
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->getParameter('timeout');
    }

    /**
     * Set amount of time to wait for connection or response, in seconds
     *
     * @param int
     * @return static
     */
    public function setTimeout($value)
    {
        return $this->setParameter('timeout', $value);
    }

    /**
     * Get the start time.
     *
     * @return string
     */
    public function getStartTime()
    {
        return $this->getParameter('startTime');
    }

    /**
     * Set the start time. Takes a date/timestamp string, such as
     * "2016-06-02T12:30:00-04:00" (June 2, 2016 @ 12:30 PM, GMT - 4 hours)
     * Use varies depending on request. May be used to specify the start time
     * of a subscription or a range of objects to fetch.
     *
     * @param string $value
     * @return static
     */
    public function setStartTime($value)
    {
        return $this->setParameter('startTime', $value);
    }

    /**
     * Get the end time.
     *
     * @return string
     */
    public function getEndTime()
    {
        return $this->getParameter('endTime');
    }

    /**
     * Set the end time. Takes a date/timestamp string, such as
     * "2016-06-02T12:30:00-04:00" (June 2, 2016 @ 12:30 PM, GMT - 4 hours)
     * Used to specify the end time of a range of objects to fetch.
     *
     * @param string $value
     * @return static
     */
    public function setEndTime($value)
    {
        return $this->setParameter('endTime', $value);
    }

    /**
     * Gets the description shown on the customers billing statement from the bank
     *
     * @return string
     */
    public function getStatementDescriptor()
    {
        return $this->getParameter('statementDescriptor');
    }

    /**
     * Sets the description shown on the customers billing statement from the bank
     * This field’s value and its format are constrained by your payment processor;
     * consult with Vindicia Client Services before setting the value.
     *
     * @param string $value
     * @return static
     */
    public function setStatementDescriptor($value)
    {
        return $this->setParameter('statementDescriptor', $value);
    }

    /**
     * Get the redirect url that the customer will be sent to after
     * HOA completes.
     *
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->getParameter('returnUrl');
    }

    /**
     * Set the redirect url that the customer will be sent to after
     * HOA or a PayPal purchase completes.
     *
     * @param string $value
     * @return static
     */
    public function setReturnUrl($value)
    {
        return $this->setParameter('returnUrl', $value);
    }

    /**
     * Get minimum chargeback probability.
     * If chargeback probabilty from risk scoring is greater than the
     * this value, the transaction will fail. If the value is 100,
     * all transactions will succeed.
     *
     * @return int
     */
    public function getMinChargebackProbability()
    {
        return $this->getParameter('minChargebackProbability');
    }

    /**
     * Set minimum chargeback probability.
     * If chargeback probabilty from risk scoring is greater than the
     * set value, the transaction will fail. If the value is 100,
     * all transactions will succeed.
     *
     * @param int
     * @return static
     */
    public function setMinChargebackProbability($value)
    {
        return $this->setParameter('minChargebackProbability', $value);
    }

    /**
     * Get the redirect url that will be used in the case of a cancel from PayPal's site.
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->getParameter('cancelUrl');
    }

    /**
     * Set the redirect url that will be used in the case of a cancel from PayPal's site.
     *
     * @param string $value
     * @return static
     */
    public function setCancelUrl($value)
    {
        return $this->setParameter('cancelUrl', $value);
    }
    /**
     * Gets whether PayPal redirected the customer to your success page. If false, they
     * were redirected to the error page.
     *
     * @return bool
     */
    public function getSuccess()
    {
        return $this->getParameter('success');
    }

    /**
     * Sets whether PayPal redirected the customer to your success page. Set to false
     * if they were redirected to the error page.
     *
     * @param bool
     * @return static
     */
    public function setSuccess($value)
    {
        return $this->setParameter('success', $value);
    }

    /**
     * Gets the identifier generated by the gateway to represent the underlying
     * PayPal transaction.
     *
     * @return string
     */
    public function getPayPalTransactionReference()
    {
        return $this->getParameter('payPalTransactionReference');
    }

    /**
     * Sets the identifier generated by the gateway to represent the underlying
     * PayPal transaction.
     *
     * @param string
     * @return static
     */
    public function setPayPalTransactionReference($value)
    {
        return $this->setParameter('payPalTransactionReference', $value);
    }

    /**
     * A list of prices (currency and amount)
     *
     * @return PriceBag|null
     */
    public function getPrices()
    {
        return $this->getParameter('prices');
    }

    /**
     * Set the prices (currency and amount)
     * If you only need a price for one currency, you can also use setAmount and setCurrency.
     *
     * @param PriceBag|array $prices
     * @return AbstractRequest
     * @throws InvalidPriceBagException if multiple prices have the same currency
     */
    public function setPrices($prices)
    {
        if ($prices && !$prices instanceof PriceBag) {
            $prices = new PriceBag($prices);
        }

        return $this->setParameter('prices', $prices);
    }

    abstract protected function getObject();

    abstract protected function getFunction();

    final protected function getEndpoint()
    {
        return ($this->getTestMode() === false ? self::LIVE_ENDPOINT : self::TEST_ENDPOINT)
               . '/'
               . self::API_VERSION
               . '/'
               . $this->getObject()
               . '.wsdl';
    }

    /**
     * @param array
     * @return Response
     */
    public function sendData($data)
    {
        $originalWsdlCacheEnabled = ini_get('soap.wsdl_cache_enabled');
        $originalWsdlCacheTtl = ini_get('soap.wsdl_cache_ttl');
        $originalSocketTimeout = ini_get('default_socket_timeout');
        ini_set('soap.wsdl_cache_enabled', 1);
        ini_set('soap.wsdl_cache_ttl', 3600);
        ini_set('default_socket_timeout', $this->getTimeout());

        $data['srd'] = '';

        $auth = new stdClass();
        $auth->version = self::API_VERSION;
        $auth->login = $this->getUsername();
        $auth->password = $this->getPassword();
        $auth->evid = null;
        $auth->userAgent = null;
        $data['auth'] = $auth;

        $action = $data['action'];
        unset($data['action']);

        $params = array();
        $params['parameters'] = $data;

        try {
            $client = new TestableSoapClient(
                $this->getEndpoint(),
                array(
                    'style'              => SOAP_DOCUMENT,
                    'use'                => SOAP_LITERAL,
                    'connection_timeout' => $this->getTimeout(),
                    'trace'              => true,
                    'features'           => SOAP_SINGLE_ELEMENT_ARRAYS,
                    'location'           => ($this->getTestMode() === false ? self::LIVE_ENDPOINT : self::TEST_ENDPOINT)
                                            . '/v'
                                            . self::API_VERSION
                                            . '/soap.pl'
                )
            );
            $response = $client->__soapCall($action, $params);
        } catch (SoapFault $exception) {
            throw $exception;
        }

        // reset to how they were before
        ini_set('soap.wsdl_cache_enabled', $originalWsdlCacheEnabled);
        ini_set('soap.wsdl_cache_ttl', $originalWsdlCacheTtl);
        ini_set('default_socket_timeout', $originalSocketTimeout);

        $this->response = $this->buildResponse($response);
        return $this->response;
    }

    /**
     * Overriding to provide a more precise return type
     * @return Response
     */
    public function send()
    {
        /**
         * @var Response
         */
        return parent::send();
    }

    protected function buildResponse($response)
    {
        return new Response($this, $response);
    }

    protected function isUpdate()
    {
        return $this->isUpdate;
    }

    /**
     * Helper function to make a Vindicia transaction object.
     * Set $validateCard to false to skip card validation.
     *
     * @param string $paymentMethodType default null
     * @return stdClass
     */
    protected function buildTransaction($paymentMethodType = null)
    {
        $account = new stdClass();
        // doesn't work if account isn't created, id must be passed in :-(
        $account->merchantAccountId = $this->getCustomerId();
        $account->VID = $this->getCustomerReference();
        $account->name = $this->getName();
        $account->emailAddress = $this->getEmail();

        $amount = $this->getAmount();
        $transactionItems = array();
        $items = $this->getItems();

        if (!empty($items)) {
            $totalPriceOfItems = '0';
            foreach ($items as $i => $item) {
                $item->validate();

                $transactionItem = new stdClass();
                $transactionItem->name = $item->getName();
                $transactionItem->price = $item->getPrice();
                $transactionItem->quantity = $item->getQuantity();
                $transactionItem->indexNumber = $i + 1; // vindicia index numbers start at 1
                $transactionItem->itemType = 'Purchase';
                $transactionItem->taxClassification = $this->getTaxClassification();
                $transactionItem->sku = $item->getSku();
                $transactionItem->nameValues = array(
                    new NameValue('description', $item->getDescription())
                );

                $transactionItems[] = $transactionItem;

                // strval to avoid floating point error
                $totalPriceOfItems = strval($totalPriceOfItems + $transactionItem->price * $transactionItem->quantity);
            }

            if ($amount && floatval($amount) !== floatval($totalPriceOfItems)) {
                throw new InvalidRequestException('Sum of item prices not equal to set amount.');
            }
        } elseif ($amount) {
            $transactionItem = new stdClass();
            $transactionItem->name = 'Item'; // generic name since the name field is required
            $transactionItem->price = $amount;
            $transactionItem->quantity = 1;
            $transactionItem->indexNumber = 1;
            $transactionItem->itemType = 'Purchase';
            $transactionItem->taxClassification = $this->getTaxClassification();
            $transactionItem->sku = 0;

            $transactionItems[] = $transactionItem;
        }

        $transaction = new stdClass();
        $transaction->account = $account;
        $transaction->currency = $this->getCurrency();
        $transaction->merchantTransactionId = $this->getTransactionId();
        $transaction->sourcePaymentMethod = $this->buildPaymentMethod($paymentMethodType);
        $transaction->transactionItems = $transactionItems;
        $transaction->billingStatementIdentifier = $this->getStatementDescriptor();
        $transaction->sourceIp = $this->getIp();

        $attributes = $this->getAttributes();
        if ($attributes) {
            $transaction->nameValues = $this->buildNameValues($attributes);
        }

        return $transaction;
    }

    /**
     * Helper function to make a Vindicia payment method object.
     *
     * @param string $paymentMethodType default null
     * @return stdClass
     */
    protected function buildPaymentMethod($paymentMethodType = null)
    {
        $paymentMethod = new stdClass();
        $paymentMethod->merchantPaymentMethodId = $this->getPaymentMethodId();
        $paymentMethod->VID = $this->getPaymentMethodReference();
        $paymentMethod->active = true;
        $paymentMethod->currency = $this->getCurrency();

        $card = $this->getCard();

        switch ($paymentMethodType) {
            case self::PAYMENT_METHOD_CREDIT_CARD:
                if ($card) {
                    // if we're adding a new credit card, the whole thing needs to be provided
                    if (!$this->isUpdate()) {
                        $card->validate();
                    }

                    $creditCard = new stdClass();
                    $creditCard->account = $card->getNumber();
                    $creditCard->expirationDate = $card->getExpiryDate('Ym');

                    $paymentMethod->creditCard = $creditCard;
                }
                break;

            case self::PAYMENT_METHOD_PAYPAL:
                $paypal = new stdClass();
                $paypal->cancelUrl = $this->getCancelUrl();
                $paypal->returnUrl = $this->getReturnUrl();
                $paypal->requestReferenceId = true;

                $paymentMethod->paypal = $paypal;
                break;

            case null:
            default:
                // this is for the null case, such as for when we're making a simple payment method
                // to calculate sales tax
                break;
        }

        // never change the type on an update
        if (!$this->isUpdate() && $paymentMethodType) {
            $paymentMethod->type = $paymentMethodType;
        }

        $attributes = $this->getAttributes();
        if ($attributes) {
            $paymentMethod->nameValues = $this->buildNameValues($attributes);
        }

        if ($card !== null) {
            $customerName = $card->getName();

            $address = new stdClass();
            $address->addr1 = $card->getAddress1();
            $address->addr2 = $card->getAddress2();
            $address->city = $card->getCity();
            $address->country = $card->getCountry();
            $address->district = $card->getState();
            $address->name = $customerName;
            $address->postalCode = $card->getPostcode();

            $paymentMethod->accountHolderName = $customerName;
            $paymentMethod->billingAddress = $address;

            if ($card->getCvv()) {
                if (!isset($paymentMethod->nameValues)) {
                    $paymentMethod->nameValues = array();
                }
                $paymentMethod->nameValues[] = new NameValue('CVN', $card->getCvv());
            }
        }

        return $paymentMethod;
    }

    /**
     * Helper function to make an array of name values from a bag of attributes
     *
     * @param AttributeBag $attributes
     * @return array of NameValue
     */
    protected function buildNameValues(AttributeBag $attributes)
    {
        $nameValues = array();
        foreach ($attributes as $attribute) {
            $nameValues[] = new NameValue($attribute->getName(), $attribute->getValue());
        }

        return $nameValues;
    }

    /**
     * Builds the value for Vindicia's prices field
     *
     * @return array of stdClass
     */
    protected function makePricesForVindicia()
    {
        $prices = $this->getPrices();
        $amount = $this->getAmount();
        $currency = $this->getCurrency();
        if (!empty($prices) && (isset($amount) || isset($currency))) {
            throw new InvalidRequestException(
                'The amount and currency parameters cannot be set if the prices parameter is set.'
            );
        }

        $vindiciaPrices = array();
        if (!empty($prices)) {
            foreach ($prices as $price) {
                $vindiciaPrice = new stdClass();
                $vindiciaPrice->amount = $price->getAmount();
                $vindiciaPrice->currency = $price->getCurrency();
                $vindiciaPrices[] = $vindiciaPrice;
            }
        } else {
            if (isset($amount)) {
                $price = new stdClass();
                $price->amount = $amount;
                $price->currency = $currency;
                $vindiciaPrices[] = $price;
            }
        }

        return $vindiciaPrices;
    }

    /**
     * This method is only overriden to provide type hinting for static type checking
     * by PSALM.
     * This is necessary because Omnipay\Common\AbstractRequest::setParameter says it
     * returns AbstractRequest instead of static.
     *
     * @return static
     */
    protected function setParameter($key, $value)
    {
        /**
         * @var static
         */
        return parent::setParameter($key, $value);
    }

    abstract public function getData();

    /**
     * Redefining to tell psalm it's variadic
     *
     * @psalm-variadic
     */
    public function validate()
    {
        return call_user_func_array('parent::validate', func_get_args());
    }
}
