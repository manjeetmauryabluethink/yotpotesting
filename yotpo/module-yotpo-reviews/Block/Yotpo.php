<?php
namespace Yotpo\Reviews\Block;

use Magento\Catalog\Helper\Image as CatalogImageHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Serialize;
use Magento\Framework\View\Element\Template\Context;
use Yotpo\Reviews\Model\Config as YotpoConfig;
use Magento\Framework\View\Element\Template;

enum WidgetsLocations: string {
    case HOME = 'cms_index_index';
    case CATEGORY = 'catalog_category_view';
    case OTHER = '';
}

/**
 * Class Yotpo - Block file for Yotpo Reviews Widget
 */
class Yotpo extends Template
{
    /**
     * @var YotpoConfig
     */
    private $yotpoConfig;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var CatalogImageHelper
     */
    private $catalogImageHelper;

    /**
     * Yotpo constructor.
     * @param Context $context
     * @param YotpoConfig $yotpoConfig
     * @param Registry $coreRegistry
     * @param CatalogImageHelper $catalogImageHelper
     * @param array<mixed> $data
     */
    public function __construct(
        Context $context,
        YotpoConfig $yotpoConfig,
        Registry $coreRegistry,
        CatalogImageHelper $catalogImageHelper,
        array $data = []
    ) {
        $this->yotpoConfig = $yotpoConfig;
        $this->coreRegistry = $coreRegistry;
        $this->catalogImageHelper = $catalogImageHelper;
        parent::__construct($context, $data);
    }

    /**
     * Check module is enabled and API tokens set
     *
     * @return boolean
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function isEnabled()
    {
        return $this->yotpoConfig->isEnabled() && $this->yotpoConfig->isAppKeyAndSecretSet();
    }

    /**
     * Get Yotpo API Key
     *
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getAppKey()
    {
        return $this->yotpoConfig->getAppKey();
    }

    /**
     * Set current product to the block object
     *
     * @return array|mixed|null
     */
    public function getProduct()
    {
        if ($this->getData('product') === null) {
            $this->setData('product', $this->coreRegistry->registry('current_product'));
        }
        return $this->getData('product');
    }

    /**
     * Find if current product is exist
     *
     * @return bool
     */
    public function hasProduct()
    {
        return $this->getProduct() && $this->getProduct()->getId();
    }

    /**
     * Get Product ID from product object
     *
     * @return int|null
     */
    public function getProductId()
    {
        if (!$this->hasProduct()) {
            return null;
        }
        return $this->getProduct()->getId();
    }

    /**
     * Get product Name from product object
     *
     * @return array<int, mixed>|string|null
     */
    public function getProductName()
    {
        if (!$this->hasProduct()) {
            return null;
        }
        return $this->escapeString($this->getProduct()->getName());
    }

    /**
     * Get description from product object
     *
     * @return array<int, mixed>|string|null
     */
    public function getProductDescription()
    {
        if (!$this->hasProduct()) {
            return null;
        }
        return $this->escapeString($this->getProduct()->getShortDescription());
    }

    /**
     * Get Product URL from product object
     *
     * @return string|null
     */
    public function getProductUrl()
    {
        if (!$this->hasProduct()) {
            return null;
        }
        return $this->getProduct()->getProductUrl();
    }

    /**
     * Get price from product object
     *
     * @return float|null
     */
    public function getProductFinalPrice()
    {
        if (!$this->hasProduct()) {
            return null;
        }
        return $this->getProduct()->getFinalPrice();
    }

    /**
     * Get Product Image from product object
     *
     * @param  string  $imageId
     * @return string|null
     */
    public function getProductImageUrl($imageId = 'product_page_image_large')
    {
        if (!$this->hasProduct()) {
            return null;
        }
        return $this->catalogImageHelper->init($this->getProduct(), $imageId)->getUrl();
    }

    /**
     * Check if widget is ready to render
     *
     * @return string
     */
    public function getCurrentCurrency()
    {
        return $this->yotpoConfig->getCurrentCurrency();
    }

