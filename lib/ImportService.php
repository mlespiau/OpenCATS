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

    public function prepareData($dataNamed, $encoding)
    {
        $dataColumns = array();
        $data = array();

        foreach ($dataNamed AS $dataColumn => $d) {
            $dataColumns[] = $dataColumn;
            if ($encoding != "") {
                $data[] = iconv($encoding, 'UTF-8', $this->_db->makeQueryStringOrNULL($d));
            } else {
                $data[] = $this->_db->makeQueryStringOrNULL($d);
            }
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
    public function add($dataNamed, $userID, $importID, $encoding)
    {
        $data = $this->prepareData($dataNamed, $encoding);
        $sql = $this->getInsertQuery($data['dataColumuns'], $data['data'], $userID, $importID);
        $queryResult = $this->_db->query($sql);
        if (!$queryResult)
        {
            return -1;
        }
        return $this->_db->getLastInsertID();
    }

}

