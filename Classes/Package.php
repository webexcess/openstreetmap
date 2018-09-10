<?php

namespace WebExcess\OpenStreetMap;

use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Package\Package as BasePackage;
use Neos\Neos\EventLog\Integrations\ContentRepositoryIntegrationService;
use Neos\ContentRepository\Domain\Model\Node;
use WebExcess\OpenStreetMap\Service\GeocodingService;

class Package extends BasePackage
{
    /**
     * @param Bootstrap $bootstrap The current bootstrap
     * @return void
     */
    public function boot(Bootstrap $bootstrap)
    {
        $dispatcher = $bootstrap->getSignalSlotDispatcher();
        $dispatcher->connect(Node::class, 'nodePropertyChanged', GeocodingService::class, 'nodePropertyChanged');
    }
}
