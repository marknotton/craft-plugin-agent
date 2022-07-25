<?php

namespace marknotton\agent\models;

use craft\base\Model;
use craft\validators\ArrayValidator;
use craft\base\ElementInterface;
use craft\events\ModelEvent;

class Settings extends Model {

  public bool $injectAgentJsAsset = false; 
  public array $whitelist = [];

  private array $defaultWhitelist = [ 
    'APIs-Google', 
    'Mediapartners-Google', 
    'Googlebot', 
    'AdsBot-Google', 
    'Googlebot-Image', 
    'FeedFetcher-Google'
  ];

  public function rules(): array 
  {
    return [
      ['injectAgentJsAsset', 'boolean'],
      [['whitelist'], ArrayValidator::class],
    ];
  }


  public static function onBeforeSaveSettings(ModelEvent $event): ModelEvent
  {
    $plugin = $event->sender;
    $settigs = $plugin->getSettings();
    // Modify the whitelist data into a associative array needded for the 
    // editableTable fields. Also remove any empty rows.
    $settigs['whitelist'] = is_string($settigs['whitelist']) ? [] : array_filter(array_column($settigs['whitelist'], 'item'));
    return $event;
  }



}
