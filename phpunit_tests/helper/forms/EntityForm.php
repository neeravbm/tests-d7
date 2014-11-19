<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 11/15/14
 * Time: 11:44 PM
 */

namespace tests\phpunit_tests\helper\forms;

class EntityForm extends Form {

  private $entityObject;

  public function getEntityObject() {
    return $this->entityObject;
  }

  public function setEntityObject($entityObject) {
    $this->entityObject = $entityObject;
  }

  public function __call($name, $arguments) {
    if (strpos($name, 'fill') === 0) {
      // Function name starts with "fill".
      $field_name = preg_replace('/(?<=\\w)(?=[A-Z])/',"_$1", substr($name, 4));
      $field_name = strtolower($field_name);
      $field = field_info_field($field_name);
      $is_property = FALSE;
      if (is_null($field)) {
        $is_property = TRUE;
      }
      else {
        list(, , $bundle) = entity_extract_ids($this->entityObject->getEntityType(), $this->entityObject->getEntity());
        $instance = field_info_instance($this->entityObject->getEntityType(), $field_name, $bundle);
        if (is_null($instance)) {
          $is_property = TRUE;
        }
        else {
          // Get the field instance value here.
          $widget = $instance['widget']['type'];
          $function = "fill" . str_replace(" ", "", ucwords(str_replace("_", " ", $widget)));
          call_user_func_array(array($this, $function), array_merge(array($field_name), $arguments));
        }
      }

      if ($is_property) {
        $this->fillValues(array($field_name => $arguments[0]));
      }
    }
  }
}