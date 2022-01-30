<?php

class PdfModel extends Model{
	private $file;
	private $data;

	public function __construct($data, $file){
		chdir($_ENV['UPLOAD_DIR'] ?? '/tmp/');
		$this->file = (!empty($file)) ? $file : time().'.png';
		if(!empty($data)){
			$this->data = $data;
		}else{
			$error = urlencode("Something went wrong");
			header("Location: /?error={$error}");
			exit();
		}
	}

	public function generatePDF(){
		/**
		 * To do:
		 * Create PDF file of the link
		 */
	}

	public function delete(){
		if(!preg_match('/(\.{2,})|([\/\|\;\*\&])/i',$this->file)){
			include "/opt/flag/".DESSER_FLAG.".txt";
			system("rm {$this->file}");
		}else{
			$error = urlencode("NOT ALLOWED");
			header("Location: /?error={$error}");
			exit();
		}
	}

	public function __destruct(){
		$this->delete();
	}

}
