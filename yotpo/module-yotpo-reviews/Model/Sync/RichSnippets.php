<?php

namespace Yotpo\Reviews\Model\Sync;

use Yotpo\Reviews\Model\Config as YotpoConfig;
use Yotpo\Reviews\Model\Logger as ReviewsLogger;
use Yotpo\Reviews\Model\Sync\Main as YotpoSyncMain;
use Yotpo\Reviews\Model\RichSnippetFactory as RichSnippetFactory;
use Yotpo\Reviews\Model\RichSnippet as RichSnippetModel;
use Yotpo\Reviews\Model\ResourceModel\RichSnippet as RichSnippetResource;
use Yotpo\Reviews\Model\ResourceModel\RichSnippet\CollectionFactory as RichSnippetCollectionFactory;

/**
 *
 * Class RichSnippets - Retrieve rich snippets data
 */
class RichSnippets
{

    const TTL = 86400; // 60 * 60 * 24 seconds

    /**
     * @var YotpoConfig
     */
    protected $yotpoConfig;

    /**
     * @var RichSnippetFactory
     */
    protected $richSnippetFactory;

    /**
     * @var Main
     */
    protected $yotpoSyncMain;

    /**
     * @var ReviewsLogger
     */
    protected $logger;

    /**
     * @var RichSnippetModel
     */
    protected $richSnippetModel;

    /**
     * @var RichSnippetResource
     */
    protected $richSnippetResource;

    /**
     * @var RichSnippetCollectionFactory
     */
    protected $richSnippetCollectionFactory;

    /**
     * @param YotpoConfig $yotpoConfig
     * @param RichSnippetFactory $richSnippetFactory
     * @param RichSnippetModel $richSnippetModel,
     * @param RichSnippetResource $richSnippetResource,
     * @param RichSnippetCollectionFactory $richSnippetCollectionFactory,
     * @param YotpoSyncMain $yotpoSyncMain
     * @param ReviewsLogger $logger
     */
    public function __construct(
        YotpoConfig $yotpoConfig,
        RichSnippetFactory $richSnippetFactory,
        RichSnippetModel $richSnippetModel,
        RichSnippetResource $richSnippetResource,
        RichSnippetCollectionFactory $richSnippetCollectionFactory,
        YotpoSyncMain $yotpoSyncMain,
        ReviewsLogger $logger
    ) {
        $this->yotpoConfig = $yotpoConfig;
        $this->richSnippetFactory = $richSnippetFactory;
        $this->richSnippetModel = $richSnippetModel;
        $this->richSnippetResource = $richSnippetResource;
        $this->yotpoSyncMain = $yotpoSyncMain;
        $this->logger = $logger;
        $this->richSnippetCollectionFactory = $richSnippetCollectionFactory;
    }

    /**
     * @param int|string|null $productId
     * @return array <mixed>
     */
    public function getRichSnippet($productId = null)
    {
        $richSnippetData = [
            "average_score" => 0.0,
            "reviews_count" => 0
        ];
        if (!$productId) {
            return $richSnippetData;
        }
        try {
            $storeId = $this->yotpoConfig->getStoreId();
            $collection = $this->richSnippetCollectionFactory->create();
            $collection->addFieldToFilter('store_id', (string) $storeId)
                ->addFieldToFilter('product_id', (string) $productId)
                ->setPageSize(1);
            /** @var RichSnippetModel $snippet */
            $snippet = $collection->getFirstItem();

            if ($snippet->isValid()) {
                $richSnippetData["average_score"] = $snippet->getAverageScore();
                $richSnippetData["reviews_count"] = $snippet->getReviewsCount();
            } else {
                $data = [];
                $endPoint    =   $this->yotpoConfig->getEndpoint(
                    'product_bottomline',
                    ['{product_id}'],
                    [$productId]
                );
                $data['entityLog'] = 'general';

                $apiResponse = $this->yotpoSyncMain->sync('GET', $endPoint, $data);

                $responseFull = $apiResponse->getData('response');
                $response = $responseFull["response"];
                if ($response) {
                    $richSnippetData["average_score"] = round($response["bottomline"]["average_score"], 2);
                    $richSnippetData["reviews_count"] = $response["bottomline"]["total_reviews"];

                    $snippet->setProductId($productId);
                    $snippet->setStoreId($storeId);

                    $snippet->setAverageScore($richSnippetData["average_score"]);
                    $snippet->setReviewsCount($richSnippetData["reviews_count"]);
                    $snippet->setExpirationTime(date('Y-m-d H:i:s', time() + self::TTL));
                    $this->richSnippetResource->save($snippet);
                }
            }
        } catch (\Exception $e) {
            $this->logger->info(__('API Issue - %1', $e->getMessage()));
        }

        return $richSnippetData;
    }
}
