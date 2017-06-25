<?php
namespace OpenCATS\Entity;
use OpenCATS\Entity\Company;
use OpenCATS\Exception\RepositoryException;

include_once(LEGACY_ROOT . '/lib/History.php');

class CompanyRepository
{
    private $databaseConnection;
    
    function __construct(\DatabaseConnection $databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }
    
    function persist(Company $company, \History $history)
    {
        $sql = sprintf(
            "INSERT INTO company (
                name,
                address,
                city,
                state,
                zip,
                phone1,
                phone2,
                fax_number,
                url,
                key_technologies,
                is_hot,
                notes,
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
                %s,
                %s,
                %s,
                %s,
                %s,
                %s,
                %s,
                %s,
                %s,
                %s,
                NOW(),
                NOW(),
                %s
            )",
            $this->databaseConnection->makeQueryString($company->getName()),
            $this->databaseConnection->makeQueryString($company->getAddress()),
            $this->databaseConnection->makeQueryString($company->getCity()),
            $this->databaseConnection->makeQueryString($company->getState()),
            $this->databaseConnection->makeQueryString($company->getZipCode()),
            $this->databaseConnection->makeQueryString($company->getPhoneNumberOne()),
            $this->databaseConnection->makeQueryString($company->getPhoneNumberTwo()),
            $this->databaseConnection->makeQueryString($company->getFaxNumber()),
            $this->databaseConnection->makeQueryString($company->getUrl()),
            $this->databaseConnection->makeQueryString($company->getKeyTechnologies()),
            ($company->isHot() ? '1' : '0'),
            $this->databaseConnection->makeQueryString($company->getNotes()),
            $this->databaseConnection->makeQueryInteger($company->getEnteredBy()),
            $this->databaseConnection->makeQueryInteger($company->getOwner()),
            $company->getSiteId(),
            $this->databaseConnection->makeQueryInteger($company->getImportId())
        );
        if ($result = $this->databaseConnection->query($sql)) {
            $companyId = $this->databaseConnection->getLastInsertID();
            // FIXME: History should be split in HistoryService and History (Entity)
            // Also, the action of saving a history should not be explicitely done 
            // by each Entity Service, but instead, each Entity Service should 
            // dispatch a hook and the History Service should listen to all 
            // hooks and persist the History entities.
            // That way, the code is more mantainable as not all Entities need to
            // be aware of History and vice-versa
            $history->storeHistoryNew(DATA_ITEM_COMPANY, $companyId);
            return $companyId;
        } else {
            throw new RepositoryException('errorPersistingCompany');
        }
    }
    
    // FIXME: Consolidate with Search.php code
    function findByName($siteId, $companyName)
    {
        $wildCardString = str_replace('*', '%', $companyName) . '%';
        $wildCardString = $this->databaseConnection->makeQueryString($wildCardString);
        
        $sql = sprintf(
            "SELECT
                company_id,
                name,
                address,
                city,
                state,
                zip,
                phone1,
                phone2,
                fax_number,
                url,
                key_technologies,
                is_hot,
                notes,
                entered_by,
                date_created,
                date_modified,
                import_id
            FROM
                company
            WHERE
                company.name LIKE %s
            AND
                company.site_id = %s
            ",
            $wildCardString,
            $siteId
        );
        $result = $this->databaseConnection->getAssoc($sql);
        if (empty($result)) {
            throw new RepositoryException('Company named: ' . $companyName . ' does not exist');
        }
        $company = Company::create(
            $siteId,
            $result['name'],
            $result['address'],
            $result['city'],
            $result['state'],
            $result['zip'],
            $result['phone1'],
            $result['phone2'],
            $result['fax_number'],
            $result['url'],
            $result['key_technologies'],
            $result['is_hot'],
            $result['notes'],
            $result['entered_by'],
            $result['owner']
        );
        $company->setId($result['company_id']);
        $company->setImportId($result['import_id']);
        return $company;
    }

    function exists($siteId, $companyName)
    {
        try {
            $this->findByName($siteId, $companyName);
        } catch (RepositoryException $e) {
            return false;
        }
        return true;
    }
}