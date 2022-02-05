/**
 * Copyright (c) Tiny Technologies, Inc. All rights reserved.
 * Licensed under the LGPL or a commercial license.
 * For LGPL see License.txt in the project root for license information.
 * For commercial licenses see https://www.tiny.cloud/
 *
 * Version: 5.10.2 (2021-11-17)
 */
!function(){"use strict";var n=tinymce.util.Tools.resolve("tinymce.PluginManager"),r=tinymce.util.Tools.resolve("tinymce.Env");n.add("print",function(n){var t,i;function e(){return i.execCommand("mcePrint")}(t=n).addCommand("mcePrint",function(){r.browser.isIE()?t.getDoc().execCommand("print",!1,null):t.getWin().print()}),(i=n).ui.registry.addButton("print",{icon:"print",tooltip:"Print",onAction:e}),i.ui.registry.addMenuItem("print",{text:"Print...",icon:"print",onAction:e}),n.addShortcut("Meta+P","","mcePrint")})}();