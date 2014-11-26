<?php
/**
 * Created by PhpStorm.
 * User: Anil
 * Date: 3/15/14
 * Time: 6:00 PM
 */

namespace tests\phpunit_tests\core\forms;

use tests\phpunit_tests\core\Utilities as Utilities;

class TaxonomyFormTerm extends EntityForm {

  private $vocabulary;

  function __construct($tid = NULL) {
    $classname = get_called_class();
    $class = new \ReflectionClass($classname);
    $class_shortname = $class->getShortName();
    $vocabulary_name = Utilities::convertTitleCaseToUnderscore(substr($class_shortname, 0, -4));

    if (!is_null($tid) && is_numeric($tid)) {
      // Tid is not null and is numeric.
      $term = taxonomy_term_load($tid);
      if ($term->vocabulary_machine_name == $vocabulary_name) {
        $this->vocabulary = taxonomy_vocabulary_machine_name_load($vocabulary_name);
        $this->setEntityObject($term);
        module_load_include('inc', 'taxonomy', 'taxonomy.admin');
        parent::__construct('taxonomy_form_term', $term, $this->vocabulary);
        return;
      }
      else {
        // Vocabulary name of the provided term does not match the class it was called from. Return without doing anything.
        return;
      }
    }

    // tid is not provided or is not numeric.
    $this->vocabulary = taxonomy_vocabulary_machine_name_load($vocabulary_name);
    module_load_include('inc', 'taxonomy', 'taxonomy.admin');
    parent::__construct('taxonomy_form_term', array(), $this->vocabulary);
  }

  /**
   * This function is used for submit Taxonomy form.
   *
   * @return this will return $form_state is success else error if not   *
   */
  public function submit() {
    $this->fillValues(array('op' => t('Save')));
    $weight = $this->getValues('weight');
    if (empty($weight)) {
      $this->fillValues(array('weight' => 0));
    }
    $output = parent::submit(array(), $this->vocabulary);

    $classname = get_called_class();
    $class = new \ReflectionClass($classname);
    $class_shortname = $class->getShortName();
    $class_fullname = "tests\\phpunit_tests\\custom\\entities\\terms\\" . substr($class_shortname, 0, -4);

    $form_state = $this->getFormState();
    $termObject = new $class_fullname($form_state['term']->tid);
    $this->setEntityObject($termObject);
    $this->getEntityObject()->reload();
    return $output;
  }

  /**
   * This function is used for vocabulary machine name
   *
   * @param  $value
   *   This is vocabulary machine name
   */
  public function fillTermVocabField($value) {
    $this->fillTermVocabWidgetField($value);
  }

  /**
   * This function is used for vocabulary id
   *
   * @param  $value
   *   This is vocabulary id
   */
  public function fillTermVocabVidField($value) {
    $this->fillTermVocabVidWidgetField($value);
  }
} 
