<?php

defined('TYPO3_MODE') or die();

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['httpbasicauth']
    = \Lemming\Httpbasicauth\Updates\HttpbasicauthUpdateWizard::class;
