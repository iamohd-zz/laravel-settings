<?php

namespace Smartisan\Settings\Repositories;

use Smartisan\Settings\Contracts\Repository as RepositoryContract;

abstract class Repository implements RepositoryContract
{
    /**
     * Settings filter instance.
     *
     * @var \Smartisan\Settings\EntryFilter
     */
    protected $entryFilter;

    /**
     * Set settings filter.
     *
     * @param \Smartisan\Settings\EntryFilter $filter
     * @return \Smartisan\Settings\Repositories\Repository $this
     */
    public function withFilter($filter)
    {
        $this->entryFilter = $filter;

        return $this;
    }
}
