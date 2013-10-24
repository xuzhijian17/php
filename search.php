<?php
#header("Content-type:text/html;charset=big5");
require_once 'simple_html_dom.php';

$keyword = $_GET['keyword'];

$burl = 'http://www.baidu.com';
$gurl = 'http://www.google.com';


?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8"> 
	<title></title>
	<style type="text/css">
		#result{
			margin-left: 5%;
		}
		#google{
			width: 500px;
			border: 1px solid red;
			float: left;
		}
		#baidu{
			width: 500px;
			margin-left: 10px;
			border: 1px solid blue;
			float: left;
		}
	</style>
</head>
<body>
<form method="get" id="searchform" action="./search.php">
	<label for="searched_content">Search:</label>
	<input name="keyword" class="box" id="searched_content" title="在此输入搜索内容。" type="text" value="<?php echo $keyword ?>">
	<input id="btn" value="goobai" class="button" title="搜索！" type="submit">
</form>
<div id="result">
	<div id="google">
		<?php
			//$html = baidu($burl.'/baidu?wd='.$keyword);
			$html = file_get_html('http://www.google.com.hk/search?as_q='.$keyword);
			$tret = $html->find('li[class=g]');

			foreach($tret as $ret)
			{
			    foreach($ret->find('h3') as $a)
			    {
			        echo iconv("Big5","UTF-8//IGNORE",$a)."<br />";
			        foreach ($ret->find('span[class=st]') as $st) {
			        	echo iconv("Big5","UTF-8//IGNORE",$ret->text())."<br />";
			        }
			    }
			    echo "<hr />";
			}
		?>
	</div>
	<div id="baidu">
		<?php
			//$html = baidu($burl.'/baidu?wd='.$keyword);
			$html = file_get_html('http://www.baidu.com/baidu?wd='.$keyword);
			$tret = $html->find('div[id=1],table[id]');

			foreach($tret as $ret)
			{
			    foreach($ret->find('h3') as $a)
			    {
			        echo $a."<br />";
			        echo $ret->text()."<br />";
			    }
			    echo "<hr />";
			}
		?>
	</div>
</div>
</body>
</html>

