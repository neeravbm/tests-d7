<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 11/12/14
 * Time: 8:20 PM
 */

namespace tests\phpunit_tests\custom\entities\node;

use tests\phpunit_tests\core\entities\Node as Node;

/**
 * Class Contract
 * @package tests\phpunit_tests\helper\entities
 */
final class Contract extends Node {

  /**
   * Default constructor.
   *
   * @param int $nid
   *   Node id if an existing node needs to be loaded.
   */
  public function __construct($nid = NULL) {
    parent::__construct($nid);
  }
}