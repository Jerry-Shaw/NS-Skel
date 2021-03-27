<?php

namespace app\model;

use app\app_model;

/**
 * Class channel
 *
 * @package app\model
 */
class channel extends app_model
{
    /**
     * Get app info by app_id
     *
     * @param int $app_id
     *
     * @return array
     */
    public function getInfoById(int $app_id): array
    {
        return $this->select('*')->where(['app_id', $app_id])->getRow();
    }

    /**
     * Get app info by app_key
     *
     * @param string $app_key
     *
     * @return array
     */
    function getInfoByKey(string $app_key): array
    {
        return $this->select('*')->where(['app_key', $app_key])->getRow();
    }
}