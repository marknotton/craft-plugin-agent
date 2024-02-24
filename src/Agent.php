<?php

namespace marknotton\agent;

use Craft;
use craft\base\Plugin;
use craft\base\Model;
use craft\web\twig\variables\CraftVariable;
use Jenssegers\Agent\Agent as JenssegersAgent;
use craft\helpers\Html;
use marknotton\agent\variables\Variable;
use marknotton\agent\models\Settings;

// use Symfony\Component\VarDumper\VarDumper;

use yii\base\Event;

class Agent extends Plugin {

  /**
   * @var Agent
   */
  public static Plugin $instance;

  /**
   * @var JenssegersAgent
   */
  public static JenssegersAgent $agent;

  public function init(): void
  {

    parent::init();

    $this->hasCpSettings = true;

    self::$instance = $this;

    self::$agent = new Variable();

    Craft::setAlias('@agent', $this->getBasePath());

    Event::on(
      CraftVariable::class,
      CraftVariable::EVENT_INIT,
      static function (Event $event) {
        $variable = $event->sender;
        $variable->set('agent', self::$agent);
      }
    );

    Event::on(
      Agent::class,
      Agent::EVENT_BEFORE_SAVE_SETTINGS,
      [Settings::class, 'onBeforeSaveSettings']
    );

    if (Craft::$app->request->isSiteRequest && self::getInstance()->settings->injectAgentJsAsset) {
      self::$agent->registerAgentJsFile();
    } 

  }

  protected function createSettingsModel(): Model {
    return new Settings();
  }

  protected function settingsHtml(): string {
    return Craft::$app->view->renderTemplate('agent/settings', [
      'settings' => $this->getSettings()
    ]);
  }

  /**
   * This magic method adds a little syntax suger to querying agent. Both of
   * these example will call the same method.
   * @example Agent::$agent->browser()
   * @example Agent::browser()
   */
  public static function __callStatic($name, $arguments) {
    return self::$agent->$name(...$arguments);;
  }

}
