<?php

/**
 * Created by PhpStorm.
 * User: SMART
 * Date: 9/11/2015
 * Time: 4:23 PM
 */
class SM_XPos_Adminhtml_XPosController extends Mage_Adminhtml_Sales_Order_CreateController
{
    public $storeId;


    public function indexAction()
    {
        $this->loadLayout()->renderLayout();
    }

    protected function _initProduct()
    {
        $productId = (int)$this->getRequest()->getParam('productId');
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }

    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    public function saveOrderAction()
    {

        $onePage = Mage::getSingleton('checkout/type_onepage');
        $onePage->saveOrder();
    }

    public function addProductAction()
    {
        Mage::app()->setCurrentStore($this->getStore());
        if (!$this->_validateFormKey()) {
            return;
        }
        $cart = $this->_getCart();
        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();

            /**
             * Check product availability
             */
            if (!$product) {
                return;
            }
            $cart->addProduct($product, $params);
            $cart->save();

        } catch (Mage_Core_Exception $e) {

        } catch (Exception $e) {
        }
        $cart = Mage::getModel('checkout/cart')->getQuote();
        $block = $this->getLayout()->createBlock('adminhtml/template')
            ->setTemplate('sm/xpos/sales/order/create/items/item.phtml')
            ->assign('items', $cart);
        $this->getResponse()->setBody($block->toHtml());

    }

    public function cancelOrderAction()
    {
        Mage::app()->setCurrentStore($this->getStore());
        try {
            $this->_getCart()->truncate()->save();
            $this->_getSession()->setCartWasUpdated(true);
        } catch (Mage_Core_Exception $exception) {
            $this->_getSession()->addError($exception->getMessage());
        } catch (Exception $exception) {
            $this->_getSession()->addException($exception, $this->__('Cannot update shopping cart.'));
        }
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
        Mage::app()->setCurrentStore($store);

        $this->getResponse()->setBody(($this->getStore()));
    }

    public function setCustomerAction()
    {
        Mage::app()->setCurrentStore($this->getStore());
        $customerId = $this->getRequest()->getParam('customerId');
        $customer=Mage::getSingleton('customer/customer')->load($customerId);
        $this->_getSession()->setCustomerId($customerId);
        $quote = Mage::getModel('checkout/cart')->getQuote();
        $quote->setCustomer($customer);
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