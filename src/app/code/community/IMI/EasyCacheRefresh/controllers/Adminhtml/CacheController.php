<?php

/**
 * iMi Magento Module
 *
 * NOTICE OF LICENSE

 * This source file is subject to the Open Software License (OSL 3.0)
 * which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2014 iMi digital GmbH (http://www.iMi.de)
 * @author iMi digital GmbH <info@iMi.de>
 * @license OSL-3.0
 * @category IMI
 * @package IMI_EasyCacheRefresh
 */

require_once 'Mage/Adminhtml/controllers/CacheController.php';


/**
 *
 *
 * @category IMI
 * @package IMI_EasyCacheRefresh
 * @author iMi digital GmbH <info@iMi.de>
 */
class IMI_EasyCacheRefresh_Adminhtml_CacheController extends Mage_Adminhtml_CacheController
{
    /**
     * Refresh all invalidated caches
     */
    public function refreshInvalidatedAction()
    {
        $updatedTypes = 0;
        $types = Mage::app()->getCacheInstance()->getInvalidatedTypes();
        if (!empty($types)) {
            foreach ($types as $type) {
                $tags = Mage::app()->getCacheInstance()->cleanType($type->getId());
                Mage::dispatchEvent('adminhtml_cache_refresh_type', array('type' => $type->getId()));
                $updatedTypes++;
            }
        }
        if ($updatedTypes > 0) {
            $this->_getSession()->addSuccess(Mage::helper('adminhtml')->__("%s cache type(s) refreshed.", $updatedTypes));
        }

        $this->_redirect('*/*');
    }

    /**
     * Do we only have the limited cache management?
     * @return bool
     */
    protected function _isLimited()
    {
        return Mage::helper('imi_easycacherefresh')->isLimited();
    }

    /**
     * Allow certain actions, if imi_easycache ACL present
     * @return bool
     */
    protected function _isAllowed()
    {
        $action = $this->getRequest()->getActionName();

        $limitedActions = array('index' , 'refreshInvalidated', 'massRefresh');
        $limitedAllowed = in_array($action, $limitedActions);
        return parent::_isAllowed() || ($this->_isLimited() && $limitedAllowed);
    }

}