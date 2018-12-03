<?php
/**
 * Created by PhpStorm.
 * User: miikk
 * Date: 11/22/2018
 * Time: 2:19 PM
 */
    //returns the sessionToken on login success or null on login failure
    function login($username, $password, $appKey) {
        $ch = curl_init();
        $crtPath = "C:\cert\client-2048.pem";
        curl_setopt($ch, CURLOPT_SSLCERT, $crtPath);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, "https://identitysso-cert.betfair.com/api/certlogin");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:',
            'X-Application: ' . $appKey,
            'Content-Type: application/x-www-form-urlencoded'
        ));
        $postData = "username=" . $username . "&password=" . $password;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $response = json_decode(curl_exec($ch), true);
        /*if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }*/
        curl_close($ch);
        if (isset($response["sessionToken"])) {
            $sessionToken = $response["sessionToken"];
            return $sessionToken;
        }
        echo "Login failed. Error: " . $response["loginStatus"];
        return null;
    }

    function sportsApingRequest($appKey, $sessionToken, $operation, $params) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.betfair.com/exchange/betting/rest/v1/$operation/");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:',
            'X-Application: ' . $appKey,
            'X-Authentication: ' . $sessionToken,
            'Accept: application/json',
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        $response = json_decode(curl_exec($ch));
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_status == 200) {
            return $response;
        } else {
            echo 'Call to api-ng failed: ' . "\n";
            echo  'Response: ' . json_encode($response) . ", HTTP-code: " . $http_status;
            exit(-1);
        }
    }

    //returns an array of nested objects which contain event type names, market counts and ids.
    //example: [0]=> object(stdClass) (2) { ["eventType"]=> object(stdClass) (2) { ["id"]=> string(1) "1" ["name"]=> string(6) "Soccer" } ["marketCount"]=> int(23911) }
    function getAllEventTypes($appKey, $sessionToken) {
        $jsonResponse = sportsApingRequest($appKey, $sessionToken, 'listEventTypes', '{"filter":{}}');
        return $jsonResponse;
    }

    //returns an array of event-objects that have ids, names, country codes, timezones and starting times.
    //!Issues with time control!
    function getEventsInEventType($appKey, $sessionToken, $eventTypeId, $countryCode, $competitionId, $startingDate, $finalDate) {
        $events = array();
        $filter = '{"filter":{"eventTypeIds":["' . strval($eventTypeId) . '"],
                    "competitionIds":["' . $competitionId . '"],
                    "bspOnly":"false",
                    "inPlayOnly":"false",
                    "marketCountries":["' . $countryCode . '"],
                    "marketTypeCodes":["MATCH_ODDS"],
                    "marketStartTime":{"from":"' . date('c', $startingDate) . '", "to":"' . date('c', $finalDate) . '"}}}';
        $jsonResponse = sportsApingRequest($appKey, $sessionToken, "listEvents", $filter);
        foreach ($jsonResponse as $item) {
            $event = $item->event;
            array_push($events, $event);
        }
        return $events;
    }

    //Returns an array of competitions that have ids and names.
    //In football competitions are leagues and cups.
    //Excludes competitions that don't have a "Match odds" market.
    function getCompetitionsInEventType($appKey, $sessionToken, $eventTypeId, $countryCode, $startingDate, $finalDate) {
        $competitions = array();
        $filter = '{"filter":{"eventTypeIds":["' . strval($eventTypeId) . '"],
                    "bspOnly":"false",
                    "inPlayOnly":"false",
                    "marketCountries":["' . $countryCode . '"],
                    "marketTypeCodes":["MATCH_ODDS"],
                    "marketStartTime":{"from":"' . date('c', $startingDate) . '", "to":"' . date('c', $finalDate) . '"}}}';
        $jsonResponse = sportsApingRequest($appKey, $sessionToken, "listCompetitions", $filter);
        foreach ($jsonResponse as $item) {
            $competition = $item->competition;
            array_push($competitions, $competition);
        }
        return $competitions;
    }

    //returns an array with all the event names
    function getAllEventTypeNames($appKey, $sessionToken) {
        $eventTypes = getAllEventTypes($appKey, $sessionToken);
        $eventNames = array();
        foreach ($eventTypes as $type) {
            array_push($eventNames, $type->eventType->name);
        }
        return $eventNames;
    }

    //returns the specified EventType's ID or null if no EventType has the specified name.
    function getEventTypeId($appKey, $sessionToken, $eventTypeName) {
        $eventTypes = getAllEventTypes($appKey, $sessionToken);
        foreach ($eventTypes as $type) {
            if ($type->eventType->name === $eventTypeName) {
                return $type->eventType->id;
            }
        }
        echo "Tried to get an ID from an EventType that does not exist.";
        return null;
    }

    //Returns the market id for the specified event.
    //The event must have "MATCH_ODDS"-market.
    function getMarketId($appKey, $sessionToken, $eventId) {
        $filter = '{"filter":{"eventIds":["' . $eventId . '"], "marketTypeCodes":["MATCH_ODDS"]}, 
                    "marketProjection":["MARKET_DESCRIPTION"],  
                    "maxResults":"1"}';
        $jsonResponse = sportsApingRequest($appKey, $sessionToken, 'listMarketCatalogue', $filter);
        if (isset($jsonResponse[0])) {
            return $jsonResponse[0]->marketId;
        } else {
            echo "Failed to get the events marketID";
            return null;
        }
    }

    //Returns the event name
    function getEventName($appKey, $sessionToken, $eventId) {
        $filter = '{"filter":{"eventIds":["' . $eventId . '"], "marketTypeCodes":["MATCH_ODDS"]}, 
                    "marketProjection":["EVENT"],  
                    "maxResults":"1"}';
        $jsonResponse = sportsApingRequest($appKey, $sessionToken, 'listMarketCatalogue', $filter);
        return $jsonResponse[0]->event->name;
    }

	//Returns the event date
    function getEventDate($appKey, $sessionToken, $eventId) {
        $filter = '{"filter":{"eventIds":["' . $eventId . '"], "marketTypeCodes":["MATCH_ODDS"]}, 
                    "marketProjection":["EVENT"],  
                    "maxResults":"1"}';
        $jsonResponse = sportsApingRequest($appKey, $sessionToken, 'listMarketCatalogue', $filter);
        return $jsonResponse[0]->event->openDate;
    }
    //Returns the event name and -odds in an array
    function getEventOdds($appKey, $sessionToken, $marketId, $eventId) {
        $odds = array();
        $params = '{"marketIds":["' . $marketId . '"],
                    "priceProjection":{"priceData":["EX_BEST_OFFERS"], "exBestOffersOverrides":{"bestPricesDepth": "1"}},
                    "includeOverallPosition":"false"}';
        $jsonResponse = sportsApingRequest($appKey, $sessionToken, 'listMarketBook', $params);
        $runners = $jsonResponse[0]->runners;
        $odds["Name"] = getEventName($appKey, $sessionToken, $eventId);
		$odds["Date"] = getEventDate($appKey, $sessionToken, $eventId);
        $odds["HomeTeam"] = $runners[0]->ex->availableToBack[0]->price;
        $odds["AwayTeam"] = $runners[1]->ex->availableToBack[0]->price;
        $odds["Draw"] = $runners[2]->ex->availableToBack[0]->price;
		$odds["id"] = $eventId;
        return $odds;
    }


    //final values
    $appKey = "u3F8sMbhV3UEoJ5K";
    $username = "miikko";
    $password = "teamsix6";
    $competitionId = "117"; //Spanish La Liga
    $countryCode = "ES"; //Spain's country code
    $eventTypeName = "Soccer";

    //Example use:

    //1) Perform a anonymous login to get a sessionToken
    $sessionToken = login($username, $password, $appKey);

    //2) Choose a time range from which events are chosen
    $now = time();
    $nextWeek = time(); + 7 * 24 * 60 * 60;

    //3) Choose an eventTypeName from the array received from the getAllEventTypeNames()-function
    //In this example the name has already been chosen: '$eventTypeName = "Soccer"'

    //4) Choose a competition from the array received from the getCompetitionsInEventType()-function
    //Then store its id in a variable
    //In this example the competition has already been chosen: 'competitionId = "117"'

    //5) Get the eventTypeId using the eventTypeName
    $eventTypeId = getEventTypeId($appKey, $sessionToken, $eventTypeName);

    //6) Choose an event from an an array received from the getEventsInEventType-function
    //Then store its Id in a variable
    $events = getEventsInEventType($appKey, $sessionToken, $eventTypeId, $countryCode, $competitionId, $now, $nextWeek);
    //$eventId = $events[0]->id;
	$resultArray = array();
	foreach($events as $event){
		$eventId = $event->id;
		$marketId = getMarketId($appKey,$sessionToken,$eventId);
		$resultArray[] = getEventOdds($appKey, $sessionToken, $marketId, $eventId);
	}

    //7) Get the marketId
    //Events can have multiple markets, for example "Match winner", "End result"
    //getMarketId()-function looks for "End result" markets
    //$marketId = getMarketId($appKey, $sessionToken, $eventId);

    //8) Get the event odds and name in an associative array
    //var_dump(getEventOdds($appKey, $sessionToken, $marketId, $eventId));
	$connect = mysqli_connect("localhost", "eero", "eero", "betfair");
	if ($connect->connect_error) {
		die("Connection failed: " . $connect->connect_error);
	} 
	
	foreach($resultArray as $row) {
		
		//Modify Date format to SQL DATETIME() format
		$row["Date"] = preg_replace("/[^T0-9-:.]/", "", $row["Date"]);
		$row["Date"] = preg_replace("/[^0-9-:.]/", " ", $row["Date"]);
		$row["Date"] = str_replace(".000", "", $row["Date"]);
		
		$query = "INSERT INTO events (id, Name, HomeTeam, AwayTeam, Draw, Date)
		VALUES('".$row["id"]."', '".$row["Name"]."', '".$row["HomeTeam"]."', '".$row["AwayTeam"]."', '".$row["Draw"]."', '".$row["Date"]."')
		ON DUPLICATE KEY UPDATE HomeTeam=VALUES(HomeTeam), AwayTeam=VALUES(AwayTeam), Draw=VALUES(Draw)";
		
		mysqli_query($connect, $query);
	}
	

    //Check if the values are about the same in this website: https://www.betfair.com/sport/football
    //Select Spanish La Liga as the league
    //The values received from the Betfair API are delayed so they aren't exactly the same.