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
   * This allows the Agent plugin to modify the <html> attribute even on cached templates. 
   * We do this very early on to avoid any flashes of unstyled content that would 
   * othersie be handled with Javascript.
   * @example 'on afterRequest' => 'marknotton\agent\Agent::setAttributesToHTML'
   * @todo This no longer works on Craft 4. Craft::$app->response->data returns nothing.
   */
  public static function setAttributesToHTML(): void  
  {

    if(Craft::$app->request->isSiteRequest) {

      if ( version_compare(Craft::$app->getVersion(), '4.0', '>=') ) {

        throw new \Exception('The "setAttributesToHTML" method doesn\'t currently work in Craft 4.0 or above. This is under review.');

      } else {

        $html = &Craft::$app->response->data;

        // VarDumper::dump([
        //   'name' => get_class(Craft::$app->response),
        //   'methods' => get_class(Craft::$app->response),
        //   'properties' => get_object_vars(Craft::$app->response)
        // ]); die;

        if ( !empty($html) && is_string($html)) { 
      
          preg_match('/<html.*?>/m', $html, $matches);
      
          if ( !empty($matches) ) { 
      
            // Only looking for the first tag found. 
            $oldTag = $matches[0]; 
            
            $newTag = Html::modifyTagAttributes($oldTag, self::$agent->commonData('data-'));
      
            $html = str_replace($oldTag, $newTag, $html);
          }
        }
      }
    }
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
