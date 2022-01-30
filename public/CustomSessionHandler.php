<?php
class CustomSessionHandler 
{

    private $data = [];
    private $secret;
    private static $session;

    public function __construct()
    {
        $this->secret = $this->secret();

        if (isset($_COOKIE['PHPSESSID']))
        {
            $split = explode('.', $_COOKIE['PHPSESSID']);

            if(isset($split[0]) && isset($split[1]) && isset($split[2])){

                $algo = base64_decode($split[0]);
                $data = base64_decode($split[1]);
                $signature = base64_decode($split[2]);

            }else{

                $error = urlencode("Something went wrong");
                header("Location: /?error={$error}");
                exit;

            }

            if($signature === hash_hmac('sha256', "${algo}.${data}" , $this->secret)){

                $this->data = json_decode($data, true);

            }else{

                $error = urlencode("Something went wrong");
                header("Location: /?error={$error}");
                exit;
            }   
        }

        self::$session = $this;
    }

    public function secret()
    {
        $db = Database::getDatabase();
        $data = $db->query("SELECT secret FROM definitely_not_a_flag" , []);
        if($data){
            return $data['secret'];
        }else{
            return md5(random_bytes(32));
        }
    }


    public static function getSession(): CustomSessionHandler 
    {
        return self::$session;
    }

    public function read($key)
    {
        return $this->data[$key] ?? null;
    }

    public function write($key, $val)
    {
        $this->data[$key] = $val;
    }

    public function save()
    {   
        $json = $this->toJson();
        $jsonb64 = str_replace("=","",base64_encode($json));
        $header = '{"alg":"sha256","typ":"hash hmac"}';
        $headerb64 = str_replace("=","",base64_encode($header));
        $signature = str_replace("=","",base64_encode(hash_hmac("sha256", "${header}.${json}", $this->secret)));

        setcookie('PHPSESSID', "${headerb64}.${jsonb64}.${signature}", time()+60*60*24, '/');
    }

    public function toJson()
    {
        ksort($this->data);
        return json_encode($this->data);
    }
}
