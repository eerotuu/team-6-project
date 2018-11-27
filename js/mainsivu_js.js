window.onscroll = function (){
	pageScroll();
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




