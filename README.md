# Introduction
The poll is fully customizable, It saves data in json files based on user's IP address.

# Example
http://c.404.ge/ 

# Installation
```php 
include("studio404_ajax.php"); 
include("studio404_poll.php"); 

if(
	isset($_GET['ajax'],$_GET['qid'],$_GET['aid']) && 
	is_numeric($_GET['qid']) && 
	is_numeric($_GET['aid'])
){
	$studio404_ajax = new studio404_ajax(); 
	$studio404_ajax->reciver(); 
}

$main_options = array(
	"poll_id"=>"1", /* Poll unique ID */
	"header_text"=>"გამოკითხვა", /* Poll header text */
	"poll_question"=>"მოგწონთ ჩვენი გამოკითხვის მოდული ?", /* Poll Question */
	"poll_answers"=>array(
		"ძალიან მომწონს", 
		"მომწონს", 
		"კარგია",
		"არაუშავს", 
		"არა"
	), /* Poll possible answers, You can have as many as you want */
	"please_wait"=>"გთხოვთ დაიცადოთ ...", /* waiting text */
	"temp_path"=>"_temp/" /* Temp folder path, recommending file permition 0755 */
);

$studio404_poll = new studio404_poll(); 
$studio404_poll->lanch($main_options);
```