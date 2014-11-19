<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 11/13/14
 * Time: 6:16 PM
 */

namespace tests\phpunit_tests\helper\entities;

/**
 * Class Entity
 * @package tests\phpunit_tests\helper\entities
 */
abstract class Entity {

  /**
   * Entity object.
   *
   * @var object $entity
   */
  private $entity;

  /**
   * Entity type.
   *
   * @var string $entity_type
   *   Type of entity.
   */
  private $entity_type;

  /**
   * Prevent an object from being constructed.
   *
   * @param object $entity
   *   Entity object.
   */
  protected function __construct($entity) {
    $this->entity = $entity;
    $this->entity_type = $this->getEntityType();
  }

  /**
   * Returns the entity id.
   *
   * @return int $id
   *   Entity id.
   */
  public function getId() {
    return entity_id($this->entity_type, $this->entity);
  }

  /**
   * Returns the entity type.
   *
   * @return bool|string $entity_type
   *   Entity type if one exists, FALSE otherwise.
   */
  public function getEntityType() {
    if (!is_null($this->entity_type)) {
      return $this->entity_type;
    }

    $classes = class_parents(get_called_class());
    if (sizeof($classes) >= 2) {
      // If there are at least 2 parent classes, such as Entity and Node.
      $classnames = array_values($classes);
      $classname = $classnames[sizeof($classes)-2];
      $class = new \ReflectionClass($classname);
      $entity_type = drupal_strtolower($class->getShortName());
      return $entity_type;
    }
    elseif (sizeof($classes) == 1) {
      // If an entity such as User is calling the class directly, then entity type will be User itself.
      $classname = get_called_class();
      $class = new \ReflectionClass($classname);
      $entity_type = drupal_strtolower($class->getShortName());
      return $entity_type;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Reloads the entity from database.
   */
  public function reload() {
    $entity_id = $this->getId();
    if (empty($entity_id)) {
      $this->entity = NULL;
      return;
    }

    $entities = entity_load($this->entity_type, array($entity_id), array(), TRUE);
    if (!empty($entities[$entity_id])) {
      $this->entity = $entities[$entity_id];
    }
    else {
      $this->entity = NULL;
    }
  }

  /**
   * Returns the entity object.
   *
   * @return object $entity
   *   Entity object.
   */
  public function getEntity() {
    return $this->entity;
  }

  /**
   * Sets the entity object.
   *
   * @param object $entity
   *   Entity object.
   */
  public function setEntity($entity) {
    $this->entity = $entity;
  }

  /**
   * Returns the view of the entity for a given view mode. This is copied from entity_view() function since entity module may not be installed.
   *
   * @param string $view_mode
   *   View mode. If view mode is not specified, then default view mode is assumed.
   *
   * @return array $view
   *   A renderable array of the entity for the provided view mode. If there is any error, then FALSE is returned.
   */
  public function view($view_mode = 'default') {
    $entities = array($this->entity);
    $langcode = NULL;
    $page = NULL;

    $info = entity_get_info($this->entity_type);
    if (isset($info['view callback'])) {
      $entities = entity_key_array_by_property($entities, $info['entity keys']['id']);
      return $info['view callback']($entities, $view_mode, $langcode, $this->entity_type);
    }
    elseif (in_array('EntityAPIControllerInterface', class_implements($info['controller class']))) {
      return entity_get_controller($this->entity_type)->view($entities, $view_mode, $langcode, $page);
    }
    return FALSE;
  }

  /**
   * Saves the entity to database. This is copied from entity_save() function since entity module may not be installed.
   */
  public function save() {
    $info = entity_get_info($this->entity_type);
    if (method_exists($this->entity, 'save')) {
      $this->entity->save();
    }
    elseif (isset($info['save callback'])) {
      $info['save callback']($this->entity);
    }
    elseif (in_array('EntityAPIControllerInterface', class_implements($info['controller class']))) {
      entity_get_controller($this->entity_type)->save($this->entity);
    }
  }

  /**
   * Deletes the entity from database. This is copied from the entity_delete() function since entity module may not be installed.
   */
  public function delete() {
    $info = entity_get_info($this->entity_type);
    if (isset($info['deletion callback'])) {
      $info['deletion callback']($this->getId());
    }
    elseif (in_array('EntityAPIControllerInterface', class_implements($info['controller class']))) {
      entity_get_controller($this->entity_type)->delete(array($this->getId()));
    }
  }

  /**
   * Returns whether currently logged in user has access to view the entity.
   *
   * @return bool $out
   *   TRUE if user has access and FALSE otherwise.
   */
  public static function hasCreateAccess() {
    $entity_type = self::getEntityType();
    return entity_access('create', $entity_type);
  }

  /**
   * Returns whether currently logged in user has access to view the node.
   *
   * @return bool $out
   *   TRUE if user has access and FALSE otherwise.
   */
  public function hasViewAccess() {
    return entity_access('view', $this->entity_type, $this->entity);
  }

  /**
   * Returns whether currently logged in user has access to edit the node.
   *
   * @return bool $out
   *   TRUE if user has access and FALSE otherwise.
   */
  public function hasEditAccess() {
    return entity_access('update', $this->entity_type, $this->entity);
  }

  /**
   * Returns whether currently logged in user has access to delete the node.
   *
   * @return bool $out
   *   TRUE if user has access and FALSE otherwise.
   */
  public function hasDeleteAccess() {
    return entity_access('delete', $this->entity_type, $this->entity);
  }

  /**
   * Sets values in the entity object.
   *
   * @param array $values
   *   An array of values.
   */
  public function setValues($values) {
    foreach ($values as $key => $value) {
      $this->entity->$key = $value;
    }
  }

  /**
   * @param $field_name
   * @return bool
   */
  public function getEntityreferenceViewWidget($field_name) {
    $class = get_called_class();
    $field_values = field_get_items(drupal_strtolower($class), $this->entity, $field_name);
    return $field_values;
  }

  /**
   * Returns label of the entity.
   *
   * @return bool|string $label
   *   Entity label.
   */
  public function getLabel() {
    $entity_type = $this->getEntityType();
    return entity_label($entity_type, $this->entity);
  }

  public function __call($name, $arguments) {
    if (strpos($name, 'get') === 0) {
      // Function name starts with "get".
      $field_name = preg_replace('/(?<=\\w)(?=[A-Z])/',"_$1", substr($name, 3));
      $field_name = strtolower($field_name);
      $field = field_info_field($field_name);
      $is_property = FALSE;
      if (is_null($field)) {
        $is_property = TRUE;
      }
      else {
        list(, , $bundle) = entity_extract_ids($this->entity_type, $this->entity);
        $instance = field_info_instance($this->entity_type, $field_name, $bundle);
        if (is_null($instance)) {
          $is_property = TRUE;
        }
        else {
          // Get the field instance value here.
          $widget = $instance['widget']['type'];
          $function = "get" . str_replace(" ", "", ucwords(str_replace("_", " ", $widget)));
          return $this->$function($field_name);
        }
      }

      if ($is_property && !empty($this->entity->$field_name)) {
        return $this->entity->$field_name;
      }

      return NULL;
    }
  }

  public function getTextTextareaWithSummary($field_name) {
    $field = field_get_items($this->entity_type, $this->entity, $field_name);
    $output = array();
    foreach ($field as $key => $val) {
      if (!empty($val['safe_value'])) {
        $output[] = $val['safe_value'];
      }
      else {
        $output[] = $val['value'];
      }
    }
    return $output;
  }
}