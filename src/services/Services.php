<?php

namespace marknotton\agent\services;

use marknotton\agent\Agent;
use Jenssegers\Agent\Agent as JenssegersAgent;

use Craft;
use craft\base\Component;
use craft\helpers\Template;
use craft\helpers\UrlHelper;
use craft\helpers\StringHelper;


class Services extends Component {

  public $agent;    // Global variable for easy access for the additional options: https://github.com/jenssegers/agent
  public $name;     // Global variable for easy access: {{ browser.name }}
  public $version;  // Global variable for easy access: {{ browser.version }}

  /**
   * Get the full name of the browser or version number
   * @return object
   */
  public function full() {
    $browser = $this->agent->browser();

    $agent = [
      "name" => $browser == 'IE' ? "Internet Explorer" : $browser,
      "version" => $this->agent->version($browser),
    ];

    return $agent;
  }

  /**
   * Return data attribute specifically for the html/body tags
   * @return object
   */
  public function data() {

    $version = $this->version == 0 ? '' : ' '.$this->version;
    $data = 'data-browser="'.$this->name.$version.'" ';
    $data .= ' data-platform="'.str_replace(' ', '', strtolower($this->agent->platform())).'" ';

    if ( $this->agent->isDesktop() ) { $device = 'desktop'; }
    else if ( $this->agent->isTablet() ) { $device = 'tablet'; }
    else if ( $this->agent->isMobile() ) { $device = 'mobile'; }
    else { $device = null; }
    if (!is_null($device)) { $data .= ' data-device="'.strtolower($device).'" '; }

    return Template::raw($data);
  }


  /**
   * Checks for browser types and versions
   * @return boolean
   */
  public function check() {

    // Atleast one browser string arugment should be passed
    if ( func_num_args() < 1 ){
      return false;
    }

    $valid = true;

    $arguments = func_get_args();

    if (is_array($arguments[0])) {
      $arguments = $arguments[0];
    }

    $rules = [];

    foreach ($arguments as &$argument) {

      $rule = [];

      // Check all the given settings
      foreach (explode(' ', $argument) as &$setting) {

        if (preg_match('[<|>|=>|<=]', $setting)) {
          // If a greater or less than condition is passed
          $rule['condition'] = $setting;
        } else if (ctype_digit($setting)) {
          // If number, add as version
          $rule['version'] = $setting;
        } else if ($setting !== '='){
          // Anything else is assumed to be the agents name
          $rule['name'] = $setting;
        }

      }

      array_push($rules, $rule);
    }

    // echo '<pre>';
    // var_dump($rules);
    // echo '</pre>';
    // if (!empty($agents)) {
    //   if (in_array($this->name, $agents)) {
    //     if (!empty($versions)) {
    //
    //     }
    //   } else {
    //
    //   }
    // }

//       if (!empty($agents)) {
//
//         var_dump($agents);
//
//         $checkVersion = null;
//
//         // Versions
//         // If there is at least one version to check do the following:
//         if (!empty($versions)) {
//           // Validate any of the given versions
//           foreach ($versions as &$version) {
//
//
//             // Only update check variable if it is null or true. Once it's false, it stays false
//             if (is_null($checkVersion) || $checkVersion != false) {
//               if (isset($condition)){
//
//                 // If there is a condition to validate
//                 switch ($condition) {
//                   case ">=":
//                     $checkVersion = $version >= $this->version;
//                     break;
//                   case ">":
//                     $checkVersion = $version > $this->version;
//                     break;
//                   case "<=":
//                   echo '<pre>';
//                   echo $agents[0] . '<br>';
//                   echo $this->version . '<br>';
//                   echo $version . '<br>';
//                   echo '</pre>';
//                     $checkVersion = $version <= $this->version;
//                     break;
//                   case "<":
//                     $checkVersion = $version < $this->version;
//                     break;
//                 }
//               } else {
//                 // Otherwise just check if the given version is exact
//                 $checkVersion = $version == $this->version;
//               }
//             }
//           }
//         }
//
// echo $checkVersion ? 'valid' : 'not valid';
//
//         $checkBrowser = null;
//
//         // Browsers
//         // Check all the browsers
//         foreach ($agents as &$agent) {
//           // If any of the agents don't match, set false
//           if($this->agent->is($agent)) {
//             $checkBrowser = true;
//           }
//         }
//
//         // The prenultimate validation.
//         // If the version is null or valid, and the browser is valid change the
//         // valid variable to true;
//         if (is_null($valid) || $valid != false) {
//           if (is_null($checkVersion) || $checkVersion == true) {
//             if ($checkBrowser == true) {
//               $valid = true;
//             }
//           }
//         }
//
//       } else {
//         // There were no agents defined
//         $valid = false;
//       }
//     }

    return $valid;

  }

  /**
   * Redirect to a specific page if the criteria is matched
   * @param  mixed   $criteria   Object or string the adheres to the search criteria syntax
   * @param  string  $redirect   Relative URL to redirect to
   * @param  integer $statuscode Amend the status code
   */
  public function redirect($criteria, $redirect = 'browser', $statuscode = 302) {
    if ( $this->check($criteria) ) {
      $url = UrlHelper::url($redirect);
      // Craft::$app->getResponse()->redirect($url, $statuscode);
    }
  }

  /**
   * Innitialisers
   * @return none
   */
  public function init() {
    $this->agent = new JenssegersAgent();
    $this->name = StringHelper::toKebabCase($this->agent->browser());
    $this->version = floor($this->agent->version($this->agent->browser()));
  }

  /**
   * Use Jenssegers agent methods as fallbacks should they not be defined in this services class
   * @param  [type] $method [description]
   * @param  [type] $args   [description]
   * @return [type]         [description]
   */
  public function __call($method, $args) {
    return call_user_func_array( array($this->agent, $method), $args );
  }
}
