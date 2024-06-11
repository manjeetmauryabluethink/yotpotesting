<?php

namespace Yotpo\Reviews\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Registry;
use Yotpo\Reviews\Model\Sync\RichSnippets as YotpoRichSnippets;

/**
 * [!] This class is deprecated & will be removed on future releases.
 * Please use \Yotpo\Reviews\Model\Sync\RichSnippets instead.
 */
class RichSnippets extends AbstractHelper
{
    /**
     * @var YotpoRichSnippets
     */
    private $yotpoRichSnippets;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @param Context $context
     * @param YotpoRichSnippets $yotpoRichSnippets
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context           $context,
        YotpoRichSnippets $yotpoRichSnippets,
        Registry          $coreRegistry
    ) {
        parent::__construct($context);
        $this->yotpoRichSnippets = $yotpoRichSnippets;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * @return array <mixed>
     */
    public function getRichSnippet()
    {
        $productId = null;
        $product = $this->coreRegistry->registry('current_product');
        if ($product) {
            $productId = $product->getId();
        }
        return $this->yotpoRichSnippets->getRichSnippet($productId);
    }
}
