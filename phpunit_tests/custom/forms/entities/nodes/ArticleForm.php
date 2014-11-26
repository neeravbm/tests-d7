<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 11/16/14
 * Time: 11:37 AM
 */

namespace tests\phpunit_tests\custom\forms\entities\nodes;

use tests\phpunit_tests\core\forms\NodeForm as NodeForm;

class ArticleForm extends NodeForm {

  public function __construct($nid = NULL) {
    parent::__construct($nid);
  }
}