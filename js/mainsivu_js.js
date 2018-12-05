window.addEventListener('load', function() {
    document.getElementById('hidebutton').style.visibility = 'hidden';
    getComments();
    getAllEvents();
});

function easterEgg() {
    alert('You found Easter egg!');
}

function tableCreate() {
    document.getElementById('hidebutton').style.visibility = 'visible';
    let upcomingEvents = [];
    eventArray.forEach(function (event) {
        if (new Date(event["Date"]).getTime() > Date.now()) {
            upcomingEvents.push(event);
        }
    });
    let place = document.getElementById('tablespace');
    let tbl = document.createElement('table');
    tbl.setAttribute('border', '1');
    let tBody = document.createElement('tbody');
    let td1 = document.createElement('td');
    let td2 = document.createElement('td');
    let td3 = document.createElement('td');
    let td4 = document.createElement('td');
    let td5 = document.createElement('td');
    let td6 = document.createElement('td');
    td1.innerHTML = "Date";
    td2.innerHTML = "Home team";
    td3.innerHTML = "Away team";
    td4.innerHTML = "Home win factor";
    td5.innerHTML = "Draw factor";
    td6.innerHTML = "Away win factor";
    tBody.appendChild(td1);
    tBody.appendChild(td2);
    tBody.appendChild(td3);
    tBody.appendChild(td4);
    tBody.appendChild(td5);
    tBody.appendChild(td6);
    for (let i = 0; i < upcomingEvents.length; i++) {
        let tr = document.createElement('tr');
        let homeTeamName = upcomingEvents[i]["Name"].split(" v ")[0];
        let awayTeamName = upcomingEvents[i]["Name"].split(" v ")[1];
        let formattedEvent = [upcomingEvents[i]["Date"], homeTeamName, awayTeamName, upcomingEvents[i]["HomeTeam"], upcomingEvents[i]["Draw"], upcomingEvents[i]["AwayTeam"]];
        for (let j = 0; j < formattedEvent.length; j++) {
            let td = document.createElement('td');
            td.innerHTML = formattedEvent[j];
            tr.appendChild(td);
        }
        tBody.appendChild(tr);
    }
    tbl.appendChild(tBody);
    place.appendChild(tbl);
    let button = document.getElementById('eventbutton');
    button.parentNode.removeChild(button);
}

