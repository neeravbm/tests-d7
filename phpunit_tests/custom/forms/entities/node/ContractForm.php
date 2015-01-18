<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 11/12/14
 * Time: 5:40 PM
 */

namespace tests\phpunit_tests\custom\forms\entities\node;

use tests\phpunit_tests\core\forms\NodeForm as NodeForm;
use tests\phpunit_tests\core\Utilities;
use tests\phpunit_tests\custom\entities\node\Contract;
use tests\phpunit_tests\custom\entities\taxonomy_term\BusinessContacts;
use tests\phpunit_tests\custom\entities\taxonomy_term\LegalEntity;
use tests\phpunit_tests\custom\entities\taxonomy_term\ContractingCompany;
use tests\phpunit_tests\custom\entities\taxonomy_term\ContractType;
use tests\phpunit_tests\custom\entities\taxonomy_term\Departments;
use tests\phpunit_tests\custom\entities\taxonomy_term\Mfn;
use tests\phpunit_tests\custom\entities\taxonomy_term\TalentAgent;
use tests\phpunit_tests\custom\entities\taxonomy_term\TalentArtistCompany;
use tests\phpunit_tests\custom\entities\taxonomy_term\TalentAttorney;
use tests\phpunit_tests\custom\entities\taxonomy_term\TalentBaExec;
use tests\phpunit_tests\custom\entities\taxonomy_term\TalentCompany;
use tests\phpunit_tests\custom\entities\taxonomy_term\TalentCreativeExecutive;
use tests\phpunit_tests\custom\entities\taxonomy_term\TalentLawFirm;
use tests\phpunit_tests\custom\entities\taxonomy_term\TalentLegalExec;
use tests\phpunit_tests\custom\entities\taxonomy_term\TalentProject;
use tests\phpunit_tests\custom\entities\taxonomy_term\TalentServices;
use tests\phpunit_tests\custom\entities\taxonomy_term\TalentStatus;
use tests\phpunit_tests\custom\entities\taxonomy_term\TalentTypeOfAgreement;
use tests\phpunit_tests\custom\entities\taxonomy_term\TypeOfContract;
use tests\phpunit_tests\custom\forms\entities\taxonomy_term\BusinessContactsForm;
use tests\phpunit_tests\custom\forms\entities\taxonomy_term\ContractingCompanyForm;
use tests\phpunit_tests\custom\forms\entities\taxonomy_term\ContractTypeForm;
use tests\phpunit_tests\custom\forms\entities\taxonomy_term\DepartmentsForm;
use tests\phpunit_tests\custom\forms\entities\taxonomy_term\LegalEntityForm;

class ContractForm extends NodeForm {

  public function __construct($nid = NULL) {
    parent::__construct($nid);
  }

