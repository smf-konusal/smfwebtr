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
			 <select name="boardid">';
					foreach ($context['post_boards'] as $board) {
						echo '
							 <option value="', $board['id'], '"',  $board['selected'] ? ' selected="selected"' : '', '>', $board['name'], '</option>';
								}
			echo '</select>';
			} 


if(isset($_GET['bot'])){

}


$so = new Baglan;

if(isset($_REQUEST['start']) && $_REQUEST['start'] != 0 && $_REQUEST['start'] != 1){
	$site_link="https://www.tooplate.com/free-templates/".$_REQUEST['start'];
}else{
	$site_link="https://www.tooplate.com/free-templates";
}

echo ' <ul class="pagination">';

for($i=1; $i <= 8;$i++){
	echo "<li class='page-item'><a class='page-link' href='{$scripturl}?action=bot&bot=tooplate&start=$i'>{$i}</a></li>";

}
echo '</ul>';

echo '<br /><hr />';


$sonuc=$so->balan($site_link);
/*
*/
preg_match_all('#<div class="col-xs-6 col-md-3 col-sm-4 tooplate_box">                <a href="(.*?)"><img src="(.*?)" alt="(.*?)" title="(.*?)" class="img-responsive" width="260" height="260" /></a>                <div class="box_title"><h2><a href="(.*?)">(.*?)</a></h2>(.*?)</div>#', $sonuc, $son);
$link =$son[1];
$img =$son[2];
$title =$son[3];


//$sonuc=$so->balan('https://www.tooplate.com/view/2128-tween-agency');


//print_r($son);

echo '
<div id="sonuc"></div>';



$i=0;
foreach($link as $linke){
echo '<hr>';
	echo '<div class="row">
	<div class="col-6">
		<input type="text" id="title_id_'.$i.'" name="title_id_'.$i.'" class="form-control" placeholder="Konu başlık" value="'.$title[$i].'">
			<textarea id="message_id_'.$i.'" name="message_id_'.$i.'" class="form-control" cols="10" rows="5">';

				echo '[demo_resim]https://www.tooplate.com'.$img[$i].'[/demo_resim]&#10;';

					$soz=$so->balan("https://www.tooplate.com/".$linke);

					preg_match_all('#<p>(.*?)<p>#', $soz, $p);
					preg_match_all('#href="/templates/(.*?)"#', $soz, $live);
					preg_match_all('#href="/download/(.*?)"#', $soz, $down);
					

					$indir_link="https://www.tooplate.com/download/".$live[1][0];
					$demo_link="https://www.tooplate.com/templates/".$down[1][0];
					

				echo '[demo]'.$demo_link.'[/demo]';
				echo '[download]'.$indir_link.'[/download]&#10;';
				echo $p[1][0];
			echo '
			</textarea>
		</div>
		<div class="col-6">
			<button onclick="id_ekle_konu('.$i.')">ID '.$i.' ekle</button>
		</div>
	</div>';

echo '<hr>';
	$i++;
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



echo "<hr style='height:50px;background:#000;'>";
//echo $sonuc;
?>