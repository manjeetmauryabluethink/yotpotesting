<?php
namespace Yotpo\Reviews\Controller\Adminhtml\Report;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Website;
use Yotpo\Reviews\Model\Config as YotpoConfig;
use Magento\Backend\App\Action;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Reviews - Controller for reviews report page
 */
class Reviews extends Action
{
    /**
     * @var string
     */
    private $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT;

    /**
     * @var int
     */
    private $scopeId = 0;

    /**
     * @var bool
     */
    private $isEnabled;

    /**
     * @var string|null
     */
    private $appKey;

    /**
     * @var bool
     */
    private $isAppKeyAndSecretSet;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var YotpoConfig
     */
    private $yotpoConfig;

    /**
     * Reviews constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param YotpoConfig $yotpoConfig
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        YotpoConfig $yotpoConfig
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->yotpoConfig = $yotpoConfig;
    }

    /**
     * Initialize global variables
     *
     * @return void
     * @throws LocalizedException
     */
    private function initialize()
    {
        if (($storeId = $this->getRequest()->getParam(ScopeInterface::SCOPE_STORE, 0))) {
            $allStoreIds = [$storeId];
        } elseif (($websiteId = $this->getRequest()->getParam(ScopeInterface::SCOPE_WEBSITE, 0))) {
            /**
             * @var Website $website
             */
            $website = $this->yotpoConfig->getStoreManager()->getWebsite($websiteId);
            $allStoreIds = $website->getStoreIds();
        } else {
            $allStoreIds = $this->yotpoConfig->getAllStoreIds(false);
        }
        $allStoreIds = $this->yotpoConfig->filterDisabledStoreIds((array)$allStoreIds);
        $this->scopeId = ($allStoreIds) ? $allStoreIds[0] : 0;

        $this->isEnabled = (bool)$allStoreIds;
        $this->isAppKeyAndSecretSet = (bool)$allStoreIds;
        $this->appKey = ($this->scopeId) ? $this->yotpoConfig->getAppKey($this->scopeId, $this->scope) : null;
    }

    /**
     * Load the page defined in view/adminhtml/layout/yotpo_reviews_report_reviews.xml
     *
     * @return Page
     * @throws LocalizedException
     */
    public function execute()
    {
        $this->initialize();
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Yotpo Reviews'));
        if (!($this->isEnabled && $this->isAppKeyAndSecretSet)) {
            $resultPage->getLayout()->unsetElement('store_switcher');
        }
        return $resultPage;
    }
}
