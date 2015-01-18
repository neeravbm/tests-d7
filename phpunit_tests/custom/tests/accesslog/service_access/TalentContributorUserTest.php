<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 12/21/14
 * Time: 12:04 PM
 */

namespace tests\phpunit_tests\custom\tests\Accesslog\ServiceAccess;

use tests\phpunit_tests\core\entities\User;
use tests\phpunit_tests\core\Service;
use tests\phpunit_tests\core\Utilities;
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
 * @task SHER-53
 * @test SHER-74
 * @package tests\phpunit_tests\custom\tests\Accesslog\ServiceAccess
 */
class TalentContributorUserTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var Contract
   */
  private static $contractObject;

  /**
   * @var array
   */
  private static $file_paths;

  /**
   * Administrator user name.
   */
  CONST ADMINISTRATOR_USER = 'neeravm';

  /**
   * Talent Contributor user name.
   */
  CONST TALENT_CONTRIBUTOR_USER = 'karenal';

  /**
   * Truncate the accesslog table.
   * Create a contract as an administrator.
   * Log in as a Legal Contributor user and download files associated with the
   * contract.
   */
  public static function setUpBeforeClass() {
    // Log in as a Talent Contributor user.
    db_truncate('accesslog')->execute();

    self::createContractAsAdministratorUser();

    $userObject = User::loginProgrammatically(self::TALENT_CONTRIBUTOR_USER);
    self::simulateFileDownloadAction();
    $userObject->logout();

    // Store file paths in a static array so that it can be used later.
    self::storeFilePaths();
  }

  /**
   * Create a contract.
   */
  private static function createContractAsAdministratorUser() {
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
   * Simulate file download action.
   */
  private static function simulateFileDownloadAction() {
    $_SERVER['HTTP_REFERER'] = Utilities::getRandomString();

    // Iterate over the files and execute drupal_page_footer() once for each.
    $files = self::$contractObject->getFieldContractFiles(FALSE);
    foreach ($files as $key => $file) {
      $_GET['q'] = str_replace('private://', 'system/files/', $file['uri']);
      // drupal_page_footer() executes hook_exit(). Statistics module uses
      // hook_exit() to record values in accesslog.
      drupal_page_footer();
    }
  }

  /**
   * Stores file paths from created Contract in an array so that it can be used
   * later for comparison.
   */
  private static function storeFilePaths() {
    $files = self::$contractObject->getFieldContractFiles(FALSE);
    self::$file_paths = array();
    foreach ($files as $file) {
      self::$file_paths[] = str_replace(
        'private://',
        'system/files/',
        $file['uri']
      );
    }
  }

  /**
   * Make sure that the user is not able to view the Service.
   *
   * @task SHER-53
   * @test SHER-74
   */
  public function testFileDownloadEntryInAccesslog() {
    $userObject = User::loginProgrammatically(self::TALENT_CONTRIBUTOR_USER);

    $service = new Service('rest');
    $response = json_decode($service->execute('views/accesslog.json'));
    $this->assertStringStartsWith(
      "Access denied for user ",
      $response[0],
      "User was not denied access to the Accesslog service."
    );

    $userObject->logout();
  }

  /**
   * Make sure that the service returns the expected accesslog when accessed by
   * an administrator user.
   *
   * @task SHER-53
   * @test SHER-74
   */
  public function testFileDownloadEntryInAccesslogAsAdministratorUser() {
    $userObject = User::loginProgrammatically(self::ADMINISTRATOR_USER);
    $this->checkServiceResponse(self::$file_paths);
    $userObject->logout();
  }

  /**
   * Make sure that the service returns the expected accesslog when accessed by
   * an administrator user with all filters filled correctly.
   *
   * @task SHER-53
   * @test SHER-74
   */
  public function testAllFiltersResponse() {
    $userObject = User::loginProgrammatically(self::ADMINISTRATOR_USER);
    $options = array(
      'userid' => self::TALENT_CONTRIBUTOR_USER,
      'timestamp' => array(
        'min' => '-1 day',
        'max' => '1 day',
      )
    );
    $this->checkServiceResponse(self::$file_paths, $options);
    $userObject->logout();
  }

  /**
   * Make sure that the service returns the expected accesslog when accessed by
   * an administrator user with missing min date filter and all other filters
   * filled correctly.
   *
   * @task SHER-53
   * @test SHER-74
   */
  public function testMissingMinDateFilterResponse() {
    $userObject = User::loginProgrammatically(self::ADMINISTRATOR_USER);
    // Make sure that missing min date with correct max date returns expected
    // response.
    $options = array(
      'userid' => self::TALENT_CONTRIBUTOR_USER,
      'timestamp' => array(
        'max' => '1 day',
      )
    );
    $this->checkServiceResponse(self::$file_paths, $options);
    $userObject->logout();
  }

  /**
   * Make sure that the service returns the expected accesslog when accessed by
   * an administrator user with missing max date filter and all other filters
   * filled correctly.
   *
   * @task SHER-53
   * @test SHER-74
   */
  public function testMissingMaxDateFilterResponse() {
    $userObject = User::loginProgrammatically(self::ADMINISTRATOR_USER);
    // Make sure that missing max date with correct min date returns expected
    // response.
    $options = array(
      'userid' => self::TALENT_CONTRIBUTOR_USER,
      'timestamp' => array(
        'min' => '-1 day',
      )
    );
    $this->checkServiceResponse(self::$file_paths, $options);
    $userObject->logout();
  }

  /**
   * Make sure that the service returns the expected accesslog when accessed by
   * an administrator user with missing username filter and all other filters
   * filled correctly.
   *
   * @task SHER-53
   * @test SHER-74
   */
  public function testMissingUsernameFilterResponse() {
    $userObject = User::loginProgrammatically(self::ADMINISTRATOR_USER);
    // Make sure that missing username with correct min and max dates returns
    // expected response.
    $options = array(
      'timestamp' => array(
        'min' => '-1 day',
        'max' => '1 day',
      )
    );
    $this->checkServiceResponse(self::$file_paths, $options);
    $userObject->logout();
  }

  /**
   * Make sure that the service returns no accesslog entry when accessed by
   * an administrator user with wrong min date filter and all other filters
   * filled correctly.
   *
   * @task SHER-53
   * @test SHER-74
   */
  public function testWrongMinDateFilterResponse() {
    $userObject = User::loginProgrammatically(self::ADMINISTRATOR_USER);
    // Make sure that wrong min timestamp filter results in empty response.
    $options = array(
      'userid' => self::TALENT_CONTRIBUTOR_USER,
      'timestamp' => array(
        'min' => '1 day',
        'max' => '2 day',
      ),
    );
    $this->checkServiceResponse(array(), $options);
    $userObject->logout();
  }

  /**
   * Make sure that the service returns no accesslog entry when accessed by
   * an administrator user with wrong min date filter and all other filters
   * filled correctly.
   *
   * @task SHER-53
   * @test SHER-74
   */
  public function testWrongMaxDateFilterResponse() {
    $userObject = User::loginProgrammatically(self::ADMINISTRATOR_USER);
    // Make sure that wrong max timestamp filter results in empty response.
    $options = array(
      'userid' => self::TALENT_CONTRIBUTOR_USER,
      'timestamp' => array(
        'min' => '-2 day',
        'max' => '-1 day',
      ),
    );
    $this->checkServiceResponse(array(), $options);
    $userObject->logout();
  }

  /**
   * Make sure that the service returns no accesslog entry when accessed by
   * an administrator user with wrong username filter and all other filters
   * filled correctly.
   *
   * @task SHER-53
   * @test SHER-74
   */
  /*public function testRandomUsernameResponse() {
    $userObject = User::loginProgrammatically(self::ADMINISTRATOR_USER);
    // Make sure that wrong username filter results in empty response.
    $options = array(
      'userid' => Utilities::getRandomString(),
      'timestamp' => array(
        'min' => '-1 day',
        'max' => '1 day',
      )
    );
    $this->checkServiceResponse(array(), $options);
    $userObject->logout();
  }*/

  /**
   * Delete the entities created in this test scenario.
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

    db_truncate('accesslog')->execute();

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

  /**
   * Checks whether the URLs in service response matches expected file paths.
   *
   * @param array $file_paths
   *   Expected file paths array.
   * @param array $options
   *   Options to be provided to the Service.
   */
  private function checkServiceResponse($file_paths, $options = array()) {
    $service = new Service('rest');
    $response = json_decode(
      $service->execute('views/accesslog.json', $options)
    );

    $response_paths = array();
    foreach ($response as $record) {
      $response_paths[] = $record->path;
    }

    sort($file_paths);
    sort($response_paths);

    $this->assertEquals(
      $file_paths,
      $response_paths,
      "Items in accesslog don't match the expected file accesses: " . serialize(
        $options
      )
    );
  }
}