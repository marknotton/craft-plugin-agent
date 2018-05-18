class Agent {
  constructor () {

    let $this = this;

    window.device = [];

    let autoload = true;
    let element = document.getElementsByTagName("html")[0];

    let autoloads = ['browser', 'platform', 'mobile', 'tablet', 'desktop', 'viewport']

    // List of known notched devices and their screen resolutions.
    let notchedScreens = {
      'iphoneX' : [1125, 2436],
    };

    var args = [].slice.call(arguments);

    args.forEach(function(arg) {
      switch(typeof arg) {
        case 'object':
          element = arg;
        break;
        case 'boolean':
          autoload = arg;
        break;
      }
    });

    this.element = element;
    this.attributes = element.attributes;

    if ( autoload ) {

      // Run the autoloader methods
      autoloads.forEach(function (method) {
        window[method] = $this[method];
      })

      // Add a listener for mobile and tablets to check for orientation changes. Call this function on Dom Ready too.
      if (window.mobile || window.tablet) {
        window.addEventListener('orientationchange', $this.debounce((e) => {
          this.orientation.update
        }));
        this.orientation.update
      }

      // Add device width in pixels and device height in pixels to the screen object
      if ( window.screen !== undefined ) {
        for (var attr in this.screen) {
          window.screen[attr] = this.screen[attr];
        }
      } else {
        window.screen = this.screen;
      }

      // Loop through known notched devices
      for (const key of Object.keys(notchedScreens)) {
        if (screen.pixelWidth == notchedScreens[key][0] && screen.pixelHeight == notchedScreens[key][1]
          || screen.pixelWidth == notchedScreens[key][1] && screen.pixelHeight == notchedScreens[key][0]) {
          window.device.type = key;
          window.device.notched = true
          this.notch;
        }
      }

    }

  }

  debounce (fn, time = 10)  {
    let timeout;

    return function() {
      const functionCall = () => fn.apply(this, arguments);

      clearTimeout(timeout);
      timeout = setTimeout(functionCall, time);
    }
  }

  get browser () {
    let values = this.attributes.getNamedItem('data-browser').value.split(' ');
    return {
      name: values[0] || null,
      version: values[1] || null
    }
  }

  get platform () {
    let value = this.attributes.getNamedItem('data-platform').value;
    return value || 'unknown';
  }

  get mobile () {
    let values = this.attributes.getNamedItem('data-device').value;
    return values == 'mobile';
  }

  get tablet () {
    let values = this.attributes.getNamedItem('data-device').value;
    return values == 'tablet'
  }

  get desktop () {
    let values = this.attributes.getNamedItem('data-device').value;
    return values == 'desktop'
  }

  get viewport () {
    return window.viewport = {
      width : window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth,
      height : window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight
    };
  }

  get screen () {
    var ratio = window.devicePixelRatio || 1
    return {
      pixelWidth:screen.width * ratio,
      pixelHeight:screen.height * ratio
    }
  }

  get notch () {

    if (window.device.orientation == 'landscape') {
      // If the device is rotated left 90 degrees, assume the notch exists on the left
      this.element.setAttribute("data-notch", 'left')
      window.device.notch = 'left'
    } else if (window.device.orientation == 'upside-down landscape') {
      // If the device is rotated right 90 degrees, assume the notch exists on the right
      this.element.setAttribute("data-notch", 'right')
      window.device.notch = 'right'
    } else {
      // If the device is not landscape at all, remove both classes
      // this.element.removeAttribute("data-notch")
      // window.device.notch = false;
      this.element.setAttribute("data-notch", 'top')
      window.device.notch = 'top'
    }
    return window.device.notch;
  }

  get orientation () {

    let $this = this;

    return {

      get update () {

        // Update the global device.orientation variable
        window.device.orientation = $this.orientation.check;

        // Update the data-orientation on the HTML element
        $this.element.setAttribute("data-orientation", window.device.orientation)

        // If the device is notched do a check for 'the notch' and it's position
        if (window.device.notched) {
          $this.notch
        }
      },
      get check () {
        var _o = ''

        if ('orientation' in window) {
        // Mobile
          switch (window.orientation) {
            case 0:
              _o = 'portrait'
              break
            case 90:
              _o = 'landscape'
              break
            case 180:
              _o = 'upside-down portrait'
              break
            case -90:
              _o = 'upside-down landscape'
              break
          }
        } else if ('orientation' in window.screen) {
        // Webkit
          if (screen.orientation.type === 'landscape-primary') {
            _o = 'landscape'
          } else if (screen.orientation.type === 'landscape-secondary') {
            _o = 'upside-down landscape'
          } else if (screen.orientation.type === 'portrait-primary') {
            _o = 'portrait'
          } else if (screen.orientation.type === 'portrait-secondary') {
            _o = 'upside-down portrait'
          }
        } else if ('mozOrientation' in window.screen) {
        // Firefox
          if (screen.mozOrientation === 'landscape-primary') {
            _o = 'landscape'
          } else if (screen.mozOrientation === 'landscape-secondary') {
            _o = 'upside-down landscape'
          } else if (screen.mozOrientation === 'portrait-primary') {
            _o = 'portrait'
          } else if (screen.mozOrientation === 'portrait-secondary') {
            _o = 'upside-down portrait'
          }
        } else if ('matchMedia' in window) {
          var portMediaQuery = window.matchMedia('all and (orientation:portrait)'),
            landMediaQuery = window.matchMedia('all and (orientation:landscape)')

          if (landMediaQuery.matches) {
            _o = 'landscape'
          } else if (portMediaQuery.matches) {
            _o = 'portrait'
          }
        } else {
          if (screen.width > screen.height) {
            _o = 'landscape'
          } else if (screen.width <= screen.height) {
            _o = 'portrait'
          } else {
            return false
          }
        }

        return _o
      }
    }
  }
}
