// Url retrieval function
window.baseUrl = function(path) {
    let basePath = document.querySelector('meta[name="base-url"]').getAttribute('content');
    if (basePath[basePath.length-1] === '/') basePath = basePath.slice(0, basePath.length-1);
    if (path[0] === '/') path = path.slice(1);
    return basePath + '/' + path;
};

window.importVersioned = function(moduleName) {
    const version = document.querySelector('link[href*="/dist/styles.css?version="]').href.split('?version=').pop();
    const importPath = window.baseUrl(`dist/${moduleName}.js?version=${version}`);
    return import(importPath);
};

// Set events and http services on window
import events from "./services/events"
import httpInstance from "./services/http"
window.$http = httpInstance;
window.$events = events;

// Translation setup
// Creates a global function with name 'trans' to be used in the same way as Laravel's translation system
import Translations from "./services/translations"
const translator = new Translations();
window.trans = translator.get.bind(translator);
window.trans_choice = translator.getPlural.bind(translator);
window.trans_plural = translator.parsePlural.bind(translator);

// Load Components
import * as components from "./services/components"
import * as componentMap from "./components";
components.register(componentMap);
window.$components = components;
components.init();

import $ from "jquery";
$.when($.ready).then(function () {
    const scrollYPosition = localStorage.getItem(window.location.href + "-scroll-y");
    if (scrollYPosition !== null) {
        window.scrollTo(0, scrollYPosition)
    }
    $(window).scroll(function () {
        localStorage.setItem(window.location.href + "-scroll-y", window.scrollY);
    });
		$('#startReadingButton').on('click', ()=>{
			const userEmailAddress = $("#welcomePageEmailAddressInput").val();
			window.location.href = `/register?email=${userEmailAddress}`;
		})

		$('#userRegistration').on('click', ()=>{
			const userEmailAddress = $("#welcomePageEmailAddressInput").val();
			if(userEmailAddress)
				window.location.href = `/register?email=${userEmailAddress}`;
			else	
				window.location.href = `/register`;
		})


		
});