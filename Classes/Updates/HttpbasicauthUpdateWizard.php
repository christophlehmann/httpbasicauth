<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Lemming\Httpbasicauth\Updates;

use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Configuration\SiteConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\ChattyInterface;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

class HttpbasicauthUpdateWizard implements UpgradeWizardInterface, ChattyInterface
{

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @return string Unique identifier of this updater
     */
    public function getIdentifier(): string
    {
        return 'httpbasicauth';
    }

    /**
     * @return string Title of this updater
     */
    public function getTitle(): string
    {
        return 'Migrate all user:password combinations from httpbasicauth to a new field';
    }

    /**
     * @return string Longer description of this updater
     */
    public function getDescription(): string
    {
        return 'This update wizard goes through all sites and'
            . ' move basicauth_user and basicauth_password to a new field';
    }

    /**
     * @return bool True if there are records to update
     */
    public function updateNecessary(): bool
    {
        return $this->checkSiteConfiguration();
    }

    /**
     * @return string[] All new fields and tables must exist
     */
    public function getPrerequisites(): array
    {
        return [];
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    /**
     * Performs the configuration update.
     *
     * @return bool
     */
    public function executeUpdate(): bool
    {
        /** @var SiteFinder $siteFinder */
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $sites = $siteFinder->getAllSites();
        foreach ($sites as $site) {
            /** @var SiteConfiguration $siteConfiguration */
            $siteConfiguration = GeneralUtility::makeInstance(SiteConfiguration::class, Environment::getConfigPath() . '/sites');
            $configuration = $siteConfiguration->load($site->getIdentifier());
            if (!isset($configuration['basicauth_credentials']) || empty($configuration['basicauth_credentials'])) {
                if (!empty($configuration['basicauth_user']) && !empty($configuration['basicauth_password'])) {
                    $configuration['basicauth_credentials'] = $configuration['basicauth_user'] . ':' . $configuration['basicauth_password'];
                    unset($configuration['basicauth_user'], $configuration['basicauth_password']);
                }
                $siteConfiguration->write($site->getIdentifier(), $configuration);
            }
        }
        return true;
    }

    /**
     * Check if basicauth_user still exist
     *
     * @return bool
     */
    public function checkSiteConfiguration(): bool
    {
        /** @var SiteConfiguration $siteConfiguration */
        $siteConfiguration = GeneralUtility::makeInstance(SiteConfiguration::class, Environment::getConfigPath() . '/sites');
        $allSiteConfigurations = $siteConfiguration->getAllExistingSites(true);
        foreach ($allSiteConfigurations as $identifier => $siteConfiguration) {
            if ($siteConfiguration->getConfiguration()['basicauth_user']) {
                return true;
            }
        }
        return false;
    }
}
