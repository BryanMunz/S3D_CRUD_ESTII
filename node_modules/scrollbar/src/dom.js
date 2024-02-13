import { bindAll } from './util';

export default {
    setNode(node = document.querySelector('body')) {
        this.n1 = node.querySelector(this.__wrapper);
        this.n2 = node.querySelector(this.__scroller);
        this.n3 = node.querySelector(this.__bar);

        this._bindAll();
        this._installListener();
        this.doRefresh();
        this.doLoop();
    },

    doRefresh() {
        const bounding = this.n1.getBoundingClientRect();

        this.setDimension(
            bounding.left,
            bounding.top,
            this.n1.clientWidth,
            this.n1.clientHeight,
            this.n2.clientWidth,
            this.n2.clientHeight,
            this.n3.clientWidth,
            this.n3.clientHeight
        );
        this.doScrollBy(0, 0, false);
        this.doNotify(true);
    },

    _bindAll() {
        bindAll(this,
            '_onResize',
            '_onMouseUp',
            '_onMouseDown',
            '_onMouseMove',
            '_onMouseWheel'
        );
    },

    _installListener() {
        window.addEventListener('resize', this._onResize);
        window.addEventListener('mouseup', this._onMouseUp);
        window.addEventListener('mousemove', this._onMouseMove);
        this.n1.addEventListener('mousedown', this._onMouseDown);
        this.n1.addEventListener('mousewheel', this._onMouseWheel);
        this.n1.addEventListener('DOMMouseScroll', this._onMouseWheel);
    },

    _uninstallListener() {
        window.removeEventListener('resize', this._onResize);
        window.removeEventListener('mouseup', this._onMouseUp);
        window.removeEventListener('mousemove', this._onMouseMove);
        this.n1.removeEventListener('mousedown', this._onMouseDown);
        this.n1.removeEventListener('mousewheel', this._onMouseWheel);
        this.n1.removeEventListener('DOMMouseScroll', this._onMouseWheel);
    },

    _antiBoundingX(x) {
        return x - this.x;
    },

    _antiBoundingY(y) {
        return y - this.y;
    },

    _onResize(ev) {
        this.doRefresh();
    },

    _onMouseUp(ev) {
        this.doMouseUp(ev.timeStamp);
    },

    _onMouseDown(ev) {
        const withinBar = this.doMouseDown(
            this._antiBoundingX(ev.clientX),
            this._antiBoundingY(ev.clientY),
            ev.timeStamp
        );

        if (withinBar) {
            ev.preventDefault();
        }
    },

    _onMouseMove(ev) {
        this.doMouseMove(
            this._antiBoundingX(ev.clientX),
            this._antiBoundingY(ev.clientY),
            ev.timeStamp
        );
    },

    _onMouseWheel(ev) {
        ev.preventDefault();

        const delta = ev.detail ? ev.detail * -1 : ev.wheelDelta / 40;

        if (delta === 0) {
            return;
        }

        this.doMouseWheel(
            delta / Math.abs(delta),
            this._antiBoundingX(ev.clientX),
            this._antiBoundingY(ev.clientY),
            ev.timeStamp
        );
    }
};
