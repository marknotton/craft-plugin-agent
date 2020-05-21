////////////////////////////////////////////////////////////////////////////////
// Agent
////////////////////////////////////////////////////////////////////////////////

/**
  * Query User Agent data that has been rendered directly from your HTML. 
  * This is an Immediately-invoked Function Expression
  *
  * @author Mark Notton <mark@marknotton.uk>
  *
  * @link https://github.com/marknotton/craft-plugin-agent
  *
  * @license Copyright 2020 Mark Notton
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
  
  var element = document.querySelector('[data-browser]')  ||
                document.querySelector('[data-platform]') ||
                document.querySelector('[data-device]') 

  if (element) {

    // Extrapolate browser details from the element tag into an object
    window.browser = function () {
      if (element.attributes['data-browser']) {
        var values = element.attributes.getNamedItem('data-browser').value.split(' ');
        return {
          name: values[0] || null,
          version: values[1] || null
        };
      }
    }(); 
    
    // Extrapolate platform details from the element tag into a usble value
    window.platform = function () {
      if (element.attributes['data-platform']) {
        var value = element.attributes.getNamedItem('data-platform').value;
        return value || 'unknown';
      }
    }(); 
    
    // Extrapolate device type from the element tag into a usble value
    window.mobile = function () {
      if (element.attributes['data-device']) {
        var values = element.attributes.getNamedItem('data-device').value;
        return values == 'mobile';
      }
    }(); 
    
    // Extrapolate device type from the element tag into a usble value
    window.tablet = function () {
      if (element.attributes['data-device']) {
        var values = element.attributes.getNamedItem('data-device').value;
        return values == 'tablet';
      }
    }(); 
    
    // Extrapolate device type from the element tag into a usble value
    window.desktop = function () {
      if (element.attributes['data-device']) {
        var values = element.attributes.getNamedItem('data-device').value;
        return values == 'desktop';
      }
    }();
  }
})();