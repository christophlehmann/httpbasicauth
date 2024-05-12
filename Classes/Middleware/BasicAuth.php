<?php
declare(strict_types=1);
namespace Lemming\Httpbasicauth\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BasicAuth implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $site = $request->getAttribute('site');
        if (!$site instanceof Site) {
            return $handler->handle($request);
        }

        if (!$this->isBasicAuthenticationEnabled($site)) {
            return $handler->handle($request);
        }

        if ($this->isAccessGrantedForDeveloperIps($site) && $this->isVisitorIpMatchingDeveloperIpMask()) {
            return $handler->handle($request);
        }

        if ($this->isAccessGrantedForBackendUsers($site) && $this->isVisitorABackendUser()) {
            return $handler->handle($request);
        }

        $authorizationHeaderCredentials = $this->getCredentialsFromAuthorizationHeader($request);
        if ($authorizationHeaderCredentials && in_array($authorizationHeaderCredentials, $this->getCredentials($site))) {
            return $handler->handle($request);
        }

        return new HtmlResponse('Not authorized', 401, ['WWW-Authenticate' => 'Basic realm="Not authorized"']);
    }

    protected function isBasicAuthenticationEnabled(Site $site): bool
    {
        try {
            return (bool)$site->getAttribute('basicauth_enabled');
        } catch (\InvalidArgumentException) {
            // Attribute does not exist
            return false;
        }
    }

    protected function isAccessGrantedForDeveloperIps(Site $site): bool
    {
        try {
            return (bool)$site->getAttribute('basicauth_allow_devipmask');
        } catch (\InvalidArgumentException) {
            // Attribute does not exist
            return false;
        }
    }

    protected function isVisitorIpMatchingDeveloperIpMask(): bool
    {
        return GeneralUtility::cmpIP(
            GeneralUtility::getIndpEnv('REMOTE_ADDR'),
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['devIPmask']
        );
    }

    protected function isAccessGrantedForBackendUsers(Site $site): bool
    {
        try {
            return (bool)$site->getAttribute('basicauth_allow_beuser');
        } catch (\InvalidArgumentException) {
            // Attribute does not exist
            return false;
        }
    }

    protected function isVisitorABackendUser(): bool
    {
        $context = GeneralUtility::makeInstance(Context::class);
        return $context->getPropertyFromAspect('backend.user', 'id') > 0;
    }

    protected function getCredentialsFromAuthorizationHeader(ServerRequestInterface $request): ?string
    {
        if (preg_match("/Basic\s+(.*)$/i", $request->getHeaderLine("Authorization"), $matches)) {
            $credentials = base64_decode($matches[1]);
            $usernamePasswordSeparator = ':';
            if (str_contains($credentials, $usernamePasswordSeparator)
                && !str_starts_with($credentials, $usernamePasswordSeparator)
            ) {
                return $credentials;
            }
        }
        return null;
    }

    protected function getCredentials(Site $site): array
    {
        try {
            $credentials = GeneralUtility::trimExplode(
                LF,
                $site->getAttribute('basicauth_credentials'),
                true
            );
            return $credentials;
        } catch (\InvalidArgumentException) {
            // Attribute does not exist
            return [];
        }
    }
}
