/**
 * Copyright (c) Tiny Technologies, Inc. All rights reserved.
 * Licensed under the LGPL or a commercial license.
 * For LGPL see License.txt in the project root for license information.
 * For commercial licenses see https://www.tiny.cloud/
 *
 * Version: 5.10.2 (2021-11-17)
 */
!function(){"use strict";function o(n,e){for(var a="",o=0;o<e;o++)a+=n;return a}function s(n,e){var a=n.getParam("nonbreaking_wrap",!0,"boolean")||n.plugins.visualchars?'<span class="'+(n.plugins.visualchars&&n.plugins.visualchars.isEnabled()?"mce-nbsp-wrap mce-nbsp":"mce-nbsp-wrap")+'" contenteditable="false">'+o("&nbsp;",e)+"</span>":o("&nbsp;",e);n.undoManager.transact(function(){return n.insertContent(a)})}var n=tinymce.util.Tools.resolve("tinymce.PluginManager"),c=tinymce.util.Tools.resolve("tinymce.util.VK");n.add("nonbreaking",function(n){var e,a,o,t,i;function r(){return a.execCommand("mceNonBreaking")}(e=n).addCommand("mceNonBreaking",function(){s(e,1)}),(a=n).ui.registry.addButton("nonbreaking",{icon:"non-breaking",tooltip:"Nonbreaking space",onAction:r}),a.ui.registry.addMenuItem("nonbreaking",{icon:"non-breaking",text:"Nonbreaking space",onAction:r}),0<(i="boolean"==typeof(t=(o=n).getParam("nonbreaking_force_tab",0))?!0===t?3:0:t)&&o.on("keydown",function(n){n.keyCode!==c.TAB||n.isDefaultPrevented()||n.shiftKey||(n.preventDefault(),n.stopImmediatePropagation(),s(o,i))})})}();