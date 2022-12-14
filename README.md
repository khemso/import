# Import Test

## Summary

This service has 5 endpoints that can be called without any authantication, the endpoints are:
- `/load`: Upload the file
- `/sellers/{id}`: Provide complete seller data via id
- `/sellers/{id}/contacts`: Provide a list of all contacts established by the seller.
- `/sellers/{id}/sales`: Provide a list of all sales data accomplished by the seller.
- `/sales/{year}`: List of all sales of a given ear.


## Clone this repo

```bash
git clone https://github.com/saada/docker-lumen.git
cd docker-lumen
```

## Postman collection 

A postmaon collection is included in the repo under the name `Import-test.postman_collection.json`


### Configuration

- First you need to clone the repo

```bash
git clone git@github.com:khemso/import.git
cd import
```

- There are two configurations files `.env` files. One `.env` file for docker-compose.yaml and another for the php application `.env.app.example` feel free to edit the configurations as you see fit.


- There is a make file with all the commands you need to run the serice 
```sh
# to start the docker container 
make start

# to stop the docker container 
make stop

# to destory the docker container 
make destroy

# to run the db migration
make migrate
```