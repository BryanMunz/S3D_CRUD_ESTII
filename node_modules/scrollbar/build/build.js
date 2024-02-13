const rollup = require('rollup').rollup;
const babel = require('rollup-plugin-babel');

rollup({
    entry: 'src/core.js',
    plugins: [
        babel()
    ]
}).then((bundle) => {
    bundle.write({
        dest: 'dest/scrollbar-cjs.js',
        moduleName: 'ScrollBar',
        format: 'cjs'
    });
    bundle.write({
        dest: 'dest/scrollbar-iife.js',
        moduleName: 'ScrollBar',
        format: 'iife'
    });
}).catch(console.error);
