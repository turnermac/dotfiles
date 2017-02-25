var $=function(id) { return document.getElementById(id); };

function clear() {
    $('textA').value="";
    $('textA').focus();
}

function process(data) {
    switch(true) {
        case /^success(.*)/.test(data):
            clear();
            break;
        case /^update(.*)/.test(data):
            window.location.href=window.location.href;
            break;
        case /^message(.*)/.test(data):
            $('text').innerHTML=data.substr(7)+$('text').innerHTML;
            break;
        default:
    }

    locked=false;
}

var t,url=window.location.href.split("/");
var remote=url[0]+"//"+url[2]+"/";
function fetch() {
    clearTimeout(t);

    var xhr;
    if(window.XMLHttpRequest) xhr=new XMLHttpRequest();
    else                      xhr=new ActiveXObject("Microsoft.XMLHTTP");

    xhr.onreadystatechange=function() {
        if(xhr.readyState==XMLHttpRequest.DONE) {
            if(xhr.responseText!="") process(xhr.responseText);
        }
    }

    xhr.open("GET",remote+"handle.php",true);
    xhr.timeout=8*1000;
    xhr.send();

    t=setTimeout(function() { fetch(); },1000);
}

var locked=false;
function post() {
    if(!locked) {
        var i,xhr,message;

        message=$('textA').value;

        if(message!="") {
            locked=true;

            if(window.XMLHttpRequest) xhr=new XMLHttpRequest();
            else                      xhr=new ActiveXObject("Microsoft.XMLHTTP");

            xhr.onreadystatechange=function() {
                if(xhr.readyState==XMLHttpRequest.DONE) {
                    process(xhr.responseText);
                }
            }

            xhr.open("POST",remote+"handle.php",true);
            xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
            xhr.send("send=&message="+encodeURIComponent(message));
        }
    }
}
