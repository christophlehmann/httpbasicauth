<?php

$EM_CONF['httpbasicauth'] = [
    'title' => 'HTTP Basic Auth',
    'description' => 'HTTP Basic Auth via Site Configuration',
    'category' => 'fe',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'author' => 'Christoph Lehmann',
    'author_email' => 'post@christophlehmann.eu',
    'version' => '2.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-12.4.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    'autoload' => [
        'classmap' => ['Classes'],
    ]
];
