<?php
$DB_host = "localhost";
$DB_USER = "root";
$DB_PASSWORD = "root";
$DB_NAME = "matcha42";
$DB_DSN = 'mysql:dbname='.$DB_NAME.';host='.$DB_host;


try{
    echo '- START -'.PHP_EOL;
    print_r("user=".$DB_USER.PHP_EOL);
    print_r("password=".$DB_PASSWORD.PHP_EOL);
    $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // ERRMODE_WARNING | ERRMODE_EXCEPTION | ERRMODE_SILENT
    echo '- Droping tables -'.PHP_EOL;
    $pdo->query("DROP TABLE IF EXISTS users");
    $pdo->query("DROP TABLE IF EXISTS pictures");
    $pdo->query("DROP TABLE IF EXISTS interests");
//    $pdo->query("DROP TABLE IF EXISTS comments");
//    $pdo->query("DROP TABLE IF EXISTS likes");
    echo '- Create tables -'.PHP_EOL;
    $pdo->query("CREATE TABLE users ( 
    id               INTEGER               PRIMARY KEY AUTO_INCREMENT,
    name             VARCHAR( 255 )        NOT NULL,
    lastname         VARCHAR( 255 )        NOT NULL,
    mail             VARCHAR( 255 )        NOT NULL,
    password         VARCHAR( 512 )        NOT NULL,
    age              INT                   NOT NULL,
    gender           VARCHAR (1),
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
    $pdo->query("CREATE TABLE interests (
    id               INTEGER               PRIMARY KEY AUTO_INCREMENT,
    interest         VARCHAR (140)         NOT NULL,
    created_at       DATETIME              NOT NULL,
    updated_at       DATETIME
    );");
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
        $pdo->query("INSERT INTO users (name, lastname, mail, password, age, token, verified, created_at)
                              VALUES ('Hoareau', 'Alexandre', 'hoa.alexandre@gmail.com', '74dfc2b27acfa364da55f93a5caee29ccad3557247eda238831b3e9bd931b01d77fe994e4f12b9d4cfa92a124461d2065197d8cf7f33fc88566da2db2a4d6eae', '28', 'toto', 1, CURRENT_DATE),
                              ('Medarhri', 'Roeam', 'mroeam@live.fr', '74dfc2b27acfa364da55f93a5caee29ccad3557247eda238831b3e9bd931b01d77fe994e4f12b9d4cfa92a124461d2065197d8cf7f33fc88566da2db2a4d6eae', '25', 'tutu', 1, CURRENT_DATE)");
        $pdo->query("INSERT INTO interests (interest, created_at) VALUES ('#licornes', CURRENT_DATE)");
echo "Database : ".$DB_NAME." created".PHP_EOL;
    }
    else
    {
        die(print_r($pdo->errorInfo(), true));
    }
    $pdo = null;
} catch(Exception $e) {
    echo "Impossible d'accéder à la base de données Mysql : ".$e->getMessage();
    die();
}