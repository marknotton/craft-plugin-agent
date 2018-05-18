<?php
namespace marknotton\agent\variables;

use marknotton\agent\Agent;

use Craft;

class Variables {
  // Refer to all functions in the services class
  public function __call($method, $args) {
    return call_user_func_array( array(Agent::$plugin->services, $method), $args );
  }
}
