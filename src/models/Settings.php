<?php

namespace marknotton\agent\models;

use craft\base\Model;
use craft\validators\ArrayValidator;
use craft\events\ModelEvent;

class Settings extends Model {

  public bool $injectAgentJsAsset = false; 
  public array $whitelist = [];

  public function rules(): array 
  {
    return [
      ['injectAgentJsAsset', 'boolean'],
      ['whitelist', ArrayValidator::class],
    ];
  }

  public static function onBeforeSaveSettings(ModelEvent $event): ModelEvent
  {
    $plugin = $event->sender;
    $settings = $plugin->getSettings();

    // Modify the whitelist data into a associative array needded for the 
    // editableTable fields. Also remove any empty rows.
    $settings['whitelist'] = is_string($settings['whitelist']) ? [] : array_filter(array_column($settings['whitelist'], 'item'));
    return $event;
  }

  public static function beforeSaveSettings() {
    echo 'beforeSaveSettings'; die; 
  }

}
