<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    Codilar_Gst Extension
 * @package    Codilar\Gst\Controller\Checkout
 * @copyright   Copyright (c) 2017 Codilar. (http://www.codilar.com)
 * @purpose
 * @author       Codilar Team
 **/
namespace Codilar\Gst\Controller\Checkout;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\AddressFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BillingAddress extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    /**
     * @var AddressFactory
     */
    protected $_addressFactory;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * ChangeBillingAddress constructor.
     * @param Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Psr\Log\LoggerInterface $loggerInterface,
        AddressFactory $addressFactory,
        Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession
    ) {
        parent::__construct($context);
        $this->_logger = $loggerInterface;
        $this->_addressFactory = $addressFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
    }

    public function execute()
    {
        $gstin = $this->getRequest()->getParam('gstin');
        $saveAddress = $this->getRequest()->getParam('save');
        if($this->_customerSession->isLoggedIn()){
            $this->_checkoutSession->setGuestCheckout("0");
            $newCustomer = $this->_checkoutSession->getNewCustomer();
            if($newCustomer=="1"){
                $this->_checkoutSession->setNewCustomerBillingGst($gstin);
                $this->_checkoutSession->setNewCustomerSaveBillingAddress("1");
            }
            else{
                $this->_checkoutSession->setOldCustomer("1");
                $billingGstin = $gstin;
                $this->_checkoutSession->setOldSaveBillingAddress($saveAddress);
                $this->_checkoutSession->setOldNewBillingGst($billingGstin);
            }
        }
        else{
            $this->_checkoutSession->getQuote()->getBillingAddress()->setData('gstin',$gstin)->setAddressType('billing')->save();
            $this->_checkoutSession->setGuestCheckout("1");
            $this->_checkoutSession->setGuestBillingGst($gstin);
        }
        $data['status'] = 1;
        print_r(json_encode($data));
    }
}
