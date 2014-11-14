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
   * Prevent an object from being constructed.
   */
  private function __construct() {}

  /**
   * Returns the entity id.
   *
   * @return int $id
   *   Entity id.
   */
  public function getId() {
    $entity_type = $this->getEntityType();
    return entity_id($entity_type, $this->entity);
  }

  /**
   * Returns the entity type.
   *
   * @return bool|string $entity_type
   *   Entity type if one exists, FALSE otherwise.
   */
  public function getEntityType() {
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
   * Returns the entity object.
   *
   * @return object $entity
   *   Entity object.
   */
  protected function getEntity() {
    return $this->entity;
  }

  /**
   * Sets the entity object.
   *
   * @param object $entity
   *   Entity object.
   */
  protected function setEntity($entity) {
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
    $entity_type = $this->getEntityType();
    $entities = array($this->entity);
    $langcode = NULL;
    $page = NULL;

    $info = entity_get_info($entity_type);
    if (isset($info['view callback'])) {
      $entities = entity_key_array_by_property($entities, $info['entity keys']['id']);
      return $info['view callback']($entities, $view_mode, $langcode, $entity_type);
    }
    elseif (in_array('EntityAPIControllerInterface', class_implements($info['controller class']))) {
      return entity_get_controller($entity_type)->view($entities, $view_mode, $langcode, $page);
    }
    return FALSE;
  }

  /**
   * Saves the entity to database. This is copied from entity_save() function since entity module may not be installed.
   */
  public function save() {
    $entity_type = $this->getEntityType();

    $info = entity_get_info($entity_type);
    if (method_exists($this->entity, 'save')) {
      $this->entity->save();
    }
    elseif (isset($info['save callback'])) {
      $info['save callback']($this->entity);
    }
    elseif (in_array('EntityAPIControllerInterface', class_implements($info['controller class']))) {
      entity_get_controller($entity_type)->save($this->entity);
    }
  }

  /**
   * Deletes the entity from database. This is copied from the entity_delete() function since entity module may not be installed.
   */
  public function delete() {
    $entity_type = $this->getEntityType();

    $info = entity_get_info($entity_type);
    if (isset($info['deletion callback'])) {
      $info['deletion callback']($this->getId());
    }
    elseif (in_array('EntityAPIControllerInterface', class_implements($info['controller class']))) {
      entity_get_controller($entity_type)->delete(array($this->getId()));
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
    $entity_type = $this->getEntityType();
    return entity_access('view', $entity_type, $this->entity);
  }

  /**
   * Returns whether currently logged in user has access to edit the node.
   *
   * @return bool $out
   *   TRUE if user has access and FALSE otherwise.
   */
  public function hasEditAccess() {
    $entity_type = $this->getEntityType();
    return entity_access('update', $entity_type, $this->entity);
  }

  /**
   * Returns whether currently logged in user has access to delete the node.
   *
   * @return bool $out
   *   TRUE if user has access and FALSE otherwise.
   */
  public function hasDeleteAccess() {
    $entity_type = $this->getEntityType();
    return entity_access('delete', $entity_type, $this->entity);
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
}