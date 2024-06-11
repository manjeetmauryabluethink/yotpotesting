<?php
namespace Yotpo\Reviews\Model\Sync;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Yotpo\Core\Model\Api\Sync as YotpoCoreApiSync;

/**
 * Class Main - Common point of Reviews Sync
 */
class Main
{

    /**
     * @var YotpoCoreApiSync
     */
    private $yotpoCoreApiSync;

    /**
     * Main constructor.
     * @param YotpoCoreApiSync $yotpoCoreApiSync
     */
    public function __construct(YotpoCoreApiSync $yotpoCoreApiSync)
    {
        $this->yotpoCoreApiSync = $yotpoCoreApiSync;
    }

    /**
     * Sync magento data to Yotpo API
     *
     * @param string $method
     * @param string $url
     * @param array<mixed> $data
     * @return DataObject
     * @throws NoSuchEntityException
     */
    public function sync($method, $url, array $data = []): DataObject
    {
        return $this->yotpoCoreApiSync->syncV1($method, $url, $data);
    }
}
