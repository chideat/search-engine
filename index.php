<?php
	//title is set in configure.php
	include_once 'lib/configure.php';
	ob_start();
?>
<div id="index_title">思考zhe</div>
<div id="index_form">
	<form name="form" id="form" action="public/search.php" method="post">
		<input id="index_query"  name="index_query" maxlength="255"></input>
		<input value="思考?" id="index_go" name="index_go" style="background-img:url('img/1.jpg')" type="submit">
		<input type="hidden" name="mygo" class="mygo" value="1">
	</form>
</div>
<?php 
	$GLOBALS['template']['content'] = ob_get_clean();
	include_once 'templates/index_page.php';
?>