"use strict";

var _typeof =
  typeof Symbol === "function" && typeof Symbol.iterator === "symbol"
    ? function(obj) {
        return typeof obj;
      }
    : function(obj) {
        return obj &&
          typeof Symbol === "function" &&
          obj.constructor === Symbol &&
          obj !== Symbol.prototype
          ? "symbol"
          : typeof obj;
      };

var _createClass = (function() {
  function defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      Object.defineProperty(target, descriptor.key, descriptor);
    }
  }
  return function(Constructor, protoProps, staticProps) {
    if (protoProps) defineProperties(Constructor.prototype, protoProps);
    if (staticProps) defineProperties(Constructor, staticProps);
    return Constructor;
  };
})();

function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

var Agent = (function() {
  function Agent() {
    var _this = this;

    _classCallCheck(this, Agent);

    var $this = this;

    window.device = [];

    var autoload = true;
    var element = document.getElementsByTagName("html")[0];

    var autoloads = [
      "browser",
      "platform",
      "mobile",
      "tablet",
      "desktop",
      "viewport"
    ];

    // List of known notched devices and their screen resolutions.
    var notchedScreens = {
      iphoneX: [1125, 2436]
    };

    var args = [].slice.call(arguments);

    args.forEach(function(arg) {
      switch (typeof arg === "undefined" ? "undefined" : _typeof(arg)) {
        case "object":
          element = arg;
          break;
        case "boolean":
          autoload = arg;
          break;
      }
    });

    this.element = element;
    this.attributes = element.attributes;

    if (autoload) {
      // Run the autoloader methods
      autoloads.forEach(function(method) {
        window[method] = $this[method];
      });

      // Add a listener for mobile and tablets to check for orientation changes. Call this function on Dom Ready too.
      if (window.mobile || window.tablet) {
        window.addEventListener("orientationchange", function(event) {
          setTimeout(_this.orientation.update, 10);
        });
        this.orientation.update;
      }

      // Add device width in pixels and device height in pixels to the screen object
      if (window.screen !== undefined) {
        for (var attr in this.screen) {
          window.screen[attr] = this.screen[attr];
        }
      } else {
        window.screen = this.screen;
      }

      // Loop through known notched devices
      var _iteratorNormalCompletion = true;
      var _didIteratorError = false;
      var _iteratorError = undefined;

      try {
        for (
          var _iterator = Object.keys(notchedScreens)[Symbol.iterator](), _step;
          !(_iteratorNormalCompletion = (_step = _iterator.next()).done);
          _iteratorNormalCompletion = true
        ) {
          var key = _step.value;

          if (
            (screen.pixelWidth == notchedScreens[key][0] &&
              screen.pixelHeight == notchedScreens[key][1]) ||
            (screen.pixelWidth == notchedScreens[key][1] &&
              screen.pixelHeight == notchedScreens[key][0])
          ) {
            window.device.type = key;
            window.device.notched = true;
            this.notch;
          }
        }
      } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
      } finally {
        try {
          if (!_iteratorNormalCompletion && _iterator.return) {
            _iterator.return();
          }
        } finally {
          if (_didIteratorError) {
            throw _iteratorError;
          }
        }
      }
    }
  }

  _createClass(Agent, [
    {
      key: "browser",
      get: function get() {
        if (this.attributes["data-browser"]) {
          var values = this.attributes
            .getNamedItem("data-browser")
            .value.split(" ");
          return {
            name: values[0] || null,
            version: values[1] || null
          };
        }
      }
    },
    {
      key: "platform",
      get: function get() {
        if (this.attributes["data-platform"]) {
          var value = this.attributes.getNamedItem("data-platform").value;
          return value || "unknown";
        }
      }
    },
    {
      key: "mobile",
      get: function get() {
        if (this.attributes["data-device"]) {
          var values = this.attributes.getNamedItem("data-device").value;
          return values == "mobile";
        }
      }
    },
    {
      key: "tablet",
      get: function get() {
        if (this.attributes["data-device"]) {
          var values = this.attributes.getNamedItem("data-device").value;
          return values == "tablet";
        }
      }
    },
    {
      key: "desktop",
      get: function get() {
        if (this.attributes["data-device"]) {
          var values = this.attributes.getNamedItem("data-device").value;
          return values == "desktop";
        }
      }
    },
    {
      key: "viewport",
      get: function get() {
        return (window.viewport = {
          width:
            window.innerWidth ||
            document.documentElement.clientWidth ||
            document.body.clientWidth,
          height:
            window.innerHeight ||
            document.documentElement.clientHeight ||
            document.body.clientHeight
        });
      }
    },
    {
      key: "screen",
      get: function get() {
        var ratio = window.devicePixelRatio || 1;
        return {
          pixelWidth: screen.width * ratio,
          pixelHeight: screen.height * ratio
        };
      }
    },
    {
      key: "notch",
      get: function get() {
        if (window.device.orientation == "landscape") {
          // If the device is rotated left 90 degrees, assume the notch exists on the left
          this.element.setAttribute("data-notch", "left");
          window.device.notch = "left";
        } else if (window.device.orientation == "upside-down landscape") {
          // If the device is rotated right 90 degrees, assume the notch exists on the right
          this.element.setAttribute("data-notch", "right");
          window.device.notch = "right";
        } else {
          // If the device is not landscape at all, remove both classes
          // this.element.removeAttribute("data-notch")
          // window.device.notch = false;
          this.element.setAttribute("data-notch", "top");
          window.device.notch = "top";
        }
        return window.device.notch;
      }
    },
    {
      key: "orientation",
      get: function get() {
        var $this = this;

        return {
          get update() {
            // Update the global device.orientation variable
            window.device.orientation = $this.orientation.check;

            // Update the data-orientation on the HTML element
            $this.element.setAttribute(
              "data-orientation",
              window.device.orientation
            );

            // If the device is notched do a check for 'the notch' and it's position
            if (window.device.notched) {
              $this.notch;
            }
          },
          get check() {
            var _o = "";

            if ("orientation" in window) {
              // Mobile
              switch (window.orientation) {
                case 0:
                  _o = "portrait";
                  break;
                case 90:
                  _o = "landscape";
                  break;
                case 180:
                  _o = "upside-down portrait";
                  break;
                case -90:
                  _o = "upside-down landscape";
                  break;
              }
            } else if ("orientation" in window.screen) {
              // Webkit
              if (screen.orientation.type === "landscape-primary") {
                _o = "landscape";
              } else if (screen.orientation.type === "landscape-secondary") {
                _o = "upside-down landscape";
              } else if (screen.orientation.type === "portrait-primary") {
                _o = "portrait";
              } else if (screen.orientation.type === "portrait-secondary") {
                _o = "upside-down portrait";
              }
            } else if ("mozOrientation" in window.screen) {
              // Firefox
              if (screen.mozOrientation === "landscape-primary") {
                _o = "landscape";
              } else if (screen.mozOrientation === "landscape-secondary") {
                _o = "upside-down landscape";
              } else if (screen.mozOrientation === "portrait-primary") {
                _o = "portrait";
              } else if (screen.mozOrientation === "portrait-secondary") {
                _o = "upside-down portrait";
              }
            } else if ("matchMedia" in window) {
              var portMediaQuery = window.matchMedia(
                  "all and (orientation:portrait)"
                ),
                landMediaQuery = window.matchMedia(
                  "all and (orientation:landscape)"
                );

              if (landMediaQuery.matches) {
                _o = "landscape";
              } else if (portMediaQuery.matches) {
                _o = "portrait";
              }
            } else {
              if (screen.width > screen.height) {
                _o = "landscape";
              } else if (screen.width <= screen.height) {
                _o = "portrait";
              } else {
                return false;
              }
            }

            return _o;
          }
        };
      }
    }
  ]);

  return Agent;
})();
