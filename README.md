# Free Magento 2 GST Extension for India

[![Codilar](https://www.codilar.com/codilar-logo.png)](https://www.codilar.com/)

## Introduction

This Magento GST extension can help to easily configure Magento stores with GST rules according to Indian government regulations. GST splits – CGST, SGST and IGST in various formats including invoices, PDFs, orders, and reports can be generated.  

Our extension works perfectly for all e-commerce transactions across various product types and categories. Setting up GST with different tax slabs in online stores can be easily done using Magento's taxation architecture of tax classes, rates, and zones.

### Significant features of Magento GST extension

 - Admins can configure GST number to specific stores. 
 - Invoices and order templates that comply with GST standards are updated.
 - Respective GST number will be attached to all invoice and order emails. 
 - For clear display of SGST, CGST and IGST, separate columns are used. 
 - SGCT, CGST and IGST will be calculated and added to all corresponding transactional emails and PDFs automatically.
 - In admin panel and order PDF, GST columns for order email are added according to the standard format issued by Government of India.
 - Optional buyer level GSTIN and an option to display HSN value in invoice are included. 
 - In all transactional emails and PDFs, the word “TAX” can be replaced with the word “GST”.

### Magento Supported Versions
> Magento 2.0.x

> Magento 2.1.x

> Magento 2.2.x

> Magento 2.3.x


## Installation

### Install with Composer

    $ cd <Magento-root-directory>
    $ composer require codilar/gst
    $ bin/magento setup:upgrade
    $ bin/magento setup:static-content:deploy -f

> Fix file permission and owner if required

> Navigate to Magento admin → Stores → Configuration → Codilar → GST to
> make sure GST extension is installed properly.


### Manual Installation

Download the lastest package from the [release](https://github.com/Codilar/magento-2-gst/releases)
Extract the downloaded package to Magento-root-folder/app/code/Codilar/Gst
Run the following command

    $ bin/magento setup:upgrade
    $ bin/magento setup:static-content:deploy -f

> Navigate to Magento admin → Stores → Configuration → Codilar → GST to
> make sure GST extension is installed properly.

For more details and user guide visit [Codilar Magento GST Extensions](https://www.codilar.com/magento-gst-extension/)

