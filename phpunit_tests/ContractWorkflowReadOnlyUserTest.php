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
use tests\phpunit_tests\custom\entities\nodes\Contract as Contract;
use tests\phpunit_tests\custom\forms\entities\nodes\ContractForm as ContractForm;
use tests\phpunit_tests\custom\entities\terms\ContractedFamily as ContractedFamily;
use tests\phpunit_tests\custom\forms\entities\terms\ContractedFamilyForm as ContractedFamilyForm;

define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_override_server_variables();
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

class ContractWorkflowReadOnlyUserTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var ContractedFamily
   */
  private static $contractedFamilyObject;

  /**
   * @var User
   */
  private static $userObject;

  /**
   * @var Contract
   */
  private static $nodeObject;

  /**
   * Log in as "neemehta" who has administrator rights.
   */
  public static function setUpBeforeClass() {
    // Log in as an admin user and create a contract.
    $userObject = User::loginProgrammatically('neeravm');

    $contractedFamilyForm = new ContractedFamilyForm();
    $contractedFamilyForm->fillName(Utilities::getRandomString());
    $output = $contractedFamilyForm->submit();
    self::assertTrue(
      $output,
      "Administrator user could not create a Contracted Family term."
    );
    if ($output) {
      self::$contractedFamilyObject = $contractedFamilyForm->getEntityObject();
    }

    $contractForm = new ContractForm();
    /*$contractForm->fillFieldContractingFamily(
      array(32, self::$contractedFamilyObject->getId())
    );*/
    $contractForm->fillFieldContractFiles('Contract Delivery.pdf');
    $output = $contractForm->submit();
    self::assertTrue(
      $output,
      "Administrator user could not create a contract."
    );
    if ($output) {
      self::$nodeObject = $contractForm->getEntityObject();
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
      self::$nodeObject->hasUpdateAccess(),
      "User with ReadOnly role has access to edit the contract."
    );
  }

  /**
   * User should not access to delete an existing contract.
   */
  public function testContractDeleteAccess() {
    $this->assertFalse(
      self::$nodeObject->hasDeleteAccess(),
      "User with ReadOnly role has access to delete the contract."
    );
  }

  /**
   * User should have access to view an existing published contract.
   */
  public function testContractViewAccess() {
    $this->assertTrue(
      self::$nodeObject->hasViewAccess(),
      "User with ReadOnly role does not have access to view a published contract."
    );
  }

  /**
   * User should have access to view Contracting Family field.
   */
  public function testFieldContractingFamilyAccess() {
    $this->assertTrue(
      self::$nodeObject->hasFieldContractingFamilyViewAccess(),
      "User with ReadOnly role does not have access to view the Contracting Family field of a published contract."
    );
    $this->assertEquals(
      array(32, self::$contractedFamilyObject->getId()),
      self::$nodeObject->getFieldContractingFamily(),
      "User with ReadOnly role is seeing incorrect Contract Family values."
    );
  }

  /**
   * User should have access to view Contracting Family field.
   */
  public function testFieldContractFilesAccess() {
    $this->assertTrue(
      self::$nodeObject->hasFieldContractingFilesViewAccess(),
      "User with ReadOnly role does not have access to view the Contract Files field of a published contract."
    );
  }

  /**
   * Delete the node object from the database.
   */
  public static function tearDownAfterClass() {
    /*if (is_object(self::$nodeObject)) {
      node_delete(self::$nodeObject->getId());
    }*/

    /*if (is_object(self::$contractedFamilyObject)) {
      taxonomy_term_delete(self::$contractedFamilyObject->getId());
    }*/

    self::$userObject->logout();
  }
}