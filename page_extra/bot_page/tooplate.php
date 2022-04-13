<?php

$so = new Baglan;

if(isset($_REQUEST['start']) && $_REQUEST['start'] != 0 && $_REQUEST['start'] != 1){
	$site_link="https://www.tooplate.com/free-templates/".$_REQUEST['start'];
}else{
	$site_link="https://www.tooplate.com/free-templates";
}

echo '<hr />';
echo ' <ul class="pagination">';

for($i=1; $i <= 8;$i++){
	echo "<li class='page-item'><a class='page-link' href='{$scripturl}?action=bot&bot=tooplate&start=$i'>{$i}</a></li>";

}
echo '</ul>';
echo "<hr />";


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
			<button onclick="id_ekle_konu('.$i.')" class="btn btn-danger">ID '.$i.' ekle</button>
		</div>
	</div>';

echo '<hr>';
	$i++;
}

echo "<hr style='height:50px;background:#000;'>";
//echo $sonuc;


?>