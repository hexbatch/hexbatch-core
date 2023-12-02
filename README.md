# Hex Batch Core

Versions

| version | date       | notes                                                                  |
|---------|------------|------------------------------------------------------------------------|
| pre-1   | Dec 1 2023 | Set up environment, install laravel framework, setup user api for auth |
|         |            |                                                                        |


# Notes

using 
* https://laravel.com/docs/10.x/sanctum
* https://laravel.com/docs/10.x/fortify

When initializing the db for the first time, need to manually change the ownership of the db to the user in the php:
`ALTER DATABASE hbc_core_dev OWNER TO hbc_core_dev;`
