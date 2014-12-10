<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 11/19/14
 * Time: 9:45 PM
 */

namespace tests\phpunit_tests\custom\forms\entities\taxonomy_term;

use tests\phpunit_tests\core\forms\TaxonomyFormTerm as TaxonomyFormTerm;

class ContractedFamilyForm extends TaxonomyFormTerm {

  /**
   * Default constructor.
   *
   * @param int $tid
   *   TaxonomyTerm id if an existing term needs to be loaded.
   */
  public function __construct($tid = NULL) {
    parent::__construct($tid);
  }
}