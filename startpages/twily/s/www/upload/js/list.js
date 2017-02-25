var $=function(id) { return document.getElementById(id); };

var mimg=["application.png","archive.png","audio.png","image.png","text.png","unknown.png","url.png","video.png"];

function listFiles() {
    $('fList').innerHTML="<div class=\"tbl-tr nohover\"> \
"+      "<div class=\"tbl-td\"><a href=\"javascript:resort('name');\" target=\"_self\">Name</a></div> \
"+      "<div class=\"tbl-td fr\"><a href=\"javascript:resort('size');\" target=\"_self\">Size</a></div> \
"+      "<div class=\"tbl-td\"><a href=\"javascript:resort('age');\" target=\"_self\">Age</a></div> \
"+      "<div class=\"tbl-td fr\">DL</div> \
"+      "<div class=\"tbl-td fr\">RM</div> \
"+      "</div>";

    for(var i=0;i<fList.length;i++) {
        var name=fList[i][0]
        if(name.length>32) name=name.substr(0,32)+"...";

        $('fList').innerHTML+="<div class=\"tbl-tr\"> \
"+      "<div class=\"tbl-td\"><img src=\"img/"+fList[i][3]+"\" class=\"icon\" /><a href=\""+path+fList[i][0]+"\" target=\"_blank\">"+name+"</a></div> \
"+      "<div class=\"tbl-td fr\">"+fList[i][2]+"</div> \
"+      "<div class=\"tbl-td\">"+fList[i][1]+"</div> \
"+      "<div class=\"tbl-td fr\"><a href=\"list.php?download="+encodeURIComponent(fList[i][0])+"\" target=\"_self\">[v]</a></div> \
"+      "<div class=\"tbl-td fr\"><a href=\"list.php?delete="+encodeURIComponent(fList[i][0])+"\" target=\"_self\">[x]</a></div> \
"+      "</div>";
    }
}

var sType="";
function resort(x) {
    switch(x) {
        case "name":
            if(sType=="name") {
                fList.sort(function(a,b) { if(a[0]<b[0]) return 1; if(a[0]>b[0]) return -1; });
                sType="rname";
            } else {
                fList.sort(function(a,b) { if(a[0]<b[0]) return -1; if(a[0]>b[0]) return 1; });
                sType="name";
            }
            break;
        case "age":
            if(sType=="age") {
                fList.sort(function(a,b) { if(a[4]<b[4]) return -1; if(a[4]>b[4]) return 1; });
                sType="rage";
            } else {
                fList.sort(function(a,b) { if(a[4]<b[4]) return 1; if(a[4]>b[4]) return -1; });
                sType="age";
            }
            break;
        case "size":
            if(sType=="size") {
                fList.sort(function(a,b) { if(a[5]<b[5]) return -1; if(a[5]>b[5]) return 1; });
                sType="rsize";
            } else {
                fList.sort(function(a,b) { if(a[5]<b[5]) return 1; if(a[5]>b[5]) return -1; });
                sType="size";
            }
            break;
    }

    listFiles();
}

