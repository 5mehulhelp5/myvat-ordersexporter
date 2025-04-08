<?php
namespace MyVat\OrdersExporter\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\View\Result\PageFactory;

class Export extends Action
{
    protected $orderCollectionFactory;
    protected $fileFactory;
    protected $directoryList;
    protected $file;
    protected $resultPageFactory;

    public function __construct(
        Context $context,
        OrderCollectionFactory $orderCollectionFactory,
        FileFactory $fileFactory,
        DirectoryList $directoryList,
        File $file,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->fileFactory = $fileFactory;
        $this->directoryList = $directoryList;
        $this->file = $file;
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        try {
            $month = $this->getRequest()->getParam('month');
            $year = $this->getRequest()->getParam('year');
            $countries = $this->getRequest()->getParam('countries');

            $fromDate = $year . '-' . $month . '-01 00:00:00';
            $toDate = date('Y-m-t 23:59:59', strtotime($fromDate));

            $orders = $this->orderCollectionFactory->create()
                ->addFieldToFilter('created_at', ['from' => $fromDate, 'to' => $toDate])
                ->addFieldToFilter('status', ['in' => ['processing', 'complete']]);

            $orders->getSelect()->join(
                ['shipping_address' => $orders->getTable('sales_order_address')],
                'main_table.entity_id = shipping_address.parent_id AND shipping_address.address_type = "shipping"',
                []
            )->where('shipping_address.country_id IN (?)', $countries);

            $countryNames = $this->getCountryNames();

            $data = [];
            foreach ($orders as $order) {
                $shippingAddress = $order->getShippingAddress();
                if ($shippingAddress && in_array($shippingAddress->getCountryId(), $countries)) {
                    $taxPercent = $this->getOrderTaxPercent($order);

                    $countryCode = $shippingAddress->getCountryId();
                    $countryName = isset($countryNames[$countryCode]) ? $countryNames[$countryCode] : $countryCode;

                    $grossSaleAmount = $order->getGrandTotal() - $order->getShippingAmount();

                    $data[] = [
                        'Order Number' => $order->getIncrementId(), // Order number
                        'Country' => $countryName,
                        'Sale Date' => date('d/m/Y', strtotime($order->getCreatedAt())),
                        'Sale Currency' => $order->getOrderCurrencyCode(),
                        'Gross Sale Amount in Sale Currency' => $grossSaleAmount,
                        'VAT RATE (%)' => $taxPercent . '%',
                    ];
                }
            }

            if (empty($data)) {
                throw new LocalizedException(__('No orders found for the specified criteria.'));
            }

            $directory = $this->directoryList->getPath(DirectoryList::VAR_DIR) . '/importexport/';
            $this->file->checkAndCreateFolder($directory);

            $fileName = 'myvat-ordersexport-' . $year . '-' . $month . '.csv';
            $filePath = $directory . $fileName;

            $file = fopen($filePath, 'w');
            fputcsv($file, array_keys($data[0]));
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            fclose($file);

            return $this->fileFactory->create(
                $fileName,
                [
                    'type' => 'filename',
                    'value' => $filePath,
                    'rm' => true, // Remove file after download
                ],
                DirectoryList::VAR_DIR,
                'application/octet-stream'
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error occurred while exporting orders: ') . $e->getMessage());

            $resultPage = $this->resultPageFactory->create();
            $resultPage->setActiveMenu('MyVat_OrdersExporter::main_menu');
            $resultPage->getConfig()->getTitle()->prepend(__('MyVat Orders Exporter'));

            return $resultPage;
        }
    }

    private function getOrderTaxPercent($order)
    {
        foreach ($order->getAllItems() as $item) {
            $taxPercent = $item->getTaxPercent();
            if ($taxPercent > 0) {
                return round($taxPercent, 2);
            }
        }
        return 0;
    }

    private function getCountryNames()
    {
        return [
            'AT' => 'Austria',
            'BE' => 'Belgium',
            'BG' => 'Bulgaria',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DE' => 'Germany',
            'DK' => 'Denmark',
            'EE' => 'Estonia',
            'ES' => 'Spain',
            'FI' => 'Finland',
            'FR' => 'France',
            'GR' => 'Greece',
            'HR' => 'Croatia',
            'HU' => 'Hungary',
            'IE' => 'Ireland',
            'IT' => 'Italy',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'LV' => 'Latvia',
            'MT' => 'Malta',
            'NL' => 'Netherlands',
            'PL' => 'Poland',
            'PT' => 'Portugal',
            'RO' => 'Romania',
            'SE' => 'Sweden',
            'SI' => 'Slovenia',
            'SK' => 'Slovakia'
        ];
    }
}
