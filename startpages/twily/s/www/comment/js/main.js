var $=function(id) { return document.getElementById(id); };

var cmtInput,cmtVar;
function setInput() {
    cmtInput="<div class=\"cmt-frame\" id=\"commentInput\"><br /> \
"+  "<input type=\"text\" value=\""+lName+"\" placeholder=\"Anonymous\" id=\"cmtName\" class=\"cmt-txt\" maxlength=\""+nMax+"\" /><br /> \
"+  "<textarea placeholder=\"Write a comment...\" id=\"cmtText\" class=\"cmt-txt\" maxlength=\""+tMax+"\"></textarea><br /> \
"+  "<a href=\"javascript:postComment("+cmtVar[2]+");\" target=\"_self\">"+cmtVar[0]+"</a>";

    if(cmtVar[1]!="") cmtInput+="<a href=\"javascript:cmtCancel();\" target=\"_self\">"+cmtVar[1]+"</a>";
    cmtInput+="<br /><br /></div>";
}

function rmInput() {
    if($('commentInput')) {
        var elm=$('commentInput');
        elm.parentNode.removeChild(elm);
    }
}

function cmtCancel() {
    rmInput();
    cmtVar=["Post comment","",-1];
    setInput();
    $('comments').innerHTML=cmtInput+$('comments').innerHTML;
    //$('cmtText').focus();
}

function cmtReply(x) {
    rmInput();
    cmtVar=["Post reply","Cancel reply",x];
    setInput();
    $('comment'+x).innerHTML+=cmtInput;
    $('cmtText').focus();
}

function postComment(x) {
    var form=document.createElement('form');
    form.setAttribute("id","postSubmit");
    form.setAttribute("action","./?f="+cid);
    form.setAttribute("method","post");
    form.setAttribute("target","_self");

    var data={
        name:       $('cmtName').value,
        text:       $('cmtText').value,
        post:       x
    };

    for(key in data) {
        var hidden=document.createElement('input');
        hidden.setAttribute("type","hidden");
        hidden.setAttribute("name",key);
        hidden.setAttribute("value",data[key]);

        form.appendChild(hidden);
    }

    document.body.appendChild(form);
    form.submit();
}

function loadComments() {
    for(var i=0;i<comments.length;i++) {
        var time=convT(comments[i][0]);
        var name=comments[i][2];
        var text=comments[i][3];

        if(name.substr(0,1)=="@") name="<span class=\"admin\">"+name+"</span>";

        var lText=text.split("<br />");
        text="";
        var tC=0,tCut=false;
        for(j=0;j<lText.length;j++) {
            tC+=lText[j].length;

            if(tC>tLimit) {
                text+=lText[j].substr(0,(lText[j].length-(tC-tLimit)))+"<br />";
                tCut=true;
                break;

            } else if(j>lLimit) {
                tCut=true;
                break;
            } else {
                text+=lText[j]+"<br />";
            }
        }
        if(tCut) {
            text+="... <a href=\"javascript:showMore("+i+");\" target=\"_self\">Show more</a>";
        }

        var printCmt="<div class=\"cmt-frame\" id=\"comment"+comments[i][0]+"\"> \
"+  "<div class=\"cmt-title\"><span class=\"name\">"+name+"</span><span class=\"time\">"+time+"</span>";
//"+  "<div class=\"cmt-title\"><a href=\"\" target=\"_self\" class=\"hide\">[-]</a> \

        //if(comments[i][1]!=-1) printCmt+="<a href=\"#comment"+comments[i][1]+"\" target=\"_self\">^</a> ";

        //printCmt+="<a href=\"#comment"+comments[i][0]+"\" target=\"_self\">#</a> \
        printCmt+="<a href=\"javascript:cmtReply("+comments[i][0]+");\" target=\"_self\">Reply</a> \
"+  "</div> \
"+  "<div class=\"cmt-content\" id=\"cmtI"+i+"\">"+text+"</div> \
"+  "</div>";

        if(comments[i][1]==-1) {
            $('comments').innerHTML=printCmt+$('comments').innerHTML;
        } else {
            $('comment'+comments[i][1]).innerHTML+=printCmt;
        }
    }
}

function showMore(x) {
    $('cmtI'+x).innerHTML=comments[x][3]+"<br /> \
"+  "<a href=\"javascript:showLess("+x+");\" target=\"_self\">Show less</a>";
}

function showLess(x) {
    var lText=comments[x][3].split("<br />");
    var text="";
    var tC=0;
    for(j=0;j<lText.length;j++) {
        tC+=lText[j].length;

        if(tC>tLimit) {
            text+=lText[j].substr(0,(lText[j].length-(tC-tLimit)))+"<br />";
            break;

        } else if(j>lLimit) {
            break;
        } else {
            text+=lText[j]+"<br />";
        }
    }
    $('cmtI'+x).innerHTML=text+"... <a href=\"javascript:showMore("+x+");\" target=\"_self\">Show more</a>";
}

function convT(x) {
    var t=tid-Math.floor(x/1000);
    var mT="";

    if(t>=60*60*24*7*52)   { mT=Math.floor(t/52/7/24/60/60)+" year"; }
    else if(t>=60*60*24*7) { mT=Math.floor(t/7/24/60/60)+" week"; }
    else if(t>=60*60*24)   { mT=Math.floor(t/24/60/60)+" day"; }
    else if(t>=60*60)      { mT=Math.floor(t/60/60)+" hour"; }
    else if(t>=60)         { mT=Math.floor(t/60)+" minute"; }
    else                   { mT=t+" second"; }

    mT+=(mT.substr(0,mT.indexOf(" "))!=1)?"s ago":" ago";
    return mT;
}

function init() {
    loadComments();
    cmtCancel();
}
