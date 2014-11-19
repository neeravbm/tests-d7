<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 11/16/14
 * Time: 11:37 AM
 */

namespace tests\phpunit_tests\helper\forms;

use tests\phpunit_tests\helper\entities as entities;

class ArticleForm extends NodeForm {

  public function __construct($nid = NULL) {
    parent::__construct($nid);
  }
} 