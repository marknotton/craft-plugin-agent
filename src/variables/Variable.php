<?php

namespace marknotton\agent\variables;

use Jenssegers\Agent\Agent as JenssegersAgent;

use Craft;
use craft\helpers\Template;
use craft\helpers\UrlHelper;
use craft\helpers\StringHelper;
use craft\helpers\Html;
use Twig\Markup;

class Variable extends JenssegersAgent {

  public function config(): array 
  {
    return Craft::$app->getConfig()->getConfigFromFile('agent');
  }


  /**
   * Adjust version numbers to floor it to it's major release number (no decimal numbers) 
   * @param String $propertyName - Agent::browser(), Agent::platform(), etc..
   * @param String $type - 'float' or 'string'
   * @example Php -  Agent::version()
   * @example Twig - {{ craft.agent.version() }}
   * @return string
   */
  public function version($propertyName = null, $type = self::VERSION_TYPE_FLOAT): string
  {
    $version = parent::version($propertyName ?? parent::browser(), $type);

    if ( $type == self::VERSION_TYPE_FLOAT && (intval($version) == $version || floatval($version) == $version) ) {
      $version = floor($version);
    }

    return $version;
  }

  /**
   * Register an IIFE Agent.js file on the front-end. This defines global properties
   * to the window element for the browser name and version plus device types.
   * @example {{ craft.agent.registerAgentJsFile() }}
   * @example Agent::registerAgentJsFile();
   */
  public function registerAgentJsFile($useCompressedFile = true, $position = null): void
  {

    $compressedFile = $useCompressedFile === TRUE ? 'min.' : '';
    $asset          = Craft::$app->assetManager->getPublishedUrl('@agent/assets/agent.'.$compressedFile.'js', true);
    $position       = is_null($position) ? Craft::$app->view::POS_HEAD : $position;
    $prelaodOptions = ['position' => $position, 'as' => 'script', 'rel' => 'preload'];
    $scriptOptions  = ['position' => $position, 'async' => true, 'defer' => true];

    $scriptOptions = array_merge($this->commonData('data-'), $scriptOptions);

    // We'll refactor the registerCssFile method output to create a script preload <link> tag.
    Craft::$app->view->registerCssFile($asset, $prelaodOptions);
    Craft::$app->view->registerJsFile($asset, $scriptOptions);
  }


  /**
   * Undocumented function
   *
   * @param string $prefix
   * @param string $suffix
   * @return Array
   */
  public function commonData($prefix = "", $suffix = ""): array {
    return array_map([StringHelper::class, 'toKebabCase'], [
      $prefix.'browser-name'.$suffix    => $this->browser(),
      $prefix.'browser-version'.$suffix => $this->version(),
      $prefix.'device'.$suffix          => $this->deviceType()
    ]);
  }

  /**
   * If a user agent partially matches any of these strings, the 'check' method
   * will pass regardless of any other user defined rules.
   */ 
  private array $userAgentWhitelist = [
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
   * change the user agent string to this "Mac OS Chrome 103" user agent atring.
   * This essentially emulates a modern browser to bots you want to allow.
   */ 
  private string $userAgentFallback = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36';

  /**
   * Build data attribute markup
   * @deprecated Depending on how your managing your template caching, 
   * this method could break things for you as the attributes are inline-line.  
   * You should inject the agent.min.js file or enable the 'setAttributesToHTML' 
   * method via the /configs/app.php to work around caching issues. 
   * If you really need it, I suggest you define your attributes using
   * Crafts own attr twig extension
   * @example <html {{ attr({ data : craft.agent.commonData() })}}>
   * @example <html {{ craft.agent.data() }}>
   * @return Markup
   */
  public function data(): Markup 
  {
    $attributesAsString = Html::renderTagAttributes($this->commonData('data-'));
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
          $valid = version_compare($browserVersion, $version, $condition);


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
   * Redirect to a specific page if the criteria is matched
   * @param  mixed   $criteria   Object or string the adheres to the search criteria syntax
   * @param  string  $redirect   Relative URL to redirect to
   * @param  integer $statuscode Amend the status code
   */
  public function redirect($criteria, $redirect, $statuscode = 302): void
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

    // $config = $this->config();
    if (  $config= Craft::$app->getConfig()->getConfigFromFile('agent')  ) {
      if ( array_key_exists('userAgentWhitelist', $config) ) {
        $this->userAgentWhitelist = array_merge($this->userAgentWhitelist, $config['userAgentWhitelist']);
      }
      if ( array_key_exists('userAgentFallback', $config) ) {
        $this->userAgentFallback =  $config['userAgentFallback'];
      }
    }

    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    if ( !empty($userAgent)) {
      $regex = '/('.implode("|",$this->userAgentWhitelist).')/i';
      if (preg_match($regex, $userAgent)) {
        $this->setUserAgent($this->userAgentFallback);
      }
    }
  }

  /**
   * Full user agent is returned when Agent is treated as a string
   * @example {{ craft.agent }}
   * @return String Mozilla/5.0 (Macintosh; Intel Mac...
   */
  public function __toString(): string
  {
    return $this->userAgent;
  }

}
