<?php

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

?>
<div id="sonuc"></div>
<hr>
<div class="row">
	<div class="col-6">
		<input type="text" id="title_id_15" name="title_id_15" class="form-control" placeholder="Konu başlık">
		<textarea id="message_id_15" name="message_id_15" class="form-control" cols="10" rows="5"></textarea>
	</div>
	<div class="col-6">
		<button onclick="id_ekle_konu(15)">ID 15 ekle</button>
	</div>
</div>
<hr>


<script>
	function id_ekle_konu(dgr){
			$.ajax({
				type:"post",
				url:smf_scripturl + "?action=posttable",
				data:"title="+$("#title_id_"+dgr).val()+"&message="+$("#message_id_"+dgr).val()+"&boardid="+$("select[name='boardid']").val(),
				success:function(ok){
				},
				error: function(hat){
					console.log(hat);
				}
			});
	}
</script>
<?php
	addInlineJavaScript('		
	', false);
}
?>