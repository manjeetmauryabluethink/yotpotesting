<?php
namespace Yotpo\Reviews\Plugin\Catalog\Block\Product\View;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Layout;
use Yotpo\Reviews\Model\Config as YotpoConfig;
use Magento\Catalog\Block\Product\View\Details as MagentoDetails;

/**
 * Class Details - Inject Yotpo Reviews in Product Details Page
 */
class Details
{
    /**
     * @var YotpoConfig
     */
    private $yotpoConfig;

    /**
     * Details constructor.
     * @param YotpoConfig $yotpoConfig
     */
    public function __construct(
        YotpoConfig $yotpoConfig
    ) {
        $this->yotpoConfig = $yotpoConfig;
    }

    /**
     * Remove Magento Review form from PDP if
     * Yotpo is enabled and Magento Review form is set to disabled
     *
     * @param MagentoDetails $reviewBlock
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function beforeToHtml(
        MagentoDetails $reviewBlock
    ) {
        /**
         * @var Layout $layout
        */
        $layout = $reviewBlock->getLayout();

        if ($this->yotpoConfig->isEnabled() && $this->yotpoConfig->isMdrEnabled() && $layout->getBlock('reviews.tab')) {
            $layout->unsetElement('reviews.tab');
        }
    }
}
