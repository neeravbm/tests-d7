<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 1/13/15
 * Time: 8:31 PM
 */

namespace tests\phpunit_tests\custom\tests\contract\crud;

use tests\phpunit_tests\core\entities\User;
use tests\phpunit_tests\core\forms\NodeDeleteConfirm;
use tests\phpunit_tests\custom\entities\node\Contract;

/**
 * Drupal root directory.
 */
define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_override_server_variables();
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

/**
 * Class TalentContributorUserTest
 *
 * @package tests\phpunit_tests\custom\tests\contract\crud
 */
class TalentContributorUserTest extends \PHPUnit_Framework_TestCase {

  /**
   * Administrator user name.
   */
  CONST ADMINISTRATOR_USER = 'neeravm';

  /**
   * Talent Contributor user name.
   */
  CONST TALENT_CONTRIBUTOR_USER = 'karenal';

  /**
   * @var Contract
   */
  private static $contractObject;

  /**
   * Log in as administrator.
   * Create a contract.
   * Log out.
   */
  public static function setupBeforeClass() {
    $userObject = User::loginProgrammatically(self::ADMINISTRATOR_USER);

    $skip = array(
      'field_contracting_family',
      'field_territory',
    );
    list($success, self::$contractObject, $msg) = Contract::createDefault(
      1,
      $skip
    );
    self::assertTrue($success, $msg);

    $userObject->logout();
  }

  /**
   * Make sure that user is able to delete the purchasing contract.
   */
  public function testDelete() {
    $userObject = User::loginProgrammatically(self::TALENT_CONTRIBUTOR_USER);

    $this->assertTrue(
      self::$contractObject->hasDeleteAccess(),
      "User does not have delete access."
    );

    $deleteForm = new NodeDeleteConfirm(self::$contractObject->getId());
    $output = $deleteForm->delete();
    $this->assertTrue(
      $output,
      "User is not able to delete the node: " . implode(
        ", ",
        $deleteForm->getErrors()
      )
    );
    $userObject->logout();
  }

  /**
   * @throws \Exception
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
  }

}