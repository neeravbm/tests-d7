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
use tests\phpunit_tests\custom\forms\entities\node\ContractForm as ContractForm;

define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_override_server_variables();
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

class ContractWorkflowReadOnlyUserTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var array
   */
  private static $entities;

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

  /**
   * Log in as "neemehta" who has administrator rights.
   */
  public static function setUpBeforeClass() {
    // Log in as an admin user and create a contract.
    $userObject = User::loginProgrammatically('neeravm');

    self::$entities = array();

    $contractForm = new ContractForm();
    list($output, self::$fields, $msg) = $contractForm->fillDefaultValues(
      self::$entities
    );
    self::assertTrue($output, $msg);

    $output = $contractForm->submit();
    self::assertTrue(
      $output,
      "Administrator user could not create a contract."
    );
    if ($output) {
      self::$contractObject = $contractForm->getEntityObject();
    }
    $userObject->logout();

    // Log in as a ReadOnly user.
    self::$userObject = User::loginProgrammatically('asheth');
  }

  /**
   * User should not have access to create a new contract.
   */
  public function testContractCreationAccess() {
    $this->assertFalse(
      Contract::hasCreateAccess(),
      "User with ReadOnly role has access to create contract."
    );
  }

  /**
   * User should not access to update an existing contract.
   */
  public function testContractUpdateAccess() {
    $this->assertFalse(
      self::$contractObject->hasUpdateAccess(),
      "User with ReadOnly role has access to edit the contract."
    );
  }

  /**
   * User should not access to delete an existing contract.
   */
  public function testContractDeleteAccess() {
    $this->assertFalse(
      self::$contractObject->hasDeleteAccess(),
      "User with ReadOnly role has access to delete the contract."
    );
  }

  /**
   * User should have access to view an existing published contract.
   */
  public function testContractViewAccess() {
    $this->assertTrue(
      self::$contractObject->hasViewAccess(),
      "User with ReadOnly role does not have access to view a published contract."
    );
  }

  /**
   * User should have access to view the Contracting Family field.
   */
  public function testFieldContractingFamilyAccess() {
    $this->assertTrue(
      self::$contractObject->hasFieldContractingFamilyViewAccess(),
      "User with ReadOnly role does not have access to view the Contracting Family field of a published contract."
    );
    /*$this->assertEquals(
      array(32, self::$contractedFamilyObject->getId()),
      self::$contractObject->getFieldContractingFamily(),
      "User with ReadOnly role is seeing incorrect Contract Family values."
    );*/
  }

  /**
   * User should have access to view the Contracting Family field.
   */
  public function testFieldContractFilesAccess() {
    $this->assertTrue(
      self::$contractObject->hasFieldContractFilesViewAccess(),
      "User with ReadOnly role does not have access to view the Contract Files field of a published contract."
    );
  }

  /**
   * User should have access to view the Contract Company field.
   */
  public function testFieldContractCompany() {
    $this->assertTrue(
      self::$contractObject->hasFieldContractCompanyViewAccess(),
      "User with ReadOnly role does not have access to view the Contract Company field of a published contract."
    );
    $this->assertEquals(
      Utilities::getId(self::$fields['field_contract_company']),
      self::$contractObject->getFieldContractCompany(),
      "User with ReadOnly role is seeing incorrect Contract Company values."
    );
    $this->assertEquals(
      Utilities::getLabel(self::$fields['field_contract_company']),
      self::$contractObject->viewFieldContractCompany(),
      "User with ReadOnly role is seeing incorrect Contract Company values."
    );
  }

  /**
   * User should have access to view the Contract Type field.
   */
  public function testFieldContractType() {
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
  public function testFieldContractName() {
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
  public function testLegalId() {
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
  public function testEffectiveDate() {
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
  public function testBusinessContact() {
    $this->assertTrue(
      self::$contractObject->hasFieldBusinessContactViewAccess(),
      "User with ReadOnly role does not have access to view the Business Contact field of a published contract."
    );
    $this->assertEquals(
      self::$fields['field_business_contact']->getId(),
      self::$contractObject->getFieldBusinessContact(),
      "User with ReadOnly role is seeing incorrect Business Contact values."
    );
    $this->assertEquals(
      self::$fields['field_business_contact']->getLabel(),
      self::$contractObject->viewFieldBusinessContact(),
      "User with ReadOnly role is seeing incorrect Business Contact values."
    );
  }

  /**
   * User should have access to view the Department field.
   */
  public function testDepartment() {
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
  public function testNetflixLegalEntity() {
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
  public function testMFN() {
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
   *
   */
  public function testMasterContract() {
    $this->assertTrue(
      self::$contractObject->hasFieldMasterContractViewAccess(),
      "User with ReadOnly role does not have access to view the Master Contract field of a published contract."
    );
    $this->assertEquals(
      Utilities::getId(self::$fields['field_master_contract']),
      self::$contractObject->getFieldMasterContract(),
      "User with ReadOnly role is seeing incorrect Master Contract values."
    );
    $this->assertEquals(
      Utilities::getId(self::$fields['field_master_contract']),
      self::$contractObject->viewFieldMasterContract(),
      "User with ReadOnly role is seeing incorrect Master Contract values."
    );
  }

  public function testExclusivityHoldback() {
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

  public function testSecurity() {
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

  public function testOutput() {
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

  public function testPro() {
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

  public function testGuaranty() {
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

  public function testArtist() {
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

    $this->assertEquals(
      $term_names,
      self::$contractObject->viewFieldTalentArtist(),
      "User with ReadOnly role is seeing incorrect Artist values."
    );
  }

  /**
   * Delete the node object from the database.
   */
  public static function tearDownAfterClass() {

    if (!empty(self::$entities)) {
      foreach (self::$entities as $key => $val) {
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