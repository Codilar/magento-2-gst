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
class ShippingAddress extends \Magento\Framework\App\Action\Action
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
     * @var Customer
     */
    protected $_customer;

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
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Customer $customer
    ) {
        parent::__construct($context);
        $this->_logger = $loggerInterface;
        $this->_addressFactory = $addressFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_customer = $customer;
    }

    public function execute()
    {
        $gstin = $this->getRequest()->getParam('gstin');
        if($this->_customerSession->isLoggedIn()){
            $this->_checkoutSession->setGuestCheckout("0");
            $customerId = $this->_customerSession->getCustomerId();
            $customer = $this->_customer->load($customerId);
            if(!$customer->getAddresses()){
                $this->_checkoutSession->getQuote()->getShippingAddress()->setData('gstin',$gstin)->setAddressType('shipping')->save();
                $this->_checkoutSession->setNewCustomer("1");
                $this->_checkoutSession->setNewCustomerShippingGst($gstin);
                $this->_checkoutSession->setNewCustomerSaveShippingAddress("1");
            }
            else{
                $this->_checkoutSession->setOldCustomer("1");
                if($this->_checkoutSession->getQuote()->getShippingAddress()->getCustomerAddressId()){
                    $shippingAddressId = $this->_checkoutSession->getQuote()->getShippingAddress()->getCustomerAddressId();
                    $addressCollection = $this->_addressFactory->create()->load($shippingAddressId);
                    $shippingGstin = $addressCollection->getGstin();
                    $this->_checkoutSession->setOldShippingGst($shippingGstin);
                    $this->_checkoutSession->getQuote()->getShippingAddress()->setData('gstin',$shippingGstin)->setAddressType('shipping')->save();
                }
                else{
                    $shippingGstin = $gstin;
                    $this->_checkoutSession->setOldNewShippingGst($shippingGstin);
                }
            }

        }
        else{
            $this->_checkoutSession->getQuote()->getShippingAddress()->setData('gstin',$gstin)->setAddressType('shipping')->save();
            $this->_checkoutSession->setGuestCheckout("1");
            $this->_checkoutSession->setGuestShippingGst($gstin);
        }
        $data['status'] = 1;
        print_r(json_encode($data));
    }
}
