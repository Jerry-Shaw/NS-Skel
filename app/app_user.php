<?php

namespace app;

use Core\Factory;

/**
 * Class app_user
 *
 * @package app
 */
class app_user extends Factory
{
    //Channel data
    public string $app_id     = '';
    public string $app_key    = '';
    public string $app_auth   = '';
    public string $app_secret = '';

    //User data
    public int    $user_id     = 0;
    public string $user_img    = '';
    public string $user_auth   = '';
    public string $user_status = '';
    public string $user_agent  = '';
}