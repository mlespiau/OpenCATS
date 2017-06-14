<?php
use \OpenCATS\Entity\Company;
use \OpenCATS\Entity\CompanyRepository;
use \OpenCATS\Entity\CompanyRepositoryException;
use \OpenCATS\Exception\ImportServiceException;
use \OpenCATS\Entity\ExtraFieldRepository;


class CompaniesImportService
{
    private $companyRepository;
    private $siteID;

    public function __construct($siteID, CompanyRepository $companyRepository, ExtraFieldRepository $extraFieldRepository)
    {
        $this->siteID = $siteID;
        $this->companyRepository = $companyRepository;
        $this->extraFieldRepository = $extraFieldRepository;
    }

    public function add(Company $company)
    {
        if (empty($company->getName()))
        {
            throw new ImportServiceException('Required fields (Company Name) are missing.');
        }
        $companies = $this->companyRepository->findByName($this->siteID, $company->getName());
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
        $company->setId($companyId);
        $this->persistExtraFields($company);
        if (!eval(Hooks::get('IMPORT_ADD_CLIENT_POST'))) return;
        return $companyId;
    }

    /**
     * @param Company $company
     * @param $companyId
     */
    private function persistExtraFields(Company $company)
    {
        if (!empty($company->getExtraFields())) {
            foreach ($company->getExtraFields() as $extraField) {
                $extraField->setDataItemId($company->getId());
            }
            $this->extraFieldRepository->persistMultiple($company->getExtraFields());
        }
    }
}