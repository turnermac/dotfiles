var $=function(id) { return document.getElementById(id); };
var go=function(url) { window.parent.location.href=url; };

function init() {
    bottom();
    clear();
    fetch();
}

function checkNotification() {
    if(window.Notification && Notification.permission!=="denied") {
        Notification.requestPermission();
    }
}

var nShow;
function showNotification(nt,nc) {
    var nc1=nc.substr(nc.indexOf("<b>")+3,nc.indexOf("</b>")-nc.indexOf("<b>")-3);
    var nc2=nc.substr(nc.indexOf("</span>")+7,nc.indexOf("<br>")-nc.indexOf("</span>")-7);

    if(window.Notification && Notification.permission!=="denied") {
        Notification.requestPermission(function(status) {
            nShow=new Notification('Twily.info ('+nt+')', {
                body: nc1+' '+nc2,
                icon: '../favicon.ico'
            });
        });

        nShow.close();
    }
}

function bottom() {
    $('container').scrollTop=$('container').scrollHeight;
}

function clear() {
    if($('message')) {
        $('message').value="";
        $('message').focus();
    }
}

var tM,d=false;
function focusMsg(e,x) {
    clearTimeout(tM);

    if(e.which==1) {
        switch(x) {
            case 1:
                d=true;
                tM=setTimeout(function() { focusMsg(e,0); },200);
                break;
            case 2:
                if(d) { 
                    if($('message')) $('message').focus();
                    else             $('nick').focus();
                }
                break;
            default:
                d=false;
        }
    }
}

function reply(nick) {
    var elm;
    if($('message')) elm=$('message');
    else             elm=$('nick');

    var sPos=elm.selectionStart;
    var ePos=elm.selectionEnd;
    if(elm.value.substring(sPos-1,sPos)!=" ") nick=" "+nick;
    if(elm.value.substring(ePos,ePos+1)!=" ") nick+=" ";

    if(document.selection) {
        elm.focus();
        sel=document.selection.createRange();
        sel.text=nick;
    } else if(sPos!='0') {
        elm.value=elm.value.substring(0,sPos)+nick+elm.value.substring(ePos,elm.value.length);
    } else {
        elm.value=nick.substring(1,nick.length-1)+": "+elm.value;
    }
}

function q(cmd,auto) {
    var elm;
    if($('message')) elm=$('message');
    else             elm=$('nick');

    elm.focus();
    elm.value="/"+cmd;

    if(auto) post();
}

function handle(e) {
    if(e.keyCode==13) { //enter
        post();
        return false;
    }
}

var n=0;
function process(data){
    switch(true) {
        case /^success(.*)/.test(data):
            clear();
            break;
        case /^update(.*)/.test(data):
            window.location.href=window.location.href;
            break;
        case /^message(.*)/.test(data):
            $('log').innerHTML+=data.substr(7);
            bottom();

            n+=data.split("\n").length-1;
            if(document.body.className=="hidden") {
                showNotification(n,data.substr(7));
                parent.postMessage('notify '+n,'*');
            }
            n=0;
        default:
    }

    locked=false;
}

var t,remote="http://"+window.location.hostname+"/chat/";
function fetch() { // Update chat
    clearTimeout(t);

    var xhr;
    if(window.XMLHttpRequest) xhr=new XMLHttpRequest();
    else                      xhr=new ActiveXObject("Microsoft.XMLHTTP");

    xhr.onreadystatechange=function() {
        if(xhr.readyState==XMLHttpRequest.DONE) {
            if(xhr.responseText!="") process(xhr.responseText);
        }
    }

    xhr.open("GET",remote+"read.php",true);
    xhr.timeout=8*1000;
    xhr.send();

    t=setTimeout(function() { fetch(); },1000);
}

var locked=false;
function post() { // Send message
    if(!locked) {
        var i,xhr,message;

        if($('nick') && $('nick').value.indexOf("/")!=0) message="/nick "+$('nick').value;
        else if($('nick'))                               message=$('nick').value;
        else                                             message=$('message').value.replace(/\\/g,'\\\\');

        if(message!="") {
            locked=true;

            if(window.XMLHttpRequest) xhr=new XMLHttpRequest();
            else                      xhr=new ActiveXObject("Microsoft.XMLHTTP");

            xhr.onreadystatechange=function() {
                if(xhr.readyState==XMLHttpRequest.DONE) {
                    process(xhr.responseText);
                }
            }

            xhr.open("POST",remote+"post.php",true);
            xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
            xhr.send("send=&message="+encodeURIComponent(message));
        }
    }

    checkNotification();
}

var hidden="hidden";
if(hidden in document)                       document.addEventListener("visibilitychange",onchange);
else if((hidden="mozHidden") in document)    document.addEventListener("mozvisibilitychange",onchange);
else if((hidden="webkitHidden") in document) document.addEventListener("webkitvisibilitychange",onchange);
else if((hidden="msHidden") in document)     document.addEventListener("msvisibilitychange",onchange);
else if("onfocusin" in document)             document.onfocusin=document.onfocusout=onchange;
else                                         window.onpageshow=window.onpagehide=window.onfocus=window.onblur=onchange;
function onchange(e) {
    var v="visible",h="hidden",eMap={focus:v, focusin:v, pageshow:v, blur:h, focusout:h, pagehide:h};
    e=e || window.event;
    if(e.type in eMap) document.body.className=eMap[e.type];
    else               document.body.className=this[hidden]?"hidden":"visible";

    if(document.body.className=="visible") {
        parent.postMessage('dismiss','*');
        n=0;
        delay=1000;
    } else {
        delay=4000;
    }
}
if(document[hidden]!==undefined) onchange({type: document[hidden]?"blur":"focus"});
