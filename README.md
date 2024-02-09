# Hex Batch Core

Versions

| version | date       | notes                                                                  |
|---------|------------|------------------------------------------------------------------------|
| pre-1   | Dec 2 2023 | Set up environment, install laravel framework, setup user api for auth |
|         |            |                                                                        |


# Notes


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

Doing remotes next
