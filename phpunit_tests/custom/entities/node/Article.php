<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 11/16/14
 * Time: 11:37 AM
 */

namespace tests\phpunit_tests\custom\entities\node;

use tests\phpunit_tests\core\entities\Node as Node;

class Article extends Node {

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