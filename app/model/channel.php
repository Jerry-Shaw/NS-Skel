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
     * @param int  $app_id
     * @param bool $only_secret
     *
     * @return array
     */
    public function getInfoById(int $app_id, bool $only_secret = false): array
    {
        return !$only_secret
            ? $this->select('*')->where(['app_id', $app_id])->getRow()
            : $this->select('app_id', 'app_secret', 'app_status')->where(['app_id', $app_id])->getRow();
    }

    /**
     * Get app info by app_key
     *
     * @param string $app_key
     * @param bool   $only_secret
     *
     * @return array
     */
    function getInfoByKey(string $app_key, bool $only_secret = false): array
    {
        return !$only_secret
            ? $this->select('*')->where(['app_key', $app_key])->getRow()
            : $this->select('app_id', 'app_secret', 'app_status')->where(['app_key', $app_key])->getRow();
    }
}