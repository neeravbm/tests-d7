<?php
/**
 * Created by PhpStorm.
 * User: Anil
 * Date: 3/9/14
 * Time: 4:58 PM
 */

namespace tests\phpunit_tests\helper\forms;

use tests\phpunit_tests\helper\entities as entities;

class NodeForm extends Form {

  private $nodeObject;

  /**
   * Default constructor of the node form.
   *
   * @param int $nid
   *   Node id if an existing node form is to be loaded.
   */
  protected function __construct($nid = NULL) {
    $classname = get_called_class();
    $class = new \ReflectionClass($classname);
    $class_shortname = $class->getShortName();
    $class_fullname = "tests\\phpunit_tests\\helper\\entities\\" . substr($class_shortname, 0, -4);

    $type = drupal_strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', "_$1", substr($class_shortname, 0, -4)));
    $this->nodeObject = new $class_fullname($nid);

    if (!is_null($this->nodeObject->getNode())) {
      module_load_include('inc', 'node', 'node.pages');
      parent::__construct($type . '_node_form', $this->nodeObject->getNode());
    }
  }

  /**
   * Loads the appropriate class file based on the type.
   *
   * @param string $type
   *   Content type.
   *
   * @return string $class
   *   Name of the class that was loaded.
   */
  /*private function getClassByType($type) {
    $class = str_replace(" ", "", ucwords(str_replace("_", " ", $type)));
    return "tests\\phpunit_tests\\helper\\entities\\" . $class;
  }*/

  /**
   * Set author name.
   *
   * @param string $username
   *   Username of the author.
   */
  function setAuthorname($username) {
    $this->fillValues(
      array(
        'name' => $username,
      )
    );
  }

  /**
   * This function is used for node form submit
   */
  public function submit() {
    $this->fillValues(array('op' => t('Save')));
    module_load_include('inc', 'node', 'node.pages');
    $output = parent::submit($this->nodeObject->getNode());
    if (is_array($output)) {
      // There was an error.
      return $output;
    }
    else {
      $form_state = $this->getFormState();
      return $form_state['nid'];
    }
  }
}