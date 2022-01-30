<?php

class UrlModel extends Model{

    public $link;
    public $url;
    public $meta;

    public function __construct($link)
    {
        $this->link = urldecode($link);
        parent::__construct();
    }

    public function isValidUrl($url){
    	if(preg_match('/(127\.)|(localhost)|(\:\:1)/i', $url)){
    		return false;
    	}

    	if(preg_match('/^https?:\/\/(.*)/i', $url)){
    		return true;
    	}
    }

    public function getMetadata(){
    	$content = $this->getContent();

        preg_match('/(?<=property\=[\"|\']og:url[\"|\']\ content=[\"|\']).*(?=[\"|\'])/i', $content, $meta_url);
        preg_match('/(?<=property\=[\"|\']og:title[\"|\']\ content=[\"|\']).*(?=[\"|\'])/i', $content, $meta_title);
        preg_match('/(?<=property\=[\"|\']og:description[\"|\']\ content=[\"|\']).*(?=[\"|\'])/i', $content, $meta_description);
        preg_match('/(?<=property\=[\"|\']og:image[\"|\']\ content=[\"|\']).*(?=[\"|\'])/i', $content, $meta_image);

        $this->meta['meta_url'] = ($meta_url !== []) ? $meta_url[0] : NULL;
        $this->meta['meta_title'] = ($meta_title !== []) ? $meta_title[0] : NULL;
        $this->meta['meta_desc'] = ($meta_description !== []) ? $meta_description[0] : NULL;
        $this->meta['meta_image'] = ($meta_image !== []) ? file_get_contents($meta_image[0]) : NULL;

        return $this->meta;
    }

    public function relocate(){
        try {
            $data = $this->database->query("SELECT url_name FROM urls WHERE url_short = ?" , ['s' => [$this->link]]);
            $this->url = $data->fetch_all(MYSQLI_ASSOC) ?? false;

            if($data->num_rows >= 1){
                header("Location: {$this->url[0]['url_name']}");
            }

        } catch (Exception $e) {
            $error = urlencode('Something went wrong');
            header("Location: /?error=${error}");
            exit;
        }
        
    }

    public function getContent(){
        return file_get_contents($this->url[0]['url_name']);
    }

    public function check(){
        try {
            $data = $this->database->query("SELECT url_name FROM urls WHERE url_short = '$this->link'" , []);
            $this->url = $data;
            return $this->url ?? false;
        } catch (Exception $e) {
            return false;
        }

    }

    public function checkPreview(){
        try {
            $data = $this->database->query("SELECT url_name FROM urls WHERE url_short = ?" , ['s' => [$this->link]]);
            $this->url = $data->fetch_all(MYSQLI_ASSOC) ?? false;
            return $this->url;
        } catch (Exception $e) {
            return false;
        }
    }


    public function save($url = Null){
        $this->database->query('INSERT INTO urls(url_short, url_name) VALUES(?,?)', [
            's' => [$this->link,$url]
        ]);
    }


    public function __destruct()
    {
        if(isset($this->meta['meta_url'])){
            unset($this->meta['meta_url']);
        }

        if(isset($this->meta['meta_title'])){
            unset($this->meta['meta_title']);
        }
        
        if(isset($this->meta['meta_desc'])){
            unset($this->meta['meta_desc']);
        }
        
        if(isset($this->meta['meta_image'])){
            unset($this->meta['meta_image']);
        }

    }
}
