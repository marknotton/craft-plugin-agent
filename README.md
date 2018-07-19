
<img src="https://i.imgur.com/RcNoQQa.png" alt="Agent" align="left" height="60" />

# Agent plugin for Craft CMS 3

Query the server-side information from the users agent data.

## Table of Contents

- [Credit](#dependencies)
- [Installation](#installation)
- [Check](#check)
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
- [Agent.js](#agent.js)

## Credit

- [Agent by Jens Segers](https://github.com/jenssegers/agent)
- [Mobile Detect](http://mobiledetect.net/)

## Installation

Via compsoer:

```
composer require marknotton/agent
```

Or manually in your compsoer.json:

```
"require": {
  "marknotton/agent": "^1.0.9"
}
```

## Check

Perform a number of checks to determine wether the users browser type is a match. Returns ```boolean```.

#### Example 1:
Returns true if current browser is either 'IE, Edge, or Firefox'
```
{{ craft.agent.check('ie edge firefox') }}
```

#### Example 2:
Exactly the same as example one, but demonstrates you can pass in as many arguments as you like. Each argument is handled as an "or" not an "and".
```
{{ craft.agent.check('ie', 'edge', 'firefox') }}
```

#### Example 3:
Returns true if current browser is greater than IE 9
```
{{ craft.agent.check('ie 9 >') }}
```

#### Example 4:
Returns true if current browser is greater or equal to IE 9
```
{{ craft.agent.check('ie => 9') }}
```

#### Example 5:
Returns true if current browser is either, IE version 9, Chrome version 50 or above, or Firefox any version
```
{{ craft.agent.check('ie 9', 'chrome > 49', 'firefox') }}
```

## Redirect
Redirect users if your browser conditions are not met. Following the same syntax as the 'check' function,
this will redirect users to a specific template. You can also pass in a status code too.

```
{% set criteria = [
  'ie < 11',
  'chrome <= 55',
  'firefox <= 44',
  'safari <= 7',
  'edge <= 15',
  'opera <= 50'
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
data-browser="chrome 52"
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
{% set browser = craft.agent.browser() }}
{% set version = craft.agent.version($browser) }}

{% set platform = craft.agent.platform() }}
{% set version = craft.agent.version($platform) }}
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

Agent comes complete with a Javascript class to help make it easier to query some of the user agent data.

You can include the agent.js like this:

```js
{% do view.registerJsFile(
  craft.app.assetManager.getPublishedUrl('@agent/assets/scripts/agent.js', true),
  {'position' : constant('\\yii\\web\\View::POS_HEAD')}
)%}
```

You can initialise it like this:

```js
let agent = new Agent();
```

By default, we assume the Twig data function is defined in your HTML tag via Twig:

```html
<html {{ craft.agent.data|default }}>
```

If you're using it on another tag, you'll need to define the element like this:

```js
let agent = new Agent($('body')[0]);
```

Now you have access to these methods:


| Function | Return Example | Description |
| -- | -- | -- |
| agent.browser | ```{name: "chrome", version: "66"}``` | Gets the users browser name and version number |
| agent.viewport | ```{width: 1345, height: 1321}``` | Gets the users viewport width and height |
| agent.screen | ```{pixelWidth: 2560, pixelHeight: 1440}``` | Gets the users device resolution. This takes into account condensed pixels |
| agent.platform | ```osx``` | Gets the users platform type |
| agent.mobile | ```true``` | Checks if the user is on a mobile device |
| agent.tablet | ```true``` | Checks if the user is on a tablet device |
| agent.desktop | ```true``` | Checks if the user is on a desktop |
| agent.orientation | ```landscape``` | Checks the orientation of the users display/device |
| agent.notch | ```left``` | Checks if the users device has a notch, and tells you what side it's on |

Orientation and Notch data is stored in the DOM window as `device`. Refering to this will return something like this:

```[orientation: "landscape", type: "iphoneX", notched: true, notch: "left"]```

All of the above methods are auto loaded when the Agent Class is initialised. Each method stores data to the DOM window. So rather than actioning each method when you need it, you can refer to the cached data in your Window. Meaning you can simply call `browser` in your script to get the data. You can disable this by passing in `false` as argument: `new Agent(false)`.