    /**
     * Check if widget is ready to render
     *
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function isRenderWidget()
    {
        return $this->hasProduct() && ($this->yotpoConfig->isWidgetEnabled() || $this->getData('fromHelper'));
    }

    /**
     * Check if BottomLine is ready to render
     *
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function isRenderBottomline()
    {
        return $this->hasProduct() && ($this->yotpoConfig->isBottomlineEnabled() || $this->getData('fromHelper'));
    }

    /**
     * Check if BottomLineQnA is ready to render
     *
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function isRenderBottomlineQna()
    {
        return $this->hasProduct() && $this->yotpoConfig->isBottomlineQnaEnabled();
    }

    /**
     * Check if Carousel is ready to render
     *
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function isRenderCarousel()
    {
        return $this->isRenderCarouselOnProductPages()
            || $this->isRenderCarouselOnCategoryPages()
            || $this->isRenderCarouselOnHomePages();
    }

    /**
     * Check if Carousel is ready to render on product pages
     *
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function isRenderCarouselOnProductPages()
    {
        return $this->hasProduct() && $this->yotpoConfig->isCarouselOnProductPagesEnabled();
    }

    /**
     * Check if Carousel is ready to render on category pages
     *
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function isRenderCarouselOnCategoryPages()
    {
        return $this->isItCategoryPage() && $this->yotpoConfig->isCarouselOnCategoryPagesEnabled();
    }

    /**
     * Check if Carousel is ready to render on home pages
     *
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function isRenderCarouselOnHomePages()
    {
        return $this->isItHomePage() && $this->yotpoConfig->isCarouselOnHomePagesEnabled();
    }

    /**
     * Check if Promoted Products is ready to render
     *
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function isRenderPromotedProducts()
    {
        return $this->isRenderPromotedProductsOnProductPage()
            || $this->isRenderPromotedProductsOnCategoryPages()
            || $this->isRenderPromotedProductsOnHomePage();
    }

    /**
     * Check if Promoted Products is ready to render on product pages
     *
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function isRenderPromotedProductsOnProductPage()
    {
        return $this->hasProduct() && $this->yotpoConfig->isPromotedProductsOnProductPageEnabled();
    }

    /**
     * Check if Promoted Products is ready to render on category pages
     *
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function isRenderPromotedProductsOnCategoryPages()
    {
        return $this->isItCategoryPage() && $this->yotpoConfig->isPromotedProductsOnCategoryPagesEnabled();
    }

    /**
     * Check if Promoted Products is ready to render on home page
     *
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function isRenderPromotedProductsOnHomePage()
    {
        return $this->isItHomePage() && $this->yotpoConfig->isPromotedProductsOnHomePageEnabled();
    }

    /**
     * Check if Reviews Tab is ready to render
     *
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function isRenderReviewsTab()
    {
        return $this->hasProduct() && $this->yotpoConfig->isReviewsTabEnabled();
    }

    /**
     * Escape tags from string
     *
     * @param string $str
     * @return array<int, mixed>|string
     */
    public function escapeString($str)
    {
        if ($str === null) {
            return '';
        }

        return $this->_escaper->escapeHtml(strip_tags($str));
    }

    /**
     * Get widget URL
     *
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getYotpoWidgetUrl()
    {
        return $this->yotpoConfig->getYotpoWidgetUrl();
    }

    /**
     * Returns V3 reviews main widget instance id
     *
     * @return string|bool
     */
    public function getReviewsInstanceId()
    {
        return $this->yotpoConfig->getV3InstanceId('ReviewsMainWidget');
    }

    /**
     * Returns V3 questions and answers widget instance id
     *
     * @return string|bool
     */
    public function getQuestionsAndAnswersInstanceId()
    {
        return $this->yotpoConfig->getV3InstanceId('QuestionsAndAnswers');
    }

    /**
     * Returns V3 star rating widget instance id
     *
     * @return string|bool
     */
    public function getStarRatingInstanceId()
    {
        return $this->yotpoConfig->getV3InstanceId('ReviewsStarRatingsWidget');
    }

    /**
     * Returns V3 carousel widget instance id
     *
     * @return string|bool
     */
    public function getCarouselInstanceId()
    {
        return $this->yotpoConfig->getV3InstanceId('ReviewsCarousel');
    }

    /**
     * Returns V3 Promoted Products widget instance id
     *
     * @return string|bool
     */
    public function getPromotedProductsInstanceId()
    {
        return $this->yotpoConfig->getV3InstanceId('PromotedProducts');
    }

    /**
     * Returns V3 Reviews Tab widget instance id
     *
     * @return string|bool
     */
    public function getReviewsTabInstanceId()
    {
        return $this->yotpoConfig->getV3InstanceId('ReviewsTab');
    }

    /**
     * Checks if it should display V3 reviews main widget
     *
     * @return bool
     */
    public function isV3ReviewsWidget()
    {
        $instanceId = $this->getReviewsInstanceId();

        return $this->isV3Widget($instanceId);
    }

    /**
     * Checks if it should display V3 questions and answers widget
     *
     * @return bool
     */
    public function isV3QuestionsAndAnswersWidget()
    {
        $instanceId = $this->getQuestionsAndAnswersInstanceId();

        return $this->isV3Widget($instanceId);
    }

    /**
     * Checks if it should display V3 star rating widget
     *
     * @return bool
     */
    public function isV3StarRatingWidget()
    {
        $instanceId = $this->getStarRatingInstanceId();

        return $this->isV3Widget($instanceId);
    }

    /**
     * Checks if it should display V3 carousel widget
     *
     * @return bool
     */
    public function isV3CarouselWidget()
    {
        $instanceId = $this->getCarouselInstanceId();

        return $this->isV3Widget($instanceId);
    }

    /**
     * Checks if it should display V3 Promoted Products widget
     *
     * @return bool
     */
    public function isV3PromotedProductsWidget()
    {
        $instanceId = $this->getPromotedProductsInstanceId();

        return $this->isV3Widget($instanceId);
    }

    /**
     * Checks if it should display V3 Reviews Tab widget
     *
     * @return bool
     */
    public function isV3ReviewsTabWidget()
    {
        $instanceId = $this->getReviewsTabInstanceId();

        return $this->isV3Widget($instanceId);
    }

    /**
     * Checks if it should display V3 widget with specified instance id
     *
     * @param string|bool $instanceId
     * @return bool
     */
    public function isV3Widget($instanceId)
    {
        return $instanceId !== false && $this->yotpoConfig->isV3Enabled();
    }

    /**
     * Check if it is Home Page
     *
     * @return boolean
     */
    public function isItHomePage(): bool
    {
        return $this->getWidgetLocation() === WidgetsLocations::HOME->value;
    }

    /**
     * Check if it is Category Page
     *
     * @return boolean
     */
    public function isItCategoryPage(): bool
    {
        return $this->getWidgetLocation() === WidgetsLocations::CATEGORY->value;
    }

    private function getWidgetLocation(): string {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('Magento\Framework\App\Request\Http');
        return $request->getFullActionName();
    }

}
