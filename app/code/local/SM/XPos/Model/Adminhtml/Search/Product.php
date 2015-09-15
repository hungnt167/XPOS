<?php

/**
 * Created by PhpStorm.
 * User: SMART
 * Date: 9/15/2015
 * Time: 2:53 PM
 */
class SM_XPos_Model_Adminhtml_Search_Product
{
    public function search($query, $storeId, $limit = 10)
    {
        $arr = array();

        $collection = Mage::getModel('catalog/product')->getCollection()
//            ->addNameToSelect()
            ->addAttributeToSelect(array('name', 'sku','price', 'thumbnail'))
//            ->joinAttribute('telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->addStoreFilter($storeId)
            ->addAttributeToFilter(array(
                array('attribute' => 'name', 'like' => $query . '%'),
                array('attribute' => 'sku', 'like' => $query . '%'),
            ))
            ->setPage(1, $limit)
            ->load();
        $helper = Mage::helper('catalog/image');;
        foreach ($collection->getItems() as $k => $product) {
            $image = $helper->init($product, 'thumbnail')->resize(163, 100);
            $arr[] = array(
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'image' => $image,
            );
        }

        return $arr;
    }
}