<?php
declare(strict_types=1);
namespace Lemming\Httpbasicauth\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BasicAuth implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var Site $site */
        $site = $request->getAttribute('site');
        if (!$site) {
            return $handler->handle($request);
        }

        try {
            if (!$site->getAttribute('basicauth_enabled')) {
                return $handler->handle($request);
            }
        } catch (\InvalidArgumentException $e) {
            // Attribute maybe not set after installing this extension?
            return $handler->handle($request);
        }

        if ($site->getAttribute('basicauth_allow_devipmask')) {
            $clientIPMatchesDevelopmentSystem = GeneralUtility::cmpIP(GeneralUtility::getIndpEnv('REMOTE_ADDR'),
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['devIPmask']);
            if ($clientIPMatchesDevelopmentSystem) {
                return $handler->handle($request);
            }
        }

        if ($site->getAttribute('basicauth_allow_beuser')) {
            $context = GeneralUtility::makeInstance(Context::class);
            $backendUserLoggedIn = $context->getPropertyFromAspect('backend.user', 'id') > 0;
            if ($backendUserLoggedIn) {
                return $handler->handle($request);
            }
        }

        if (preg_match("/Basic\s+(.*)$/i", $request->getHeaderLine("Authorization"), $matches)) {
            $credentials = explode(":", base64_decode($matches[1]), 2);

            if (count($credentials) == 2) {
                list($user, $password) = $credentials;

                $basicauthCredentials = GeneralUtility::trimExplode(
                    LF,
                    $site->getAttribute('basicauth_credentials'),
                    true
                );
                foreach ($basicauthCredentials ?? [] as $basicauthCredential){
                    list($basicauthUser, $basicauthPassword) = GeneralUtility::trimExplode(':', $basicauthCredential, true, 2 );
                    if ($basicauthUser === $user &&
                        $basicauthPassword === $password
                    ) {
                        return $handler->handle($request);
                    }
                }
            }
        }

        // @Todo: Return PSR-7 Response ?
        header('WWW-Authenticate: Basic realm="Not authorized"');
        header('HTTP/1.0 401 Unauthorized');
        die("Not authorized");
    }
}
