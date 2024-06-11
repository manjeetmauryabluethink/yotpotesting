<?php

namespace Yotpo\Reviews\Model\ResourceModel\RichSnippet;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Yotpo\Reviews\Model\RichSnippet;
use Yotpo\Reviews\Model\ResourceModel\RichSnippet as RichSnippetResource;

/**
 * Collection - Manage rich snippets
 */
class Collection extends AbstractCollection
{
    /**
     * Standard resource collection initialization
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            RichSnippet::class,
            RichSnippetResource::class
        );
    }
}
