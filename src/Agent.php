<?php
/**
* Agent plugin for Craft CMS 3
*
* Query the server-side information from the users agent data.
*
* @link      https://github.com/marknotton/craft-plugin-agent
* @copyright Copyright (c) 2018 Mark Notton
*/

namespace marknotton\agent;

use marknotton\agent\services\Services;

use Craft;
use craft\base\Plugin;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;


class Agent extends Plugin 
{
  /**
   * @var Agent
   */
  public static Plugin $plugin;
  
  /**
  * @var String
  */
  public string $schemaVersion = '1.0.0';

  public function init(): void
  {

    parent::init();

    self::$plugin = $this;

    Craft::setAlias('@agent', $this->getBasePath());

    $this->setComponents(['services' => Services::class]);

    Craft::$app->view->getTwig()->addGlobal('agent', Agent::$plugin->services);
    
    Event::on(
      CraftVariable::class,
      CraftVariable::EVENT_INIT,
      static function (Event $event) {
        $variable = $event->sender;
        $variable->set('agent', Services::class);
      }
    );

  }

}
