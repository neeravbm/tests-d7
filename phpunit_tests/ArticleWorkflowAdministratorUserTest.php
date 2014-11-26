<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 11/16/14
 * Time: 11:46 AM
 */

namespace tests\phpunit_tests;

use tests\phpunit_tests\helper\entities as entities;
use tests\phpunit_tests\helper\forms as forms;

define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_override_server_variables();
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

class ArticleWorkflowAdministratorUserTest extends \PHPUnit_Framework_TestCase {

  private static $nodeObject;
  private static $userObject;

  /**
   * Log in as "admin" who has administrator rights.
   */
  public static function setUpBeforeClass() {
    // Log in as an admin user.
    self::$userObject = entities\User::loginProgrammatically('admin');
  }

  /**
   * Article should get created.
   */
  public function testArticleCreation() {
    $articleForm = new forms\ArticleForm();
    $articleForm->fillTitle('Test article');
    /*$articleForm->fillBody(
      array(
        0 => array(
          'value' => 'Testing article body.',
          'summary' => 'summary',
          'format' => 'filtered_html',
        ),
      )
    );*/
    //$articleForm->fillBody('Testing article body.');
    $articleForm->fillBody(
      '<p>Testing article body.</p><script type="text/javascript">console.log("123");</script>',
      '',
      'filtered_html'
    );
    $output = $articleForm->submit();
    $this->assertTrue($output, "Article could not be created.");
    if ($output) {
      self::$nodeObject = $articleForm->getEntityObject();
    }
  }

  /**
   * Test that title of the article matches the value set.
   *
   * @depends testArticleCreation
   */
  public function testTitleFieldValue() {
    $this->assertEquals(
      'Test article',
      self::$nodeObject->getTitle(),
      "Article title does not match."
    );
  }

  /**
   * Test that body of the article matches the value set.
   *
   * @depends testArticleCreation
   */
  public function testBodyFieldValue() {
    $body = self::$nodeObject->getBody();
    $this->assertEquals(
      1,
      sizeof($body),
      "Number of values in body field is not 1."
    );
    $this->assertEquals(
      "<p>Testing article body.console.log(\"123\");</p>\n",
      $body[0],
      "Article body does not match."
    );
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