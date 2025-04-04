# MyVat Orders Exporter v1.0.0

## Overview

The MyVat Orders Exporter plugin allows you to easily export EU orders from Magento 2. This tool is designed to help you generate a CSV file that can be imported into MyVat's MyVAT Import OSS Sales sheet.

## Features

- Export EU orders to CSV format.
- Filter orders by month and year.
- Optionally filter orders by specific EU countries (all EU countries are selected by default).

## How to Export Orders

1. Log into the Magento 2 backend.
2. Navigate to **System > Data Transfer > MyVat Orders Exporter**.
3. Select the preferred month and year. Optionally, you can select specific countries, but by default, all EU countries are selected.
4. Click **Export Orders**. The CSV file will be automatically downloaded.

## Installation

### Recommended Method: Composer

The recommended way to install the MyVat Orders Exporter plugin is through Composer. Run the following command in your Magento 2 root directory:

```bash
composer require git-seb/myvat-ordersexporter
```

After installing the plugin, run the following commands:

```bash
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
```

Clear the cache:

```bash
php bin/magento cache:clean
php bin/magento cache:flush
```

### Alternative Method: Upload to /app/code/

1. Download the plugin package.
2. Extract the package and upload the contents to the `app/code/MyVat/OrdersExporter` directory of your Magento 2 installation.
3. Run the following commands in your Magento 2 root directory:

```bash
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
```

Clear the cache:

```bash
php bin/magento cache:clean
php bin/magento cache:flush
```

## Support

This plugin is provided as-is without any support. Improvements are always welcome.

## License

This project is licensed under the MIT License.
