<?php
/**
 * Agent plugin for Craft CMS 3.x
 *
 * Query the server-side information from the users agent data.
 *
 * @link      https://github.com/marknotton/craft-plugin-agent
 * @copyright Copyright (c) 2018 Mark Notton
 */

namespace marknotton\agent\variables;

use marknotton\agent\Agent;

use Craft;

/**
 * @author    Mark Notton
 * @package   Agent
 * @since     1.0.0
 */
class AgentVariable
{
  // Refer to all functions in the services class
  public function __call($method, $args) {
    return call_user_func_array( array(Agent::$plugin->agentService, $method), $args );
  }
}
