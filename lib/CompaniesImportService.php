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
        if (!eval(Hooks::get('IMPORT_ADD_CLIENT'))) return;
        try {
            $companyId = $this->companyRepository->persist($company, new History($this->siteID));
        } catch(CompanyRepositoryException $e) {
            return -1;
        }
        if (!eval(Hooks::get('IMPORT_ADD_CLIENT_POST'))) return;
        return $companyId;
    }
}