/**
  * Agent IIFE for querying the users device browser name, version and device type.
  *
  * @author Mark Notton <mark@marknotton.uk>
  *
  * @link https://bitbucket.org/yellostudio/agent/src/master/
  * @link https://github.com/marknotton/craft-plugin-agent
  * 
  * @license Copyright 2022 Mark Notton
  *
  * Licensed under the Apache License, Version 2.0 (the "License");
  * you may not use this file except in compliance with the License.
  * You may obtain a copy of the License at http://www.apache.org/licenses/LICENSE-2.0
  *
  * Unless required by applicable law or agreed to in writing, software
  * distributed under the License is distributed on an "AS IS" BASIS,
  * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  * See the License for the specific language governing permissions and
  * limitations under the License.
  */

(function () {

  let element, browserName, browserVersion, device

  if ( document.currentScript.dataset.browserName ) {
    element = document.currentScript
  } else if ( document.documentElement.dataset.browserName ) {
    element = document.documentElement
  }

  if ( element ) {

    browserName    = element.dataset.browserName
    browserVersion = element.dataset.browserVersion
    device         = element.dataset.device

    if ( browserName && browserVersion ) {
      window.browser = {
        name    : document.documentElement.dataset.browserName    = browserName,
        version : document.documentElement.dataset.browserVersion = parseInt(browserVersion)
      }
    }

    if ( device ) {
      window.device    = document.documentElement.dataset.device = device
      window.isPhone   = device == 'phone'
      window.isTablet  = device == 'tablet'
      window.isDesktop = device == 'desktop'
    }

  }

})();
