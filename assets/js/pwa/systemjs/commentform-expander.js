System.register(['./transition-animator.js'],function(){'use strict';var a;return{setters:[function(b){a=b.TransitionAnimator}],execute:function(){async function b(){f=document.querySelector('.commentformexpand'),g=document.querySelector('#pendingcomments'),i=document.querySelector('.comments'),h=document.querySelector('#respond'),f.addEventListener('click',c)}async function c(){const b=f.getBoundingClientRect(),c=i.getBoundingClientRect();h.style.display='block',f.style.display='none;';const j=i.getBoundingClientRect();f.style.display='block',h.style.opacity='0';const k=c.top-j.top;g.style.transform=i.style.transform=`translateY(${k}px)`,f.style.position='relative',await e(),await e(),g.style.transition=i.style.transition=`transform ${a.TRANSITION_DURATION} ${a.TRANSITION_F}`,f.style.transition=`opacity ${a.TRANSITION_DURATION} ${a.TRANSITION_F}`,g.style.transform=i.style.transform=`translateY(${-b.height}px)`,f.style.opacity='0',await d(g),f.style.display='none',g.style.transition=i.style.transition=g.style.transform=i.style.transform='',h.style.transition=`opacity ${a.TRANSITION_DURATION} ${a.TRANSITION_F}`,h.style.opacity='1',await d(h)}function d(a){return new Promise((b)=>{a.addEventListener('transitionend',function c(d){d.target!==a||(a.removeEventListener('transitionend',c),b())})})}function e(){return new Promise((a)=>requestAnimationFrame(a))}let f,g,h,i;b().catch(()=>{}),_pubsubhub.subscribe('navigation',()=>b().catch(()=>{}))}}});