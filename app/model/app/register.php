<?php

namespace app\model\app;

use app\app_model;

/**
 * Class register
 *
 * @package app\model
 */
class register extends app_model
{
    /**
     * Fetch all info by app_id
     *
     * @param int $app_id
     *
     * @return array
     */
    public function getInfoById(int $app_id): array
    {
        return $this->select('*')->where(['app_id', $app_id])->getRow();
    }
}