import { default as eventAPI } from './event';
import { default as domAPI } from './dom';
import { default as virtualAPI } from './virtual';

function ScrollerBar(callback = null, opts = {}) {
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
    Object.keys(opts).forEach((key) => {
        this[`__${key}`] = opts[key];
    });
}

Object.assign(
    ScrollerBar.prototype,
    domAPI,
    eventAPI,
    virtualAPI
);

export default ScrollerBar;
