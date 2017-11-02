System.register(["./intersectionobserver-polyfill.js"],function(){"use strict";return{setters:[function(){}],execute:function(){const a=new IntersectionObserver((a)=>{a.forEach((a)=>{a.isIntersecting&&(a.target.full=!0)})},{rootMargin:"50px",threshold:0}),b=document.createElement("style");b.innerHTML=`
@keyframes pwp-lazy-image-fade-in {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}`,document.head.appendChild(b);class c extends HTMLElement{static get observedAttributes(){return["full","src"]}constructor(){super()}connectedCallback(){a.observe(this),new URL(location.href).searchParams.has("loadimages")&&(this.full=!0)}disconnectedCallback(){a.unobserve(this)}attributeChangedCallback(){if(!this.full)return;const a=document.createElement("img");a.src=this.src,a.onload=()=>{a.style.animationName="pwp-lazy-image-fade-in",a.style.animationDuration="0.5s",a.style.animationIterationCount=1,a.style.position="absolute",a.style.top=0,a.style.left=0,a.style.width="100%",a.style.height="100%",this.appendChild(a)}}get full(){return this.hasAttribute("full")}set full(a){a=!!a,a?this.setAttribute("full",""):this.removeAttribute("full")}get src(){return this.getAttribute("src")}set src(a){this.setAttribute("src",a)}get width(){return this.hasAttribute("width")?this.getAttribute("width"):1}get height(){return this.hasAttribute("height")?this.getAttribute("height"):1}}customElements.define("pwp-lazy-image",c)}}});