/**
  * Agent by Jens Segers wrapper for querying user agent data. 
  *
  * @author Mark Notton <mark@yello.studio>
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

(async function () {

  let element, data = [];

  if ( document.currentScript.dataset.browserName ) {
    element = document.currentScript
  } else if ( document.documentElement.dataset.browserName ) {
    element = document.documentElement
  }

  if ( element ) {
    ['browserName', 'browserVersion', 'device'].forEach(key => data[key] = element.dataset[key])
  } else {
    await fetch('/api/agent').then(response => response.json()).then(response => { 
      ['browserName', 'browserVersion', 'device'].forEach(key => data[key] = response[key])
    }).catch((err) => {
      console.log(err)
    })
  }

  if ( data.browserName && data.browserVersion ) {
    window.browser = {
      name    : document.documentElement.dataset.browserName = data.browserName,
      version : document.documentElement.dataset.browserVersion = Number(data.browserVersion)
    }
  }

  if ( data.device ) {
    window.device    = document.documentElement.dataset.device = data.device
    window.isPhone   = data.device == 'phone'
    window.isTablet  = data.device == 'tablet'
    window.isDesktop = data.device == 'desktop'
  }

})();
