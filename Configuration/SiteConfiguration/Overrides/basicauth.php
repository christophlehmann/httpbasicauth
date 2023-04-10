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
                'label' => '',
                'labelChecked' => 'Enabled',
                'labelUnchecked' => 'Disabled',
            ]
        ],
    ]
];
$GLOBALS['SiteConfiguration']['site']['columns']['basicauth_credentials'] = [
    'label' => 'Credentials',
    'description' => 'One user:password combination per line',
    'config' => [
        'type' => 'text',
        'placeholder' => 'user:password' . LF . 'user2:password2',
        'rows' => 5,
        'cols' => 30,
        'max' => 2000,
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
                'label' => '',
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
                'label' => '',
                'labelChecked' => 'Enabled',
                'labelUnchecked' => 'Disabled',
            ]
        ],
    ]
];

$GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] .= '
    ,--div--;HTTP Basic Auth, basicauth_enabled, basicauth_credentials, basicauth_allow_devipmask, basicauth_allow_beuser
';

