<?php
function template_main(){

      echo '<div class="row">';
         ssi_boardNews($board = null, $limit = 4, $start = 0, $length = 400, $output_method = 'echo');
      echo '</div>';

}

?>