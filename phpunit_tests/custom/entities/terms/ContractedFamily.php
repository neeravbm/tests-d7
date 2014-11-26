<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 11/19/14
 * Time: 10:55 PM
 */

namespace tests\phpunit_tests\custom\entities\terms;

use tests\phpunit_tests\core\entities\TaxonomyTerm as TaxonomyTerm;

class ContractedFamily extends TaxonomyTerm {

  /**
   * Default constructor.
   *
   * @param int $tid
   *   Node id if an existing node needs to be loaded.
   */
  public function __construct($tid = NULL) {
    parent::__construct($tid);
  }
}