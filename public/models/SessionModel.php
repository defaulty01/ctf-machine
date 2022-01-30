<?php
class SessionModel extends Model
{

    public $user;

    public function __construct(){
        parent::__construct();
        $this->user = $this->session->read('username');
    }

    public function getData()
    {
        $data = $this->database->query("SELECT 0 FROM urls WHERE url_short = '".$this->user."'" , []);

        if ($data) {
            return base64_encode($data[0]);
        }

        return false;
        
    }

    public static function update()
    {
        $user = new self;
        if (is_null($user->user)) return;
        if($user->getData()){
            $user->session->write('data', $user->getData());
        }

        $user->session->save();
        
    }
}