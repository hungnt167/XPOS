<?php

/**
 * Created by PhpStorm.
 * User: SMART
 * Date: 9/15/2015
 * Time: 11:30 AM
 */
class SM_XPos_Model_Adminhtml_Search_Customer
{
    public function search($query,$limit=10)
    {
        $arr = array();

        $collection = Mage::getResourceModel('customer/customer_collection')
            ->addNameToSelect()
            ->addAttributeToSelect(array('firstname','lastname','telephone','email'))
            ->joinAttribute('telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->addAttributeToFilter(array(
                array('attribute'=>'firstname', 'like'=>$query.'%'),
                array('attribute'=>'lastname', 'like'=>$query.'%'),
                array('attribute'=>'telephone', 'like'=>$query.'%'),
                array('attribute'=>'email', 'like'=>'%'.$query.'%'),
            ))
            ->setPage(1, $limit)
            ->load();
        foreach ($collection->getItems() as $k=> $customer) {
            $arr[] = array(
                'value'            => $customer->getId(),
                'label'          => $customer->getName(),
            );
        }

        return $arr;
    }
}