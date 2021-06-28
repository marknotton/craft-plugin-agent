<img  src="https://i.imgur.com/RcNoQQa.png"  alt="Agent"  align="left"  height="60" />

# Agent plugin for Craft CMS 3

Query the server-side information from the users agent data.

## Table of Contents

- [Credit](#dependencies)
- [Installation](#installation)
- [Check](#check)
- [User Agent Exceptions and Fallback](#user-agent-exceptions-and-fallback)
- [Redirect](#redirect)
- [Data](#data)
- [Full](#full)
- [Browser/platform version](#browserplatform-version)
- [Magic is-method](#magic-is-method)
- [Mobile Detection](#mobile-detection)
- [Match User Agent](#match-user-agent)
- [Accept Languages](#accept-languages)
- [Device Name](#device-name)
- [Desktop Detection](#desktop-detection)
- [Phone Detection](#phone-detection)
- [Robot Detection](#robot-detection)
- [Robot Name](#robot-name)
- [Extra](#extra)
- [Agent.js](#agentjs)

## Credit

Agent is pretty much just a wrapper for the following classes. 
Most of how this plugin works goes through these two tools.

- [Agent by Jens Segers](https://github.com/jenssegers/agent)
- [Mobile Detect](http://mobiledetect.net/)

 ## Installation

Compsoer:
```
composer require marknotton/agent
```

## Check

Perform a number of checks to determine wether the users browser type is a match. Returns ```boolean```.

#### Example 1:

Returns true if current browser is either 'IE, Edge, or Firefox'

```
{{ craft.agent.check('ie', 'edge', 'firefox') }}
```

#### Example 2:

Returns true if current browser is greater than IE 9

```
{{ craft.agent.check('ie > 9') }}
```

#### Example 3:

Returns true if current browser is greater or equal to IE 9

```
{{ craft.agent.check('ie => 9') }}
```  

#### Example 4:

Returns true if current browser is either, IE version 9, Chrome version 50 or above, or any version of Firefox

```
{{ craft.agent.check('ie 9', 'chrome > 49', 'firefox') }}
```

## User Agent Exceptions and Fallback:

If the User Agent contains any of the following exceptions, even partially, then [Jenssegers "setUserAgent"](https://github.com/jenssegers/agent#basic-usage) method is used to edit the User Agent string to a fallback. The default fallbacks to a "Chrome 81 Mac" string which can be modified in the Agents config file. This will not amend your true browser User Agent, it only changes the user agent string referenced in this plugin. The intended use case for this is to prevent bots from seeing error messages when the [Check](#check) method or other queries are used. The follow bots are part of the predefined exceptions list:

- APIs-Google
- Mediapartners-Google
- AdsBot-Google-Mobile
- AdsBot-Google-Mobile
- AdsBot-Google
- Googlebot-Image
- Googlebot
- Googlebot-News
- Googlebot
- Googlebot-Video
- Mediapartners-Google
- AdsBot-Google-Mobile-Apps
- FeedFetcher-Google  

You can add your own by creating an `agent.php` config file in your projects `configs` directory. 

```
<?php
return [
'userAgentExceptions' => ['codingBox'],
'userAgentFallback' => "<insert agent string here>"
]; 
```

## Redirect
 
Redirect users if their current agent doesn't meet any of these conditions. Following the same syntax as the 'check' function,
this will redirect users to a specific template. You can also pass in a status code too.  

```
{% set criteria = [
 'ie 11',
 'chrome > 55',
 'firefox > 44',
 'safari >= 7',
 'edge >= 15',
 'opera > 50'
] %}

{{ craft.agent.redirect(criteria, 'no-support.twig', 302) }}
```

## Data

Returns a string in the format of data attributes containing the browser name and version number, platform and device type. Ideal for querying via Javascript or CSS. See the included agent.js file for more information.

#### Example:

```
{{ craft.agent.data }}
```

#### Example Output:

```html
data-browser="chrome 81" data-platform="osx" data-device="desktop"
```

#### Example Output: jQuery Usage

```js
if ( $('html[data-browser^=chrome]').length ) {...}
```  

#### Example Output: CSS Usage

```css
html[data-browser^=chrome] {...}
```

## Full
Simply returns the name and version of the users browser.
Returns browser name and version number in an array

```
{{ craft.agent.full }}
```

Returns browser name

```
{{ craft.agent.full.name }}
```

Returns version number

```
{{ craft.agent.full.version }}
```

## Browser/platform version

MobileDetect recently added a `version` method that can get the version number for components. To get the browser or platform version you can use:

```
{% set browser  = craft.agent.browser() %}
{% set version  = craft.agent.version(browser) %} - 91.0.4472.114
{% set version  = craft.agent.version(null, true) %} - 91
{% set platform = craft.agent.platform() %}
{% set version  = craft.agent.version(platform) %}
```

*Note, the version method is still in beta, so it might not return the correct result.*

## Magic is-method

Magic method that does the same as the previous `is()` method:

```
{{ craft.agent.isAndroidOS() }}
{{ craft.agent.isNexus() }}
{{ craft.agent.isSafari() }}
```

## Mobile detection

Check for mobile device:

```
{{ craft.agent.isMobile() }}
{{ craft.agent.isTablet() }}
```

## Match user agent

Search the user agent with a regular expression:

```
{{ craft.agent.match('regexp') }}
```

## Accept languages

Get the browser's accept languages. Example:

```
{% set languages = craft.agent.languages() %}
// ['nl-nl', 'nl', 'en-us', 'en']
```

## Device name

Get the device name, if mobile. (iPhone, Nexus, AsusTablet, ...)

```
{{ craft.agent.device() }}
```

## Operating system name

Get the operating system. (Ubuntu, Windows, OS X, ...)

```
{{ craft.agent.platform() }}
```

## Browser name

Get the browser name. (Chrome, IE, Safari, Firefox, ...)

```
{{ craft.agent.browser() }}
```

## Desktop detection

Check if the user is using a desktop device.

```
{{ craft.agent.isDesktop() }}
```

*This checks if a user is not a mobile device, tablet or robot.*

## Phone detection

Check if the user is using a phone device.

```
{{ craft.agent.isPhone() }}
```

## Robot detection

Check if the user is a robot. This uses [jaybizzle/crawler-detect](https://github.com/JayBizzle/Crawler-Detect) to do the actual robot detection.

```
{{ craft.agent.isRobot() }}
```

## Robot name

Get the robot name.

```
{{ craft.agent.robot() }}
```

## Extra

All Agent service methods are accessible without the need to define 'craft.'. So all of the functions above can be called like this too:

```
{{ agent.browser() }}
```

## Agent.js

Agent comes with a small IIFE Javascript file to help make it easier to query some of the user agent data.

It will bind a number of getters to the window DOM element.

You can include the agent.js like this:

```js 
  {%  do  view.registerJsFile(
    craft.app.assetManager.getPublishedUrl('@agent/assets/scripts/agent.js', true),
    {'position' : constant('\\yii\\web\\View::POS_HEAD')}
  )%}
```

Adding the data method to a dom element will available to the methods registered from the Agent.js file

```html
<html {{ craft.agent.data|default  }}> 
```

Now you have access to these methods:
  
| Function | Return Example | Description | 
| -- | -- | -- |
| browser | ```{name: "chrome", version: "66"}``` | Gets the users browser name and version number |
| platform | ```osx``` | Gets the users platform type |
| mobile | ```true``` | Checks if the user is on a mobile device |
| tablet | ```true``` | Checks if the user is on a tablet device |
| desktop | ```true``` | Checks if the user is on a desktop |
  