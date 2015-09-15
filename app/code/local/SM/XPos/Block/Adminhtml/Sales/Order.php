<?php

/**
 * Created by PhpStorm.
 * User: SMART
 * Date: 9/14/2015
 * Time: 1:56 PM
 */
class SM_XPos_Block_Adminhtml_Sales_Order extends Mage_Adminhtml_Block_Sales_Order
{
    public function __construct()
    {
        $this->_controller = 'sales_order';
        $this->_headerText = Mage::helper('sales')->__('Orders');
        parent::__construct();
        $this->_removeButton('add');
    }
}