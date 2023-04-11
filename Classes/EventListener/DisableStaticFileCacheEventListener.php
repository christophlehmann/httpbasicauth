<?php
declare(strict_types=1);

namespace Lemming\Httpbasicauth\EventListener;

use Lemming\Httpbasicauth\Middleware\BasicAuth;
use SFC\Staticfilecache\Event\CacheRuleEvent;

class DisableStaticFileCacheEventListener
{
    public function __invoke(CacheRuleEvent $event): void
    {
        if ($event->getRequest()->getAttribute(BasicAuth::IS_ENABLED, false)) {
            $event->setSkipProcessing(true);
            $event->addExplanation(__CLASS__, 'Basic Authentication is enabled for current site');
        }
    }
}