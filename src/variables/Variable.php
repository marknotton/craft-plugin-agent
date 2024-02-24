<?php

namespace marknotton\agent\variables;

use Jenssegers\Agent\Agent as JenssegersAgent;

use Craft;
use craft\helpers\Template;
use craft\helpers\UrlHelper;
use craft\helpers\StringHelper;
use craft\helpers\Html;
use Twig\Markup;
use marknotton\agent\Agent;

class Variable extends JenssegersAgent {

  const VERSION_TYPE_FLOOR = 'floor';

  /**
   * Adjust version numbers to floor it to it's major release number (no decimal numbers) 
   * @param String $propertyName - Agent::browser(), Agent::platform(), etc..
   * @param String $type - floor, float or text
   * @param Bool   $floor - Wether to truncate the version number to a whole number
   * @example Php -  Agent::version()
   * @example Twig - {{ craft.agent.version() }}
   * @return string
   */
  public function version($propertyName = null, $type = self::VERSION_TYPE_FLOOR): string
  { 

    if ( in_array($propertyName, ['text', 'floor', 'float']) ) {
      $type = $propertyName;
      $propertyName = null;
    }

    if ( $type == self::VERSION_TYPE_FLOOR ) {
      $version = parent::version($propertyName ?? parent::browser(), self::VERSION_TYPE_FLOAT);
    } else {
      $version = parent::version($propertyName ?? parent::browser(), $type);
    }

    if ( $type == self::VERSION_TYPE_FLOOR && (intval($version) == $version || floatval($version) == $version) ) {
      $version = floor($version);
    }

    return $version;
  }

  /**
   * Register an IIFE agent.min.js 
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
   * Returns common user agent properties. 
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
   * Checks for browser type and versions
   * @param string|array each argument must be a string, or the first argument can be an array of strings
   * @return bool returns 'true' if any one criteria is true
   */
  public function check(...$criterias): bool
  {

    if ( empty($this->userAgent) ) {
      return false;
    }

    $whitelist = Agent::$instance->getSettings()->whitelist ?? false;

    if ( !empty($this->userAgent) && !empty($whitelist) ) {
      foreach ($whitelist as $item) {
        if(strpos($this->userAgent, $item) !== false) {
          return true; 
        }
      }
    }

    if ( count($criterias) ) {

      if (count($criterias) == 1 && is_array($criterias[0])) {
        $criterias = $criterias[0];
      }

      $browserName = StringHelper::toKebabCase($this->browser());
      $browserVersion = $this->version();

      foreach ($criterias as $criteria) {
        
        $browser  = null;
        $version  = null;
        $operator = null;
        $negate   = false;

        $parts = explode(' ', $criteria);

        foreach ($parts as $part) {
          if ( preg_match('[==|===|!=|<>|!==|<|>|<=|>=]', $part) ) {
            $operator = $part;
          } else if ($part === 'not' ) {
            $negate = true;
          } else if (ctype_digit($part) ) {
            $version = $part;
          } else {
            $browser = trim($part);
          }
        }

        if ( $browser ) {

          if ($negate == true) {
            if ( $browser !== $browserName ) {
              return true;
            } else if ($operator && $version) {
              if (!version_compare($browserVersion, $version, $operator) ) {
                return true;
              }
            }
          } else if ( $browser === $browserName ) {
            if ($operator && $version) {
              if ($version == 0 || version_compare($browserVersion, $version, $operator) ) {
                return true;
              }
            } else {
              return true;
            }
          }
        }
      }
    }

    return false;

  }

  /**
   * Redirect to a specific page if the criteria is matched
   * @param mixed   $criteria   Array of criteria
   * @param string  $redirect   Relative URL to redirect to
   * @param integer $statusCode Amend the status code
   */
  public function redirect($criteria, $redirect, $statusCode = 302): void
  {
    $criteria = is_string($criteria) ? [$criteria] : $criteria;
    if ( $this->check(...$criteria) ) {
      $url = UrlHelper::url($redirect);
      Craft::$app->getResponse()->redirect($url, $statusCode);
    }
  }

}
