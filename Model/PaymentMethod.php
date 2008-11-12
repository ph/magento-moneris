<?php
/**
* Moneris Magento v0.5 - show me the money release
*
* 
* Copyright (c) 2008 Pier-Hugues Pellerin
* 
* Permission is hereby granted, free of charge, to any person obtaining
* a copy of this software and associated documentation files (the
* "Software"), to deal in the Software without restriction, including
* without limitation the rights to use, copy, modify, merge, publish,
* distribute, sublicense, and/or sell copies of the Software, and to
* permit persons to whom the Software is furnished to do so, subject to
* the following conditions:
* 
* The above copyright notice and this permission notice shall be
* included in all copies or substantial portions of the Software.
* 
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
* EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
* MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
* NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
* LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
* OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
* WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*
*
*
*
* Simple class to add support of the moneris payment gateway to
* magento the opensource ecommerce.
*
* This module is heavily based on the Authorized.net.
* 
* The void method is not yet implemented.
* I am away there is some work to do to make is more robust and more php5-ish.
*
* Documentation: http://github.com/ph/magento-moneris/
* 
* Pier-Hugues Pellerin ph@heykimo.com
*
* Thanks to Antoine Girard - antoinegirard.com if you need magento integration contact him.
*/
set_include_path(get_include_path().PS.Mage::getBaseDir('lib').DS.'moneris');
require('mpgClasses.php');

class Mage_Moneris_Model_PaymentMethod extends Mage_Payment_Model_Method_Cc
{
	/** 
	* Constants 
	**/
	const TRANSACTION_PREAUTH    = 'preauth';
	const TRANSACTION_COMPLETION = 'completion';
	const ERROR_CODE_LIMIT 	     = 50;
	const ERROR_CODE_UPPER_LIMIT = 1000;
	const CRYPT_TYPE			 = 7; // SSL enabled merchant
	
	/**
	* Human readable errors from Monoris' CSV file
	* as of 2008-08-16
	*
	* @var array
	*/
	protected $_errors = array(
		'50' => 'Decline',
		'51' => 'Expired Card',
		'52' => 'PIN retries exceeded',
		'53' => 'No sharing',
		'54' => 'No security module',
		'55' => 'Invalid transaction',
		'56' => 'No Support',
		'57' => 'Lost or stolen card',
		'58' => 'Invalid status',
		'59' => 'Restricted Card',
		'60' => 'No Chequing account',
		'60' => 'No Savings account',
		'61' => 'No PBF',
		'62' => 'PBF update error',
		'63' => 'Invalid authorization type',
		'64' => 'Bad Track 2',
		'65' => 'Adjustment not allowed',
		'66' => 'Invalid credit card advance increment',
		'67' => 'Invalid transaction date',
		'68' => 'PTLF error',
		'69' => 'Bad message error',
		'70' => 'No IDF',
		'71' => 'Invalid route authorization',
		'72' => 'Card on National NEG file ',
		'73' => 'Invalid route service (destination)',
		'74' => 'Unable to authorize',
		'75' => 'Invalid PAN length',
		'76' => 'Low funds',
		'77' => 'Pre-auth full',
		'78' => 'Duplicate transaction',
		'79' => 'Maximum online refund reached',
		'80' => 'Maximum offline refund reached',
		'81' => 'Maximum credit per refund reached',
		'82' => 'Number of times used exceeded',
		'83' => 'Maximum refund credit reached',
		'84' => 'Duplicate transaction - authorization number has already been corrected by host. ',
		'85' => 'Inquiry not allowed',
		'86' => 'Over floor limit ',
		'87' => 'Maximum number of refund credit by retailer',
		'88' => 'Place call',
		'89' => 'CAF status inactive or closed',
		'90' => 'Referral file full',
		'91' => 'NEG file problem',
		'92' => 'Advance less than minimum',
		'93' => 'Delinquent',
		'94' => 'Over table limit',
		'95' => 'Amount over maximum',
		'96' => 'PIN required',
		'97' => 'Mod 10 check failure',
		'98' => 'Force Post',
		'99' => 'Bad PBF',
		'100' => 'Unable to process transaction',
		'101' => 'Place call',
		'102' => 'Place call',
		'103' => 'NEG file problem',
		'104' => 'CAF problem',
		'105' => 'Card not supported',
		'106' => 'Amount over maximum',
		'107' => 'Over daily limit',
		'108' => 'CAF Problem',
		'109' => 'Advance less than minimum',
		'110' => 'Number of times used exceeded',
		'111' => 'Delinquent',
		'112' => 'Over table limit',
		'113' => 'Timeout',
		'115' => 'PTLF error',
		'121' => 'Administration file problem',
		'122' => 'Unable to validate PIN: security module down',
		'150' => 'Merchant not on file',
		'200' => 'Invalid account',
		'201' => 'Incorrect PIN',
		'202' => 'Advance less than minimum',
		'203' => 'Administrative card needed',
		'204' => 'Amount over maximum ',
		'205' => 'Invalid Advance amount',
		'206' => 'CAF not found',
		'207' => 'Invalid transaction date',
		'208' => 'Invalid expiration date',
		'209' => 'Invalid transaction code',
		'210' => 'PIN key sync error',
		'212' => 'Destination not available',
		'251' => 'Error on cash amount',
		'252' => 'Debit not supported',
		'426' => 'AMEX - Denial 12',
		'427' => 'AMEX - Invalid merchant',
		'429' => 'AMEX - Account error',
		'430' => 'AMEX - Expired card',
		'431' => 'AMEX - Call Amex',
		'434' => 'AMEX - Call 03',
		'435' => 'AMEX - System down',
		'436' => 'AMEX - Call 05',
		'437' => 'AMEX - Declined',
		'438' => 'AMEX - Declined',
		'439' => 'AMEX - Service error',
		'440' => 'AMEX - Call Amex',
		'441' => 'AMEX - Amount error',
		'475' => 'CREDIT CARD - Invalid expiration date',
		'476' => 'CREDIT CARD - Invalid transaction, rejected',
		'477' => 'CREDIT CARD - Refer Call',
		'478' => 'CREDIT CARD - Decline, Pick up card, Call',
		'479' => 'CREDIT CARD - Decline, Pick up card',
		'480' => 'CREDIT CARD - Decline, Pick up card',
		'481' => 'CREDIT CARD - Decline',
		'482' => 'CREDIT CARD - Expired Card',
		'483' => 'CREDIT CARD - Refer',
		'484' => 'CREDIT CARD - Expired card - refer',
		'485' => 'CREDIT CARD - Not authorized',
		'486' => 'CREDIT CARD - CVV Cryptographic error',
		'487' => 'CREDIT CARD - Invalid CVV',
		'489' => 'CREDIT CARD - Invalid CVV',
		'490' => 'CREDIT CARD - Invalid CVV',
		'800' => 'Bad format',
		'801' => 'Bad data',
		'802' => 'Invalid Clerk ID',
		'809' => 'Bad close ',
		'810' => 'System timeout',
		'811' => 'System error',
		'821' => 'Bad response length',
		'877' => 'Invalid PIN block',
		'878' => 'PIN length error',
		'880' => 'Final packet of a multi-packet transaction',
		'881' => 'Intermediate packet of a multi-packet transaction',
		'889' => 'MAC key sync error',
		'898' => 'Bad MAC value',
		'899' => 'Bad sequence number - resend transaction',
		'900' => 'Capture - PIN Tries Exceeded',
		'901' => 'Capture - Expired Card',
		'902' => 'Capture - NEG Capture',
		'903' => 'Capture - CAF Status 3',
		'904' => 'Capture - Advance < Minimum',
		'905' => 'Capture - Num Times Used',
		'906' => 'Capture - Delinquent',
		'907' => 'Capture - Over Limit Table',
		'908' => 'Capture - Amount Over Maximum',
		'909' => 'Capture - Capture',
		'960' => 'Initialization failure - merchant number mismatch',
		'961' => 'Initialization failure - pinpad  mismatch',
		'963' => 'No match on Poll code',
		'964' => 'No match on Concentrator ID',
		'965' => 'Invalid software version',
		'966' => 'Duplicate terminal name'
	);


