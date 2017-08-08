<?php
$DB_host = "localhost";
$DB_USER = "root";
$DB_PASSWORD = "root";
$DB_NAME = "matcha42";
$DB_DSN = 'mysql:dbname='.$DB_NAME.';host='.$DB_host;

$pdo = new PDO('mysql:host=' . $DB_host, $DB_USER, $DB_PASSWORD, [PDO::MYSQL_ATTR_LOCAL_INFILE => true]);
$pdo->query("CREATE DATABASE $DB_NAME;
                CREATE USER $DB_USER@$DB_host IDENTIFIED BY $DB_PASSWORD;
                GRANT ALL ON $DB_NAME.* TO $DB_USER@$DB_host;
                FLUSH PRIVILEGES;");
try{
    echo '- START -'.PHP_EOL;
    print_r("User=".$DB_USER.PHP_EOL);
    print_r("Password=".$DB_PASSWORD.PHP_EOL);
    $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD, [PDO::MYSQL_ATTR_LOCAL_INFILE => true]);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // ERRMODE_WARNING | ERRMODE_EXCEPTION | ERRMODE_SILENT
    echo '- Droping tables -'.PHP_EOL;
    $pdo->query("DROP TABLE IF EXISTS users");
    $pdo->query("DROP TABLE IF EXISTS pictures");
    $pdo->query("DROP TABLE IF EXISTS userinterests");
    $pdo->query("DROP TABLE IF EXISTS userlocation");
    $pdo->query("DROP TABLE IF EXISTS iplocation");
    $pdo->query("DROP TABLE IF EXISTS likes");
    $pdo->query("DROP TABLE IF EXISTS notifications");
    $pdo->query("DROP TABLE IF EXISTS messages");
    echo '- Create tables -'.PHP_EOL;
    $pdo->query("CREATE TABLE users ( 
    id               INTEGER               PRIMARY KEY AUTO_INCREMENT,
    name             VARCHAR( 255 )        NOT NULL,
    lastname         VARCHAR( 255 )        NOT NULL,
    mail             VARCHAR( 255 )        NOT NULL,
    password         VARCHAR( 512 )        NOT NULL,
    age              INT                   NOT NULL,
    gender           VARCHAR (6),
    orientation      VARCHAR(255),
    popularity       INT                   NOT NULL DEFAULT '0',
    resume           VARCHAR(140),
    interests        VARCHAR (8000),
    last_seen        VARCHAR (255),
    is_connected     BOOLEAN               NOT NULL DEFAULT '0',
    token            VARCHAR( 255 )        NOT NULL,
    verified         BOOLEAN               NOT NULL,
    created_at       DATETIME              NOT NULL,
    updated_at       DATETIME
    );");
    $pdo->query("CREATE TABLE pictures (
    id               INTEGER               PRIMARY KEY AUTO_INCREMENT,
    id_user          INTEGER               NOT NULL,
    url              VARCHAR (255)         NOT NULL,
    is_profil        BOOLEAN               NOT NULL ,
    created_at       DATETIME              NOT NULL,
    updated_at       DATETIME
    );");
    $pdo->query("CREATE TABLE userinterests (
    id               INTEGER               PRIMARY KEY AUTO_INCREMENT,
    interest         VARCHAR (140)         NOT NULL,
    id_user          INTEGER               NOT NULL,
    created_at       DATETIME              NOT NULL,
    updated_at       DATETIME
    );");
    $pdo->query("CREATE TABLE userlocation (
    id               INTEGER               PRIMARY KEY AUTO_INCREMENT,
    country          VARCHAR (140)         NOT NULL,
    region           VARCHAR (140)         NOT NULL,
    zipCode          INTEGER,
    city             VARCHAR (140)         NOT NULL,
    lat              FLOAT,
    lon              FLOAT,
    id_user          INTEGER               NOT NULL,
    created_at       DATETIME              NOT NULL,
    updated_at       DATETIME
    );");
    $pdo->query("CREATE TABLE likes (
    id               INTEGER               PRIMARY KEY AUTO_INCREMENT,
    id_user          INTEGER               NOT NULL,
    id_user_like     INTEGER               NOT NULL,
    is_match         TINYINT(1)            NOT NULL DEFAULT '0',
    created_at       DATETIME              NOT NULL,
    updated_at       DATETIME
    );");
    $pdo->query("CREATE TABLE notifications (
    id               INTEGER               PRIMARY KEY AUTO_INCREMENT,
    type             VARCHAR(255)          NOT NULL ,
    id_user          INTEGER               NOT NULL,
    id_user_dest     INTEGER               NOT NULL,
    reading          TINYINT(1)            NOT NULL DEFAULT '0',
    message          VARCHAR(255)          NOT NULL ,
    link             VARCHAR(255),
    created_at       DATETIME              NOT NULL,
    updated_at       DATETIME
    );");
    $pdo->query("CREATE TABLE messages (
    id               INTEGER               PRIMARY KEY AUTO_INCREMENT,
    idNotif          INTEGER               NOT NULL ,
    id_user          INTEGER               NOT NULL,
    id_user_dest     INTEGER               NOT NULL,
    message          VARCHAR(255)          NOT NULL ,
    created_at       DATETIME              NOT NULL,
    updated_at       DATETIME
    );");
//    $pdo->query("CREATE TABLE iplocation (
//    ip_from          INT(10)               UNSIGNED,
//    ip_to            INT(10)               UNSIGNED,
//    country_code     CHAR (2),
//    country          VARCHAR (64),
//    region           VARCHAR(128),
//    city             VARCHAR(128),
//    lat              DOUBLE,
//    lon              DOUBLE,
//
//    INDEX idx_ip_from (ip_from),
//                               INDEX idx_ip_to (ip_to),
//                               INDEX idx_ip_from_to (ip_from, ip_to) )
//    ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin
//    ;");
//    $pdo->query("CREATE TABLE comments (
//    pic_id           INTEGER               ,
//    login            VARCHAR(255)          NOT NULL,
//    comments         TEXT                  NOT NULL,
//    post_at          DATETIME              NOT NULL
//    );");
//    $pdo->query("CREATE TABLE likes (
//    id               INTEGER               ,
//    pic_id           INTEGER               ,
//    login            VARCHAR(255)
//    );");
    if ($pdo)
    {
//        $pdo->query("INSERT INTO users (name, lastname, mail, password, age, token, verified, created_at)
//                              VALUES ('Hoareau', 'Alexandre', 'hoa.alexandre@gmail.com', '74dfc2b27acfa364da55f93a5caee29ccad3557247eda238831b3e9bd931b01d77fe994e4f12b9d4cfa92a124461d2065197d8cf7f33fc88566da2db2a4d6eae', '28', 'toto', 1, CURRENT_DATE),
//                              ('Medarhri', 'Roeam', 'mroeam@live.fr', '74dfc2b27acfa364da55f93a5caee29ccad3557247eda238831b3e9bd931b01d77fe994e4f12b9d4cfa92a124461d2065197d8cf7f33fc88566da2db2a4d6eae', '25', 'tutu', 1, CURRENT_DATE)");

        echo "Database : ".$DB_NAME." created".PHP_EOL;
    }
    else
    {
        die(print_r($pdo->errorInfo(), true));
    }
    $pdo = null;
} catch(Exception $e) {
    echo "Error : " . $e->getMessage() . "\n";
    die();
}