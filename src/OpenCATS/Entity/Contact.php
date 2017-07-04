<?php
namespace OpenCATS\Entity;
use OpenCATS\Entity\EntityException;

class Contact
{
    private $contactId;
    private $companyId;
    private $siteId;
    private $lastName;
    private $firstName;
    private $title;
    private $email1;
    private $email2;
    private $phoneWork;
    private $phoneCell;
    private $phoneOther;
    private $address;
    private $city;
    private $state;
    private $zip;
    private $isHot;
    private $notes;
    private $enteredBy;
    private $owner;
    private $dateCreated;
    private $dateModified;
    private $leftCompany;
    private $importId;
    private $companyDepartmentId;
    private $reportsTo;
    private $id;

    public function __construct($siteId, $companyId, $lastName, $firstName)
    {
        $this->siteId = $siteId;
        $this->companyId = $companyId;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getContactId()
    {
        return $this->contactId;
    }

    /**
     * @param mixed $contactId
     */
    public function setContactId($contactId)
    {
        $this->contactId = $contactId;
    }

    /**
     * @return mixed
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @param mixed $companyId
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * @return mixed
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * @param mixed $siteId
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getEmail1()
    {
        return $this->email1;
    }

    /**
     * @param mixed $email1
     */
    public function setEmail1($email1)
    {
        $this->email1 = $email1;
    }

    /**
     * @return mixed
     */
    public function getEmail2()
    {
        return $this->email2;
    }

    /**
     * @param mixed $email2
     */
    public function setEmail2($email2)
    {
        $this->email2 = $email2;
    }

    /**
     * @return mixed
     */
    public function getPhoneWork()
    {
        return $this->phoneWork;
    }

    /**
     * @param mixed $phoneWork
     */
    public function setPhoneWork($phoneWork)
    {
        $this->phoneWork = $phoneWork;
    }

    /**
     * @return mixed
     */
    public function getPhoneCell()
    {
        return $this->phoneCell;
    }

    /**
     * @param mixed $phoneCell
     */
    public function setPhoneCell($phoneCell)
    {
        $this->phoneCell = $phoneCell;
    }

    /**
     * @return mixed
     */
    public function getPhoneOther()
    {
        return $this->phoneOther;
    }

    /**
     * @param mixed $phoneOther
     */
    public function setPhoneOther($phoneOther)
    {
        $this->phoneOther = $phoneOther;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @param mixed $zip
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * @return mixed
     */
    public function isHot()
    {
        return $this->isHot;
    }

    /**
     * @param mixed $isHot
     */
    public function setIsHot($isHot)
    {
        $this->isHot = $isHot;
    }

    /**
     * @return mixed
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param mixed $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * @return mixed
     */
    public function getEnteredBy()
    {
        return $this->enteredBy;
    }

    /**
     * @param mixed $enteredBy
     */
    public function setEnteredBy($enteredBy)
    {
        $this->enteredBy = $enteredBy;
    }

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param mixed $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return mixed
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * @param mixed $dateModified
     */
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;
    }

    /**
     * @return mixed
     */
    public function getLeftCompany()
    {
        return $this->leftCompany;
    }

    /**
     * @param mixed $leftCompany
     */
    public function setLeftCompany($leftCompany)
    {
        $this->leftCompany = $leftCompany;
    }

    /**
     * @return mixed
     */
    public function getImportId()
    {
        return $this->importId;
    }

    /**
     * @param mixed $importId
     */
    public function setImportId($importId)
    {
        $this->importId = $importId;
    }

    /**
     * @return mixed
     */
    public function getCompanyDepartmentId()
    {
        return $this->companyDepartmentId;
    }

    /**
     * @param mixed $companyDepartmentId
     */
    public function setCompanyDepartmentId($companyDepartmentId)
    {
        $this->companyDepartmentId = $companyDepartmentId;
    }

    /**
     * @return mixed
     */
    public function getReportsTo()
    {
        return $this->reportsTo;
    }

    /**
     * @param mixed $reportsTo
     */
    public function setReportsTo($reportsTo)
    {
        $this->reportsTo = $reportsTo;
    }

    static function create(
        $siteId,
        $companyId,
        $lastName,
        $firstName,
        $title,
        $email1,
        $email2,
        $phoneWork,
        $phoneCell,
        $phoneOther,
        $address,
        $city,
        $state,
        $zip,
        $isHot,
        $notes,
        $enteredBy,
        $owner,
        $leftCompany,
        $companyDepartmentId,
        $reportsTo
    ) {
        if ($companyId <= 0) {
            throw new EntityException('invalidCompanyId: company id must be greater than 0, it is: ' . $companyId);
        }
        if (empty($lastName))
        {
            throw new EntityException('invalidLastName: last name cannot be empty');
        }
        if (empty($firstName))
        {
            throw new EntityException('invalidFirstName: first name cannot be empty');
        }
        $instance = new self($siteId, $companyId, $lastName, $firstName);
        $instance->setTitle($title);
        $instance->setEmail1($email1);
        $instance->setEmail2($email2);
        $instance->setPhoneWork($phoneWork);
        $instance->setPhoneCell($phoneCell);
        $instance->setPhoneOther($phoneOther);
        $instance->setAddress($address);
        $instance->setCity($city);
        $instance->setState($state);
        $instance->setZip($zip);
        $instance->setIsHot($isHot);
        $instance->setNotes($notes);
        $instance->setEnteredBy($enteredBy);
        $instance->setOwner($owner);
        $instance->setLeftCompany($leftCompany);
        $instance->setCompanyDepartmentId($companyDepartmentId);
        $instance->setReportsTo($reportsTo);
        return $instance;
    }
}