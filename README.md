# Magento 2 Inpost Paczkomaty. Magento Ver. 2.3-2.4

Moduł paczkomatów inpost 
Posiada podstawowe ustawienia, wykorzystuje widget mapy inpostu. Wymagane klucz google Maps.


Kontakt: 
https://mazyl.pl

mazyl@wp.pl


    ``mazyl/module-inpost``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities


## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Mazyl/Inpost`
 - Enable the module by running `php bin/magento module:enable Mazyl_Inpost`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require mazyl/module-inpost`
 - enable the module by running `php bin/magento module:enable Mazyl_Inpost`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration

 - InpostPaczkomaty - carriers/inpostpaczkomaty/*

 - InpostPaczkomatyPobranie - carriers/inpostpaczkomatypobranie/*

 - InpostPaczkomatyKurier - carriers/inpostpaczkomatykurier/*


## Specifications

 - Cronjob
	- mazyl_inpost_updateinpost

 - Shipping Method
	- inpostpaczkomaty

 - Shipping Method
	- inpostpaczkomatypobranie

 - Shipping Method
	- inpostpaczkomatykurier

 - Observer
	- sales_model_service_quote_submit_before > Mazyl\Inpost\Observer\Sales\ModelServiceQuoteSubmitBefore

 - Controller
	- adminhtml > inpost/packages/index

 - Controller
	- adminhtml > inpost/packages/edit


## Attributes



