<?php

$GLOBALS['SiteConfiguration']['site']['columns']['basicauth_enabled'] = [
    'label' => 'Enable HTTP Basic Authentication',
    'config' => [
        'type' => 'check',
        'renderType' => 'checkboxLabeledToggle',
        'items' => [
            [
                0 => '',
                1 => '',
                'labelChecked' => 'Enabled',
                'labelUnchecked' => 'Disabled',
            ]
        ],
    ]
];
$GLOBALS['SiteConfiguration']['site']['columns']['basicauth_user'] = [
    'label' => 'Username',
    'config' => [
        'type' => 'input',
    ],
];
$GLOBALS['SiteConfiguration']['site']['columns']['basicauth_password'] = [
    'label' => 'Password',
    'config' => [
        'type' => 'input',
    ],
];
$GLOBALS['SiteConfiguration']['site']['columns']['basicauth_allow_devipmask'] = [
    'label' => 'Grant access when devIPMask matches',
    'config' => [
        'type' => 'check',
        'default' => '1',
        'renderType' => 'checkboxLabeledToggle',
        'items' => [
            [
                0 => '',
                1 => '',
                'labelChecked' => 'Enabled',
                'labelUnchecked' => 'Disabled',
            ]
        ],
    ]
];
$GLOBALS['SiteConfiguration']['site']['columns']['basicauth_allow_beuser'] = [
    'label' => 'Grant access when backend user logged in',
    'config' => [
        'type' => 'check',
        'default' => '1',
        'renderType' => 'checkboxLabeledToggle',
        'items' => [
            [
                0 => '',
                1 => '',
                'labelChecked' => 'Enabled',
                'labelUnchecked' => 'Disabled',
            ]
        ],
    ]
];

$GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] .= '
    ,--div--;HTTP Basic Auth, basicauth_enabled, basicauth_user, basicauth_password, basicauth_allow_devipmask, basicauth_allow_beuser
';
