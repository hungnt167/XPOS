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

    public function addProductAction()
    {

//        if (!$this->_validateFormKey()) {
//            return;
//        }
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
            var_dump($cart);die;


            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);


            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
            }
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
                }
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
        }
//        $block = $this->getLayout()->createBlock('adminhtml/template')
//            ->setTemplate('sm/xpos/sales/order/create/items/item.phtml')
//            ->assign('product', $cart);

//        $this->getResponse()->setBody($block->toHtml());
        $this->getResponse()->setBody(var_dump($cart));

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