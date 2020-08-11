function enterFullscreen(){setTimeout(function(){window.dispatchEvent(new Event("resize"))},220),$("body").addClass("fullscreen"),$("a.fullscreen").addClass("active"),Cookies.set("tx-dlf-pageview-zoomFullscreen","true")}function exitFullscreen(){setTimeout(function(){window.dispatchEvent(new Event("resize"))},220),$("body").removeClass("fullscreen"),$("a.fullscreen").removeClass("active"),Cookies.remove("tx-dlf-pageview-zoomFullscreen")}function resizeVideoCanvas(){var a,b,c;a=$(".media-viewport"),b=$(".mediaplayer-container"),c=$("video"),c.css({width:"100%",height:"auto"}),b.height()>a.height()&&c.css({width:"80%",height:"auto"})}function bindPlayerFunctions(){$(".button-settings").bind("click",function(){toggleSettingsMenu()}),$(".button-nextframe").bind("click",function(){frameForward()}),$(".button-lastframe").bind("click",function(){frameBackward()}),$(".button-backward").bind("click",function(){backward()}),$(".button-forward").bind("click",function(){forward()}),bindSettingsMenuItems(),bindSpeedSettings(),viewport.bind($.jPlayer.event.timeupdate,function(a){$(".time-current").text(getFormattedVideoCurrentTime()),$(".frame-current").text(video.get()),$(".time-remaining").text($.jPlayer.convertTime(a.jPlayer.status.duration-a.jPlayer.status.currentTime))}),viewport.bind($.jPlayer.event.canplay,function(a){generateChapters(),$(".time-current").text(getFormattedVideoCurrentTime()),$(".frame-current").text(video.get()),$(".time-remaining").text($.jPlayer.convertTime(a.jPlayer.status.duration-a.jPlayer.status.currentTime))}),viewport.bind($.jPlayer.event.loadeddata,function(a){getParams(document.URL).timecode&&viewport.jPlayer("pause",parseFloat(getParams(document.URL).timecode)),resizeVideoCanvas()})}function bindSettingsMenuItems(){$("#mediaplayer-viewport").contextmenu(function(a){a.preventDefault(),toggleSettingsMenu()}),$(".menu-item-back").bind("click",function(){$(".viewport-menu").children().hide(),$(".settings-menu").show("fast")}),$(".settings-menu-item-speed-menu").bind("click",function(){$(".settings-menu").hide(),$(".speed-menu").show("fast")}),$(".settings-menu-item-quality-menu").bind("click",function(){$(".settings-menu").hide(),$(".quality-menu").show("fast")}),$(".settings-menu-item-subtitle").bind("click",function(){$(".settings-menu").hide(),$(".subtitle-menu").show("fast")}),$(".settings-menu-item-language").bind("click",function(){$(".settings-menu").hide(),$(".language-menu").show("fast")}),$(".settings-menu-item-help").bind("click",function(){$(".viewport-menu").hide(),$(".dfgplayer-help").show("fast")}),$(".settings-menu-item-screenshot").bind("click",function(){renderScreenshot()}),$(".settings-menu-item-url").click(function(a){$(".viewport-menu").hide(),generateUrl()}),$(".modal-close").bind("click",function(){$(".dfgplayer-help").hide("fast")})}function bindSpeedSettings(){$(".speed-menu").children().each(function(){$(this).data("speed")&&$(this).bind("click",function(){viewport.jPlayer("option","playbackRate",$(this).data("speed")),$(".speed-label").text($(this).data("speed")+"x"),$(".viewport-menu").children().hide(),$(".settings-menu").show()})})}function bindKeyboardEvents(){$(document).keydown(function(a){switch(a.keyCode){case 13:a.altKey&&viewport.data("jPlayer").options.fullScreen?viewport.jPlayer("option","fullScreen",!1):viewport.jPlayer("option","fullScreen",!0);break;case 32:viewport.data("jPlayer").status.paused?viewport.jPlayer("play"):viewport.jPlayer("pause");break;case 37:!0===a.shiftKey?backward():frameBackward();break;case 39:!0===a.shiftKey?forward():frameForward();break;case 72:toggleHelp();break;case 77:viewport.data("jPlayer").options.muted?viewport.jPlayer("option","muted",!1):viewport.jPlayer("option","muted",!0);break;case 112:a.preventDefault(),toggleHelp();break;case 187:toggleVolumeBar(),viewport.jPlayer("option","volume",viewport.data("jPlayer").options.volume+.1);break;case 189:toggleVolumeBar(),viewport.jPlayer("option","volume",viewport.data("jPlayer").options.volume-.1)}})}function initializePlayer(){viewport.jPlayer({ready:function(){$(this).jPlayer("setMedia",{m4v:demoMovieFile})},backgroundColor:"#000000",supplied:"m4v",swfPath:"/typo3conf/ext/dlf/Resources/Public/Javascript/jPlayer/jquery.jplayer.swf",size:{width:"100%",height:"auto"},cssSelectorAncestor:".media-viewport",cssSelector:{videoPlay:".button-play",play:".button-play",pause:".button-pause",stop:".button-stop",seekBar:".jp-seek-bar",playBar:".jp-play-bar",mute:".button-mute",unmute:".button-unmute",volumeBar:".jp-volume-bar",volumeBarValue:".jp-volume-bar-value",volumeMax:".jp-volume-max",playbackRateBar:".jp-playback-rate-bar",playbackRateBarValue:".jp-playback-rate-bar-value",currentTime:".jp-current-time",duration:".jp-duration",title:".jp-title",fullScreen:".button-fullscreen",restoreScreen:".button-minimize",repeat:".jp-repeat",repeatOff:".jp-repeat-off",gui:".control-bars",noSolution:".jp-no-solution"}}),viewport.jPlayer("load"),video=VideoFrame({id:"jp_video_0",frameRate:fps,callback:function(a){console.log("callback response: "+a)}})}function generateChapters(){var a=getMediaLength(),b=$(".jp-seek-bar");$(".chapter").each(function(){var c=$(this).data("timecode");$(this).data("title");$("<span />",{class:"jp-chapter-marker",title:$(this).data("title"),style:"position: absolute; left: "+100*(c-.5)/a+"%",click:function(){play(c)}}).appendTo(b)})}function generateUrl(){var a=document.URL,b=$("#url-field"),c=$("#url-container");a=getParams(a)?a+"&timecode="+viewport.data("jPlayer").status.currentTime:a+"?timecode="+viewport.data("jPlayer").status.currentTime,b.val(a),c.show("fast")}function toggleSettingsMenu(){var a=$(".viewport-menu");a.children().hide(),$(".settings-menu").show(),a.toggle("fast")}function getMediaLength(){return viewport.data("jPlayer").status.duration}function frameForward(){viewport.data("jPlayer").status.currentTime<viewport.data("jPlayer").status.duration&&video.seekForward(1)}function frameBackward(){viewport.data("jPlayer").status.currentTime>0&&video.seekBackward(1)}function forward(){viewport.data("jPlayer").status.currentTime+10<viewport.data("jPlayer").status.duration&&viewport.jPlayer("play",viewport.data("jPlayer").status.currentTime+10)}function backward(){viewport.data("jPlayer").status.currentTime-10>0&&viewport.jPlayer("play",viewport.data("jPlayer").status.currentTime-10)}function play(a){viewport.jPlayer("play",a)}function toggleHelp(){var a=$(".dfgplayer-help");"none"===a.css("display")?a.show("fast"):a.hide("fast")}function toggleVolumeBar(){var a=$(".jp-volume-bar");a.css({visibility:"visible",opacity:1}),setTimeout(function(){a.css({visibility:"hidden",opacity:0})},3e3)}function renderScreenshot(){toggleSettingsMenu();var a=$("<div id='screenshot-overlay'><span class='close-screenshot-modal icon-close'></span><canvas id='screenshot-canvas'></canvas></div>");$("body").append(a),$(".close-screenshot-modal").bind("click",function(){$("#screenshot-overlay").detach()}),drawCanvas()}function drawCanvas(){var a,b,c,d;a=document.getElementById("jp_video_0"),b=document.getElementById("screenshot-canvas"),d="© "+copyright+" / SLUB "+signature+" / "+getFormattedVideoCurrentTime(),b.width=a.videoWidth,b.height=a.videoHeight,c=b.getContext("2d"),c.drawImage(a,0,0,b.width,b.height),c.font="25px Arial",c.textAlign="end",c.fillStyle="#FFFFFF",c.shadowBlur=5,c.shadowColor="black",c.fillText(d,b.width-10,b.height-10),b.style.width="80%",b.style.height="auto"}function getFormattedVideoCurrentTime(){var a=viewport.data("jPlayer").status;return(a.currentTime<3600?"00:":"")+$.jPlayer.convertTime(a.currentTime)+":"+("0"+video.get()%fps).slice(-2)}!function(a,b,c){function d(a,b){return typeof a===b}function e(a){return a.replace(/([a-z])-([a-z])/g,function(a,b,c){return b+c.toUpperCase()}).replace(/^-/,"")}function f(a,b){return!!~(""+a).indexOf(b)}function g(){return"function"!=typeof b.createElement?b.createElement(arguments[0]):x?b.createElementNS.call(b,"http://www.w3.org/2000/svg",arguments[0]):b.createElement.apply(b,arguments)}function h(){var a=b.body;return a||(a=g(x?"svg":"body"),a.fake=!0),a}function i(a,c,d,e){var f,i,j,k,l="modernizr",m=g("div"),n=h();if(parseInt(d,10))for(;d--;)j=g("div"),j.id=e?e[d]:l+(d+1),m.appendChild(j);return f=g("style"),f.type="text/css",f.id="s"+l,(n.fake?n:m).appendChild(f),n.appendChild(m),f.styleSheet?f.styleSheet.cssText=a:f.appendChild(b.createTextNode(a)),m.id=l,n.fake&&(n.style.background="",n.style.overflow="hidden",k=w.style.overflow,w.style.overflow="hidden",w.appendChild(n)),i=c(m,a),n.fake?(n.parentNode.removeChild(n),w.style.overflow=k,w.offsetHeight):m.parentNode.removeChild(m),!!i}function j(a,b){return function(){return a.apply(b,arguments)}}function k(a,b,c){var e;for(var f in a)if(a[f]in b)return!1===c?a[f]:(e=b[a[f]],d(e,"function")?j(e,c||b):e);return!1}function l(a){return a.replace(/([A-Z])/g,function(a,b){return"-"+b.toLowerCase()}).replace(/^ms-/,"-ms-")}function m(b,c,d){var e;if("getComputedStyle"in a){e=getComputedStyle.call(a,b,c);var f=a.console;if(null!==e)d&&(e=e.getPropertyValue(d));else if(f){var g=f.error?"error":"log";f[g].call(f,"getComputedStyle returning null, its possible modernizr test results are inaccurate")}}else e=!c&&b.currentStyle&&b.currentStyle[d];return e}function n(b,d){var e=b.length;if("CSS"in a&&"supports"in a.CSS){for(;e--;)if(a.CSS.supports(l(b[e]),d))return!0;return!1}if("CSSSupportsRule"in a){for(var f=[];e--;)f.push("("+l(b[e])+":"+d+")");return f=f.join(" or "),i("@supports ("+f+") { #modernizr { position: absolute; } }",function(a){return"absolute"==m(a,null,"position")})}return c}function o(a,b,h,i){function j(){l&&(delete G.style,delete G.modElem)}if(i=!d(i,"undefined")&&i,!d(h,"undefined")){var k=n(a,h);if(!d(k,"undefined"))return k}for(var l,m,o,p,q,r=["modernizr","tspan","samp"];!G.style&&r.length;)l=!0,G.modElem=g(r.shift()),G.style=G.modElem.style;for(o=a.length,m=0;o>m;m++)if(p=a[m],q=G.style[p],f(p,"-")&&(p=e(p)),G.style[p]!==c){if(i||d(h,"undefined"))return j(),"pfx"!=b||p;try{G.style[p]=h}catch(a){}if(G.style[p]!=q)return j(),"pfx"!=b||p}return j(),!1}function p(a,b,c,e,f){var g=a.charAt(0).toUpperCase()+a.slice(1),h=(a+" "+C.join(g+" ")+g).split(" ");return d(b,"string")||d(b,"undefined")?o(h,b,e,f):(h=(a+" "+z.join(g+" ")+g).split(" "),k(h,b,c))}function q(a,b,d){return p(a,c,c,b,d)}var r=[],s=[],t={_version:"3.5.0",_config:{classPrefix:"",enableClasses:!0,enableJSClass:!0,usePrefixes:!0},_q:[],on:function(a,b){var c=this;setTimeout(function(){b(c[a])},0)},addTest:function(a,b,c){s.push({name:a,fn:b,options:c})},addAsyncTest:function(a){s.push({name:null,fn:a})}},u=function(){};u.prototype=t,u=new u;var v=t._config.usePrefixes?" -webkit- -moz- -o- -ms- ".split(" "):["",""];t._prefixes=v;var w=b.documentElement,x="svg"===w.nodeName.toLowerCase(),y="Moz O ms Webkit",z=t._config.usePrefixes?y.toLowerCase().split(" "):[];t._domPrefixes=z;var A="CSS"in a&&"supports"in a.CSS,B="supportsCSS"in a;u.addTest("supports",A||B);var C=t._config.usePrefixes?y.split(" "):[];t._cssomPrefixes=C;var D=function(b){var d,e=v.length,f=a.CSSRule;if(void 0===f)return c;if(!b)return!1;if(b=b.replace(/^@/,""),(d=b.replace(/-/g,"_").toUpperCase()+"_RULE")in f)return"@"+b;for(var g=0;e>g;g++){var h=v[g];if(h.toUpperCase()+"_"+d in f)return"@-"+h.toLowerCase()+"-"+b}return!1};t.atRule=D;var E=t.testStyles=i;u.addTest("touchevents",function(){var c;if("ontouchstart"in a||a.DocumentTouch&&b instanceof DocumentTouch)c=!0;else{var d=["@media (",v.join("touch-enabled),("),"heartz",")","{#modernizr{top:9px;position:absolute}}"].join("");E(d,function(a){c=9===a.offsetTop})}return c});var F={elem:g("modernizr")};u._q.push(function(){delete F.elem});var G={style:F.elem.style};u._q.unshift(function(){delete G.style}),t.testProp=function(a,b,d){return o([a],c,b,d)},t.testAllProps=p;var H=t.prefixed=function(a,b,c){return 0===a.indexOf("@")?D(a):(-1!=a.indexOf("-")&&(a=e(a)),b?p(a,b,c):p(a,"pfx"))};t.testAllProps=q,u.addTest("csstransforms3d",function(){var a=!!q("perspective","1px",!0),b=u._config.usePrefixes;if(a&&(!b||"webkitPerspective"in w.style)){var c;u.supports?c="@supports (perspective: 1px)":(c="@media (transform-3d)",b&&(c+=",(-webkit-transform-3d)")),c+="{#modernizr{width:7px;height:18px;margin:0;padding:0;border:0}}",E("#modernizr{width:0;height:0}"+c,function(b){a=7===b.offsetWidth&&18===b.offsetHeight})}return a}),u.addTest("csstransitions",q("transition","all",!0)),u.addTest("objectfit",!!H("objectFit"),{aliases:["object-fit"]}),function(){var a,b,c,e,f,g,h;for(var i in s)if(s.hasOwnProperty(i)){if(a=[],b=s[i],b.name&&(a.push(b.name.toLowerCase()),b.options&&b.options.aliases&&b.options.aliases.length))for(c=0;c<b.options.aliases.length;c++)a.push(b.options.aliases[c].toLowerCase());for(e=d(b.fn,"function")?b.fn():b.fn,f=0;f<a.length;f++)g=a[f],h=g.split("."),1===h.length?u[h[0]]=e:(!u[h[0]]||u[h[0]]instanceof Boolean||(u[h[0]]=new Boolean(u[h[0]])),u[h[0]][h[1]]=e),r.push((e?"":"no-")+h.join("-"))}}(),function(a){var b=w.className,c=u._config.classPrefix||"";if(x&&(b=b.baseVal),u._config.enableJSClass){var d=new RegExp("(^|\\s)"+c+"no-js(\\s|$)");b=b.replace(d,"$1"+c+"js$2")}u._config.enableClasses&&(b+=" "+c+a.join(" "+c),x?w.className.baseVal=b:w.className=b)}(r),delete t.addTest,delete t.addAsyncTest;for(var I=0;I<u._q.length;I++)u._q[I]();a.Modernizr=u}(window,document),function(a){var b=!1;if("function"==typeof define&&define.amd&&(define(a),b=!0),"object"==typeof exports&&(module.exports=a(),b=!0),!b){var c=window.Cookies,d=window.Cookies=a();d.noConflict=function(){return window.Cookies=c,d}}}(function(){function a(){for(var a=0,b={};a<arguments.length;a++){var c=arguments[a];for(var d in c)b[d]=c[d]}return b}function b(c){function d(b,e,f){var g;if("undefined"!=typeof document){if(arguments.length>1){if(f=a({path:"/"},d.defaults,f),"number"==typeof f.expires){var h=new Date;h.setMilliseconds(h.getMilliseconds()+864e5*f.expires),f.expires=h}f.expires=f.expires?f.expires.toUTCString():"";try{g=JSON.stringify(e),/^[\{\[]/.test(g)&&(e=g)}catch(a){}e=c.write?c.write(e,b):encodeURIComponent(String(e)).replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g,decodeURIComponent),b=encodeURIComponent(String(b)),b=b.replace(/%(23|24|26|2B|5E|60|7C)/g,decodeURIComponent),b=b.replace(/[\(\)]/g,escape);var i="";for(var j in f)f[j]&&(i+="; "+j,!0!==f[j]&&(i+="="+f[j]));return document.cookie=b+"="+e+i}b||(g={});for(var k=document.cookie?document.cookie.split("; "):[],l=/(%[0-9A-Z]{2})+/g,m=0;m<k.length;m++){var n=k[m].split("="),o=n.slice(1).join("=");'"'===o.charAt(0)&&(o=o.slice(1,-1));try{var p=n[0].replace(l,decodeURIComponent);if(o=c.read?c.read(o,p):c(o,p)||o.replace(l,decodeURIComponent),this.json)try{o=JSON.parse(o)}catch(a){}if(b===p){g=o;break}b||(g[p]=o)}catch(a){}}return g}}return d.set=d,d.get=function(a){return d.call(d,a)},d.getJSON=function(){return d.apply({json:!0},[].slice.call(arguments))},d.defaults={},d.remove=function(b,c){d(b,"",a(c,{expires:-1}))},d.withConverter=b,d}return b(function(){})});var getParams=function(a){var b={},c=document.createElement("a");c.href=a;var d=c.search.substring(1),e=d.split("&");if(e[0].length){for(var f=0;f<e.length;f++){var g=e[f].split("=");b[g[0]]=decodeURIComponent(g[1])}return b}return!1};$(document).ready(function(){var a=function(){var a=!1;return function(b){(/(android|ipad|playbook|silk|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(b)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(b.substr(0,4)))&&(a=!0)}(navigator.userAgent||navigator.vendor||window.opera),a}()?"touchend":"click";$(".offcanvas-toggle").on(a,function(a){$(this).parent().toggleClass("open")}),$(".document-functions li.submenu > a").on(a,function(a){return $("li.submenu.open a").not(this).parent().removeClass("open"),$(this).parent().toggleClass("open"),!1}),$("nav .nav-toggle").on(a,function(a){$(this).toggleClass("active"),$("nav .viewer-nav").toggleClass("open")}),$(".calendar-view .contains-issues").on(a,function(a){$(".calendar-view table td.open").not($(this).parent()).removeClass("open"),$(this).parent().toggleClass("open")}),$(".tx-dfgviewer-newspaper-calendar").parents("body").addClass("calendar"),$(".tx-dfgviewer-newspaper-calendar .calendar-list-selection a.select-calendar-view, .tx-dfgviewer-newspaper-calendar .calendar-view").addClass("active"),$(".tx-dfgviewer-newspaper-calendar .calendar-list-selection a").on(a,function(a){if(!$(this).hasClass("active")){var b="."+$(this).attr("class").replace("select-","");$(".tx-dfgviewer-newspaper-calendar .active").removeClass("active"),$(this).addClass("active"),$(b).addClass("active")}}),$(".provider img").each(function(){(void 0!==this.naturalWidth&&0==this.naturalWidth||"uninitialized"==this.readyState)&&$(this).parents(".document-functions").addClass("missing-provider-image")}),$(".pages select option[selected]")[0]&&$("dl.mobile-meta").append('<dt class="mobile-page-number">No.</dt><dd class="mobile-page-number">'+$(".pages select option[selected]").text()+"</dd>"),$(".provider").append('<div class="mobile-controls" />'),$(".view-functions .pages form, .view-functions .zoom a.fullscreen").clone().appendTo(".provider .mobile-controls"),shortenMobileMetaElement=$(".provider dl.mobile-meta dd.tx-dlf-title a"),shortenMobileMetaTitle=shortenMobileMetaElement.text(),shortenMobileMetaTitle.length>140&&(shortenMobileMetaTitle=shortenMobileMetaTitle.substr(0,140)+"...",shortenMobileMetaElement.text(shortenMobileMetaTitle)),$(".submenu.downloads ul li")[0]||$("#tab-downloads").replaceWith(function(){return $('<span title="'+$(this).attr("title")+'" class="'+$(this).attr("class")+'" id="'+$(this).attr("id")+'">'+$(this).html()+"</span>")}),Cookies.get("tx-dlf-pageview-zoomFullscreen")&&($("body").addClass("fullscreen static"),$("a.fullscreen").addClass("active")),$("a.fullscreen").on(a,function(){$("body.fullscreen")[0]?exitFullscreen():enterFullscreen()}),Modernizr.touchevents?($(".fwds, .backs").on("touchstart",function(){$(this).addClass("over"),triggeredElement=$(this),setTimeout(function(){triggeredElement.addClass("enable-touchevent")},250)}).on("touchend",function(){localStorage.txDlfFromPage=$(this).attr("class").split(" ")[0]}),$("body").on("touchstart",function(a){target=$(a.target),target.closest(".page-control")[0]||($(".fwds, .backs").removeClass("over enable-touchevent"),localStorage.clear())}),localStorage.txDlfFromPage&&($("."+localStorage.txDlfFromPage).addClass("no-transition over enable-touchevent"),localStorage.clear())):($(".fwds, .backs").on("mouseenter",function(){$(this).addClass("over")}).on("mouseleave",function(){$(this).removeClass("over")}).on("click",function(){localStorage.txDlfFromPage=$(this).attr("class").split(" ")[0]}),localStorage.txDlfFromPage&&($("."+localStorage.txDlfFromPage).addClass("no-transition over"),localStorage.clear())),$("body").removeClass("hidden"),setTimeout(function(){$("body").removeClass("static")},1e3)}),$(document).keyup(function(a){if(27==a.keyCode){if($("body.fullscreen")[0])return exitFullscreen();$(".document-functions .search.open")[0]&&$(".document-functions .search").removeClass("open")}if(70==a.keyCode&&!$("#tx-dfgviewer-sru-query").is(":focus"))return enterFullscreen()});var demoMovieFile="http://test.digital.slub-dresden.de:8096/emby/Videos/7/stream.webm?Static=true&api_key=7f845fc0c5f64d25bebcd817403c7b83",fps=25,viewport,copyright="Hirsch Film Filmproduktion",signature="BK 28",video;$(document).ready(function(){var a=$(".mime-type-video");a&&a.length>0&&(viewport=$("#mediaplayer-viewport"),initializePlayer(),bindPlayerFunctions(),bindKeyboardEvents(),resizeVideoCanvas())}),$(window).resize(function(){resizeVideoCanvas()});var VideoFrame=function(a){if(this===window)return new VideoFrame(a);this.obj=a||{},this.frameRate=this.obj.frameRate||24,this.video=document.getElementById(this.obj.id)||document.getElementsByTagName("video")[0]},FrameRates={film:24,NTSC:29.97,NTSC_Film:23.98,NTSC_HD:59.94,PAL:25,PAL_HD:50,web:30,high:60};VideoFrame.prototype={get:function(){return Math.floor(this.video.currentTime.toFixed(5)*this.frameRate)},listen:function(a,b){var c=this;a?this.interval=setInterval(function(){if(!c.video.paused&&!c.video.ended){var b="SMPTE"===a?c.toSMPTE():"time"===a?c.toTime():c.get();return c.obj.callback&&c.obj.callback(b,a),b}},b||1e3/c.frameRate/2):console.log("VideoFrame: Error - The listen method requires the format parameter.")},stopListen:function(){clearInterval(this.interval)},fps:FrameRates},VideoFrame.prototype.toTime=function(a){function b(a){return 10>a?"0"+a:a}var c="number"!=typeof a?this.video.currentTime:a,d=this.frameRate,e=new Date;return a="hh:mm:ss"+("number"==typeof a?":ff":""),e.setHours(0),e.setMinutes(0),e.setSeconds(0),e.setMilliseconds(1e3*c),a.replace(/hh|mm|ss|ff/g,function(a){switch(a){case"hh":return b(13>e.getHours()?e.getHours():e.getHours()-12);case"mm":return b(e.getMinutes());case"ss":return b(e.getSeconds());case"ff":return b(Math.floor(c%1*d))}})},VideoFrame.prototype.toSMPTE=function(a){if(!a)return this.toTime(this.video.currentTime);a=Number(a);var b=this.frameRate,c=60*b,d=(a/(3600*b)).toFixed(0),c=Number((a/c).toString().split(".")[0])%60,e=Number((a/b).toString().split(".")[0])%60;return(10>d?"0"+d:d)+":"+(10>c?"0"+c:c)+":"+(10>e?"0"+e:e)+":"+(10>a%b?"0"+a%b:a%b)},VideoFrame.prototype.toSeconds=function(a){return a?(a=a.split(":"),3600*Number(a[0])+60*Number(a[1])+Number(a[2])):Math.floor(this.video.currentTime)},VideoFrame.prototype.toMilliseconds=function(a){var b=a?Number(a.split(":")[3]):Number(this.toSMPTE().split(":")[3]),b=1e3/this.frameRate*(isNaN(b)?0:b);return Math.floor(1e3*this.toSeconds(a)+b)},VideoFrame.prototype.toFrames=function(a){a=a?a.split(":"):this.toSMPTE().split(":");var b=this.frameRate;return Math.floor(3600*Number(a[0])*b+60*Number(a[1])*b+Number(a[2])*b+Number(a[3]))},VideoFrame.prototype.__seek=function(a,b){this.video.paused||this.video.pause();var c=Number(this.get());this.video.currentTime=("backward"===a?c-b:c+b)/this.frameRate+1e-5},VideoFrame.prototype.seekForward=function(a,b){return a||(a=1),this.__seek("forward",Number(a)),!b||b()},VideoFrame.prototype.seekBackward=function(a,b){return a||(a=1),this.__seek("backward",Number(a)),!b||b()},VideoFrame.prototype.seekTo=function(a){a=a||{};var b,c=Object.keys(a)[0];if("SMPTE"==c||"time"==c)b=a[c],b=this.toMilliseconds(b)/1e3+.001,this.video.currentTime=b;else{switch(c){case"frame":b=this.toSMPTE(a[c]),b=this.toMilliseconds(b)/1e3+.001;break;case"seconds":b=Number(a[c]);break;case"milliseconds":b=Number(a[c])/1e3+.001}isNaN(b)||(this.video.currentTime=b)}};