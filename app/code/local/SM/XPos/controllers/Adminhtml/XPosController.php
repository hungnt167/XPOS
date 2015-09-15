<?php

/**
 * Created by PhpStorm.
 * User: SMART
 * Date: 9/11/2015
 * Time: 4:23 PM
 */
class SM_XPos_Adminhtml_XPosController extends Mage_Adminhtml_Controller_Action
{
    public $storeId;

    public function _initAction()
    {

    }

    public function indexAction()
    {
        $this->loadLayout()->renderLayout();
    }


    public function searchCustomerAction()
    {
        $query = $this->getRequest()->getParam('query');
        $model = Mage::getModel('xpos/adminhtml_search_customer');
        $customer = $model->search($query);
        $jsonData = Mage::helper('core')->jsonEncode($customer);
        $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(($jsonData));

    }

    public function searchProductAction()
    {
        $query = $this->getRequest()->getParam('query');
        $limit = 9;
        $storeId = $this->getStore();

        $model = Mage::getModel('xpos/adminhtml_search_product');
        $products = $model->search($query, $storeId, $limit);
        $block = $this->getLayout()->createBlock('adminhtml/template')
            ->setTemplate('sm/xpos/sales/order/create/products/grid.phtml')
            ->assign('products', $products);

        $this->getResponse()->setBody($block->toHtml());

    }

    public function setStoreAction()
    {
        $store = $this->getRequest()->getParam('storeId');
        $this->_getSession()->setStoreId($store);
        $this->getResponse()->setBody(($this->getStore()));
    }

    public function setCustomerAction()
    {
        $store = $this->getRequest()->getParam('customerId');
        $this->_getSession()->setCustomerId($store);
        $this->getResponse()->setBody(($this->getCustomer()));
    }

    public function getStore()
    {
        return $this->_getSession()->getStoreId();
    }

    public function getCustomer()
    {
        return $this->_getSession()->getCustomerId();
    }
}