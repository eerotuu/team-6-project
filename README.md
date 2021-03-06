# Rest API Documentation
This document provides information about API calls, data parameters and responses.



## Methods
Resource        | Allowed Methods
------------    | -------------
/events         | GET
/comments       | GET, POST, DELETE
    
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
    * **If data not exist:**   `{"message": "Data Not Found"}`
         

### Error responses
* **Code:** 405 - Method not allowed
    * **Content:** `Allow: Get`
### Sample Call
**AJAX**
```javascript
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

xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        alert(this.responseText);
    }
};
xhttp.open("GET", "http://127.0.0.1/api/events", true);
xhttp.send();
```
## Show Comments
* **List all comments** `/api/comments`
### Success responses
* **Code:** 200 - OK
    * **Example content:**   
        ```
        {
            "id": "3",
            "name": "Pepe",
            "time_stamp": "2018-12-03 12:15:11",
            "message": "Hello."
        }
        ```
    * **If data not exist:**   `Data Not Found`
         

### Error responses
* **Code:** 405 - Method not allowed
    * **Content:** `Allow: Get`

    
## Post Comment
You can create and update existing comments with this method.
* **Allowed input formats**
    * JSON

* **Required parameters**  
    * `name=[string]`  
    * `message=[string]`
    * When updating existing comment: `id=[integer]`
* **Optional parameters**  
    * `image_url=[string]`
* **Example format:**
    ````
    {  
        "name": "test",
        "message": "test",
        "image_url": "https://cdn.frankerfacez.com/emoticon/103171/4"
    }
    ````
* **Example format for updating comment:**
    ````
    {  
        "id": "1",
        "name": "test",
        "message": "Updated message",
    }
    ````

### Success responses
* **Code:** 201 - Created
    * **Content:**   `{"message": "Comment was created successfully."}`
* **Code:** 202 - Accepted
    * **Content:**   `{"message": "Comment was updated successfully."}`


### Error responses
* **Code:** 400 - Bad request
    * **Content:** `{"message": "Unable to create comment. Data is incomplete."}`   
* **Code:** 405 - Method not allowed
    * **Content:** ` Allow: Get|POST|DELETE `
* **Code:** 503 - Service unavailable
    * **Content:** `{"message": "Unable to create comment."}`
    * **Content:** `{"message": "Unable to update comment."}`

    
### Sample Call
**AJAX**
```javascript
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
```

## Delete comment
**Required parameters:** `[integer]`  
  
**DELETE comment where id 1:** `/api/comments/1`

### Success responses
* **Code:** 200 - OK
    * Comment have been deleted or no comment found with given id

### Error responses
* **Code:** 400 - Bad request
    * **Content:** `{"message": "Unable to delete comment. id needs to be integer."}`   
* **Code:** 405 - Method not allowed
    * **Content:** ` Allow: Get|POST|DELETE `
* **Code:** 503 - Service unavailable
    * **Content:** `{"message": "Unable to delete comment."}`
