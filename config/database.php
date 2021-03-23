<?php
/* Database Configuration */
return array(
    /**
     * 
     * Database Sample Project
     * =======================
     * Install database sample via terminal
     * cd assests/
     * sudo php install.php
     * 
     * Tunneling Database
     * ssh -N -L 13306:127.0.0.1:3306 root@domain.com
     * 
     */
    'database' => array(
        'driver' => 'mysql',
        'host' => $_ENV['MYSQL_HOST'] ?? 'localhost',
        'port' => $_ENV['MYSQL_PORT'] ?? '3306',
        'user' => $_ENV['MYSQL_USER'] ?? 'batangpolres',
        'password' => $_ENV['MYSQL_PASS'] ?? 'VGaKFoRnvIcO',
        'dbname' => $_ENV['MYSQL_DBNAME'] ?? 'batangpo_ppkm',
        'charset' => 'utf8',
        'collate' => 'utf8_general_ci',
        'persistent' => false,
        'errorMsg' => 'Maaf, Gagal terhubung dengan database',
    ),
);
/*----------------------*/
?>
