<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 11/12/14
 * Time: 5:47 PM
 */

namespace tests\phpunit_tests;

use tests\phpunit_tests\helper\entities as entities;
use tests\phpunit_tests\helper\forms as forms;

define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_override_server_variables();
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

class ContractWorkflowAdministratorUserTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var entities\User
   */
  private static $userObject;

  /**
   * @var entities\Entity
   */
  private static $nodeObject;

  /**
   * Log in as "neemehta" who has administrator rights.
   */
  public static function setUpBeforeClass() {
    // Log in as an admin user.
    self::$userObject = entities\User::loginProgrammatically('neemehta');
  }

  /**
   * Contract should get created.
   */
  public function testContractCreation() {
    $contractForm = new forms\ContractForm();
    $contractForm->fillTitle('Test contract');
    $output = $contractForm->submit();
    $this->assertTrue($output, "Contract could not be created.");
    if ($output) {
      self::$nodeObject = $contractForm->getEntityObject();
    }
  }

  /**
   * @depends testContractCreation
   */
  public function testContractTitle() {
    $this->assertEquals('contract ' . self::$nodeObject->getId(), self::$nodeObject->getTitle(), "Contract title does not match.");
  }

  /**
   * Select Group ID field should be set to node id.
   *
   * @depends testContractCreation
   */
  public function testSelectGroupIDFieldIsSet() {
    $master_contract = self::$nodeObject->getFieldMasterContract();
    $this->assertCount(1, $master_contract, "Select Group ID does not have 1 reference.");
    $this->assertEquals(self::$nodeObject->getId(), $master_contract[0], "Select Group ID does not match the contract ID.");
  }

  /**
   * Delete the node object from the database.
   */
  public static function tearDownAfterClass() {
    if (is_object(self::$nodeObject)) {
      node_delete(self::$nodeObject->getId());
    }

    self::$userObject->logout();
  }
}