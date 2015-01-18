<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 11/12/14
 * Time: 5:47 PM
 */

namespace tests\phpunit_tests;

use tests\phpunit_tests\core\entities\User as User;
use tests\phpunit_tests\core\Utilities as Utilities;
use tests\phpunit_tests\custom\entities\node\Contract as Contract;
use tests\phpunit_tests\custom\entities\taxonomy_term\TypeOfContract;
use tests\phpunit_tests\custom\forms\entities\node\ContractForm as ContractForm;

define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_override_server_variables();
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

class ContractWorkflowReadOnlyUserNoMasterContractTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var array
   */
  private static $fields;

  /**
   * @var User
   */
  private static $userObject;

  /**
   * @var Contract
   */
  private static $contractObject;

  private static $talentFields;

  /**
   * Log in as "neemehta" who has administrator rights.
   */
  public static function setUpBeforeClass() {
    // Log in as an admin user and create a contract.
    $userObject = User::loginProgrammatically('neeravm');

    global $entities;
    $entities = array();

    $contractForm = new ContractForm();
    $skip = array(
      'field_contracting_family',
      'field_master_contract',
    );
    list($output, self::$fields, $msg) = $contractForm->fillDefaultValues(
      $skip
    );
    self::assertTrue($output, $msg);

    $output = $contractForm->submit();
    self::assertTrue(
      $output,
      "Administrator user could not create a contract: " . implode(
        ", ",
        $contractForm->getErrors()
      )
    );
    if ($output) {
      self::$contractObject = $contractForm->getEntityObject();
    }
    $userObject->logout();

    // Log in as a ReadOnly user.
    self::$userObject = User::loginProgrammatically(self::READONLY_USER);
  }

  public function testValues() {
    self::$talentFields = array(
      'field_talent_project_title',
      'field_talent_creative_executive',
      'field_talent_artist',
      'field_talent_company',
      'field_talent_attorney',
      'field_talent_law_firm',
      'field_talent_agent',
      'field_talent_agency',
      'field_talent_services',
      'field_talent_ba_exec',
      'field_talent_legal_exec',
      'field_talent_status',
      'field_talent_deal_terms',
      'field_talent_group_id',
      'field_talent_type_of_agreements',
    );

    $skip = array(
        'title',
        'field_type_of_contract',
        'field_executed',
        'field_master_contract',
      ) + self::$talentFields;

    self::$contractObject->checkItems($this, self::$fields, $skip);

    // Master contract is set to itself.
    self::$contractObject->checkFieldMasterContractItems(
      $this,
      self::$contractObject->getId()
    );

    // Contract title is always "contract <nid>" if field_master_contract field
    // is set.
    self::$contractObject->checkTitleItems(
      $this,
      'contract ' . self::$contractObject->getId()
    );

    // Executed field is set to 1.
    self::$contractObject->checkFieldExecutedItems($this, 1);

    // Type of contract is set to Talent (tid: 18820) using Rules irrespective
    // of the value set by the user.
    $talentTerm = new TypeOfContract(18820);
    self::$contractObject->checkFieldTypeOfContractItems($this, $talentTerm);
  }

  /**
   * User should not have access to create a new contract.
   */
  public function atestContractCreationAccess() {
    $this->assertFalse(
      Contract::hasCreateAccess(),
      "User with ReadOnly role has access to create contract."
    );
  }

  /**
   * User should not access to update an existing contract.
   */
  public function atestContractUpdateAccess() {
    $this->assertFalse(
      self::$contractObject->hasUpdateAccess(),
      "User with ReadOnly role has access to edit the contract."
    );
  }

  /**
   * User should not access to delete an existing contract.
   */
  public function atestContractDeleteAccess() {
    $this->assertFalse(
      self::$contractObject->hasDeleteAccess(),
      "User with ReadOnly role has access to delete the contract."
    );
  }

  /**
   * User should have access to view an existing published contract.
   */
  public function atestContractViewAccess() {
    $this->assertTrue(
      self::$contractObject->hasViewAccess(),
      "User with ReadOnly role does not have access to view a published contract."
    );
  }

  /**
   * User should have access to view the Contracting Family field.
   */
  public function atestFieldContractingFamilyAccess() {
    $this->assertTrue(
      self::$contractObject->hasFieldContractingFamilyViewAccess(),
      "User with ReadOnly role does not have access to view the Contracting Family field of a published contract."
    );
    /*$this->assertEquals(
      Utilities::getId(self::$fields['field_contracting_family']),
      self::$contractObject->getFieldContractingFamily(),
      "User with ReadOnly role is seeing incorrect Contract Family values."
    );
    $this->assertEquals(
      Utilities::getId(self::$fields['field_contracting_family']),
      self::$contractObject->viewFieldContractingFamily(),
      "User with ReadOnly role is seeing incorrect Contract Family values."
    );*/
  }

  /**
   * User should have access to view the Contracting Family field.
   */
  public function atestFieldContractFilesAccess() {
    $this->assertTrue(
      self::$contractObject->hasFieldContractFilesViewAccess(),
      "User with ReadOnly role does not have access to view the Contract Files field of a published contract."
    );
  }

  /**
   * User should have access to view the Contract Company field.
   */
  public function atestFieldContractCompany() {
    $this->assertTrue(
      self::$contractObject->hasFieldContractCompanyViewAccess(),
      "User with ReadOnly role does not have access to view the Contract Company field of a published contract."
    );
    /*$this->assertEquals(
      Utilities::getId(self::$fields['field_contract_company']),
      self::$contractObject->getFieldContractCompany(),
      "User with ReadOnly role is seeing incorrect Contract Company values."
    );*/
    $this->assertEquals(
      self::$fields['field_contract_company'],
      self::$contractObject->viewFieldContractCompany(),
      "User with ReadOnly role is seeing incorrect Contract Company values."
    );
  }

  /**
   * User should have access to view the Contract Type field.
   */
  public function atestFieldContractType() {
    $this->assertTrue(
      self::$contractObject->hasFieldContractTypeViewAccess(),
      "User with ReadOnly role does not have access to view the Contract Type field of a published contract."
    );
    $this->assertEquals(
      self::$fields['field_contract_type']->getId(),
      self::$contractObject->getFieldContractType(),
      "User with ReadOnly role is seeing incorrect Contract Type value."
    );
    $this->assertEquals(
      self::$fields['field_contract_type']->getLabel(),
      self::$contractObject->viewFieldContractType(),
      "User with ReadOnly role is seeing incorrect Contract Type value."
    );
  }

  /**
   * User should have access to view the Contract Name field.
   */
  public function atestFieldContractName() {
    $this->assertTrue(
      self::$contractObject->hasFieldContractNameViewAccess(),
      "User with ReadOnly role does not have access to view the Contract Name field of a published contract."
    );
    $this->assertEquals(
      self::$fields['field_contract_name'],
      self::$contractObject->getFieldContractName(),
      "User with ReadOnly role is seeing incorrect Contract Name value."
    );
    $this->assertEquals(
      self::$fields['field_contract_name'],
      self::$contractObject->viewFieldContractName(),
      "User with ReadOnly role is seeing incorrect Contract Name value."
    );
  }

  /**
   * Automatic Entity Label should be setting the value of Legal Id.
   */
  public function atestLegalId() {
    $this->assertEquals(
      "contract", // . " " . self::$contractObject->getId(),
      self::$contractObject->getTitle(),
      "User with ReadOnly role is seeing incorrect Legal Id value."
    );
    $this->assertEquals(
      "contract", // . " " . self::$contractObject->getId(),
      self::$contractObject->viewTitle(),
      "User with ReadOnly role is seeing incorrect Legal Id value."
    );
  }

  /**
   * User should have access to view the Effective Date field.
   */
  public function atestEffectiveDate() {
    $this->assertTrue(
      self::$contractObject->hasFieldEffectiveDateViewAccess(),
      "User with ReadOnly role does not have access to view the Effective Date field of a published contract."
    );
    $this->assertEquals(
      date(
        "Y-m-d",
        strtotime(self::$fields['field_effective_date'])
      ) . " 00:00:00",
      self::$contractObject->getFieldEffectiveDate(),
      "User with ReadOnly role is seeing incorrect Effective Date value."
    );
    $this->assertEquals(
      '<span class="date-display-single">' . self::$fields['field_effective_date'] . '</span>',
      self::$contractObject->viewFieldEffectiveDate(),
      "User with ReadOnly role is seeing incorrect Effective Date value."
    );
  }

  /**
   * User should have access to view the Business Contact field.
   */
  public function atestBusinessContact() {
    $this->assertTrue(
      self::$contractObject->hasFieldBusinessContactViewAccess(),
      "User with ReadOnly role does not have access to view the Business Contact field of a published contract."
    );
    /*$this->assertEquals(
      self::$fields['field_business_contact']->getId(),
      self::$contractObject->getFieldBusinessContact(),
      "User with ReadOnly role is seeing incorrect Business Contact values."
    );
    $this->assertEquals(
      self::$fields['field_business_contact']->getLabel(),
      self::$contractObject->viewFieldBusinessContact(),
      "User with ReadOnly role is seeing incorrect Business Contact values."
    );*/
  }

  /**
   * User should have access to view the Department field.
   */
  public function atestDepartment() {
    $this->assertTrue(
      self::$contractObject->hasFieldDepartmentViewAccess(),
      "User with ReadOnly role does not have access to view the Department field of a published contract."
    );
    $this->assertEquals(
      self::$fields['field_department']->getId(),
      self::$contractObject->getFieldDepartment(),
      "User with ReadOnly role is seeing incorrect Department values."
    );
    $this->assertEquals(
      self::$fields['field_department']->getLabel(),
      self::$contractObject->viewFieldDepartment(),
      "User with ReadOnly role is seeing incorrect Department values."
    );
  }

  /**
   * User should have access to view the Netflix Legal Entity field.
   */
  public function atestNetflixLegalEntity() {
    $this->assertTrue(
      self::$contractObject->hasFieldNetflixLegalEntityViewAccess(),
      "User with ReadOnly role does not have access to view the Netflix Legal Entity field of a published contract."
    );
    $this->assertEquals(
      Utilities::getId(self::$fields['field_netflix_legal_entity']),
      self::$contractObject->getFieldNetflixLegalEntity(),
      "User with ReadOnly role is seeing incorrect Netflix Legal Entity values."
    );
    $this->assertEquals(
      Utilities::getLabel(self::$fields['field_netflix_legal_entity']),
      self::$contractObject->viewFieldNetflixLegalEntity(),
      "User with ReadOnly role is seeing incorrect Netflix Legal Entity values."
    );
  }

  /**
   * User should have access to view the MFN field.
   */
  public function atestMFN() {
    $this->assertTrue(
      self::$contractObject->hasFieldMfnViewAccess(),
      "User with ReadOnly role does not have access to view the MFN field of a published contract."
    );
    $this->assertEquals(
      Utilities::getId(self::$fields['field_mfn']),
      self::$contractObject->getFieldMfn(),
      "User with ReadOnly role is seeing incorrect MFN values."
    );
    $this->assertEquals(
      Utilities::getLabel(self::$fields['field_mfn']),
      self::$contractObject->viewFieldMfn(),
      "User with ReadOnly role is seeing incorrect MFN values."
    );
  }

  /**
   * User should have access to view the Master Contract field.
   */
  public function atestMasterContract() {
    $this->assertTrue(
      self::$contractObject->hasFieldMasterContractViewAccess(),
      "User with ReadOnly role does not have access to view the Master Contract field of a published contract."
    );
    $this->assertEquals(
      self::$fields['field_master_contract'],
      self::$contractObject->getFieldMasterContract(),
      "User with ReadOnly role is seeing incorrect Master Contract values."
    );
    $this->assertEquals(
      self::$fields['field_master_contract'],
      self::$contractObject->viewFieldMasterContract(),
      "User with ReadOnly role is seeing incorrect Master Contract values."
    );
  }

  /**
   * User should have access to view the Exclusivity/Holdback field.
   */
  public function atestExclusivityHoldback() {
    $this->assertTrue(
      self::$contractObject->hasFieldExclusivityHoldbackViewAccess(),
      "User with ReadOnly role does not have access to view the Exclusivity/Holdback field of a published contract."
    );
    $this->assertEquals(
      self::$fields['field_exclusivity_holdback'],
      self::$contractObject->getFieldExclusivityHoldback(),
      "User with ReadOnly role is seeing incorrect Exclusivity/Holdback values."
    );
    $this->assertEquals(
      (self::$fields['field_exclusivity_holdback'] ? "Yes" : "No"),
      self::$contractObject->viewFieldExclusivityHoldback(),
      "User with ReadOnly role is seeing incorrect Exclusivity/Holdback values."
    );
  }

  /**
   * User should have access to view the Security field.
   */
  public function atestSecurity() {
    $this->assertTrue(
      self::$contractObject->hasFieldSecurityViewAccess(),
      "User with ReadOnly role does not have access to view the Security field of a published contract."
    );
    $this->assertEquals(
      self::$fields['field_security'],
      self::$contractObject->getFieldSecurity(),
      "User with ReadOnly role is seeing incorrect Security values."
    );
    $this->assertEquals(
      (self::$fields['field_security'] ? "Yes" : "No"),
      self::$contractObject->viewFieldSecurity(),
      "User with ReadOnly role is seeing incorrect Security values."
    );
  }

  /**
   * User should have access to view the Output field.
   */
  public function atestOutput() {
    $this->assertTrue(
      self::$contractObject->hasFieldOutputViewAccess(),
      "User with ReadOnly role does not have access to view the Output field of a published contract."
    );
    $this->assertEquals(
      self::$fields['field_output'],
      self::$contractObject->getFieldOutput(),
      "User with ReadOnly role is seeing incorrect Output values."
    );
    $this->assertEquals(
      (self::$fields['field_output'] ? "Yes" : "No"),
      self::$contractObject->viewFieldOutput(),
      "User with ReadOnly role is seeing incorrect Output values."
    );
  }

  /**
   * User should have access to view the PRO field.
   */
  public function atestPro() {
    $this->assertTrue(
      self::$contractObject->hasFieldProViewAccess(),
      "User with ReadOnly role does not have access to view the PRO field of a published contract."
    );
    $this->assertEquals(
      self::$fields['field_pro'],
      self::$contractObject->getFieldPro(),
      "User with ReadOnly role is seeing incorrect PRO values."
    );
    $this->assertEquals(
      (self::$fields['field_pro'] ? "Yes" : "No"),
      self::$contractObject->viewFieldPro(),
      "User with ReadOnly role is seeing incorrect PRO values."
    );
  }

  /**
   * User should have access to view the Guaranty field.
   */
  public function atestGuaranty() {
    $this->assertTrue(
      self::$contractObject->hasFieldGuarantyViewAccess(),
      "User with ReadOnly role does not have access to view the Guaranty field of a published contract."
    );
    $this->assertEquals(
      self::$fields['field_guaranty'],
      self::$contractObject->getFieldGuaranty(),
      "User with ReadOnly role is seeing incorrect Guaranty values."
    );
    $this->assertEquals(
      (self::$fields['field_guaranty'] ? "Yes" : "No"),
      self::$contractObject->viewFieldGuaranty(),
      "User with ReadOnly role is seeing incorrect Guaranty values."
    );
  }

  /**
   * User should have access to view the Notes field.
   */
  public function atestNotes() {
    $this->assertTrue(
      self::$contractObject->hasFieldNotesViewAccess(),
      "User with ReadOnly role does not have access to view the Notes field of a published contract."
    );
    $this->assertEquals(
      self::$fields['field_notes'],
      self::$contractObject->getFieldNotes(),
      "User with ReadOnly role is seeing incorrect Notes values."
    );
    $this->assertEquals(
      self::$fields['field_notes'],
      self::$contractObject->viewFieldNotes(),
      "User with ReadOnly role is seeing incorrect Notes values."
    );
  }

  /**
   * User should not have access to view the Project Title field.
   */
  public function atestProjectTitle() {
    $this->assertFalse(
      self::$contractObject->hasFieldTalentProjectTitleViewAccess(),
      "User with ReadOnly role has access to view the Notes field of a published contract."
    );
    $this->assertEquals(
      self::$fields['field_talent_project_title']->getId(),
      self::$contractObject->getFieldTalentProjectTitle(),
      "User with ReadOnly role is seeing incorrect Project Title values."
    );
    $this->assertNull(
      self::$contractObject->viewFieldTalentProjectTitle(),
      "User with ReadOnly role is seeing incorrect Project Title values."
    );
  }

  /**
   * User should not have access to view the Creative Executive field.
   */
  public function atestCreativeExecutive() {
    $this->assertFalse(
      self::$contractObject->hasFieldTalentCreativeExecutiveViewAccess(),
      "User with ReadOnly role has access to view the Creative Executive field of a published contract."
    );
    $this->assertEquals(
      Utilities::getId(self::$fields['field_talent_creative_executive']),
      self::$contractObject->getFieldTalentCreativeExecutive(),
      "User with ReadOnly role is seeing incorrect Creative Executive values."
    );
    $this->assertNull(
      self::$contractObject->viewFieldTalentCreativeExecutive(),
      "User with ReadOnly role is seeing incorrect Creative Executive values."
    );
  }

  /**
   * User should not have access to view the Artist field.
   */
  public function atestArtist() {
    $this->assertFalse(
      self::$contractObject->hasFieldTalentArtistViewAccess(),
      "User with ReadOnly role has access to view the Artist field of a published contract."
    );

    $term_names = array();
    foreach (self::$fields['field_talent_artist'] as $val) {
      if (is_string($val)) {
        $term_names[] = $val;
      }
      else {
        $term_names[] = $val->getLabel();
      }
    }

    $this->assertEquals(
      $term_names,
      self::$contractObject->getFieldTalentArtist(),
      "User with ReadOnly role is seeing incorrect Artist values."
    );

    $this->assertNull(
      self::$contractObject->viewFieldTalentArtist(),
      "User with ReadOnly role is seeing incorrect Artist values."
    );
  }

  /**
   * User should not have access to view the Company/Loan Out field.
   */
  public function atestCompanyLoanOut() {
    $this->assertFalse(
      self::$contractObject->hasFieldTalentCompanyViewAccess(),
      "User with ReadOnly role has access to view the Company/Loan Out field of a published contract."
    );

    $term_names = array();
    foreach (self::$fields['field_talent_company'] as $val) {
      if (is_string($val)) {
        $term_names[] = $val;
      }
      else {
        $term_names[] = $val->getLabel();
      }
    }

    $this->assertEquals(
      $term_names,
      self::$contractObject->getFieldTalentCompany(),
      "User with ReadOnly role is seeing incorrect Company/Loan Out values."
    );

    $this->assertNull(
      self::$contractObject->viewFieldTalentCompany(),
      "User with ReadOnly role is seeing incorrect Company/Loan Out values."
    );
  }

  /**
   * User should not have access to view the Attorney field.
   */
  public function atestAttorney() {
    $this->assertFalse(
      self::$contractObject->hasFieldTalentAttorneyViewAccess(),
      "User with ReadOnly role has access to view the Attorney field of a published contract."
    );

    $term_names = array();
    foreach (self::$fields['field_talent_attorney'] as $val) {
      if (is_string($val)) {
        $term_names[] = $val;
      }
      else {
        $term_names[] = $val->getLabel();
      }
    }

    $this->assertEquals(
      $term_names,
      self::$contractObject->getFieldTalentAttorney(),
      "User with ReadOnly role is seeing incorrect Attorney values."
    );

    $this->assertNull(
      self::$contractObject->viewFieldTalentAttorney(),
      "User with ReadOnly role is seeing incorrect Attorney values."
    );
  }

  /**
   * User should not have access to view the Law Firm field.
   */
  public function atestLawFirm() {
    $this->assertFalse(
      self::$contractObject->hasFieldTalentLawFirmViewAccess(),
      "User with ReadOnly role has access to view the Law Firm field of a published contract."
    );

    $term_names = array();
    foreach (self::$fields['field_talent_law_firm'] as $val) {
      if (is_string($val)) {
        $term_names[] = $val;
      }
      else {
        $term_names[] = $val->getLabel();
      }
    }

    $this->assertEquals(
      $term_names,
      self::$contractObject->getFieldTalentLawFirm(),
      "User with ReadOnly role is seeing incorrect Law Firm values."
    );

    $this->assertNull(
      self::$contractObject->viewFieldTalentLawFirm(),
      "User with ReadOnly role is seeing incorrect Law Firm values."
    );
  }

  /**
   * User should not have access to view the Agent field.
   */
  public function atestAgent() {
    $this->assertFalse(
      self::$contractObject->hasFieldTalentAgentViewAccess(),
      "User with ReadOnly role has access to view the Agent field of a published contract."
    );

    $term_names = array();
    foreach (self::$fields['field_talent_agent'] as $val) {
      if (is_string($val)) {
        $term_names[] = $val;
      }
      else {
        $term_names[] = $val->getLabel();
      }
    }

    $this->assertEquals(
      $term_names,
      self::$contractObject->getFieldTalentAgent(),
      "User with ReadOnly role is seeing incorrect Agent values."
    );

    $this->assertNull(
      self::$contractObject->viewFieldTalentAgent(),
      "User with ReadOnly role is seeing incorrect Agent values."
    );
  }

  /**
   * User should not have access to view the Agency field.
   */
  public function atestAgency() {
    $this->assertFalse(
      self::$contractObject->hasFieldTalentAgencyViewAccess(),
      "User with ReadOnly role has access to view the Agency field of a published contract."
    );

    $term_names = array();
    foreach (self::$fields['field_talent_agency'] as $val) {
      if (is_string($val)) {
        $term_names[] = $val;
      }
      else {
        $term_names[] = $val->getLabel();
      }
    }

    $this->assertEquals(
      $term_names,
      self::$contractObject->getFieldTalentAgency(),
      "User with ReadOnly role is seeing incorrect Agency values."
    );

    $this->assertNull(
      self::$contractObject->viewFieldTalentAgency(),
      "User with ReadOnly role is seeing incorrect Agency Out values."
    );
  }

  /**
   * User should not have access to view the Services field.
   */
  public function atestServices() {
    $this->assertFalse(
      self::$contractObject->hasFieldTalentServicesViewAccess(),
      "User with ReadOnly role has access to view the Services field of a published contract."
    );
    $this->assertEquals(
      Utilities::getId(self::$fields['field_talent_services']),
      self::$contractObject->getFieldTalentServices(),
      "User with ReadOnly role is seeing incorrect Services values."
    );
    $this->assertNull(
      self::$contractObject->viewFieldTalentServices(),
      "User with ReadOnly role is seeing incorrect Services values."
    );
  }

  /**
   * User should not have access to view the BA Exec field.
   */
  public function atestBAExec() {
    $this->assertFalse(
      self::$contractObject->hasFieldTalentBaExecViewAccess(),
      "User with ReadOnly role has access to view the BA Exec field of a published contract."
    );
    $this->assertEquals(
      Utilities::getId(self::$fields['field_talent_ba_exec']),
      self::$contractObject->getFieldTalentBaExec(),
      "User with ReadOnly role is seeing incorrect BA Exec values."
    );
    $this->assertNull(
      self::$contractObject->viewFieldTalentBaExec(),
      "User with ReadOnly role is seeing incorrect BA Exec values."
    );
  }

  /**
   * User should not have access to view the Legal Exec field.
   */
  public function atestLegalExec() {
    $this->assertFalse(
      self::$contractObject->hasFieldTalentLegalExecViewAccess(),
      "User with ReadOnly role has access to view the Legal Exec field of a published contract."
    );
    $this->assertEquals(
      Utilities::getId(self::$fields['field_talent_legal_exec']),
      self::$contractObject->getFieldTalentLegalExec(),
      "User with ReadOnly role is seeing incorrect Legal Exec values."
    );
    $this->assertNull(
      self::$contractObject->viewFieldTalentLegalExec(),
      "User with ReadOnly role is seeing incorrect Legal Exec values."
    );
  }

  /**
   * User should not have access to view the Status field.
   */
  public function atestStatus() {
    $this->assertFalse(
      self::$contractObject->hasFieldTalentStatusViewAccess(),
      "User with ReadOnly role has access to view the Status field of a published contract."
    );
    $this->assertEquals(
      Utilities::getId(self::$fields['field_talent_status']),
      self::$contractObject->getFieldTalentStatus(),
      "User with ReadOnly role is seeing incorrect Status values."
    );
    $this->assertNull(
      self::$contractObject->viewFieldTalentStatus(),
      "User with ReadOnly role is seeing incorrect Status values."
    );
  }

  /**
   * User should not have access to view the Deal Terms field.
   */
  public function atestDealTerms() {
    $this->assertFalse(
      self::$contractObject->hasFieldTalentDealTermsViewAccess(),
      "User with ReadOnly role has access to view the Deal Terms field of a published contract."
    );
    $this->assertEquals(
      self::$fields['field_talent_deal_terms'],
      self::$contractObject->getFieldTalentDealTerms(),
      "User with ReadOnly role is seeing incorrect Deal Terms values."
    );
    $this->assertNull(
      self::$contractObject->viewFieldTalentDealTerms(),
      "User with ReadOnly role is seeing incorrect Deal Terms values."
    );
  }

  /**
   * User should not have access to view the Type Of Contract field. On saving
   * a new contract, type of contract should get set to "Talent" automatically.
   */
  public function atestTypeOfContract() {
    $this->assertFalse(
      self::$contractObject->hasFieldTypeOfContractViewAccess(),
      "User with ReadOnly role has access to view the Type Of Contract field of a published contract."
    );
    $this->assertEquals(
      18820,
      self::$contractObject->getFieldTypeOfContract(),
      "User with ReadOnly role is seeing incorrect Type Of Contract values."
    );
    $this->assertNull(
      self::$contractObject->viewFieldTypeOfContract(),
      "User with ReadOnly role is seeing incorrect Type Of Contract values."
    );
  }

  /**
   * User should not have access to view the Executed field. Since the original
   * contract is created by a user having TalentContributor role, Executed
   * field should be set to 1 using Rules.
   */
  public function atestExecuted() {
    $this->assertFalse(
      self::$contractObject->hasFieldExecutedViewAccess(),
      "User with ReadOnly role has access to view the Executed field of a published contract."
    );
    $this->assertEquals(
      1,
      self::$contractObject->getFieldExecuted(),
      "User with ReadOnly role is seeing incorrect Executed values."
    );
    $this->assertNull(
      self::$contractObject->viewFieldExecuted(),
      "User with ReadOnly role is seeing incorrect Executed values."
    );
  }

  /**
   * User should not have access to view the Contract Num field.
   */
  public function atestContractNum() {
    $this->assertFalse(
      self::$contractObject->hasFieldContractNumViewAccess(),
      "User with ReadOnly role has access to view the Contract Num field of a published contract."
    );
    $this->assertEquals(
      self::$fields['field_contract_num'],
      self::$contractObject->getFieldContractNum(),
      "User with ReadOnly role is seeing incorrect Contract Num values."
    );
    $this->assertNull(
      self::$contractObject->viewFieldContractNum(),
      "User with ReadOnly role is seeing incorrect Contract Num values."
    );
  }

  /**
   * User should not have access to view the Talent Notes field.
   */
  public function atestTalentNotes() {
    $this->assertFalse(
      self::$contractObject->hasFieldTalentNotesViewAccess(),
      "User with ReadOnly role has access to view the Talent Notes field of a published contract."
    );
    $this->assertEquals(
      self::$fields['field_talent_notes'],
      self::$contractObject->getFieldTalentNotes(),
      "User with ReadOnly role is seeing incorrect Talent Notes values."
    );
    $this->assertNull(
      self::$contractObject->viewFieldTalentNotes(),
      "User with ReadOnly role is seeing incorrect Talent Notes values."
    );
  }

  /**
   * User should have access to view the Group Id field.
   */
  public function atestGroupId() {
    $this->assertTrue(
      self::$contractObject->hasFieldTalentGroupIdViewAccess(),
      "User with ReadOnly role does not have access to view the Group Id field of a published contract."
    );
    $this->assertEquals(
      self::$fields['field_talent_group_id'],
      self::$contractObject->getFieldTalentGroupId(),
      "User with ReadOnly role is seeing incorrect Group Id values."
    );
    $this->assertEquals(
      array_map(
        function ($val) { return "Legal Contract " . $val; },
        self::$fields['field_talent_group_id']
      ),
      self::$contractObject->viewFieldTalentGroupId(),
      "User with ReadOnly role is seeing incorrect Group Id values."
    );
  }

  /**
   * User should not have access to view the Talent Type Of Agreements field.
   */
  public function atestTalentTypeOfAgreements() {
    $this->assertFalse(
      self::$contractObject->hasFieldTalentTypeOfAgreementsViewAccess(),
      "User with ReadOnly role has access to view the Talent Type Of Agreements field of a published contract."
    );
    $this->assertEquals(
      self::$fields['field_talent_type_of_agreements']->getId(),
      self::$contractObject->getFieldTalentTypeOfAgreements(),
      "User with ReadOnly role is seeing incorrect Talent Type Of Agreements values."
    );
    $this->assertNull(
      self::$contractObject->viewFieldTalentTypeOfAgreements(),
      "User with ReadOnly role is seeing incorrect Talent Type Of Agreements values."
    );
  }

  /**
   * Delete the node object from the database.
   */
  public static function tearDownAfterClass() {

    global $entities;

    if (!empty($entities)) {
      foreach ($entities as $key => $val) {
        /**
         * @var  $entity_id int
         * @var  $object Entity
         */
        foreach ($val as $entity_id => $object) {
          $object->delete();
        }
      }
    }

    $query = new \EntityFieldQuery();
    $results = $query->entityCondition('entity_type', 'node')
      ->entityCondition('entity_id', 103931, '>=')
      ->execute();
    if (isset($results['node'])) {
      $nids = array_keys($results['node']);
      node_delete_multiple($nids);
    }

    $query = new \EntityFieldQuery();
    $results = $query->entityCondition('entity_type', 'taxonomy_term')
      ->entityCondition('entity_id', 19066, '>=')
      ->execute();
    if (isset($results['taxonomy_term'])) {
      $tids = array_keys($results['taxonomy_term']);
      foreach ($tids as $tid) {
        taxonomy_term_delete($tid);
      }
    }

    self::$userObject->logout();
  }
}