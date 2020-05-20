# Agent Changelog

  

All notable changes to this project will be documented in this file.

  

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

  

## 1.1.7 - 2020-05-20

### Updated

- Composer dependency versions

  

### Added

- [Issue 7](https://github.com/marknotton/craft-plugin-agent/issues/7) - You can add bespoke user agents to a configs file which will allow the "Check" method to pass. This will help in circumstances where bots using uncommon agent strings were failing for one reason or the other.

### Fixed

- [Issue 6](https://github.com/marknotton/craft-plugin-agent/issues/6) - Empty user agents now return false.

- In some cases where the user agent version was 0 but the user agent name was corrent, the "check" method would return false. Also, when the version was 0 but the user agent name was incorrect the "check" method would returns true. This mixmatch of rules is now working as expected.

  

## 1.1.6 - 2019-07-01

### Fixed

- There were circumstances where non-human traffic would throw an error because the 'HTTP_USER_AGENT' didn't exist.

  

## 1.1.5 - 2019-03-20

### Fixed

- The new version of Googlebot (smartphone) emulates Chrome 41, which shouldn't really be flagged as an unsupported browser given it's actually a bot. So there are now user agent exceptions (specifically tailored to Google for now) that will allow trusted bots to access the site regardless of version number or browser name. In a future update this will be manageable via the plugins settings.

  

## 1.1.4 - 2018-10-18

### Added

- ES5 Babelified agent.es5.js distribution file.

  
  

## 1.1.2 / 1.1.3 - 2018-10-11

### Fixed

- Redirect function breaks in cases where user agents versions can't be read. To avoid new browsers from being redirected incorrectly, we have to allow browsers coming back with versions 0 regardless of any other criteria.

  

## 1.1.1 - 2018-08-13

### Added

- Agent.js now includes a rudimentary check for touch devices.

  
  

## 1.1.0 - 2018-07-19

### Changed

- Checks function has been completely rewritten. Instead of checking if criteria is not met, it now does the opposite. This is far more legible and should have been this way from the offset. It's also much quicker, and deals with far less faff.

  

## 1.0.8 - 2018-06-19

### Fixed

- Fixed a schema-versioning bug that's preventing the plugin from being able to install via the Plugin Store.

  

## Unchanged

### Fixed

- Fix a bug that's preventing the plugin from being able to install via the Plugin Store.

  

## 1.0.7 - 2018-06-08

### Added

- A 'redirect' function that automatically redirects to a template if browser check conditions are not met.

  

## 1.0.6 - 2018-06-07

### Fixed

- Resolved errors on older browsers where data-attributes didn't exsit.

  

## 1.0.5 - 2018-05-18

### Added

- agent.js is an optional script that opens up a list of features to query data attributes and device types.

  

### Updated

- Read Me includes documentation for agent.js

  

## 1.0.4 - 2018-05-09

### Added

- A repo icon

  

### Changed

- The browser name to "Internet Explorer" if "IE" is found.

- The way data is returned. No longer echos, but instead uses the appropriate template helper.

  

## 1.0.0 - 2018-03-09

### Added

- Initial release