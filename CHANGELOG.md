  

# Agent Changelog

## 4.0.0 - 2022-09-11

### Changed

* Script file has moved from `/vendor/marknotton/agent/src/assets/scripts/agent.js` to `/vendor/marknotton/agent/src/assets/agent.js`
* Script window object properties have changed:
	* `window.mobile` is now `window.isPhone`
	* `window.tablet` is now `window.isTablet`
	* `window.desktop` is now `window.isDesktop`
	* `window.platform` has been removed
* Version numbers are now floored by default, so instead of `103.0.5060` it will just be `103`. You can still return whole version string like this: ```{{ craft.agent.version(null, 'text') }}```
* ```{{ craft.agent.data }}``` no longer includes the platform and will no longer return the device 'mobile', and will instead stick to the native value of 'phone'. Please note, this method will be deprecated in the future release.
* The config property `userAgentExceptions` is now `userAgentWhitelist` and will no longer attempt to amend the user agent to a fallback. Instead any matches will be used within the Check method.
* Previously the `userAgentWhitelist` would refer to a set of default exceptions: 'APIs-Google', 'Mediapartners-Google', 'AdsBot-Google', 'Googlebot-Image', 'Googlebot', 'FeedFetcher-Google'. These were hardcoded and couldn't be removed. This was far too imposing and have been removed. If you still need them, you'll have to apply them manually in the CMS plugin settings or your config/agent.php file.
* Previously the Agent service class was exposed via a Twig Extension allowing you to omit the `{{ craft.` from any method call. This was overkill and unnecessary. ```{{ agent.version }}``` will no longer work. It must be ```{{ craft.agent.version() }}```
* Plugin version number has gone from 1.20.0 to 4.0.0. Matching the version schema to latest major supported version of Craft helps me troubleshoot support requests more easily.

### Added
* Minified copy of the `agent.min.js` file
* Whitelist management via the CMS plugin settings
* [Issue 10](https://github.com/marknotton/craft-plugin-agent/issues/10) Added check method criteria negation using the 'not' prefix 
 
###  Removed
* `/api/agent` endpoint.
* ```{{ craft.agent.full }} ``` method. The individual ```{{ craft.agent.browser() }} ``` and ```{{ craft.agent.version() }}``` methods should suffice.
* The magic method that managed the use of ```{{ craft.agent }}``` as a string. If you need the user agent, use ```{{ craft.agent.userAgent }}``` instead.
* The plugin schemaVersion property, this should never have been added or updated in the way I was using it. This may flag up in your YAML changes and throw an error on existing projects. To fix this, [update the version from 2.0.3 to 1.0.0 in your `config/project/project.yaml` file:
![fix yaml schemeVersion](https://i.imgur.com/xDGcsTN.png)

## 1.2.0 - 2021-06-28  

### Added
* A '==' condition can now be used with comparing browser versions
* An extension to JenssegersAgent `version` function that now accepts the "simplify" param which will floor the unit to 0 decimal places. So browser versions like '91.0.4472.114' will be returned as '91'. This is disabled by default.  

### Changed
* The Agent service now extrends JenssegersAgent utility correctly. Previously all methods and properties were being forwarded via a magic method which is completely unecessary. This should help significantly when trying to trace errors. 

### Fixed
* [Issue 11](https://github.com/marknotton/craft-plugin-agent/issues/11) Fixed an error being thrown relating to how version units are handled when passed as a string.

### Updated
* The data() method was a clanky string concatenation that is now be handled more elegantly with Crafts own 'renderTagAttributes' HTML Helper.
* All instances where version numbers are compared, the native `version_compare` PHP method is used.  

### Removed
* Variables class. It wasn't needed and is managed correctly via the base init method. All variables are still accessible. 

## 1.1.8 - 2020-05-21  

### Changed
* User agent exceptions are now handled earlier on in the Services init method, rather than each time the 'Check' method is called.
* User agent matches will no longer return `true` during the 'Check' method, but instead uses [Jenssegers "setUserAgent"](https://github.com/jenssegers/agent#basic-usage) method to edit the user agent string to a fallback. The fallback defaults to a "Chrome 81 on Windows" string and be can modified in the agents config file. This will not amend your true browser User Agent, it only changes the user agent string referenced in this plugin

### Breaking
* Adding custom user agent exceptions in the config file meant defining a 'checkExceptions' value. This is now renamed to 'userAgentExceptions'. 

### Updated
* The Javascript class is now formatted to ES5 instead of ES6. So the ES5 version has been removed.
* The Javascript classes viewport related methods are removed as they were not-inkeeping with the nature of this plugin. Only data-attribute querying methods remain.
* The Javascript class is now IIFE instead of a Class.  

## 1.1.7 - 2020-05-20 

### Updated
* Composer dependency versions 

### Added

* [Issue 7](https://github.com/marknotton/craft-plugin-agent/issues/7) 
* You can add bespoke user agents to a configs file which will allow the "Check" method to pass. This will help in circumstances where bots using uncommon agent strings were failing for one reason or the other.

### Fixed
* [Issue 6](https://github.com/marknotton/craft-plugin-agent/issues/6) 
* Empty user agents now return false.  
* In some cases where the user agent version was 0 but the user agent name was current, the "check" method would return false. Also, when the version was 0 but the user agent name was incorrect the "check" method would returns true. This mixmatch of rules is now working as expected.  

## 1.1.6 - 2019-07-01  

### Fixed
* There were circumstances where non-human traffic would throw an error because the 'HTTP_USER_AGENT' didn't exist.

## 1.1.5 - 2019-03-20

### Fixed
* The new version of Googlebot (smartphone) emulates Chrome 41, which shouldn't really be flagged as an unsupported browser given it's actually a bot. So there are now user agent exceptions (specifically tailored to Google for now) that will allow trusted bots to access the site regardless of version number or browser name. In a future update this will be manageable via the plugins settings.

## 1.1.4 - 2018-10-18  

### Added
* ES5 Babelified agent.es5.js distribution file.  

## 1.1.2 / 1.1.3 - 2018-10-11  

### Fixed
* Redirect function breaks in cases where user agents versions can't be read. To avoid new browsers from being redirected incorrectly, we have to allow browsers coming back with versions 0 regardless of any other criteria  

## 1.1.1 - 2018-08-13

### Added 
* Agent.js now includes a rudimentary check for touch devices.

## 1.1.0 - 2018-07-19  

### Changed
* Checks function has been completely rewritten. Instead of checking if criteria is not met, it now does the opposite. This is far more legible and should have been this way from the offset. It's also much quicker, and deals with far less faff.  

## 1.0.8 - 2018-06-19  

### Fixed
* Fixed a schema-versioning bug that's preventing the plugin from being able to install via the Plugin Store.  

## Unchanged  

### Fixed
* Fix a bug that's preventing the plugin from being able to install via the Plugin Store.  

## 1.0.7 - 2018-06-08

### Added
* A 'redirect' function that automatically redirects to a template if browser check conditions are not met.  

## 1.0.6 - 2018-06-07  

### Fixed
* Resolved errors on older browsers where data-attributes didn't exist.  

## 1.0.5 - 2018-05-18 

### Added
* agent.js is an optional script that opens up a list of features to query data attributes and device types. 

### Updated
* Read Me includes documentation for agent.js

## 1.0.4 - 2018-05-09

### Added
* A repo icon

### Changed
* The browser name to "Internet Explorer" if "IE" is found.
* The way data is returned. No longer echos, but instead uses the appropriate template helper.

## 1.0.0 - 2018-03-09

### Added
* Initial release
