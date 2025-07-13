<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),
    
    'attachment' => [
        'accepted_extension' =>  ['xlsx', 'xls', 'docx', 'doc', 'pptx', 'ppt',
        'pdf', 'jpg', 'gif', 'png'],
        'uploading_max_size' => 10, // in MB
    ],

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        'seeding-files' => [
            'driver' => 'local',
            'root' => database_path('seeding-files'),
            'throw' => false,
        ],

        'text-files' => [
            'driver' => 'local',
            'root' => database_path('textFile'),
            'throw' => false,
        ],

        'split-text-files' => [
            'driver' => 'local',
            'root' => database_path('split-texts'),
            'throw' => false,
        ],
        'split-texts-firm-orders' => [
            'driver' => 'local',
            'root' => database_path('split-texts/firm-orders'),
            'throw' => false,
        ],
        'split-texts-sales-actuals' => [
            'driver' => 'local',
            'root' => database_path('split-texts/sales_actuals'),
            'throw' => false,
        ],
        'split-texts-purchases' => [
            'driver' => 'local',
            'root' => database_path('split-texts/purchases'),
            'throw' => false,
        ],
        'split-texts-purchase-requisitions' => [
            'driver' => 'local',
            'root' => database_path('split-texts/purchase_requisitions'),
            'throw' => false,
        ],
        'split-texts-outsource-material-failures' => [
            'driver' => 'local',
            'root' => database_path('split-texts/outsource_material_failures'),
            'throw' => false,
        ],
        'split-texts-outsource-process-failures' => [
            'driver' => 'local',
            'root' => database_path('split-texts/outsource_process_failures'),
            'throw' => false,
        ],
        'split-texts-purchase-arrivals' => [
            'driver' => 'local',
            'root' => database_path('split-texts/purchase_arrivals'),
            'throw' => false,
        ],
        'split-texts-payments' => [
            'driver' => 'local',
            'root' => database_path('split-texts/payments'),
            'throw' => false,
        ],
        'split-texts-payment-details' => [
            'driver' => 'local',
            'root' => database_path('split-texts/payment_details'),
            'throw' => false,
        ],

        // facility related
        'split-texts-facility-work-results' => [
            'driver' => 'local',
            'root' => database_path('split-texts/facility_work_results'),
            'throw' => false,
        ],
        'split-texts-facility-work-details' => [
            'driver' => 'local',
            'root' => database_path('split-texts/facility_work_details'),
            'throw' => false,
        ],
        'split-texts-facility-purchase-amount-results' => [
            'driver' => 'local',
            'root' => database_path('split-texts/facility_purchase_amount_results'),
            'throw' => false,
        ],
        'split-texts-facility-work-amount-results' => [
            'driver' => 'local',
            'root' => database_path('split-texts/facility_work_amount_results'),
            'throw' => false,
        ],



        'database-folder' => [
            'driver' => 'local',
            'root' => database_path(''),
            'throw' => false,
        ],

        'excel-files' => [
            'driver' => 'local',
            'root' => database_path('excelFile'),
            'throw' => false,
        ],

        'database-json-files' => [
            'driver' => 'local',
            'root' => database_path('jsonfiles'),
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
