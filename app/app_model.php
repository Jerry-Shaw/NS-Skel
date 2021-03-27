<?php

namespace app;

use Ext\libMySQL;

/**
 * Class app_model
 *
 * @package app
 */
class app_model extends libMySQL
{
    /**
     * app_model constructor.
     */
    public function __construct()
    {
        $this->bindPdo(base::new()->pdo)->setTablePrefix('app_')->setTableName(get_class($this));
    }
}