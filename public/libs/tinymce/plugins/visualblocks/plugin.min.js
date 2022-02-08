/**
 * Copyright (c) Tiny Technologies, Inc. All rights reserved.
 * Licensed under the LGPL or a commercial license.
 * For LGPL see License.txt in the project root for license information.
 * For commercial licenses see https://www.tiny.cloud/
 *
 * Version: 5.10.2 (2021-11-17)
 */
!function(){"use strict";function f(t,o,e){var n,i;t.dom.toggleClass(t.getBody(),"mce-visualblocks"),e.set(!e.get()),n=t,i=e.get(),n.fire("VisualBlocks",{state:i})}function g(e,n){return function(o){function t(t){return o.setActive(t.state)}return o.setActive(n.get()),e.on("VisualBlocks",t),function(){return e.off("VisualBlocks",t)}}}tinymce.util.Tools.resolve("tinymce.PluginManager").add("visualblocks",function(t,o){var e,n,i,s,c,u,l,a=(e=!1,{get:function(){return e},set:function(t){e=t}});function r(){return s.execCommand("mceVisualBlocks")}i=a,(n=t).addCommand("mceVisualBlocks",function(){f(n,0,i)}),(s=t).ui.registry.addToggleButton("visualblocks",{icon:"visualblocks",tooltip:"Show blocks",onAction:r,onSetup:g(s,c=a)}),s.ui.registry.addToggleMenuItem("visualblocks",{text:"Show blocks",icon:"visualblocks",onAction:r,onSetup:g(s,c)}),l=a,(u=t).on("PreviewFormats AfterPreviewFormats",function(t){l.get()&&u.dom.toggleClass(u.getBody(),"mce-visualblocks","afterpreviewformats"===t.type)}),u.on("init",function(){u.getParam("visualblocks_default_state",!1,"boolean")&&f(u,0,l)})})}();