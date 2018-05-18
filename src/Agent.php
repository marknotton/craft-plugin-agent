<?php
/**
* Agent plugin for Craft CMS 3.x
*
* Query the server-side information from the users agent data.
*
* @link      https://github.com/marknotton/craft-plugin-agent
* @copyright Copyright (c) 2018 Mark Notton
*/

namespace marknotton\agent;

use marknotton\agent\services\Services;
use marknotton\agent\variables\Variables;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;


class Agent extends Plugin {

  public static $plugin;

  public $schemaVersion = '1.0.6';

  public function init() {

    parent::init();
    self::$plugin = $this;

    Craft::setAlias('@agent', $this->getBasePath());

    $this->setComponents([
      'services' => \marknotton\agent\services\Services::class,
    ]);

    $twig = Craft::$app->view->getTwig(null, ['safe_mode' => false]);
    $twig->addGlobal('agent', Agent::$plugin->services);

    Event::on(
      CraftVariable::class,
      CraftVariable::EVENT_INIT,
      function (Event $event) {
        $variable = $event->sender;
        $variable->set('agent', Variables::class);
      }
    );

    Craft::info(
      Craft::t(
        'agent',
        '{name} plugin loaded',
        ['name' => $this->name]
      ),
      __METHOD__
    );
  }

}