function hideEvents() {
    let x = document.getElementById('tablespace');
    if (x.style.display === 'none') {
        document.getElementById('hidebutton').innerHTML = 'Hide matches';
        x.style.display = 'block';
    } else {
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
    td3.innerHTML = "Score";
    firstRow.appendChild(td1);
    firstRow.appendChild(td2);
    firstRow.appendChild(td3);
    tBody.appendChild(firstRow);

    for (let i = 0; i < rankedTeams.length; i++) {
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

function compareTeams() {
    let form = document.forms["comparison"];
    console.log(form.children[0].value);
    let team1Name = form["Team1"].value;
    let team2Name = form["Team2"].value;
    let team1WinOdds = 0;
    let drawOdds = 0;
    let team2WinOdds = 0;
    let eventCount = 0;
    eventArray.forEach(function (event) {
        let homeTeamName = event["Name"].split(" v ")[0];
        let awayTeamName = event["Name"].split(" v ")[1];
        if ((homeTeamName === team1Name || homeTeamName === team2Name) && (awayTeamName === team1Name || awayTeamName === team2Name)) {
            if (team1Name === homeTeamName) {
                team1WinOdds += event["HomeTeam"];
                team2WinOdds += event["AwayTeam"];
            } else {
                team2WinOdds += event["HomeTeam"];
                team1WinOdds += event["AwayTeam"];
            }
            drawOdds += event["Draw"];
            eventCount++;
        }
    });

    if (eventCount > 0) {
        team1WinOdds /= eventCount;
        drawOdds /= eventCount;
        team2WinOdds /= eventCount;
        team1WinOdds = Math.round(team1WinOdds * 100) / 100;
        drawOdds = Math.round(drawOdds * 100) / 100;
        team2WinOdds = Math.round(team2WinOdds * 100) / 100;
        let header = document.getElementById("comparisonResult");
        header.innerText = team1Name + " Winning Factor: " + team1WinOdds + ", Draw Factor: " + drawOdds + ", " + team2Name + " Winning Factor: " + team2WinOdds;
    } else {
        alert("Either you wrote invalid team name(s) or there are no records between these teams.")
    }
}

let eventArray;
let teams;

function getAllEvents() {
    let httpRequest;
    let url = "http://127.0.0.1/api/events";
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

    function alertContents() {
        if (httpRequest.readyState === 4) {
            if (httpRequest.status === 200) {
                eventArray = JSON.parse(httpRequest.responseText);
                let rankedTeams = rankTeams();
                showEvents(rankedTeams);
            } else if (httpRequest.status === 404) {
                alert("Site is DOWN!");
            } else {
                alert('There was a problem with the request.');
                console.log(httpRequest.status);
            }
        }
    }
}


function rankTeams() {
    let teamNames = [];
    teams = [];

    //Get a list of all the team names
    eventArray.forEach(function (event) {
        let homeTeamName = event["Name"].split(" v ")[0];
        let awayTeamName = event["Name"].split(" v ")[1];
        if (!teamNames.includes(homeTeamName)) {
            teamNames.push(homeTeamName);
            let team = {name: homeTeamName, score: 100};
            teams.push(team);
        }
        if (!teamNames.includes(awayTeamName)) {
            teamNames.push(awayTeamName);
            let team = {name: awayTeamName, score: 100};
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
    //Remove the lowest score from all scores so the ranking looks cleaner
    let lowestScore = teams[teams.length - 1].score;
    teams.forEach(function (team) {
        team.score -= lowestScore;
        team.score = Math.round(team.score * 100) / 100
    });
    return teams;
}

function postComment() {
    let httpRequest;
    let name = document.getElementById('form-name').value;
    let message = document.getElementById('form-message').value;
    let url = "http://127.0.0.1/api/comments";
 
	if (document.getElementById('image-url').value){
		var data = JSON.stringify({"name": name, "message": message, "image_url": document.getElementById('image-url').value});
	} else {
		var data = JSON.stringify({"name": name, "message": message});
	}
	

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

    httpRequest.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 201) {
            let result = JSON.parse(this.responseText);
            alert(result.message);
            window.location.reload();
        }
    };

	httpRequest.open("POST",url, true);
    httpRequest.send(data);
}
function getComments() {
    let httpRequest;
    let url = "http://127.0.0.1/api/comments";
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

    httpRequest.onreadystatechange = function() {
        if (this.readyState === 4 && this.status === 200) {
            //alert(this.responseText);
            let arr = JSON.parse(this.responseText);

            let comments = document.getElementById('comments');

            arr.forEach(function (comment) {
                let name = comment["name"];

                let time = comment["time_stamp"];
                time = mysqlTimeStampToDate(time);
                let current_time = new Date();
                time = calculateTimeDifference(time, current_time);

                let image_url = comment["image_url"];
                let message = comment["message"];

                let this_comment = document.createElement("div");
                this_comment.className = "comment";
                let row = document.createElement("span");
                row.className = "comment-header";
                let name_text = document.createElement("div");
                name_text.className = "comment-name";
                name_text.innerHTML = name;
                let time_text = document.createElement("div");
                time_text.className = "comment-time";
                time_text.innerHTML = time;
                row.appendChild(name_text);
                row.appendChild(time_text);
                this_comment.appendChild(row);

                let message_box = document.createElement("div");
                let message_text = document.createElement("div");
                message_box.className = "comment-message-box";
                if(image_url){
                    let image = document.createElement("div");
                    image.className = "comment-image";
                    image.innerHTML = "<img src='"+image_url+"' style='max-width: 120px'>";
                    message_box.appendChild(image);

                }


                message_text.className = "comment-message";
                message_text.innerHTML = message;
                message_box.appendChild(message_text);
                this_comment.appendChild(message_box);
                comments.appendChild(this_comment);

            });
        }
    };

    // now do the actual AJAX request
    httpRequest.open('GET', url);
    httpRequest.send();
}


function mysqlTimeStampToDate(timestamp) {
    //function parses mysql datetime string and returns javascript Date object
    //input has to be in this format: 2007-06-05 15:26:02
    let regex=/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/;
    let parts=timestamp.replace(regex,"$1 $2 $3 $4 $5 $6").split(' ');
    return new Date(parseInt(parts[0]),parts[1]-1,parseInt(parts[2]),parseInt(parts[3]),parseInt(parts[4]),parseInt(parts[5]));
}

function calculateTimeDifference(old_time, current_time) {
    let time_difference = Math.abs(old_time - current_time);
    let days = time_difference/ (1000 * 3600 * 24);
    if(days < 1){
        let hours = time_difference / 36e5;
        if(hours < 1) {
            let minutes = ((time_difference % 86400000) % 3600000) / 60000;
            if (minutes === 1){
                return " minute ago";
            } else if (minutes < 1) {
                return " less than minute ago"
            } else {
                return Math.round(minutes) + " minutes ago";
            }

        } else {
            if(Math.round(hours) === 1){
                return "1 hour ago"

            } else {
                return Math.round(hours) + " hours ago";
            }

        }
    } else {
        return Math.ceil(days) + "days ago";
    }
}