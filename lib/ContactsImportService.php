<?php

include_once(LEGACY_ROOT . '/lib/ImportService.php');
use \OpenCATS\Entity\Contact;
use \OpenCATS\Entity\ContactRepository;
use \OpenCATS\Exception\ImportServiceException;
use \OpenCATS\Exception\RepositoryException;

class ContactImportService
{
    private $siteID;
    private $contactRepository;

    public function __construct($siteId, ContactRepository $contactRepository)
    {
        $this->siteID = $siteId;
        $this->contactRepository = $contactRepository;
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
        return $contactId;
    }

}