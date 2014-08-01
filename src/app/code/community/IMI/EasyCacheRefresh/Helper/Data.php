<?php
/**
 * iMi Magento Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject of iMi.
 * You may not be allowed to change the sources
 * without authorization of iMi digital GmbH.
 *
 * @copyright  Copyright (c) 2014 iMi digital GmbH (http://www.iMi.de)
 * @author iMi digital GmbH <info@iMi.de>
 * @license OSL
 * @category IMI
 * @package IMI_EasyCacheRefresh
 */

class IMI_EasyCacheRefresh_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Do we only have the limited cache management?
     *
     * @return bool
     */
    public function isLimited()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/limited_cache') &&
            !Mage::getSingleton('admin/session')->isAllowed('system/cache');
    }


} 