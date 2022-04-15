<?php
$so = new Baglan;

if(isset($_REQUEST['start']) && $_REQUEST['start'] != 0){
	$site_link="https://www.free-css.com/free-css-templates?start=".$_REQUEST['start'];
}else{
	$site_link="https://www.free-css.com/free-css-templates";
}

echo '<hr />';
echo '
<div class="row">
	<form action="'.$scripturl.'?action=bot&bot=freecsscom&?start=" class="row g-3">
	<input type="hidden" name="action" value="bot">
	<input type="hidden" name="bot" value="freecsscom">
	  <div class="col-auto">
	    <select class="form-select" name="start">';
	    	for($i=12; $i <= 3317;$i+=12){
	    		echo  '<option value="'.$i.'">'.$i.'</option>';
	    	}
	    
	    echo '
	    </select>
	  </div>
	  <div class="col-auto">
	    <button type="submit" class="btn btn-primary mb-3">Getir</button>
	  </div>
	</form>
</div>';
echo "<hr />";




$sonuc=$so->balan($site_link);
/*
*/
preg_match_all('#<figure><a href="(.*?)" title="(.*?)"><span class="name">(.*?)</span> <img src="(.*?)" alt="(.*?)" title="(.*?)"> <span class="posted">(.*?)</span></a></figure>#', $sonuc, $son);

$link = $son[1];
$title = $son[3];
$img = $son[4];
//print_r($son);

$i=0;
foreach($link as $linke){
	echo '<div class="row">
	<div class="col-6">
		<input type="text" id="title_id_'.$i.'" name="title_id_'.$i.'" class="form-control" placeholder="Konu başlık" value="'.$title[$i].'">';

	echo '<textarea id="message_id_'.$i.'" name="message_id_'.$i.'" class="form-control" cols="10" rows="5">';

	echo '[url=https://www.free-css.com'.$img[$i].']Demo images[/url]&#10;';
	echo '[demo_resim]https://www.free-css.com'.$img[$i].'[/demo_resim]&#10;';

		$soz=$so->balan("https://www.free-css.com".$linke);
		preg_match_all('#<li class="layout" title="Website Layout">(.*?)</li>#', $soz, $sozp);
		preg_match_all('#<ul class="clear">        <li class="dld"><a rel="nofollow" href="(.*?)" download="(.*?)" title="(.*?)">Download</a></li>        <li class="demo"><a onclick="return(.*?)" href="(.*?)">Live Demo</a></li>      </ul>#', $soz, $sozdown);

		$indir_link="https://www.free-css.com".$sozdown[1][0];
		$demo_link="https://www.free-css.com".$sozdown[5][0];
		

	echo '[demo]'.$demo_link.'[/demo]';
	echo '[download]https://www.free-css.com'.$linke.'[/download]&#10;';
	echo $sozp[1][0];;
			echo '
			</textarea>
		</div>
		<div class="col-6">
			<button onclick="id_ekle_konu('.$i.')" class="btn btn-danger">ID '.$i.' ekle</button>
		</div>
	</div>';
echo '<hr />';
	$i++;
}



echo "<hr style='height:50px;background:#000;'>";
//echo $sonuc;

?>