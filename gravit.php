<?php
    require 'core/config.php';

    $port = $conf['mysql']['port'];
    $driver = 'mysql';
    $host = $conf['mysql']['host'];
    $db_name = $conf['mysql']['db'];
    $charset = $conf['mysql']['charset'];
    $prefix = $conf['mysql']['prefix'];
    
    try {
        $pdo = new PDO("$driver:host=$host;port=$port;dbname=$db_name;charset=$charset",
            $conf['mysql']['username'],
            $conf['mysql']['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
    catch(PDOException $e) {
        die('Connetion estabilished.');
    }

    if(isset($_GET['login']) && isset($_GET['pass']) && isset($_GET['ip'])) {
        $login = trim($_GET['login']);
        $password = trim($_GET['pass']);
        $ip = urldecode(trim($_GET['ip']));
        $sql =  "SELECT * FROM `".$prefix."users` WHERE `username` = ? OR `email` = ? LIMIT 1";
        $stmt = $pdo -> prepare($sql);
        $stmt -> execute([$login, $login]);
        $user = $stmt -> fetch(PDO::FETCH_OBJ);
        if($user) {
            if(password_verify($password, $user -> password)) {
                $sql = "UPDATE `".$prefix."users` SET `lastip` = ?, `last_online` = ? WHERE `id` = ".$user -> id;
                $stmt = $pdo->prepare($sql);
                $stmt -> execute([$ip, time()]);
                $out_login = $user -> nickname;

                echo('OK:'.$out_login.':0');
            }
            else {
                echo('ERROR. Wrong login or password');
            }
        }
        else {
            echo('ERROR. Wrong login or password');
        }
    }
   
