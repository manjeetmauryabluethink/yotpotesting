<?php
namespace Yotpo\Reviews\Block\Adminhtml\Dashboard\Tab;

use Yotpo\Reviews\Block\Adminhtml\Report\Reviews;

/**
 * Class YotpoReviews - Adminhtml dashboard Yotpo reviews tab
 */
class YotpoReviews extends Reviews
{
    /**
     * @var string
     */
    protected $_defaultPeriod = 'all';

    /**
     * @var string
     */
    protected $_template = 'Yotpo_Reviews::dashboard/yotpo_reviews_tab.phtml';
}
