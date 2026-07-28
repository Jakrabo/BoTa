(function(){
  'use strict';

  const configured=document.documentElement.getAttribute('data-bota-theme')||'auto';
  const media=window.matchMedia('(prefers-color-scheme: dark)');

  function apply(){
    const resolved=configured==='auto'?(media.matches?'dark':'light'):configured;
    document.documentElement.setAttribute('data-bota-theme-resolved',resolved);
    document.documentElement.style.colorScheme=resolved;
    if(document.body){
      document.body.classList.toggle('bota-theme-dark',resolved==='dark');
      document.body.classList.toggle('bota-theme-light',resolved==='light');
    }
  }

  apply();
  if(document.readyState==='loading'){
    document.addEventListener('DOMContentLoaded',apply,{once:true});
  }
  if(configured==='auto'){
    media.addEventListener?.('change',apply);
  }
})();
