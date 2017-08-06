
let componentMapping = {
    'dropdown': require('./dropdown'),
    'overlay': require('./overlay'),
    'back-to-top': require('./back-top-top'),
    'notification': require('./notification'),
    'chapter-toggle': require('./chapter-toggle'),
    'expand-toggle': require('./expand-toggle'),
};

window.components = {};

let componentNames = Object.keys(componentMapping);

for (let i = 0, len = componentNames.length; i < len; i++) {
    let name = componentNames[i];
    let elems = document.querySelectorAll(`[${name}]`);
    if (elems.length === 0) continue;

    let component = componentMapping[name];
    if (typeof window.components[name] === "undefined") window.components[name] = [];
    for (let j = 0, jLen = elems.length; j < jLen; j++) {
         let instance = new component(elems[j]);
         window.components[name].push(instance);
    }
}