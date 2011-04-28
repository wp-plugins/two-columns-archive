<?php
/*
Plugin Name: Two Columns Archive
Version: 1.0
Plugin URI: http://eax.me/two-columns-archive-plugin/
Description: Description - http://eax.me/two-columns-archive-plugin/
Author: Alexandr Alexeev
Author URI: http://eax.me/
*/

function two_columns_archive_widget($args) {
  global $wpdb, $wp_locale;
  extract($args);

$query = "
  select
    max(post_date) as max_date,
    min(post_date) as min_date
  from ".$wpdb->posts."
  where post_type = 'post'
    and post_status = 'publish'
";
  $res = $wpdb->get_results($query, ARRAY_A);
  $row = $res[0];

  preg_match('/^([0-9]{4})\-([0-9]{2})/', $row['max_date'], $m);
  $year = $max_year = $m[1]; $month = $max_month = $m[2];

  preg_match('/^([0-9]{4})\-([0-9]{2})/', $row['min_date'], $m);
  $min_year = $m[1]; $min_month = $m[2];

  $delta = $max_year*12 + $max_month - $min_year*12 - $min_month + 1;

  echo $before_widget.$before_title;
  echo htmlspecialchars(get_option('two_columns_archive_title'));
  echo $after_title;

  $left = ""; $right = ""; $site = get_option('site_url'); 
  $left_steps = ceil($delta / 2);
  for($i = 1; $i <= $delta; $i++) {
    $month = sprintf("%02d", $month);
    $month_name = $wp_locale->get_month($month);
    $html = "<li><a href=\"".get_month_link($year, $month).
      "\">$month_name $year</a></li>\n";

    if($i <= $left_steps) $left .= $html;
    else $right .= $html;

    if(!--$month) {
      $month = 12; $year--;
    }
  }
?>
<div style="float: left;">
<ul><?php echo $left; ?></ul>
</div>
<div style="float: right; width: 50%;">
<ul><?php echo $right; ?></ul>
</div>
<div class="clear"></div>
<?
  echo $after_widget;
}

function two_columns_archive_control() {
  if (!empty($_REQUEST['two_columns_archive_title']))
    update_option('two_columns_archive_title', $_REQUEST['two_columns_archive_title']);
?>
  Title: 
  <input type="text" name="two_columns_archive_title" value="<?php
  echo htmlspecialchars(get_option('two_columns_archive_title'))."\" />";
}

function register_two_columns_archive() {
  register_sidebar_widget('Two Columns Archive', 'two_columns_archive_widget');
  register_widget_control('Two Columns Archive', 'two_columns_archive_control' );
}

add_action('init', 'register_two_columns_archive');

?>
