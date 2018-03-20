# Agent plugin for Craft CMS 3.x

Query the server-side information from the users agent data.

<img src="http://i.imgur.com/klRglRT.png" alt="Agent" align="left" height="60" />

## Table of Contents

- [Dependencies](#dependencies)
- [Agent](#agent)
- [Is](#is)
- [Data](#data)
- [Full](#full)
- [Local](#local)
- [Session](#session)

## Credit

- [Agent by Jens Segers](https://github.com/jenssegers/agent)
- [Mobile Detect](http://mobiledetect.net/)

## Agent

If you want to use some of the native functionality from [Agent](https://github.com/jenssegers/agent) you can simply query the global agent instance:

```
{{ craft.agent.agent.isPhone() }}
```

## Is

Perform a number of checks to determine wether the users browser type is a match. Returns ```boolean```.

#### Example 1:
Returns true if current browser is either 'IE, Edge, or Firefox'
```
{{ craft.agent.is('ie edge firefox') }}
```

#### Example 2:
Exactly the same as example one, but demonstrates you can pass in as many arguments as you like. Each argument is handled as an "or" not an "and".
```
{{ craft.agent.is('ie', 'edge', 'firefox') }}
```

#### Example 3:
Returns true if current browser is greater than IE 9
```
{{ craft.agent.is('ie 9 >') }}
```

#### Example 4:
Returns true if current browser is greater or equal to IE 9
```
{{ craft.agent.is('ie => 9') }}
```

#### Example 5:
Returns true if current browser is either, IE version 9 or 10, Chrome version 50 or above, or Firefox any version
```
{{ craft.agent.is('ie 9 10', 'chrome > 49', 'firefox') }}
```

## Data

Returns a string in the format of data attributes containing the browser name and version number, platform and Operating system. Ideal for querying via Javascript or CSS

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

### Magic is-method

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

Additional Functionality
------------------------

## Accept languages

Get the browser's accept languages. Example:

```
{% set languages = craft.agent.languages() %}
// ['nl-nl', 'nl', 'en-us', 'en']
```

## Device name

Get the device name, if mobile. (iPhone, Nexus, AsusTablet, ...)

```
{{ craft.agent->device() }}
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

### Phone detection

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

## Browser/platform version

MobileDetect recently added a `version` method that can get the version number for components. To get the browser or platform version you can use:

```
{% set browser = craft.agent.browser() }}
{% set version = craft.agent.version($browser) }}

{% set platform = craft.agent.platform() }}
{% set version = craft.agent.version($platform) }}
```

*Note, the version method is still in beta, so it might not return the correct result.*

## Extra

All Agent service methods are accessible without the need to define 'craft.'. So all of the functions above can be called something like this instead:

```
{{ agent.browser() }}
```
