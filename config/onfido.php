<?php

// config for OANNA/Onfido
return [

    /*
    |--------------------------------------------------------------------------
    | Redirection
    |--------------------------------------------------------------------------
    |
    | Define here the type of redirection for the startWorkflow method in the Verifiable trait.
    | Expected value:
    | true           => If true is set, it will redirect to the default page of verification
    | 'my.own.route' => Provide your own verification route by setting the route name
    | false|null     => If false or null is set, it will not redirect the user to another route.
    |                   Set it if you verified the user via another way than a route.
    |
    */

    'redirection' => true,

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Set true if you use livewire in your project. This will dispatch event.
    |
    */

    'livewire' => true,

    /*
    |--------------------------------------------------------------------------
    | Web SDK
    |--------------------------------------------------------------------------
    |
    | Set the Web SDK version to use in cdn import
    |
    */

    'sdk' => [

        'version' => '14.46.1',

    ],

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
