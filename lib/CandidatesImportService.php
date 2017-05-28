<?php

include_once(LEGACY_ROOT . '/lib/ImportService.php');

class CandidatesImportService extends ImportService
{
    public function __construct($siteID)
    {
        parent::__construct($siteID);
    }

    public function getInsertQuery($columns, $values, $userID, $importID)
    {
        return sprintf(
            "INSERT INTO candidate (
                %s,
                can_relocate,
                entered_by,
                owner,
                site_id,
                date_created,
                date_modified,
                import_id
            )
            VALUES (
                %s,
                %s,
                %s,
                %s,
                %s,
                NOW(),
                NOW(),
                %s
            )",
            implode(",\n", $columns),
            implode(",\n", $values),
            0,
            $userID,
            $userID,
            $this->_siteID,
            $importID
        );
    }
}