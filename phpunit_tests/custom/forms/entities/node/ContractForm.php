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
use tests\phpunit_tests\custom\entities\taxonomy_term\TalentArtistCompany;
use tests\phpunit_tests\custom\entities\taxonomy_term\TerritoryNow;
use tests\phpunit_tests\custom\forms\entities\taxonomy_term\BusinessContactsForm;
use tests\phpunit_tests\custom\forms\entities\taxonomy_term\ContractingCompanyForm;
use tests\phpunit_tests\custom\forms\entities\taxonomy_term\ContractTypeForm;
use tests\phpunit_tests\custom\forms\entities\taxonomy_term\DepartmentsForm;
use tests\phpunit_tests\custom\forms\entities\taxonomy_term\LegalEntityForm;
use tests\phpunit_tests\custom\forms\entities\taxonomy_term\MfnForm;
use tests\phpunit_tests\custom\forms\entities\taxonomy_term\TerritoryNowForm;

class ContractForm extends NodeForm {

  public function __construct($nid = NULL) {
    parent::__construct($nid);
  }

  public function fillDefaultValues(&$entities, $skip = array()) {

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
        $contractingCompanyForm->fillDefaultValues($entities);
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
      $contractTypeForm->fillDefaultValues($entities);
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
      $businessContactsForm->fillDefaultValues($entities);
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
      $departmentsForm->fillDefaultValues($entities);
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
        $legalEntityForm->fillDefaultValues($entities);
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
      $num = Utilities::getRandomInt(2, 5);
      for ($i = 0; $i < $num; $i++) {
        $mfnForm = new MfnForm();
        $mfnForm->fillDefaultValues($entities);
        $output = $mfnForm->submit();
        if ($output) {
          /* @var $object Mfn */
          $object = $mfnForm->getEntityObject();
          $entities['taxonomy_term'][$object->getId()] = $object;
          $fields['field_mfn'][$i] = $object;
        }
        else {
          return array(FALSE, $fields, "Could not create a MFN term.");
        }
      }
      $this->fillFieldMfn(Utilities::getId($fields['field_mfn']));
    }

    if (!in_array('field_master_contract', $skip)) {
      $num = Utilities::getRandomInt(2, 5);
      for ($i = 0; $i < $num; $i++) {
        $groupContractForm = new ContractForm();
        $sk = array('field_master_contract');
        $groupContractForm->fillDefaultValues($entities, $sk);
        $output = $groupContractForm->submit();
        if ($output) {
          /* @var $object Contract */
          $object = $groupContractForm->getEntityObject();
          $entities['node'][$object->getId()] = $object;
          $fields['field_master_contract'][$i] = $object;
        }
        else {
          return array(
            FALSE,
            $fields,
            "Could not create Master Contract node."
          );
        }
      }
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

    if (!in_array('field_talent_artist', $skip)) {
      list($success, $territoryNowObjects, $msg) = TalentArtistCompany::createDefault(
        $entities,
        Utilities::getRandomInt(2, 3)
      );
      if (!$success) {
        return array(FALSE, $fields, $msg);
      }

      $term_names = array();
      foreach ($territoryNowObjects as $territoryNowObject) {
        $term_names[] = $territoryNowObject->getLabel();
      }

      $num = Utilities::getRandomInt(1, 2);
      for ($i = 0; $i < $num; $i++) {
        $new_term_name = Utilities::getRandomString();
        $territoryNowObjects[] = $new_term_name;
        $term_names[] = $new_term_name;
      }
      $fields['field_talent_artist'] = $territoryNowObjects;
      $this->fillFieldTalentArtist($term_names);
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