  public function fillDefaultValues1(&$entities, $skip = array()) {

    $fields = array();

    /*$contractedFamilyForm = new ContractedFamilyForm();
    $contractedFamilyForm->fillName(Utilities::getRandomString());
    $output = $contractedFamilyForm->submit();
    self::assertTrue(
      $output,
      "Administrator user could not create a Contracted Family term."
    );
    if ($output) {
      self::$contractedFamilyObject = $contractedFamilyForm->getEntityObject();
    }*/

    /*$contractForm->fillFieldContractingFamily(
  array(32, self::$contractedFamilyObject->getId())
);
$contractForm->fillFieldContractFiles(
  array('Continuous Delivery.pdf', 'Continuous Delivery.pdf')
);*/

    if (!in_array('field_contract_company', $skip)) {
      $num = Utilities::getRandomInt(2, 5);
      for ($i = 0; $i <= $num; $i++) {
        $contractingCompanyForm = new ContractingCompanyForm();
        $contractingCompanyForm->fillDefaultValues();
        $output = $contractingCompanyForm->submit();
        if ($output) {
          /* @var $object ContractingCompany */
          $object = $contractingCompanyForm->getEntityObject();
          $entities['taxonomy_term'][$object->getId()] = $object;
          $fields['field_contract_company'][$i] = $object;
        }
        else {
          return array(
            FALSE,
            $fields,
            "Could not create a Contracting Company term."
          );
        }
      }
      $this->fillFieldContractCompany(
        implode(
          ",",
          Utilities::getLabel($fields['field_contract_company'])
        )
      );
    }

    if (!in_array('field_contract_type', $skip)) {
      $contractTypeForm = new ContractTypeForm();
      $contractTypeForm->fillDefaultValues();
      $output = $contractTypeForm->submit();
      if ($output) {
        /* @var $object ContractType */
        $object = $contractTypeForm->getEntityObject();
        $entities['taxonomy_term'][$object->getId()] = $object;
        $fields['field_contract_type'] = $object;
      }
      else {
        return array(
          FALSE,
          $fields,
          "Could not create a Contract Type term."
        );
      }
      $this->fillFieldContractType($object->getId());
    }

    if (!in_array('field_business_contact', $skip)) {
      $businessContactsForm = new BusinessContactsForm();
      $businessContactsForm->fillDefaultValues();
      $output = $businessContactsForm->submit();
      if ($output) {
        /* @var $object BusinessContacts */
        $object = $businessContactsForm->getEntityObject();
        $entities['taxonomy_term'][$object->getId()] = $object;
        $fields['field_business_contact'] = $object;
      }
      else {
        return array(
          FALSE,
          $fields,
          "Could not create a Business Contacts term."
        );
      }
      $this->fillFieldBusinessContact($object->getLabel());
    }

    if (!in_array('field_department', $skip)) {
      $departmentsForm = new DepartmentsForm();
      $departmentsForm->fillDefaultValues();
      $output = $departmentsForm->submit();
      if ($output) {
        /* @var $object Departments */
        $object = $departmentsForm->getEntityObject();
        $entities['taxonomy_term'][$object->getId()] = $object;
        $fields['field_department'] = $object;
      }
      else {
        return array(FALSE, $fields, "Could not create a Departments term.");
      }
      $this->fillFieldDepartment($object->getId());
    }


    if (!in_array('field_netflix_legal_entity', $skip)) {
      $num = Utilities::getRandomInt(2, 5);
      for ($i = 0; $i <= $num; $i++) {
        $legalEntityForm = new LegalEntityForm();
        $legalEntityForm->fillDefaultValues();
        $output = $legalEntityForm->submit();
        if ($output) {
          /* @var $object LegalEntity */
          $object = $legalEntityForm->getEntityObject();
          $entities['taxonomy_term'][$object->getId()] = $object;
          $fields['field_netflix_legal_entity'][$i] = $object;
        }
        else {
          return array(
            FALSE,
            $fields,
            "Could not create a Legal Entity term."
          );
        }
      }
      $this->fillFieldNetflixLegalEntity(
        Utilities::getId($fields['field_netflix_legal_entity'])
      );
    }

    if (!in_array('field_mfn', $skip)) {
      list($success, $mfnObjects, $msg) = Mfn::createDefault(
        Utilities::getRandomInt(2, 5)
      );
      if (!$success) {
        return array(FALSE, $fields, $msg);
      }

      $fields['field_mfn'] = $mfnObjects;
      $this->fillFieldMfn(Utilities::getId($fields['field_mfn']));
    }

    if (!in_array('field_master_contract', $skip)) {
      list($success, $masterContractObjects, $msg) = Contract::createDefault(
        Utilities::getRandomInt(2, 5),
        array('field_master_contract', 'field_talent_group_id')
      );
      if (!$success) {
        return array(FALSE, $fields, $msg);
      }

      $fields['field_master_contract'] = $masterContractObjects;
      $this->fillFieldMasterContract(
        Utilities::getId($fields['field_master_contract'])
      );
    }

    /*if (!in_array('field_territory', $skip)) {
      list($success, $territoryNowObjects, $msg) = TerritoryNow::createDefault(
        $entities,
        Utilities::getRandomInt(2, 5)
      );
      if (!$success) {
        return array(FALSE, $fields, $msg);
      }
      $fields['field_territory'] = $territoryNowObjects;
      $this->fillFieldTerritory(Utilities::getId($fields['field_territory']));
    }*/

    if (!in_array('field_exclusivity_holdback', $skip)) {
      $fields['field_exclusivity_holdback'] = Utilities::getRandomInt(0, 1);
      $this->fillFieldExclusivityHoldback(
        $fields['field_exclusivity_holdback']
      );
    }

    if (!in_array('field_security', $skip)) {
      $fields['field_security'] = Utilities::getRandomInt(0, 1);
      $this->fillFieldSecurity($fields['field_security']);
    }

    if (!in_array('field_output', $skip)) {
      $fields['field_output'] = Utilities::getRandomInt(0, 1);
      $this->fillFieldOutput($fields['field_output']);
    }

    if (!in_array('field_pro', $skip)) {
      $fields['field_pro'] = Utilities::getRandomInt(0, 1);
      $this->fillFieldPro($fields['field_pro']);
    }

    if (!in_array('field_guaranty', $skip)) {
      $fields['field_guaranty'] = Utilities::getRandomInt(0, 1);
      $this->fillFieldGuaranty($fields['field_guaranty']);
    }

    if (!in_array('field_notes', $skip)) {
      $fields['field_notes'] = Utilities::getRandomString();
      $this->fillFieldNotes($fields['field_notes']);
    }

    if (!in_array('field_talent_project_title', $skip)) {
      list($success, $talentProjectObject, $msg) = TalentProject::createDefault(
      );
      if (!$success) {
        return array(FALSE, $fields, $msg);
      }

      $fields['field_talent_project_title'] = $talentProjectObject;
      $this->fillFieldTalentProjectTitle(
        Utilities::getId($fields['field_talent_project_title'])
      );
    }

    if (!in_array('field_talent_creative_executive', $skip)) {
      list($success, $talentCreativeExecutiveObject, $msg) = TalentCreativeExecutive::createDefault(
      );
      if (!$success) {
        return array(FALSE, $fields, $msg);
      }

      $fields['field_talent_creative_executive'] = $talentCreativeExecutiveObject;
      $this->fillFieldTalentCreativeExecutive(
        Utilities::getId($fields['field_talent_creative_executive'])
      );
    }

    if (!in_array('field_talent_artist', $skip)) {
      list($success, $talentArtistObjects, $msg) = TalentArtistCompany::createDefault(
        Utilities::getRandomInt(2, 3)
      );
      if (!$success) {
        return array(FALSE, $fields, $msg);
      }

      $term_names = array();
      foreach ($talentArtistObjects as $talentArtistObject) {
        $term_names[] = $talentArtistObject->getLabel();
      }

      $num = Utilities::getRandomInt(1, 2);
      for ($i = 0; $i < $num; $i++) {
        $new_term_name = Utilities::getRandomString();
        $talentArtistObjects[] = $new_term_name;
        $term_names[] = $new_term_name;
      }
      $fields['field_talent_artist'] = $talentArtistObjects;
      $this->fillFieldTalentArtist($term_names);
    }

    if (!in_array('field_talent_company', $skip)) {
      list($success, $talentCompanyObjects, $msg) = TalentCompany::createDefault(
        Utilities::getRandomInt(2, 3)
      );
      if (!$success) {
        return array(FALSE, $fields, $msg);
      }

      $term_names = array();
      foreach ($talentCompanyObjects as $talentCompanyObject) {
        $term_names[] = $talentCompanyObject->getLabel();
      }

      $num = Utilities::getRandomInt(1, 2);
      for ($i = 0; $i < $num; $i++) {
        $new_term_name = Utilities::getRandomString();
        $talentCompanyObjects[] = $new_term_name;
        $term_names[] = $new_term_name;
      }
      $fields['field_talent_company'] = $talentCompanyObjects;
      $this->fillFieldTalentCompany($term_names);
    }

    if (!in_array('field_talent_attorney', $skip)) {
      list($success, $talentAttorneyObjects, $msg) = TalentAttorney::createDefault(
        Utilities::getRandomInt(2, 3)
      );
      if (!$success) {
        return array(FALSE, $fields, $msg);
      }

      $term_names = array();
      foreach ($talentAttorneyObjects as $talentAttorneyObject) {
        $term_names[] = $talentAttorneyObject->getLabel();
      }

      $num = Utilities::getRandomInt(1, 2);
      for ($i = 0; $i < $num; $i++) {
        $new_term_name = Utilities::getRandomString();
        $talentAttorneyObjects[] = $new_term_name;
        $term_names[] = $new_term_name;
      }
      $fields['field_talent_attorney'] = $talentAttorneyObjects;
      $this->fillFieldTalentAttorney($term_names);
    }

    if (!in_array('field_talent_law_firm', $skip)) {
      list($success, $talentLawFirmObjects, $msg) = TalentLawFirm::createDefault(
        Utilities::getRandomInt(2, 3)
      );
      if (!$success) {
        return array(FALSE, $fields, $msg);
      }

      $term_names = array();
      foreach ($talentLawFirmObjects as $talentLawFirmObject) {
        $term_names[] = $talentLawFirmObject->getLabel();
      }

      $num = Utilities::getRandomInt(1, 2);
      for ($i = 0; $i < $num; $i++) {
        $new_term_name = Utilities::getRandomString();
        $talentLawFirmObjects[] = $new_term_name;
        $term_names[] = $new_term_name;
      }
      $fields['field_talent_law_firm'] = $talentLawFirmObjects;
      $this->fillFieldTalentLawFirm($term_names);
    }

    if (!in_array('field_talent_agent', $skip)) {
      list($success, $talentAgentObjects, $msg) = TalentAgent::createDefault(
        Utilities::getRandomInt(2, 3)
      );
      if (!$success) {
        return array(FALSE, $fields, $msg);
      }

      $term_names = array();
      foreach ($talentAgentObjects as $talentAgentObject) {
        $term_names[] = $talentAgentObject->getLabel();
      }

      $num = Utilities::getRandomInt(1, 2);
      for ($i = 0; $i < $num; $i++) {
        $new_term_name = Utilities::getRandomString();
        $talentAgentObjects[] = $new_term_name;
        $term_names[] = $new_term_name;
      }
      $fields['field_talent_agent'] = $talentAgentObjects;
      $this->fillFieldTalentAgent($term_names);
    }

    if (!in_array('field_talent_agency', $skip)) {
      list($success, $talentAgencyObjects, $msg) = TalentAgent::createDefault(
        Utilities::getRandomInt(2, 3)
      );
      if (!$success) {
        return array(FALSE, $fields, $msg);
      }

      $term_names = array();
      foreach ($talentAgencyObjects as $talentAgencyObject) {
        $term_names[] = $talentAgencyObject->getLabel();
      }

      $num = Utilities::getRandomInt(1, 2);
      for ($i = 0; $i < $num; $i++) {
        $new_term_name = Utilities::getRandomString();
        $talentAgencyObjects[] = $new_term_name;
        $term_names[] = $new_term_name;
      }
      $fields['field_talent_agency'] = $talentAgencyObjects;
      $this->fillFieldTalentAgency($term_names);
    }

    if (!in_array('field_talent_services', $skip)) {
      list($success, $talentServicesObjects, $msg) = TalentServices::createDefault(
        Utilities::getRandomInt(2, 5)
      );
      if (!$success) {
        return array(FALSE, $fields, $msg);
      }

      $fields['field_talent_services'] = $talentServicesObjects;
      $this->fillFieldTalentServices(
        Utilities::getId($fields['field_talent_services'])
      );
    }

    if (!in_array('field_talent_ba_exec', $skip)) {
      list($success, $talentBAExecObject, $msg) = TalentBaExec::createDefault();
      if (!$success) {
        return array(FALSE, $fields, $msg);
      }

      $fields['field_talent_ba_exec'] = $talentBAExecObject;
      $this->fillFieldTalentBaExec($talentBAExecObject->getId());
    }

    if (!in_array('field_talent_legal_exec', $skip)) {
      list($success, $talentLegalExecObject, $msg) = TalentLegalExec::createDefault(
      );
      if (!$success) {
        return array(FALSE, $fields, $msg);
      }

      $fields['field_talent_legal_exec'] = $talentLegalExecObject;
      $this->fillFieldTalentLegalExec($talentLegalExecObject->getId());
    }

    if (!in_array('field_talent_status', $skip)) {
      list($success, $talentStatusObject, $msg) = TalentStatus::createDefault();
      if (!$success) {
        return array(FALSE, $fields, $msg);
      }

      $fields['field_talent_status'] = $talentStatusObject;
      $this->fillFieldTalentStatus($talentStatusObject->getId());
    }

    if (!in_array('field_talent_deal_terms', $skip)) {
      $fields['field_talent_deal_terms'] = Utilities::getRandomString();
      $this->fillFieldTalentDealTerms($fields['field_talent_deal_terms']);
    }

    if (!in_array('field_type_of_contract', $skip)) {
      list($success, $typeOfContractObject, $msg) = TypeOfContract::createDefault(
      );
      if (!$success) {
        return array(FALSE, $fields, $msg);
      }

      $fields['field_type_of_contract'] = $typeOfContractObject;
      $this->fillFieldTypeOfContract($typeOfContractObject->getId());
    }

    if (!in_array('field_executed', $skip)) {
      $fields['field_executed'] = 0;//Utilities::getRandomInt(0, 1);
      $this->fillFieldExecuted($fields['field_executed']);
    }

    if (!in_array('field_contract_num', $skip)) {
      $fields['field_contract_num'] = Utilities::getRandomInt(-255, 255);
      $this->fillFieldContractNum($fields['field_contract_num']);
    }

    if (!in_array('field_talent_notes', $skip)) {
      $fields['field_talent_notes'] = Utilities::getRandomString();
      $this->fillFieldTalentNotes($fields['field_talent_notes']);
    }

    if (!in_array('field_talent_group_id', $skip)) {
      list($success, $talentGroupContractObjects, $msg) = Contract::createDefault(
        Utilities::getRandomInt(2, 5),
        array('field_master_contract', 'field_talent_group_id')
      );
      if (!$success) {
        return array(FALSE, $fields, $msg);
      }

      $fields['field_talent_group_id'] = $talentGroupContractObjects;
      $this->fillFieldTalentGroupId(
        Utilities::getId($fields['field_talent_group_id'])
      );
    }

    if (!in_array('field_talent_type_of_agreements', $skip)) {
      list($success, $talentTypeOfAgreementObject, $msg) = TalentTypeOfAgreement::createDefault(
      );
      if (!$success) {
        return array(FALSE, $fields, $msg);
      }

      $fields['field_talent_type_of_agreements'] = $talentTypeOfAgreementObject;
      $this->fillFieldTalentTypeOfAgreements(
        $talentTypeOfAgreementObject->getId()
      );
    }

    if (!in_array('field_contract_name', $skip)) {
      $contractNameString = Utilities::getRandomString();
      $fields['field_contract_name'] = $contractNameString;
      $this->fillFieldContractName($contractNameString);
    }

    if (!in_array('field_effective_date', $skip)) {
      // The effective date should be after Jan 1, 2000 otherwise Drupal takes
      // 1/1/99 as 1/1/2099.
      $contractEffectiveDate = Utilities::getRandomDate(
        'm/d/y',
        "1/1/2000"
      );
      $fields['field_effective_date'] = $contractEffectiveDate;
      $this->fillFieldEffectiveDate($contractEffectiveDate);
    }

    return array(TRUE, $fields, "");
  }
}