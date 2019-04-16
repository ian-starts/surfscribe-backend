# surfscribe
A platform for subbscribing to different surfspots based on the forecast

## Requirements
* NPM
* Docker
* PHP (optional but recommended)
* Composer (optional but recommended)


## Installation

* clone the repo
* run `docker-compose up`
* run `composer install` or if you don't want to install composer run 
```bash
docker exec -it surfscribe-local composer install   
```
* run `php artisan migrate` or 
```bash
docker exec -it surfscribe-local php artian migrate 
```

go to http://localhost to see if it's running.

## API
The Api has 3 main features. Embeds, search, and pagination.
to call the api use:
`/api/{entity}/{id?}` 

The id is optional.

### Embed
On the list call (`api/{entity}`) you can use the embed like so:

`api/locations?embeds[]=images&embeds[]=users`

### Search
To search the api use

`api/locations?filters[all]=canggu indonesia`

### Paginator
To change the responsesize from the paginator use the `pagesize` paramater:

`api/locations?pagesize=20`

Or use the `page` parameter to go the next page:

`api/locations?page=2`

### Notes

* All these can be used together to create more complex queries
* The `filter[all]` parameter filters the data on country name, region name, wavebreak name and slug.
* The only available embed is currently `images`


## Final Notes
* The migrate command pulls some testdata, not all, due to high bandwitdth demand.
* The migration is also syncronous to prevent looking like a ddos.

