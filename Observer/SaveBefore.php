<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    Codilar_Gst Extension
 * @package    Codilar\Gst\Observer
 * @copyright   Copyright (c) 2017 Codilar. (http://www.codilar.com)
 * @purpose
 * @author       Codilar Team
 **/
namespace Codilar\Gst\Observer;
use Magento\Checkout\Model\Session;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Customer\Model\AddressFactory;
class SaveBefore implements \Magento\Framework\Event\ObserverInterface {

    /**
     * @var Session
     */
    protected $_checkoutSession;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    /**
     * @var \Magento\Framework\ObjectManager\ObjectManager
     */
    protected $_objectManager;
    /**
     * @var AddressFactory
     */
    protected $_addressFactory;

    public function __construct(
        \Psr\Log\LoggerInterface $loggerInterface,
        Session $checkoutSession,
        AddressFactory $addressFactory,
        \Magento\Framework\ObjectManager\ObjectManager $objectManager
    ) {
        $this->_logger = $loggerInterface;
        $this->_addressFactory = $addressFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_objectManager = $objectManager;
    }

    /**
     * This is the method that fires when the event runs.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer )
    {
        $order = $observer->getEvent()->getOrder();
        try{
            $guestCheckout = $this->_checkoutSession->getGuestCheckout();
            $this->_checkoutSession->unsGuestCheckout();
            $guestShippingGst = $this->_checkoutSession->getGuestShippingGst();
            $this->_checkoutSession->unsGuestShippingGst();
            $guestBillingGst = $this->_checkoutSession->getGuestBillingGst();
            $this->_checkoutSession->unsGuestBillingGst();

            $newCustomer = $this->_checkoutSession->getNewCustomer();
            $this->_checkoutSession->unsNewCustomer();
            $newCustomerShippingGst = $this->_checkoutSession->getNewCustomerShippingGst();
            $this->_checkoutSession->unsNewCustomerShippingGst();
            $newCustomerBillingGst = $this->_checkoutSession->getNewCustomerBillingGst();
            $this->_checkoutSession->unsNewCustomerBillingGst();

            $oldCustomer = $this->_checkoutSession->getOldCustomer();
            $this->_checkoutSession->unsOldCustomer();
            $oldShippingGst = $this->_checkoutSession->getOldShippingGst();
            $this->_checkoutSession->unsOldShippingGst();
            $oldNewShippingGst = $this->_checkoutSession->getOldNewShippingGst();
            $this->_checkoutSession->unsOldNewShippingGst();
            $oldNewSaveShippingAddress = $this->_checkoutSession->getOldNewSaveShippingAddress();
            $this->_checkoutSession->unsOldNewSaveShippingAddress();
            $oldBillingGst = $this->_checkoutSession->getOldBillingGst();
            $this->_checkoutSession->unsOldBillingGst();
            $oldNewSaveBillingAddress = $this->_checkoutSession->getOldSaveBillingAddress();
            $this->_checkoutSession->unsOldSaveBillingAddress();
            $oldNewBillingGst = $this->_checkoutSession->getOldNewBillingGst();
            $this->_checkoutSession->unsOldNewBillingGst();
            $billingAddressId = $order->getBillingAddress()->getCustomerAddressId();
            $shippingAddressId = $order->getShippingAddress()->getCustomerAddressId();

            if($guestCheckout == "1" && !empty($guestBillingGst)){
                $order->getShippingAddress()->setGstin($guestShippingGst);
                $order->getBillingAddress()->setGstin($guestBillingGst);
            }
            elseif ($guestCheckout == "1"){

                $order->getShippingAddress()->setGstin($guestShippingGst);
                $order->getBillingAddress()->setGstin($guestShippingGst);
            }
            elseif ($newCustomer == "1" && !empty($newCustomerBillingGst)){
                $order->getShippingAddress()->setGstin($newCustomerShippingGst);
                $order->getBillingAddress()->setGstin($newCustomerBillingGst);
                $saveBillingAddress = [
                    'parent_id' => $order->getCustomerId(),
                    'entity_id' =>$order->getBillingAddress()->getCustomerAddressId(),
                    'firstname' => $order->getBillingAddress()->getFirstname(),
                    'lastname' => $order->getBillingAddress()->getLastname(),
                    'company' => $order->getBillingAddress()->getCompany(),
                    'postcode' => $order->getBillingAddress()->getPostcode(),
                    'region' => $order->getBillingAddress()->getRegion(),
                    'street' => $order->getBillingAddress()->getStreet(),
                    'city' => $order->getBillingAddress()->getCity(),
                    'country_id' => $order->getBillingAddress()->getCountryId(),
                    'telephone' => $order->getBillingAddress()->getTelephone(),
                    'gstin' => $newCustomerBillingGst
                ];
                $this->_addressFactory->create()->setData($saveBillingAddress)->save();
                $saveShippingAddress = [
                    'parent_id' => $order->getCustomerId(),
                    'entity_id' =>$order->getShippingAddress()->getCustomerAddressId(),
                    'firstname' => $order->getShippingAddress()->getFirstname(),
                    'lastname' => $order->getShippingAddress()->getLastname(),
                    'company' => $order->getShippingAddress()->getCompany(),
                    'postcode' => $order->getShippingAddress()->getPostcode(),
                    'region' => $order->getShippingAddress()->getRegion(),
                    'street' => $order->getShippingAddress()->getStreet(),
                    'city' => $order->getShippingAddress()->getCity(),
                    'country_id' => $order->getShippingAddress()->getCountryId(),
                    'telephone' => $order->getShippingAddress()->getTelephone(),
                    'gstin' => $newCustomerShippingGst
                ];
                $this->_addressFactory->create()->setData($saveShippingAddress)->save();
            }
            elseif($newCustomer == "1"){
                $order->getShippingAddress()->setGstin($newCustomerShippingGst);
                $order->getBillingAddress()->setGstin($newCustomerShippingGst);
                $saveaddress = [
                    'parent_id' => $order->getCustomerId(),
                    'entity_id' =>$order->getShippingAddress()->getCustomerAddressId(),
                    'firstname' => $order->getShippingAddress()->getFirstname(),
                    'lastname' => $order->getShippingAddress()->getLastname(),
                    'company' => $order->getShippingAddress()->getCompany(),
                    'postcode' => $order->getShippingAddress()->getPostcode(),
                    'region' => $order->getShippingAddress()->getRegion(),
                    'street' => $order->getShippingAddress()->getStreet(),
                    'city' => $order->getShippingAddress()->getCity(),
                    'country_id' => $order->getShippingAddress()->getCountryId(),
                    'telephone' => $order->getShippingAddress()->getTelephone(),
                    'gstin' => $newCustomerShippingGst
                ];
                $this->_addressFactory->create()->setData($saveaddress)->save();
            }
            elseif($oldCustomer == "1" && ($shippingAddressId == $billingAddressId) && !empty($shippingAddressId && !empty($billingAddressId))){
                if($oldNewSaveShippingAddress == "1"){
                    $gstin = $oldNewShippingGst;
                    $saveaddress = [
                        'parent_id' => $order->getCustomerId(),
                        'entity_id' =>$order->getShippingAddress()->getCustomerAddressId(),
                        'firstname' => $order->getShippingAddress()->getFirstname(),
                        'lastname' => $order->getShippingAddress()->getLastname(),
                        'company' => $order->getShippingAddress()->getCompany(),
                        'postcode' => $order->getShippingAddress()->getPostcode(),
                        'region' => $order->getShippingAddress()->getRegion(),
                        'street' => $order->getShippingAddress()->getStreet(),
                        'city' => $order->getShippingAddress()->getCity(),
                        'country_id' => $order->getShippingAddress()->getCountryId(),
                        'telephone' => $order->getShippingAddress()->getTelephone(),
                        'gstin' => $gstin
                    ];
                    $this->_addressFactory->create()->setData($saveaddress)->save();
                }
                else{
                    $addressCollection = $this->_addressFactory->create()->load($shippingAddressId);
                    $gstin = $addressCollection->getGstin();
                }
                $order->getBillingAddress()->setGstin($gstin);
                $order->getShippingAddress()->setGstin($gstin);

            }
            elseif($oldCustomer == "1" && ($shippingAddressId != $billingAddressId) && !empty($shippingAddressId) && !empty($billingAddressId)){
                $addressCollection = $this->_addressFactory->create()->load($shippingAddressId);
                $shippingGstin = $addressCollection->getGstin();
                $billingAddressCollection = $this->_addressFactory->create()->load($billingAddressId);
                $billingGstin = $billingAddressCollection->getGstin();
                if($oldNewSaveShippingAddress == "1"){
                    $shippingGstin = $oldNewShippingGst;
                    $saveaddress = [
                        'parent_id' => $order->getCustomerId(),
                        'entity_id' =>$order->getShippingAddress()->getCustomerAddressId(),
                        'firstname' => $order->getShippingAddress()->getFirstname(),
                        'lastname' => $order->getShippingAddress()->getLastname(),
                        'company' => $order->getShippingAddress()->getCompany(),
                        'postcode' => $order->getShippingAddress()->getPostcode(),
                        'region' => $order->getShippingAddress()->getRegion(),
                        'street' => $order->getShippingAddress()->getStreet(),
                        'city' => $order->getShippingAddress()->getCity(),
                        'country_id' => $order->getShippingAddress()->getCountryId(),
                        'telephone' => $order->getShippingAddress()->getTelephone(),
                        'gstin' => $shippingGstin
                    ];
                    $this->_addressFactory->create()->setData($saveaddress)->save();
                }
                if ($oldNewSaveBillingAddress == "1"){
                    $billingGstin = $oldNewBillingGst;
                    $saveaddress = [
                        'parent_id' => $order->getCustomerId(),
                        'entity_id' =>$order->getBillingAddress()->getCustomerAddressId(),
                        'firstname' => $order->getBillingAddress()->getFirstname(),
                        'lastname' => $order->getBillingAddress()->getLastname(),
                        'company' => $order->getBillingAddress()->getCompany(),
                        'postcode' => $order->getBillingAddress()->getPostcode(),
                        'region' => $order->getBillingAddress()->getRegion(),
                        'street' => $order->getBillingAddress()->getStreet(),
                        'city' => $order->getBillingAddress()->getCity(),
                        'country_id' => $order->getBillingAddress()->getCountryId(),
                        'telephone' => $order->getBillingAddress()->getTelephone(),
                        'gstin' => $billingGstin
                    ];
                    $this->_addressFactory->create()->setData($saveaddress)->save();
                }

                $order->getBillingAddress()->setGstin($billingGstin);
                $order->getShippingAddress()->setGstin($shippingGstin);
            }
            elseif($oldCustomer == "1" && ($shippingAddressId != $billingAddressId) && empty($billingAddressId)){
                if($oldNewSaveShippingAddress == "1"){
                    $shippingGstin = $oldNewShippingGst;
                    $saveaddress = [
                        'parent_id' => $order->getCustomerId(),
                        'entity_id' =>$order->getShippingAddress()->getCustomerAddressId(),
                        'firstname' => $order->getShippingAddress()->getFirstname(),
                        'lastname' => $order->getShippingAddress()->getLastname(),
                        'company' => $order->getShippingAddress()->getCompany(),
                        'postcode' => $order->getShippingAddress()->getPostcode(),
                        'region' => $order->getShippingAddress()->getRegion(),
                        'street' => $order->getShippingAddress()->getStreet(),
                        'city' => $order->getShippingAddress()->getCity(),
                        'country_id' => $order->getShippingAddress()->getCountryId(),
                        'telephone' => $order->getShippingAddress()->getTelephone(),
                        'gstin' => $shippingGstin
                    ];
                    $this->_addressFactory->create()->setData($saveaddress)->save();
                }
                else{
                    $addressCollection = $this->_addressFactory->create()->load($shippingAddressId);
                    $shippingGstin = $addressCollection->getGstin();
                }
                if(!empty($oldNewBillingGst)){
                    $order->getBillingAddress()->setGstin($oldNewBillingGst);
                }
                elseif($oldNewSaveBillingAddress=="0" && empty($oldNewBillingGst)){

                }
                else{
                    $order->getBillingAddress()->setGstin($shippingGstin);
                }
                $order->getShippingAddress()->setGstin($shippingGstin);
            }
            elseif($oldCustomer == "1" && ($shippingAddressId != $billingAddressId) && empty($shippingAddressId)){
                if($oldNewSaveBillingAddress == "1"){
                    $billingGstin = $oldNewBillingGst;
                    $saveaddress = [
                        'parent_id' => $order->getCustomerId(),
                        'entity_id' =>$order->getBillingAddress()->getCustomerAddressId(),
                        'firstname' => $order->getBillingAddress()->getFirstname(),
                        'lastname' => $order->getBillingAddress()->getLastname(),
                        'company' => $order->getBillingAddress()->getCompany(),
                        'postcode' => $order->getBillingAddress()->getPostcode(),
                        'region' => $order->getBillingAddress()->getRegion(),
                        'street' => $order->getBillingAddress()->getStreet(),
                        'city' => $order->getBillingAddress()->getCity(),
                        'country_id' => $order->getBillingAddress()->getCountryId(),
                        'telephone' => $order->getBillingAddress()->getTelephone(),
                        'gstin' => $billingGstin
                    ];
                    $this->_addressFactory->create()->setData($saveaddress)->save();
                }
                else{
                    $addressCollection = $this->_addressFactory->create()->load($billingAddressId);
                    $billingGstin = $addressCollection->getGstin();
                }
                $shippingGstin = $oldNewShippingGst;
                $order->getBillingAddress()->setGstin($billingGstin);
                $order->getShippingAddress()->setGstin($shippingGstin);
            }
            elseif($oldCustomer == "1" && ($shippingAddressId == $billingAddressId) && empty($shippingAddressId) && empty($billingAddressId)){
                $shippingGstin = $oldNewShippingGst;
                if(!empty($oldNewBillingGst)){
                    $order->getBillingAddress()->setGstin($oldNewBillingGst);
                }
                elseif($oldNewSaveBillingAddress=="0" && empty($oldNewBillingGst)){

                }
                else{
                    $order->getBillingAddress()->setGstin($shippingGstin);
                }
                $order->getShippingAddress()->setGstin($shippingGstin);
            }
        }catch(\Exception $e){

        }

    }
}