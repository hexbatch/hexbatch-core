# Hex Batch Core

This code is implenting the ideas in core-docs/core-overview.md


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

## Kafka stuff

https://hub.docker.com/r/bitnami/kafka
https://kafka.apache.org/intro
https://laravel-news.com/laravel-kafka-package
https://github.com/mateusjunges/laravel-kafka
https://junges.dev/documentation/laravel-kafka/v1.13/1-introduction
https://medium.com/simform-engineering/integrating-apache-kafka-in-laravel-real-time-database-synchronization-with-debezium-connector-2506bc8f37a7
https://www.golinuxcloud.com/laravel-kafka-tutorial/ //ads
