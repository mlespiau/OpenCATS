<?php

include_once(LEGACY_ROOT . '/lib/ImportService.php');
use \OpenCATS\Entity\Contact;
use \OpenCATS\Entity\ContactRepository;
use \OpenCATS\Entity\ExtraFieldRepository;
use \OpenCATS\Exception\ImportServiceException;
use \OpenCATS\Exception\RepositoryException;

class ContactImportService
{
    private $siteID;
    private $contactRepository;
    private $extraFieldRepository;

    public function __construct($siteId, ContactRepository $contactRepository, ExtraFieldRepository $extraFieldRepository)
    {
        $this->siteID = $siteId;
        $this->contactRepository = $contactRepository;
        $this->extraFieldRepository = $extraFieldRepository;
    }

    public function add(Contact $contact)
    {
        if (!eval(Hooks::get('IMPORT_ADD_CONTACT'))) return;
        try {
            $contactId = $this->contactRepository->persist($contact, new History($this->siteID));
        } catch(RepositoryException $e) {
            throw new ImportServiceException('Failed to add contact');
        }
        $contact->setId($contactId);
        $this->persistExtraFields($contact);
        return $contactId;
    }

    /**
     * @param Contact $contact
     */
    private function persistExtraFields(Contact $contact)
    {
        if (!empty($contact->getExtraFields())) {
            foreach ($contact->getExtraFields() as $extraField) {
                $extraField->setDataItemId($contact->getId());
            }
            $this->extraFieldRepository->persistMultiple($contact->getExtraFields());
        }
    }
}