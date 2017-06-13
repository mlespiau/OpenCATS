<?php

namespace OpenCATS\Entity;
use OpenCATS\Entity\ExtraField;
use OpenCATS\Exception\RepositoryException;

class ExtraFieldRepository
{
    private $databaseConnection;

    public function __construct(\DatabaseConnection $databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }

    public function persistMultiple($extraFields)
    {
        $ar = array();
        foreach ($extraFields AS $extraField)
        {
            if (empty($extraField->getValue())) {
                throw new RepositoryException('emptyValue for field: ' . print_r($extraField->toArray(), true));
            }
            $ar[] = '('
                . $extraField->getDataItemId() . ', '
                . $this->databaseConnection->makeQueryStringOrNULL($extraField->getFieldName()) . ', '
                . $this->databaseConnection->makeQueryStringOrNULL($extraField->getValue()) . ', '
                . $extraField->getImportId() . ','
                . $extraField->getSiteId() . ', '
                . $extraField->getDataItemType()
                . ')';

        }
        $dataS = implode(',', $ar);
        if (!empty($dataS))
        {
            $sql = sprintf(
                "INSERT INTO extra_field (
                    data_item_id,
                    field_name,
                    value,
                    import_id,
                    site_id,
                    data_item_type
                )
                VALUES %s;",
                $dataS
            );
            $queryResult = $this->databaseConnection->query($sql);
            if (!$queryResult)
            {
                return -1;
            }

            return $this->databaseConnection->getLastInsertID();
        }
    }

}