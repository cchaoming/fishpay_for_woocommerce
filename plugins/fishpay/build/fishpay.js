(()=>{"use strict";var e,t,n,a,r,c,o={816:(e,t,n)=>{let a;function r(e){if("string"!=typeof e||-1===e.indexOf("&"))return e;void 0===a&&(a=document.implementation&&document.implementation.createHTMLDocument?document.implementation.createHTMLDocument("").createElement("textarea"):document.createElement("textarea")),a.innerHTML=e;const t=a.textContent;return a.innerHTML="",t}n.d(t,{S:()=>r})}},i={};function l(e){var t=i[e];if(void 0!==t)return t.exports;var n=i[e]={exports:{}};return o[e](n,n.exports,l),n.exports}l.d=(e,t)=>{for(var n in t)l.o(t,n)&&!l.o(e,n)&&Object.defineProperty(e,n,{enumerable:!0,get:t[n]})},l.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),e=l(816),t=window.wc.wcBlocksRegistry.registerPaymentMethod,n=(0,window.wc.wcSettings.getSetting)("fishpay_wx_gateway_data",{}),a=(0,e.S)(n.title),r=function(){return(0,e.S)(n.description||"")},c=function(e){var t=e.components.PaymentMethodLabel;return React.createElement(t,{text:a})},t({name:"fishpay_wx_gateway",label:React.createElement(c,null),content:React.createElement(r,null),edit:React.createElement(r,null),canMakePayment:function(){return!0},ariaLabel:a,supports:{features:n.supports}}),(()=>{var e=l(816),t=window.wc.wcBlocksRegistry.registerPaymentMethod,n=(0,window.wc.wcSettings.getSetting)("fishpay_alipay_gateway_data",{}),a=(0,e.S)(n.title),r=function(){return(0,e.S)(n.description||"")},c=function(e){var t=e.components.PaymentMethodLabel;return React.createElement(t,{text:a})};t({name:"fishpay_alipay_gateway",label:React.createElement(c,null),content:React.createElement(r,null),edit:React.createElement(r,null),canMakePayment:function(){return!0},ariaLabel:a,supports:{features:n.supports}})})()})();