"use strict";

require("./bootstrap");

var _alpinejs = _interopRequireDefault(require("alpinejs"));

var _persist = _interopRequireDefault(require("@alpinejs/persist"));

var _collapse = _interopRequireDefault(require("@alpinejs/collapse"));

var _http = require("./http");

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

_alpinejs["default"].plugin(_persist["default"]);

_alpinejs["default"].plugin(_collapse["default"]);

window.Alpine = _alpinejs["default"];
document.addEventListener("alpine:init", function () {
  _alpinejs["default"].data("toast", function () {
    return {
      visible: false,
      delay: 5000,
      percent: 0,
      interval: null,
      timeout: null,
      message: null,
      type: null,
      close: function close() {
        this.visible = false;
        clearInterval(this.interval);
      },
      show: function show(message, type) {
        var _this = this;

        this.visible = true;
        this.message = message;
        this.type = type;

        if (this.interval) {
          clearInterval(this.interval);
          this.interval = null;
        }

        if (this.timeout) {
          clearTimeout(this.timeout);
          this.timeout = null;
        }

        this.timeout = setTimeout(function () {
          _this.visible = false;
          _this.timeout = null;
        }, this.delay);
        var startDate = Date.now();
        var futureDate = Date.now() + this.delay;
        this.interval = setInterval(function () {
          var date = Date.now();
          _this.percent = (date - startDate) * 100 / (futureDate - startDate);

          if (_this.percent >= 100) {
            clearInterval(_this.interval);
            _this.interval = null;
          }
        }, 30);
      }
    };
  });

  _alpinejs["default"].data("productItem", function (product) {
    return {
      slug: product.slug,
      product: product,
      addToCart: function addToCart() {
        var _this2 = this;

        var quantity = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 1;
        (0, _http.post)(this.product.addToCartUrl, {
          quantity: quantity
        }).then(function (result) {
          _this2.$dispatch('cart-change', {
            count: result.count
          });

          _this2.$dispatch('notify', {
            message: 'The item was successfully added to cart'
          });
        })["catch"](function (response) {
          _this2.$dispatch('notify', {
            message: response.message || 'Server Error. Please try again',
            type: 'error'
          });
        });
      },
      removeItemFromCart: function removeItemFromCart() {
        var _this3 = this;

        (0, _http.post)(this.product.removeUrl).then(function (result) {
          _this3.$dispatch('notify', {
            message: 'The item was successfully removed from cart'
          });

          _this3.$dispatch('cart-change', {
            count: result.count
          });

          _this3.cartItems = _this3.cartItems.filter(function (p) {
            return p.id !== product.id;
          });
        });
      },
      changeQuantity: function changeQuantity() {
        var _this4 = this;

        (0, _http.post)(this.product.updateQuantityUrl, {
          quantity: product.quantity
        }).then(function (result) {
          _this4.$dispatch('cart-change', {
            count: result.count
          });

          _this4.$dispatch('notify', {
            message: 'The item quantity was successfully updated'
          });
        })["catch"](function (response) {
          _this4.$dispatch('notify', {
            message: response.message || 'Server Error. Please try again',
            type: 'error'
          });
        });
      }
    };
  });
});

_alpinejs["default"].start();