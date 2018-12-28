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
class NewShippingAddress extends \Magento\Framework\App\Action\Action
{
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
        AddressFactory $addressFactory,
        Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Customer $customer
    ) {
        parent::__construct($context);
        $this->_addressFactory = $addressFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_customer = $customer;
    }

    public function execute()
    {
        $gstin = $this->getRequest()->getParam('gstin');
        $saveAddress = $this->getRequest()->getParam('save_address');
        $this->_checkoutSession->setOldCustomer("1");
        $this->_checkoutSession->setOldNewShippingGst($gstin);
        $this->_checkoutSession->setOldNewSaveShippingAddress($saveAddress);
        $data['status'] = 1;
        print_r(json_encode($data));die;
    }
}
