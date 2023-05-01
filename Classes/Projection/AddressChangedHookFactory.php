<?php

namespace WebExcess\OpenStreetMap\Projection;

use Neos\ContentRepository\Core\ContentRepository;
use Neos\ContentRepository\Core\Projection\CatchUpHookFactoryInterface;
use WebExcess\OpenStreetMap\Service\GeocodingService;
use Neos\Flow\Annotations as Flow;

class AddressChangedHookFactory implements CatchUpHookFactoryInterface
{
    /**
     * @Flow\Inject
     */
    protected GeocodingService $geocodingService;

    public function build(ContentRepository $contentRepository): AddressChangedHook
    {
        return new AddressChangedHook(
            $contentRepository,
            $this->geocodingService
        );
    }
}
