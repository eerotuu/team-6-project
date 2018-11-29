window.onscroll = function (){
	pageScroll();
};
window.onload = function() {
	document.getElementById('hidebutton').style.visibility = 'hidden';
	showEvents();
};
var navbar = document.getElementsByClassName('topnav');
var sticky = navbar.offsetTop;

function pageScroll() {
	if (window.pageYOffset >= sticky) {
		navbar.classList.add('sticky');
	} else {
		navbar.classList.remove('sticky');
	}
}

function easterEgg() {
	alert('You found Easter egg!');
}

function tableCreate() {
	document.getElementById('hidebutton').style.visibility = 'visible';
	var place = document.getElementById('tablespace');
	var tbl = document.createElement('table');
	tbl.style.width = '20%';
	tbl.setAttribute('border', '1');
	var tbdy = document.createElement('tbody');
	for (var i = 0; i < 1; i++) {
		var tr = document.createElement('tr');
		for (var j = 0; j < 2; j++) {
			if (i === 3 && j === 3) {
				break;
			} else {
				var td = document.createElement('td');
				td.appendChild(document.createTextNode('testi'));
				tr.appendChild(document.createTextNode('Event: '));
				tr.appendChild(td);
			}
		}
		tbdy.appendChild(tr);
	}
	tbl.appendChild(tbdy);
	place.appendChild(tbl);
	var button = document.getElementById('eventbutton');
	button.parentNode.removeChild(button);
}
function hideEvents() {
	var x = document.getElementById('tablespace');
	if(x.style.display === 'none'){
		document.getElementById('hidebutton').innerHTML = 'Hide matches';
		x.style.display = 'block';
	}else {
		document.getElementById('hidebutton').innerHTML = 'Show matches';
		x.style.display = 'none';
	}
}

function showEvents() {
	var table = document.getElementById('myTable');
	for(var i = 0; i<10; i++){
		var row = table.insertRow(0);
		var cell1 = row.insertCell(0);
		cell1.innerHTML = 'Eventin nimi';
	}
}


var ypos;
var image;
window.addEventListener('scroll', function() {
	ypos = window.pageYOffset;
	image = document.getElementById('centerblock');
	image.style.top = ypos * 0.8 + 'px';
});

