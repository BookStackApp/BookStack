# JavaScript Components

This document details the format for JavaScript components in BookStack. This is a really simple class-based setup with a few helpers provided.

#### Defining a Component in JS

```js
class Dropdown {
    setup() {
        this.toggle = this.$refs.toggle;
        this.menu = this.$refs.menu;
    
        this.speed = parseInt(this.$opts.speed);
    }
}
```

All usage of $refs, $manyRefs and $opts should be done at the top of the `setup` function so any requirements can be easily seen.

#### Using a Component in HTML

A component is used like so:

```html
<div component="dropdown"></div>

<!-- or, for multiple -->

<div components="dropdown image-picker"></div>
```

The names will be parsed and new component instance will be created if a matching name is found in the `components/index.js` componentMapping. 

#### Element References

Within a component you'll often need to refer to other element instances. This can be done like so:

```html
<div component="dropdown">
    <span refs="dropdown@toggle othercomponent@handle">View more</span>
</div>
```

You can then access the span element as `this.$refs.toggle` in your component.

#### Component Options

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

#### Global Helpers

There are various global helper libraries which can be used in components:

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