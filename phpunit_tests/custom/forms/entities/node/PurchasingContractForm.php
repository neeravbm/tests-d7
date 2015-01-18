<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 11/12/14
 * Time: 5:40 PM
 */

namespace tests\phpunit_tests\custom\forms\entities\node;

use tests\phpunit_tests\core\forms\NodeForm as NodeForm;

class PurchasingContractForm extends NodeForm {

  public function __construct($nid = NULL) {
    parent::__construct($nid);
  }

  public function fillDefaultFieldServiceStartDate() {
    return array(
      LANGUAGE_NONE => array(
        0 => array(
          'value' => '2015-01-15',
          'timezone' => 'America/Los_Angeles',
          'timezone_db' => 'America/Los_Angeles',
          'date_type' => 'datetime',
          'show_todate' => FALSE,
        )
      )
    );
  }

}