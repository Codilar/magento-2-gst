<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    Codilar_Gst Extension
 * @package    Codilar\Gst\Model|Checkout
 * @copyright   Copyright (c) 2017 Codilar. (http://www.codilar.com)
 * @purpose     For sending configuration to checkout
 * @author       Codilar Team
 **/
namespace Codilar\Gst\Model\Checkout;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * ConfigProvider constructor.
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        StoreManagerInterface $storeManagerInterface,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->_storeManager = $storeManagerInterface;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $config = [];
        try {
            $config['codilar_gst']['production_state'] = $this->_scopeConfig->getValue('gst/codilar/production_state', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $config['codilar_gst']['status'] = $this->_scopeConfig->getValue('gst/codilar/status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $config['codilar_gst']['gstin'] = $this->_scopeConfig->getValue('gst/codilar/gstin', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $config['codilar_gst']['shipping_status'] = $this->_scopeConfig->getValue('gst/codilar/shipping_status', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $config['codilar_gst']['baseUrl'] = $baseUrl;
            return $config;
        }catch(\Exception $e){

        }
    }


}
