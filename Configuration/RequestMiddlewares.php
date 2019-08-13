<?php

return [
    'frontend' => [
        'lemming/httpbasicauth/basic-auth' => [
            'target' => \Lemming\Httpbasicauth\Middleware\BasicAuth::class,
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