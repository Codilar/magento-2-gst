<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    Codilar_Gst Extension
 * @package
 * @copyright   Copyright (c) 2017 Codilar. (http://www.codilar.com)
 * @purpose
 * @author       Codilar Team
 **/
namespace Codilar\Gst\Observer;
use Magento\Sales\Model\Order\Tax;
use Magento\Sales\Model\OrderFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
class SaveTax implements \Magento\Framework\Event\ObserverInterface {

    /** @var \Magento\Framework\Logger\Monolog */
    protected $_logger;
    protected $_orderFactory;
    protected $_scopeConfig;
    protected $_tax;

    public function __construct(
        \Psr\Log\LoggerInterface $loggerInterface,
        OrderFactory $orderFactory,
        Tax $tax,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->_orderFactory = $orderFactory;
        $this->_tax = $tax;
        $this->_logger = $loggerInterface;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * This is the method that fires when the event runs.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer ) {

        $orderIds = $observer->getEvent()->getOrderIds();

        if (count($orderIds)) {
            $orderId = $orderIds[0];
            $order = $this->_orderFactory->create()->load($orderId);
            $taxCollection = $this->_tax->load($orderId,'order_id');
            $productionState = $this->_scopeConfig->getValue('gst/codilar/production_state', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $orderState = $order->getShippingAddress()->getRegion();
            $orderState = str_replace(' ', '', strtolower($orderState));
            $productionState = str_replace(' ', '', strtolower($productionState));
            $taxAmount = $taxCollection->getAmount();
            $taxSgst = 0;
            $taxIgst = 0;
            if($orderState == $productionState) {
                $taxSgst = $taxAmount/2;
                $taxIgst = 0;
            }
            else{
                $taxSgst = 0;
                $taxIgst = $taxAmount;
            }
            $taxCollection->setSgst($taxSgst);
            $taxCollection->setCgst($taxSgst);
            $taxCollection->setIgst($taxIgst);
            $taxCollection->save();
        }
    }
}