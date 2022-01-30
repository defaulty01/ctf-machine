<?php
class PdfController extends Controller{
    
    public function index($router,$params){
        $router->view('maintenance', []);
    }

    private function download($file,$data)
    {
        $data = new PdfModel($file,$data);
    }

}