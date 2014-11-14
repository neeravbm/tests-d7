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

  private static $userObject;
  private static $node;

  /**
   * Log in as "neemehta" who has administrator rights.
   */
  public static function setUpBeforeClass() {
    // Log in as an admin user.
    self::$userObject = entities\User::loginProgrammatically('neemehta');
  }

  /**
   * Contract should get created.
   *
   * @return mixed $nid
   *   Node id if the contract was created and an array of errors, if not.
   */
  public function testContractCreation() {
    $contractForm = new forms\ContractForm();
    $contractForm->fillContractName('Test contract');
    $nid = $contractForm->submit();
    $this->assertInternalType(
      'string',
      gettype($nid),
      "Contract could not be created."
    );

    return $nid;
  }

  /**
   * Select Group ID field should be set to node id.
   *
   * @param int $nid
   *   Node id.
   *
   * @depends testContractCreation
   */
  public function testSelectGroupIDFieldIsSet($nid) {
    /*$nodeObject = new entities\Contract($nid);
    $master_contract = $nodeObject->getFieldMasterContract();*/
    self::$node = node_load($nid);
    $master_contract = field_get_items(
      'node',
      self::$node,
      'field_master_contract'
    );
    $this->assertEquals(
      1,
      sizeof($master_contract),
      "Select Group ID does not have 1 reference."
    );
    $this->assertEquals($nid, $master_contract[0]['target_id']);
  }

  /**
   * Delete the node object from the database.
   */
  public static function tearDownAfterClass() {
    if (!is_object(self::$node)) {
      return;
    }

    node_delete(self::$node->nid);

    self::$userObject->logout();
  }
}