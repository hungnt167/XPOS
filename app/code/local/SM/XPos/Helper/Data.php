<?php

/**
 * Created by PhpStorm.
 * User: SMART
 * Date: 9/11/2015
 * Time: 4:54 PM
 */
class SM_XPos_Helper_Data extends  Mage_Core_Helper_Abstract
{
    public function getAllStore(){
        $allStore=array();
        foreach (Mage::app()->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {
                    $allStore[]=$store->getData();
                }
            }
        }
        return $allStore;
    }
}