<?php

namespace Yotpo\Reviews\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * RichSnippet - Manage rich snippets
 */
class RichSnippet extends AbstractDb
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('yotpo_rich_snippets', 'rich_snippet_id');
    }
}
