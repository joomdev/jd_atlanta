var kmt_domready=false;if(typeof window.addEvent!="undefined"){window.addEvent("domready",function(){kmt_domready=true;});}window.onload=function(){if(typeof MooTools=="undefined"){alert("Kunena: MooTools JavaScript library is not loaded!");return;}var a=MooTools.version.split(".");if(a[0]==1&&a[1]>=10){alert("Kunena: Deprecated MooTools "+MooTools.version+" JavaScript library loaded!");return;}if(typeof window.addEvent=="undefined"){alert("Kunena: MooTools window.addEvent() is not a function!");return;}if(kmt_domready!=true){alert("Kunena: MooTools domready event was never fired!");}};