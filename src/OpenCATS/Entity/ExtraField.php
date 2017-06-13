<?php
namespace OpenCATS\Entity;

class ExtraField
{
    private $siteId;
    private $dataItemType;
    private $dataItemId;
    private $fieldName;
    private $value;
    private $importId;

    /**
     * ExtraField constructor.
     * @param $siteId
     * @param $dataItemType
     * @param $dataItemId
     * @param $fieldName
     * @param $value
     * @param $importId
     */
    public function __construct($siteId, $dataItemType, $dataItemId, $fieldName, $value, $importId)
    {
        $this->siteId = $siteId;
        $this->dataItemType = $dataItemType;
        $this->dataItemId = $dataItemId;
        $this->fieldName = $fieldName;
        $this->value = $value;
        $this->importId = $importId;
    }

    /**
     * @return mixed
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * @return mixed
     */
    public function getDataItemType()
    {
        return $this->dataItemType;
    }

    /**
     * @return mixed
     */
    public function getDataItemId()
    {
        return $this->dataItemId;
    }

    /**
     * @return mixed
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function getImportId()
    {
        return $this->importId;
    }

    public function toArray()
    {
        return array(
            'siteId' => $this->siteId,
            'dataItemType' => $this->dataItemType,
            'dataItemId' => $this->dataItemId,
            'fieldName' => $this->fieldName,
            'value' => $this->value,
            'importId' => $this->importId
        );
    }

}