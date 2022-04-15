<?php

class Baglan{
	public $site;

 	function balan($site){
		$ch = curl_init();
		$user_agent = 'Mozilla HotFox 1.0';
		curl_setopt($ch, CURLOPT_URL, $site);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_NOBODY, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		$sonuc = curl_exec($ch);

		//$sonuc = str_replace("\n\r", "", $sonuc);
		$sonuc = str_ireplace(array("\r","\n",'\r','\n'),'', $sonuc);

		curl_close($ch);


		return $sonuc;
	}
}


function template_main(){
	global $smcFunc;

		$boards = boardsAllowedTo('post_new');
		if (empty($boards))
			fatal_lang_error('cannot_post_new');

		$board = empty($_GET['board'])? '' :(int) $_GET['board'];
		
		$request = $smcFunc['db_query']('', '
			SELECT c.name AS catName, c.id_cat, b.id_board, b.name AS boardName, b.child_level
			FROM {db_prefix}boards AS b
				LEFT JOIN {db_prefix}categories AS c ON (c.id_cat = b.id_cat)
			WHERE {query_see_board} ' . (in_array(0, $boards) ? '' : '
				AND b.id_board IN (' . implode(', ', $boards) . ')'). 
				'AND b.redirect = {string:empty}', array(
				'empty' => ''
				)
			);
		$context['post_boards'] = array();
		while ($row = $smcFunc['db_fetch_assoc']($request))
			$context['post_boards'][] = array(
				'id' => $row['id_board'],
				'name' => $row['boardName'],
				'childLevel' => $row['child_level'],
				'selected' => $board == $row['id_board'],
				'cat' => array(
					'id' => $row['id_cat'],
					'name' => $row['catName']
				)
			);
		$smcFunc['db_free_result']($request);

			if(!empty($context['post_boards'])) {
			echo '
			Kategori Seç : 			
			 <select class="form-select" name="boardid">';
					foreach ($context['post_boards'] as $board) {
						echo '
							 <option value="', $board['id'], '"',  $board['selected'] ? ' selected="selected"' : '', '>', $board['name'], '</option>';
								}
			echo '</select>';
			} 


$bot_array_page = array('tooplate');

echo '
<div class="row">
	<form action="'.$scripturl.'?action=bot" class="row g-3">
	  <input type="hidden" name="action" value="bot">
	  <div class="col-auto">
	    <select class="form-select" name="bot">';
	    	foreach($bot_array_page as $page){
	    		echo  '<option value="'.$page.'">'.$page.'</option>';
	    	}
	    
	    echo '
	    </select>
	  </div>
	  <div class="col-auto">
	    <button type="submit" class="btn btn-primary mb-3">Getir</button>
	  </div>
	</form>
</div>';


if(isset($_GET['bot'])){
	if(file_exists("bot_page/".$_GET['bot'].".php")){
		require_once "bot_page/".$_GET['bot'].".php";
	}
}



echo '
<script>
	function id_ekle_konu(dgr){
			$.ajax({
				type:"post",
				url:smf_scripturl + "?action=posttable",
				data:"title="+$("#title_id_"+dgr).val()+"&message="+$("#message_id_"+dgr).val()+"&boardid="+$("select[name=\'boardid\']").val(),
				success:function(ok){
				},
				error: function(hat){
					console.log(hat);
				}
			});
	}
</script>';

	addInlineJavaScript('		
	', false);



}




?>