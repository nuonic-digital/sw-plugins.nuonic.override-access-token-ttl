<?php

declare(strict_types=1);

namespace NuonicOverrideAccessTokenTtl\EventListener\Authentication;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use NuonicOverrideAccessTokenTtl\Config\PluginConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiAuthenticationListener implements EventSubscriberInterface
{

    public function __construct(
        private readonly AuthorizationServer $authorizationServer,
        private readonly PluginConfigService $pluginConfigService
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['setupOAuth', 125]
        ];
    }

    public function setupOAuth(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $accessTokenInterval = new \DateInterval(
            sprintf('PT%dM', $this->pluginConfigService->getInt('accessTokenTtl'))
        );

        $this->authorizationServer->enableGrantType(new ClientCredentialsGrant(), $accessTokenInterval);
    }
}
