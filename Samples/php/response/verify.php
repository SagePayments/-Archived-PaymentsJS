<?php
    require('../shared/shared.php');
    $req = json_decode(file_get_contents('php://input'));
    
    $results = [
        RequestIdHash => getHmac($req->RequestId, $developer['KEY']),
        ResponseHash => getHmac($req->Response, $developer['KEY'])
    ];
    
    echo json_encode($results);
?>