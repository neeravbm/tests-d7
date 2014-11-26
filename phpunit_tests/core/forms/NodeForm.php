<?php
/**
 * Created by PhpStorm.
 * User: Anil
 * Date: 3/9/14
 * Time: 4:58 PM
 */

namespace tests\phpunit_tests\core\forms;

use tests\phpunit_tests\core\Utilities as Utilities;

class NodeForm extends EntityForm {

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
    $class_fullname = "tests\\phpunit_tests\\custom\\entities\\nodes\\" . substr($class_shortname, 0, -4);

    $type = Utilities::convertTitleCaseToUnderscore(substr($class_shortname, 0, -4));
    $nodeObject = new $class_fullname($nid);
    $this->setEntityObject($nodeObject);

    if (!is_null($this->getEntityObject()->getEntity())) {
      module_load_include('inc', 'node', 'node.pages');
      parent::__construct($type . '_node_form', $this->getEntityObject()->getEntity());
    }
  }

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
   * This function is used for node form submit.
   */
  public function submit() {
    $this->fillValues(array('op' => t('Save')));
    module_load_include('inc', 'node', 'node.pages');
    $output = parent::submit($this->getEntityObject()->getEntity());

    if ($output) {
      // Get the node from form_state.
      $form_state = $this->getFormState();
      $node = $form_state['node'];
      $type = $node->type;
      $classname = Utilities::convertUnderscoreToTitleCase($type);
      $class_fullname = "tests\\phpunit_tests\\custom\\entities\\nodes\\" . $classname;
      $nodeObject = new $class_fullname($node->nid);
      $this->setEntityObject($nodeObject);
    }

    return $output;
  }
}