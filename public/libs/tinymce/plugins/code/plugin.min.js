/**
 * Copyright (c) Tiny Technologies, Inc. All rights reserved.
 * Licensed under the LGPL or a commercial license.
 * For LGPL see License.txt in the project root for license information.
 * For commercial licenses see https://www.tiny.cloud/
 *
 * Version: 5.10.2 (2021-11-17)
 */
!function(){"use strict";tinymce.util.Tools.resolve("tinymce.PluginManager").add("code",function(e){var t,o;function n(){return o.execCommand("mceCodeEditor")}return(t=e).addCommand("mceCodeEditor",function(){var n,e;e=(n=t).getContent({source_view:!0}),n.windowManager.open({title:"Source Code",size:"large",body:{type:"panel",items:[{type:"textarea",name:"code"}]},buttons:[{type:"cancel",name:"cancel",text:"Cancel"},{type:"submit",name:"save",text:"Save",primary:!0}],initialData:{code:e},onSubmit:function(e){var t=n,o=e.getData().code;t.focus(),t.undoManager.transact(function(){t.setContent(o)}),t.selection.setCursorLocation(),t.nodeChanged(),e.close()}})}),(o=e).ui.registry.addButton("code",{icon:"sourcecode",tooltip:"Source code",onAction:n}),o.ui.registry.addMenuItem("code",{icon:"sourcecode",text:"Source code",onAction:n}),{}})}();