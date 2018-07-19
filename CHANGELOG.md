# Agent Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 1.1.0 - 2018-07-49
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
-  A 'redirect' function that automatically redirects to a template if browser check conditions are not met.

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
