
![Agent: An extension to Jens Segers Agent tool for querying user agent data.](https://i.imgur.com/uK2FnnU.jpg)

## Installation

```
composer require marknotton/agent
```

## Official Documentation

This really is just an extension to Jens Segers Agent utility. Refer to their [documentation](https://github.com/jenssegers/agent) for all available methods.

Whilst I have tried to keep this plugin as lean as possible, I have extended Jens Segers Agent utility by including a couple of my own methods...

### Check

Check to determine the users browser and version type is a match.  

##### Example 1:
`true` if the users current browser matches either browser name
```twig
{{ craft.agent.check('edge', 'firefox') }}
```

##### Example 2:
You can use most [comparison operators](https://www.php.net/manual/en/language.operators.comparison.php) to match against the browsers version. 

`true` if browser version is equal to 100:
```twig
{{ craft.agent.check('chrome == 100'}}
```

`true` if browser version is not equal to 100:
```twig
{{ craft.agent.check('chrome != 100'}}
```

`true`  if browser version is less than 100:
```twig
{{ craft.agent.check('chrome < 100'}}
```

`true` if browser version is greater than 100:
```twig
{{ craft.agent.check('chrome > 100'}}
```

`true` if browser version is less than or equal to 100:
```twig
{{ craft.agent.check('chrome <= 100'}}
```

`true` if browser version is greater than or equal to 100:
```twig
{{ craft.agent.check('chrome >= 100'}}
```

##### Example 3:  

You can add multiple criteria for your check. `true` if any criteria is a match:

```twig
{{ craft.agent.check('ie 9', 'chrome > 49', 'firefox') }}
```  

##### Example 5:  

You may also negate a check by prefixing a `not ` string. `true` if the users current browser is not IE version 9 or above.

```twig
{{ craft.agent.check('not ie => 9') }}
```  

### User agent whitelist:
 
If the User Agent contains any whitelist exceptions, even with partial matches, then the Check method will always return `true`.  This can be useful for allowing certain bots to pass the Check method. 

You can mange the whitelist by creating an `agent.php` config file in your projects `configs` directory:
```php
return [
'userAgentWhitelist' => [
	'APIs-Google',
	'Mediapartners-Google',
	'AdsBot-Google',
	'Googlebot-Image',
	'Googlebot',
	'FeedFetcher-Google'
	]
];
```
or via the CMS plugin settings:

![User Agent Whitelist](https://i.imgur.com/Suotfhv.png)


### Version

Jens Segers original **version** method required a property name (browser, platform, os, etc...); and the return value would resolve to a full schema version: 

```twig 
{{ craft.agent.version(craft.agent.browser) }} // 104.3.0.1 
```

I have found in most cases getting the major browser version would suffice. So instead of the previous example you can return a 'floored' version number where the browser is the assumed default argument.

```twig 
{{ craft.agent.version() }} // 104
```

You can still get full version or a floated version number like so:

```twig 
{{ craft.agent.version('text') }} // 104.3.0.1 
{{ craft.agent.version('float') }} // 104.3
```

### Redirect

Redirect users to a new template/url if the user agent doesn't match any of the check method criteria:

```twig
{% set criteria = [
  'chrome > 55',
  'firefox > 44',
  'safari >= 7',
  'edge >= 15',
  'opera > 50'
] %}

{{ craft.agent.redirect(criteria, 'no-support.twig', 302) }}
```

## Set user agent data attributes in `<html>` tag 

> UPDATE - August 2022: The **setAttributesToHTML** method only works on Craft 3. Support for Craft 4 is under review.

To set the user agents device name, version and device type directly to the `<html>` element after the template has rendered, you can define the **setAttributesToHTML** method in your `config/app.php` file.

```php
'on afterRequest' => 'marknotton\agent\Agent::setAttributesToHTML'
```
The end result will look like something like this:

```html
<html data-browser-name="chrome" data-browser-version="103" data-device="desktop">
```
*But why would you want this?* This opens up some options for browser specific styling within your CSS; and this server side approach will omit flashes of unstyled content (FOUC) or layout shifts because styling rules aren't dependant on Javascript during page load. This means you can confidently use something like this in your CSS:

```css
html[data-browser-name="safari"] article img { ... }
```

### Data

> Deprecation Warning: This method will be removed in a future release. 

This agent property returns the same ***setAttributesToHTML*** data attributes as a `string`

```twig
<html {{ craft.agent.data }}>
```
Assigning a string of attributes isn't ideal due to template caching patterns which could mean cached data attributes. I suggest using Crafts own [attr method](https://craftcms.com/docs/4.x/dev/functions.html#attr)  if you're not caching the `<html>` tag:

```twig
<html {{ attr({ data : craft.agent.commonData() })}}>
```

## agent.js

There is a small [IIFE](https://en.wikipedia.org/wiki/Immediately_invoked_function_expression) agent.min.js (< 0.7k) file that can be injected directly into the  `<head>`. You'll need to enable this via the plugin settings. 

![Agent CMS Toggle Option](https://i.imgur.com/z7Q9Ynl.png)

This will define global properties to the window object for the browser name, version, and different device types.

|  |  |
|--|--|
| `window.browser.name` | string |
| `window.browser.version` | int |
| `window.device` | string |
| `window.isPhone` | bool |
| `window.isTable` | bool |
| `window.isDesktop` | bool |

Alternatively you can register the the agent.min.js asset manually:

Twig: 
```twig 
{{ craft.agent.registerAgentJsFile(<useCompressedFile:bool>, <position:string>) }}
```
Php:
```php
Agent::registerAgentJsFile(<useCompressedFile:bool>, <position:string>);
```

## Change Log & Breaking Changes

There have been many changes since the previous version of Agent 1.2.0. Some for performance, some for sanity. Arguably some practices used in the previous version were over engineered for no obvious gains. These changes could be breaking, that require small syntax tweaks to resolve on older projects. [Please review the "4.0.0 - 2022-09-11" change log for suggestions and fixes.](https://github.com/marknotton/craft-plugin-agent/blob/master/CHANGELOG.md#400---2022-09-11)  