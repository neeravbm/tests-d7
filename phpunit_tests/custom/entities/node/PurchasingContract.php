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
 * Class PurchasingContract
 *
 * @package tests\phpunit_tests\helper\entities
 */
final class PurchasingContract extends Node {

  /*public static $fields = array(
    'field_contract_files' => array(
      'type' => 'file',
      'widget' => 'file_generic',
    ),
    'field_contracting_family' => array(
      'type' => 'taxonomy_term_reference',
      'widget' => 'taxonomy_shs',
    ),
    'field_contract_company' => array(
      'type' => 'taxonomy_term_reference',
      'widget' => 'taxonomy_autocomplete',
    ),
    'field_netflix_legal_entity' => array(
      'type' => 'taxonomy_term_reference',
      'widget' => 'options_select',
    ),
    'field_mfn' => array(
      'type' => 'taxonomy_term_reference',
      'widget' => 'options_select',
    ),
    'field_contract_type' => array(
      'type' => 'taxonomy_term_reference',
      'widget' => 'options_select',
    ),
    'field_exclusivity_holdback' => array(
      'type' => 'list_boolean',
      'widget' => 'options_onoff',
    ),
    'field_security' => array(
      'type' => 'list_boolean',
      'widget' => 'options_onoff',
    ),
    'field_output' => array(
      'type' => 'list_boolean',
      'widget' => 'options_onoff',
    ),
    'field_pro' => array(
      'type' => 'list_boolean',
      'widget' => 'options_onoff',
    ),
    'field_guaranty' => array(
      'type' => 'list_boolean',
      'widget' => 'options_onoff',
    ),
    'field_effective_date' => array(
      'type' => 'datetime',
      'widget' => 'date_popup',
    ),
    'field_business_contact' => array(
      'type' => 'taxonomy_term_reference',
      'widget' => 'taxonomy_autocomplete',
    ),
    'field_master_contract' => array(
      'type' => 'entityreference',
      'widget' => 'entityreference_view_widget',
    ),
    'field_department' => array(
      'type' => 'taxonomy_term_reference',
      'widget' => 'options_select',
    ),
    'field_territory' => array(
      'type' => 'taxonomy_term_reference',
      'widget' => 'term_reference_tree',
    )
  );*/

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