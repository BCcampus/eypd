System.register([],function(){'use strict';return{setters:[],execute:function(){function a(a){const b=document.createElement('div');return b.innerHTML=a,Array.from(b.children)}Promise.resolve();const b=function*(){const b=[...Array.from(document.querySelectorAll('noscript.lazyload')).map((b)=>a(b.innerText).map((a)=>({elem:a,parent:b.parentNode}))),...Array.from(document.querySelectorAll('template.lazyload')).map((a)=>Array.from(a.content.cloneNode(!0).children).map((b)=>({elem:b,parent:a.parentNode})).map((a)=>{return'SCRIPT'===a.elem.tagName?(a.elem=Object.assign(a.elem.cloneNode(!0),{async:!1}),a):a}))];for(const a of b)yield*a}();requestIdleCallback(function a(){const{value:c,done:d}=b.next();d||(c.parent.appendChild(c.elem),requestIdleCallback(a))})}}});