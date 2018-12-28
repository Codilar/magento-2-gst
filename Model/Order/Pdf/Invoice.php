<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    Codilar_Gst Extension
 * @package    Codilar\Gst\Model\Order\Pdf
 * @copyright   Copyright (c) 2017 Codilar. (http://www.codilar.com)
 * @purpose     Overriding core invoice file for adding gst fields
 * @author       Codilar Team
 **/
namespace Codilar\Gst\Model\Order\Pdf;

use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;

/**
 * Sales Order Invoice PDF model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Invoice extends \Magento\Sales\Model\Order\Pdf\Invoice
{
    protected $_moduleStatus = false;
    protected $_productionState = "";
    protected $_shippingGstStatus = true;
    protected $_gstid = "";
    protected $taxOrder="";

    /**
     * Invoice constructor.
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Sales\Model\Order\Pdf\Config $pdfConfig
     * @param \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory
     * @param \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param array $data
     */
    public function __construct(
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sales\Model\Order\Pdf\Config $pdfConfig,
        \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory,
        \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        array $data = [])
    {
        parent::__construct($paymentData, $string, $scopeConfig, $filesystem, $pdfConfig, $pdfTotalFactory, $pdfItemsFactory, $localeDate, $inlineTranslation, $addressRenderer, $storeManager, $localeResolver, $data);
    }
    /**
     * Draw header for item table
     *
     * @param \Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(\Zend_Pdf_Page $page)
    {
        if(!$this->_moduleStatus){
            parent::_drawHeader($page);
        }
        else{
            $order = $this->taxOrder;
            $orderDate = $order->getCreatedAt();
            $orderDate = date_create($orderDate);
            $date = "2017-06-30 11:59:59";
            $gstDate=date_create($date);
            $gstOrderStatus = false;
            if($gstDate < $orderDate){
                $gstOrderStatus = true;
            }
            $state = $order->getShippingAddress()->getRegion();
            $state = str_replace(' ', '', strtolower($state));
            /* Add table head */
            $this->_setFontRegular($page, 10);
            $page->setFillColor(new \Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 570, $this->y - 15);
            $this->y -= 10;
            $page->setFillColor(new \Zend_Pdf_Color_RGB(0, 0, 0));

            //columns headers
            $lines[0][] = ['text' => __('Products'), 'feed' => 35];

            $lines[0][] = ['text' => __('SKU'), 'feed' => 290, 'align' => 'right'];

            $lines[0][] = ['text' => __('HSN'), 'feed' => 325, 'align' => 'right'];

            $lines[0][] = ['text' => __('Qty'), 'feed' => 510, 'align' => 'right'];

            $lines[0][] = ['text' => __('Price'), 'feed' => 370, 'align' => 'right'];

            if(($state==$this->_productionState) && $gstOrderStatus){
                $lines[0][] = ['text' => __('SGST'), 'feed' => 420, 'align' => 'right'];

                $lines[0][] = ['text' => __('CGST'), 'feed' => 470, 'align' => 'right'];
            }
            else if($gstOrderStatus){
                $lines[0][] = ['text' => __('IGST'), 'feed' => 450, 'align' => 'right'];
            }
            else{
                $lines[0][] = ['text' => __('Tax Amount'), 'feed' => 450, 'align' => 'right'];
            }

            $lines[0][] = ['text' => __('Subtotal'), 'feed' => 565, 'align' => 'right'];

            $lineBlock = ['lines' => $lines, 'height' => 5];

            $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
            $this->y -= 20;
        }

    }

    /**
     * Return PDF document
     *
     * @param array|Collection $invoices
     * @return \Zend_Pdf
     */
    public function getPdf($invoices = [])
    {
        $this->_moduleStatus = $this->_scopeConfig->getValue(
            "gst/codilar/status",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $this->_productionState = $this->_scopeConfig->getValue('gst/codilar/production_state', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->_productionState = str_replace(' ', '', strtolower($this->_productionState));
        $this->_shippingGstStatus = $this->_scopeConfig->getValue(
            "gst/codilar/shipping_status",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                $this->_localeResolver->emulate($invoice->getStoreId());
                $this->_storeManager->setCurrentStore($invoice->getStoreId());
            }
            $page = $this->newPage();
            $order = $invoice->getOrder();
            $this->taxOrder = $order;
            /* Add image */
            $this->insertLogo($page, $invoice->getStore());
            /* Add address */
            $this->insertAddress($page, $invoice->getStore());
            if($this->_moduleStatus){
                /* Add head */
                $this->insertOrderCustom(
                    $page,
                    $order,
                    $this->_scopeConfig->isSetFlag(
                        self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $order->getStoreId()
                    )
                );
            }
            else{
                /* Add head */
                $this->insertOrder(
                    $page,
                    $order,
                    $this->_scopeConfig->isSetFlag(
                        self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $order->getStoreId()
                    )
                );
            }

            /* Add document text and number */
            $this->insertDocumentNumber($page, __('Invoice # ') . $invoice->getIncrementId());
            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($invoice->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
            if($this->_moduleStatus){
                /* Add totals */
                $this->insertTotalsCustom($page, $invoice);
                if ($invoice->getStoreId()) {
                    $this->_localeResolver->revert();
                }
            }
            else{
                /* Add totals */
                $this->insertTotals($page, $invoice);
                if ($invoice->getStoreId()) {
                    $this->_localeResolver->revert();
                }
            }

        }
        $this->_afterGetPdf();
        return $pdf;
    }

    /**
     * Insert totals to pdf page
     *
     * @param  \Zend_Pdf_Page $page
     * @param  \Magento\Sales\Model\AbstractModel $source
     * @return \Zend_Pdf_Page
     */
    protected function insertTotalsCustom($page, $source)
    {
        $order = $source->getOrder();
        $state = $order->getShippingAddress()->getRegion();
        $state = str_replace(' ', '', strtolower($state));
        $orderTotalTaxAmount = $order->getTaxAmount();
        $shippingTaxAmount = $order->getShippingTaxAmount();
        $orderTaxAmount = $orderTotalTaxAmount;
        if($this->_shippingGstStatus){
            $orderTaxAmount = $orderTotalTaxAmount - $shippingTaxAmount;
        }
        $sgst = $orderTaxAmount / 2;
        $orderDate = $order->getCreatedAt();
        $orderDate = date_create($orderDate);
        $date = "2017-06-30 11:59:59";
        $gstDate=date_create($date);
        $gstOrderStatus = false;
        if($gstDate < $orderDate){
            $gstOrderStatus = true;
        }

        $totals = $this->_getTotalsList();
        $lineBlock = ['lines' => [], 'height' => 15];
        $flag = 1;
        foreach ($totals as $total) {
            $total->setOrder($order)->setSource($source);
            if ($total->canDisplay()) {
                $total->setFontSize(10);
                foreach ($total->getTotalsForDisplay() as $totalData) {
                    if($gstOrderStatus && ($totalData['label']=="Tax:" || array_key_exists('tax_amount',$totalData))){
                        if($flag){
                            $flag = 0;
                            if($state==$this->_productionState){
                                $lineBlock['lines'][] = [
                                    [
                                        'text' => "SGST:",
                                        'feed' => 475,
                                        'align' => 'right',
                                        'font_size' => $totalData['font_size'],
                                        'font' => 'bold',
                                    ],
                                    [
                                        'text' => $order->formatPriceTxt($sgst),
                                        'feed' => 565,
                                        'align' => 'right',
                                        'font_size' => $totalData['font_size'],
                                        'font' => 'bold'
                                    ],
                                ];
                                $lineBlock['lines'][] = [
                                    [
                                        'text' => "CGST:",
                                        'feed' => 475,
                                        'align' => 'right',
                                        'font_size' => $totalData['font_size'],
                                        'font' => 'bold',
                                    ],
                                    [
                                        'text' => $order->formatPriceTxt($sgst),
                                        'feed' => 565,
                                        'align' => 'right',
                                        'font_size' => $totalData['font_size'],
                                        'font' => 'bold'
                                    ],
                                ];
                            }
                            else{
                                $lineBlock['lines'][] = [
                                    [
                                        'text' => "IGST:",
                                        'feed' => 475,
                                        'align' => 'right',
                                        'font_size' => $totalData['font_size'],
                                        'font' => 'bold',
                                    ],
                                    [
                                        'text' => $order->formatPriceTxt($orderTaxAmount),
                                        'feed' => 565,
                                        'align' => 'right',
                                        'font_size' => $totalData['font_size'],
                                        'font' => 'bold'
                                    ],
                                ];
                            }

                            if($this->_shippingGstStatus && $shippingTaxAmount>0 ){
                                $lineBlock['lines'][] = [
                                    [
                                        'text' => "Shipping GST:",
                                        'feed' => 475,
                                        'align' => 'right',
                                        'font_size' => $totalData['font_size'],
                                        'font' => 'bold',
                                    ],
                                    [
                                        'text' => $order->formatPriceTxt($shippingTaxAmount),
                                        'feed' => 565,
                                        'align' => 'right',
                                        'font_size' => $totalData['font_size'],
                                        'font' => 'bold'
                                    ],
                                ];
                            }
                            $lineBlock['lines'][] = [
                                [
                                    'text' => "Total GST:",
                                    'feed' => 475,
                                    'align' => 'right',
                                    'font_size' => $totalData['font_size'],
                                    'font' => 'bold',
                                ],
                                [
                                    'text' => $order->formatPriceTxt($orderTotalTaxAmount),
                                    'feed' => 565,
                                    'align' => 'right',
                                    'font_size' => $totalData['font_size'],
                                    'font' => 'bold'
                                ],
                            ];
                        }
                    }
                    else{
                        $lineBlock['lines'][] = [
                        [
                            'text' => $totalData['label'],
                            'feed' => 475,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold',
                        ],
                        [
                            'text' => $totalData['amount'],
                            'feed' => 565,
                            'align' => 'right',
                            'font_size' => $totalData['font_size'],
                            'font' => 'bold'
                        ],
                        ];
                    }

                }
            }
        }

        $this->y -= 20;
        $page = $this->drawLineBlocks($page, [$lineBlock]);
        return $page;
    }


    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     * @return \Zend_Pdf_Page
     */
    public function newPage(array $settings = [])
    {
        /* Add new table head */
        $page = $this->_getPdf()->newPage(\Zend_Pdf_Page::SIZE_A4);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;
        if (!empty($settings['table_header'])) {
            $this->_drawHeader($page);
        }
        return $page;
    }

    protected function insertOrderCustom(&$page, $obj, $putOrderId = true)
    {
        if ($obj instanceof \Magento\Sales\Model\Order) {
            $shipment = null;
            $order = $obj;
        } elseif ($obj instanceof \Magento\Sales\Model\Order\Shipment) {
            $shipment = $obj;
            $order = $shipment->getOrder();
        }

        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0.45));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.45));
        $page->drawRectangle(25, $top, 570, $top - 65);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $this->setDocHeaderCoordinates([25, $top, 570, $top - 55]);
        $this->_setFontRegular($page, 10);

        if ($putOrderId) {
            $page->drawText(__('Order # ') . $order->getRealOrderId(), 35, $top -= 30, 'UTF-8');
        }
        $page->drawText(
            __('Order Date: ') .
            $this->_localeDate->formatDate(
                $this->_localeDate->scopeDate(
                    $order->getStore(),
                    $order->getCreatedAt(),
                    true
                ),
                \IntlDateFormatter::MEDIUM,
                false
            ),
            35,
            $top -= 15,
            'UTF-8'
        );
        $gstin = $this->_scopeConfig->getValue(
        "gst/codilar/gstin",
        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (strlen($gstin)>0) {
            $page->drawText(__('GSTIN : ') . $gstin, 35, $top -= 15, 'UTF-8');
        }

        $top -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $top, 275, $top - 25);
        $page->drawRectangle(275, $top, 570, $top - 25);

        /* Calculate blocks info */

        /* Billing Address */
        $billingAddress = $this->_formatAddress($this->addressRenderer->format($order->getBillingAddress(), 'pdf'));

        /* Payment */
        $paymentInfo = $this->_paymentData->getInfoBlock($order->getPayment())->setIsSecureMode(true)->toPdf();
        $paymentInfo = htmlspecialchars_decode($paymentInfo, ENT_QUOTES);
        $payment = explode('{{pdf_row_separator}}', $paymentInfo);
        foreach ($payment as $key => $value) {
            if (strip_tags(trim($value)) == '') {
                unset($payment[$key]);
            }
        }
        reset($payment);

        /* Shipping Address and Method */
        if (!$order->getIsVirtual()) {
            /* Shipping Address */
            $shippingAddress = $this->_formatAddress($this->addressRenderer->format($order->getShippingAddress(), 'pdf'));
            $shippingMethod = $order->getShippingDescription();
        }

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 12);
        $page->drawText(__('Sold to:'), 35, $top - 15, 'UTF-8');

        if (!$order->getIsVirtual()) {
            $page->drawText(__('Ship to:'), 285, $top - 15, 'UTF-8');
        } else {
            $page->drawText(__('Payment Method:'), 285, $top - 15, 'UTF-8');
        }

        $addressesHeight = $this->_calcAddressHeight($billingAddress);
        if (isset($shippingAddress)) {
            $addressesHeight = max($addressesHeight, $this->_calcAddressHeight($shippingAddress));
        }

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, $top - 25, 570, $top - 33 - $addressesHeight);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 10);
        $this->y = $top - 40;
        $addressesStartY = $this->y;

        foreach ($billingAddress as $value) {
            if ($value !== '') {
                $text = [];
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $page->drawText(strip_tags(ltrim($part)), 35, $this->y, 'UTF-8');
                    $this->y -= 15;
                }
            }
        }

        $addressesEndY = $this->y;

        if (!$order->getIsVirtual()) {
            $this->y = $addressesStartY;
            foreach ($shippingAddress as $value) {
                if ($value !== '') {
                    $text = [];
                    foreach ($this->string->split($value, 45, true, true) as $_value) {
                        $text[] = $_value;
                    }
                    foreach ($text as $part) {
                        $page->drawText(strip_tags(ltrim($part)), 285, $this->y, 'UTF-8');
                        $this->y -= 15;
                    }
                }
            }

            $addressesEndY = min($addressesEndY, $this->y);
            $this->y = $addressesEndY;

            $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $this->y, 275, $this->y - 25);
            $page->drawRectangle(275, $this->y, 570, $this->y - 25);

            $this->y -= 15;
            $this->_setFontBold($page, 12);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
            $page->drawText(__('Payment Method'), 35, $this->y, 'UTF-8');
            $page->drawText(__('Shipping Method:'), 285, $this->y, 'UTF-8');

            $this->y -= 10;
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));

            $this->_setFontRegular($page, 10);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));

            $paymentLeft = 35;
            $yPayments = $this->y - 15;
        } else {
            $yPayments = $addressesStartY;
            $paymentLeft = 285;
        }

        foreach ($payment as $value) {
            if (trim($value) != '') {
                //Printing "Payment Method" lines
                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $page->drawText(strip_tags(trim($_value)), $paymentLeft, $yPayments, 'UTF-8');
                    $yPayments -= 15;
                }
            }
        }

        if ($order->getIsVirtual()) {
            // replacement of Shipments-Payments rectangle block
            $yPayments = min($addressesEndY, $yPayments);
            $page->drawLine(25, $top - 25, 25, $yPayments);
            $page->drawLine(570, $top - 25, 570, $yPayments);
            $page->drawLine(25, $yPayments, 570, $yPayments);

            $this->y = $yPayments - 15;
        } else {
            $topMargin = 15;
            $methodStartY = $this->y;
            $this->y -= 15;

            foreach ($this->string->split($shippingMethod, 45, true, true) as $_value) {
                $page->drawText(strip_tags(trim($_value)), 285, $this->y, 'UTF-8');
                $this->y -= 15;
            }

            $yShipments = $this->y;
            $totalShippingChargesText = "(" . __(
                    'Total Shipping Charges'
                ) . " " . $order->formatPriceTxt(
                    $order->getShippingAmount()
                ) . ")";

            $page->drawText($totalShippingChargesText, 285, $yShipments - $topMargin, 'UTF-8');
            $yShipments -= $topMargin + 10;

            $tracks = [];
            if ($shipment) {
                $tracks = $shipment->getAllTracks();
            }
            if (count($tracks)) {
                $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
                $page->setLineWidth(0.5);
                $page->drawRectangle(285, $yShipments, 510, $yShipments - 10);
                $page->drawLine(400, $yShipments, 400, $yShipments - 10);
                //$page->drawLine(510, $yShipments, 510, $yShipments - 10);

                $this->_setFontRegular($page, 9);
                $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
                //$page->drawText(__('Carrier'), 290, $yShipments - 7 , 'UTF-8');
                $page->drawText(__('Title'), 290, $yShipments - 7, 'UTF-8');
                $page->drawText(__('Number'), 410, $yShipments - 7, 'UTF-8');

                $yShipments -= 20;
                $this->_setFontRegular($page, 8);
                foreach ($tracks as $track) {
                    $maxTitleLen = 45;
                    $endOfTitle = strlen($track->getTitle()) > $maxTitleLen ? '...' : '';
                    $truncatedTitle = substr($track->getTitle(), 0, $maxTitleLen) . $endOfTitle;
                    $page->drawText($truncatedTitle, 292, $yShipments, 'UTF-8');
                    $page->drawText($track->getNumber(), 410, $yShipments, 'UTF-8');
                    $yShipments -= $topMargin - 5;
                }
            } else {
                $yShipments -= $topMargin - 5;
            }

            $currentY = min($yPayments, $yShipments);

            // replacement of Shipments-Payments rectangle block
            $page->drawLine(25, $methodStartY, 25, $currentY);
            //left
            $page->drawLine(25, $currentY, 570, $currentY);
            //bottom
            $page->drawLine(570, $currentY, 570, $methodStartY);
            //right

            $this->y = $currentY;
            $this->y -= 15;
        }
    }
}