    /**
    * unique internal payment method identifier
    *
    * @var string [a-z0-9_]
    */
    protected $_code = 'moneris';

	/**
	* Magento defined flags
	*/

    /**
     * Is this payment method a gateway (online auth/charge) ?
     */
    protected $_isGateway               = true;

    /**
     * Can authorize online?
     */
    protected $_canAuthorize            = true;

    /**
     * Can capture funds online?
     */
    protected $_canCapture              = true;

    /**
     * Can capture partial amounts online?
     */
    protected $_canCapturePartial       = false;

    /**
     * Can refund online?
     */
    protected $_canRefund               = true;

    /**
     * Can void transactions online?
     */
    protected $_canVoid                 = false;

    /**
     * Can use this payment method in administration panel?
     */
    protected $_canUseInternal          = true;

    /**
     * Can show this payment method as an option on checkout payment page?
     */
    protected $_canUseCheckout          = true;

    /**
     * Is this payment method suitable for multi-shipping checkout?
     */
    protected $_canUseForMultishipping  = true;

    /**
     * Can save credit card information for future processing?
     */
    protected $_canSaveCc = false;

    /**
     * Authorize a payment for future capture
	 * 
     * @var Variant_Object $payment 
	 * @var Float $amount
     */
	public function authorize(Varien_Object $payment, $amount)
	{	
	
		$error = false;
		
		// check for payment
		if($amount > 0){
		
			$payment->setAmount($amount);
	
			// Map magento keys to moneris way
			$transaction = $this->_build($payment, self::TRANSACTION_PREAUTH);
			$response = $this->_send($transaction);
			
			$payment->setCcApproval($response->getReceiptId())
             ->setLastTransId($response->getReceiptId())
             ->setCcTransId($response->getTxnNumber())
             ->setCcAvsStatus($response->getAuthCode())
             ->setCcCidStatus($response->getResponseCode());

		
			if($response->getResponseCode() > 0 && $response->getResponseCode() <= self::ERROR_CODE_LIMIT) {
				$payment->setStatus(self::STATUS_APPROVED);
			} else if($response->getResponseCode() > self::ERROR_CODE_LIMIT && $response->getResponseCode() < self::ERROR_CODE_UPPER_LIMIT) {
				$error = Mage::helper('paygate')->__($this->_errors[$response->getResponseCode()]);
			} else {
				$error = Mage::helper('paygate')->__('Incomplete transaction.');
			}
		} else{
		    $error = Mage::helper('paygate')->__('Invalid amount for authorization.');
		}
	
		// we've got something bad here.
		if ($error !== false) 
		    Mage::throwException($error);
	
		return $this;
	}
	
