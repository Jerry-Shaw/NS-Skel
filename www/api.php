<?php

//Require NS main script
require __DIR__ . '/../../NervSys/NS.php';

//Set core env
$core_api = \Ext\libCoreApi::new()
    ->readHeaderKeys('app_key')
    ->addCorsRecord('*', 'app_key');

//Check authority
$core_api->hookBefore('api/', \app\hook::class, 'chkAuth');
//Check data signature
$core_api->hookBefore('api/', \app\hook::class, 'chkSign');
//Prepare API arguments
$core_api->hookBefore('api/', \app\hook::class, 'prepArgs');

//Call stats after API
$core_api->hookAfter('api/', \app\hook::class, 'apiStats');

//Call NS
NS::new();