<?php
namespace MyVat\OrdersExporter\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory as StatusCollectionFactory;
use Magento\Directory\Model\Config\Source\Country as CountrySource;
use Magento\Framework\Data\Form\FormKey;

class Export extends Template
{
    protected $statusCollectionFactory;
    protected $countrySource;
    protected $formKey;

    public function __construct(
        Context $context,
        StatusCollectionFactory $statusCollectionFactory,
        CountrySource $countrySource,
        FormKey $formKey,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->statusCollectionFactory = $statusCollectionFactory;
        $this->countrySource = $countrySource;
        $this->formKey = $formKey;
    }

    public function getOrderStatuses()
    {
        return $this->statusCollectionFactory->create()->toOptionArray();
    }

    public function getEuropeanCountries()
    {
        $europeanCountries = ['AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK'];
        $countries = $this->countrySource->toOptionArray();
        return array_filter($countries, function ($country) use ($europeanCountries) {
            return in_array($country['value'], $europeanCountries);
        });
    }

    public function getMonths()
    {
        return [
            ['value' => '01', 'label' => __('January')],
            ['value' => '02', 'label' => __('February')],
            ['value' => '03', 'label' => __('March')],
            ['value' => '04', 'label' => __('April')],
            ['value' => '05', 'label' => __('May')],
            ['value' => '06', 'label' => __('June')],
            ['value' => '07', 'label' => __('July')],
            ['value' => '08', 'label' => __('August')],
            ['value' => '09', 'label' => __('September')],
            ['value' => '10', 'label' => __('October')],
            ['value' => '11', 'label' => __('November')],
            ['value' => '12', 'label' => __('December')],
        ];
    }

    public function getYears()
    {
        $currentYear = (int)date('Y');
        $years = [];
        for ($i = $currentYear; $i >= $currentYear - 10; $i--) {
            $years[] = ['value' => (string)$i, 'label' => (string)$i];
        }
        return $years;
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}