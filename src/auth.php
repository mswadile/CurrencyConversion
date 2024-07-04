<?php
class Auth{
    private $pdo;
    public $reset_link;

    public function __construct($pdo){
        $this -> pdo = $pdo;
        date_default_timezone_set("Asia/Calcutta");
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

    public function sendPasswordResetToken($email)
    {
        $user = $this->getUserByEmail($email);
        if ($user) {
            $token = bin2hex(random_bytes(50));
            $expires_at = date("Y-m-d H:i:s", strtotime('+1 hour'));

            $stmt = $this->pdo->prepare("INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$user['id'], $token, $expires_at]);

            //set reset link
            $this -> reset_link = "http://localhost/CurrencyConversion/public/reset_password.php?token=$token";

            return true;
        }

        return false;
    }

    private function getUserByEmail($email)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByToken($token)
    {
        $stmt = $this->pdo->prepare("SELECT users.* FROM users JOIN password_reset_tokens ON users.id = password_reset_tokens.user_id WHERE password_reset_tokens.token = ? AND password_reset_tokens.expires_at > NOW()");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePassword($user_id, $new_password)
    {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$hashed_password, $user_id]);
    }

    public function deleteToken($token)
    {
        $stmt = $this->pdo->prepare("DELETE FROM password_reset_tokens WHERE token = ?");
        return $stmt->execute([$token]);
    }


}

?>