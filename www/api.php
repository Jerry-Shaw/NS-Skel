<?php

//Require NS main script
require __DIR__ . '/../../NervSys/NS.php';

//Set core env
$core_api = \Ext\libCoreApi::new()
    ->readHeaderKeys('app_id')
    ->addCorsRecord('*', 'app_id');

//Check data sign
$core_api->hookBefore('api/', \app\hook::class, 'chkSign');
//Prepare API arguments
$core_api->hookBefore('api/', \app\hook::class, 'prepareArgs');

//Call stats after API
$core_api->hookAfter('api/', \app\hook::class, 'apiStats');

NS::new();