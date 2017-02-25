var $=function(id) { return document.getElementById(id); };

var t,d=false;
function selectText(e,x) {

    clearTimeout(t);

    if(e.which==1) {
        switch(x) {
            case 1:
                d=true;
                t=setTimeout(function() { selectText(e,0); },200);
                break;
            case 2:
                if(d) {
                    if(document.selection) {
                        var range=document.body.createTextRange();
                        range.moveToElementText($('text'));
                        range.select();
                    } else if(window.getSelection) {
                        var range=document.createRange();
                        range.selectNode($('text'));
                        window.getSelection().addRange(range);
                    }
                }
                break;
            default:
                d=false;
        }
    }
}

function zindex(id) {
    $('text').style.zIndex=9;
    $('chat').style.zIndex=9;
    $(id).style.zIndex=10;
}

var n=0,fI,fT=false,fL=false;
window.addEventListener('message',function(event) {
    var data=event.data.split(" ");

    var title=document.title;
    var link=document.createElement('link');
    link.id="favicon";
    link.type="image/x-icon";
    link.rel="shortcut icon";

    if(data[0]=="notify") {
        if(!fL) {
            fL=true;

            fI=setInterval(function() {
                fT=!fT;

                if(fT) link.href="http://twily.info/favicon_red.ico";
                else   link.href="http://twily.info/favicon.ico";

                document.getElementsByTagName('head')[0].removeChild($('favicon'));
                document.getElementsByTagName('head')[0].appendChild(link);
            },500);
        }

        if(title.substr(0,1)!="(") {
            n=1;
        } else {
            n+=parseInt(data[1]);
            title=title.substr(title.indexOf(" ")+1);
        }
        document.title="("+n+") "+title;
    } else {
        clearInterval(fI);
        fT=false;
        fL=false;

        link.href="http://twily.info/favicon.ico";

        document.getElementsByTagName('head')[0].removeChild($('favicon'));
        document.getElementsByTagName('head')[0].appendChild(link);

        n=0;
        if(title.substr(0,1)=="(") document.title=title.substr(title.indexOf(" ")+1);
    }
});

window.addEventListener('scroll',function() {
    if($('text')) {
        var eT,wY;
        eT=$('text').offsetTop;
        eB=$('text').offsetTop+$('text').offsetHeight;
        wY=window.pageYOffset;
        wH=window.innerHeight;

        if(wY>eT) {
            $('top_arrow').style.display="block";
        } else {
            $('top_arrow').style.display="none";
        }
        if((wY+wH)<eB) {
            $('bot_arrow').style.display="block";
        } else {
            $('bot_arrow').style.display="none";
        }
    }
});

var rzC;
function loadCmt(x,f) {
    clearInterval(rzC);

    //$(x).contentWindow.postMessage(f,'*');

    rzC=setInterval(function() {
        //$(x).style.height=0;
        $(x).style.height=$(x).contentWindow.document.body.scrollHeight+"px";
    },100);
}


