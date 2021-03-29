function Pager(tableName, itemsPerPage) {
this.tableName = tableName;
this.itemsPerPage = itemsPerPage;
this.currentPage = 1;
this.pages = 0;
this.inited = false;

this.showRecords = function(from, to) {
var rows = document.getElementById(tableName).rows;
// i starts from 1 to skip table header row
for (var i = 1; i < rows.length; i++) {
if (i < from || i > to)
rows[i].style.display = 'none';
else
rows[i].style.display = '';
}
}

this.showPage = function(pageNumber) {
if (! this.inited) {
alert("not inited");
return;
}

var oldPageAnchor = document.getElementById('pg'+this.currentPage);
oldPageAnchor.className = 'pg-normal';

this.currentPage = pageNumber;
var newPageAnchor = document.getElementById('pg'+this.currentPage);
newPageAnchor.className = 'pg-selected';

var from = (pageNumber - 1) * itemsPerPage + 1;
var to = from + itemsPerPage - 1;
this.showRecords(from, to);
}

this.prev = function() {
if (this.currentPage > 1)
this.showPage(this.currentPage - 1);
}

this.next = function() {
if (this.currentPage < this.pages) {
this.showPage(this.currentPage + 1);
}
}

this.init = function() {
var rows = document.getElementById(tableName).rows;
var records = (rows.length - 1);
this.pages = Math.ceil(records / itemsPerPage);
this.inited = true;
}

this.showPageNav = function(pagerName, positionId) {
if (! this.inited) {
alert("not inited");
return;
}
var element = document.getElementById(positionId);

var pagerHtml = '<span onclick="' + pagerName + '.prev();" style=\"cursor: pointer;\"> Anterior </span>';
var blocosPorLinha = 1;
var topx = 0;
var top = "";
var position = "";
for (var page = 1; page <= this.pages; page++) {
    if (blocosPorLinha!=20) {
        pagerHtml += '<span id="pg' + page + '" onclick="' + pagerName + '.showPage(' + page + ');" class=\"pg-normal\" style=\"'+position+top+'\">' + page + '</span>';
        blocosPorLinha++;
    } else {
        pagerHtml += '<span id="pg' + page + '" onclick="' + pagerName + '.showPage(' + page + ');" class=\"pg-normal\" style=\"'+position+top+'\">' + page + '</span><br>';
        blocosPorLinha=1;
        position = "position: relative; ";
        if (topx==0) topx = 8; else topx = (topx/2) + 8;
        top = "top: "+topx+"px; ";
    }
}
pagerHtml += '<span onclick="'+pagerName+'.next();" style=\"cursor: pointer; '+position+top+'\"> Próxima </span>';

element.innerHTML = pagerHtml;
}
}