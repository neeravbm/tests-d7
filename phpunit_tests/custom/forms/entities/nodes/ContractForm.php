<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 11/12/14
 * Time: 5:40 PM
 */

namespace tests\phpunit_tests\custom\forms\entities\nodes;

use tests\phpunit_tests\core\forms\NodeForm as NodeForm;

class ContractForm extends NodeForm {

  public function __construct($nid = NULL) {
    parent::__construct($nid);
  }
}