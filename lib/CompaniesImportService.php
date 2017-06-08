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
        if (empty($company->getName()))
        {
            throw new ImportServiceException('Required fields (Company Name) are missing.');
        }
        $companies = $this->companyRepository->findByName($company->getName());
        if (count($companies) > 0)
        {
            throw new ImportServiceException('Duplicate entry.');
        }
        if (!eval(Hooks::get('IMPORT_ADD_CLIENT'))) return;
        try {
            $companyId = $this->companyRepository->persist($company, new History($this->siteID));
        } catch(CompanyRepositoryException $e) {
            throw new ImportServiceException('Failed to add candidate.');
        }
        if (!eval(Hooks::get('IMPORT_ADD_CLIENT_POST'))) return;
        return $companyId;
    }
}