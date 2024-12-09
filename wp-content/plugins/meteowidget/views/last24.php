<?php
global $wpdb;
$last24 = $wpdb->get_results ( "
        SELECT allertarecord
        FROM  `{$wpdb->prefix}meteofetcherHI`
        ORDER BY `fetchtime` DESC
        LIMIT 24
        ", ARRAY_N );

/*foreach ($last24 as $value) {
        $last24[] = $row;
      }
*/

echo '<div class="col-6">';
for ($i = 23; $i>=12; $i--) {
  ?>
  <div class="col-1 <?php echo($last24[$i][0]); ?>"><?php if ($i == 23) {echo "<span class='segnatempo'>24</span>";} if ($i == 17) {echo "<span class='segnatempo'>18</span>";} ?></div>
  <?php
}
echo '</div>';
echo '<div class="col-6">';
for ($i = 11; $i>=0; $i--) {
  ?>
  <div class="col-1 <?php echo($last24[$i][0]); ?>"><?php if ($i == 11) {echo "<span class='segnatempo'>12</span>";} if ($i == 5) {echo "<span class='segnatempo'>6</span>";} ?></div>
  <?php
}
echo '</div>';
//var_dump($last24);
