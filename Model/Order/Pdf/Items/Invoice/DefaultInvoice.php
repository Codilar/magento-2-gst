<?php
/**
 * Codilar Technologies Pvt. Ltd.
 * @category    Codilar_Gst Extension
 * @package    Codilar\Gst\Model\Order\Pdf\Items\Invoice
 * @copyright   Copyright (c) 2017 Codilar. (http://www.codilar.com)
 * @purpose     Overriding core defaultinvoice file for adding gst fields
 * @author       Codilar Team
 **/
namespace Codilar\Gst\Model\Order\Pdf\Items\Invoice;
/**
 * Sales Order Invoice Pdf default items renderer
 */
class DefaultInvoice extends \Magento\Sales\Model\Order\Pdf\Items\Invoice\DefaultInvoice
{
    
    /**
     * Draw item line
     *
     * @return void
     */
    public function draw()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $scopeConfig = $objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $status = $scopeConfig->getValue(
        "gst/codilar/status",
        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if(!$status){
            parent::draw();
        }
        else{
            $productionState = $scopeConfig->getValue('gst/codilar/production_state', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $productionState = str_replace(' ', '', strtolower($productionState));
            $order = $this->getOrder();
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
            $item = $this->getItem();
            $productFactory = $objectManager->create('Magento\Catalog\Model\Product');
            $productData = $productFactory->load($item->getProductId());
            $productHsn = $productData->getHsn();
            $pdf = $this->getPdf();
            $page = $this->getPage();
            $lines = [];

            // draw Product name
            $lines[0] = [['text' => $this->string->split($item->getName(), 35, true, true), 'feed' => 35]];

            // draw SKU
            $lines[0][] = [
                'text' => $this->string->split($this->getSku($item), 17),
                'feed' => 290,
                'align' => 'right',
            ];
            // draw HSN
            $lines[0][] = [
                'text' => $productHsn,
                'feed' => 325,
                'align' => 'right',
            ];

            // draw QTY
            $lines[0][] = ['text' => $item->getQty() * 1, 'feed' => 510, 'align' => 'right'];

            // draw item Prices
            $i = 0;
            $prices = $this->getItemPricesForDisplay();
            $feedPrice = 380;
            $feedSubtotal = 565;
            foreach ($prices as $priceData) {
                if (isset($priceData['label'])) {
                    // draw Price label
                    $lines[$i][] = ['text' => $priceData['label'], 'feed' => $feedPrice, 'align' => 'right'];
                    // draw Subtotal label
                    $lines[$i][] = ['text' => $priceData['label'], 'feed' => $feedSubtotal, 'align' => 'right'];
                    $i++;
                }
                // draw Price
                $lines[$i][] = [
                    'text' => $priceData['price'],
                    'feed' => $feedPrice,
                    'font' => 'bold',
                    'align' => 'right',
                ];
                // draw Subtotal
                $lines[$i][] = [
                    'text' => $priceData['subtotal'],
                    'feed' => $feedSubtotal,
                    'font' => 'bold',
                    'align' => 'right',
                ];
                $i++;
            }

            if(($state==$productionState) && $gstOrderStatus){
                $sgst_percent = $item->getOrderItem()->getTaxPercent()/2;
                $stateTax = $item->getTaxAmount() / 2;
                    $data = $order->formatPriceTxt($stateTax);
                $per =  "(" . number_format($sgst_percent, 2, ".", " ") . "%" . ")";
                $total_sgst = $this->splitString($data, $per);
                // draw SGST
                $lines[0][] = [
                    'text' => $total_sgst,
                    'feed' => 430,
                    'font' => 'bold',
                    'align' => 'right',
                ];

                // draw CGST
                $lines[0][] = [
                    'text' => $total_sgst,
                    'feed' => 480,
                    'font' => 'bold',
                    'align' => 'right',
                ];
            }
            else{
                $igst_amount = $item->getTaxAmount();
                $igst_percent = $item->getOrderItem()->getTaxPercent();
                $data = $order->formatPriceTxt($igst_amount);
                $per =  "(" . number_format($igst_percent, 2, ".", " ") . "%" . ")";
                $total_igst = $this->splitString($data, $per);
                // draw Tax/IGST
                $lines[0][] = [
                    'text' => $total_igst,
                    'feed' => 450,
                    'font' => 'bold',
                    'align' => 'right',
                ];
            }

            // custom options
            $options = $this->getItemOptions();
            if ($options) {
                foreach ($options as $option) {
                    // draw options label
                    $lines[][] = [
                        'text' => $this->string->split($this->filterManager->stripTags($option['label']), 40, true, true),
                        'font' => 'italic',
                        'feed' => 35,
                    ];

                    if ($option['value']) {
                        if (isset($option['print_value'])) {
                            $printValue = $option['print_value'];
                        } else {
                            $printValue = $this->filterManager->stripTags($option['value']);
                        }
                        $values = explode(', ', $printValue);
                        foreach ($values as $value) {
                            $lines[][] = ['text' => $this->string->split($value, 30, true, true), 'feed' => 40];
                        }
                    }
                }
            }

            $lineBlock = ['lines' => $lines, 'height' => 20];

            $page = $pdf->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
            $this->setPage($page);
        }

    }

    /**
     * @param $data
     * @param $per
     * @return array
     */
    public function splitString($data, $per)
    {
        if (strlen($data) > strlen($per)) {
            $total_cgst = str_split($data . $per, strlen($data));
            return $total_cgst;
        } else {
            for ($i = strlen($data); $i < strlen($per); $i++) {
                $data = $data . " ";
            }
            $total_cgst = str_split($data . $per, strlen($per));
            return $total_cgst;
        }
    }
}
