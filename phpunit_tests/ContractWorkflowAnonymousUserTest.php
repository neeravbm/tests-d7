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

class ContractWorkflowAnonymousUserTest extends \PHPUnit_Framework_TestCase {

  private static $contractObject;

  /**
   * Log in as "neemehta" who has administrator rights, create a contract node and log out.
   */
  public static function setUpBeforeClass() {
    // Log in as an admin user.
    $userObject = entities\User::loginProgrammatically('neemehta');
    $contractForm = new forms\ContractForm();
    $contractForm->fillContractName('Test contract');
    $nid = $contractForm->submit();
    self::$contractObject = new entities\Contract($nid);
    $userObject->logout();
  }

  /**
   * Anonymous user should not have create access.
   */
  public function testContractCreateAccess() {
    $this->assertFalse(
      entities\Contract::hasCreateAccess(),
      "Anonymous user is able to create a contract."
    );
  }

  /**
   * Anonymous user should not have view access.
   */
  public function testContractViewAccess() {
    $this->assertFalse(
      self::$contractObject->hasViewAccess(),
      "Anonymous user is able to view the contract."
    );
  }

  /**
   * Anonymous user should not have edit access.
   */
  public function testContractEditAccess() {
    $this->assertFalse(
      self::$contractObject->hasEditAccess(),
      "Anonymous user is able to edit the contract."
    );
  }

  /**
   * Anonymous user should not have create access.
   */
  public function testContractDeleteAccess() {
    $this->assertFalse(
      self::$contractObject->hasDeleteAccess(),
      "Anonymous user is able to delete the contract."
    );
  }

  /**
   * Delete the node object from the database.
   */
  public static function tearDownAfterClass() {
    if (!is_object(self::$contractObject)) {
      return;
    }

    self::$contractObject->delete();
  }
}