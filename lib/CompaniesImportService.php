<?php
use \OpenCATS\Entity\Company;
use \OpenCATS\Entity\CompanyRepository;

class CompaniesImportService
{
    private $companyRepository;
    private $siteID;

    public function __construct($siteID, CompanyRepository $companyRepository)
    {
        $this->siteID = $siteID;
        $this->companyRepository = $companyRepository;
    }

    public function add(Company $company)
    {
        try {
            $companyId = $this->companyRepository->persist($company, new History($this->siteID));
        } catch(CompanyRepositoryException $e) {
            return -1;
        }
        return $companyId;
    }
}