<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  2011-2015 ESS-UA [M2E Pro]
 * @license    Commercial use is forbidden
 */

namespace Ess\M2ePro\Block\Adminhtml\ControlPanel\Inspection;

class DatabaseBrokenTables extends AbstractInspection
{
    public $emptyTables        = array();
    public $notInstalledTables = array();
    public $crashedTables      = array();

    //########################################

    public function _construct()
    {
        parent::_construct();

        // Initialization block
        // ---------------------------------------
        $this->setId('controlPanelInspectionDatabaseBrokenTables');
        // ---------------------------------------

        $this->setTemplate('control_panel/inspection/databaseBrokenTables.phtml');

        $this->prepareTablesInfo();
    }

    //########################################

    public function isShown()
    {
        return !empty($this->emptyTables) ||
               !empty($this->notInstalledTables) ||
               !empty($this->crashedTables);
    }

    //########################################

    private function prepareTablesInfo()
    {
        $this->emptyTables        = $this->getEmptyTables();
        $this->notInstalledTables = $this->getNotInstalledTables();
        $this->crashedTables      = $this->getCrashedTables();
    }

    //########################################

    private function getEmptyTables()
    {
        $helper = $this->getHelper('Module\Database\Structure');

        $emptyTables = array();
        foreach ($this->getGeneralTables() as $table) {

            if (!$helper->isTableReady($table)) {
                continue;
            }

            !$helper->getCountOfRecords($table) && $emptyTables[] = $table;
        }

        return $emptyTables;
    }

    private function getNotInstalledTables()
    {
        $helper = $this->getHelper('Module\Database\Structure');

        $notInstalledTables = array();
        foreach ($helper->getMySqlTables() as $tableName) {
            !$helper->isTableExists($tableName) && $notInstalledTables[] = $tableName;
        }

        return $notInstalledTables;
    }

    private function getCrashedTables()
    {
        $helper = $this->getHelper('Module\Database\Structure');

        $crashedTables = array();
        foreach ($helper->getMySqlTables() as $tableName) {

            if (!$helper->isTableExists($tableName)) {
                continue;
            }

            !$helper->isTableStatusOk($tableName) && $crashedTables[] = $tableName;
        }

        return $crashedTables;
    }

    //########################################

    private function getGeneralTables()
    {
        return array(
            'm2epro_primary_config',
            'm2epro_module_config',
            'm2epro_synchronization_config',
            'm2epro_wizard',
            'm2epro_marketplace',
            'm2epro_amazon_marketplace',
            'm2epro_ebay_marketplace',
        );
    }

    //########################################
}