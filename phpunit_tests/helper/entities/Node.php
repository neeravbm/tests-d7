<?php
/**
 * Created by PhpStorm.
 * User: Anil
 * Date: 3/9/14
 * Time: 4:00 PM
 */

namespace tests\phpunit_tests\helper\entities;

class Node extends Entity {

  /**
   * Default constructor for the node object.
   *
   * @param int $nid
   *   Nid if an existing node is to be loaded.
   */
  protected function __construct($nid = NULL) {
    $class = new \ReflectionClass(get_called_class());
    $type = drupal_strtolower($class->getShortName());

    if (!is_null($nid) && is_numeric($nid)) {
      $node = node_load($nid);
      if ($node->type == $type) {
        $this->setEntity($node);
      }
    }
    else {
      $node = (object) array(
        'title' => NULL,
        'type' => $type,
        'language' => LANGUAGE_NONE,
        'is_new' => TRUE,
      );
      node_object_prepare($node);
      $this->setEntity($node);
    }

    return $this->getEntity();
  }

  /**
   * Returns the node.
   *
   * @return object node
   *   Node object.
   */
  public function getNode() {
    return $this->getEntity();
  }

  /**
   * Returns content type.
   *
   * @return string $type
   *   Content type.
   */
  public function getType() {
    return $this->getEntity()->type;
  }


  public function __call($name, $arguments) {
    if (strpos($name, 'get') === 0) {
      // Function name starts with "get".
      /*$field_name = preg_replace('/(?<=\\w)(?=[A-Z])/',"_$1", substr($name, 3));
      $field_name = strtolower($field_name);
      $field = field_info_field($field_name);
      $class = get_called_class();
      $type = $class::TYPE;
      $instance = field_info_instance('node', $field_name, $type);
      $widget = $instance['widget']['type'];
      $function = "get" . str_replace(" ", "", ucwords(str_replace("_", " ", $widget)));
      $values = self::$function($field_name);*/
    }
  }
}