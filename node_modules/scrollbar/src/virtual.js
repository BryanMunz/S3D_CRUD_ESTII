export default {
    setDimension(x, y, w1, h1, w2, h2, w3, h3) {
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

    _withinBar(x, y) {
        const { w1, h1, t3, w3, h3 } = this;

        return x <= w1 &&
            x >= (w1 - w3) &&
            y >= t3 &&
            y <= (t3 + h3);
    }
};
