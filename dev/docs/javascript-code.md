# BookStack JavaScript Code

BookStack is primarily server-side-rendered, but it uses JavaScript sparingly to drive any required dynamic elements. Most JavaScript is applied via a custom, and very thin, component interface to keep code organised and somewhat reusable.

JavaScript source code can be found in the `resources/js` directory. This gets bundled and transformed by `esbuild`, ending up in the `public/dist` folder for browser use. Read the [Development > "Building CSS & JavaScript Assets"](development.md#building-css-&-javascript-assets) documentation for details on this process.

## Components

This section details the format for JavaScript components in BookStack. This is a really simple class-based setup with a few helpers provided.

### Defining a Component in JS

```js
class Dropdown {
    setup() {
        this.container = this.$el;
        this.menu = this.$refs.menu;
        this.toggles = this.$manyRefs.toggle;
    
        this.speed = parseInt(this.$opts.speed);
    }
}
```

All usage of $refs, $manyRefs and $opts should be done at the top of the `setup` function so any requirements can be easily seen.

Once defined, the component has to be registered for use. This is done in the `resources/js/components/index.js` file. You'll need to import the component class then add it to `componentMapping` object, following the pattern of other components. 

### Using a Component in HTML

A component is used like so:

```html
<div component="dropdown"></div>

<!-- or, for multiple -->

<div components="dropdown image-picker"></div>
```

The names will be parsed and new component instance will be created if a matching name is found in the `components/index.js` componentMapping. 

### Element References

Within a component you'll often need to refer to other element instances. This can be done like so:

```html
<div component="dropdown">
    <span refs="dropdown@toggle othercomponent@handle">View more</span>
</div>
```

You can then access the span element as `this.$refs.toggle` in your component.

Multiple elements of the same reference name can be accessed via a `this.$manyRefs` property within your component. For example, all the buttons in the below example could be accessed via `this.$manyRefs.buttons`.

```html
<div component="list">
    <button refs="list@button">Click here</button>
    <button refs="list@button">No, Click here</button>
    <button refs="list@button">This button is better</button>
</div>
```

### Component Options

```html
<div component="dropdown"
    option:dropdown:delay="500"
    option:dropdown:show>
</div>
```

Will result with `this.$opts` being:

```json
{
    "delay": "500",
    "show": ""  
}
```

#### Component Properties

A component has the below shown properties available for use. As mentioned above, most of these should be used within the `setup()` function to make the requirements/dependencies of the component clear.

```javascript
// The root element that the compontent has been applied to.
this.$el

// A map of defined element references within the compontent.
// See "Element References" above.
this.$refs

// A map of defined multi-element references within the compontent.
// See "Element References" above.
this.$manyRefs

// Options defined for the compontent.
this.$opts
```

## Global JavaScript Helpers

There are various global helper libraries in BookStack which can be accessed via the `window`. The below provides an overview of what's available. 

```js
// HTTP service
window.$http.get(url, params);
window.$http.post(url, data);
window.$http.put(url, data);
window.$http.delete(url, data);
window.$http.patch(url, data);

// Global event system
// Emit a global event
window.$events.emit(eventName, eventData);
// Listen to a global event
window.$events.listen(eventName, callback);
// Show a success message
window.$events.success(message);
// Show an error message
window.$events.error(message);
// Show validation errors, if existing, as an error notification
window.$events.showValidationErrors(error);

// Translator
// Take the given plural text and count to decide on what plural option
// to use, Similar to laravel's trans_choice function but instead
// takes the direction directly instead of a translation key.
window.trans_plural(translationString, count, replacements);

// Component System
// Parse and initialise any components from the given root el down.
window.components.init(rootEl);
// Get the first active component of the given name
window.components.first(name);
```