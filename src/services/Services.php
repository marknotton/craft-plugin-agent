<?php

namespace marknotton\agent\services;

use marknotton\agent\Agent;
use Jenssegers\Agent\Agent as JenssegersAgent;

use Craft;
use craft\base\Component;
use craft\helpers\Template;
use craft\helpers\UrlHelper;

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
  public function is() {

    // Atleast one browser sting arugment should be passed
    if ( func_num_args() < 1 ){
      return false;
    }

    $valid = null;

    $arguments = func_get_args();

    foreach ($arguments as &$settings) {

      $agents = [];
      $versions = [];
      $condition = null;

      if (gettype($settings) == 'string') {
        $explodeSettings = explode(' ', $settings);
      } else {
        $explodeSettings = $settings;
      }

      // Check all the given settings
      foreach ($explodeSettings as &$setting) {

        if (preg_match('[<|>|=>|<=]', $setting)) {
          // If a greater or less than condition is passed
          if (is_null($condition)) {
            $condition = $setting;
          }
        } else if (ctype_digit($setting)) {
          // If number, add as version
          array_push($versions, $setting);
        } else {
          // Anything else is assumed to be the agents name
          array_push($agents, $setting);
        }
      }

      // If mutliple versions and a condition are used at the same time
      // Recreate the version array with only the more relivant version number.
      if (!is_null($condition) && count($versions) >= 2) {
        switch ($condition) {
          case ( $condition == ">" || $condition == "=>" ):
            $versions = array(max($versions));
          break;
          case ( $condition == "<" || $condition == "=<" ):
            $versions = array(min($versions));
          break;
        }
      }


      if (!empty($agents)) {

        $checkVersion = null;

        // Versions
        // If there is at least one version to check do the following:
        if (!empty($versions)) {
          // Validate any of the given versions
          foreach ($versions as &$version) {
            // Only update check variable is it is null or true. Once it's false, it stays false
            if (is_null($checkVersion) || $checkVersion != false) {
              if (isset($condition)){
                // If there is a condition to validate
                switch ($condition) {
                  case ">=":
                    $checkVersion = $this->version >= $version;
                    break;
                  case ">":
                    $checkVersion = $this->version > $version;
                    break;
                  case "<=":
                    $checkVersion = $this->version <= $version;
                    break;
                  case "<":
                    $checkVersion = $this->version < $version;
                    break;
                }
              } else {
                // Otherwise just check if the given version is exact
                $checkVersion = $this->version == $version;
              }
            }
          }
        }

        $checkBrowser = null;

        // Browsers
        // Check all the browsers
        foreach ($agents as &$agent) {
          // If any of the agents don't match, set false
          if($this->agent->is($agent)) {
            $checkBrowser = true;
          }
        }

        // The prenultimate validation.
        // If the version is null or valid, and the browser is valid change the
        // valid variable to true;
        if (is_null($valid) || $valid != false) {
          if (is_null($checkVersion) || $checkVersion == true) {
            if ($checkBrowser == true) {
              $valid = true;
            }
          }
        }

      } else {
        // There were no agents defined
        $valid = false;
      }
    }

    return $valid;

  }

  /**
   * Redirect to a specific page if the criteria is matched
   * @param  mixed   $criteria   Object or string the adheres to the search criteria syntax
   * @param  string  $redirect   Relative URL to redirect to
   * @param  integer $statuscode Amend the status code
   */
  public function redirect($criteria, $redirect = 'browser', $statuscode = 302) {
    if ( $this->is($criteria) ) {
      $url = UrlHelper::url($redirect);
      Craft::$app->getResponse()->redirect($url, $statuscode);
    }
  }

  /**
   * Innitialisers
   * @return none
   */
  public function init() {
    $this->agent = new JenssegersAgent();
    $this->name = strtolower($this->agent->browser());
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
