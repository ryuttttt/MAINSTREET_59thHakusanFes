<?php

function h($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'utf-8');
}

    function connect()
    {
        $dsn = 'mysql:host=xxx;port=xxx;dbname=xxx;charset=utf8mb4;';
        $username = 'xxx';
        $password = '^%^%zik5nty3&2yz$$u8wk^fn#48xd^&v^to%hxky%u26at7umt389dw23hhs66mvf%i94';
            $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];
        $pdo = new PDO($dsn, $username, $password, $options);
        return $pdo;
    } 
?>
