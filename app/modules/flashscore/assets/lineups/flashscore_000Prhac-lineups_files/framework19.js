if (typeof JSON !== 'object') {
    JSON = {};
}

(function () {
    'use strict';

    function f(n) {
        // Format integers to have at least two digits.
        return n < 10 ? '0' + n : n;
    }

    if (typeof Date.prototype.toJSON !== 'function') {

        Date.prototype.toJSON = function (key) {

            return isFinite(this.valueOf())
                ? this.getUTCFullYear()     + '-' +
                    f(this.getUTCMonth() + 1) + '-' +
                    f(this.getUTCDate())      + 'T' +
                    f(this.getUTCHours())     + ':' +
                    f(this.getUTCMinutes())   + ':' +
                    f(this.getUTCSeconds())   + 'Z'
                : null;
        };

        String.prototype.toJSON      =
            Number.prototype.toJSON  =
            Boolean.prototype.toJSON = function (key) {
                return this.valueOf();
            };
    }

    var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
        gap,
        indent,
        meta = {    // table of character substitutions
            '\b': '\\b',
            '\t': '\\t',
            '\n': '\\n',
            '\f': '\\f',
            '\r': '\\r',
            '"' : '\\"',
            '\\': '\\\\'
        },
        rep;


    function quote(string) {
        escapable.lastIndex = 0;
        return escapable.test(string) ? '"' + string.replace(escapable, function (a) {
            var c = meta[a];
            return typeof c === 'string'
                ? c
                : '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
        }) + '"' : '"' + string + '"';
    }


    function str(key, holder) {

        var i,          // The loop counter.
            k,          // The member key.
            v,          // The member value.
            length,
            mind = gap,
            partial,
            value = holder[key];


        if (value && typeof value === 'object' &&
                typeof value.toJSON === 'function') {
            value = value.toJSON(key);
        }


        if (typeof rep === 'function') {
            value = rep.call(holder, key, value);
        }


        switch (typeof value) {
        case 'string':
            return quote(value);

        case 'number':

            return isFinite(value) ? String(value) : 'null';

        case 'boolean':
        case 'null':


            return String(value);

        case 'object':

            if (!value) {
                return 'null';
            }

            gap += indent;
            partial = [];


            if (Object.prototype.toString.apply(value) === '[object Array]') {

                length = value.length;
                for (i = 0; i < length; i += 1) {
                    partial[i] = str(i, value) || 'null';
                }

                v = partial.length === 0
                    ? '[]'
                    : gap
                    ? '[\n' + gap + partial.join(',\n' + gap) + '\n' + mind + ']'
                    : '[' + partial.join(',') + ']';
                gap = mind;
                return v;
            }

            if (rep && typeof rep === 'object') {
                length = rep.length;
                for (i = 0; i < length; i += 1) {
                    if (typeof rep[i] === 'string') {
                        k = rep[i];
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
            } else {

                for (k in value) {
                    if (Object.prototype.hasOwnProperty.call(value, k)) {
                        v = str(k, value);
                        if (v) {
                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                        }
                    }
                }
            }

            v = partial.length === 0
                ? '{}'
                : gap
                ? '{\n' + gap + partial.join(',\n' + gap) + '\n' + mind + '}'
                : '{' + partial.join(',') + '}';
            gap = mind;
            return v;
        }
    }


    if (typeof JSON.stringify !== 'function') {
        JSON.stringify = function (value, replacer, space) {

            var i;
            gap = '';
            indent = '';

            if (typeof space === 'number') {
                for (i = 0; i < space; i += 1) {
                    indent += ' ';
                }

            } else if (typeof space === 'string') {
                indent = space;
            }

            rep = replacer;
            if (replacer && typeof replacer !== 'function' &&
                    (typeof replacer !== 'object' ||
                    typeof replacer.length !== 'number')) {
                throw new Error('JSON.stringify');
            }


            return str('', {'': value});
        };
    }


    if (typeof JSON.parse !== 'function') {
        JSON.parse = function (text, reviver) {

            var j;

            function walk(holder, key) {


                var k, v, value = holder[key];
                if (value && typeof value === 'object') {
                    for (k in value) {
                        if (Object.prototype.hasOwnProperty.call(value, k)) {
                            v = walk(value, k);
                            if (v !== undefined) {
                                value[k] = v;
                            } else {
                                delete value[k];
                            }
                        }
                    }
                }
                return reviver.call(holder, key, value);
            }



            text = String(text);
            cx.lastIndex = 0;
            if (cx.test(text)) {
                text = text.replace(cx, function (a) {
                    return '\\u' +
                        ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
                });
            }


            if (/^[\],:{}\s]*$/
                    .test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@')
                        .replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']')
                        .replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {


                j = eval('(' + text + ')');


                return typeof reviver === 'function'
                    ? walk({'': j}, '')
                    : j;
            }


            throw new SyntaxError('JSON.parse');
        };
    }
}());jQuery.uaMatch=function(ua){ua=ua.toLowerCase();var match=/(chrome)[ \/]([\w.]+)/.exec(ua)||/(webkit)[ \/]([\w.]+)/.exec(ua)||/(opera)(?:.*version|)[ \/]([\w.]+)/.exec(ua)||/(msie) ([\w.]+)/.exec(ua)||ua.indexOf("compatible")<0&&/(mozilla)(?:.*? rv:([\w.]+)|)/.exec(ua)||[];return{browser:match[1]||"",version:match[2]||"0"}};if(!jQuery.browser){matched=jQuery.uaMatch(navigator.userAgent);browser={};if(matched.browser){browser[matched.browser]=true;browser.version=matched.version}if(browser.chrome){browser.webkit=true}else if(browser.webkit){browser.safari=true}jQuery.browser=browser}(function($){var keyString="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";var uTF8Encode=function(string){string=string.replace(/\x0d\x0a/g,"\x0a");var output="";for(var n=0;n<string.length;n++){var c=string.charCodeAt(n);if(c<128){output+=String.fromCharCode(c)}else if((c>127)&&(c<2048)){output+=String.fromCharCode((c>>6)|192);output+=String.fromCharCode((c&63)|128)}else{output+=String.fromCharCode((c>>12)|224);output+=String.fromCharCode(((c>>6)&63)|128);output+=String.fromCharCode((c&63)|128)}}return output};var uTF8Decode=function(input){var string="";var i=0;var c=c1=c2=0;while(i<input.length){c=input.charCodeAt(i);if(c<128){string+=String.fromCharCode(c);i++}else if((c>191)&&(c<224)){c2=input.charCodeAt(i+1);string+=String.fromCharCode(((c&31)<<6)|(c2&63));i+=2}else{c2=input.charCodeAt(i+1);c3=input.charCodeAt(i+2);string+=String.fromCharCode(((c&15)<<12)|((c2&63)<<6)|(c3&63));i+=3}}return string};$.extend({base64Encode:function(input){var output="";var chr1,chr2,chr3,enc1,enc2,enc3,enc4;var i=0;input=uTF8Encode(input);while(i<input.length){chr1=input.charCodeAt(i++);chr2=input.charCodeAt(i++);chr3=input.charCodeAt(i++);enc1=chr1>>2;enc2=((chr1&3)<<4)|(chr2>>4);enc3=((chr2&15)<<2)|(chr3>>6);enc4=chr3&63;if(isNaN(chr2)){enc3=enc4=64}else if(isNaN(chr3)){enc4=64}output=output+keyString.charAt(enc1)+keyString.charAt(enc2)+keyString.charAt(enc3)+keyString.charAt(enc4)}return output},base64Decode:function(input){var output="";var chr1,chr2,chr3;var enc1,enc2,enc3,enc4;var i=0;input=input.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(i<input.length){enc1=keyString.indexOf(input.charAt(i++));enc2=keyString.indexOf(input.charAt(i++));enc3=keyString.indexOf(input.charAt(i++));enc4=keyString.indexOf(input.charAt(i++));chr1=(enc1<<2)|(enc2>>4);chr2=((enc2&15)<<4)|(enc3>>2);chr3=((enc3&3)<<6)|enc4;output=output+String.fromCharCode(chr1);if(enc3!=64){output=output+String.fromCharCode(chr2)}if(enc4!=64){output=output+String.fromCharCode(chr3)}}output=uTF8Decode(output);return output}})})(jQuery);/*
 * jQuery Color Animations
 * Copyright 2007 John Resig
 * Released under the MIT and GPL licenses.
 */

(function(jQuery){

    // We override the animation for all of these color styles
    jQuery.each(['backgroundColor', 'borderBottomColor', 'borderLeftColor', 'borderRightColor', 'borderTopColor', 'color', 'outlineColor'], function(i,attr){
        jQuery.fx.step[attr] = function(fx){
            if ( !fx.colorInit ) {
                fx.start = getColor( fx.elem, attr );
                fx.end = getRGB( fx.end );
                fx.colorInit = true;
            }

            fx.elem.style[attr] = "rgb(" + [
                Math.max(Math.min( parseInt((fx.pos * (fx.end[0] - fx.start[0])) + fx.start[0]), 255), 0),
                Math.max(Math.min( parseInt((fx.pos * (fx.end[1] - fx.start[1])) + fx.start[1]), 255), 0),
                Math.max(Math.min( parseInt((fx.pos * (fx.end[2] - fx.start[2])) + fx.start[2]), 255), 0)
            ].join(",") + ")";
        }
    });

    // Color Conversion functions from highlightFade
    // By Blair Mitchelmore
    // http://jquery.offput.ca/highlightFade/

    // Parse strings looking for color tuples [255,255,255]
    function getRGB(color) {
        var result;

        // Check if we're already dealing with an array of colors
        if ( color && color.constructor == Array && color.length == 3 )
            return color;

        // Look for rgb(num,num,num)
        if (result = /rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(color))
            return [parseInt(result[1]), parseInt(result[2]), parseInt(result[3])];

        // Look for rgb(num%,num%,num%)
        if (result = /rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)/.exec(color))
            return [parseFloat(result[1])*2.55, parseFloat(result[2])*2.55, parseFloat(result[3])*2.55];

        // Look for #a0b1c2
        if (result = /#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(color))
            return [parseInt(result[1],16), parseInt(result[2],16), parseInt(result[3],16)];

        // Look for #fff
        if (result = /#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(color))
            return [parseInt(result[1]+result[1],16), parseInt(result[2]+result[2],16), parseInt(result[3]+result[3],16)];

        // Look for rgba(0, 0, 0, 0) == transparent in Safari 3
        if (result = /rgba\(0, 0, 0, 0\)/.exec(color))
            return colors['transparent'];

        // Otherwise, we're most likely dealing with a named color
        return colors[jQuery.trim(color).toLowerCase()];
    }

    function getColor(elem, attr) {
        var color;

        do {
            color = jQuery.css(elem, attr);

            // Keep going until we find an element that has color, or we hit the body
            if ( color != '' && color != 'transparent' || jQuery.nodeName(elem, "body") )
                break;

            attr = "backgroundColor";
        } while ( elem = elem.parentNode );

        return getRGB(color);
    };

    // Some named colors to work with
    // From Interface by Stefan Petre
    // http://interface.eyecon.ro/

    var colors = {
        aqua:[0,255,255],
        azure:[240,255,255],
        beige:[245,245,220],
        black:[0,0,0],
        blue:[0,0,255],
        brown:[165,42,42],
        cyan:[0,255,255],
        darkblue:[0,0,139],
        darkcyan:[0,139,139],
        darkgrey:[169,169,169],
        darkgreen:[0,100,0],
        darkkhaki:[189,183,107],
        darkmagenta:[139,0,139],
        darkolivegreen:[85,107,47],
        darkorange:[255,140,0],
        darkorchid:[153,50,204],
        darkred:[139,0,0],
        darksalmon:[233,150,122],
        darkviolet:[148,0,211],
        fuchsia:[255,0,255],
        gold:[255,215,0],
        green:[0,128,0],
        indigo:[75,0,130],
        khaki:[240,230,140],
        lightblue:[173,216,230],
        lightcyan:[224,255,255],
        lightgreen:[144,238,144],
        lightgrey:[211,211,211],
        lightpink:[255,182,193],
        lightyellow:[255,255,224],
        lime:[0,255,0],
        magenta:[255,0,255],
        maroon:[128,0,0],
        navy:[0,0,128],
        olive:[128,128,0],
        orange:[255,165,0],
        pink:[255,192,203],
        purple:[128,0,128],
        violet:[128,0,128],
        red:[255,0,0],
        silver:[192,192,192],
        white:[255,255,255],
        yellow:[255,255,0],
        transparent: [255,255,255]
    };

})(jQuery);
(function($){

var TIMEOUT = 20000;
var lastTime = (new Date()).getTime();

setInterval(function() {
  var currentTime = (new Date()).getTime();
  if (currentTime > (lastTime + TIMEOUT + 2000)) {
    $(document).wake();
  }
  lastTime = currentTime;
}, TIMEOUT);

$.fn.wake = function(callback) {
  if (typeof callback === 'function') {
    return $(this).on('wake', callback);
  } else {
    return $(this).trigger('wake');
  }
};

})(jQuery);/*	SWFObject v2.2 <http://code.google.com/p/swfobject/> 
	is released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/
var swfobject=function(){var D="undefined",r="object",S="Shockwave Flash",W="ShockwaveFlash.ShockwaveFlash",q="application/x-shockwave-flash",R="SWFObjectExprInst",x="onreadystatechange",O=window,j=document,t=navigator,T=false,U=[h],o=[],N=[],I=[],l,Q,E,B,J=false,a=false,n,G,m=true,M=function(){var aa=typeof j.getElementById!=D&&typeof j.getElementsByTagName!=D&&typeof j.createElement!=D,ah=t.userAgent.toLowerCase(),Y=t.platform.toLowerCase(),ae=Y?/win/.test(Y):/win/.test(ah),ac=Y?/mac/.test(Y):/mac/.test(ah),af=/webkit/.test(ah)?parseFloat(ah.replace(/^.*webkit\/(\d+(\.\d+)?).*$/,"$1")):false,X=!+"\v1",ag=[0,0,0],ab=null;if(typeof t.plugins!=D&&typeof t.plugins[S]==r){ab=t.plugins[S].description;if(ab&&!(typeof t.mimeTypes!=D&&t.mimeTypes[q]&&!t.mimeTypes[q].enabledPlugin)){T=true;X=false;ab=ab.replace(/^.*\s+(\S+\s+\S+$)/,"$1");ag[0]=parseInt(ab.replace(/^(.*)\..*$/,"$1"),10);ag[1]=parseInt(ab.replace(/^.*\.(.*)\s.*$/,"$1"),10);ag[2]=/[a-zA-Z]/.test(ab)?parseInt(ab.replace(/^.*[a-zA-Z]+(.*)$/,"$1"),10):0}}else{if(typeof O.ActiveXObject!=D){try{var ad=new ActiveXObject(W);if(ad){ab=ad.GetVariable("$version");if(ab){X=true;ab=ab.split(" ")[1].split(",");ag=[parseInt(ab[0],10),parseInt(ab[1],10),parseInt(ab[2],10)]}}}catch(Z){}}}return{w3:aa,pv:ag,wk:af,ie:X,win:ae,mac:ac}}(),k=function(){if(!M.w3){return}if((typeof j.readyState!=D&&j.readyState=="complete")||(typeof j.readyState==D&&(j.getElementsByTagName("body")[0]||j.body))){f()}if(!J){if(typeof j.addEventListener!=D){j.addEventListener("DOMContentLoaded",f,false)}if(M.ie&&M.win){j.attachEvent(x,function(){if(j.readyState=="complete"){j.detachEvent(x,arguments.callee);f()}});if(O==top){(function(){if(J){return}try{j.documentElement.doScroll("left")}catch(X){setTimeout(arguments.callee,0);return}f()})()}}if(M.wk){(function(){if(J){return}if(!/loaded|complete/.test(j.readyState)){setTimeout(arguments.callee,0);return}f()})()}s(f)}}();function f(){if(J){return}try{var Z=j.getElementsByTagName("body")[0].appendChild(C("span"));Z.parentNode.removeChild(Z)}catch(aa){return}J=true;var X=U.length;for(var Y=0;Y<X;Y++){U[Y]()}}function K(X){if(J){X()}else{U[U.length]=X}}function s(Y){if(typeof O.addEventListener!=D){O.addEventListener("load",Y,false)}else{if(typeof j.addEventListener!=D){j.addEventListener("load",Y,false)}else{if(typeof O.attachEvent!=D){i(O,"onload",Y)}else{if(typeof O.onload=="function"){var X=O.onload;O.onload=function(){X();Y()}}else{O.onload=Y}}}}}function h(){if(T){V()}else{H()}}function V(){var X=j.getElementsByTagName("body")[0];var aa=C(r);aa.setAttribute("type",q);var Z=X.appendChild(aa);if(Z){var Y=0;(function(){if(typeof Z.GetVariable!=D){var ab=Z.GetVariable("$version");if(ab){ab=ab.split(" ")[1].split(",");M.pv=[parseInt(ab[0],10),parseInt(ab[1],10),parseInt(ab[2],10)]}}else{if(Y<10){Y++;setTimeout(arguments.callee,10);return}}X.removeChild(aa);Z=null;H()})()}else{H()}}function H(){var ag=o.length;if(ag>0){for(var af=0;af<ag;af++){var Y=o[af].id;var ab=o[af].callbackFn;var aa={success:false,id:Y};if(M.pv[0]>0){var ae=c(Y);if(ae){if(F(o[af].swfVersion)&&!(M.wk&&M.wk<312)){w(Y,true);if(ab){aa.success=true;aa.ref=z(Y);ab(aa)}}else{if(o[af].expressInstall&&A()){var ai={};ai.data=o[af].expressInstall;ai.width=ae.getAttribute("width")||"0";ai.height=ae.getAttribute("height")||"0";if(ae.getAttribute("class")){ai.styleclass=ae.getAttribute("class")}if(ae.getAttribute("align")){ai.align=ae.getAttribute("align")}var ah={};var X=ae.getElementsByTagName("param");var ac=X.length;for(var ad=0;ad<ac;ad++){if(X[ad].getAttribute("name").toLowerCase()!="movie"){ah[X[ad].getAttribute("name")]=X[ad].getAttribute("value")}}P(ai,ah,Y,ab)}else{p(ae);if(ab){ab(aa)}}}}}else{w(Y,true);if(ab){var Z=z(Y);if(Z&&typeof Z.SetVariable!=D){aa.success=true;aa.ref=Z}ab(aa)}}}}}function z(aa){var X=null;var Y=c(aa);if(Y&&Y.nodeName=="OBJECT"){if(typeof Y.SetVariable!=D){X=Y}else{var Z=Y.getElementsByTagName(r)[0];if(Z){X=Z}}}return X}function A(){return !a&&F("6.0.65")&&(M.win||M.mac)&&!(M.wk&&M.wk<312)}function P(aa,ab,X,Z){a=true;E=Z||null;B={success:false,id:X};var ae=c(X);if(ae){if(ae.nodeName=="OBJECT"){l=g(ae);Q=null}else{l=ae;Q=X}aa.id=R;if(typeof aa.width==D||(!/%$/.test(aa.width)&&parseInt(aa.width,10)<310)){aa.width="310"}if(typeof aa.height==D||(!/%$/.test(aa.height)&&parseInt(aa.height,10)<137)){aa.height="137"}j.title=j.title.slice(0,47)+" - Flash Player Installation";var ad=M.ie&&M.win?"ActiveX":"PlugIn",ac="MMredirectURL="+O.location.toString().replace(/&/g,"%26")+"&MMplayerType="+ad+"&MMdoctitle="+j.title;if(typeof ab.flashvars!=D){ab.flashvars+="&"+ac}else{ab.flashvars=ac}if(M.ie&&M.win&&ae.readyState!=4){var Y=C("div");X+="SWFObjectNew";Y.setAttribute("id",X);ae.parentNode.insertBefore(Y,ae);ae.style.display="none";(function(){if(ae.readyState==4){ae.parentNode.removeChild(ae)}else{setTimeout(arguments.callee,10)}})()}u(aa,ab,X)}}function p(Y){if(M.ie&&M.win&&Y.readyState!=4){var X=C("div");Y.parentNode.insertBefore(X,Y);X.parentNode.replaceChild(g(Y),X);Y.style.display="none";(function(){if(Y.readyState==4){Y.parentNode.removeChild(Y)}else{setTimeout(arguments.callee,10)}})()}else{Y.parentNode.replaceChild(g(Y),Y)}}function g(ab){var aa=C("div");if(M.win&&M.ie){aa.innerHTML=ab.innerHTML}else{var Y=ab.getElementsByTagName(r)[0];if(Y){var ad=Y.childNodes;if(ad){var X=ad.length;for(var Z=0;Z<X;Z++){if(!(ad[Z].nodeType==1&&ad[Z].nodeName=="PARAM")&&!(ad[Z].nodeType==8)){aa.appendChild(ad[Z].cloneNode(true))}}}}}return aa}function u(ai,ag,Y){var X,aa=c(Y);if(M.wk&&M.wk<312){return X}if(aa){if(typeof ai.id==D){ai.id=Y}if(M.ie&&M.win){var ah="";for(var ae in ai){if(ai[ae]!=Object.prototype[ae]){if(ae.toLowerCase()=="data"){ag.movie=ai[ae]}else{if(ae.toLowerCase()=="styleclass"){ah+=' class="'+ai[ae]+'"'}else{if(ae.toLowerCase()!="classid"){ah+=" "+ae+'="'+ai[ae]+'"'}}}}}var af="";for(var ad in ag){if(ag[ad]!=Object.prototype[ad]){af+='<param name="'+ad+'" value="'+ag[ad]+'" />'}}aa.outerHTML='<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"'+ah+">"+af+"</object>";N[N.length]=ai.id;X=c(ai.id)}else{var Z=C(r);Z.setAttribute("type",q);for(var ac in ai){if(ai[ac]!=Object.prototype[ac]){if(ac.toLowerCase()=="styleclass"){Z.setAttribute("class",ai[ac])}else{if(ac.toLowerCase()!="classid"){Z.setAttribute(ac,ai[ac])}}}}for(var ab in ag){if(ag[ab]!=Object.prototype[ab]&&ab.toLowerCase()!="movie"){e(Z,ab,ag[ab])}}aa.parentNode.replaceChild(Z,aa);X=Z}}return X}function e(Z,X,Y){var aa=C("param");aa.setAttribute("name",X);aa.setAttribute("value",Y);Z.appendChild(aa)}function y(Y){var X=c(Y);if(X&&X.nodeName=="OBJECT"){if(M.ie&&M.win){X.style.display="none";(function(){if(X.readyState==4){b(Y)}else{setTimeout(arguments.callee,10)}})()}else{X.parentNode.removeChild(X)}}}function b(Z){var Y=c(Z);if(Y){for(var X in Y){if(typeof Y[X]=="function"){Y[X]=null}}Y.parentNode.removeChild(Y)}}function c(Z){var X=null;try{X=j.getElementById(Z)}catch(Y){}return X}function C(X){return j.createElement(X)}function i(Z,X,Y){Z.attachEvent(X,Y);I[I.length]=[Z,X,Y]}function F(Z){var Y=M.pv,X=Z.split(".");X[0]=parseInt(X[0],10);X[1]=parseInt(X[1],10)||0;X[2]=parseInt(X[2],10)||0;return(Y[0]>X[0]||(Y[0]==X[0]&&Y[1]>X[1])||(Y[0]==X[0]&&Y[1]==X[1]&&Y[2]>=X[2]))?true:false}function v(ac,Y,ad,ab){if(M.ie&&M.mac){return}var aa=j.getElementsByTagName("head")[0];if(!aa){return}var X=(ad&&typeof ad=="string")?ad:"screen";if(ab){n=null;G=null}if(!n||G!=X){var Z=C("style");Z.setAttribute("type","text/css");Z.setAttribute("media",X);n=aa.appendChild(Z);if(M.ie&&M.win&&typeof j.styleSheets!=D&&j.styleSheets.length>0){n=j.styleSheets[j.styleSheets.length-1]}G=X}if(M.ie&&M.win){if(n&&typeof n.addRule==r){n.addRule(ac,Y)}}else{if(n&&typeof j.createTextNode!=D){n.appendChild(j.createTextNode(ac+" {"+Y+"}"))}}}function w(Z,X){if(!m){return}var Y=X?"visible":"hidden";if(J&&c(Z)){c(Z).style.visibility=Y}else{v("#"+Z,"visibility:"+Y)}}function L(Y){var Z=/[\\\"<>\.;]/;var X=Z.exec(Y)!=null;return X&&typeof encodeURIComponent!=D?encodeURIComponent(Y):Y}var d=function(){if(M.ie&&M.win){window.attachEvent("onunload",function(){var ac=I.length;for(var ab=0;ab<ac;ab++){I[ab][0].detachEvent(I[ab][1],I[ab][2])}var Z=N.length;for(var aa=0;aa<Z;aa++){y(N[aa])}for(var Y in M){M[Y]=null}M=null;for(var X in swfobject){swfobject[X]=null}swfobject=null})}}();return{registerObject:function(ab,X,aa,Z){if(M.w3&&ab&&X){var Y={};Y.id=ab;Y.swfVersion=X;Y.expressInstall=aa;Y.callbackFn=Z;o[o.length]=Y;w(ab,false)}else{if(Z){Z({success:false,id:ab})}}},getObjectById:function(X){if(M.w3){return z(X)}},embedSWF:function(ab,ah,ae,ag,Y,aa,Z,ad,af,ac){var X={success:false,id:ah};if(M.w3&&!(M.wk&&M.wk<312)&&ab&&ah&&ae&&ag&&Y){w(ah,false);K(function(){ae+="";ag+="";var aj={};if(af&&typeof af===r){for(var al in af){aj[al]=af[al]}}aj.data=ab;aj.width=ae;aj.height=ag;var am={};if(ad&&typeof ad===r){for(var ak in ad){am[ak]=ad[ak]}}if(Z&&typeof Z===r){for(var ai in Z){if(typeof am.flashvars!=D){am.flashvars+="&"+ai+"="+Z[ai]}else{am.flashvars=ai+"="+Z[ai]}}}if(F(Y)){var an=u(aj,am,ah);if(aj.id==ah){w(ah,true)}X.success=true;X.ref=an}else{if(aa&&A()){aj.data=aa;P(aj,am,ah,ac);return}else{w(ah,true)}}if(ac){ac(X)}})}else{if(ac){ac(X)}}},switchOffAutoHideShow:function(){m=false},ua:M,getFlashPlayerVersion:function(){return{major:M.pv[0],minor:M.pv[1],release:M.pv[2]}},hasFlashPlayerVersion:F,createSWF:function(Z,Y,X){if(M.w3){return u(Z,Y,X)}else{return undefined}},showExpressInstall:function(Z,aa,X,Y){if(M.w3&&A()){P(Z,aa,X,Y)}},removeSWF:function(X){if(M.w3){y(X)}},createCSS:function(aa,Z,Y,X){if(M.w3){v(aa,Z,Y,X)}},addDomLoadEvent:K,addLoadEvent:s,getQueryParamValue:function(aa){var Z=j.location.search||j.location.hash;if(Z){if(/\?/.test(Z)){Z=Z.split("?")[1]}if(aa==null){return L(Z)}var Y=Z.split("&");for(var X=0;X<Y.length;X++){if(Y[X].substring(0,Y[X].indexOf("="))==aa){return L(Y[X].substring((Y[X].indexOf("=")+1)))}}}return""},expressInstallCallback:function(){if(a){var X=c(R);if(X&&l){X.parentNode.replaceChild(l,X);if(Q){w(Q,true);if(M.ie&&M.win){l.style.display="block"}}if(E){E(B)}}a=false}}}}();/*! http://mths.be/punycode by @mathias */
;(function(y){var e,a=typeof define=='function'&&typeof define.amd=='object'&&define.amd&&define,p=typeof exports=='object'&&exports,I=typeof module=='object'&&module,A=typeof require=='function'&&require,s=2147483647,l=36,n=1,q=26,i=38,m=700,o=72,h=128,G='-',d=/[^ -~]/,v=/^xn--/,r={overflow:'Overflow: input needs wider integers to process.',ucs2decode:'UCS-2(decode): illegal sequence',ucs2encode:'UCS-2(encode): illegal value','not-basic':'Illegal input >= 0x80 (not a basic code point)','invalid-input':'Invalid input'},g=l-n,B=Math.floor,x=String.fromCharCode,H;function z(J){throw RangeError(r[J])}function E(L,J){var K=L.length;while(K--){L[K]=J(L[K])}return L}function c(J,K){var L='.';return E(J.split(L),K).join(L)}function k(M){var L=[],K=0,N=M.length,O,J;while(K<N){O=M.charCodeAt(K++);if((O&63488)==55296){J=M.charCodeAt(K++);if((O&64512)!=55296||(J&64512)!=56320){z('ucs2decode')}O=((O&1023)<<10)+(J&1023)+65536}L.push(O)}return L}function D(J){return E(J,function(L){var K='';if((L&63488)==55296){z('ucs2encode')}if(L>65535){L-=65536;K+=x(L>>>10&1023|55296);L=56320|L&1023}K+=x(L);return K}).join('')}function f(J){return J-48<10?J-22:J-65<26?J-65:J-97<26?J-97:l}function w(K,J){return K+22+75*(K<26)-((J!=0)<<5)}function b(M,K,L){var J=0;M=L?B(M/m):M>>1;M+=B(M/K);for(;M>g*q>>1;J+=l){M=B(M/g)}return B(J+(g+1)*M/(M+i))}function C(K,J){K-=(K-97<26)<<5;return K+(!J&&K-65<26)<<5}function u(W){var M=[],P=W.length,R,S=0,L=h,T=o,O,Q,U,K,X,N,V,Z,J,Y;O=W.lastIndexOf(G);if(O<0){O=0}for(Q=0;Q<O;++Q){if(W.charCodeAt(Q)>=128){z('not-basic')}M.push(W.charCodeAt(Q))}for(U=O>0?O+1:0;U<P;){for(K=S,X=1,N=l;;N+=l){if(U>=P){z('invalid-input')}V=f(W.charCodeAt(U++));if(V>=l||V>B((s-S)/X)){z('overflow')}S+=V*X;Z=N<=T?n:(N>=T+q?q:N-T);if(V<Z){break}Y=l-Z;if(X>B(s/Y)){z('overflow')}X*=Y}R=M.length+1;T=b(S-K,R,K==0);if(B(S/R)>s-L){z('overflow')}L+=B(S/R);S%=R;M.splice(S++,0,L)}return D(M)}function j(V){var M,X,S,K,T,R,N,J,Q,Z,W,L=[],P,O,Y,U;V=k(V);P=V.length;M=h;X=0;T=o;for(R=0;R<P;++R){W=V[R];if(W<128){L.push(x(W))}}S=K=L.length;if(K){L.push(G)}while(S<P){for(N=s,R=0;R<P;++R){W=V[R];if(W>=M&&W<N){N=W}}O=S+1;if(N-M>B((s-X)/O)){z('overflow')}X+=(N-M)*O;M=N;for(R=0;R<P;++R){W=V[R];if(W<M&&++X>s){z('overflow')}if(W==M){for(J=X,Q=l;;Q+=l){Z=Q<=T?n:(Q>=T+q?q:Q-T);if(J<Z){break}U=J-Z;Y=l-Z;L.push(x(w(Z+U%Y,0)));J=B(U/Y)}L.push(x(w(J,0)));T=b(X,O,S==K);X=0;++S}}++X;++M}return L.join('')}function t(J){return c(J,function(K){return v.test(K)?u(K.slice(4).toLowerCase()):K})}function F(J){return c(J,function(K){return d.test(K)?'xn--'+j(K):K})}e={version:'1.0.0',ucs2:{decode:k,encode:D},decode:u,encode:j,toASCII:F,toUnicode:t};if(p){if(I&&I.exports==p){I.exports=e}else{for(H in e){e.hasOwnProperty(H)&&(p[H]=e[H])}}}else{if(a){define('punycode',e)}else{y.punycode=e}}}(this));(function(a){(jQuery.browser=jQuery.browser||{}).mobile=/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))})(navigator.userAgent||navigator.vendor||window.opera);var PushClient={};
PushClient.a=function(){return navigator.userAgent&&navigator.userAgent.indexOf("ANTGalio")!==-1?"Opera":navigator.userAgent&&navigator.userAgent.indexOf("Chrome")!==-1&&navigator.userAgent.indexOf("WebKit")!==-1?"WebKit Chrome":navigator.userAgent&&navigator.userAgent.indexOf("Android")!==-1?"WebKit Android":navigator.userAgent&&navigator.userAgent.indexOf("iPhone")!==-1?"WebKit iPhone":navigator.userAgent&&navigator.userAgent.indexOf("WebKit")!==-1?"WebKit":navigator.userAgent&&navigator.userAgent.indexOf("MSIE")!==
-1?"IE":navigator.userAgent&&navigator.userAgent.indexOf("Gecko")!==-1?"Gecko":navigator.userAgent&&navigator.userAgent.indexOf("Opera Mobi")!==-1?"Opera Mobile":navigator.userAgent&&navigator.userAgent.indexOf("Opera Mini")!==-1?"unknown":window.opera?"Opera":"unknown"};PushClient.b=function(a){if(!document.body)throw"Error: The document doesn't have a body!";var b=true;if(this.c==="unknown"){b=false;if(a)if(this.d===null)throw"Error: Browser not supported!";else this.d(this.ERROR_UNSUPPORTED_BROWSER)}return b};
PushClient.e=function(){if(document.readyState==="complete")this.f();else if(document.addEventListener){document.addEventListener("DOMContentLoaded",this.f,false);window.addEventListener("load",this.f,false)}else if(document.attachEvent){document.attachEvent("onreadystatechange",this.f);window.attachEvent("onload",this.f);var a=false;try{a=window.frameElement==null}catch(b){}document.documentElement.doScroll&&a&&this.g()}};
PushClient.g=function(){if(!PushClient.h){try{document.documentElement.doScroll("left")}catch(a){setTimeout(PushClient.g,1);return}PushClient.f()}};PushClient.i=function(a){this.h?a():this.j.push(a)};PushClient.f=function(){if(!PushClient.h)if(document.body){PushClient.h=true;for(var a=0;a<PushClient.j.length;a++)PushClient.j[a]();PushClient.j=null}else setTimeout(PushClient.f,13)};
PushClient.k=function(a){var b=window.location.protocol,c=window.location.host,d=window.location.port;if(c.indexOf("localhost")===0)return{l:"localhost:80",m:a+"/"};if(d.length>0&&c.lastIndexOf(":")!==-1)c=c.substring(0,c.lastIndexOf(":"));var e=a.indexOf("//");if(e===-1)return null;var f=a.substring(0,e);if(b!==f)return null;a=a.substring(f.length+2);e=a.indexOf("/");if(e!==-1)a=a.substring(0,e);e=a.lastIndexOf(":");b="";if(e!==-1){b=a.substring(e+1);a=a.substring(0,e)}if(navigator.userAgent&&
navigator.userAgent.indexOf("ANTGalio")!==-1){e=80;if(f==="https:")e=443;if(b!==""&&b!==e&&d!==e)if(b!==d)return null}else if(b!==d)return null;if(a.length<4)return null;var g=-1;e=a.length-1;for(var i=c.length-1;e>=0&&i>=0;e--,i--)if(a.charAt(e)!==c.charAt(i)){g=e;break}d="";if(g===-1)if(e===-1&&i===-1){e=a.indexOf(".");d=a.substring(e+1)}else if(e===-1)if(c.charAt(i-1)===".")d=a;else{e=a.indexOf(".");if(e===-1)return null;d=a.substring(e+1)}else{if(i===-1)if(a.charAt(e-1)===".")d=c;else{e=c.indexOf(".");
if(e===-1)return null;d=c.substring(e+1)}}else{e=a.indexOf(".",g+1);if(e===-1)return null;d=a.substring(e+1)}if(d.length<4||d.indexOf(".")===-1)return null;d+=b.length>0?":"+b:"";a=f+"//"+a+(b.length>0?":"+b:"")+"/";if(this.n>=2){c=d.split(".");if(c.length>=this.n)d=c.slice(-1*this.n).join(".")}return{l:d,m:a}};PushClient.o=function(a){return this.k(a)===null};
PushClient.p=function(a,b){if(a.name)a=a.name;else{var c=a.toString();a=c.substring(c.indexOf("function")+8,c.indexOf("("));a=a.replace(/^\s+|\s+$/g,"");if(a.length===0)a="anonymous";if(a==="anonymous"&&typeof b==="object")for(var d=0;d<b.length;d++)for(var e in b[d])if(b[d].hasOwnProperty(e)&&typeof b[d][e]==="function")if(b[d][e].toString()===c)return e}return a};
PushClient.q=function(a){if(a===null||a===undefined||typeof a==="number"||typeof a==="boolean")return a;else if(typeof a==="string")return'"'+a+'"';else if(typeof a==="function")return this.p(a,this)+"()";else if(a instanceof Array)return this.r(a);var b="{",c=0;for(var d in a){if(c>0)b+=", ";if(a.hasOwnProperty(d))b+=d+":"+this.q(a[d]);c++}b+="}";return b};PushClient.s=function(a,b){for(var c=0;c<a.length;c++)if(a[c]===b)return c;return-1};
PushClient.t=function(a){for(var b=[],c=0;c<a.length;c++)b.push(a[c]);return b};PushClient.u=function(a,b){for(var c=[],d=0;d<a.length;d++)this.s(b,a[d])===-1&&c.push(a[d]);return c};PushClient.v=function(a,b){for(var c=[],d=0;d<a.length;d++)this.s(b,a[d])!==-1&&c.push(a[d]);return c};PushClient.w=function(a,b){for(var c=this.t(a),d=0;d<b.length;d++)this.s(a,b[d])===-1&&c.push(b[d]);return c};
PushClient.x=function(a,b,c,d,e){var f=this.p(arguments.callee.caller,this);if(typeof a!=="object"||typeof a.length!=="number")throw"Error: "+f+". The argument should be a list!";if(c!==null&&a.length<c)throw"Error: "+f+". The list argument should have at minimum "+c+" elements!";if(b!==null)for(var g=0;g<a.length;g++)if(typeof a[g]!==b)throw"Error: "+f+". The list argument should contain only '"+b+"' elements, the "+g+"-th element is not of type '"+b+"'!";if(typeof d==="object"&&typeof d.test===
"function")for(g=0;g<a.length;g++)if(!d.test(a[g]))throw"Error: "+f+". "+e+". The "+g+"-th element is the cause of the error!";};PushClient.r=function(a){for(var b="[",c=0;c<a.length;c++){if(c>0)b+=", ";b+=this.q(a[c])}b+="]";return b};PushClient.y=function(a,b){if(a===this.z.a0)this.a1(b);else if(a===this.z.a2)this.a3(b);else if(this.a4!==null)if(this.a4.a5===this.a6&&a===this.z.a6)this.a7(b);else this.a4.a5===this.a8&&a===this.z.a8&&this.a9(b)};
PushClient.a7=function(a){var b=a[this.z.aa];if(b!==undefined)this.ab(this.ac,this.ad.ae[b]);else{if(this.af!==this.ag){a=a[this.z.ah];if(a===undefined){PushClient.ab(this.ac,"server subscribe response is missing the session id");return}this.ai=a;a=this.af;this.af=this.ag;this.aj=0;this.ak();if(a!==this.al){this.am.an[this.ao].ap++;if(a===null||this.aq)this.ar({type:this.NOTIFY_SERVER_UP,info:""})}else this.am.an[this.ao].as++;this.aq=false}this.at()}};
PushClient.a9=function(a){a=a[this.z.aa];a!==undefined?this.ab(this.ac,this.ad.ae[a]):this.at()};
PushClient.a1=function(a){for(var b=[],c=0,d=0;d<a.length;d++){var e=a[d],f=this.z.au,g=this.z.av,i=this.z.aw,k=this.z.ax,l=this.z.ay;if(e[f]===undefined||e[g]===undefined)return;var j=false;if(e[l]!==undefined)if(e[l]==this.az)j=true;l=[];i=e[i];k=e[k];if(i!==undefined&&k!=undefined)if(i instanceof Array)for(var m=0;m<i.length;m++)l[m]={name:i[m],value:k[m]};else l[0]={name:i,value:k};f={subject:e[f],data:e[g],fields:l,isSnapshot:j};g=this.z.b0;if(e[g]!==undefined){g=((new Date).getTime()&
16777215)-e[g];if(g>-14400000)f.latency=g}if(PushClient.b1==true&&this.b2[f.subject]===undefined){g=parseInt(e[this.z.b3]);e=parseInt(e[this.z.b4]);j=this.b5[f.subject];if(j===undefined){this.b5[f.subject]={seqid:7E4,seq:0,recovery:false};j=this.b5[f.subject]}else j.seq++;if(j.seqid!==g){j.seqid=g;j.seq=e;j.recovery=false}else if(j.seq!==e)if(j.recovery==false){j.seq--;if(e<=j.seq)continue;PushClient.b6();return}else{j.recovery=false;if(e>j.seq){g={type:this.NOTIFY_DATA_LOSS,info:f.subject};
this.ar(g)}else continue;j.seq=e;g={type:this.NOTIFY_DATA_SYNC,info:f.subject};this.ar(g)}else if(j.recovery==true)j.recovery=false}b[c]=f;c++}if(c>0){this.am.an[this.ao].b7++;this.ar(b)}};PushClient.ab=function(a,b){a=a+", "+b;b=this.am.an[this.ao].ae;if(b[a]===undefined)b[a]=1;else b[a]++;this.b6()};
PushClient.a3=function(a){var b=this.z.b8,c=this.z.au;if(!(a[b]===undefined||a[c]===undefined))switch(a[b]){case "a":a={type:this.ENTITLEMENT_ALLOW,info:a[c]};this.ar(a);break;case "d":a={type:this.ENTITLEMENT_DENY,info:a[c]};this.ar(a);break}};PushClient.ar=function(a){this.b9.push(a);setTimeout(function(){var b=PushClient.b9.shift();if(b&&b instanceof Array)PushClient.ba.call(window,b);else PushClient.d!==null&&PushClient.d.call(window,b)},0)};
PushClient.bb=function(){this.am.bc=(new Date).getTime();this.am.an[this.ao].bd++;this.be!==null&&this.ak();this.a4!==null&&this.a4.a5===this.bf&&this.at();var a=(new Date).getTime();if(a-this.bg>=this.bh){this.bg=a;a={};a.a5=this.bf;this.bi(a)}};PushClient.ak=function(){this.be!==null&&clearTimeout(this.be);this.be=setTimeout(function(){PushClient.b6()},this.bj)};
PushClient.b6=function(){this.am.an[this.ao].bk++;if(this.a4!==null){this.a4.a5===this.bl&&this.at();this.at()}this.bm();if(this.af!==null)this.af=this.bn;this.bo.push(this.bp[this.ao]);this.ai=this.ao=null;this.aj++;if(this.be!==null){clearTimeout(this.be);this.be=null}if(!this.aq&&(this.aj===this.bq||this.aj===this.bp.length)){this.aq=true;var a={type:this.NOTIFY_SERVER_DOWN,info:""};PushClient.d!==null&&PushClient.d.call(window,a)}a=false;if(PushClient.b1==true){a=
true;for(var b in this.b5){var c=this.b5[b];if(c.seqid!=7E4)if(c.recovery==true)a=false;else c.recovery=true}}if(PushClient.br)if(this.bp.length>0){b={};b.a5=this.bl;this.bi(b)}PushClient.bs=false;if(this.bt.length>0){b={};b.a5=this.a6;if(a==true)b.bu=true;b.bt=this.bt;this.bi(b)}};
PushClient.bv=function(){this.bw();var a=this.bp[this.ao].m;if(PushClient.br){encoding=this.bx;transport=this.by}if("/"!==a.substring(a.length-1,a.length))a+="/";this.bz=setTimeout(function(){PushClient.bz=null;PushClient.ab(PushClient.c0,PushClient.a4.a5)},this.c1);transport.call(this,a,this.ao,null)};
PushClient.c2=function(){this.bw();var a=false,b=this.bp[this.ao].m,c=this.o(b),d=null;if(!PushClient.bs&&PushClient.c3!=="")d=PushClient.c3;if(!PushClient.br){if(!this.c4||!c)b=this.c5(b);if(!c&&!this.c6(b,this.ao))return}this.bt=this.w(this.bt,this.a4.bt);var e=c&&!this.c7?this.c8:null,f=null,g=null,i=this.c9,k=this.z,l=null,j=null;if(PushClient.br){i=this.ca;e=this.bx}else if(this.c==="IE"&&c&&PushClient.cb){i=this.cc;k=this.cd;e=this.ce}if(this.af!==
this.ag){if(!PushClient.br)if(this.c==="IE"&&!c)if(this.cf){e=this.cg;i=this.ch}else{e=this.ci;f="PushClient0.cj";g=this.ck;i=this.cl;k=this.cd}else if(this.c==="IE"&&PushClient.cb&&this.cf){a=true;e=this.cg;i=this.cm;k=this.cd}else{e=this.cn;if(this.c==="WebKit Android")e=this.co;e=c&&!this.c7?this.c8:e;i=this.cp}l=navigator.userAgent;j=this.cq}c="";for(var m=false,h=null,n=0;n<this.a4.bt.length;n++){if(n>0)g=f=null;if(!PushClient.bs)PushClient.bs=true;
if(m===false&&this.a4.bu!==undefined&&this.a4.bu===true){h=PushClient.getInfo();m=true}c+=this.cr(this.a4.bt[n],e,f,g,this.ai,k,this.a4.bu,d,a,l,j,h);h=j=l=null}if("/"!==b.substring(b.length-1,b.length))b+="/";this.bz=setTimeout(function(){PushClient.bz=null;PushClient.ab(PushClient.c0,PushClient.a4.a5)},this.c1);i.call(this,b,this.ao,c)};
PushClient.cs=function(){this.bw();var a=this.bp[this.ao].m,b=this.o(a);if(!PushClient.br){if(!this.c4||!b)a=this.c5(a);if(!b&&!this.c6(a,this.ao))return}this.bt=this.u(this.bt,this.a4.bt);if(this.af!==this.ag){this.at();for(b=0;b<this.a4.bt.length;b++)delete this.b2[this.a4.bt[b]]}else{var c=b&&!this.c7?this.c8:null,d=this.c9,e=this.z;if(PushClient.br){c=this.bx;d=this.ca}else if(this.c==="IE"&&b&&PushClient.cb&&this.cf){d=this.cc;e=this.cd;c=this.ce;if("/"!==
a.substring(a.length-1,a.length))a+="/"}var f="";for(b=0;b<this.a4.bt.length;b++){f+=this.ct(this.a4.bt[b],c,this.ai,e);delete this.b2[this.a4.bt[b]]}this.bz=setTimeout(function(){PushClient.bz=null;PushClient.ab(PushClient.c0,PushClient.a4.a5)},this.c1);d.call(this,a,this.ao,f)}};
PushClient.cu=function(){this.bw();var a=this.bp[this.ao].m,b=this.o(a);if(!PushClient.br){if(!this.c4||!b)a=this.c5(a);if(!b&&!this.c6(a,this.ao))return}if(this.af!==this.ag)this.at();else{var c=b&&!this.c7?this.c8:null,d=this.c9,e=this.z;if(PushClient.br){c=this.bx;d=this.ca}else if(this.c==="IE"&&b&&PushClient.cb&&this.cf){d=this.cc;e=this.cd;c=this.ce;if("/"!==a.substring(a.length-1,a.length))a+="/"}b=PushClient.cv(this.ai,e,c);this.bz=setTimeout(function(){PushClient.bz=
null;PushClient.ab(PushClient.c0,PushClient.a4.a5)},this.c1);d.call(this,a,this.ao,b)}};PushClient.at=function(){if(this.cw.length!==0){this.cw.shift();this.cx();this.cy(false)}};PushClient.bi=function(a){this.cw.push(a);this.cz(false)};PushClient.cy=function(a){this.cw.length!==0&&setTimeout(function(){PushClient.cz(a)},0)};
PushClient.cz=function(a){if(this.d0)if(this.b(true))if(!(!a&&(this.a4!==null||this.cw.length===0))){this.a4=this.cw[0];switch(this.a4.a5){case this.a6:this.c2();break;case this.a8:this.cs();break;case this.bf:this.cu();break;case this.bl:this.bv();break}}};PushClient.d1=function(){this.d0=true;this.cy(false)};
PushClient.cx=function(){this.a4=null;if(this.bz!==null){clearTimeout(this.bz);this.bz=null}if(this.d2!==null&&this.d2.readyState&&this.d2.readyState!==4){if(typeof XMLHttpRequest!=="undefined")this.d2.aborted=true;this.d2.abort()}this.d2!==null&&delete this.d2;this.d2=null};
PushClient.bm=function(){if(this.d3!==null){clearTimeout(this.d3);this.d3=null}if(this.d4!==null)if(this.d4.d5!=="HTML5")if(this.d4.d5==="XDR_HTML5"){var a=document.getElementById("PushClient1");a!==null&&a.contentWindow.postMessage("disconnect","*")}else if(this.d4.getElementById){a=this.d4.getElementById("d6");if(a!==null){a.src="";this.d4.body.removeChild(a);delete a}delete this.d4;this.d4=null;CollectGarbage()}else{if(PushClient.br)this.d4.close();else{if(this.d4.d7!==
undefined){clearTimeout(this.d4.d7);this.d4.d7=undefined}this.d4.readyState&&this.d4.readyState!==4&&this.d4.abort();this.d4.d5==="XDR_STREAM"&&this.d4.abort()}delete this.d4;this.d4=null}};PushClient.d8=function(){this.cx();this.bm();this.ao=null;this.bp=[];this.am.an=[];this.af=null;this.bt=[];this.b2={};this.ai=null;this.bo=[];this.aj=0;this.aq=false;this.cw=[];if(this.be!==null){clearTimeout(this.be);this.be=null}};
PushClient.d9=function(){if(this.o(this.bp[this.ao].m)&&!this.c7){if(this.d3!==null){clearTimeout(this.d3);this.d3=null}if(this.d4!==null){this.d4.responseText="";this.d4.da=0}}else{this.af=this.al;this.bm();this.ai=null;PushClient.bs=false;if(this.bt.length>0){var a={};a.a5=this.a6;if(PushClient.b1==true)a.bu=true;a.bt=this.bt;this.bi(a)}}};PushClient.bw=function(){if(this.ao===null)this.ao=this.db()};
PushClient.db=function(){var a=this.u(this.bp,this.bo);if(a.length===0){this.bo=[];a=this.bp}if(a.length===0)throw"Error: db() No available servers!";for(var b=0,c=0;c<a.length;c++)b+=a[c].dc;var d;if(b===0)d=Math.floor(a.length*Math.random());else{var e=Math.floor(b*Math.random());for(c=b=0;c<a.length;c++){b+=a[c].dc;if(b>e){d=c;break}}}return this.s(this.bp,a[d])};
PushClient.c5=function(a){var b=this.k(a);if(b===null||this.ck!==null&&b.l!==this.ck)throw"Error: Invalid common parent domain of the servers! Cause server is '"+a+"'.";if(this.ck===null){this.ck=b.l;if(b.l.indexOf(":")===-1)document.domain=b.l}return b.m};PushClient.c6=function(a,b){var c="PushClient2"+b;if(window.frames[c]===undefined||window.frames[c].dd===undefined){this.de(a,b);return false}return true};
PushClient.de=function(a,b){b="PushClient2"+b;var c=document.getElementById(b);if(!c){c=document.createElement("iframe");c.name=b;c.id=b;c.style.display="none";document.body.appendChild(c)}this.bz=setTimeout(function(){PushClient.bz=null;c.src="";c.parentNode.removeChild(c);PushClient.ab(PushClient.c0,"iframe")},this.c1);if("/"!==a.substring(a.length-1,a.length))a+="/";c.src=a+"_"+this.df(this.ck,"dd","PushClient.dg")};
PushClient.dg=function(){clearTimeout(this.bz);this.bz=null;this.cy(true)};PushClient.dh=function(a){return this.c7?new XMLHttpRequest:this.di(a)};
PushClient.di=function(a){if(a){if(this.dj)this.dj.responseText="";else this.dj={open:function(b,c){b=PushClient.dk(c);PushClient.dl.connect("0",b.host,b.port,"PushClient.dm")},setRequestHeader:function(){},send:function(b){PushClient.dl.write("0","POST / HTTP/1.1\r\nContent-Length: "+b.length+"\r\n\r\n"+b)},readyState:4,status:200,responseText:"",abort:function(){this.responseText="";PushClient.dl.close("0")}};return this.dj}if(this.dn)this.dn.responseText=
"";else this.dn={open:function(b,c){b=PushClient.dk(c);PushClient.dl.connect("1",b.host,80,"PushClient.dp")},setRequestHeader:function(){},send:function(b){PushClient.dl.write("1","POST / HTTP/1.1\r\nContent-Length: "+b.length+"\r\n\r\n"+b)},readyState:4,status:200,responseText:"",abort:function(){this.responseText="";PushClient.dl.close("1")}};return this.dn};
PushClient.dk=function(a){var b,c;b=a.indexOf("https://")==0?"https://":"http://";a=a.substring(b.length);var d=a.indexOf("/");if(d!=-1){c=a.substring(0,d);a.substring(d)}else c=a;d=c.lastIndexOf(":");if(d!=-1){a=c.substring(d+1);c=c.substring(0,d)}else a=b=="https://"?"443":"80";return{host:c,port:a}};PushClient.dp=function(a){if(this.d2){this.d2.responseText+=a;this.dq()}};PushClient.dm=function(a){if(this.d4){this.d4.responseText+=a;this.dr()}};
PushClient.ds=function(){this.b(false);var a=document.createElement("div");document.body.appendChild(a);var b=document.createElement("div");b.id="PushClient3";a.appendChild(b);setTimeout(function(){var c="flash-transport.swf";if(typeof PushClientFlashTransport==="string")c=PushClientFlashTransport;swfobject.embedSWF(c,"PushClient3","0","0","9",false,{readyCallback:"PushClient.dt"},{allowFullScreen:false,allowScriptAccess:"always"},{id:"PushClient4",
name:"PushClient4"})},0)};PushClient.dt=function(){PushClient.dl=document.getElementById("PushClient4");if(!PushClient.dl)throw"Error: Could not get the reference of the flash-transport.swf!";PushClient.d1()};
PushClient.cc=function(a,b,c){b=document.getElementById("PushClient5");if(b===null){b=document.createElement("iframe");b.id="PushClient5";b.style.display="none";document.body.appendChild(b)}var d=(new Date).getTime();b.src=a+"_"+c+d};
PushClient.cl=function(a,b,c){this.d4=new ActiveXObject("htmlfile");this.d4.open();PushClient.ck.indexOf(":")===-1?this.d4.write("<html><head><script>document.domain='"+PushClient.ck+"';<\/script></head><body></body></html>"):this.d4.write("<html><head></head><body></body></html>");this.d4.close();this.d4.parentWindow.PushClient0=this;b=this.d4.createElement("iframe");b.id="d6";this.d4.body.appendChild(b);this.d4.du=0;b.src=a+"_"+c};
PushClient.cj=function(a){PushClient.dv(a);PushClient.d4.du+=a.length;PushClient.d4.du>=PushClient.dw&&PushClient.a4===null&&PushClient.af!==PushClient.al&&PushClient.d9()};
PushClient.cm=function(a,b,c){this.d4={};this.d4.d5="XDR_HTML5";PushClient.du=0;b=document.getElementById("PushClient1");if(b===null){b=document.createElement("iframe");b.id="PushClient1";b.style.display="none";document.body.appendChild(b)}var d=(new Date).getTime();b.src=a+"_"+c+d};
PushClient.dx=function(a){if(a.data.indexOf(PushClient.z.dy)!==-1){PushClient.dv(a.data);PushClient.du+=a.data.length;PushClient.du>=PushClient.dw&&PushClient.a4===null&&PushClient.af!==PushClient.al&&PushClient.d9()}};
PushClient.ch=function(a,b,c){window.frames["PushClient2"+b].dd("window.parent.PushClient.d4 = new XDomainRequest();");this.d4.da=0;this.d4.d5="XDR_STREAM";this.d4.onload=function(){PushClient.dz()};this.d4.onprogress=function(){PushClient.dz()};this.d4.onerror=function(){};this.d4.ontimeout=function(){};try{this.d4.open("POST",a);this.d4.send(c)}catch(d){}};
PushClient.dz=function(){var a=this.d4;if(a.responseText){for(var b=a.responseText.substring(a.da),c=b.indexOf(this.z.e0);c!==-1;){b=b.substring(0,c);this.dv(b);a.da+=c+1;b=a.responseText.substring(a.da);c=b.indexOf(this.z.e0)}a.da>=this.dw&&this.a4===null&&this.af!==this.al&&this.d9()}};
PushClient.cp=function(a,b,c){var d=this.o(a);if(d)this.d4=this.dh(true);else window.frames["PushClient2"+b].dd("window.parent.PushClient.d4 = new XMLHttpRequest();");this.d4.da=0;this.d4.onreadystatechange=function(){PushClient.dr()};this.d4.open("POST",a,true);d&&this.c.indexOf("WebKit")===0&&this.d4.setRequestHeader("Content-Type","text/plain");this.d4.send(c)};
PushClient.dr=function(){var a=this.d4;if(!(a===null||a.readyState!==3&&a.readyState!==4||a.status!==200)){if(this.c.indexOf("Opera")!==-1){this.d4.d7!==undefined&&clearTimeout(this.d4.d7);this.d4.d7=setTimeout(function(){PushClient.d4.d7=undefined;PushClient.dr()},this.e1)}if(a.responseText){for(var b=a.responseText.substring(a.da),c=b.indexOf(this.z.e0);c!==-1;){b=b.substring(0,c);this.dv(b);a.da+=c+1;b=a.responseText.substring(a.da);c=b.indexOf(this.z.e0)}a.da>=this.dw&&
this.a4===null&&this.af!==this.al&&this.d9()}}};PushClient.e2=function(){if(typeof XMLHttpRequest==="undefined"){try{return new ActiveXObject("Msxml2.XMLHTTP.6.0")}catch(a){}try{return new ActiveXObject("Msxml2.XMLHTTP.3.0")}catch(b){}try{return new ActiveXObject("Msxml2.XMLHTTP")}catch(c){}throw"Error: The browser does not support XMLHttpRequest!";}else return new XMLHttpRequest};
PushClient.c9=function(a,b,c){var d=this.o(a);if(d)this.d2=this.dh(false);else window.frames["PushClient2"+b].dd("window.parent.PushClient.d2 = ("+this.e2.toString()+")();");this.d2.onreadystatechange=function(){PushClient.dq()};this.d2.open("POST",a,true);d&&this.c.indexOf("WebKit")===0&&this.d2.setRequestHeader("Content-Type","text/plain");if(this.c.indexOf("IE")===0){this.d2.setRequestHeader("Content-Type","text/plain");this.d2.setRequestHeader("Connection",
"close")}this.d2.send(c)};PushClient.dq=function(){var a=this.d2;a===null||typeof XMLHttpRequest!=="undefined"&&typeof a.aborted!=="undefined"&&a.aborted!==null&&a.aborted==true||a===null||a.readyState!==4||a.status!==200||a.responseText&&this.dv(a.responseText)};
PushClient.by=function(a){a=a.substring(0,a.indexOf("://"))==="http"?"ws://"+a.substring(a.indexOf("://")+3)+"WebSocketConnection":"wss://"+a.substring(a.indexOf("://")+3)+"WebSocketConnection-Secure";this.d4=PushClient.e3(a);this.d4.onmessage=function(b){PushClient.e4(b.data)};this.d4.onopen=function(){PushClient.at()}};PushClient.e3=function(a){if(window.WebSocket)return new WebSocket(a);else if(window.MozWebSocket)return new MozWebSocket(a);return null};
PushClient.ca=function(a,b,c){this.d4!=null&&this.d4.readyState===1&&this.d4.send(c)};PushClient.e4=function(a){var b=PushClient.d4;if(!(b===null||b.readyState!==1))if(a){b=a;for(var c=b.indexOf(PushClient.z.e0);c!==-1;){b=b.substring(0,c);PushClient.dv(b);b=a.substring(c+1);c=b.indexOf(PushClient.z.e0)}}};
PushClient.dv=function(a){for(var b=0,c=[],d="\u0000",e={},f=0;;){var g=a.indexOf(this.z.dy,b);if(g===-1)break;if(g-b>0){b=a.substring(b,g);d=b.charAt(0);e=this.e5(b);if(d===this.z.a0){c[f]=e;f++}else this.y(d,e)}b=g+1}f>0&&d!=="\u0000"&&this.y(d,c);this.bb()};
PushClient.e5=function(a){if(this.z.e6[a.charAt(0)]!==undefined){for(var b=1,c={};;){if(b>=a.length)break;var d=a.charAt(b),e=a.indexOf(this.z.e7,b+1);if(e===-1)return c;if(this.z.e8[d]!==undefined){b++;var f="";switch(this.z.e9[d]){case this.ad.ea:f=this.eb(this.z,a.substring(b,e));break;case this.ad.ec:f=PushClient.ed(this.z,a.substring(b,e));break}b=c[d];if(b===undefined)c[d]=f;else if(c[d]instanceof Array)c[d][c[d].length]=f;else{c[d]=[];c[d][0]=b;c[d][1]=f}}b=e+1}return c}};
PushClient.df=function(a,b,c){var d="";d+=this.cd.ee;d+=this.cd.ef;d+=this.eg(this.cd,a);d+=this.cd.e7;d+=this.cd.eh;d+=this.eg(this.cd,b);d+=this.cd.e7;d+=this.cd.ei;d+=this.eg(this.cd,c);d+=this.cd.e7;d+=this.cd.dy;return d};
PushClient.cr=function(a,b,c,d,e,f,g,i,k,l,j,m){var h="";h+=k?f.ej:f.a6;h+=f.au;h+=this.eg(f,a);h+=f.e7;if(b!==null){h+=f.ek;h+=this.el(f,b);h+=f.e7;if(this.c.indexOf("Opera")!==-1){h+=f.em;h+=this.el(f,1);h+=f.e7}}if(this.b2[a]!==undefined){h+=f.en;h+=this.el(f,this.b2[a]);h+=f.e7}if(c!==null){h+=f.ei;h+=this.eg(f,c);h+=f.e7}if(i!==null){h+=f.eo;h+=this.eg(f,i);h+=f.e7}if(d!==null){h+=f.ef;h+=this.eg(f,d);h+=f.e7}if(e!==null){h+=f.ah;h+=this.el(f,e);h+=f.e7}if(g!==undefined)if(this.b5[a].seqid!==
7E4){h+=f.b3;h+=this.el(f,this.b5[a].seqid);h+=f.e7;h+=f.b4;h+=this.el(f,this.b5[a].seq+1);h+=f.e7}if(l!==null){h+=f.ep;h+=this.eg(f,l);h+=f.e7}if(j!==null){h+=f.eq;h+=this.el(f,j);h+=f.e7}if(m!==null){h+=f.er;h+=this.eg(f,m);h+=f.e7}h+=f.dy;return h};
PushClient.ct=function(a,b,c,d){var e="";e+=d.a8;e+=d.au;e+=this.eg(d,a);e+=d.e7;if(this.b2[a]!==undefined){e+=d.en;e+=this.el(d,this.b2[a]);e+=d.e7}if(c!==null){e+=d.ah;e+=this.el(d,c);e+=d.e7}if(b!==null){e+=d.ek;e+=this.el(d,b);e+=d.e7}e+=d.dy;return e};PushClient.cv=function(a,b,c){var d="";d+=b.bf;d+=b.ah;d+=this.el(b,a);d+=b.e7;if(c!==null){d+=b.ek;d+=this.el(b,c);d+=b.e7}d+=b.dy;return d};PushClient.cd={};PushClient.cd.dy="!";PushClient.cd.e0="&";
PushClient.cd.e7="$";PushClient.cd.es="~";PushClient.cd.et=" ";PushClient.cd.eu='"';PushClient.cd.ev="#";PushClient.cd.ew="%";PushClient.cd.ex="'";PushClient.cd.ey="/";PushClient.cd.ez="<";PushClient.cd.f0=">";PushClient.cd.f1="[";PushClient.cd.f2="\\";PushClient.cd.f3="]";PushClient.cd.f4="^";PushClient.cd.f5="`";PushClient.cd.f6="{";
PushClient.cd.f7="|";PushClient.cd.f8="}";PushClient.cd.f9="";PushClient.cd.a6="&";PushClient.cd.a8="(";PushClient.cd.a0=")";PushClient.cd.bf="*";PushClient.cd.ee="+";PushClient.cd.fa=",";PushClient.cd.a2="0";PushClient.cd.ej="2";PushClient.cd.au="&";PushClient.cd.av="(";PushClient.cd.b4=")";PushClient.cd.b3="*";PushClient.cd.ek="+";
PushClient.cd.ah=",";PushClient.cd.ef="-";PushClient.cd.ei=".";PushClient.cd.eh="?";PushClient.cd.aa="0";PushClient.cd.fb="1";PushClient.cd.b0="2";PushClient.cd.em="3";PushClient.cd.fc="4";PushClient.cd.fd="5";PushClient.cd.eo="7";PushClient.cd.b8="8";PushClient.cd.fe="9";PushClient.cd.aw="D";PushClient.cd.ax="E";PushClient.cd.en="G";
PushClient.cd.ay="J";PushClient.cd.ep="K";PushClient.cd.eq="L";PushClient.cd.er="M";PushClient.cd.ff={};
(function(a){var b=a.ff;b["\u0000"]="y";for(var c=1;c<8;c++)b[String.fromCharCode(c)]=String.fromCharCode(c+39);b["\u0008"]="x";for(c=9;c<21;c++)b[String.fromCharCode(c)]=String.fromCharCode(c+39);b["\u0015"]="=";for(c=22;c<32;c++)b[String.fromCharCode(c)]=String.fromCharCode(c+41);b[a.dy]="I";b[a.e0]="z";b[a.e7]="J";b[a.es]="K";b[a.et]="L";b[a.eu]="M";b[a.ev]="N";b[a.ew]="O";b[a.ex]="P";b[a.ey]="_";b[a.ez]="Q";b[a.f0]="R";b[a.f1]="S";b[a.f2]="T";b[a.f3]="U";b[a.f4]="V";b[a.f5]="W";b[a.f6]="X";b[a.f7]=
"Y";b[a.f8]="Z";b[a.f9]="v"})(PushClient.cd);PushClient.cd.fg={};(function(a){for(var b in a.ff)if(a.ff.hasOwnProperty(b))a.fg[a.ff[b]]=b})(PushClient.cd);PushClient.z={};PushClient.z.dy="";PushClient.z.e0="\u0019";PushClient.z.e7="\u001e";PushClient.z.es="\u001f";PushClient.z.fh="\u0000";PushClient.z.fi="\n";PushClient.z.fj="\r";PushClient.z.eu='"';PushClient.z.f2="\\";
PushClient.z.a6="\u0001";PushClient.z.a8="\u0002";PushClient.z.a0="\u0003";PushClient.z.bf="\u0004";PushClient.z.ee="\u0005";PushClient.z.fa="\u0006";PushClient.z.a2="\t";PushClient.z.ej="\u000c";PushClient.z.au="\u0001";PushClient.z.av="\u0002";PushClient.z.b4="\u0003";PushClient.z.b3="\u0004";PushClient.z.ek="\u0005";PushClient.z.ah="\u0006";PushClient.z.ef="\u0007";
PushClient.z.ei="\u0008";PushClient.z.eh="\t";PushClient.z.aa="\u000b";PushClient.z.fb="\u000c";PushClient.z.b0="\u000e";PushClient.z.em="\u000f";PushClient.z.fc="\u0010";PushClient.z.fd="\u0011";PushClient.z.eo="\u0013";PushClient.z.b8="\u0014";PushClient.z.fe="\u0015";PushClient.z.aw="\u001b";PushClient.z.ax="\u001c";PushClient.z.en="\u001e";PushClient.z.ay="!";
PushClient.z.ep="#";PushClient.z.eq="$";PushClient.z.er="%";PushClient.z.ff={};(function(a){var b=a.ff;b[a.dy]="\u0001";b[a.e7]="\u0002";b[a.es]="\u0003";b[a.fh]="\u0004";b[a.fi]="\u0005";b[a.fj]="\u0006";b[a.eu]="\u0007";b[a.f2]="\u0008";b[PushClient.cd.dy]="\t";b[a.e0]="\u000b"})(PushClient.z);PushClient.z.fg={};(function(a){for(var b in a.ff)if(a.ff.hasOwnProperty(b))a.fg[a.ff[b]]=b})(PushClient.z);
PushClient.z.e6={};(function(a){a.e6[a.a6]=true;a.e6[a.a8]=true;a.e6[a.a0]=true;a.e6[a.bf]=true;a.e6[a.ee]=true;a.e6[a.fa]=true;a.e6[a.a2]=true;a.e6[a.ej]=true})(PushClient.z);PushClient.z.e8={};
(function(a){a.e8[a.au]=true;a.e8[a.av]=true;a.e8[a.b4]=true;a.e8[a.b3]=true;a.e8[a.ek]=true;a.e8[a.ah]=true;a.e8[a.ef]=true;a.e8[a.ei]=true;a.e8[a.eh]=true;a.e8[a.aa]=true;a.e8[a.fb]=true;a.e8[a.b0]=true;a.e8[a.em]=true;a.e8[a.fc]=true;a.e8[a.fd]=true;a.e8[a.eo]=true;a.e8[a.b8]=true;a.e8[a.fe]=true;a.e8[a.aw]=true;a.e8[a.ax]=true;a.e8[a.en]=true;a.e8[a.ay]=true;a.e8[a.ep]=true;a.e8[a.eq]=true;a.e8[a.er]=true})(PushClient.z);PushClient.ad={};PushClient.ad.fk=1;
PushClient.ad.fl=2;PushClient.ad.ec=3;PushClient.ad.ea=4;PushClient.ad.fm={};(function(a){var b=a.fm;b.au=a.ec;b.av=a.ec;b.fd=a.ec;b.b4=a.ea;b.b3=a.ea;b.ek=a.ea;b.fc=a.ec;b.ah=a.ea;b.ef=a.ec;b.ei=a.ec;b.eh=a.ec;b.aa=a.ea;b.fb=a.ec;b.b0=a.ea;b.em=a.ea;b.eo=a.ec;b.b8=a.ec;b.fe=a.ea;b.aw=a.ec;b.ax=a.ec;b.ay=a.ec;b.ep=a.ec;b.eq=a.ea;b.er=a.ec})(PushClient.ad);PushClient.z.e9={};PushClient.cd.e9={};
(function(){for(var a in PushClient.ad.fm)if(PushClient.ad.fm.hasOwnProperty(a)){PushClient.z.e9[PushClient.z[a]]=PushClient.ad.fm[a];PushClient.cd.e9[PushClient.cd[a]]=PushClient.ad.fm[a]}})();PushClient.ad.ae={};PushClient.ad.ae[0]="UNKNOWN_SESSION_ID";PushClient.eg=function(a,b){for(var c="",d=0;d<b.length;d++){var e=a.ff[b.charAt(d)];if(e!==undefined){c+=a.es;c+=e}else c+=b.charAt(d)}return c};
PushClient.ed=function(a,b){for(var c="",d=0;d<b.length;d++){var e=b.charAt(d);if(e===a.es){if(d+1>=b.length||a.fg[b.charAt(d+1)]===undefined)throw"Error: ed() Illegal argument '"+b+"'!";e=a.fg[b.charAt(d+1)];d++}c+=e}return c};
PushClient.el=function(a,b){if((b&4294967168)===0){var c=String.fromCharCode(b),d=a.ff[c];return d===undefined?c:a.es+d}var e;e=(b&4278190080)!==0?24:(b&16711680)!==0?16:8;c=[];for(d=0;d<10;d++)c.push(0);for(var f=0,g=0;e>=0;){var i=b>>e&255;g++;c[f]|=i>>g;d=a.ff[String.fromCharCode(c[f])];if(d!==undefined){c[f]=a.es.charCodeAt(0);c[f+1]=d.charCodeAt(0);f++}f++;c[f]|=i<<7-g&127;e-=8}d=a.ff[String.fromCharCode(c[f])];if(d!==undefined){c[f]=a.es.charCodeAt(0);c[f+1]=d.charCodeAt(0);f++}f++;
a="";for(d=0;d<f;d++)a+=String.fromCharCode(c[d]);return a};
PushClient.eb=function(a,b){var c="Error: eb() Illegal argument '"+b+"'!",d=0,e=-1,f=0,g,i=b.length,k=0;if(i===1)return b.charCodeAt(0);else if(i===2&&b.charAt(0)===a.es){g=a.fg[b.charAt(1)];if(g!==undefined)return g.charCodeAt(0);else throw c;}for(;i>0;i--){g=b.charAt(k);k++;if(g===a.es){if(i-1<0)throw c;i--;g=b.charAt(k);k++;g=a.fg[g];if(g===undefined)throw c;}else g=g;if(e>0){f|=g.charCodeAt(0)>>e;d=d<<8|f;f=g.charCodeAt(0)<<8-e}else f=g.charCodeAt(0)<<-e;e=(e+7)%8}return d};
PushClient.NOTIFY_SERVER_DOWN="NOTIFY_SERVER_DOWN";PushClient.NOTIFY_SERVER_UP="NOTIFY_SERVER_UP";PushClient.ERROR_UNSUPPORTED_BROWSER="ERROR_UNSUPPORTED_BROWSER";PushClient.NOTIFY_DATA_LOSS="NOTIFY_DATA_LOSS";PushClient.NOTIFY_DATA_SYNC="NOTIFY_DATA_SYNC";PushClient.ENTITLEMENT_ALLOW="ENTITLEMENT_ALLOW";PushClient.ENTITLEMENT_DENY="ENTITLEMENT_DENY";
PushClient.setServersDownBeforeNotify=function(a){if(typeof a!=="number"||a<1)throw"Error: setServersDownBeforeNotify() should have a positive number as an argument!";this.bq=a};
PushClient.setServers=function(a){this.x(a,"string",1,/^(\d+)?\s*https?:\/\/(\w|-)+(\.(\w|-)+)*(:\d+)?$/i,"The list argument should contain full URLs, optionally preceded by a priority.");for(var b=[],c=0,d=0;d<a.length;d++){var e=/https?:\/\/(\w|-)+(\.(\w|-)+)*(:\d+)?$/i.exec(a[d])[0],f=/^\d+/.exec(a[d]);if(f===null)f=100;else{f=parseInt(f[0]);if(f>100)throw"Error: connect() The priority value needs to be an integer between 0 and 100!";}this.c4||this.c5(e);b.push({m:e,dc:f});c+=f}this.bp=
b;this.am.an=[];for(d=0;d<a.length;d++){this.am.an[d]={};this.am.an[d].ap=0;this.am.an[d].bk=0;this.am.an[d].as=0;this.am.an[d].bd=0;this.am.an[d].b7=0;this.am.an[d].ae={}}if(PushClient.br){a={};a.a5=this.bl;this.bi(a)}};PushClient.getSubjects=function(){return this.t(this.bt)};PushClient.setMessageHandler=function(a){if(typeof a!=="function")throw"Error: setMessageHandler() should have a function as an argument!";this.ba=a};
PushClient.setStatusHandler=function(a){if(typeof a!=="function")throw"Error: setStatusHandler() should have a function as an argument!";this.d=a};PushClient.setCertifiedDelivery=function(a){if(this.bt.length===0){if(a==true)PushClient.b1=true}else throw"Error: setCertifiedDelivery() Unable to change the delivery type when there are running subject subscriptions!";};PushClient.subscribe=function(a){PushClient.subscribeWithConflation(a,0)};
PushClient.setNumberOfSubdomainLevels=function(a){if(typeof a!=="number"||a<2)throw"Error: setNumberOfSubdomainLevels() should have a positive number larger or equal to 2 as an argument!";if(this.bp.length>0)throw"Error: Error: setNumberOfSubdomainLevels() Unable to set the number of subdomain levels when servers are already configured - use the api call setNumberOfSubdomainLevels() before the api call setServers()!";this.n=a};
PushClient.subscribeWithConflation=function(a,b){this.x(a,"string",1,/^\/[^\/*]+\/([^\/*]+\/)*([^\/*]+|\*)$/,"The subject is invalid");if(this.bp.length===0)throw"Error: subscribe() The servers are not configured!";if(this.ba===null)throw"Error: subscribe() The message handler is not configured!";a=this.u(a,this.bt);if(a.length!==0){if(b!==undefined&&b!==null)if(b>=100){b=Math.floor(b/100)*100;for(var c=0;c<a.length;c++)this.b2[a[c]]=b}for(c=0;c<a.length;c++)this.b5[a[c]]={seqid:7E4,seq:0,
recovery:false};if(PushClient.br)this.bt=this.w(this.bt,a);b={};b.a5=this.a6;b.bt=a;this.bi(b)}};PushClient.unsubscribe=function(a){this.x(a,"string",1,/^\/[^\/*]+\/([^\/*]+\/)*([^\/*]+|\*)$/,"The subject is invalid");a=this.v(a,this.bt);if(a.length!==0){var b={};b.a5=this.a8;b.bt=a;this.bi(b)}};PushClient.disconnect=function(){this.b(false)&&this.d8()};
PushClient.setEntitlementToken=function(a){if(this.bt.length===0){this.c3=a;this.bs=false}else throw"Error: setEntitlementToken() Unable to set the entitlement information when there are running subject subscriptions!";};
PushClient.getInfo=function(){s="Date: "+(new Date).toString()+"\n";s+="Uptime: "+((new Date).getTime()-this.am.fn)+" ms\n";s+="window.location: "+window.location+"\n";s+="document.domain: "+document.domain+"\n";s+="User-agent: "+navigator.userAgent+"\n";s+="Detected browser: "+this.c+"\n";s+="Servers: ";for(var a=0;a<this.bp.length;a++){if(a>0)s+=", ";s+=this.bp[a].dc+" "+this.bp[a].m}s+="\nSubjects: "+this.bt.toString()+"\n";s+="Connection status ["+(this.ao===null?null:this.bp[this.ao].m)+
"]: "+this.af+"\n";s+="Time from last server activity: "+(this.am.bc!==null?(new Date).getTime()-this.am.bc:null)+" ms\n";s+="Servers down before notify: "+this.bq+"\n";s+="Consecutive server down count: "+this.aj+" times\n";for(a=0;a<this.am.an.length;a++){s+="\nServer up ["+this.bp[a].m+"]: "+this.am.an[a].ap+" times\n";s+="Server down ["+this.bp[a].m+"]: "+this.am.an[a].bk+" times\n";s+="Server connection recycled ["+this.bp[a].m+"]: "+this.am.an[a].as+" times\n";s+="Received server events ["+
this.bp[a].m+"]: "+this.am.an[a].bd+"\n";s+="Received messages ["+this.bp[a].m+"]: "+this.am.an[a].b7+"\n";for(var b in this.am.an[a].ae)if(this.am.an[a].ae.hasOwnProperty(b))s+="Error ["+this.bp[a].m+"] x"+this.am.an[a].ae[b]+" times : "+b+"\n"}return s};PushClient.c4=false;PushClient.c7=false;PushClient.cb=false;PushClient.bh=9E5;PushClient.bj=3E4+Math.floor(Math.random()*1E4);PushClient.c1=1E4;PushClient.e1=100;
PushClient.dw=524288;PushClient.bq=1;PushClient.n=0;PushClient.b1=false;PushClient.bs=false;PushClient.c3="";PushClient.a6="SUBSCRIBE";PushClient.a8="UNSUBSCRIBE";PushClient.bf="PING";PushClient.bl="CONNECT";PushClient.fo=0;PushClient.ci=1;PushClient.cn=2;PushClient.c8=3;PushClient.fp=4;PushClient.co=5;PushClient.fq=6;PushClient.fr=7;
PushClient.bx=8;PushClient.cg=9;PushClient.ce=10;PushClient.ag="SERVER_UP";PushClient.bn="SERVER_DOWN";PushClient.al="SERVER_RECYCLE";PushClient.az="1";PushClient.fs="2";PushClient.ft="3";PushClient.cq=1;PushClient.c0="ERROR_TIMEOUT";PushClient.fu="ERROR_HTTP";PushClient.ac="ERROR_SERVER";PushClient.c=PushClient.a();PushClient.ck=null;
PushClient.bp=[];PushClient.ba=null;PushClient.d=null;PushClient.b9=[];PushClient.d0=false;PushClient.j=[];PushClient.h=false;PushClient.ao=null;PushClient.af=null;PushClient.bt=[];PushClient.b2={};PushClient.b5={};PushClient.ai=null;PushClient.d4=null;PushClient.bg=(new Date).getTime();PushClient.be=null;PushClient.bo=[];PushClient.aj=0;
PushClient.aq=false;PushClient.cw=[];PushClient.a4=null;PushClient.d2=null;PushClient.bz=null;PushClient.am={};PushClient.am.fn=(new Date).getTime();PushClient.am.bc=null;PushClient.am.an=[];PushClient.br=false;PushClient.cf=false;if(window.WebSocket)PushClient.br=true;else if(window.MozWebSocket)PushClient.br=true;
if(PushClient.br==false)if(window.XDomainRequest){var xdrTest=new XDomainRequest;try{xdrTest.open("GET",window.location.protocol+"//127.0.0.1");xdrTest.send();PushClient.cf=true;xdrTest.abort()}catch(e$$6){xdrTest.abort();PushClient.cf=false}}else PushClient.cf=false;PushClient.c==="IE"?window.attachEvent("onunload",function(){PushClient.d8()}):window.addEventListener("unload",function(){PushClient.d8()},false);PushClient.e();
if(PushClient.c==="WebKit iPhone")PushClient.dw=65536;else if(PushClient.c==="Opera Mobile"){PushClient.dw=32768;PushClient.e1=500}
if(PushClient.br==true){PushClient.c4=true;PushClient.c7=true;PushClient.cb=true}else if(PushClient.cf&&window.postMessage){PushClient.c4=true;PushClient.c7=true;PushClient.cb=true;window.attachEvent("onmessage",PushClient.dx)}else if(this.XMLHttpRequest&&(new XMLHttpRequest).withCredentials!==undefined){PushClient.c4=true;PushClient.c7=true}else if(this.swfobject&&swfobject.hasFlashPlayerVersion("9")&&
(typeof PushClient_Allow_Flash_Transport==="undefined"||PushClient_Allow_Flash_Transport==true)){PushClient.c4=true;PushClient.i(function(){PushClient.ds()})}if(!PushClient.c4||PushClient.c7)PushClient.i(function(){PushClient.d1()});
/** @license
 *
 * SoundManager 2: JavaScript Sound for the Web
 * ----------------------------------------------
 * http://schillmania.com/projects/soundmanager2/
 *
 * Copyright (c) 2007, Scott Schiller. All rights reserved.
 * Code provided under the BSD License:
 * http://schillmania.com/projects/soundmanager2/license.txt
 *
 * V2.97a.20130101
 */
(function(i,g){function R(R,fa){function S(b){return c.preferFlash&&A&&!c.ignoreFlash&&c.flash[b]!==g&&c.flash[b]}function m(b){return function(c){var d=this._s;return!d||!d._a?null:b.call(this,c)}}this.setupOptions={url:R||null,flashVersion:8,debugMode:!0,debugFlash:!1,useConsole:!0,consoleOnly:!0,waitForWindowLoad:!1,bgColor:"#ffffff",useHighPerformance:!1,flashPollingInterval:null,html5PollingInterval:null,flashLoadTimeout:1E3,wmode:null,allowScriptAccess:"always",useFlashBlock:!1,useHTML5Audio:!0,
html5Test:/^(probably|maybe)$/i,preferFlash:!0,noSWFCache:!1};this.defaultOptions={autoLoad:!1,autoPlay:!1,from:null,loops:1,onid3:null,onload:null,whileloading:null,onplay:null,onpause:null,onresume:null,whileplaying:null,onposition:null,onstop:null,onfailure:null,onfinish:null,multiShot:!0,multiShotEvents:!1,position:null,pan:0,stream:!0,to:null,type:null,usePolicyFile:!1,volume:100};this.flash9Options={isMovieStar:null,usePeakData:!1,useWaveformData:!1,useEQData:!1,onbufferchange:null,ondataerror:null};
this.movieStarOptions={bufferTime:3,serverURL:null,onconnect:null,duration:null};this.audioFormats={mp3:{type:['audio/mpeg; codecs="mp3"',"audio/mpeg","audio/mp3","audio/MPA","audio/mpa-robust"],required:!0},mp4:{related:["aac","m4a","m4b"],type:['audio/mp4; codecs="mp4a.40.2"',"audio/aac","audio/x-m4a","audio/MP4A-LATM","audio/mpeg4-generic"],required:!1},ogg:{type:["audio/ogg; codecs=vorbis"],required:!1},wav:{type:['audio/wav; codecs="1"',"audio/wav","audio/wave","audio/x-wav"],required:!1}};this.movieID=
"sm2-container";this.id=fa||"sm2movie";this.debugID="soundmanager-debug";this.debugURLParam=/([#?&])debug=1/i;this.versionNumber="V2.97a.20130101";this.altURL=this.movieURL=this.version=null;this.enabled=this.swfLoaded=!1;this.oMC=null;this.sounds={};this.soundIDs=[];this.didFlashBlock=this.muted=!1;this.filePattern=null;this.filePatterns={flash8:/\.mp3(\?.*)?$/i,flash9:/\.mp3(\?.*)?$/i};this.features={buffering:!1,peakData:!1,waveformData:!1,eqData:!1,movieStar:!1};this.sandbox={};this.html5={usingFlash:null};
this.flash={};this.ignoreFlash=this.html5Only=!1;var Ga,c=this,Ha=null,h=null,T,q=navigator.userAgent,ga=i.location.href.toString(),l=document,ha,Ia,ia,k,r=[],J=!1,K=!1,j=!1,s=!1,ja=!1,L,t,ka,U,la,B,C,D,Ja,ma,V,na,W,oa,E,pa,M,qa,X,F,Ka,ra,La,sa,Ma,N=null,ta=null,v,ua,G,Y,Z,H,p,O=!1,va=!1,Na,Oa,Pa,$=0,P=null,aa,Qa=[],u=null,Ra,ba,Q,y,wa,xa,Sa,n,db=Array.prototype.slice,w=!1,ya,A,za,Ta,x,ca=q.match(/(ipad|iphone|ipod)/i),Ua=q.match(/android/i),z=q.match(/msie/i),eb=q.match(/webkit/i),Aa=q.match(/safari/i)&&
!q.match(/chrome/i),Ba=q.match(/opera/i),Ca=q.match(/(mobile|pre\/|xoom)/i)||ca||Ua,Va=!ga.match(/usehtml5audio/i)&&!ga.match(/sm2\-ignorebadua/i)&&Aa&&!q.match(/silk/i)&&q.match(/OS X 10_6_([3-7])/i),Da=l.hasFocus!==g?l.hasFocus():null,da=Aa&&(l.hasFocus===g||!l.hasFocus()),Wa=!da,Xa=/(mp3|mp4|mpa|m4a|m4b)/i,Ea=l.location?l.location.protocol.match(/http/i):null,Ya=!Ea?"http://":"",Za=/^\s*audio\/(?:x-)?(?:mpeg4|aac|flv|mov|mp4||m4v|m4a|m4b|mp4v|3gp|3g2)\s*(?:$|;)/i,$a="mpeg4 aac flv mov mp4 m4v f4v m4a m4b mp4v 3gp 3g2".split(" "),
fb=RegExp("\\.("+$a.join("|")+")(\\?.*)?$","i");this.mimePattern=/^\s*audio\/(?:x-)?(?:mp(?:eg|3))\s*(?:$|;)/i;this.useAltURL=!Ea;var Fa;try{Fa=Audio!==g&&(Ba&&opera!==g&&10>opera.version()?new Audio(null):new Audio).canPlayType!==g}catch(hb){Fa=!1}this.hasHTML5=Fa;this.setup=function(b){var e=!c.url;b!==g&&(j&&u&&c.ok()&&(b.flashVersion!==g||b.url!==g||b.html5Test!==g))&&H(v("setupLate"));ka(b);e&&(M&&b.url!==g)&&c.beginDelayedInit();!M&&(b.url!==g&&"complete"===l.readyState)&&setTimeout(E,1);return c};
this.supported=this.ok=function(){return u?j&&!s:c.useHTML5Audio&&c.hasHTML5};this.getMovie=function(b){return T(b)||l[b]||i[b]};this.createSound=function(b,e){function d(){a=Y(a);c.sounds[a.id]=new Ga(a);c.soundIDs.push(a.id);return c.sounds[a.id]}var a,f=null;if(!j||!c.ok())return H(void 0),!1;e!==g&&(b={id:b,url:e});a=t(b);a.url=aa(a.url);if(p(a.id,!0))return c.sounds[a.id];ba(a)?(f=d(),f._setup_html5(a)):(8<k&&null===a.isMovieStar&&(a.isMovieStar=!(!a.serverURL&&!(a.type&&a.type.match(Za)||a.url.match(fb)))),
a=Z(a,void 0),f=d(),8===k?h._createSound(a.id,a.loops||1,a.usePolicyFile):(h._createSound(a.id,a.url,a.usePeakData,a.useWaveformData,a.useEQData,a.isMovieStar,a.isMovieStar?a.bufferTime:!1,a.loops||1,a.serverURL,a.duration||null,a.autoPlay,!0,a.autoLoad,a.usePolicyFile),a.serverURL||(f.connected=!0,a.onconnect&&a.onconnect.apply(f))),!a.serverURL&&(a.autoLoad||a.autoPlay)&&f.load(a));!a.serverURL&&a.autoPlay&&f.play();return f};this.destroySound=function(b,e){if(!p(b))return!1;var d=c.sounds[b],a;
d._iO={};d.stop();d.unload();for(a=0;a<c.soundIDs.length;a++)if(c.soundIDs[a]===b){c.soundIDs.splice(a,1);break}e||d.destruct(!0);delete c.sounds[b];return!0};this.load=function(b,e){return!p(b)?!1:c.sounds[b].load(e)};this.unload=function(b){return!p(b)?!1:c.sounds[b].unload()};this.onposition=this.onPosition=function(b,e,d,a){return!p(b)?!1:c.sounds[b].onposition(e,d,a)};this.clearOnPosition=function(b,e,d){return!p(b)?!1:c.sounds[b].clearOnPosition(e,d)};this.start=this.play=function(b,e){var d=
!1;return!j||!c.ok()?(H("soundManager.play(): "+v(!j?"notReady":"notOK")),d):!p(b)?(e instanceof Object||(e={url:e}),e&&e.url&&(e.id=b,d=c.createSound(e).play()),d):c.sounds[b].play(e)};this.setPosition=function(b,e){return!p(b)?!1:c.sounds[b].setPosition(e)};this.stop=function(b){return!p(b)?!1:c.sounds[b].stop()};this.stopAll=function(){for(var b in c.sounds)c.sounds.hasOwnProperty(b)&&c.sounds[b].stop()};this.pause=function(b){return!p(b)?!1:c.sounds[b].pause()};this.pauseAll=function(){var b;
for(b=c.soundIDs.length-1;0<=b;b--)c.sounds[c.soundIDs[b]].pause()};this.resume=function(b){return!p(b)?!1:c.sounds[b].resume()};this.resumeAll=function(){var b;for(b=c.soundIDs.length-1;0<=b;b--)c.sounds[c.soundIDs[b]].resume()};this.togglePause=function(b){return!p(b)?!1:c.sounds[b].togglePause()};this.setPan=function(b,e){return!p(b)?!1:c.sounds[b].setPan(e)};this.setVolume=function(b,e){return!p(b)?!1:c.sounds[b].setVolume(e)};this.mute=function(b){var e=0;b instanceof String&&(b=null);if(b)return!p(b)?
!1:c.sounds[b].mute();for(e=c.soundIDs.length-1;0<=e;e--)c.sounds[c.soundIDs[e]].mute();return c.muted=!0};this.muteAll=function(){c.mute()};this.unmute=function(b){b instanceof String&&(b=null);if(b)return!p(b)?!1:c.sounds[b].unmute();for(b=c.soundIDs.length-1;0<=b;b--)c.sounds[c.soundIDs[b]].unmute();c.muted=!1;return!0};this.unmuteAll=function(){c.unmute()};this.toggleMute=function(b){return!p(b)?!1:c.sounds[b].toggleMute()};this.getMemoryUse=function(){var b=0;h&&8!==k&&(b=parseInt(h._getMemoryUse(),
10));return b};this.disable=function(b){var e;b===g&&(b=!1);if(s)return!1;s=!0;for(e=c.soundIDs.length-1;0<=e;e--)La(c.sounds[c.soundIDs[e]]);L(b);n.remove(i,"load",C);return!0};this.canPlayMIME=function(b){var e;c.hasHTML5&&(e=Q({type:b}));!e&&u&&(e=b&&c.ok()?!!(8<k&&b.match(Za)||b.match(c.mimePattern)):null);return e};this.canPlayURL=function(b){var e;c.hasHTML5&&(e=Q({url:b}));!e&&u&&(e=b&&c.ok()?!!b.match(c.filePattern):null);return e};this.canPlayLink=function(b){return b.type!==g&&b.type&&c.canPlayMIME(b.type)?
!0:c.canPlayURL(b.href)};this.getSoundById=function(b){if(!b)throw Error("soundManager.getSoundById(): sID is null/_undefined");return c.sounds[b]};this.onready=function(b,c){if("function"===typeof b)c||(c=i),la("onready",b,c),B();else throw v("needFunction","onready");return!0};this.ontimeout=function(b,c){if("function"===typeof b)c||(c=i),la("ontimeout",b,c),B({type:"ontimeout"});else throw v("needFunction","ontimeout");return!0};this._wD=this._writeDebug=function(){return!0};this._debug=function(){};
this.reboot=function(b,e){var d,a,f;for(d=c.soundIDs.length-1;0<=d;d--)c.sounds[c.soundIDs[d]].destruct();if(h)try{z&&(ta=h.innerHTML),N=h.parentNode.removeChild(h)}catch(g){}ta=N=u=h=null;c.enabled=M=j=O=va=J=K=s=w=c.swfLoaded=!1;c.soundIDs=[];c.sounds={};if(b)r=[];else for(d in r)if(r.hasOwnProperty(d)){a=0;for(f=r[d].length;a<f;a++)r[d][a].fired=!1}c.html5={usingFlash:null};c.flash={};c.html5Only=!1;c.ignoreFlash=!1;i.setTimeout(function(){oa();e||c.beginDelayedInit()},20);return c};this.reset=
function(){return c.reboot(!0,!0)};this.getMoviePercent=function(){return h&&"PercentLoaded"in h?h.PercentLoaded():null};this.beginDelayedInit=function(){ja=!0;E();setTimeout(function(){if(va)return!1;X();W();return va=!0},20);D()};this.destruct=function(){c.disable(!0)};Ga=function(b){var e,d,a=this,f,ab,i,I,l,m,q=!1,j=[],n=0,s,u,r=null;d=e=null;this.sID=this.id=b.id;this.url=b.url;this._iO=this.instanceOptions=this.options=t(b);this.pan=this.options.pan;this.volume=this.options.volume;this.isHTML5=
!1;this._a=null;this.id3={};this._debug=function(){};this.load=function(b){var c=null;b!==g?a._iO=t(b,a.options):(b=a.options,a._iO=b,r&&r!==a.url&&(a._iO.url=a.url,a.url=null));a._iO.url||(a._iO.url=a.url);a._iO.url=aa(a._iO.url);b=a.instanceOptions=a._iO;if(b.url===a.url&&0!==a.readyState&&2!==a.readyState)return 3===a.readyState&&b.onload&&b.onload.apply(a,[!!a.duration]),a;a.loaded=!1;a.readyState=1;a.playState=0;a.id3={};if(ba(b))c=a._setup_html5(b),c._called_load||(a._html5_canplay=!1,a.url!==
b.url&&(a._a.src=b.url,a.setPosition(0)),a._a.autobuffer="auto",a._a.preload="auto",a._a._called_load=!0,b.autoPlay&&a.play());else try{a.isHTML5=!1,a._iO=Z(Y(b)),b=a._iO,8===k?h._load(a.id,b.url,b.stream,b.autoPlay,b.usePolicyFile):h._load(a.id,b.url,!!b.stream,!!b.autoPlay,b.loops||1,!!b.autoLoad,b.usePolicyFile)}catch(e){F({type:"SMSOUND_LOAD_JS_EXCEPTION",fatal:!0})}a.url=b.url;return a};this.unload=function(){0!==a.readyState&&(a.isHTML5?(I(),a._a&&(a._a.pause(),wa(a._a,"about:blank"),r="about:blank")):
8===k?h._unload(a.id,"about:blank"):h._unload(a.id),f());return a};this.destruct=function(b){a.isHTML5?(I(),a._a&&(a._a.pause(),wa(a._a),w||i(),a._a._s=null,a._a=null)):(a._iO.onfailure=null,h._destroySound(a.id));b||c.destroySound(a.id,!0)};this.start=this.play=function(b,c){var e,d;d=!0;d=null;c=c===g?!0:c;b||(b={});a.url&&(a._iO.url=a.url);a._iO=t(a._iO,a.options);a._iO=t(b,a._iO);a._iO.url=aa(a._iO.url);a.instanceOptions=a._iO;if(a._iO.serverURL&&!a.connected)return a.getAutoPlay()||a.setAutoPlay(!0),
a;ba(a._iO)&&(a._setup_html5(a._iO),l());1===a.playState&&!a.paused&&((e=a._iO.multiShot)||(d=a));if(null!==d)return d;b.url&&b.url!==a.url&&a.load(a._iO);a.loaded||(0===a.readyState?(a.isHTML5||(a._iO.autoPlay=!0),a.load(a._iO),a.instanceOptions=a._iO):2===a.readyState&&(d=a));if(null!==d)return d;!a.isHTML5&&(9===k&&0<a.position&&a.position===a.duration)&&(b.position=0);if(a.paused&&0<=a.position&&(!a._iO.serverURL||0<a.position))a.resume();else{a._iO=t(b,a._iO);if(null!==a._iO.from&&null!==a._iO.to&&
0===a.instanceCount&&0===a.playState&&!a._iO.serverURL){e=function(){a._iO=t(b,a._iO);a.play(a._iO)};if(a.isHTML5&&!a._html5_canplay)a.load({oncanplay:e}),d=!1;else if(!a.isHTML5&&!a.loaded&&(!a.readyState||2!==a.readyState))a.load({onload:e}),d=!1;if(null!==d)return d;a._iO=u()}(!a.instanceCount||a._iO.multiShotEvents||!a.isHTML5&&8<k&&!a.getAutoPlay())&&a.instanceCount++;a._iO.onposition&&0===a.playState&&m(a);a.playState=1;a.paused=!1;a.position=a._iO.position!==g&&!isNaN(a._iO.position)?a._iO.position:
0;a.isHTML5||(a._iO=Z(Y(a._iO)));a._iO.onplay&&c&&(a._iO.onplay.apply(a),q=!0);a.setVolume(a._iO.volume,!0);a.setPan(a._iO.pan,!0);a.isHTML5?(l(),d=a._setup_html5(),a.setPosition(a._iO.position),d.play()):(d=h._start(a.id,a._iO.loops||1,9===k?a._iO.position:a._iO.position/1E3,a._iO.multiShot),9===k&&!d&&a._iO.onplayerror&&a._iO.onplayerror.apply(a))}return a};this.stop=function(b){var c=a._iO;1===a.playState&&(a._onbufferchange(0),a._resetOnPosition(0),a.paused=!1,a.isHTML5||(a.playState=0),s(),c.to&&
a.clearOnPosition(c.to),a.isHTML5?a._a&&(b=a.position,a.setPosition(0),a.position=b,a._a.pause(),a.playState=0,a._onTimer(),I()):(h._stop(a.id,b),c.serverURL&&a.unload()),a.instanceCount=0,a._iO={},c.onstop&&c.onstop.apply(a));return a};this.setAutoPlay=function(b){a._iO.autoPlay=b;a.isHTML5||(h._setAutoPlay(a.id,b),b&&!a.instanceCount&&1===a.readyState&&a.instanceCount++)};this.getAutoPlay=function(){return a._iO.autoPlay};this.setPosition=function(b){b===g&&(b=0);var c=a.isHTML5?Math.max(b,0):Math.min(a.duration||
a._iO.duration,Math.max(b,0));a.position=c;b=a.position/1E3;a._resetOnPosition(a.position);a._iO.position=c;if(a.isHTML5){if(a._a&&a._html5_canplay&&a._a.currentTime!==b)try{a._a.currentTime=b,(0===a.playState||a.paused)&&a._a.pause()}catch(e){}}else b=9===k?a.position:b,a.readyState&&2!==a.readyState&&h._setPosition(a.id,b,a.paused||!a.playState,a._iO.multiShot);a.isHTML5&&a.paused&&a._onTimer(!0);return a};this.pause=function(b){if(a.paused||0===a.playState&&1!==a.readyState)return a;a.paused=!0;
a.isHTML5?(a._setup_html5().pause(),I()):(b||b===g)&&h._pause(a.id,a._iO.multiShot);a._iO.onpause&&a._iO.onpause.apply(a);return a};this.resume=function(){var b=a._iO;if(!a.paused)return a;a.paused=!1;a.playState=1;a.isHTML5?(a._setup_html5().play(),l()):(b.isMovieStar&&!b.serverURL&&a.setPosition(a.position),h._pause(a.id,b.multiShot));!q&&b.onplay?(b.onplay.apply(a),q=!0):b.onresume&&b.onresume.apply(a);return a};this.togglePause=function(){if(0===a.playState)return a.play({position:9===k&&!a.isHTML5?
a.position:a.position/1E3}),a;a.paused?a.resume():a.pause();return a};this.setPan=function(b,c){b===g&&(b=0);c===g&&(c=!1);a.isHTML5||h._setPan(a.id,b);a._iO.pan=b;c||(a.pan=b,a.options.pan=b);return a};this.setVolume=function(b,e){b===g&&(b=100);e===g&&(e=!1);a.isHTML5?a._a&&(a._a.volume=Math.max(0,Math.min(1,b/100))):h._setVolume(a.id,c.muted&&!a.muted||a.muted?0:b);a._iO.volume=b;e||(a.volume=b,a.options.volume=b);return a};this.mute=function(){a.muted=!0;a.isHTML5?a._a&&(a._a.muted=!0):h._setVolume(a.id,
0);return a};this.unmute=function(){a.muted=!1;var b=a._iO.volume!==g;a.isHTML5?a._a&&(a._a.muted=!1):h._setVolume(a.id,b?a._iO.volume:a.options.volume);return a};this.toggleMute=function(){return a.muted?a.unmute():a.mute()};this.onposition=this.onPosition=function(b,c,e){j.push({position:parseInt(b,10),method:c,scope:e!==g?e:a,fired:!1});return a};this.clearOnPosition=function(a,b){var c,a=parseInt(a,10);if(isNaN(a))return!1;for(c=0;c<j.length;c++)if(a===j[c].position&&(!b||b===j[c].method))j[c].fired&&
n--,j.splice(c,1)};this._processOnPosition=function(){var b,c;b=j.length;if(!b||!a.playState||n>=b)return!1;for(b-=1;0<=b;b--)c=j[b],!c.fired&&a.position>=c.position&&(c.fired=!0,n++,c.method.apply(c.scope,[c.position]));return!0};this._resetOnPosition=function(a){var b,c;b=j.length;if(!b)return!1;for(b-=1;0<=b;b--)c=j[b],c.fired&&a<=c.position&&(c.fired=!1,n--);return!0};u=function(){var b=a._iO,c=b.from,e=b.to,d,f;f=function(){a.clearOnPosition(e,f);a.stop()};d=function(){if(null!==e&&!isNaN(e))a.onPosition(e,
f)};null!==c&&!isNaN(c)&&(b.position=c,b.multiShot=!1,d());return b};m=function(){var b,c=a._iO.onposition;if(c)for(b in c)if(c.hasOwnProperty(b))a.onPosition(parseInt(b,10),c[b])};s=function(){var b,c=a._iO.onposition;if(c)for(b in c)c.hasOwnProperty(b)&&a.clearOnPosition(parseInt(b,10))};l=function(){a.isHTML5&&Na(a)};I=function(){a.isHTML5&&Oa(a)};f=function(b){b||(j=[],n=0);q=!1;a._hasTimer=null;a._a=null;a._html5_canplay=!1;a.bytesLoaded=null;a.bytesTotal=null;a.duration=a._iO&&a._iO.duration?
a._iO.duration:null;a.durationEstimate=null;a.buffered=[];a.eqData=[];a.eqData.left=[];a.eqData.right=[];a.failures=0;a.isBuffering=!1;a.instanceOptions={};a.instanceCount=0;a.loaded=!1;a.metadata={};a.readyState=0;a.muted=!1;a.paused=!1;a.peakData={left:0,right:0};a.waveformData={left:[],right:[]};a.playState=0;a.position=null;a.id3={}};f();this._onTimer=function(b){var c,f=!1,g={};if(a._hasTimer||b){if(a._a&&(b||(0<a.playState||1===a.readyState)&&!a.paused))c=a._get_html5_duration(),c!==e&&(e=c,
a.duration=c,f=!0),a.durationEstimate=a.duration,c=1E3*a._a.currentTime||0,c!==d&&(d=c,f=!0),(f||b)&&a._whileplaying(c,g,g,g,g);return f}};this._get_html5_duration=function(){var b=a._iO;return(b=a._a&&a._a.duration?1E3*a._a.duration:b&&b.duration?b.duration:null)&&!isNaN(b)&&Infinity!==b?b:null};this._apply_loop=function(a,b){a.loop=1<b?"loop":""};this._setup_html5=function(b){var b=t(a._iO,b),c=decodeURI,e=w?Ha:a._a,d=c(b.url),g;w?d===ya&&(g=!0):d===r&&(g=!0);if(e){if(e._s)if(w)e._s&&(e._s.playState&&
!g)&&e._s.stop();else if(!w&&d===c(r))return a._apply_loop(e,b.loops),e;g||(f(!1),e.src=b.url,ya=r=a.url=b.url,e._called_load=!1)}else a._a=b.autoLoad||b.autoPlay?new Audio(b.url):Ba&&10>opera.version()?new Audio(null):new Audio,e=a._a,e._called_load=!1,w&&(Ha=e);a.isHTML5=!0;a._a=e;e._s=a;ab();a._apply_loop(e,b.loops);b.autoLoad||b.autoPlay?a.load():(e.autobuffer=!1,e.preload="auto");return e};ab=function(){if(a._a._added_events)return!1;var b;a._a._added_events=!0;for(b in x)x.hasOwnProperty(b)&&
a._a&&a._a.addEventListener(b,x[b],!1);return!0};i=function(){var b;a._a._added_events=!1;for(b in x)x.hasOwnProperty(b)&&a._a&&a._a.removeEventListener(b,x[b],!1)};this._onload=function(b){b=!!b||!a.isHTML5&&8===k&&a.duration;a.loaded=b;a.readyState=b?3:2;a._onbufferchange(0);a._iO.onload&&a._iO.onload.apply(a,[b]);return!0};this._onbufferchange=function(b){if(0===a.playState||b&&a.isBuffering||!b&&!a.isBuffering)return!1;a.isBuffering=1===b;a._iO.onbufferchange&&a._iO.onbufferchange.apply(a);return!0};
this._onsuspend=function(){a._iO.onsuspend&&a._iO.onsuspend.apply(a);return!0};this._onfailure=function(b,c,e){a.failures++;if(a._iO.onfailure&&1===a.failures)a._iO.onfailure(a,b,c,e)};this._onfinish=function(){var b=a._iO.onfinish;a._onbufferchange(0);a._resetOnPosition(0);a.instanceCount&&(a.instanceCount--,a.instanceCount||(s(),a.playState=0,a.paused=!1,a.instanceCount=0,a.instanceOptions={},a._iO={},I(),a.isHTML5&&(a.position=0)),(!a.instanceCount||a._iO.multiShotEvents)&&b&&b.apply(a))};this._whileloading=
function(b,c,e,d){var f=a._iO;a.bytesLoaded=b;a.bytesTotal=c;a.duration=Math.floor(e);a.bufferLength=d;a.durationEstimate=!a.isHTML5&&!f.isMovieStar?f.duration?a.duration>f.duration?a.duration:f.duration:parseInt(a.bytesTotal/a.bytesLoaded*a.duration,10):a.duration;a.isHTML5||(a.buffered=[{start:0,end:a.duration}]);(3!==a.readyState||a.isHTML5)&&f.whileloading&&f.whileloading.apply(a)};this._whileplaying=function(b,c,e,d,f){var h=a._iO;if(isNaN(b)||null===b)return!1;a.position=Math.max(0,b);a._processOnPosition();
!a.isHTML5&&8<k&&(h.usePeakData&&(c!==g&&c)&&(a.peakData={left:c.leftPeak,right:c.rightPeak}),h.useWaveformData&&(e!==g&&e)&&(a.waveformData={left:e.split(","),right:d.split(",")}),h.useEQData&&(f!==g&&f&&f.leftEQ)&&(b=f.leftEQ.split(","),a.eqData=b,a.eqData.left=b,f.rightEQ!==g&&f.rightEQ&&(a.eqData.right=f.rightEQ.split(","))));1===a.playState&&(!a.isHTML5&&(8===k&&!a.position&&a.isBuffering)&&a._onbufferchange(0),h.whileplaying&&h.whileplaying.apply(a));return!0};this._oncaptiondata=function(b){a.captiondata=
b;a._iO.oncaptiondata&&a._iO.oncaptiondata.apply(a,[b])};this._onmetadata=function(b,c){var e={},d,f;d=0;for(f=b.length;d<f;d++)e[b[d]]=c[d];a.metadata=e;a._iO.onmetadata&&a._iO.onmetadata.apply(a)};this._onid3=function(b,c){var e=[],d,f;d=0;for(f=b.length;d<f;d++)e[b[d]]=c[d];a.id3=t(a.id3,e);a._iO.onid3&&a._iO.onid3.apply(a)};this._onconnect=function(b){b=1===b;if(a.connected=b)a.failures=0,p(a.id)&&(a.getAutoPlay()?a.play(g,a.getAutoPlay()):a._iO.autoLoad&&a.load()),a._iO.onconnect&&a._iO.onconnect.apply(a,
[b])};this._ondataerror=function(){0<a.playState&&a._iO.ondataerror&&a._iO.ondataerror.apply(a)}};qa=function(){return l.body||l._docElement||l.getElementsByTagName("div")[0]};T=function(b){return l.getElementById(b)};t=function(b,e){var d=b||{},a,f;a=e===g?c.defaultOptions:e;for(f in a)a.hasOwnProperty(f)&&d[f]===g&&(d[f]="object"!==typeof a[f]||null===a[f]?a[f]:t(d[f],a[f]));return d};U={onready:1,ontimeout:1,defaultOptions:1,flash9Options:1,movieStarOptions:1};ka=function(b,e){var d,a=!0,f=e!==
g,h=c.setupOptions;for(d in b)if(b.hasOwnProperty(d))if("object"!==typeof b[d]||null===b[d]||b[d]instanceof Array||b[d]instanceof RegExp)f&&U[e]!==g?c[e][d]=b[d]:h[d]!==g?(c.setupOptions[d]=b[d],c[d]=b[d]):U[d]===g?(H(v(c[d]===g?"setupUndef":"setupError",d),2),a=!1):c[d]instanceof Function?c[d].apply(c,b[d]instanceof Array?b[d]:[b[d]]):c[d]=b[d];else if(U[d]===g)H(v(c[d]===g?"setupUndef":"setupError",d),2),a=!1;else return ka(b[d],d);return a};var bb=function(b){var b=db.call(b),c=b.length;ea?(b[1]=
"on"+b[1],3<c&&b.pop()):3===c&&b.push(!1);return b},cb=function(b,c){var d=b.shift(),a=[gb[c]];if(ea)d[a](b[0],b[1]);else d[a].apply(d,b)},ea=i.attachEvent,gb={add:ea?"attachEvent":"addEventListener",remove:ea?"detachEvent":"removeEventListener"};n={add:function(){cb(bb(arguments),"add")},remove:function(){cb(bb(arguments),"remove")}};x={abort:m(function(){}),canplay:m(function(){var b=this._s,c;if(b._html5_canplay)return!0;b._html5_canplay=!0;b._onbufferchange(0);c=b._iO.position!==g&&!isNaN(b._iO.position)?
b._iO.position/1E3:null;if(b.position&&this.currentTime!==c)try{this.currentTime=c}catch(d){}b._iO._oncanplay&&b._iO._oncanplay()}),canplaythrough:m(function(){var b=this._s;b.loaded||(b._onbufferchange(0),b._whileloading(b.bytesLoaded,b.bytesTotal,b._get_html5_duration()),b._onload(!0))}),ended:m(function(){this._s._onfinish()}),error:m(function(){this._s._onload(!1)}),loadeddata:m(function(){var b=this._s;!b._loaded&&!Aa&&(b.duration=b._get_html5_duration())}),loadedmetadata:m(function(){}),loadstart:m(function(){this._s._onbufferchange(1)}),
play:m(function(){this._s._onbufferchange(0)}),playing:m(function(){this._s._onbufferchange(0)}),progress:m(function(b){var c=this._s,d,a,f=0,f=b.target.buffered;d=b.loaded||0;var g=b.total||1;c.buffered=[];if(f&&f.length){d=0;for(a=f.length;d<a;d++)c.buffered.push({start:1E3*f.start(d),end:1E3*f.end(d)});f=1E3*(f.end(0)-f.start(0));d=f/(1E3*b.target.duration)}isNaN(d)||(c._onbufferchange(0),c._whileloading(d,g,c._get_html5_duration()),d&&(g&&d===g)&&x.canplaythrough.call(this,b))}),ratechange:m(function(){}),
suspend:m(function(b){var c=this._s;x.progress.call(this,b);c._onsuspend()}),stalled:m(function(){}),timeupdate:m(function(){this._s._onTimer()}),waiting:m(function(){this._s._onbufferchange(1)})};ba=function(b){return b.serverURL||b.type&&S(b.type)?!1:b.type?Q({type:b.type}):Q({url:b.url})||c.html5Only};wa=function(b,c){b&&(b.src=c,b._called_load=!1);w&&(ya=null)};Q=function(b){if(!c.useHTML5Audio||!c.hasHTML5)return!1;var e=b.url||null,b=b.type||null,d=c.audioFormats,a;if(b&&c.html5[b]!==g)return c.html5[b]&&
!S(b);if(!y){y=[];for(a in d)d.hasOwnProperty(a)&&(y.push(a),d[a].related&&(y=y.concat(d[a].related)));y=RegExp("\\.("+y.join("|")+")(\\?.*)?$","i")}a=e?e.toLowerCase().match(y):null;!a||!a.length?b&&(e=b.indexOf(";"),a=(-1!==e?b.substr(0,e):b).substr(6)):a=a[1];a&&c.html5[a]!==g?e=c.html5[a]&&!S(a):(b="audio/"+a,e=c.html5.canPlayType({type:b}),e=(c.html5[a]=e)&&c.html5[b]&&!S(b));return e};Sa=function(){function b(a){var b,d,f=b=!1;if(!e||"function"!==typeof e.canPlayType)return b;if(a instanceof
Array){b=0;for(d=a.length;b<d;b++)if(c.html5[a[b]]||e.canPlayType(a[b]).match(c.html5Test))f=!0,c.html5[a[b]]=!0,c.flash[a[b]]=!!a[b].match(Xa);b=f}else a=e&&"function"===typeof e.canPlayType?e.canPlayType(a):!1,b=!(!a||!a.match(c.html5Test));return b}if(!c.useHTML5Audio||!c.hasHTML5)return!1;var e=Audio!==g?Ba&&10>opera.version()?new Audio(null):new Audio:null,d,a,f={},h;h=c.audioFormats;for(d in h)if(h.hasOwnProperty(d)&&(a="audio/"+d,f[d]=b(h[d].type),f[a]=f[d],d.match(Xa)?(c.flash[d]=!0,c.flash[a]=
!0):(c.flash[d]=!1,c.flash[a]=!1),h[d]&&h[d].related))for(a=h[d].related.length-1;0<=a;a--)f["audio/"+h[d].related[a]]=f[d],c.html5[h[d].related[a]]=f[d],c.flash[h[d].related[a]]=f[d];f.canPlayType=e?b:null;c.html5=t(c.html5,f);return!0};na={};v=function(){};Y=function(b){8===k&&(1<b.loops&&b.stream)&&(b.stream=!1);return b};Z=function(b){if(b&&!b.usePolicyFile&&(b.onid3||b.usePeakData||b.useWaveformData||b.useEQData))b.usePolicyFile=!0;return b};H=function(){};ha=function(){return!1};La=function(b){for(var c in b)b.hasOwnProperty(c)&&
"function"===typeof b[c]&&(b[c]=ha)};sa=function(b){b===g&&(b=!1);(s||b)&&c.disable(b)};Ma=function(b){var e=null;if(b)if(b.match(/\.swf(\?.*)?$/i)){if(e=b.substr(b.toLowerCase().lastIndexOf(".swf?")+4))return b}else b.lastIndexOf("/")!==b.length-1&&(b+="/");b=(b&&-1!==b.lastIndexOf("/")?b.substr(0,b.lastIndexOf("/")+1):"./")+c.movieURL;c.noSWFCache&&(b+="?ts="+(new Date).getTime());return b};ma=function(){k=parseInt(c.flashVersion,10);8!==k&&9!==k&&(c.flashVersion=k=8);var b=c.debugMode||c.debugFlash?
"_debug.swf":".swf";c.useHTML5Audio&&(!c.html5Only&&c.audioFormats.mp4.required&&9>k)&&(c.flashVersion=k=9);c.version=c.versionNumber+(c.html5Only?" (HTML5-only mode)":9===k?" (AS3/Flash 9)":" (AS2/Flash 8)");8<k?(c.defaultOptions=t(c.defaultOptions,c.flash9Options),c.features.buffering=!0,c.defaultOptions=t(c.defaultOptions,c.movieStarOptions),c.filePatterns.flash9=RegExp("\\.(mp3|"+$a.join("|")+")(\\?.*)?$","i"),c.features.movieStar=!0):c.features.movieStar=!1;c.filePattern=c.filePatterns[8!==k?
"flash9":"flash8"];c.movieURL=(8===k?"soundmanager2.swf":"soundmanager2_flash9.swf").replace(".swf",b);c.features.peakData=c.features.waveformData=c.features.eqData=8<k};Ka=function(b,c){if(!h)return!1;h._setPolling(b,c)};ra=function(){c.debugURLParam.test(ga)&&(c.debugMode=!0)};p=this.getSoundById;G=function(){var b=[];c.debugMode&&b.push("sm2_debug");c.debugFlash&&b.push("flash_debug");c.useHighPerformance&&b.push("high_performance");return b.join(" ")};ua=function(){v("fbHandler");var b=c.getMoviePercent(),
e={type:"FLASHBLOCK"};if(c.html5Only)return!1;c.ok()?c.oMC&&(c.oMC.className=[G(),"movieContainer","swf_loaded"+(c.didFlashBlock?" swf_unblocked":"")].join(" ")):(u&&(c.oMC.className=G()+" movieContainer "+(null===b?"swf_timedout":"swf_error")),c.didFlashBlock=!0,B({type:"ontimeout",ignoreInit:!0,error:e}),F(e))};la=function(b,c,d){r[b]===g&&(r[b]=[]);r[b].push({method:c,scope:d||null,fired:!1})};B=function(b){b||(b={type:c.ok()?"onready":"ontimeout"});if(!j&&b&&!b.ignoreInit||"ontimeout"===b.type&&
(c.ok()||s&&!b.ignoreInit))return!1;var e={success:b&&b.ignoreInit?c.ok():!s},d=b&&b.type?r[b.type]||[]:[],a=[],f,e=[e],g=u&&!c.ok();b.error&&(e[0].error=b.error);b=0;for(f=d.length;b<f;b++)!0!==d[b].fired&&a.push(d[b]);if(a.length){b=0;for(f=a.length;b<f;b++)a[b].scope?a[b].method.apply(a[b].scope,e):a[b].method.apply(this,e),g||(a[b].fired=!0)}return!0};C=function(){i.setTimeout(function(){c.useFlashBlock&&ua();B();"function"===typeof c.onload&&c.onload.apply(i);c.waitForWindowLoad&&n.add(i,"load",
C)},1)};za=function(){if(A!==g)return A;var b=!1,c=navigator,d=c.plugins,a,f=i.ActiveXObject;if(d&&d.length)(c=c.mimeTypes)&&(c["application/x-shockwave-flash"]&&c["application/x-shockwave-flash"].enabledPlugin&&c["application/x-shockwave-flash"].enabledPlugin.description)&&(b=!0);else if(f!==g&&!q.match(/MSAppHost/i)){try{a=new f("ShockwaveFlash.ShockwaveFlash")}catch(h){}b=!!a}return A=b};Ra=function(){var b,e,d=c.audioFormats;if(ca&&q.match(/os (1|2|3_0|3_1)/i))c.hasHTML5=!1,c.html5Only=!0,c.oMC&&
(c.oMC.style.display="none");else if(c.useHTML5Audio&&(!c.html5||!c.html5.canPlayType))c.hasHTML5=!1;if(c.useHTML5Audio&&c.hasHTML5)for(e in d)if(d.hasOwnProperty(e)&&(d[e].required&&!c.html5.canPlayType(d[e].type)||c.preferFlash&&(c.flash[e]||c.flash[d[e].type])))b=!0;c.ignoreFlash&&(b=!1);c.html5Only=c.hasHTML5&&c.useHTML5Audio&&!b;return!c.html5Only};aa=function(b){var e,d,a=0;if(b instanceof Array){e=0;for(d=b.length;e<d;e++)if(b[e]instanceof Object){if(c.canPlayMIME(b[e].type)){a=e;break}}else if(c.canPlayURL(b[e])){a=
e;break}b[a].url&&(b[a]=b[a].url);b=b[a]}return b};Na=function(b){b._hasTimer||(b._hasTimer=!0,!Ca&&c.html5PollingInterval&&(null===P&&0===$&&(P=i.setInterval(Pa,c.html5PollingInterval)),$++))};Oa=function(b){b._hasTimer&&(b._hasTimer=!1,!Ca&&c.html5PollingInterval&&$--)};Pa=function(){var b;if(null!==P&&!$)return i.clearInterval(P),P=null,!1;for(b=c.soundIDs.length-1;0<=b;b--)c.sounds[c.soundIDs[b]].isHTML5&&c.sounds[c.soundIDs[b]]._hasTimer&&c.sounds[c.soundIDs[b]]._onTimer()};F=function(b){b=b!==
g?b:{};"function"===typeof c.onerror&&c.onerror.apply(i,[{type:b.type!==g?b.type:null}]);b.fatal!==g&&b.fatal&&c.disable()};Ta=function(){if(!Va||!za())return!1;var b=c.audioFormats,e,d;for(d in b)if(b.hasOwnProperty(d)&&("mp3"===d||"mp4"===d))if(c.html5[d]=!1,b[d]&&b[d].related)for(e=b[d].related.length-1;0<=e;e--)c.html5[b[d].related[e]]=!1};this._setSandboxType=function(){};this._externalInterfaceOK=function(){if(c.swfLoaded)return!1;c.swfLoaded=!0;da=!1;Va&&Ta();setTimeout(ia,z?100:1)};X=function(b,
e){function d(a,b){return'<param name="'+a+'" value="'+b+'" />'}if(J&&K)return!1;if(c.html5Only)return ma(),c.oMC=T(c.movieID),ia(),K=J=!0,!1;var a=e||c.url,f=c.altURL||a,h=qa(),i=G(),k=null,k=l.getElementsByTagName("html")[0],j,n,m,k=k&&k.dir&&k.dir.match(/rtl/i),b=b===g?c.id:b;ma();c.url=Ma(Ea?a:f);e=c.url;c.wmode=!c.wmode&&c.useHighPerformance?"transparent":c.wmode;if(null!==c.wmode&&(q.match(/msie 8/i)||!z&&!c.useHighPerformance)&&navigator.platform.match(/win32|win64/i))Qa.push(na.spcWmode),
c.wmode=null;h={name:b,id:b,src:e,quality:"high",allowScriptAccess:c.allowScriptAccess,bgcolor:c.bgColor,pluginspage:Ya+"www.macromedia.com/go/getflashplayer",title:"JS/Flash audio component (SoundManager 2)",type:"application/x-shockwave-flash",wmode:c.wmode,hasPriority:"true"};c.debugFlash&&(h.FlashVars="debug=1");c.wmode||delete h.wmode;if(z)a=l.createElement("div"),n=['<object id="'+b+'" data="'+e+'" type="'+h.type+'" title="'+h.title+'" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="'+
Ya+'download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0">',d("movie",e),d("AllowScriptAccess",c.allowScriptAccess),d("quality",h.quality),c.wmode?d("wmode",c.wmode):"",d("bgcolor",c.bgColor),d("hasPriority","true"),c.debugFlash?d("FlashVars",h.FlashVars):"","</object>"].join("");else for(j in a=l.createElement("embed"),h)h.hasOwnProperty(j)&&a.setAttribute(j,h[j]);ra();i=G();if(h=qa())if(c.oMC=T(c.movieID)||l.createElement("div"),c.oMC.id)m=c.oMC.className,c.oMC.className=
(m?m+" ":"movieContainer")+(i?" "+i:""),c.oMC.appendChild(a),z&&(j=c.oMC.appendChild(l.createElement("div")),j.className="sm2-object-box",j.innerHTML=n),K=!0;else{c.oMC.id=c.movieID;c.oMC.className="movieContainer "+i;j=i=null;c.useFlashBlock||(c.useHighPerformance?i={position:"fixed",width:"8px",height:"8px",bottom:"0px",left:"0px",overflow:"hidden"}:(i={position:"absolute",width:"6px",height:"6px",top:"-9999px",left:"-9999px"},k&&(i.left=Math.abs(parseInt(i.left,10))+"px")));eb&&(c.oMC.style.zIndex=
1E4);if(!c.debugFlash)for(m in i)i.hasOwnProperty(m)&&(c.oMC.style[m]=i[m]);try{z||c.oMC.appendChild(a),h.appendChild(c.oMC),z&&(j=c.oMC.appendChild(l.createElement("div")),j.className="sm2-object-box",j.innerHTML=n),K=!0}catch(p){throw Error(v("domError")+" \n"+p.toString());}}return J=!0};W=function(){if(c.html5Only)return X(),!1;if(h||!c.url)return!1;h=c.getMovie(c.id);h||(N?(z?c.oMC.innerHTML=ta:c.oMC.appendChild(N),N=null,J=!0):X(c.id,c.url),h=c.getMovie(c.id));"function"===typeof c.oninitmovie&&
setTimeout(c.oninitmovie,1);return!0};D=function(){setTimeout(Ja,1E3)};Ja=function(){var b,e=!1;if(!c.url||O)return!1;O=!0;n.remove(i,"load",D);if(da&&!Da)return!1;j||(b=c.getMoviePercent(),0<b&&100>b&&(e=!0));setTimeout(function(){b=c.getMoviePercent();if(e)return O=!1,i.setTimeout(D,1),!1;!j&&Wa&&(null===b?c.useFlashBlock||0===c.flashLoadTimeout?c.useFlashBlock&&ua():B({type:"ontimeout",ignoreInit:!0}):0!==c.flashLoadTimeout&&sa(!0))},c.flashLoadTimeout)};V=function(){if(Da||!da)return n.remove(i,
"focus",V),!0;Da=Wa=!0;O=!1;D();n.remove(i,"focus",V);return!0};L=function(b){if(j)return!1;if(c.html5Only)return j=!0,C(),!0;var e=!0,d;if(!c.useFlashBlock||!c.flashLoadTimeout||c.getMoviePercent())j=!0,s&&(d={type:!A&&u?"NO_FLASH":"INIT_TIMEOUT"});if(s||b)c.useFlashBlock&&c.oMC&&(c.oMC.className=G()+" "+(null===c.getMoviePercent()?"swf_timedout":"swf_error")),B({type:"ontimeout",error:d,ignoreInit:!0}),F(d),e=!1;s||(c.waitForWindowLoad&&!ja?n.add(i,"load",C):C());return e};Ia=function(){var b,e=
c.setupOptions;for(b in e)e.hasOwnProperty(b)&&(c[b]===g?c[b]=e[b]:c[b]!==e[b]&&(c.setupOptions[b]=c[b]))};ia=function(){if(j)return!1;if(c.html5Only)return j||(n.remove(i,"load",c.beginDelayedInit),c.enabled=!0,L()),!0;W();try{h._externalInterfaceTest(!1),Ka(!0,c.flashPollingInterval||(c.useHighPerformance?10:50)),c.debugMode||h._disableDebug(),c.enabled=!0,c.html5Only||n.add(i,"unload",ha)}catch(b){return F({type:"JS_TO_FLASH_EXCEPTION",fatal:!0}),sa(!0),L(),!1}L();n.remove(i,"load",c.beginDelayedInit);
return!0};E=function(){if(M)return!1;M=!0;Ia();ra();!A&&c.hasHTML5&&c.setup({useHTML5Audio:!0,preferFlash:!1});Sa();c.html5.usingFlash=Ra();u=c.html5.usingFlash;!A&&u&&(Qa.push(na.needFlash),c.setup({flashLoadTimeout:1}));l.removeEventListener&&l.removeEventListener("DOMContentLoaded",E,!1);W();return!0};xa=function(){"complete"===l.readyState&&(E(),l.detachEvent("onreadystatechange",xa));return!0};pa=function(){ja=!0;n.remove(i,"load",pa)};oa=function(){if(Ca&&(c.setupOptions.useHTML5Audio=!0,c.setupOptions.preferFlash=
!1,ca||Ua&&!q.match(/android\s2\.3/i)))ca&&(c.ignoreFlash=!0),w=!0};oa();za();n.add(i,"focus",V);n.add(i,"load",D);n.add(i,"load",pa);l.addEventListener?l.addEventListener("DOMContentLoaded",E,!1):l.attachEvent?l.attachEvent("onreadystatechange",xa):F({type:"NO_DOM2_EVENTS",fatal:!0})}var fa=null;if(void 0===i.SM2_DEFER||!SM2_DEFER)fa=new R;i.SoundManager=R;i.soundManager=fa})(window);