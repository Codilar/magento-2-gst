# Magento 2 GST Extension for India
[![Codilar](https://www.codilar.com/codilar-logo.png)](https://www.codilar.com/)

## Introduction
Magento 2 GST Extension for Indian store owners to manage GST as per government of India rules

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

