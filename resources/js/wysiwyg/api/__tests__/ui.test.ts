import {createEditorApiInstance} from "./api-test-utils";
import {EditorApiButton, EditorApiToolbar, EditorApiToolbarSection} from "../ui";
import {getMainEditorFullToolbar} from "../../ui/defaults/toolbars";
import {EditorContainerUiElement} from "../../ui/framework/core";
import {EditorOverflowContainer} from "../../ui/framework/blocks/overflow-container";


describe('Editor API: UI Module', () => {

    describe('createButton()', () => {
        it('should return a button', () => {
            const {api} = createEditorApiInstance();
            const button = api.ui.createButton({label: 'Test', icon: 'test', action: () => ''});
            expect(button).toBeInstanceOf(EditorApiButton);
        });

        it('should only need action to be required', () => {
            const {api} = createEditorApiInstance();
            const button = api.ui.createButton({action: () => ''});
            expect(button).toBeInstanceOf(EditorApiButton);
        });

        it('should pass the label and icon to the button', () => {
            const {api} = createEditorApiInstance();
            const button = api.ui.createButton({label: 'TestLabel', icon: '<svg>cat</svg>', action: () => ''});
            const html = button._getOriginalModel().getDOMElement().outerHTML;
            expect(html).toContain('TestLabel');
            expect(html).toContain('<svg>cat</svg>');
        })
    });

    describe('EditorApiButton', () => {

        describe('setActive()', () => {
            it('should update the active state of the button', () => {
                const {api} = createEditorApiInstance();
                const button = api.ui.createButton({label: 'Test', icon: 'test', action: () => ''});

                button.setActive(true);
                expect(button._getOriginalModel().isActive()).toBe(true);

                button.setActive(false);
                expect(button._getOriginalModel().isActive()).toBe(false);
            })
        });

        it('should call the provided action on click', () => {
            const {api} = createEditorApiInstance();
            let count = 0;
            const button = api.ui.createButton({label: 'Test', icon: 'test', action: () => {
                count++;
            }});

            const dom = button._getOriginalModel().getDOMElement();
            dom.click();
            dom.click();
            expect(count).toBe(2);
        });

    });

    describe('getMainToolbar()', () => {
        it('should return the main editor toolbar', () => {
            const {api, context} = createEditorApiInstance();
            context.manager.setToolbar(getMainEditorFullToolbar(context));

            const toolbar = api.ui.getMainToolbar();

            expect(toolbar).toBeInstanceOf(EditorApiToolbar);
        });
    });

    describe('EditorApiToolbar', () => {
        describe('getSections()', () => {
            it('should return the sections of the toolbar', () => {
                const {api, context} = createEditorApiInstance();
                context.manager.setToolbar(testToolbar());
                const toolbar = api.ui.getMainToolbar();

                const sections = toolbar?.getSections() || [];

                expect(sections.length).toBe(2);
                expect(sections[0]).toBeInstanceOf(EditorApiToolbarSection);
            })
        })
    })

    describe('EditorApiToolbarSection', () => {

        describe('getLabel()', () => {
            it('should return the label of the section', () => {
                const {api, context} = createEditorApiInstance();
                context.manager.setToolbar(testToolbar());
                const section = api.ui.getMainToolbar()?.getSections()[0] as EditorApiToolbarSection;
                expect(section.getLabel()).toBe('section-a');
             })
        });

        describe('addButton()', () => {
            it('should add a button to the section', () => {
                const {api, context} = createEditorApiInstance();
                const toolbar = testToolbar();
                context.manager.setToolbar(toolbar);
                const section = api.ui.getMainToolbar()?.getSections()[0] as EditorApiToolbarSection;

                const button = api.ui.createButton({label: 'TestButtonText!', action: () => ''});
                section.addButton(button);

                const toolbarRendered = toolbar.getDOMElement().innerHTML;
                expect(toolbarRendered).toContain('TestButtonText!');
            });
        });

    });

    function testToolbar(): EditorContainerUiElement {
        return new EditorContainerUiElement([
            new EditorOverflowContainer('section-a', 1, []),
            new EditorOverflowContainer('section-b', 1, []),
        ]);
    }

});