Traq API Documentation Draft (rev 1)
====================================

Overview 
---------

API can be accessed over HTTP using *http://traq.example.com/api/* url. JSON is used for all data transfers.

Authentication
--------------
For accessing the API you need to pass the api_key with the request, there are two ways to achieve this:

* Append the *api_key* attribute to like so */api/user?api\_key=e6223ea605dd3cc5bc08f59b6850daadace40616*
* Pass it as a "Traq-API-Key" HTTP header

If invalid or no API key is provided and error message is returned.


HTTP Verbs
----------

Different methods require different HTTP verbs to be used for a request.

**GET**  
> Used for retriving resources, parameters are passed in the request url

**POST**  
> Used for creating resources, parameters are passed as the request body

Error codes
-----------
When a request is successful it returns HTTP code **200 - OK**, but when soemthing goes wrong it may return one of the following:

**403 - Forbidden** - this error is returned when the API key is not provided or is invalid

    HTTP/1.1 403 Forbidden
    Content-Length: 37
    
    {"error":"403","message":"Forbidden"}


**404 - Not Found** - returned when the requested resource (for example a user) does not exist

    HTTP/1.1 404 Not found
    Content-Length: 37
    
    {"error":"404","message":"Not found"}

**422 - Unprocessable Entity** - you might get it if the specified data is not valid (for example username contains illegal chars)

    HTTP/1.1 422 - Unprocessable Entity
    Content-Length: 45
    
    {"error":"422","message":"Validation Failed"}

Available methods
-----------

**Get the users whos api_key is being used for the request**

    GET /api/user

Response

    {
		"id": "1",
		"username": "admin",
		"name": "Traq Admin",
		"email": "traq@example.com",
		"group_id": "1"
	}


**Get a single user**

    GET /api/users/:user_id

Response

    {
		"id": "2",
		"username": "user",
		"name": "Traq User",
		"email": "user@example.com",
		"group_id": "2"
	}

**Register a new user**

    POST /api/users

Input  
*required* **string** username  
*required* **string** password - plaintext  
*required* **string** email  
*optional* **string** name

    username=newbie&password=qwerty&email=newbie@example.com&name=Derp

Response

    {
		"id": "3",
		"username": "newbie",
		"name": "Derp",
		"email": "newbie@example.com",
		"group_id": "2"
	}
