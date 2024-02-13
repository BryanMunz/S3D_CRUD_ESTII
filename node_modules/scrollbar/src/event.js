import { raf, caf } from './util';

export default {
    doRefine() {
        let { h1, h2, h3, t2, t3 } = this;

        t2 = Math.max(t2, h1 - h2);
        t2 = Math.min(0, t2);
        t3 = Math.max(t3, 0)
        t3 = Math.min(h1 - h3, t3);

        this.t2 = t2;
        this.t3 = t3;
    },

    doNotify(force = false) {
        let {
            w1, h1, l1, t1, __t1,
            w2, h2, l2, t2, __t2,
            w3, h3, l3, t3, __t3
        } = this;

        if (force ||
            t1 !== __t1 ||
            t2 !== __t2 ||
            t3 !== __t3) {

            this.__callback(
                w1, h1, l1, t1,
                w2, h2, l2, t2,
                w3, h3, l3, t3
            );

            this.__t1 = t1;
            this.__t2 = t2;
            this.__t3 = t3;
        }
    },

    doMove() {
        let { h1, h2, v2 } = this;

        this.v2 *= this.__friction;

        if (Math.abs(this.v2) < 1 && Math.abs(this.v3) < 1) {
            return;
        }

        this.t2 += this.v2;
        this.t3 = this.t2 * h1 / h2 * -1;;

        this.doRefine();
        this.doNotify();
    },

    doLoop() {
        this.doMove();
        caf(this._handler);
        this._handler = raf(this.doLoop.bind(this));
    },

    doScrollTo(x, y, animate = true) {
        const deltaT = y * -1 - this.t2;
        const direction = deltaT / Math.abs(deltaT) || 1;

        if (animate) {
            this.v2 = deltaT * (1 - this.__friction) / this.__friction + direction;
        } else {
            let { h1, h2 } = this;

            this.t2 = y * -1;
            this.t3 = this.t2 * h1 / h2 * -1;

            this.doRefine();
            this.doNotify();
        }
    },

    doScrollBy(x = 0, y = 0, animate) {
        this.doScrollTo(x, y + this.t2 * -1, animate);
    },

    doMouseDown(x, y, timeStamp) {
        const withinBar = this._withinBar(x, y);

        if (withinBar) {
            this.__drag = true;
            this._t3 = this.t3;
            this.mx = x;
            this.my = y;
        }

        return withinBar;
    },

    doMouseMove(x, y, timeStamp) {
        if (!this.__drag) {
            return;
        }

        let { h1, h2, _t3 } = this;

        let xDelta = x - this.mx;
        let yDelta = y - this.my;

        this.t3 = yDelta + _t3;
        this.t2 = this.t3 * h2 / h1 * -1;

        this.doRefine();
        this.doNotify();
    },

    doMouseUp(timeStamp) {
        this.__drag = false;
    },

    doMouseWheel(delta, x, y, timeStamp) {
        this.v2 += delta * this.__step;
    }
};
