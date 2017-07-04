<?php
namespace OpenCATS\Entity;
use OpenCATS\Entity\Contact;
use OpenCATS\Exception\RepositoryException;

include_once(LEGACY_ROOT . '/lib/History.php');

class ContactRepository
{
    private $databaseConnection;
    
    function __construct(\DatabaseConnection $databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }
    
    function persist(Contact $contact, \History $history)
    {
        $sql = $sql = sprintf(
            "INSERT INTO contact (
                company_id,
                site_id,
                last_name,
                first_name,
                title,
                email1,
                email2,
                phone_work,
                phone_cell,
                phone_other,
                address,
                city,
                state,
                zip,
                is_hot,
                notes,
                entered_by,
                owner,
                date_created,
                date_modified,
                left_company,
                import_id,
                company_department_id,
                reports_to
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
                %s,
                %s,
                %s,
                NOW(),
                NOW(),
                %s,
                %s,
                %s,
                %s
            )",
            $this->databaseConnection->makeQueryInteger($contact->getCompanyId()),
            $this->databaseConnection->makeQueryInteger($contact->getSiteId()),
            $this->databaseConnection->makeQueryString($contact->getLastName()),
            $this->databaseConnection->makeQueryString($contact->getFirstName()),
            $this->databaseConnection->makeQueryString($contact->getTitle()),
            $this->databaseConnection->makeQueryString($contact->getEmail1()),
            $this->databaseConnection->makeQueryString($contact->getEmail2()),
            $this->databaseConnection->makeQueryString($contact->getPhoneWork()),
            $this->databaseConnection->makeQueryString($contact->getPhoneCell()),
            $this->databaseConnection->makeQueryString($contact->getPhoneOther()),
            $this->databaseConnection->makeQueryString($contact->getAddress()),
            $this->databaseConnection->makeQueryString($contact->getCity()),
            $this->databaseConnection->makeQueryString($contact->getState()),
            $this->databaseConnection->makeQueryString($contact->getZip()),
            ($contact->isHot() ? '1' : '0'),
            $this->databaseConnection->makeQueryString($contact->getNotes()),
            $this->databaseConnection->makeQueryInteger($contact->getEnteredBy()),
            $this->databaseConnection->makeQueryInteger($contact->getOwner()),
            $this->databaseConnection->makeQueryInteger($contact->getLeftCompany()),
            $this->databaseConnection->makeQueryInteger($contact->getImportId()),
            $this->databaseConnection->makeQueryInteger($contact->getCompanyDepartmentId()),
            $this->databaseConnection->makeQueryString($contact->getReportsTo())
        );
        if ($result = $this->databaseConnection->query($sql)) {
            $contactId = $this->databaseConnection->getLastInsertID();
            // FIXME: History should be split in HistoryService and History (Entity)
            // Also, the action of saving a history should not be explicitely done 
            // by each Entity Service, but instead, each Entity Service should 
            // dispatch a hook and the History Service should listen to all 
            // hooks and persist the History entities.
            // That way, the code is more mantainable as not all Entities need to
            // be aware of History and vice-versa
            $history->storeHistoryNew(DATA_ITEM_CONTACT, $contactId);
            return $contactId;
        } else {
            throw new RepositoryException('errorPersistingContact');
        }
    }
}