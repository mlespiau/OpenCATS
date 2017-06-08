<?php
use \OpenCATS\Entity\Company;

class CompaniesImportService
{
    public function __construct($siteID)
    {
        $this->_siteID = $siteID;
        $this->_db = DatabaseConnection::getInstance();
    }

    public function add(Company $company)
    {
        $CompanyRepository = new CompanyRepository($this->getDatabaseConnection());
        try {
            $companyId = $CompanyRepository->persist($company, new History($this->_siteID));
        } catch(CompanyRepositoryException $e) {
            return -1;
        }
        return $companyId;
    }
}