#Rest API Documentation
This document purpose is ..?

## URL
81.197.165.237/api

## Methods
Resource        | Allowed Methods
------------    | -------------
/events         | GET
/comments       | GET, POST
    
## Result types
All results are returned in JSON format.

## Show Events

**List all events:** `/api/events`

**Search events by name**    `/api/events?name=barcelona`
  
### Success responses
* **Code:** 200 - OK
    * **Example content:**   
        ```
        {
            "id": "29010354",
            "Name": "Barcelona v Villarreal",
            "HomeTeam": "1.26",
            "AwayTeam": "13.5",
            "Draw": "7",
            "Date": "2018-12-02 17:30:00"
        }
        ```
    * **If data not exist:**   `Data Not Found`
         

### Error responses
* **Code:** 405 - Method not allowed
    * **Content:** `Allow: Get`
### Sample Call
**AJAX**
   ```javascript
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
    
httpRequest.onreadystatechange = alertContents;
httpRequest.open('GET', url);
httpRequest.send();

function alertContents() {
    if (httpRequest.readyState === 4) {
        if (httpRequest.status === 200) {
            alert(httpRequest.responseText);
        } else if (httpRequest.status === 404) {
            alert("Site is DOWN!");
        } else {
            alert('There was a problem with the request.');
            console.log(httpRequest.status);
        }
    }
}


   ```
## Show Comments


## Post Comment
* **Required parameters**  
`name=[string]`  
`message=[string]`
* **POST comment:**  `/api/comment/?name=nick&message=hello`

### Success responses
* **Code:** 201 - Created
    * **Content:**   `{Comment was created successfully.}`


### Error responses
* **Code:** 400 - Bad request
    * **Content:** `{Unable to create comment. Data is incomplete.}`   
* **Code:** 405 - Method not allowed
    * **Content:** ` Allow: Get|POST `
* **Code:** 503 - Service unavailable
    * **Content:** `{Unable to create comment.}`

    
### Sample Call
**AJAX**
   ```
    Sample call here
   ```

