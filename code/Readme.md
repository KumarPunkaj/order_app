## API Reference Documentation

- `localhost:8080/orders?page=:page&limit=:limit` :

    GET Method - to fetch orders with page number and limit
	1. Header :
		- GET /orders?page=0&limit=5 HTTP/1.1
        - Host: localhost:8080
        - Content-Type: application/json

    2. Responses :

	```
	    	- Response
			[
			  {
			    "id": 1,
			    "distance": 46732,
			    "status": 1
			  },
			  {
			    "id": 2,
			    "distance": 46731,
			    "status": 0
			  },
			  {
			    "id": 3,
			    "distance": 56908,
			    "status": 0
			  },
			  {
			    "id": 4,
			    "distance": 49132,
			    "status": 0
			  },
			  {
			    "id": 5,
			    "distance": 46732,
			    "status": 0
			  }
			]
	```

		Code	               	Description
		- 200	               	successful operation
		- 422	               	Invalid Request Parameter
		- 500		       	    Internal Server Error

- `localhost:8080/orders` :

    POST Method - to create new order with origin and distination
	1. Header :
		- POST /orders HTTP/1.1
		- Host: localhost:8080
		- Content-Type: application/json

	2. Post-Data :
	```
		 {
		 	"origin" :["28.704060", "77.102493"],
		 	"destination" :["28.535517", "77.391029"]
		 }
	```

    3. Responses :
	```
	    	- Response
			{
			  "id": 44,
			  "distance": 46732,
			  "status": "UNASSIGN"
			}
	```

		Code	               	Description
		- 200	               	successful operation
		- 400	               	Api request denied or not responding
		- 422	               	Invalid Request Parameter

- `localhost:8080/orders/:id` :

    PATCH method to update status for taken.(Handled simultaneous update request from multiple users at the same time with response status 409)
	1. Header :
		- PATCH /orders/44 HTTP/1.1
		- Host: localhost:8080
		- Content-Type: application/json
	2. Post-Data :
	```
		 {
		 	"status" : "TAKEN"
		 }
	```

    3. Responses :
	```
	    	- Response
			{
			  "status": "SUCCESS"
			}
	```

		Code	               	Description
		- 200	               	successful operation
		- 422	               	Invalid Request Parameter
		- 409	               	Order already taken
		- 417	               	Invalid Order Id

