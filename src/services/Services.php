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

  // If a user agent partially matches any of these strings, the 'check' method
  // will pass regardless of any other user defined rules.
  private $agentExceptions = ['APIs-Google', 'Mediapartners-Google', 'AdsBot-Google-Mobile', 'AdsBot-Google-Mobile',
  'AdsBot-Google', 'Googlebot-Image', 'Googlebot', 'Googlebot-News', 'Googlebot', 'Googlebot-Video',
  'Mediapartners-Google', 'AdsBot-Google-Mobile-Apps', 'FeedFetcher-Google'];

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

    $regex = '/('.implode("|",$this->agentExceptions).')/i';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    if (preg_match($regex, $userAgent)) {
      return true;
    }

    $valid = false;

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
          $rule['name'] = StringHelper::toKebabCase($setting);
        }

      }

      array_push($rules, $rule);
    }

    // Now we have all the rules and conditions...

    if (!empty($rules)) {

      $index = array_search($this->name, array_column($rules, 'name')) ?? false;

      // In some cases where user agents versions can't be read,
      // they will default to 0. This can happen on new browser releases.
      // To avoid the new browsers from being blocked, we have to allow these
      // regardless of any other criteria.
      if ($this->version == 0) {
        return true;
      }

      // Check to see if the current browser name exists in any of the given argument rules
      if ( $index !== false) {

        $name = $rules[$index]['name'];
        $condition = $rules[$index]['condition'] ?? false;
        $version = $rules[$index]['version'] ?? false;

        if ($condition && $version) {

          // echo 'This is ' . $name . ' version ' . $this->version . '. And this website supports anything that is ' . $condition . ' version ' . $version . '<br />';

          // If there is a condition to validate
          switch ($condition) {
            case ">=":
              $valid = $this->version >= $version;
              break;
            case ">":
              $valid = $this->version > $version;
              break;
            case "<=":
              $valid = $this->version <= $version;
              break;
            case "<":
              $valid = $this->version < $version;
              break;
          }

        } elseif ($version) {

          // echo 'This is ' . $name . ' version ' . $this->version . '. And this website only supports version '. $version . '<br />';

          $valid = $version == $this->version;

        } else {

          // echo 'This is ' . $name . ' version ' . $this->version . '. And this website only supports any version of this browser.<br />';

          $valid = $name == $this->name;

        }
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
    if ( !$this->check($criteria) ) {
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
