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

        if ($this->isAccessGrantedForDeveloperIps($site) && $this->isVisitorIpMatchingDeveloperIpMask($request)) {
            return $handler->handle($request);
        }

        if ($this->isAccessGrantedForBackendUsers($site) && $this->visitorIsBackendUser()) {
            return $handler->handle($request);
        }

        if (!$this->login($request, $site)) {
            return new HtmlResponse('Not authorized', 401, ['WWW-Authenticate' => 'Basic realm="TYPO3"']);
        }

        return $handler->handle($request);
    }

    protected function login(ServerRequestInterface $request, Site $site): bool
    {
        $authorization = $this->parseHeader($request->getHeaderLine('Authorization'));
        if ($authorization === null || $authorization === []) {
            return false;
        }

        // Load credentials
        $credentials = $this->loadCredentialsForSite($site);

        // Check the user
        if (!isset($credentials[$authorization['username']])) {
            return false;
        }

        return password_verify((string) $authorization['password'], (string) $credentials[$authorization['username']]);
    }

    private function parseHeader(string $header): ?array
    {
        if (!str_starts_with($header, 'Basic')) {
            return null;
        }

        $decodedHeader = base64_decode(substr($header, 6));
        if (!$decodedHeader) {
            return null;
        }

        $headerParts = explode(':', $decodedHeader, 2);
        return [
            'username' => $headerParts[0],
            'password' => $headerParts[1] ?? null,
        ];
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

    protected function isVisitorIpMatchingDeveloperIpMask(ServerRequestInterface $request): bool
    {
        return GeneralUtility::cmpIP(
            $request->getServerParams()['REMOTE_ADDR'] ?? '',
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

    protected function visitorIsBackendUser(): bool
    {
        $context = GeneralUtility::makeInstance(Context::class);
        return $context->getPropertyFromAspect('backend.user', 'id') > 0;
    }

    protected function loadCredentialsForSite(Site $site): array
    {
        try {
            $lines = preg_split('/\R+/', $site->getAttribute('basicauth_credentials')) ?: [];

            $credentials = [];
            foreach ($lines as $line) {
                $parts = GeneralUtility::trimExplode(':', $line, limit: 2);

                if (count($parts) < 2) {
                    continue;
                }

                $credentials[$parts[0]] = $parts[1];
            }

            return $credentials;
        } catch (\InvalidArgumentException) {
            // Attribute does not exist
            return [];
        }
    }
}
