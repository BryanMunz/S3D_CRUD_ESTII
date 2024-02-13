export function bindAll(context) {
    for (let i = 1; i < arguments.length; i += 1) {
        const name = arguments[i];
        const method = context[name];

        context[name] = method.bind(context);
    }
};

export const raf = (() => {
    return window.requestAnimationFrame ||
        window.webkitRequestAnimationFrame ||
        window.mozRequestAnimationFrame ||
        window.oRequestAnimationFrame ||
        window.msRequestAnimationFrame ||
        ((callback) => {
            return window.setTimeout(callback, 1000 / 60);
        });
})();

export const caf = (() => {
    return window.cancelAnimationFrame ||
        window.webkitCancelRequestAnimationFrame ||
        window.mozCancelRequestAnimationFrame ||
        window.oCancelRequestAnimationFrame ||
        window.msCancelRequestAnimationFrame ||
        clearTimeout
})();