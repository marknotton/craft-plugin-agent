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

## Dependencies

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

----
## Data

Echos out a data attribute with the browser name and version number. Ideal for querying via Javascript or CSS

#### Example:
```
{{ craft.agent.data|default }}
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

----
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
