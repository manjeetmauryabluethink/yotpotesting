<?php
namespace Yotpo\Reviews\Controller\Adminhtml\Dashboard;

use Magento\Backend\Controller\Adminhtml\Dashboard\AjaxBlock;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\View\Element\Template;
use Yotpo\Reviews\Block\Adminhtml\Dashboard\Tab\YotpoReviews as DashboardReviews;

/**
 * Class YotpoReviews - Controller for reviews tab in dashboard
 */
class YotpoReviews extends AjaxBlock
{
    /**
     * Gets Yotpo reviews tab
     *
     * @return Raw
     */
    public function execute()
    {
        /**
         * @var Template $output
         */
        $output = $this->layoutFactory->create()->createBlock(DashboardReviews::class);
        $output->setId('yotpoReviewsTab');
        $toHtml = $output->toHtml();
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents($toHtml);
    }
}
