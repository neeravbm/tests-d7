<?php
/**
 * Created by PhpStorm.
 * User: neeravm
 * Date: 11/25/14
 * Time: 3:39 PM
 */

namespace tests\phpunit_tests\core;


class Utilities {

  public static function convertUnderscoreToTitleCase($input) {
    $output = str_replace("_", " ", strtolower($input));
    $output = ucwords($output);
    $output = str_replace(" ", "", $output);

    return $output;
  }

  public static function convertTitleCaseToUnderscore($input) {
    return strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', "_$1", $input));
  }

  public static function getRandomString($length = 10) {
    return substr(
      str_shuffle(
        "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"
      ),
      0,
      $length
    );
  }
}