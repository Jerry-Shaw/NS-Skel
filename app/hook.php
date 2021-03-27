<?php

namespace app;

use Core\Lib\App;
use Core\Lib\IOUnit;
use Core\Lib\Router;
use Core\Reflect;
use Ext\libErrno;

/**
 * Class hook
 *
 * @package app
 */
class hook extends base
{
    public channel $channel;

    /**
     * hook constructor.
     */
    public function __construct()
    {
        parent::new();
        $this->channel = channel::new();
    }

    /**
     * Check data sign (remove FILES. add app_secret. sort by keys)
     *
     * @param string $app_key
     * @param string $sign
     * @param int    $t (timestamp, in second)
     *
     * @return bool
     */
    public function chkSign(string $app_key = '', string $sign = '', int $t = 0): bool
    {
        //Validate timestamp (valid in 10 minutes)
        if (abs(time() - $t) > 600) {
            libErrno::new()->set(1, 400);
            return false;
        }

        //Validate app_id
        if ('' === $app_key) {
            libErrno::new()->set(1, 401);
            return false;
        }

        //todo Read app_id & app_secret from DB by app_key
        $app_id     = 'xxxx';
        $app_secret = 'xxxx';

        //App NOT registered
        if ('' === $app_id || '' === $app_secret) {
            libErrno::new()->set(1, 402);
            return false;
        }

        //Copy valid app data to channel
        $this->channel->app_id     = &$app_id;
        $this->channel->app_key    = &$app_key;
        $this->channel->app_secret = &$app_secret;

        //Copy data from IOUnit
        $input_data = IOUnit::new()->src_input;

        //Remove All FILES keys (Don't sign uploaded file values)
        $input_data = array_diff_key($input_data, array_keys($_FILES));

        //Remove sign value
        unset($input_data['sign']);

        //Add app_secret
        $input_data['app_secret'] = &$app_secret;

        //Sort data by keys
        ksort($input_data);

        //Build data query
        $query = http_build_query($input_data);

        //Compare data sign
        if ($sign !== hash('md5', $query)) {
            libErrno::new()->set(1, 403);
            return false;
        }

        return true;
    }

    /**
     * Prepare API arguments
     *
     * @return bool
     * @throws \ReflectionException
     */
    public function prepareArgs(): bool
    {
        /** @var IOUnit $io_unit */
        $io_unit = IOUnit::new();
        /** @var Reflect $reflect */
        $reflect = Reflect::new();

        //Copy c_list from Router
        $c_list = Router::new()->cgi_cmd;

        //Escape XSS attacks
        $this->escapeXSS($io_unit->src_input);

        foreach ($c_list as $c) {
            $args = $reflect->getArgs($c[0], $c[1], $io_unit->src_input);

            if (!empty($args['diff'])) {
                libErrno::new()->set(1, 404, 'Data error: [' . implode(', ', $args['diff']) . '] @ API #' . ($c[2] ?? $c[0] . '/' . $c[1]));
                return false;
            }

            //Add arguments for command
            $io_unit->addArgs($c[0], $c[1], $args['args']);
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

        $app_id     = $this->channel->app_id;
        $app_key    = $this->channel->app_key;
        $app_secret = $this->channel->app_secret;

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