<?php
namespace MyVat\OrdersExporter\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Index extends Action
{
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('MyVat_OrdersExporter::main_menu');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('MyVat Orders Exporter'));
        $this->_view->renderLayout();
    }
}