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
  // Returns array
  public function full() {
    return Agent::$plugin->agentService->full();
  }

  // Returns string
  public function data() {
    return Agent::$plugin->agentService->data();
  }


	public function session() {
    return Agent::$plugin->agentService->session();
  }

  public function is($agent = null, $version = null, $condition = null) {
    return Agent::$plugin->agentService->is($agent, $version, $condition);
  }
}
