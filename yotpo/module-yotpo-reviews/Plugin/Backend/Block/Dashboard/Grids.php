<?php
namespace Yotpo\Reviews\Plugin\Backend\Block\Dashboard;

use Magento\Backend\Block\Dashboard\Grids as MagentoGrids;

/**
 * Class Grids - Inject Yotpo Reviews Tab in Admin Dashboard
 */
class Grids
{
    /**
     * Inject Yotpo Tab in Dashboard Tabs
     *
     * @param MagentoGrids $subject
     * @return void
     * @throws \Exception
     */
    public function beforeToHtml(
        MagentoGrids $subject
    ) {
        $subject->addTab(
            'yotpo_reviews',
            [
                'label' => __('Yotpo Reviews'),
                'url' => $subject->getUrl('yotpo_reviews/*/YotpoReviews', ['_current' => true]),
                'class' => 'ajax'
            ]
        );
    }
}
