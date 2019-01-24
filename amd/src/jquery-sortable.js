// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Javascript for fetching navigation items from an integrated Drupal site.
 *
 * @package   theme_maistericlean
 * @copyright 2017 Mediamaisteri Oy, https://www.mediamaisteri.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * @module theme_maistericlean/sharednavigation
 */
define(['jquery'], function($) {
    return {
        init: function() {
            console.log("jQuery Sortable init");

            var sortable=function(){"use strict";function d(e,t,n){if(void 0===n)return e&&e.h5s&&e.h5s.data&&e.h5s.data[t];e.h5s=e.h5s||{},e.h5s.data=e.h5s.data||{},e.h5s.data[t]=n}function u(e,t){if(!(e instanceof NodeList||e instanceof HTMLCollection||e instanceof Array))throw new Error("You must provide a nodeList/HTMLCollection/Array of elements to be filtered.");return"string"!=typeof t?Array.from(e):Array.from(e).filter(function(e){return 1===e.nodeType&&e.matches(t)})}var p=new Map,t=function(){function e(){this._config=new Map,this._placeholder=void 0,this._data=new Map}return Object.defineProperty(e.prototype,"config",{get:function(){var n={};return this._config.forEach(function(e,t){n[t]=e}),n},set:function(e){if("object"!=typeof e)throw new Error("You must provide a valid configuration object to the config setter.");var t=Object.assign({},e);this._config=new Map(Object.entries(t))},enumerable:!0,configurable:!0}),e.prototype.setConfig=function(e,t){if(!this._config.has(e))throw new Error("Trying to set invalid configuration item: "+e);this._config.set(e,t)},e.prototype.getConfig=function(e){if(!this._config.has(e))throw new Error("Invalid configuration item requested: "+e);return this._config.get(e)},Object.defineProperty(e.prototype,"placeholder",{get:function(){return this._placeholder},set:function(e){if(!(e instanceof HTMLElement)&&null!==e)throw new Error("A placeholder must be an html element or null.");this._placeholder=e},enumerable:!0,configurable:!0}),e.prototype.setData=function(e,t){if("string"!=typeof e)throw new Error("The key must be a string.");this._data.set(e,t)},e.prototype.getData=function(e){if("string"!=typeof e)throw new Error("The key must be a string.");return this._data.get(e)},e.prototype.deleteData=function(e){if("string"!=typeof e)throw new Error("The key must be a string.");return this._data.delete(e)},e}();function m(e){if(!(e instanceof HTMLElement))throw new Error("Please provide a sortable to the store function.");return p.has(e)||p.set(e,new t),p.get(e)}function g(e,t,n){if(e instanceof Array)for(var r=0;r<e.length;++r)g(e[r],t,n);else e.addEventListener(t,n),m(e).setData("event"+t,n)}function l(e,t){if(e instanceof Array)for(var n=0;n<e.length;++n)l(e[n],t);else e.removeEventListener(t,m(e).getData("event"+t)),m(e).deleteData("event"+t)}function h(e,t,n){if(e instanceof Array)for(var r=0;r<e.length;++r)h(e[r],t,n);else e.setAttribute(t,n)}function s(e,t){if(e instanceof Array)for(var n=0;n<e.length;++n)s(e[n],t);else e.removeAttribute(t)}function v(e){if(!e.parentElement||0===e.getClientRects().length)throw new Error("target element must be part of the dom");var t=e.getClientRects()[0];return{left:t.left+window.pageXOffset,right:t.right+window.pageXOffset,top:t.top+window.pageYOffset,bottom:t.bottom+window.pageYOffset}}function y(e,t){if(!(e instanceof HTMLElement&&(t instanceof NodeList||t instanceof HTMLCollection||t instanceof Array)))throw new Error("You must provide an element and a list of elements.");return Array.from(t).indexOf(e)}function E(e){if(!(e instanceof HTMLElement))throw new Error("Element is not a node element.");return null!==e.parentNode}var n=function(e,t,n){if(!(e instanceof HTMLElement&&e.parentElement instanceof HTMLElement))throw new Error("target and element must be a node");e.parentElement.insertBefore(t,"before"===n?e:e.nextElementSibling)},w=function(e,t){return n(e,t,"before")},b=function(e,t){return n(e,t,"after")};function T(e){if(!(e instanceof HTMLElement))throw new Error("You must provide a valid dom element");var n=window.getComputedStyle(e);return["height","padding-top","padding-bottom"].map(function(e){var t=parseInt(n.getPropertyValue(e),10);return isNaN(t)?0:t}).reduce(function(e,t){return e+t})}function f(e,t){if(!(e instanceof Array))throw new Error("You must provide a Array of HTMLElements to be filtered.");return"string"!=typeof t?e:e.filter(function(e){return e.querySelector(t)instanceof HTMLElement}).map(function(e){return e.querySelector(t)})}var L=function(e,t,n){return{element:e,posX:n.pageX-t.left,posY:n.pageY-t.top}};function C(e,t){if(!0===e.isSortable){var n=m(e).getConfig("acceptFrom");if(null!==n&&!1!==n&&"string"!=typeof n)throw new Error('HTML5Sortable: Wrong argument, "acceptFrom" must be "null", "false", or a valid selector string.');if(null!==n)return!1!==n&&0<n.split(",").filter(function(e){return 0<e.length&&t.matches(e)}).length;if(e===t)return!0;if(void 0!==m(e).getConfig("connectWith")&&null!==m(e).getConfig("connectWith"))return m(e).getConfig("connectWith")===m(t).getConfig("connectWith")}return!1}var M,A,D,x,H,I,S,Y={items:null,connectWith:null,disableIEFix:null,acceptFrom:null,copy:!1,placeholder:null,placeholderClass:"sortable-placeholder",draggingClass:"sortable-dragging",hoverClass:!1,debounce:0,throttleTime:100,maxItems:0,itemSerializer:void 0,containerSerializer:void 0,customDragImage:null};function O(e,t){if("string"==typeof m(e).getConfig("hoverClass")){var o=m(e).getConfig("hoverClass").split(" ");!0===t?(g(e,"mousemove",function(r,o){var i=this;if(void 0===o&&(o=250),"function"!=typeof r)throw new Error("You must provide a function as the first argument for throttle.");if("number"!=typeof o)throw new Error("You must provide a number as the second argument for throttle.");var a=null;return function(){for(var e=[],t=0;t<arguments.length;t++)e[t-0]=arguments[t];var n=Date.now();(null===a||o<=n-a)&&(a=n,r.apply(i,e))}}(function(r){0===r.buttons&&u(e.children,m(e).getConfig("items")).forEach(function(e){var t,n;e!==r.target?(t=e.classList).remove.apply(t,o):(n=e.classList).add.apply(n,o)})},m(e).getConfig("throttleTime"))),g(e,"mouseleave",function(){u(e.children,m(e).getConfig("items")).forEach(function(e){var t;(t=e.classList).remove.apply(t,o)})})):(l(e,"mousemove"),l(e,"mouseleave"))}}var c=function(e){l(e,"dragstart"),l(e,"dragend"),l(e,"dragover"),l(e,"dragenter"),l(e,"drop"),l(e,"mouseenter"),l(e,"mouseleave")},_=function(e,t){var n=e;return!0===m(t).getConfig("copy")&&(h(n=e.cloneNode(!0),"aria-copied","true"),e.parentElement.appendChild(n),n.style.display="none",n.oldDisplay=e.style.display),n};function W(e){for(;!0!==e.isSortable;)e=e.parentElement;return e}function F(e,t){var n=d(e,"opts"),r=u(e.children,n.items).filter(function(e){return e.contains(t)});return 0<r.length?r[0]:t}var r=function(e){var t,n,r,o=d(e,"opts")||{},i=u(e.children,o.items),a=f(i,o.handle);l(e,"dragover"),l(e,"dragenter"),l(e,"drop"),(n=t=e).h5s&&delete n.h5s.data,s(t,"aria-dropeffect"),l(a,"mousedown"),c(i),s(r=i,"aria-grabbed"),s(r,"aria-copied"),s(r,"draggable"),s(r,"role")},N=function(e){var t=d(e,"opts"),n=u(e.children,t.items),r=f(n,t.handle);(h(e,"aria-dropeffect","move"),d(e,"_disabled","false"),h(r,"draggable","true"),!1===t.disableIEFix)&&("function"==typeof(document||window.document).createElement("span").dragDrop&&g(r,"mousedown",function(){if(-1!==n.indexOf(this))this.dragDrop();else{for(var e=this.parentElement;-1===n.indexOf(e);)e=e.parentElement;e.dragDrop()}}))},P=function(e){var t=d(e,"opts"),n=u(e.children,t.items),r=f(n,t.handle);d(e,"_disabled","false"),c(n),l(r,"mousedown"),l(e,"dragover"),l(e,"dragenter"),l(e,"drop")};function j(e,c){var f=String(c);return c=c||{},"string"==typeof e&&(e=document.querySelectorAll(e)),e instanceof HTMLElement&&(e=[e]),e=Array.prototype.slice.call(e),/serialize/.test(f)?e.map(function(e){var t=d(e,"opts");return function(t,n,e){if(void 0===n&&(n=function(e,t){return e}),void 0===e&&(e=function(e){return e}),!(t instanceof HTMLElement)||1==!t.isSortable)throw new Error("You need to provide a sortableContainer to be serialized.");if("function"!=typeof n||"function"!=typeof e)throw new Error("You need to provide a valid serializer for items and the container.");var r=d(t,"opts").items,o=u(t.children,r),i=o.map(function(e){return{parent:t,node:e,html:e.outerHTML,index:y(e,o)}});return{container:e({node:t,itemCount:i.length}),items:i.map(function(e){return n(e,t)})}}(e,t.itemSerializer,t.containerSerializer)}):(e.forEach(function(s){if(/enable|disable|destroy/.test(f))return j[f](s);["connectWith","disableIEFix"].forEach(function(e){c.hasOwnProperty(e)&&null!==c[e]&&console.warn('HTML5Sortable: You are using the deprecated configuration "'+e+'". This will be removed in an upcoming version, make sure to migrate to the new options when updating.')}),c=Object.assign({},Y,m(s).config,c),m(s).config=c,d(s,"opts",c),s.isSortable=!0,P(s);var e,t=u(s.children,c.items);if(null!==c.placeholder&&void 0!==c.placeholder){var n=document.createElement(s.tagName);n.innerHTML=c.placeholder,e=n.children[0]}m(s).placeholder=function(e,t,n){if(void 0===n&&(n="sortable-placeholder"),!(e instanceof HTMLElement))throw new Error("You must provide a valid element as a sortable.");if(!(t instanceof HTMLElement)&&void 0!==t)throw new Error("You must provide a valid element as a placeholder or set ot to undefined.");return void 0===t&&(["UL","OL"].includes(e.tagName)?t=document.createElement("li"):["TABLE","TBODY"].includes(e.tagName)?(t=document.createElement("tr")).innerHTML='<td colspan="100"></td>':t=document.createElement("div")),"string"==typeof n&&(r=t.classList).add.apply(r,n.split(" ")),t;var r}(s,e,c.placeholderClass),d(s,"items",c.items),c.acceptFrom?d(s,"acceptFrom",c.acceptFrom):c.connectWith&&d(s,"connectWith",c.connectWith),N(s),h(t,"role","option"),h(t,"aria-grabbed","false"),O(s,!0),g(s,"dragstart",function(e){if(!0!==e.target.isSortable&&(e.stopImmediatePropagation(),(!c.handle||e.target.matches(c.handle))&&"false"!==e.target.getAttribute("draggable"))){var t=W(e.target),n=F(t,e.target);I=u(t.children,c.items),x=I.indexOf(n),H=y(n,t.children),D=t,function(e,t,n){if(!(e instanceof Event))throw new Error("setDragImage requires a DragEvent as the first argument.");if(!(t instanceof HTMLElement))throw new Error("setDragImage requires the dragged element as the second argument.");if(n||(n=L),e.dataTransfer&&e.dataTransfer.setDragImage){var r=n(t,v(t),e);if(!(r.element instanceof HTMLElement)||"number"!=typeof r.posX||"number"!=typeof r.posY)throw new Error("The customDragImage function you provided must return and object with the properties element[string], posX[integer], posY[integer].");e.dataTransfer.effectAllowed="copyMove",e.dataTransfer.setData("text/plain",e.target.id),e.dataTransfer.setDragImage(r.element,r.posX,r.posY)}}(e,n,c.customDragImage),A=T(n),n.classList.add(c.draggingClass),h(M=_(n,t),"aria-grabbed","true"),t.dispatchEvent(new CustomEvent("sortstart",{detail:{origin:{elementIndex:H,index:x,container:D},item:M}}))}}),g(s,"dragenter",function(e){if(!0!==e.target.isSortable){var t=W(e.target);S=u(t.children,d(t,"items")).filter(function(e){return e!==m(s).placeholder})}}),g(s,"dragend",function(e){if(M){M.classList.remove(c.draggingClass),h(M,"aria-grabbed","false"),"true"===M.getAttribute("aria-copied")&&"true"!==d(M,"dropped")&&M.remove(),M.style.display=M.oldDisplay,delete M.oldDisplay;var t=Array.from(p.values()).map(function(e){return e.placeholder}).filter(function(e){return e instanceof HTMLElement}).filter(E)[0];t&&t.remove(),s.dispatchEvent(new CustomEvent("sortstop",{detail:{origin:{elementIndex:H,index:x,container:D},item:M}})),A=M=null}}),g(s,"drop",function(e){if(C(s,M.parentElement)){e.preventDefault(),e.stopPropagation(),d(M,"dropped","true");var t=Array.from(p.values()).map(function(e){return e.placeholder}).filter(function(e){return e instanceof HTMLElement}).filter(E)[0];b(t,M),t.remove(),s.dispatchEvent(new CustomEvent("sortstop",{detail:{origin:{elementIndex:H,index:x,container:D},item:M}}));var n=m(s).placeholder,r=u(D.children,c.items).filter(function(e){return e!==n}),o=!0===this.isSortable?this:this.parentElement,i=u(o.children,d(o,"items")).filter(function(e){return e!==n}),a=y(M,Array.from(M.parentElement.children).filter(function(e){return e!==n})),l=y(M,i);H===a&&D===o||s.dispatchEvent(new CustomEvent("sortupdate",{detail:{origin:{elementIndex:H,index:x,container:D,itemsBeforeUpdate:I,items:r},destination:{index:l,elementIndex:a,container:o,itemsBeforeUpdate:S,items:i},item:M}}))}});var r,o,i,a=(r=function(t,e,n){if(M)if(c.forcePlaceholderSize&&(m(t).placeholder.style.height=A+"px"),-1<Array.from(t.children).indexOf(e)){var r=T(e),o=y(m(t).placeholder,e.parentElement.children),i=y(e,e.parentElement.children);if(A<r){var a=r-A,l=v(e).top;if(o<i&&n<l)return;if(i<o&&l+r-a<n)return}void 0===M.oldDisplay&&(M.oldDisplay=M.style.display),"none"!==M.style.display&&(M.style.display="none");var s=!1;try{s=v(e).top+e.offsetHeight/2<=n}catch(e){s=o<i}s?b(e,m(t).placeholder):w(e,m(t).placeholder),Array.from(p.values()).filter(function(e){return void 0!==e.placeholder}).forEach(function(e){e.placeholder!==m(t).placeholder&&e.placeholder.remove()})}else{var f=Array.from(p.values()).filter(function(e){return void 0!==e.placeholder}).map(function(e){return e.placeholder});-1!==f.indexOf(e)||t!==e||u(e.children,c.items).length||(f.forEach(function(e){return e.remove()}),e.appendChild(m(t).placeholder))}},void 0===(o=c.debounce)&&(o=0),function(){for(var e=[],t=0;t<arguments.length;t++)e[t-0]=arguments[t];clearTimeout(i),i=setTimeout(function(){r.apply(void 0,e)},o)}),l=function(e){var t=e.target,n=!0===t.isSortable?t:W(t);if(t=F(n,t),M&&C(n,M.parentElement)&&"true"!==d(n,"_disabled")){var r=d(n,"opts");parseInt(r.maxItems)&&u(n.children,d(n,"items")).length>=parseInt(r.maxItems)&&M.parentElement!==n||(e.preventDefault(),e.stopPropagation(),e.dataTransfer.dropEffect=!0===m(n).getConfig("copy")?"copy":"move",a(n,t,e.pageY))}};g(t.concat(s),"dragover",l),g(t.concat(s),"dragenter",l)}),e)}return j.destroy=function(e){r(e)},j.enable=function(e){N(e)},j.disable=function(e){var t,n,r;n=d(t=e,"opts"),r=f(u(t.children,n.items),n.handle),h(t,"aria-dropeffect","none"),d(t,"_disabled","true"),h(r,"draggable","false"),l(r,"mousedown")},j}();
            //# sourceMappingURL=html5sortable.min.js.map
            sortable("#quicklink-sort")[0].addEventListener('sortupdate', function(e) {
                var orderid = 1;
                $.each(e.detail.destination.items, function(index, value) {
                    $("#quicklink-sort").css({display: "block"});
                    $.get("../blocks/mmquicklink/setorder.php", { button: value.dataset.button, order: orderid } ).done(function(data) {
                        $("#quicklink-sort-alert").attr("style", "display: block !important;");
                    });
                    orderid = orderid + 1;
                });
            });
        }
    }
});