window.onscroll = function (){
    pageScroll();
};
window.onload = function() {
    document.getElementById('hidebutton').style.visibility = 'hidden';
    getEvents();
    //showEvents();
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

function showEvents(rankedTeams) {
    let table = document.getElementById('table1');
    let tHead = document.createElement('thead');
    let tBody = document.createElement('tbody');
    let firstRow = document.createElement('tr');
    let td1 = document.createElement('td');
    td1.innerHTML = "Ranking";
    let td2 = document.createElement('td');
    td2.innerHTML = "Team name";
    let td3 = document.createElement('td');
    td3.innerHTML = "Odds";
    firstRow.appendChild(td1);
    firstRow.appendChild(td2);
    firstRow.appendChild(td3);
    tBody.appendChild(firstRow);

    for (let i = 0; i < rankedTeams.length; i++){
        let row = document.createElement('tr');
        let rankCell = document.createElement('td');
        rankCell.innerHTML = (i + 1).toString();
        row.appendChild(rankCell);
        let team = rankedTeams[i];
        for (let j = 0; j < 2; j++) {
            let cell = document.createElement('td');
            cell.innerHTML = team[Object.keys(team)[j]];
            row.appendChild(cell);
        }
        tBody.appendChild(row);
    }

    table.appendChild(tHead);
    table.appendChild(tBody);
}

let httpRequest;
function getEvents() {
    let url = "http://81.197.165.237/api/events";
    if (window.XMLHttpRequest) { // Mozilla, Safari, ...
        httpRequest = new XMLHttpRequest();
    } else if (window.ActiveXObject) { // IE
        try {
            httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch (e) {
            try {
                httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch (e) {
            }
        }
    }

    if (!httpRequest) {
        alert('Giving up :( Cannot create an XMLHTTP instance');
        return false;
    }

    // set a callback function for when the httpRequest completes
    httpRequest.onreadystatechange = alertContents;

    // now do the actual AJAX request
    httpRequest.open('GET', url);
    httpRequest.send();
}

function alertContents() {
    if (httpRequest.readyState === 4) {
        if (httpRequest.status === 200) {
            //alert(httpRequest.responseText);
            let eventArray = JSON.parse(httpRequest.responseText);
            let rankedTeams = rankTeams(eventArray);
            showEvents(rankedTeams);
        } else if (httpRequest.status === 404) {
            alert("Site is DOWN!");
        } else {
            alert('There was a problem with the request.');
            console.log(httpRequest.status);
        }
    }
}

function rankTeams(eventArray) {
    let teamNames = [];
    let teams = [];

    //Get a list of all the team names
    eventArray.forEach(function (event) {
        let homeTeamName = event["Name"].split(" v ")[0];
        let awayTeamName = event["Name"].split(" v ")[1];
        if (homeTeamName == undefined || awayTeamName == undefined) {
            console.log("flag");
        }
        if (!teamNames.includes(homeTeamName)) {
            teamNames.push(homeTeamName);
            let team = {name:homeTeamName, score:100};
            teams.push(team);
        }
        if (!teamNames.includes(awayTeamName)) {
            teamNames.push(awayTeamName);
            let team = {name:awayTeamName, score:100};
            teams.push(team);
        }
    });

    //assign score to each team
    //When the odds for a draw are lowest, neither team gets/loses points
    //The team with the lowest odds in an event gains points equal to the opponents winning odds.
    //The other team then loses points equal to their opponents winning odds.
    eventArray.forEach(function (event) {
        let homeTeamName = event["Name"].split(" v ")[0];
        let awayTeamName = event["Name"].split(" v ")[1];
        let homeWinOdds = parseFloat(event["HomeTeam"]);
        let drawOdds = parseFloat(event["Draw"]);
        let awayWinOdds = parseFloat(event["AwayTeam"]);
        if (drawOdds > homeWinOdds || drawOdds > awayWinOdds) {
            teams.forEach(function (team) {
                if (team.name === homeTeamName) {
                    if (homeWinOdds > awayWinOdds) {
                        team.score -= awayWinOdds;
                    } else {
                        team.score += awayWinOdds;
                    }
                } else if (team.name === awayTeamName) {
                    if (homeWinOdds > awayWinOdds) {
                        team.score += homeWinOdds;
                    } else {
                        team.score -= homeWinOdds;
                    }
                }
            });
        }
    });
    //Order teams so that the team with the highest score is in index 0.
    teams.sort(function (a, b) {
		if (a.score > b.score) {
			return -1;
		} else if (a.score < b.score) {
			return 1;
		}
		return 0;
    });
    let lowestScore = teams[teams.length - 1].score;
    teams.forEach(function (team) {
       team.score -= lowestScore;
       team.score = Math.round(team.score * 100) / 100
    });
    return teams;
}

var ypos;
var image;
window.addEventListener('scroll', function() {
    ypos = window.pageYOffset;
    image = document.getElementById('centerblock');
    image.style.top = ypos * 0.6 + 'px';
});

