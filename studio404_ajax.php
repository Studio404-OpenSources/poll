<?php
class studio404_ajax{
	public $ip; 
	public function receiver(){
		if(
			$this->method("GET","ajax") && 
			$this->method("GET","qid") && 
			$this->method("GET","aid") && 
			$this->method("GET","p")
		){
			$this->ip = $_SERVER["REMOTE_ADDR"]; 
			$folder = sprintf("%squestion%s", $this->method("GET","p"), $this->method("GET","qid")); 
			if(!is_dir($folder)){
				mkdir($folder);
			}
			$mask = array(
				$folder."/".$this->ip.".*"
			);
			$this->rem($mask);


			$file = sprintf("%s/%s.json", $folder, $this->ip); 
			$fp = fopen($file, "w");
			$array = array("answer_id"=>$this->method("GET","aid"));
			fwrite($fp, json_encode($array));
			fclose($fp);
		}
	}	

	public static function method($type,$item){
		if($type=="POST" && isset($_POST[$item])){
			return filter_input(INPUT_POST, $item);
		}else if($type=="GET" && isset($_GET[$item])){
			return filter_input(INPUT_GET, $item);
		}else{
			return '';
		}
	}

	private function rem($mask){
		if(is_array($mask)){
			foreach ($mask as $v) {
				array_map('unlink', glob($v));
			}
		}else{
			array_map('unlink', glob($mask));
		}
	}
}

?>