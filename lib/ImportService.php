<?php

abstract class ImportService
{
    protected $_db;
    protected $_siteID;

    abstract protected function getInsertQuery($dataNamed, $userID, $importID, $encoding);

    public function __construct($siteID)
    {
        $this->_siteID = $siteID;
        $this->_db = DatabaseConnection::getInstance();
    }

    /**
     * Translate encoding of all values in an array from input encoding to UTF-8.
     *
     * @param array (field => value)
     * @param dataNamed
     * @param encoding
     * @return array
     */
    public function prepareData($dataNamed)
    {
        $dataColumns = array();
        $data = array();

        foreach ($dataNamed AS $dataColumn => $d) {
            $dataColumns[] = $dataColumn;
            $data[] = $this->_db->makeQueryStringOrNULL($d);
        }
        return array('data' => $data, 'dataColumns' => $dataColumns);
    }

    /**
     * Adds a record to the entity table.
     *
     * @param array (field => value)
     * @param userID
     * @param importID
     * @param encoding
     * @return entityID
     */
    public function add($dataNamed, $userID, $importID)
    {
        $data = $this->prepareData($dataNamed);
        $sql = $this->getInsertQuery($data['dataColumns'], $data['data'], $userID, $importID);
        $queryResult = $this->_db->query($sql);
        if (!$queryResult)
        {
            return -1;
        }
        return $this->_db->getLastInsertID();
    }

}

