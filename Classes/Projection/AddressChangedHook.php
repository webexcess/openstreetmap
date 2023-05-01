<?php

namespace WebExcess\OpenStreetMap\Projection;

use Neos\ContentRepository\Core\ContentRepository;
use Neos\ContentRepository\Core\EventStore\EventInterface;
use Neos\ContentRepository\Core\Feature\NodeModification\Command\SetNodeProperties;
use Neos\ContentRepository\Core\Feature\NodeModification\Dto\PropertyValuesToWrite;
use Neos\ContentRepository\Core\Feature\NodeModification\Event\NodePropertiesWereSet;
use Neos\ContentRepository\Core\Projection\CatchUpHookInterface;
use Neos\EventStore\Model\EventEnvelope;
use WebExcess\OpenStreetMap\Service\GeocodingService;

class AddressChangedHook implements CatchUpHookInterface
{

    public function __construct(
        private readonly ContentRepository $contentRepository,
        private readonly GeocodingService $geocodingService
    ) {
    }

    public function onBeforeCatchUp(): void
    {
    }

    public function onBeforeEvent(EventInterface $eventInstance, EventEnvelope $eventEnvelope): void
    {
        if (
            $eventInstance instanceof NodePropertiesWereSet
        ) {
            $nodeAggregate = $this->contentRepository->getContentGraph()->findNodeAggregateById(
                $eventInstance->getContentStreamId(),
                $eventInstance->getNodeAggregateId()
            );
            if (
                $nodeAggregate &&
                $nodeAggregate->nodeTypeName->value === 'WebExcess.OpenStreetMap:Map' &&
                $eventInstance->propertyValues->propertyExists('address')
            ) {
                $result = $this->geocodingService->geocodeLatLonFromAddress($eventInstance->propertyValues->getProperty('address')->value);
                if (!$result) {
                    $this->contentRepository->handle(
                        new SetNodeProperties(
                            $eventInstance->getContentStreamId(),
                            $eventInstance->getNodeAggregateId(),
                            $eventInstance->getOriginDimensionSpacePoint(),
                            PropertyValuesToWrite::fromArray(['lat' => '', 'lon' => ''])
                        )
                    );
                    return;
                }

                $this->contentRepository->handle(
                    new SetNodeProperties(
                        $eventInstance->getContentStreamId(),
                        $eventInstance->getNodeAggregateId(),
                        $eventInstance->getOriginDimensionSpacePoint(),
                        PropertyValuesToWrite::fromArray($result)
                    )
                );
            }
        }
    }

    public function onAfterEvent(EventInterface $eventInstance, EventEnvelope $eventEnvelope): void
    {
    }

    public function onBeforeBatchCompleted(): void
    {
    }

    public function onAfterCatchUp(): void
    {
    }
}
