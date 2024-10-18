# Hex Batch Core

This code is implenting the ideas in core-docs/core-overview.md

## todo for the next release
* implement the publish event in the things, with limited path, test for missing other type, test for combo of attributes
* generate all the standard resources
* make full namespaces for new users and on demand
* be able to create types using subtypes with attributes using parents
* fill in all todos

# Notes

## Docker setup

When initializing the db for the first time, need to manually change the ownership of the db to the user in the php:
`ALTER DATABASE hbc_core_dev OWNER TO hbc_core_dev;`

## SSL on localhost
https://github.com/FiloSottile/mkcert#installation
https://dockerwebdev.com/tutorials/docker-php-development/

## Snippits 

        use Illuminate\Support\Facades\DB;
        Transaction::whereNull('invoice_date')
        ->update([
        'invoice_date' => DB::raw('DATE_SUB(`payout_date`, INTERVAL 3 DAY)')
        ])
        ;

## Using libraries

https://github.com/Galbar/JsonPath-PHP
https://github.com/digitickets/lalit
https://github.com/mateusjunges/laravel-kafka

can clear out just part of the redis cache `php artisan cache:clear --tags=tests`



## Future links

I think I will use this to make open api, need to see if can connect the act with the docs and how to put in examples
https://github.com/zircote/swagger-php?tab=readme-ov-file

  https://github.com/DerManoMann/openapi-extras
  https://github.com/MattyRad/openapi-serialize
