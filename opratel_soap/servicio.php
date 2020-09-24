<?php
include_once('lib/nusoap.php');

$service = new soap_server();

$nameSpace = "urn:mywsdlservice";
$service->configureWSDL('soapTest', $nameSpace);

$service->schemaTargetNamespace = $nameSpace;

$service->register('addUser', array('username' => 'xsd:string', 'password' => 'xsd:string', 'email' => 'xsd:string'), array('return' => 'xsd:string'), $nameSpace);
$service->register('activateUser', array('username' => 'xsd:string'), array('return' => 'xsd:string'), $nameSpace);
$service->register('deactivateUser', array('username' => 'xsd:string'), array('return' => 'xsd:string'), $nameSpace);
$service->register('getUser', array('username' => 'xsd:string'), array('return' => 'xsd:string'), $nameSpace);

function dbConnection(){
    return mysqli_connect('localhost:33060', 'root', 'secret','user_db');
}

function query($sql, $params){

    if(mysqli_query(dbConnection(), $sql)){

        $response = array ('0' => 'status_code');
        $message = "INFO - Procesamos request {$params['action']} | username: {$params['username']} | password: {$params['password']} | email: {$params['email']}";
        writeLog($message);

        $xml = new SimpleXMLElement('<root/>');
        array_walk_recursive($response, array ($xml, 'addChild'));
        return $xml->asXML();
    }
    else{
        $message = "ERROR - Hubo un error al intentar procesar request {$params['action']} | username: {$params['username']} | password: {$params['password']} | email: {$params['email']}";
        writeLog($message);

        return 'ERROR';
    }
}

function addUser($username, $password, $email){
    $sql = "INSERT INTO `user_db`.`user` (`username`, `password`, `email`) VALUES ('{$username}', '{$password}', '{$email}');";
    $params = [
        'username' => $username,
        'password' => $password,
        'email' => $email,
        'action' => 'addUser'
    ];
    return query($sql, $params);
}

function activateUser($username){
    $sql = "UPDATE `user_db`.`user` SET `active`='1' WHERE `username`='{$username}';";
    $params = [
        'username' => $username,
        'password' => '',
        'email' => '',
        'action' => 'activateUser'
    ];
    return query($sql, $params);
}

function deactivateUser($username){
    $sql = "UPDATE `user_db`.`user` SET `active`='0' WHERE `username`='{$username}';";
    $params = [
        'username' => $username,
        'password' => '',
        'email' => '',
        'action' => 'deactivateUser'
    ];
    return query($sql, $params);
}

function getUser($username){
    $sql = "SELECT u.username, u.password, u.email FROM user_db.user u where username = '{$username}';";
    $result = mysqli_query(dbConnection(), $sql);
    
    if(mysqli_num_rows($result) > 0){
        $result = array_flip(mysqli_fetch_assoc($result));
        $xml = new SimpleXMLElement('<root/>');
        array_walk_recursive($result, array ($xml, 'addChild'));

        $message = "INFO - Procesamos request getUser | username: {$username}";
        writeLog($message);

        return $xml->asXML();
    }
    else{
        $message = "ERROR - Hubo un error al intentar procesar request getUser | username: {$username}";
        writeLog($message);
    }
}

function writeLog($message){    
    $date = date('Y-m-d H:i:s');
    file_put_contents('log.txt', "[{$date}] {$message} \n", FILE_APPEND);
}

$service->service(file_get_contents("php://input"));

// CREATE TABLE `user_db`.`user` (
//     `id` INT NOT NULL AUTO_INCREMENT,
//     `username` VARCHAR(45) NULL,
//     `password` VARCHAR(45) NULL,
//     `email` VARCHAR(45) NULL,
//     `active` TINYINT(1) NULL DEFAULT 0
//     PRIMARY KEY (`id`));

?>
