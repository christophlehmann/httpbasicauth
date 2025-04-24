<?php

use Lemming\Httpbasicauth\Middleware\BasicAuth;

return [
    'frontend' => [
        'lemming/httpbasicauth/basic-auth' => [
            'target' => BasicAuth::class,
            'after' => [
                'typo3/cms-frontend/site',
                'typo3/cms-frontend/backend-user-authentication'
            ],
            'before' => [
                'typo3/cms-frontend/base-redirect-resolver'
            ]
        ]
    ]
];