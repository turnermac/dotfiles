var $=function(id) { return document.getElementById(id); };

function upload() {
    $('progress').innerHTML="0%";
    $('progress').style.width="0%";
    $('note').style.display="none";

    var file=$('file').files[0];
    var formdata=new FormData();
    formdata.append('file',file);
    var ajax=new XMLHttpRequest();
    ajax.upload.addEventListener('progress',progressHandler,false);
    ajax.addEventListener('load',completeHandler,false);
    ajax.addEventListener('error',errorHandler,false);
    ajax.addEventListener('about',aboutHandler,false);
    ajax.open("POST","upload.php");
    ajax.send(formdata);
}

function progressHandler(event) {
    var percent=Math.round((event.loaded/event.total)*100);
    $('progress').innerHTML=percent+"%";
    $('progress').style.width=percent+"%";
}

function completeHandler(event) {
    $('progress').style.width="0%";
    $('note').style.display="inline-block";
    $('note').innerHTML=event.target.responseText;

    $('list').src=$('list').src;
}

function errorHandler(event) {
    $('progress').style.width="0%";
    $('note').style.display="inline-block";
    $('note').innerHTML="Upload failed!";
}

function aboutHandler(event) {
    $('progress').style.width="0%";
    $('note').style.display="inline-block";
    $('note').innerHTML="Upload aborted!";
}
