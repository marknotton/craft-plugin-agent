<?php

namespace marknotton\agent\services;

use Jenssegers\Agent\Agent as JenssegersAgent;

use Craft;
use craft\helpers\Template;
use craft\helpers\UrlHelper;
use craft\helpers\StringHelper;
use craft\helpers\Html;
use Twig\Markup;

class Services extends JenssegersAgent {

  /**
   * If a user agent partially matches any of these strings, the 'check' method
   * will pass regardless of any other user defined rules.
   */ 
  private array $userAgentExceptions = [
    'APIs-Google', 
    'Mediapartners-Google', 
    'AdsBot-Google-Mobile', 
    'AdsBot-Google-Mobile',
    'AdsBot-Google', 
    'Googlebot-Image', 
    'Googlebot', 
    'Googlebot-News', 
    'Googlebot', 
    'Googlebot-Video',
    'Mediapartners-Google', 
    'AdsBot-Google-Mobile-Apps', 
    'FeedFetcher-Google'
  ];
  
  /**
   * If one of the above exceptions is a partial match to the users current User Angent,
   * change the user agent string to this (Chrome 81 Mac)
   * @see https://developers.whatismybrowser.com/useragents/explore/software_type_specific/web-browser/2
   */ 

  private string $userAgentFallback = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36';

  /**
   * Get the full name of the browser or version number
   * @return object
   */

  public function full(): array
  {
    $browser = $this->browser();

    $agent = [
      "name" => $browser == 'IE' ? "Internet Explorer" : $browser,
      "version" => $this->version($browser),
    ];

    return $agent;
  }

  /**
   * Return data attribute specifically for the html/body tags
   * @return Markup
   */

  public function data(): Markup 
  {

    $attributes = [ 
      'browser'  => StringHelper::toKebabCase(parent::browser()).' '.self::version(parent::browser(), null, true),
      'platform' => StringHelper::toKebabCase(parent::platform()),
      'device'   => StringHelper::toKebabCase(parent::deviceType())
    ];

    if ( $attributes['device'] == 'phone' ) {
      $attributes['device'] = 'mobile';
    }

    $attributesAsString = Html::renderTagAttributes(['data' => $attributes]);

    return Template::raw($attributesAsString);
  }


  /**
   * Checks for browser types and versions
   * @return bool
   */

  public function check(): bool
  {

    // Atleast one browser string arugment should be passed
    if ( func_num_args() < 1 ){
      return false;
    }

    // Empty user agent strings will always return false
    if ( empty($_SERVER['HTTP_USER_AGENT'] ?? '') ) {
      return false;
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

        if (preg_match('[<|>|=>|<=|==|!=]', $setting)) {
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

    $browserName = StringHelper::toKebabCase($this->browser());
    $browserVersion = (string) $this->version($this->browser(), true);

    // Now we have all the rules and conditions...

    if (!empty($rules)) {

      $index = array_search($browserName, array_column($rules, 'name')) ?? false;

      // If neither agent name or version can be found, return false. 
      if ( $index === false && version_compare($browserVersion, 0) == 0) {
        return false;
      }

      // Check to see if the current browser name exists in any of the given argument rules
      if ( $index !== false) {

        $name      = $rules[$index]['name'];
        $condition = $rules[$index]['condition'] ?? false;
        $version   = $rules[$index]['version'] ?? false;


        // In some cases where user agents versions can't be read,
        // they will default to 0. This can happen on new browser releases.
        // To avoid the new browsers from being blocked, we have to allow these
        // regardless of any version criteria.
        if ($browserVersion == 0 && $name == $browserName) {
          return true;
        }

        if ($condition && $version) {

          // echo 'This is ' . $name . ' version ' . $browserVersion . '. And this website supports anything that is ' . $condition . ' version ' . $version . '<br />';

          // If there is a condition to validate
          switch ($condition) {
            case ">=":
              $valid = version_compare($browserVersion, $version, '>=');
              break;
            case ">":
              $valid = version_compare($browserVersion, $version, '>');
              break;
            case "!=":
              $valid = version_compare($browserVersion, $version, '!=');
              break;
            case "==":
              $valid = version_compare($browserVersion, $version, '==');
              break;
            case "<=":
              $valid = version_compare($browserVersion, $version, '<=');
              break;
            case "<":
              $valid = version_compare($browserVersion, $version, '<');
              break;
          }

        } elseif ($version) {

          // echo 'This is ' . $name . ' version ' . $browserVersion . '. And this website only supports version '. $version . '<br />';

          $valid = version_compare($browserVersion, $version, '==');

        } else {

          // echo 'This is ' . $name . ' version ' . $browserVersion . '. And this website only supports any version of this browser.<br />';

          $valid = $name == $browserName;

        }
      }
    }

    return $valid;

  }

  /**
   * Adjust version numbers without any decimal places 
   * @param Bool $simplify set to true if you want to return the roduned down version number 
   * @example Php -  Agent::version()
   * @example Twig - {{ agent.version() }}
   * @return string
   */
  public function version($propertyName = null, $simplify = false, $type = self::VERSION_TYPE_STRING): string
  {
    if ( is_null($propertyName) ) { $propertyName = parent::browser(); }

    if ( is_null($type)) { $type = self::VERSION_TYPE_STRING; }
    
    $version = parent::version($propertyName, $type);

    if ($simplify && (intval($version) == $version || floatval($version) == $version)) {
      $version = floor($version);
    }

    return $version;
  }

  /**
   * Redirect to a specific page if the criteria is matched
   * @param  mixed   $criteria   Object or string the adheres to the search criteria syntax
   * @param  string  $redirect   Relative URL to redirect to
   * @param  integer $statuscode Amend the status code
   */
  public function redirect($criteria, $redirect = 'browser', $statuscode = 302): void
  {
    if ( !$this->check($criteria) ) {
      $url = UrlHelper::url($redirect);
      Craft::$app->getResponse()->redirect($url, $statuscode);
    }
  }

  /**
   * Innitialisers
   */
  public function init(): void
  {
    if ( $config = Craft::$app->getConfig()->getConfigFromFile('agent') ?? false ) {
      if ( array_key_exists('userAgentExceptions', $config) ) {
        $this->userAgentExceptions = array_merge($this->userAgentExceptions, $config['userAgentExceptions']);
      }
      if ( array_key_exists('userAgentFallback', $config) ) {
        $this->userAgentFallback =  $config['userAgentFallback'];
      }
    }

    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    if ( !empty($userAgent)) {
      $regex = '/('.implode("|",$this->userAgentExceptions).')/i';
      if (preg_match($regex, $userAgent)) {
        $this->setUserAgent($this->userAgentFallback);
      }
    }
  }


}
