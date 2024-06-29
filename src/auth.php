<?php
class Auth{
    private $pdo;

    public function __construct($pdo){
        $this -> pdo = $pdo;
    }

    public function checkIp($allowedIps){
        return in_array($_SERVER['REMOTE_ADDR'],$allowedIps);
    }

    public function login($username,$password,$remember){
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if($user && password_verify($password,$user['password'])){
            if($remember){
                setcookie("user",$username,time() + (86400 * 30), "/" );
            }else{
                $_SESSION['user'] = $username;
            }
            return true;
        }

        return false;
    }



}

?>