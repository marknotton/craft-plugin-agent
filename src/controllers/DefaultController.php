<?php

namespace marknotton\agent\controllers;

use marknotton\agent\Agent;
use craft\web\Controller;

class DefaultController extends Controller {

  public function actionIndex() {

    $data = array_map('craft\helpers\StringHelper::toKebabCase', [
      'browserName'    => Agent::$agent->browser(),
      'browserVersion' => Agent::$agent->version(),
      'device'         => Agent::$agent->deviceType(),
    ]);

    $data['isDesktop'] = Agent::$agent->isDesktop();
    $data['isRobot']   = Agent::$agent->isRobot();
    $data['isPhone']   = Agent::$agent->isPhone();
    $data['isMobile']  = Agent::$agent->isMobile();
    $data['isTablet']  = Agent::$agent->isTablet();

    return $this->asJson($data);
  }

}
