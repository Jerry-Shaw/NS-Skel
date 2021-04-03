<?php

namespace app;

use app\model\channel;
use Core\Execute;
use Core\Lib\App;
use Core\Lib\IOUnit;
use Core\Lib\Router;
use Core\Reflect;
use Ext\libErrno;
use ReflectionException;

/**
 * Class hook
 *
 * @package app
 */
class hook extends base
{
    /** @var app_user $app_user */
    public app_user $app_user;

    /**
     * hook constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->app_user = app_user::new();
    }

    /**
     * Check request authority
     *
     * @param string $app_key
     * @param int    $t
     *
     * @return bool
     */
    public function chkAuth(string $app_key = '', int $t = 0): bool
    {
        //Validate timestamp (valid in 10 minutes)
        if (abs(time() - $t) > 600) {
            libErrno::new()->set(400, 1);
            return false;
        }

        //Validate app_key
        if ('' === $app_key) {
            libErrno::new()->set(401, 1);
            return false;
        }

        //Build cache key
        $cache_key = 'app:' . $app_key;

        //Read app info from cache
        $app_info = $this->lib_cache->get($cache_key);

        if (empty($app_info)) {
            //Read app info from DB by app_key
            $app_info = channel::new()->getInfoByKey($app_key, true);

            //Add cache
            $this->lib_cache->set($cache_key, $app_info);
        }

        //App NOT registered
        if (empty($app_info)) {
            libErrno::new()->set(402, 1);
            return false;
        }

        //Access blocked
        if (0 !== $app_info['app_status']) {
            libErrno::new()->set(403, 1);
            return false;
        }

        //Copy valid app data to channel
        $this->app_user->app_id     = &$app_info['app_id'];
        $this->app_user->app_key    = &$app_key;
        $this->app_user->app_secret = &$app_info['app_secret'];

        //All passed
        return true;
    }

    /**
     * Check data sign (remove FILES. add app_secret. sort by keys)
     *
     * @param string $sign
     *
     * @return bool
     */
    public function chkSign(string $sign = ''): bool
    {
        //Copy data from IOUnit
        $input_data = IOUnit::new()->src_input;

        //Remove All FILES keys (Don't sign uploaded file values)
        $input_data = array_diff_key($input_data, array_keys($_FILES));

        //Remove sign value
        unset($input_data['sign']);

        //Add app_secret
        $input_data['app_secret'] = $this->app_user->app_secret;

        //Sort data by keys
        ksort($input_data);

        //Build data query
        $query = http_build_query($input_data);

        //Compare data sign
        if ($sign !== hash('md5', $query)) {
            libErrno::new()->set(404, 1);
            return false;
        }

        //All passed
        return true;
    }

    /**
     * Prepare API arguments
     *
     * @return bool
     * @throws ReflectionException
     */
    public function prepArgs(): bool
    {
        /** @var IOUnit $io_unit */
        $io_unit = IOUnit::new();
        /** @var Reflect $reflect */
        $reflect = Reflect::new();
        /** @var Execute $execute */
        $execute = Execute::new();

        //Copy c_list from Router
        $c_list = Router::new()->cgi_cmd;

        //Escape XSS attacks
        $this->escapeXSS($io_unit->src_input);

        foreach ($c_list as $c) {
            $args = $reflect->getArgs($c[0], $c[1], $io_unit->src_input);

            if (!empty($args['diff'])) {
                libErrno::new()->set(405, 1, 'Param error: [' . implode(', ', $args['diff']) . '] @API #' . ($c[2] ?? $c[0] . '/' . $c[1]));
                return false;
            }

            //Add arguments for command
            $execute->addArgs($c[0], $c[1], $args['args']);
        }

        return true;
    }

    /**
     * Make stats
     *
     * @return bool
     */
    public function apiStats(): bool
    {
        //Copy useful values
        $ip  = App::new()->client_ip;
        $cmd = Router::new()->cgi_cmd;

        $app_id     = $this->app_user->app_id;
        $app_key    = $this->app_user->app_key;
        $app_secret = $this->app_user->app_secret;

        //todo Copy user ID from input data or DB
        $user_id = 'xxxxx';

        //todo Call your own stats function

        //Always return true
        return true;
    }

    /**
     * Escape input data for XSS
     *
     * @param array $input_data
     */
    private function escapeXSS(array &$input_data): void
    {
        foreach ($input_data as $key => &$value) {
            //Escape values recursively
            if (is_array($value)) {
                $this->escape($value);
                continue;
            }

            //Only escape string values
            if (is_string($value)) {
                $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }
    }
}