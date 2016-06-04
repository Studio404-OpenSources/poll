<?php
class studio404_poll extends studio404_ajax{
	public $error = array();

	public function __construct(){
		/* Manipulate poll's style */
		$this->option = array(
			"main_id"=>"studio404_pollContainer",
			"css"=>array(
				"margin"=>"20px 0", 
				"padding"=>"0px", 
				"width"=>"100%", 
				"border"=>"solid 1px #f2f2f2" 
			), /* poll box styling */
			"header"=>array(
					"css"=>array(
						"margin"=>"0px", 
						"padding"=>"0px 10px", 
						"width"=>"calc(100% - 20px)",
						"height"=>"40px",
						"line-height"=>"40px", 
						"background-color"=>"#f2f2f2", 
						"font-size"=>"16px", 
						"color"=>"#555555"
					)
				), /* header box styling */
			"poll_question"=>array(
					"css"=>array(
						"margin"=>"0px", 
						"padding"=>"10px",
						"font-size"=>"14px",
						"line-height"=>"30px",
						"color"=>"#555555"
						)
					), /* question styling */
			"poll_answers"=>array(
				"css"=>array(
					"svgbox"=>array(
						"margin"=>"0 10px",
						"width"=>"-webkit-calc(100% - 20px)",
						"width"=>"-moz-calc(100% - 20px)",
						"width"=>"calc(100% - 20px)"
					),
					"svg"=>array(
						"width"=>"calc(100% - 20px)", 
						"height"=>"25px", 
						"margin"=>"0 10px 10px 10px", 
						"cursor"=>"pointer" 
					),
					"rectbox"=>array(
						"fill"=>"#dddddd",
						"width"=>"100%",
					), 
					"rectanswer"=>array(
						"fill"=>"#1fa67a"
					), 
					"text"=>array(
						"font-size"=>"12px", 
						"fill"=>"#ffffff", 
					)
				) /* answer box styling */
			)
		);
	}
	
	
	public function lanch($mainoptions){
		$this->mainoptions = $mainoptions;
		if(
			!empty($this->mainoptions['poll_id']) && 
			!empty($this->mainoptions['header_text']) && 
			!empty($this->mainoptions['poll_question']) && 
			!empty($this->mainoptions['poll_answers']) && 
			count($this->mainoptions['poll_answers']) >= 2
		){/* important values is set */

			$getPermition = $this->getPermition();
			if($getPermition && ($getPermition=="0755" || $getPermition=="0775" || $getPermition=="0777")){
				echo $this->makeFrontEnd();
				return true;
			}

			$this->error[] = sprintf(
				"<b>%s</b> დირექტორიას სასურველია ჰქონდეს 0755 / 0775 / 0777 ნებართვა !", 
				$this->mainoptions['temp_path']
			);	
		}
		$this->error[] = "გთხოვთ გადაამოწმოთ \$main_options ცვლადი !";
		return false;
	}

	private function random($length)
	{
		$bytes = openssl_random_pseudo_bytes($length * 2);
		return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
	}

	private function makeFrontEnd(){
		$out = '';
		if(!studio404_ajax::method("GET","ajax")){
			$out .= sprintf('<div id="%s">', $this->option['main_id']);
		}
		$out .= sprintf(
			'<div id="pollbox%s" style="%s">', 
			$this->mainoptions['poll_id'], 
			$this->arrayToStyle($this->option['css'])
		); 

		$out .= sprintf(
			'<div id="header%s" style="%s">%s</div>', 
			$this->mainoptions['poll_id'], 
			$this->arrayToStyle($this->option['header']['css']),
			$this->mainoptions['header_text']
		); 

		$out .= sprintf(
			'<div id="question%s" style="%s">%s</div>', 
			$this->mainoptions['poll_id'], 
			$this->arrayToStyle($this->option['poll_question']['css']),
			$this->mainoptions['poll_question']
		); 

		$out .= sprintf(
			'<div id="svg-box%s" style="%s">', 
			$this->mainoptions['poll_id'], 
			$this->arrayToStyle($this->option['poll_answers']['css']['svgbox'])
		);
		$x = 1;		
		foreach ($this->mainoptions['poll_answers'] as $t) {
			$persentString = $this->countAnswers($x)."%";
			$out .= sprintf(
				'<svg style="%s" onclick="makeavote(%s,%s)">
				<rect height="25" width="%s" style="%s" />
				<rect width="%s" height="25" style="%s" />
				<text x="10" y="18" style="%s">%s %s</text>
				</svg>
				', 
				$this->arrayToStyle($this->option['poll_answers']['css']['svg']), 
				$this->mainoptions['poll_id'], 
				$x,
				"100%", 
				$this->arrayToStyle($this->option['poll_answers']['css']['rectbox']),
				$persentString, 
				$this->arrayToStyle($this->option['poll_answers']['css']['rectanswer']), 
				$this->arrayToStyle($this->option['poll_answers']['css']['text']),
				$t, 
				$persentString
			); 
			$x++;
		}  
		$out .= '</div>';

		
		$out .= sprintf(
			'<script> 
			function makeavote(q, a) {
				document.getElementById("%s").innerHTML = "%s";
				var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function() {
					if (xhttp.readyState == 4 && xhttp.status == 200) {
						location.reload(); 
					}
				};
				xhttp.open("GET", "index.php?ajax=true&qid="+q+"&aid="+a+"&p=%s", true);
				xhttp.send();
			}
			</script>', 
			$this->option['main_id'], 
			$this->mainoptions['please_wait'], 
			$this->mainoptions['temp_path']
		);

		$out .= '</div>'; 
		if(!studio404_ajax::method("GET","ajax")){
			$out .= '</div>';
		}
		return $out;
	}

	private function getPermition(){
		if(file_exists($this->mainoptions['temp_path'])){
			$fileperms = substr(sprintf('%o', fileperms($this->mainoptions['temp_path'])), -4);
			return $fileperms;
		}
		$this->error[] = sprintf(
			"<b>%s</b> დირექტორია ვერ მოიძებნა !", 
			$this->mainoptions['temp_path']
		); 
		return false;
	}

	private function arrayToStyle($css){
		$output = '';
		try{
			if(is_array($css)){
				$output = implode('; ', array_map(
					function ($v, $k) { return sprintf("%s:%s", $k, $v); },
					$css,
					array_keys($css)
				));
			}
		}catch(Exception $e){
			$this->error[] = sprintf(
				"მოხდა შეცდომა ! <b>%s</b>", 
				$e
			);
		}
		return $output;
	}	

	private function countAnswers($returnN){
		$pollFolder = sprintf(
			"%squestion%s/", 
			$this->mainoptions['temp_path'], 
			$this->mainoptions['poll_id']
		);
		$globs = glob($pollFolder."*");
		$num = array();
		foreach($globs as $file){ // iterate files
			$filePath = sprintf("%s", $file); 
			if(file_exists($filePath)){
				$f = json_decode(file_get_contents($filePath),true);
				$num[] = $f['answer_id']; 
			}
		}
		$valCount = array_count_values($num); 
		$allVals = count($num); 
		
		for($y=1; $y <= count($this->mainoptions['poll_answers']); $y++){
			if(!empty($valCount[$y])){
				$out2[$y] = sprintf('%s', (($valCount[$y] * 100) / $allVals));
			}
		}

		if(!empty($out2[$returnN])){
			return floor($out2[$returnN]); 
		}else{
			return "0";
		}
	}

}
?>