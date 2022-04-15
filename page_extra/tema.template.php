<?php
function template_main(){

      echo '<div class="row">';
         ssi_boardNews($board = 3, $limit = 4, $start = 0, $length = 400, $output_method = 'echo');
      echo '</div>';

}



function ssi_boardNews($board = null, $limit = null, $start = null, $length = null, $output_method = 'echo')
{
  global $scripturl, $txt, $settings, $modSettings, $context;
  global $smcFunc;

  loadLanguage('Stats');

  // Must be integers....
  if ($limit === null)
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
  else
    $limit = (int) $limit;

  if ($start === null)
    $start = isset($_GET['start']) ? (int) $_GET['start'] : 0;
  else
    $start = (int) $start;

  if ($board !== null)
    $board = (int) $board;
  elseif (isset($_GET['board']))
    $board = (int) $_GET['board'];

  if ($length === null)
    $length = isset($_GET['length']) ? (int) $_GET['length'] : 0;
  else
    $length = (int) $length;

  $limit = max(0, $limit);
  $start = max(0, $start);
  $request = $smcFunc['db_query']('', '
		SELECT id_board
		FROM {db_prefix}boards
		WHERE ' . ($board === null ? '' : 'id_board = {int:current_board}
			AND ') . 'FIND_IN_SET(-1, member_groups) != 0
		LIMIT 1',
		array(
			'current_board' => $board,
		)
	);
	if ($smcFunc['db_num_rows']($request) == 0)
	{
		if ($output_method == 'echo')
			die($txt['ssi_no_guests']);
		else
			return array();
	}
	list ($board) = $smcFunc['db_fetch_row']($request);
	$smcFunc['db_free_result']($request);
  $icon_sources = array();
  foreach ($context['stable_icons'] as $icon)
    $icon_sources[$icon] = 'images_url';

  if (!empty($modSettings['enable_likes']))
  {
    $context['can_like'] = allowedTo('likes_like');
  }



    //sayfalama için başlangıç

  $request = $smcFunc['db_query']('substring', '
      SELECT
        COUNT(*) as total
      FROM {db_prefix}topics AS t
        LEFT JOIN {db_prefix}messages AS m ON (m.id_msg = t.id_first_msg)
        WHERE t.id_board = {int:current_board}',
        array(
          'current_board' => $board,
        )
    );

  if($smcFunc['db_num_rows']($request)>0)
  $total_bolum = $smcFunc['db_fetch_assoc']($request);
  else
    return;
    $smcFunc['db_free_result']($request);

  if(empty($total_bolum['total']))
    return;

  if(empty($start))
    $start = 0;

  if(!empty($limit))
  {
    $context['page_index'] = constructPageIndex($scripturl . '?action=tema=%1$d', $_REQUEST['start'] , $total_bolum['total'], $limit, true);
    $start = $_REQUEST['start'];
  // Set a canonical URL for this page.
    $context['links'] = array(
      'first' => $_REQUEST['start'] >= $total_bolum['total'] ? $scripturl . '?action=tema=' . $start. '.0' : '',
      'prev' => $_REQUEST['start'] >= $total_bolum['total'] ? $scripturl . '?action=tema=' . $start. '.' . ($_REQUEST['start'] - $total_bolum['total']) : '',
      'next' => $_REQUEST['start'] + $total_bolum['total'] < $total_bolum['total'] ? $scripturl . '?action=tema=' . $start . '.' . ($_REQUEST['start'] + $total_bolum['total']) : '',
      'last' => $_REQUEST['start'] + $total_bolum['total'] < $total_bolum['total'] ? $scripturl . '?action=tema=' . $start . '.' . (floor(($board_info['total_topics'] - 1) / $total_bolum['total']) * $total_bolum['total']) : '',
    );
  }


    //sayfalama için bitiş

 	// Find the post ids.
   $request = $smcFunc['db_query']('', '
   SELECT t.id_first_msg
   FROM {db_prefix}topics as t
     LEFT JOIN {db_prefix}boards as b ON (b.id_board = t.id_board)
   WHERE t.id_board = {int:current_board}' . ($modSettings['postmod_active'] ? '
     AND t.approved = {int:is_approved}' : '') . '
     AND {query_see_board}
   ORDER BY t.id_first_msg DESC
   LIMIT ' . $start . ', ' . $limit,
   array(
     'current_board' => $board,
     'is_approved' => 1,
   )
 );
 $posts = array();
 while ($row = $smcFunc['db_fetch_assoc']($request))
   $posts[] = $row['id_first_msg'];
 $smcFunc['db_free_result']($request);

 if (empty($posts))
   return array();

 // Find the posts.
 $request = $smcFunc['db_query']('', '
   SELECT
     m.icon, m.subject, m.body, COALESCE(mem.real_name, m.poster_name) AS poster_name, m.poster_time, m.likes,
     t.num_replies, t.id_topic, m.id_member, m.smileys_enabled, m.id_msg, t.locked, t.id_last_msg, m.id_board
   FROM {db_prefix}topics AS t
     INNER JOIN {db_prefix}messages AS m ON (m.id_msg = t.id_first_msg)
     LEFT JOIN {db_prefix}members AS mem ON (mem.id_member = m.id_member)
   WHERE t.id_first_msg IN ({array_int:post_list})
   ORDER BY t.id_first_msg DESC
   LIMIT ' . count($posts),
   array(
     'post_list' => $posts,
   )
 );
  $return = array();
  $recycle_board = !empty($modSettings['recycle_enable']) && !empty($modSettings['recycle_board']) ? (int) $modSettings['recycle_board'] : 0;
  while ($row = $smcFunc['db_fetch_assoc']($request))
  {
    
   //tüm body yerine tag çekimi 
    
    $demo_resim = preg_match_all('~\[demo_resim.*?\]([^\]]+)\[\/demo_resim\]~i', $row['body'],  $images) ? '<img src="' . $images[1][0] . '" alt="' .  $row['subject'] . '" height="150" width="200" />' : '';
    $demo = preg_match_all('~\[demo.*?\]([^\]]+)\[\/demo\]~i', $row['body'],  $link) ? '<a class="btn btn-outline-danger" href="' . $link[1][0] . '" title="' .  $row['subject'] . '">Demo</a>' : '';
    $download = preg_match_all('~\[download.*?\]([^\]]+)\[\/download\]~i', $row['body'],  $link2) ? '<a class="btn btn-outline-success" href="' . $link2[1][0] . '" title="' .  $row['subject'] . '">download</a>' : '';

   //tüm body yerine tag çekimi 




    // If we want to limit the length of the post.
    if (!empty($length) && $smcFunc['strlen']($row['body']) > $length)
    {
      $row['body'] = $smcFunc['substr']($row['body'], 0, $length);
      $cutoff = false;

      $last_space = strrpos($row['body'], ' ');
      $last_open = strrpos($row['body'], '<');
      $last_close = strrpos($row['body'], '>');
      if (empty($last_space) || ($last_space == $last_open + 3 && (empty($last_close) || (!empty($last_close) && $last_close < $last_open))) || $last_space < $last_open || $last_open == $length - 6)
        $cutoff = $last_open;
      elseif (empty($last_close) || $last_close < $last_open)
        $cutoff = $last_space;

      if ($cutoff !== false)
        $row['body'] = $smcFunc['substr']($row['body'], 0, $cutoff);
      $row['body'] .= '...';
    }

    $row['body'] = parse_bbc($row['body'], $row['smileys_enabled'], $row['id_msg']);

    if (!empty($recycle_board) && $row['id_board'] == $recycle_board)
      $row['icon'] = 'recycled';

    // Check that this message icon is there...
    if (!empty($modSettings['messageIconChecks_enable']) && !isset($icon_sources[$row['icon']]))
      $icon_sources[$row['icon']] = file_exists($settings['theme_dir'] . '/images/post/' . $row['icon'] . '.png') ? 'images_url' : 'default_images_url';
    elseif (!isset($icon_sources[$row['icon']]))
      $icon_sources[$row['icon']] = 'images_url';
     
    censorText($row['subject']);
    censorText($row['body']);
    $return[] = array(
      'id' => $row['id_topic'],
      'message_id' => $row['id_msg'],
      'icon' => '<img src="' . $settings[$icon_sources[$row['icon']]] . '/post/' . $row['icon'] . '.png" alt="' . $row['icon'] . '">',
      'subject' => $row['subject'],
      'time' => timeformat($row['poster_time']),
      'timestamp' => $row['poster_time'],
      'body' => $row['body'],
      
      
      //tüm body yerine tag çekimi 
      'demo_resim'  => $demo_resim,
      'demo'  => $demo,
      'download'  => $download,
       //tüm body yerine tag çekimi 


      'href' => $scripturl . '?topic=' . $row['id_topic'] . '.0',
      'link' => '<a class="card-link" href="' . $scripturl . '?topic=' . $row['id_topic'] . '.0">' . $row['num_replies'] . ' ' . ($row['num_replies'] == 1 ? $txt['ssi_comment'] : $txt['ssi_comments']) . '</a>',
      'replies' => $row['num_replies'],
      'comment_href' => !empty($row['locked']) ? '' : $scripturl . '?action=post;topic=' . $row['id_topic'] . '.' . $row['num_replies'] . ';last_msg=' . $row['id_last_msg'],
      'comment_link' => !empty($row['locked']) ? '' : '<a class="card-link" href="' . $scripturl . '?action=post;topic=' . $row['id_topic'] . '.' . $row['num_replies'] . ';last_msg=' . $row['id_last_msg'] . '">' . $txt['ssi_write_comment'] . '</a>',
      'new_comment' => !empty($row['locked']) ? '' : '<a href="' . $scripturl . '?action=post;topic=' . $row['id_topic'] . '.' . $row['num_replies'] . '">' . $txt['ssi_write_comment'] . '</a>',
      'poster' => array(
        'id' => $row['id_member'],
        'name' => $row['poster_name'],
        'href' => !empty($row['id_member']) ? $scripturl . '?action=profile;u=' . $row['id_member'] : '',
        'link' => !empty($row['id_member']) ? '<a href="' . $scripturl . '?action=profile;u=' . $row['id_member'] . '">' . $row['poster_name'] . '</a>' : $row['poster_name']
      ),
      'locked' => !empty($row['locked']),
      'is_last' => false,
      // Nasty ternary for likes not messing around the "is_last" check.
      'likes' => !empty($modSettings['enable_likes']) ? array(
        'count' => $row['likes'],
        'you' => in_array($row['id_msg'], prepareLikesContext((int) $row['id_topic'])),
        'can_like' => !$context['user']['is_guest'] && $row['id_member'] != $context['user']['id'] && !empty($context['can_like']),
      ) : array(),
    );
  }
  $smcFunc['db_free_result']($request);

  if (empty($return))
    return $return;

  $return[count($return) - 1]['is_last'] = true;

  // If mods want to do somthing with this list of posts, let them do that now.
  call_integration_hook('integrate_ssi_boardNews', array(&$return));

  if ($output_method != 'echo')
    return $return;

  foreach ($return as $news)
  {
    echo '
    <div class="col-6 pb-2">
    <div class="card">
      <div class="card-body">
        <h3 class="card-title">
          ', $news['icon'], '
          <a href="', $news['href'], '">', $news['subject'], '</a>
        </h3>
        <div class="card-subtitle mb-2 text-muted">', $news['time'], ' ', $txt['by'], ' ', $news['poster']['link'], '</div>
        <div class="card-text" style="padding: 2ex 0;">';
       
       /* 
        ', $news['demo_resim'], '<br>
        ', $news['demo'], '<br>
        ', $news['download'], '<br>
        */
        echo '
        ', $news['body'], '
        </div>
        ', $news['link'], $news['locked'] ? '' : ' | ' . $news['comment_link'], '';

    // Is there any likes to show?
    if (!empty($modSettings['enable_likes']))
    {
      echo '
          <ul>';

      if (!empty($news['likes']['can_like']))
      {
        echo '
            <li class="smflikebutton" id="msg_', $news['message_id'], '_likes"><a href="', $scripturl, '?action=likes;ltype=msg;sa=like;like=', $news['message_id'], ';', $context['session_var'], '=', $context['session_id'], '" class="msg_like"><span class="', $news['likes']['you'] ? 'unlike' : 'like', '"></span>', $news['likes']['you'] ? $txt['unlike'] : $txt['like'], '</a></li>';
      }

      if (!empty($news['likes']['count']))
      {
        $context['some_likes'] = true;
        $count = $news['likes']['count'];
        $base = 'likes_';
        if ($news['likes']['you'])
        {
          $base = 'you_' . $base;
          $count--;
        }
        $base .= (isset($txt[$base . $count])) ? $count : 'n';

        echo '
            <li class="like_count smalltext">', sprintf($txt[$base], $scripturl . '?action=likes;sa=view;ltype=msg;like=' . $news['message_id'] . ';' . $context['session_var'] . '=' . $context['session_id'], comma_format($count)), '</li>';
      }

      echo '
          </ul>';
    }

    // Close the main div.
    echo '
      </div>
      </div>
    </div>';

    if (!$news['is_last']){
      //echo '<hr>';
    }
  }
  //sayfalama html
  echo '
  <div class="pagesection">
    <div class="pagelinks floatleft">
      ', $context['page_index'], '
    </div>
  </div>';  
  //sayfalama html
}

?>