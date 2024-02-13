var ScrollBar = (function () {
  'use strict';

  function bindAll(context) {
      for (var i = 1; i < arguments.length; i += 1) {
          var name = arguments[i];
          var method = context[name];

          context[name] = method.bind(context);
      }
  };

  var raf = function () {
      return window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame || window.msRequestAnimationFrame || function (callback) {
          return window.setTimeout(callback, 1000 / 60);
      };
  }();

  var caf = function () {
      return window.cancelAnimationFrame || window.webkitCancelRequestAnimationFrame || window.mozCancelRequestAnimationFrame || window.oCancelRequestAnimationFrame || window.msCancelRequestAnimationFrame || clearTimeout;
  }();

  var eventAPI = {
      doRefine: function doRefine() {
          var h1 = this.h1;
          var h2 = this.h2;
          var h3 = this.h3;
          var t2 = this.t2;
          var t3 = this.t3;


          t2 = Math.max(t2, h1 - h2);
          t2 = Math.min(0, t2);
          t3 = Math.max(t3, 0);
          t3 = Math.min(h1 - h3, t3);

          this.t2 = t2;
          this.t3 = t3;
      },
      doNotify: function doNotify() {
          var force = arguments.length <= 0 || arguments[0] === undefined ? false : arguments[0];
          var w1 = this.w1;
          var h1 = this.h1;
          var l1 = this.l1;
          var t1 = this.t1;
          var __t1 = this.__t1;
          var w2 = this.w2;
          var h2 = this.h2;
          var l2 = this.l2;
          var t2 = this.t2;
          var __t2 = this.__t2;
          var w3 = this.w3;
          var h3 = this.h3;
          var l3 = this.l3;
          var t3 = this.t3;
          var __t3 = this.__t3;


          if (force || t1 !== __t1 || t2 !== __t2 || t3 !== __t3) {

              this.__callback(w1, h1, l1, t1, w2, h2, l2, t2, w3, h3, l3, t3);

              this.__t1 = t1;
              this.__t2 = t2;
              this.__t3 = t3;
          }
      },
      doMove: function doMove() {
          var h1 = this.h1;
          var h2 = this.h2;
          var v2 = this.v2;


          this.v2 *= this.__friction;

          if (Math.abs(this.v2) < 1 && Math.abs(this.v3) < 1) {
              return;
          }

          this.t2 += this.v2;
          this.t3 = this.t2 * h1 / h2 * -1;;

          this.doRefine();
          this.doNotify();
      },
      doLoop: function doLoop() {
          this.doMove();
          caf(this._handler);
          this._handler = raf(this.doLoop.bind(this));
      },
      doScrollTo: function doScrollTo(x, y) {
          var animate = arguments.length <= 2 || arguments[2] === undefined ? true : arguments[2];

          var deltaT = y * -1 - this.t2;
          var direction = deltaT / Math.abs(deltaT) || 1;

          if (animate) {
              this.v2 = deltaT * (1 - this.__friction) / this.__friction + direction;
          } else {
              var h1 = this.h1;
              var h2 = this.h2;


              this.t2 = y * -1;
              this.t3 = this.t2 * h1 / h2 * -1;

              this.doRefine();
              this.doNotify();
          }
      },
      doScrollBy: function doScrollBy() {
          var x = arguments.length <= 0 || arguments[0] === undefined ? 0 : arguments[0];
          var y = arguments.length <= 1 || arguments[1] === undefined ? 0 : arguments[1];
          var animate = arguments[2];

          this.doScrollTo(x, y + this.t2 * -1, animate);
      },
      doMouseDown: function doMouseDown(x, y, timeStamp) {
          var withinBar = this._withinBar(x, y);

          if (withinBar) {
              this.__drag = true;
              this._t3 = this.t3;
              this.mx = x;
              this.my = y;
          }

          return withinBar;
      },
      doMouseMove: function doMouseMove(x, y, timeStamp) {
          if (!this.__drag) {
              return;
          }

          var h1 = this.h1;
          var h2 = this.h2;
          var _t3 = this._t3;


          var xDelta = x - this.mx;
          var yDelta = y - this.my;

          this.t3 = yDelta + _t3;
          this.t2 = this.t3 * h2 / h1 * -1;

          this.doRefine();
          this.doNotify();
      },
      doMouseUp: function doMouseUp(timeStamp) {
          this.__drag = false;
      },
      doMouseWheel: function doMouseWheel(delta, x, y, timeStamp) {
          this.v2 += delta * this.__step;
      }
  };

  var domAPI = {
      setNode: function setNode() {
          var node = arguments.length <= 0 || arguments[0] === undefined ? document.querySelector('body') : arguments[0];

          this.n1 = node.querySelector(this.__wrapper);
          this.n2 = node.querySelector(this.__scroller);
          this.n3 = node.querySelector(this.__bar);

          this._bindAll();
          this._installListener();
          this.doRefresh();
          this.doLoop();
      },
      doRefresh: function doRefresh() {
          var bounding = this.n1.getBoundingClientRect();

          this.setDimension(bounding.left, bounding.top, this.n1.clientWidth, this.n1.clientHeight, this.n2.clientWidth, this.n2.clientHeight, this.n3.clientWidth, this.n3.clientHeight);
          this.doScrollBy(0, 0, false);
          this.doNotify(true);
      },
      _bindAll: function _bindAll() {
          bindAll(this, '_onResize', '_onMouseUp', '_onMouseDown', '_onMouseMove', '_onMouseWheel');
      },
      _installListener: function _installListener() {
          window.addEventListener('resize', this._onResize);
          window.addEventListener('mouseup', this._onMouseUp);
          window.addEventListener('mousemove', this._onMouseMove);
          this.n1.addEventListener('mousedown', this._onMouseDown);
          this.n1.addEventListener('mousewheel', this._onMouseWheel);
          this.n1.addEventListener('DOMMouseScroll', this._onMouseWheel);
      },
      _uninstallListener: function _uninstallListener() {
          window.removeEventListener('resize', this._onResize);
          window.removeEventListener('mouseup', this._onMouseUp);
          window.removeEventListener('mousemove', this._onMouseMove);
          this.n1.removeEventListener('mousedown', this._onMouseDown);
          this.n1.removeEventListener('mousewheel', this._onMouseWheel);
          this.n1.removeEventListener('DOMMouseScroll', this._onMouseWheel);
      },
      _antiBoundingX: function _antiBoundingX(x) {
          return x - this.x;
      },
      _antiBoundingY: function _antiBoundingY(y) {
          return y - this.y;
      },
      _onResize: function _onResize(ev) {
          this.doRefresh();
      },
      _onMouseUp: function _onMouseUp(ev) {
          this.doMouseUp(ev.timeStamp);
      },
      _onMouseDown: function _onMouseDown(ev) {
          var withinBar = this.doMouseDown(this._antiBoundingX(ev.clientX), this._antiBoundingY(ev.clientY), ev.timeStamp);

          if (withinBar) {
              ev.preventDefault();
          }
      },
      _onMouseMove: function _onMouseMove(ev) {
          this.doMouseMove(this._antiBoundingX(ev.clientX), this._antiBoundingY(ev.clientY), ev.timeStamp);
      },
      _onMouseWheel: function _onMouseWheel(ev) {
          ev.preventDefault();

          var delta = ev.detail ? ev.detail * -1 : ev.wheelDelta / 40;

          if (delta === 0) {
              return;
          }

          this.doMouseWheel(delta / Math.abs(delta), this._antiBoundingX(ev.clientX), this._antiBoundingY(ev.clientY), ev.timeStamp);
      }
  };

  var virtualAPI = {
      setDimension: function setDimension(x, y, w1, h1, w2, h2, w3, h3) {
          this.x = x;
          this.y = y;
          this.w1 = w1;
          this.h1 = h1;
          this.w2 = w2;
          this.h2 = h2;
          this.w3 = w3;
          this.h3 = h2 > h1 ? h1 * h1 / h2 : h1;
          this.l3 = w1 - w3;
      },
      _withinBar: function _withinBar(x, y) {
          var w1 = this.w1;
          var h1 = this.h1;
          var t3 = this.t3;
          var w3 = this.w3;
          var h3 = this.h3;


          return x <= w1 && x >= w1 - w3 && y >= t3 && y <= t3 + h3;
      }
  };

  var _extends = Object.assign || function (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];

      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }

    return target;
  };

  function ScrollerBar() {
      var _this = this;

      var callback = arguments.length <= 0 || arguments[0] === undefined ? null : arguments[0];
      var opts = arguments.length <= 1 || arguments[1] === undefined ? {} : arguments[1];

      this.x = 0; // bounding x
      this.y = 0; // bounding y

      this.n1 = null; // wrapper node
      this.n2 = null; // scroller node
      this.n3 = null; // bar node

      this.w1 = 0; // wrapper width
      this.w2 = 0; // scroller width
      this.w3 = 0; // bar width

      this.h1 = 0; // wrapper height
      this.h2 = 0; // scroller height
      this.h3 = 0; // bar height

      this.t1 = 0; // wrapper top offest
      this.t2 = 0; // scroller top offset
      this.t3 = 0; // bar top offset

      this.v1 = 0; // wrapper movment
      this.v2 = 0; // scroller movment
      this.v3 = 0; // bar movment

      this.l1 = 0; // wrapper lefe offset
      this.l2 = 0; // scroller left offset
      this.l3 = 0; // bar left offset

      this.mx = 0; // mouse position x
      this.my = 0; // mouse posiiton y

      this.__drag = false; // dragging bar?
      this.__callback = callback; // notify callback

      this.__friction = 0.8; // animate friction
      this.__step = 25; // step movment
      this.__handler = null; // timer handler

      this.__wrapper = '.wrapper'; // wrapper querier
      this.__scroller = '.scroller'; // scroller querier
      this.__bar = '.bar'; // bar querier

      // apply customize configuration
      Object.keys(opts).forEach(function (key) {
          _this['__' + key] = opts[key];
      });
  }

  _extends(ScrollerBar.prototype, domAPI, eventAPI, virtualAPI);

  return ScrollerBar;

}());