<?php

function template_main(){
	global $context;
	echo "Mehraba";

	print_r($_POST);

	json_encode($_POST);

	if(isset($_POST['title']) && !empty($context['user']['id']) && $context['user']['id'] != 0){
		konu_ekle_veri();
	}
}

function konu_ekle_veri(){
	global $smcFunc,$context;
	if(!empty($_POST['title']) && !empty($_POST['message'])){
		$title = $smcFunc['htmlspecialchars']($_POST['title']);
		$boardid = (int)$_POST['boardid'];
		$message = $smcFunc['htmlspecialchars']($_POST['message']);
		$nick = $context['user']['name'];
		$userid = (int)$context['user']['id'];
		konuyolla($message,$userid,$nick,$boardid,$title);
	}

}

function konuyolla($message,$userid,$nick,$boardid,$title)
{
	global $context, $mbname,$smcFunc, $sourcedir,$txt, $modSettings;

	require_once($sourcedir . '/Subs-Post.php');
		$result = $smcFunc['db_query']('',"
		SELECT 
			subject, body 
		FROM {db_prefix}messages 
		 ORDER BY RAND() LIMIT 1");
		if ($smcFunc['db_num_rows']($result) != 0)
		{
			$row2 =  $smcFunc['db_fetch_assoc']($result);
						$msgOptions = array(
									'id' => 0,
									'subject' => $title,
									'body' => $message,
									'icon' => 'xx',
									'smileys_enabled' => 1,
									'attachments' => array(),
								);
								$topicOptions = array(
									'id' => 0,
									'board' => $boardid,
									'poll' => null,
									'lock_mode' => null,
									'sticky_mode' => null,
									'mark_as_read' => false,
								);
								$posterOptions = array(
									'id' => $userid,
									'name' => $nick,
									'email' => '',
									'update_post_count' => (($userid == 0) ? 0 : 1),
								);
			createPost($msgOptions, $topicOptions, $posterOptions);
		}
		$smcFunc['db_free_result']($result);
		redirectexit('topic='.$topicOptions['id'].'');
}