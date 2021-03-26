<?php

require __DIR__ . '/../../NervSys/NS.php';

$core_api = \Ext\libCoreApi::new()
    ->readHeaderKeys('TOKEN')
    ->addCorsRecord('*', 'TOKEN');

//Check Token
$core_api->hookBefore('api/', \app\hook::class, 'chkToken');
//Check data sign
$core_api->hookBefore('api/', \app\hook::class, 'chkSign');
//Prepare API arguments
$core_api->hookBefore('api/', \app\hook::class, 'prepareArgs');

//Call stats after API
$core_api->hookAfter('api/', \app\hook::class, 'apiStats');

NS::new();