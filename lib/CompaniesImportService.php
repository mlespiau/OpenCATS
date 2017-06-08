<?php
use \OpenCATS\Entity\Company;

class CompaniesImportService
{
    public function __construct($siteID)
    {
        $this->_siteID = $siteID;
        $this->_db = DatabaseConnection::getInstance();
    }

    public function add($dataNamed, $userID, $importID)
    {
        $company = Company::create(
            $this->getSiteId(),
            $dataNamed['name'],
            $dataNamed['address'],
            $dataNamed['city'],
            $dataNamed['state'],
            $dataNamed['zipCode'],
            $dataNamed['phoneNumberOne'],
            $dataNamed['phoneNumberTwo'],
            $dataNamed['faxNumber'],
            $dataNamed['url'],
            $dataNamed['keyTechnologies'],
            $dataNamed['isHot'],
            $dataNamed['notes'],
            $userID,
            $userID
        );
        $company->setImportId($importID);
        $CompanyRepository = new CompanyRepository($this->getDatabaseConnection());
        try {
            $companyId = $CompanyRepository->persist($company, new History($this->_siteID));
        } catch(CompanyRepositoryException $e) {
            return -1;
        }
        return $companyId;
    }
}