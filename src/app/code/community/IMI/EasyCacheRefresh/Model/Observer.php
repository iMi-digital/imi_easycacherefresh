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

class IMI_EasyCacheRefresh_Model_Observer
{

    /**
     * Rewrite Permissions for the cache management menu entry
     * if we have only limited cache control (menu entry to be shown)
     */
    public function adminhtmlBlockHtmlBefore(Varien_Event_Observer $observer)
    {
        if (!($observer->getBlock() instanceof Mage_Adminhtml_Block_Page_Menu)) {
            return;
        }

        $config = Mage::getSingleton('admin/config')->getAdminhtmlConfig();
        $menu = $config->getNode('menu');

        if (!Mage::getSingleton('admin/session')->isAllowed('system/cache')) {
            $menu->system->children->cache->addChild('resource', 'system/limited_cache');
        }
    }

    /**
     * Gateway method for customizations / remove additional block
     *
     * @param Varien_Event_Observer $observer
     * @throws Exception
     */
    public function coreBlockAbstractToHtmlBefore(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();
        $fullAction = $block->getRequest()->getModuleName() . '_'
            . $block->getRequest()->getControllerName() . '_'
            . $block->getRequest()->getActionName();
        if ($fullAction != 'admin_cache_index') {
            return;
        }
        if ($block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction) {
            $this->_customizeGrid($block);
        } elseif ($block instanceof Mage_Adminhtml_Block_Cache) {
            $this->_customizeMainBlock($block);
        } elseif ($block instanceof Mage_Adminhtml_Block_page) {
            if (Mage::helper('imi_easycacherefresh')->isLimited()) {
                $block->getChild('content')->unsetChild('cache.additional');
            }
        }
    }

    /**
     * Remove en-/disable caches
     *
     * @param Mage_Adminhtml_Block_Cache_Grid $block
     */
    protected function _customizeGrid(Mage_Adminhtml_Block_Widget_Grid_Massaction $block)
    {
        if (Mage::helper('imi_easycacherefresh')->isLimited()) {
            $block->removeItem('enable');
            $block->removeItem('disable');
        }
    }

    /**
     * Remove buttons if limited; add easy refresh button
     *
     * @param Mage_Adminhtml_Block_Cache $block
     */
    protected function _customizeMainBlock(Mage_Adminhtml_Block_Cache $block)
    {
        /* TEMP DISABLED if (Mage::helper('imi_easycacherefresh')->isLimited()) {
            $block->removeButton('flush_magento');
            $block->removeButton('flush_system');
        }*/

        $block->addButton('refresh_invalidated', array(
            'label'     => Mage::helper('imi_easycacherefresh')->__('Refresh Invalidated'),
            'onclick'   => 'setLocation(\'' . $block->getUrl('*/*/refreshInvalidated') .'\')',
            'class'     => 'delete',
        ));
    }
}