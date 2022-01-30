<?php

class UrlController extends Controller{

	public function index($router){
        return $router->view('save');
    }

    public function random_id(){
    	return substr(str_shuffle('abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789'),0,8);
    }

    public function create($router){
    	$id = $this->random_id();
    	$url = new UrlModel($id);

    	if ($url->check()){
    		$id = $this->random_id();
    		$url = new UrlModel($id);
    	}

        if (empty($_POST['link']))
        {
            header('Location: /');
            exit;
        }

        if($url->isValidUrl($_POST['link'])){
			$url->save($_POST['link']);
			header("Location: /p/{$id}");
			exit;
        }else{
        	header('Location: /?error='.urlencode('Invalid URL'));
        	exit;
        }
    }

    public function preview($router, $params){

    	$path = $params[0];

    	$url = new UrlModel($path);

    	if (!$url->checkPreview())
        {
            $router->abort(404);
        }
        $meta = $url->getMetadata();
        #var_dump($meta);
    	$router->view('show', ['link' => $url->link,'data' => $meta]);
    }


    public function show($router, $params){

    	$path = $params[0];

    	$url = new UrlModel($path);

    	if (!$url->check())
        {
            $router->abort(404);
        }

    	$url->relocate();
    }
}		