<?php

/**
 * Created by PhpStorm.
 * User: SMART
 * Date: 9/16/2015
 * Time: 5:34 PM
 */
class SM_XPos_Model_Adminhtml_Sales_Order_Create extends Mage_Adminhtml_Model_Sales_Order_Create
{
    public function updateQuoteItems($data)
    {
        if (is_array($data)) {
            try {
                foreach ($data as $itemId => $info) {
                    if (!empty($info['configured'])) {
                        $item = $this->getQuote()->updateItem($itemId, new Varien_Object($info));
                        $itemQty = (float)$item->getQty();
                    } else {
                        $item = $this->getQuote()->getItemById($itemId);
                        $itemQty = (float)$info['qty'];
                    }

                    if ($item) {
                        if ($item->getProduct()->getStockItem()) {
                            if (!$item->getProduct()->getStockItem()->getIsQtyDecimal()) {
                                $itemQty = (int)$itemQty;
                            } else {
                                $item->setIsQtyDecimal(1);
                            }
                        }

                        //$itemQty    = $itemQty > 0 ? $itemQty : 1;
                        if ($itemQty > 0) {
                            if (isset($info['custom_price'])) {
                                $itemPrice = $this->_parseCustomPrice($info['custom_price']);
                            } else {
                                $itemPrice = null;
                            }
                            $noDiscount = !isset($info['use_discount']);

                            if (empty($info['action']) || !empty($info['configured'])) {
                                $item->setQty($itemQty);
                                $item->setCustomPrice($itemPrice);
                                $item->setOriginalCustomPrice($itemPrice);
                                $item->setNoDiscount($noDiscount);
                                $item->getProduct()->setIsSuperMode(true);
                                $item->getProduct()->unsSkipCheckRequiredOption();
                                $item->checkData();
                            } else {
                                $this->moveQuoteItem($item->getId(), $info['action'], $itemQty);
                            }
                        } else {
                            $this->getQuote()->removeItem($item->getId());
                        }
                    } else {
                        try {
                            $this->addProduct($itemId, $info);
                        } catch (Mage_Core_Exception $e) {
                            $this->getSession()->addError($e->getMessage());
                        } catch (Exception $e) {
                            return $e;
                        }
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $this->recollectCart();
                throw $e;
            } catch (Exception $e) {
                Mage::logException($e);
            }
            $this->recollectCart();
        }
        return $this;
    }
}