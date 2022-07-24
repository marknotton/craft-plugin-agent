<?php

namespace marknotton\agent\models;

use craft\base\Model;
use craft\validators\ArrayValidator;

class Settings extends Model {

  public $injectAgentJsAsset = false; 
  public $whitelist = [ 
    'APIs-Google', 
    'Mediapartners-Google', 
    'AdsBot-Google', 
    'Googlebot-Image', 
    'Googlebot', 
    'FeedFetcher-Google'
  ];

  public function rules(): array 
  {
    return [
      ['injectAgentJsAsset', 'boolean'],
      [['whitelist'], ArrayValidator::class],
    ];
  }

}
