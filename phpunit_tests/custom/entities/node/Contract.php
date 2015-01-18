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
 *
 * @package tests\phpunit_tests\helper\entities
 */
final class Contract extends Node {

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

  public static $talentFields = array(
    'field_talent_project_title',
    'field_talent_creative_executive',
    'field_talent_artist',
    'field_talent_company',
    'field_talent_attorney',
    'field_talent_law_firm',
    'field_talent_agent',
    'field_talent_agency',
    'field_talent_services',
    'field_talent_ba_exec',
    'field_talent_legal_exec',
    'field_talent_status',
    'field_talent_deal_terms',
    'field_talent_group_id',
    'field_talent_type_of_agreements',
    'field_talent_notes',
  );

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