	/**
	* Capture the authorized transaction for a specific order
	*
    * @var Variant_Object $payment 
	* @var Float $amount
    */
	public function capture(Varien_Object $payment, $amount) {		
		$error = false;
		
		// check for payment
		if($amount > 0){
			$payment->setAmount($amount);

		
			// Map magento keys to moneris way
			$transaction = $this->_build($payment, self::TRANSACTION_COMPLETION);
			$response = $this->_send($transaction);

			if($response->getResponseCode() > 0 && $response->getResponseCode() <= self::ERROR_CODE_LIMIT) {
				$payment->setStatus(self::STATUS_SUCCESS);
			} else if($response->getResponseCode() > self::ERROR_CODE_LIMIT && $response->getResponseCode() < self::ERROR_CODE_UPPER_LIMIT) {
				$error = Mage::helper('paygate')->__($this->_errors[$response->getResponseCode()]);
			} else {
				$error = Mage::helper('paygate')->__('Incomplete transaction.');
			}
		} else{
		    $error = Mage::helper('paygate')->__('Invalid amount for authorization.');
		}


		// we've got something bad here.
		if ($error !== false) 
		    Mage::throwException($error);

		return $this;
		
	}
	
	/**
	* Void a transaction, 
	* This is not yet implemented in this module so you need to do them in the moneris control
	* panel.
	*/
	public function void(Varien_Object $payment) {
		Mage::throwException("Not yet implemented");
		return $this;			
	}
	
	/******************************************************************************/
	/** Custom methods	*/
	
	/**
	* Receive a moneris transaction object and send it to the moneris webservice
	*
	* @var mpgTransaction $transaction
	*/
	public function _send(mpgTransaction $transaction) {
		
		$store_id  = $this->getConfigData('store_id');
		$api_token = $this->getConfigData('api_token');
		
		$request = new mpgRequest($transaction);

		$mpgHttpsPost  = new mpgHttpsPost($store_id, $api_token, $request);

		return $mpgHttpsPost->getMpgResponse();
	}
	
	/**
	* Build a moneris transaction object the data of moneris
	* Make sure the transaction object is the appropriate type for the current
	* step.
	*
	* @var Varien_Object $payment
	* @var string $type
	*/
	public function _build(Varien_Object $payment, $type) {
		$order    = $payment->getOrder();	
		$billing  = $order->getBillingAddress();
		$shipping = $order->getShippingAddress();
		
		# Should be only used in the developement environment
		# without it we get duplicate order id.
		$token = $this->getConfigData('order_token');
		$token = (empty($token)) ? "" : "-" . $token;


		$transaction = array(
								'type'		 =>	$type,
								'order_id'	 =>	$order->getIncrementId() . $token,
								'crypt_type' =>	self::CRYPT_TYPE,
							);
	
							
		switch($type) {
			case self::TRANSACTION_PREAUTH :
				$transaction = $transaction + array(
							'cust_id'	 =>	$billing->getCustomerId(),
							'amount'	 =>	sprintf("%01.2f", $payment->getAmount()),
							'pan'		 =>	$this->_cleanCC($payment->getCcNumber()),
							'expdate'	 =>	$this->_formatExpirationDate($payment->getCcExpYear(), $payment->getCcExpMonth()),
							'cvd_value'  => $payment->getCcCid(),
							'cvd_indicator' => 1
				);
				
				break;
			case self::TRANSACTION_COMPLETION :
				$transaction = $transaction + array(
					'comp_amount' => sprintf("%01.2f", $payment->getAmount()),
					'txn_number'  => $payment->getCcTransId(),						
				);
				
				break;
			case self::TRANSACTION_VOID :
				$transaction = $transaction + array(
					'comp_amount' => sprintf("%01.2f", $payment->getAmount()),
					'txn_number'  => $payment->getCcTransId(),
				);
				break;

		}

		return new mpgTransaction($transaction);
	}
	
	/**
	* Clean the CC number, make sure its only digit.
	*
	* @var string $cc
	*/
	public function _cleanCC($cc) {
		return preg_replace('/[^\d]/', '', $cc);
	}
	
	/**
	* Format the expiration date for the moneris webservice
	*
	* The year is in two digit format
	* 2008-09 become 08-09
	*
	* @var string $year
	* @var string $month
	*/
	public function _formatExpirationDate($year, $month) {
		$year = substr($year, 2, 2); // use two year digits.
			
		return sprintf("%s%02d", $year, $month);
	}
}