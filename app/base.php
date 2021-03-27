<?php

namespace app;

use Core\Factory;
use Core\Lib\App;
use Exception;
use Ext\libCache;
use Ext\libConfGet;
use Ext\libErrno;
use Ext\libLock;
use Ext\libPDO;
use Ext\libRedis;
use PDO;
use Redis;
use RedisException;

/**
 * Class base
 *
 * @package app
 */
class base extends Factory
{
    public string $app_env = 'prod';

    public PDO   $pdo;
    public Redis $redis;

    public App        $lib_app;
    public libLock    $lib_lock;
    public libConfGet $lib_conf;
    public libCache   $lib_cache;
    public libErrno   $lib_errno;

    /**
     * base constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        //Init App
        $this->lib_app = App::new();

        //Set App ENV
        $this->setEnv();

        //Load ENV config
        $this->lib_conf = libConfGet::new()->load('conf/' . $this->app_env . '.ini');

        //Init libraries
        $this->initLib();

        //Load error msg code file
        $this->lib_errno = libErrno::new()->load('app/msg/code.ini');

        //Set default errno
        $this->lib_errno->set(200);
    }

    /**
     * Set App ENV
     */
    private function setEnv(): void
    {
        if (!is_file($env_file = ($this->lib_app->root_path . '/conf/.env'))) {
            return;
        }

        $env = trim(file_get_contents($env_file));

        if (!in_array($env, ['prod', 'test', 'dev'], true)) {
            return;
        }

        if ('prod' !== $env) {
            $this->lib_app->setCoreDebug(true);
        }

        $this->app_env = &$env;
    }

    /**
     * Init libraries
     *
     * @throws RedisException
     */
    private function initLib(): void
    {
        $this->pdo   = libPDO::new($this->lib_conf->use('mysql'))->connect();
        $this->redis = libRedis::new($this->lib_conf->use('redis'))->connect();

        $this->lib_lock  = libLock::new()->bindRedis($this->redis);
        $this->lib_cache = libCache::new()->bindRedis($this->redis);
    }
}