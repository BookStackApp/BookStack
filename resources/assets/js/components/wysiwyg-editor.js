class WysiwygEditor {

    constructor(elem) {
        this.elem = elem;
        this.options = require("../pages/page-form");
        tinymce.init(this.options);
    }

}

module.exports = WysiwygEditor;