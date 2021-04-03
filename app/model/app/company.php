<?php

namespace app\model\app;

use app\app_model;

/**
 * Class company
 *
 * @package app\model\app
 */
class company extends app_model
{
    /**
     * Fetch all info by co_id
     *
     * @param int $co_id
     *
     * @return array
     */
    public function getInfoById(int $co_id): array
    {
        return $this->select('*')->where(['co_id', $co_id])->getRow();
    }
}