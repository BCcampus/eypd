System.register([],function(){'use strict';function a(){return'caches'in window}function b(){document.body.classList.remove('offline'),document.body.classList.add('online'),d()}function c(){document.body.classList.add('offline'),document.body.classList.remove('online'),d()}function d(){a()&&(Array.from(document.querySelectorAll('article.preview')).forEach((a)=>{const b=new URL(a.querySelector('a.headline').href);b.searchParams.set('fragment','true'),caches.match(b.toString()).then((b)=>{const c=!!b;a.classList.toggle('cached',c),c&&(a.querySelector('.download').innerText='Available for offline reading')})}),Array.from(document.querySelectorAll('.download')).forEach((a)=>{a.addEventListener('click',(a)=>{let b=a.target;for(;b&&'ARTICLE'!==b.nodeName;)b=b.parentNode;if(!b)throw new Error('Invalid download button?!');b.classList.contains('cached')||e(b)})}))}async function e(a){a.classList.add('downloading');const b=new URL(a.querySelector('header a').href);b.searchParams.append('loadimages','true');const c=document.createElement('iframe');c.src=b.toString(),c.style.display='none',document.body.appendChild(c),await new Promise((a)=>{c.addEventListener('load',()=>{setTimeout(a,2e3)})}),document.body.removeChild(c),a.classList.remove('downloading'),a.classList.add('cached')}return{setters:[],execute:function(){_pubsubhub.subscribe('navigation',()=>d()),window.addEventListener('offline',()=>c()),window.addEventListener('online',()=>b()),navigator.onLine?b():c(),a()||document.body.classList.add('nocache')}}});