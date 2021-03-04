<?php

require __DIR__ . '/../../NervSys/NS.php';

$core_api = \Ext\libCoreApi::new();

//Open core debug mode
$core_api->setCoreDebug(true);

//Open CORS permission
$core_api->addCorsRecord('*');

//Add include path
$core_api->addIncPath('pkgs/inc');

//Start NS
NS::new();