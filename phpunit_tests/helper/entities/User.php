<?php
/**
 * Created by PhpStorm.
 * User: Anil
 * Date: 3/15/14
 * Time: 5:27 PM
 */

namespace tests\phpunit_tests\helper\entities;

use tests\phpunit_tests\helper\forms as forms;

class User extends Entity {

  /**
   * Default constructor for the user object.
   *
   * @param int $uid
   *   User id.
   */
  public function __construct($uid = NULL) {
    if (!is_null($uid) && is_numeric($uid)) {
      $this->setEntity(user_load($uid));
    }
  }

  /**
   * Log a user in.
   *
   * @param string $username
   *   Username.
   * @param string $password
   *   Password.
   *
   * @return mixed $user
   *   User object if the user logged in successfully and an array of errors, otherwise.
   */
  public static function login($username, $password) {
    $userLoginForm = new forms\UserLoginForm();
    $userLoginForm->fillValues(
      array(
        'name' => $username,
        'pass' => $password,
      )
    );
    $uid = $userLoginForm->submit();
    if (is_array($uid)) {
      return $uid;
    }
    else {
      return new User($uid);
    }
  }

  /**
   * Register a new user.

   * @param string $username
   *   Username.
   * @param string $email
   *   Email address.
   * @param string $password
   *   Password.
   *
   * @return mixed $user
   *   User object if the user logged in successfully and an array of errors, otherwise.
   */
  public static function registerUser($username, $email, $password) {
    $userRegisterForm = new forms\UserRegisterForm();
    $userRegisterForm->fillValues(
      array(
        'name' => $username,
        'mail' => $email,
        'pass' => array(
          'pass1' => $password,
          'pass2' => $password,
        ),
      )
    );

    $uid = $userRegisterForm->submit();
    if (is_array($uid)) {
      return $uid;
    }
    else {
      return new User($uid);
    }
  }

  /**
   * Delete the user.
   */
  public function delete() {
    $userCancelConfirmForm = new forms\UserCancelConfirmForm($this->getEntity());
    $userCancelConfirmForm->submit();
  }

  /**
   * Returns the user id.
   *
   * @return int $uid
   *   User id.
   */
  public function getUid() {
    return $this->getEntity()->uid;
  }

  /**
   * Returns email address of the user.
   *
   * @return string $email
   *   Email address.
   */
  public function getEmailAddress() {
    return $this->getEntity()->mail;
  }

  /**
   * Returns the account object.
   *
   * @return object $account
   *   Account object.
   */
  public function getUser() {
    return $this->getEntity();
  }

  /**
   * Log the currently logged in user out and load the anonymous user.
   */
  public function logout() {
    global $user;
    module_invoke_all('user_logout', $user);
    // Destroy the current session, and reset $user to the anonymous user.
    session_destroy();
    // Load Anonymous user object
    $user = user_load(0);
  }

  /**
   * Log a user in programmatically.
   *
   * @param string $username
   *   Username.
   *
   * @return object $user
   *   User object.
   */
  public static function loginProgrammatically($username) {
    $account = user_load_by_name($username);
    global $user;
    $user = $account;
    module_invoke_all('user_login', $user);
    return new User($account->uid);
  }
}