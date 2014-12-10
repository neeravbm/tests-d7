<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 11/13/14
 * Time: 6:16 PM
 */

namespace tests\phpunit_tests\core\entities;

use tests\phpunit_tests\core\Utilities as Utilities;
use tests\phpunit_tests\custom\forms as CustomForms;

/**
 * Class Entity
 *
 * @package tests\phpunit_tests\custom\entities
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
    // Check if this function is being called from static context. This usually happens when calling hasCreateAccess() function.
    $static = !(isset($this) && get_class($this) == __CLASS__);
    // If the function is being called from static context and $this->entity_type is defined, then return it.
    if (!$static && !is_null($this->entity_type)) {
      return $this->entity_type;
    }

    $classes = class_parents(get_called_class());
    if (sizeof($classes) >= 2) {
      // If there are at least 2 parent classes, such as Entity and Node.
      $classnames = array_values($classes);
      $classname = $classnames[sizeof($classes) - 2];
      $class = new \ReflectionClass($classname);
      $entity_type = Utilities::convertTitleCaseToUnderscore(
        $class->getShortName()
      );

      return $entity_type;
    }
    elseif (sizeof($classes) == 1) {
      // If an entity such as User is calling the class directly, then entity type will be User itself.
      $classname = get_called_class();
      $class = new \ReflectionClass($classname);
      $entity_type = Utilities::convertTitleCaseToUnderscore(
        $class->getShortName()
      );

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

    $entities = entity_load(
      $this->entity_type,
      array($entity_id),
      array(),
      TRUE
    );
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
   * Returns the view of the entity for a given view mode. This is copied from
   * entity_view() function since entity module may not be installed.
   *
   * @param string $view_mode
   *   View mode. If view mode is not specified, then default view mode is
   *   assumed.
   *
   * @return array $view
   *   A renderable array of the entity for the provided view mode. If there is
   *   any error, then FALSE is returned.
   */
  public function view($view_mode = 'full') {
    $entities = array($this->entity);
    $langcode = NULL;
    $page = NULL;

    $output = array();
    $info = entity_get_info($this->entity_type);
    if (isset($info['view callback'])) {
      $entities = entity_key_array_by_property(
        $entities,
        $info['entity keys']['id']
      );

      $output = $info['view callback'](
        $entities,
        $view_mode,
        $langcode,
        $this->entity_type
      );
    }
    elseif (in_array(
      'EntityAPIControllerInterface',
      class_implements($info['controller class'])
    )) {
      $output = entity_get_controller($this->entity_type)->view(
        $entities,
        $view_mode,
        $langcode,
        $page
      );
    }

    if (!empty($output[$this->entity_type][$this->getId()])) {
      return $output[$this->entity_type][$this->getId()];
    }

    return array();
  }

  /**
   * Saves the entity to database. This is copied from entity_save() function
   * since entity module may not be installed.
   */
  public function save() {
    $info = entity_get_info($this->entity_type);
    if (method_exists($this->entity, 'save')) {
      $this->entity->save();
    }
    elseif (isset($info['save callback'])) {
      $info['save callback']($this->entity);
    }
    elseif (in_array(
      'EntityAPIControllerInterface',
      class_implements($info['controller class'])
    )) {
      entity_get_controller($this->entity_type)->save($this->entity);
    }
  }

  /**
   * Deletes the entity from database. This is copied from the entity_delete()
   * function since entity module may not be installed.
   */
  public function delete() {
    $info = entity_get_info($this->entity_type);
    if (isset($info['deletion callback'])) {
      $info['deletion callback']($this->getId());
    }
    elseif (in_array(
      'EntityAPIControllerInterface',
      class_implements($info['controller class'])
    )) {
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
    return entity_access('create', self::getEntityType());
  }

  /**
   * Returns whether currently logged in user has access to view the node.
   *
   * @return bool $out
   *   TRUE if user has access and FALSE otherwise.
   */
  public function hasViewAccess() {
    return entity_access('view', $this->getEntityType(), $this->entity);
  }

  /**
   * Returns whether currently logged in user has access to update the node.
   *
   * @return bool $out
   *   TRUE if user has access and FALSE otherwise.
   */
  public function hasUpdateAccess() {
    return entity_access('update', $this->getEntityType(), $this->entity);
  }

  /**
   * Returns whether currently logged in user has access to delete the node.
   *
   * @return bool $out
   *   TRUE if user has access and FALSE otherwise.
   */
  public function hasDeleteAccess() {
    return entity_access('delete', $this->getEntityType(), $this->entity);
  }

  public function hasFieldAccess($field_name, $op = 'view') {
    if (($field = field_info_field($field_name)) && in_array(
        $op,
        array('edit', 'view')
      )
    ) {
      return field_access($op, $field, $this->getEntityType(), $this->entity);
    }

    return NULL;
  }

  /**
   * Returns text field as viewed by the logged in user in the provided view
   * mode.
   *
   * @param string $field_name
   *   Field name.
   * @param string $view_mode
   *   View mode. If this is not provided, then "full" is assumed.
   * @param bool $post_process
   *   Whether to post process the field values before returning.
   * @param bool $from_entity_view
   *   Whether to return the field values using field_view_field() function or
   *   building the entity view and returning the field value from there. If
   *   code uses entity_view_alter() or node_view_alter(), then the two values
   *   can differ. If you don't expect them to be different, then it is
   *   recommended to keep this argument to FALSE since it will be faster.
   *
   * @return null|array $view
   *   Renderable array of the field if it exists, NULL otherwise.
   */
  public function viewText(
    $field_name,
    $view_mode = 'full',
    $post_process = TRUE,
    $from_entity_view = FALSE
  ) {
    $view = array();
    if ($from_entity_view) {
      $view = $this->view($view_mode);
      if (!empty($view[$field_name])) {
        $view = $view[$field_name];
      }
    }
    else {
      $view = field_view_field(
        $this->entity_type,
        $this->entity,
        $field_name,
        $view_mode,
        NULL
      );
    }

    if (!$post_process) {
      return $view;
    }

    $output = array();
    foreach (element_children($view) as $key) {
      $output[] = $view[$key]['#markup'];
    }

    if (sizeof($output) == 1) {
      return $output[0];
    }

    return $output;
  }

  public function viewDatetime(
    $field_name,
    $view_mode = 'full',
    $post_process = TRUE,
    $from_entity_view = FALSE
  ) {
    $view = array();
    if ($from_entity_view) {
      $view = $this->view($view_mode);
      if (!empty($view[$field_name])) {
        $view = $view[$field_name];
      }
    }
    else {
      $view = field_view_field(
        $this->entity_type,
        $this->entity,
        $field_name,
        $view_mode,
        NULL
      );
    }

    if (!$post_process) {
      return $view;
    }

    $output = array();
    foreach (element_children($view) as $key) {
      $output[] = $view[$key]['#markup'];
    }

    if (sizeof($output) == 1) {
      return $output[0];
    }

    return $output;
  }

  public function viewEntityreference(
    $field_name,
    $view_mode = 'full',
    $post_process = TRUE,
    $from_entity_view = FALSE
  ) {
    if ($from_entity_view) {
      $view = $this->view($view_mode);
      if (!empty($view[$field_name])) {
        $view = $view[$field_name];
      }
    }
    else {
      $view = field_view_field(
        $this->entity_type,
        $this->entity,
        $field_name,
        $view_mode,
        NULL
      );
    }

    if (!$post_process) {
      return $view;
    }

    $output = array();
    foreach (element_children($view) as $key) {
      $output[] = $view[$key]['#markup'];
    }

    if (sizeof($output) == 1) {
      return $output[0];
    }

    return $output;
  }

  public function viewTaxonomyTermReference(
    $field_name,
    $view_mode = 'full',
    $post_process = TRUE,
    $from_entity_view = FALSE
  ) {
    if ($from_entity_view) {
      $view = $this->view($view_mode);
      if (!empty($view[$field_name])) {
        $view = $view[$field_name];
      }
    }
    else {
      $view = field_view_field(
        $this->entity_type,
        $this->entity,
        $field_name,
        $view_mode,
        NULL
      );
    }

    if (!$post_process) {
      return $view;
    }

    $output = array();
    foreach (element_children($view) as $key) {
      $output[] = $view[$key]['#title'];
    }

    if (sizeof($output) == 1) {
      return $output[0];
    }

    return $output;
  }

  /**
   * Returns field as viewed by the logged in user in the provided view mode.
   *
   * @param string $field_name
   *   Field name.
   * @param string $view_mode
   *   View mode. If this is not provided, then "full" is assumed.
   * @param bool $post_process
   *   Whether to post process the field values before returning.
   * @param bool $from_entity_view
   *   Whether to return the field values using field_view_field() function or
   *   building the entity view and returning the field value from there. If
   *   code uses entity_view_alter() or node_view_alter(), then the two values
   *   can differ. If you don't expect them to be different, then it is
   *   recommended to keep this argument to FALSE since it will be faster.
   *
   * @return null|array $view
   *   Renderable array of the field if it exists, NULL otherwise.
   */
  public function viewField(
    $field_name,
    $view_mode = 'full',
    $post_process = TRUE,
    $from_entity_view = FALSE
  ) {
    if ($instance = $this->getFieldInstance($field_name)) {
      // Field instance exists.
      // If post-processing is not required, then just return the field values
      // as provided by Drupal.
      if (!$post_process) {
        if ($from_entity_view) {
          $view = $this->view($view_mode);
          if (!empty($view[$field_name])) {
            $view = $view[$field_name];
          }
        }
        else {
          $view = field_view_field(
            $this->entity_type,
            $this->entity,
            $field_name,
            $view_mode,
            NULL
          );
        }

        return $view;
      }

      // Get the field instance value here.
      $function = 'view' . Utilities::convertUnderscoreToTitleCase(
          $instance['widget']['type']
        );

      // Check if a function exists for getting value from this particular field
      // instance.
      if (method_exists($this, $function)) {
        return $this->$function(
          $field_name,
          $view_mode,
          $post_process,
          $from_entity_view
        );
      }
      else {
        // Check if a function exists for getting value from this particular
        // field type.
        $field = $this->getFieldInfo($field_name);
        $function = 'view' . Utilities::convertUnderscoreToTitleCase(
            $field['type']
          );
        if (method_exists($this, $function)) {
          return $this->$function(
            $field_name,
            $view_mode,
            $post_process,
            $from_entity_view
          );
        }
      }

      // Field instance exists but no function is defined to get value from it.
      return NULL;
    }

    // There is no such field instance for the given entity. Check if it's a
    // property.
    if (!empty($this->entity->$field_name)) {
      return $this->entity->$field_name;
    }

    return NULL;
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
   * Returns label of the entity.
   *
   * @return bool|string $label
   *   Entity label.
   */
  public function getLabel() {
    return entity_label($this->getEntityType(), $this->entity);
  }

  /**
   * Magic method. This function will be executed when a matching function is
   * not found. Currently this supports two kinds of functions:
   * get<FieldName>() and has<FieldName><View|Edit>Access().
   *
   * @param string $name
   *   Called function name.
   * @param string $arguments
   *   Function arguments.
   *
   * @return mixed $output
   *   Output depends on which function ultimately gets called.
   */
  public function __call($name, $arguments) {
    if (strpos($name, 'has') === 0 && strrpos($name, 'Access') == strlen(
        $name
      ) - 6
    ) {
      // Function name starts with "has" and ends with "Access". Function name
      // is not one of "hasCreateAccess", "hasUpdateAccess", "hasViewAccess" or
      // "hasDeleteAccess" otherwise code execution would not have reached this
      // function. This means that we are checking if a field is accessible.
      $name = substr($name, 3, -6);
      $op = '';
      $field_name = '';
      if (strrpos($name, 'View') == strlen($name) - 4) {
        $op = 'view';
        $field_name = Utilities::convertTitleCaseToUnderscore(
          substr($name, 0, -4)
        );
      }
      elseif (strrpos($name, 'Update') == strlen($name) - 6) {
        $op = 'edit';
        $field_name = Utilities::convertTitleCaseToUnderscore(
          substr($name, 0, -6)
        );
      }

      if (in_array($op, array('view', 'edit'))) {
        return $this->hasFieldAccess($field_name, $op);
      }
    }
    elseif (strpos($name, 'get') === 0) {
      // Function name starts with "get".
      array_unshift(
        $arguments,
        Utilities::convertTitleCaseToUnderscore(substr($name, 3))
      );

      return call_user_func_array(array($this, 'getFieldValue'), $arguments);
    }
    elseif (strpos($name, 'view') === 0) {
      // Function name starts with "view".
      array_unshift(
        $arguments,
        Utilities::convertTitleCaseToUnderscore(substr($name, 4))
      );

      return call_user_func_array(array($this, 'viewField'), $arguments);
    }
  }

  public function getText($field_name, $post_process = TRUE) {
    $field = $this->getFieldItems($field_name);
    if (!$post_process) {
      return $field;
    }

    $output = array();
    foreach ($field as $key => $val) {
      if (!empty($val['safe_value'])) {
        $output[] = $val['safe_value'];
      }
      else {
        $output[] = $val['value'];
      }
    }

    if (sizeof($output) == 1) {
      return $output[0];
    }

    return $output;
  }

  public function getDatetime($field_name, $post_process = TRUE) {
    $field = $this->getFieldItems($field_name);
    if (!$post_process) {
      return $field;
    }

    $output = array();
    foreach ($field as $key => $val) {
      $output[] = $val['value'];
    }

    if (sizeof($output) == 1) {
      return $output[0];
    }

    return $output;
  }

  public function getTextTextareaWithSummary(
    $field_name,
    $post_process = TRUE
  ) {
    $field = field_get_items($this->entity_type, $this->entity, $field_name);
    if (!$post_process) {
      return $field;
    }

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

  public function getEntityreferenceViewWidget(
    $field_name,
    $post_process = TRUE
  ) {
    $field = field_get_items($this->entity_type, $this->entity, $field_name);
    if (!$post_process) {
      return $field;
    }

    $output = array();
    foreach ($field as $key => $val) {
      $output[] = $val['target_id'];
    }

    return $output;
  }

  public function getAutocompleteDeluxeTaxonomy($field_name, $post_process = TRUE) {
    $field = $this->getFieldItems($field_name);
    if (!$post_process) {
      return $field;
    }

    $output = array();
    foreach ($field as $key => $val) {
      $term = taxonomy_term_load($val['tid']);
      $output[$key] = $term->name;
    }

    if (sizeof($output) == 1) {
      return $output[0];
    }

    return $output;
  }

  public function getTaxonomyTermReference($field_name, $post_process = TRUE) {
    $field = field_get_items($this->entity_type, $this->entity, $field_name);
    if (!$post_process) {
      return $field;
    }

    $output = array();
    foreach ($field as $key => $val) {
      $output[] = $val['tid'];
    }

    if (sizeof($output) == 1) {
      return $output[0];
    }

    return $output;
  }

  public function getListBoolean($field_name, $post_process = TRUE) {
    $field = $this->getFieldItems($field_name);
    if (!$post_process) {
      return $field;
    }

    return $field[0]['value'];
  }

  public function viewListBoolean(
    $field_name,
    $view_mode = 'full',
    $post_process = TRUE,
    $from_entity_view = FALSE
  ) {
    if ($from_entity_view) {
      $view = $this->view($view_mode);
      if (!empty($view[$field_name])) {
        $view = $view[$field_name];
      }
    }
    else {
      $view = field_view_field(
        $this->entity_type,
        $this->entity,
        $field_name,
        $view_mode,
        NULL
      );
    }

    if (!$post_process) {
      return $view;
    }

    $output = array();
    foreach (element_children($view) as $key) {
      $output[] = $view[$key]['#markup'];
    }

    if (sizeof($output) == 1) {
      return $output[0];
    }

    return $output;
  }

  /**
   * Get value of a field.
   *
   * @param string $field_name
   *   Field name.
   * @param bool $post_process
   *   Whether to post process the field values before returning.
   *
   * @return mixed $output
   *   Value of the field.
   *
   * @throws \EntityMalformedException
   */
  public function getFieldValue($field_name, $post_process = TRUE) {
    if ($instance = $this->getFieldInstance($field_name)) {
      // Field instance exists.
      // If post-processing is not required, then just return the field values
      // as provided by Drupal.
      if (!$post_process) {
        return $this->getFieldItems($field_name);
      }

      // Get the field instance value here.
      $function = 'get' . Utilities::convertUnderscoreToTitleCase(
          $instance['widget']['type']
        );

      // Check if a function exists for getting value from this particular field
      // instance.
      if (method_exists($this, $function)) {
        return $this->$function($field_name);
      }
      else {
        // Check if a function exists for getting value from this particular
        // field type.
        $field = $this->getFieldInfo($field_name);
        $function = 'get' . Utilities::convertUnderscoreToTitleCase(
            $field['type']
          );
        if (method_exists($this, $function)) {
          return $this->$function($field_name);
        }
      }

      // Field instance exists but no function is defined to get value from it.
      return NULL;
    }

    // There is no such field instance for the given entity. Check if it's a
    // property.
    if (!empty($this->entity->$field_name)) {
      return $this->entity->$field_name;
    }

    return NULL;
  }

  /**
   * Returns field instance from field name for the given entity.
   *
   * @param string $field_name
   *   Field name.
   *
   * @return mixed $instance
   *   An instance array if one exists, FALSE otherwise.
   *
   * @throws \EntityMalformedException
   */
  public function getFieldInstance($field_name) {
    list(, , $bundle) = entity_extract_ids(
      $this->entity_type,
      $this->entity
    );
    $instance = field_info_instance(
      $this->entity_type,
      $field_name,
      $bundle
    );

    return $instance;
  }

  /**
   * Returns field info from field name.
   *
   * @param string $field_name
   *   Field name.
   *
   * @returns array|null $field
   *   Field info array if one exists, NULL otherwise.
   */
  public function getFieldInfo($field_name) {
    return field_info_field($field_name);
  }

  public function getFieldItems($field_name) {
    return field_get_items($this->entity_type, $this->entity, $field_name);
  }

  public static function createDefault(&$entities, $num = 1, $skip = array()) {
    $output = array();
    for ($i = 0; $i < $num; $i++) {

      $entity_type = self::getEntityType();
      $original_class = get_called_class();
      $class = new \ReflectionClass($original_class);
      $formClass = "tests\\phpunit_tests\\custom\\forms\\entities\\" . $entity_type . "\\" . $class->getShortName(
        ) . 'Form';

      $classForm = new $formClass();
      list($out, $fields, $msg) = $classForm->fillDefaultValues(
        $entities,
        $skip
      );
      if (!$out) {
        return array(FALSE, $output, $msg);
      }

      $out = $classForm->submit();
      if (!$out) {
        return array(
          FALSE,
          $output,
          "Could not create $original_class entity."
        );
      }

      $object = $classForm->getEntityObject();
      $output[] = $object;
      $entities[Utilities::convertTitleCaseToUnderscore(
        $original_class
      )][$object->getId()] = $object;
    }

    if (sizeof($output) == 1) {
      return $output[0];
    }

    return array(TRUE, $output, "");
  }
}