
![Agent: An extension to Jens Segers Agent tool for querying user agent data.](https://i.imgur.com/uK2FnnU.jpg)

## Installation

```
composer require marknotton/agent
```

## Official Documentation

This really is just an extension to Jens Segers Agent utility. Refer to their [documentation](https://github.com/jenssegers/agent) for all available methods.

## Methods

Whilst I have tried to keep this plugin as lean as possible, I have extended Jens Segers Agent utility by including some of my own methods. 

### Check

Perform a checks to determine the users browser type is a match.  

##### Example 1:
Returns true if current browser is either Edge **or** Firefox'
```twig
{{ craft.agent.check('edge', 'firefox') }}
```

##### Example 2:
Returns true if current browser Chrome and it's version is greater than 100

```twig
{{ craft.agent.check('chrome > 100') }}
```
##### Example 3:
Returns true if current browser is Chrome and it's version is greater or equal than 100 

```twig
{{ craft.agent.check('chrome => 100') }}
```

##### Example 4:  

Returns true if current browser is either, IE version 9, Chrome version 50 or above, or any version of Firefox  

```twig
{{ craft.agent.check('ie 9', 'chrome > 49', 'firefox') }}
```  

### User agent whitelist and fallback:
 
If the User Agent contains any of the following whitelist exceptions, even partially, then [Jenssegers "setUserAgent"](https://github.com/jenssegers/agent#basic-usage) method is used to edit the User Agent string to a fallback. The fallback user agent is "Chrome 103 for Mac"; which can be modified in plugin config file. This will not amend your true browser User Agent, it only changes the user agent string referenced in this plugin. The intended use case for this is to prevent bots from seeing error messages when the [Check](#check) method or other queries are used. The follow bots are part of the predefined exceptions list:  

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

```php
return [
'userAgentExceptions' => ['codingBox'],
'userAgentFallback'   => "<insert agent string here>"
];
```

### Redirect

Redirect users if their current agent doesn't meet any of these conditions. Following the same syntax as the 'check' function,

this will redirect users to a specific template. You can also pass in a status code too.

```twig
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

### Data  

> Deprecation Warning: This method will be removed in a future release. 

Depending on how you're managing your template caching, this method could break as the attributes are inline. You should inject the agent.min.js file or enable the 'setAttributesToHTML' method via the `/configs/app.php` to work around caching issues. See the main readme for details.

If you really need it after this method has been removed, I suggest you define your attributes using Crafts own [attr method](https://craftcms.com/docs/4.x/dev/functions.html#attr):

```twig
<html {{ attr({ data : craft.agent.commonData() })}}>
```

Soon-to-be deprecated method :

```twig
<html {{ craft.agent.data }}>
```

##### Example Output:

```html
data-browser-name="chrome" data-browser-version="103" data-device="desktop"
```
 

## Set user agent data attributes in `<html>` tag 

> This feature only works on Craft 3. Support for Craft 4 is in the works.

To have the user agents device name, version and device type added directly to the `<html>` element after the template has rendered, you can define the setAttributesToHTML method in your `config/app.php` file like so: 

```php
'on afterRequest' => 'marknotton\agent\Agent::setAttributesToHTML'
```
This circumvents some templating caching issues and the end result will look like something like this:

![Agent HTML Tag](https://i.imgur.com/uaxvnL4.png)

This opens up some options for browser specific styling with css:

```css
	html[data-browser-name="safari"] article img { ... }
```

This will omit flashes of unstyled content because the bespoke styling isn't dependant on Javascript, it happens server side. 
However if this isn't an option; you can include the agent.js helper instead. 


## agent.js

There is a small [IIFE](https://en.wikipedia.org/wiki/Immediately_invoked_function_expression) agent.min.js (< 0.7k) file that can be injected directly into the doms `<head>`. You'll need to enable this via the plugin settings. 

![Agent CMS Toggle Option](https://i.imgur.com/z7Q9Ynl.png)

This will automatically inject a `<script>` tag into the document `<head>`. It will define global properties to the window object for the browser name, version, and different device types.

|  |  |
|--|--|
| `window.browser.name` | string |
| `window.browser.version` | int |
| `window.device` | string |
| `window.isPhone` | bool |
| `window.isTable` | bool |
| `window.isDesktop` | bool |

Alternatively you can register the the Agent.js asset manually:

Twig: 
```twig 
{{ craft.agent.registerAgentJsFile(<useCompressedFile:bool>, <position:string>) }}
```
Php:
```php
Agent::registerAgentJsFile(<useCompressedFile:bool>, <position:string>);
```

## API Endpoint

The endpoint `/api/agent` will return a json object of potentially useful data; for example:

  ```json
{
	"browserName": "chrome",
	"browserVersion": "103",
	"device": "desktop",
	"isDesktop": true,
	"isRobot": false,
	"isPhone": false,
	"isMobile": false,
	"isTablet": false
}
```

## Breaking Changes

There have been a few minor changes from the previous version of Agent 1.2.0. The following list highlights the most important of these:

1. Script file moved from `/vendor/marknotton/agent/src/assets/scripts/agent.js` to `/vendor/marknotton/agent/src/assets/agent.js`

2. Script file now has a minified file version `agent.min.js`

3. Script window object properties have changed:
- `window.mobile` is now `window.isPhone`
- `window.tablet` is now `window.isTablet`
- `window.desktop` is now `window.isDesktop`
- `window.platform` has been removed

4. Script file refers to an api endpoint (`/api/agent`) to fetch data if it's not available from the `<html>` or `<script` tags.  The [Fetch API](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch) is required. 

5. Version numbers are floated, so instead of `103.0.5060` it will just be `103`

6. Removed ```{{ craft.agent.full }} ``` method. The individual ```{{ craft.agent.browser }} ``` and ```{{ craft.agent.version }}``` methods should suffice. 

7.  ```{{ craft.agent.data }}``` no longer includes the platform and will no longer return the device 'mobile', and will instead stick to the native value of 'phone'. Please note, this method will be deprecated in the future release too. 

8. The ```{{ craft.agent.version }}``` will float and floor the version number by default. If you want to full string as it was in the previous version you'll have to define that specifically : ```{{ craft.agent.version(null, 'string') }}```

9. The config property `userAgentExceptions` is now `userAgentWhitelist`