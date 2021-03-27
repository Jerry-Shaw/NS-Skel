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
    public string $app_key = '';

    /**
     * Check data sign (remove FILES. add app_key. sort by keys)
     *
     * @param string $app_id
     * @param string $sign
     * @param int    $t (timestamp, in second)
     *
     * @return bool
     */
    public function chkSign(string $app_id = '', string $sign = '', int $t = 0): bool
    {
        //Validate timestamp (valid in 10 minutes)
        if (abs(time() - $t) > 600) {
            libErrno::new()->set(1, 400, 'Requested timestamp ERROR!');
            return false;
        }

        //Validate app_id
        if ('' === $app_id) {
            libErrno::new()->set(1, 401, 'Authorization Required!');
            return false;
        }

        //todo Read app_key from DB by app_id
        $app_key = 'xxxx';

        //App NOT registered
        if ('' === $app_key) {
            libErrno::new()->set(1, 402, 'Authorization Required!');
            return false;
        }

        //Copy data from IOUnit
        $input_data = IOUnit::new()->src_input;

        //Remove All FILES keys (Don't sign uploaded file values)
        $input_data = array_diff_key($input_data, array_keys($_FILES));

        //Remove sign value
        unset($input_data['sign']);

        //Add app_key
        $this->app_key         = &$app_key;
        $input_data['app_key'] = &$app_key;

        //Sort data by keys
        ksort($input_data);

        //Build data query
        $query = http_build_query($input_data);

        //Compare data sign
        if ($sign !== hash('md5', $query)) {
            libErrno::new()->set(1, 403, 'Signature Error!');
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
        //Init needed libs
        $io_unit = IOUnit::new();
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
     * @param string $app_id
     *
     * @return bool
     */
    public function apiStats(string $app_id): bool
    {
        //Copy useful values
        $ip  = App::new()->client_ip;
        $cmd = Router::new()->cgi_cmd;

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
            //Skip app_id, sign
            if (in_array($key, ['app_id', 'sign'], true)) {
                continue;
            }

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