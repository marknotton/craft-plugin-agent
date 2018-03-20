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

use marknotton\agent\services\AgentService;
use marknotton\agent\variables\AgentVariable;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;

/**
 * Class Agent
 *
 * @author    Mark Notton
 * @package   Agent
 * @since     1.0.0
 *
 * @property  AgentServiceService $agentService
 */
class Agent extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var Agent
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->setComponents([
            'agentService' => \marknotton\agent\services\AgentService::class,
        ]);

        $twig = Craft::$app->view->getTwig(null, ['safe_mode' => false]);
        $twig->addGlobal('agent',  Agent::$plugin->agentService);

        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('agent', AgentVariable::class);
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
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

    // Protected Methods
    // =========================================================================

}
