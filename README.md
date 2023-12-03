# Hex Batch Core

Versions

| version | date       | notes                                                                  |
|---------|------------|------------------------------------------------------------------------|
| pre-1   | Dec 2 2023 | Set up environment, install laravel framework, setup user api for auth |
|         |            |                                                                        |


# Notes


When initializing the db for the first time, need to manually change the ownership of the db to the user in the php:
`ALTER DATABASE hbc_core_dev OWNER TO hbc_core_dev;`



## Snippits 

        use Illuminate\Support\Facades\DB;
        Transaction::whereNull('invoice_date')
        ->update([
        'invoice_date' => DB::raw('DATE_SUB(`payout_date`, INTERVAL 3 DAY)')
        ])
        ;
