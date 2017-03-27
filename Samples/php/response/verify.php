<?php
    require('../shared/shared.php');
    $req = json_decode(file_get_contents('php://input'));
    
    $results = [
        hash => getHmac($req, $developer['KEY']),
    ];
    
    echo json_encode($results);
?>