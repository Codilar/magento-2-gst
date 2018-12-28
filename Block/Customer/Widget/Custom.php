<?php
namespace Codilar\Gst\Block\Customer\Widget;

use Magento\Customer\Model\AddressFactory;
use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Review\Block\Adminhtml\Add;

class Custom extends Template
{
    /**
     * @var AddressFactory
     */
    protected $_addressFactory;
    /**
     * @var AddressMetadataInterface
     */
    private $addressMetadata;

    /**
     * Custom constructor.
     * @param Template\Context $context
     * @param AddressMetadataInterface $addressMetadata
     * @param AddressFactory $addressFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        AddressMetadataInterface $addressMetadata,
        AddressFactory $addressFactory,
        array $data = [])
    {
        $this->addressMetadata = $addressMetadata;
        $this->_addressFactory = $addressFactory;
        $this->setTemplate('widget/gstin.phtml');
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isRequired(){
        return $this->getAttribute()
            ? $this->getAttribute()->isRequired()
            : null;
    }

    /**
     * @return string
     */
    public function getFieldId(){
        return 'gstin';
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getFieldLabel(){
        return "GSTIN";
    }

    /**
     * @return string
     */
    public function getFieldName(){
        return 'gstin';
    }
    /**
     * @return string|null
     */
    public function getValue(){
        $addressId = $this->getRequest()->getParam('id');
        $addressCollection = $this->_addressFactory->create()->load($addressId);
        $gstin = $addressCollection->getGstin();
        if($gstin){
            return $gstin;
        }
        return null;
    }

    public function getAttribute(){
        try{
            $attribute = $this->addressMetadata->getAttributeMetadata('gstin');
        }catch (NoSuchEntityException $exception){
            return null;
        }
        return $attribute[0];
    }
}