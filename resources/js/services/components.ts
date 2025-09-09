import {kebabToCamel, camelToKebab} from './text';
import {Component} from "../components/component";

/**
 * Parse out the element references within the given element
 * for the given component name.
 */
function parseRefs(name: string, element: HTMLElement):
    {refs: Record<string, HTMLElement>, manyRefs: Record<string, HTMLElement[]>} {
    const refs: Record<string, HTMLElement> = {};
    const manyRefs: Record<string, HTMLElement[]> = {};

    const prefix = `${name}@`;
    const selector = `[refs*="${prefix}"]`;
    const refElems = [...element.querySelectorAll(selector)];
    if (element.matches(selector)) {
        refElems.push(element);
    }

    for (const el of refElems as HTMLElement[]) {
        const refNames = (el.getAttribute('refs') || '')
            .split(' ')
            .filter(str => str.startsWith(prefix))
            .map(str => str.replace(prefix, ''))
            .map(kebabToCamel);
        for (const ref of refNames) {
            refs[ref] = el;
            if (typeof manyRefs[ref] === 'undefined') {
                manyRefs[ref] = [];
            }
            manyRefs[ref].push(el);
        }
    }
    return {refs, manyRefs};
}

/**
 * Parse out the element component options.
 */
function parseOpts(componentName: string, element: HTMLElement): Record<string, string> {
    const opts: Record<string, string> = {};
    const prefix = `option:${componentName}:`;
    for (const {name, value} of element.attributes) {
        if (name.startsWith(prefix)) {
            const optName = name.replace(prefix, '');
            opts[kebabToCamel(optName)] = value || '';
        }
    }
    return opts;
}

export class ComponentStore {
    /**
     * A mapping of active components keyed by name, with values being arrays of component
     * instances since there can be multiple components of the same type.
     */
    protected components: Record<string, Component[]> = {};

    /**
     * A mapping of component class models, keyed by name.
     */
    protected componentModelMap: Record<string, typeof Component> = {};

    /**
     * A mapping of active component maps, keyed by the element components are assigned to.
     */
    protected elementComponentMap: WeakMap<HTMLElement, Record<string, Component>> = new WeakMap();

    /**
     * Initialize a component instance on the given dom element.
     */
     protected initComponent(name: string, element: HTMLElement): void {
        const ComponentModel = this.componentModelMap[name];
        if (ComponentModel === undefined) return;

        // Create our component instance
        let instance: Component|null = null;
        try {
            instance = new ComponentModel();
            instance.$name = name;
            instance.$el = element;
            const allRefs = parseRefs(name, element);
            instance.$refs = allRefs.refs;
            instance.$manyRefs = allRefs.manyRefs;
            instance.$opts = parseOpts(name, element);
            instance.setup();
        } catch (e) {
            console.error('Failed to create component', e, name, element);
        }

        if (!instance) {
            return;
        }

        // Add to global listing
        if (typeof this.components[name] === 'undefined') {
            this.components[name] = [];
        }
        this.components[name].push(instance);

        // Add to element mapping
        const elComponents = this.elementComponentMap.get(element) || {};
        elComponents[name] = instance;
        this.elementComponentMap.set(element, elComponents);
    }

    /**
     * Initialize all components found within the given element.
     */
    public init(parentElement: Document|HTMLElement = document) {
        const componentElems = parentElement.querySelectorAll('[component],[components]');

        for (const el of componentElems) {
            const componentNames = `${el.getAttribute('component') || ''} ${(el.getAttribute('components'))}`.toLowerCase().split(' ').filter(Boolean);
            for (const name of componentNames) {
                this.initComponent(name, el as HTMLElement);
            }
        }
    }

    /**
     * Register the given component mapping into the component system.
     * @param {Object<String, ObjectConstructor<Component>>} mapping
     */
    public register(mapping: Record<string, typeof Component>) {
        const keys = Object.keys(mapping);
        for (const key of keys) {
            this.componentModelMap[camelToKebab(key)] = mapping[key];
        }
    }

    /**
     * Get the first component of the given name.
     */
    public first(name: string): Component|null {
        return (this.components[name] || [null])[0];
    }

    /**
     * Get all the components of the given name.
     */
    public get<T extends Component>(name: string): T[] {
        return (this.components[name] || []) as T[];
    }

    /**
     * Get the first component, of the given name, that's assigned to the given element.
     */
    public firstOnElement(element: HTMLElement, name: string): Component|null {
        const elComponents = this.elementComponentMap.get(element) || {};
        return elComponents[name] || null;
    }

    public allWithinElement<T extends Component>(element: HTMLElement, name: string): T[] {
        const components = this.get<T>(name);
        return components.filter(c => element.contains(c.$el));
    }
}
