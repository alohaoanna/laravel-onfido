<?php

// config for OANNA/Onfido
return [

    'dataset' => [

        /*
        |--------------------------------------------------------------------------
        | Default datas
        |--------------------------------------------------------------------------
        |
        | Default datas for creating applicant ID.
        | It is better to provide datas directly from the create method to avoid repeated datas in your onfido dashboard
        | These are defined to prevent code to throw exception
        |
        */

        'default' => [

            'first_name' => "John",
            'last_name' => "Doe",
            'dob' => "2000-01-01",

        ],

    ],

    'api' => [

        /*
        |--------------------------------------------------------------------------
        | Timeouts
        |--------------------------------------------------------------------------
        |
        | Define here the timeouts for the api
        |
        */

        'timeout' => [

            'default' => 30,
            'connect' => 30,
            'read' => 30,

        ],

        /*
        |--------------------------------------------------------------------------
        | ONFIDO WORKFLOW ID
        |--------------------------------------------------------------------------
        |
        | Provide your workflow ID recover in your onfido dashboard in the Onfido Studio
        |
        */

        'workflow_id' => env('ONFIDO_API_WORKFLOW_ID', 'ONFIDO_API_WORKFLOW_ID'),

        /*
        |--------------------------------------------------------------------------
        | ONFIDO API TOKEN
        |--------------------------------------------------------------------------
        |
        | Provide your API Token (not the SDK Token) here
        |
        */

        'token' => env('ONFIDO_API_TOKEN', 'ONFIDO_API_TOKEN'),

        /*
        |--------------------------------------------------------------------------
        | ONFIDO API DEFAULT REGION
        |--------------------------------------------------------------------------
        |
        | Provide your default API Region. You can manually set a new region if needed when you make calls.
        |
        */

        'region' => \Onfido\Region::EU,
    ],

    'database' => [

        /*
        |--------------------------------------------------------------------------
        | Primary key
        |--------------------------------------------------------------------------
        |
        | Define here if the primary key of the verified model is uuid or id
        | That is very important and need to be set before running migration
        | Logic is if value equal uuid then made a uuid morph else (all other case) made a classic morph
        |
        */

        'primary_key' => 'id',

    ],

];
