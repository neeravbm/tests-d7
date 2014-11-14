<?php
/**
 * Created by PhpStorm.
 * User: Anil
 * Date: 3/15/14
 * Time: 6:00 PM
 */

namespace tests\phpunit_tests\helper\forms;

require_once 'Form.php';

class TaxonomyFormTerm extends Form {
  private $vocabulary;
  private $edit;

  function __construct($vocabulary, $edit=array()) {
	 $this->vocabulary = $vocabulary;
     $this->edit = $edit;
     parent::__construct('taxonomy_form_term',$vocabulary);
  }
  
  /**
   * This function is used for submit Taxonomy form
   * @return this will return $form_state is success else error if not   *
   */
  public function submit() {
    $this->fillValues(array('op' => t('Save')));
	$output = parent::submit($this->edit,$this->vocabulary);
	 if (is_array($output)) {
		// There was an error.
		return $output;
	 }
     else {
		$form_state = $this->getFormState();
		return $form_state;
     } 
  }
  
  /**
   * This function is used for vocabulary machine name
   * @param  $value
   *   This is vocabulary machine name
   */
  public function fillTermVocabField($value) {
    $this->fillTermVocabWidgetField($value);
  }
  
  /**
   * This function is used for vocabulary id
   * @param  $value
   *   This is vocabulary id
   */
  public function fillTermVocabVidField($value) {
    $this->fillTermVocabVidWidgetField($value);
  }
} 
