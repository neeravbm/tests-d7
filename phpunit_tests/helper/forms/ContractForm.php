<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 11/12/14
 * Time: 5:40 PM
 */

namespace tests\phpunit_tests\helper\forms;

final class ContractForm extends NodeForm {

  const TYPE = 'contract';

  /**
   * Default constructor.
   *
   * @param mixed $nid
   *   Nid of the contract to be loaded in the form. Leave blank for an add form.
   * @param object $nodeObject
   *   Node object if an edit form is to be loaded. Leave blank if article add form is to be loaded or if you have supplied $nid.
   */
  public function __construct($nid = FALSE, $nodeObject = NULL) {
    if ($nid && is_numeric($nid)) {
      return parent::__construct($nid);
    }
    else {
      return parent::__construct(self::TYPE, $nodeObject);
    }
  }

  /**
   * Fill contract name.
   *
   * @param string $contract_name
   *   Name of the contract.
   */
  function fillContractName($contract_name) {
    $this->fillTextField('field_contract_name', $contract_name);
  }
}