<?php
/*
Twando.com Free PHP Twitter Application
http://www.twando.com/
*/

//Work out pagination
$per_page = TABLE_ROWS_PER_PAGE;
$page = (int)$_REQUEST['page_id'];
if ($page == 0) {$page = 1;}
$start_limit = ($page - 1) * $per_page;

$q2_base  .= "LIMIT " . $start_limit . ", " . $per_page;
$q2 = $db->query($q2_base);
list($total_items) = $db->fetch_row($db->query("SELECT FOUND_ROWS();"));

if ($total_items > 0) {

//Page calculations
$total_pages = 0;
$mod_pages = $total_items % $per_page;
if ($mod_pages != 0) {
  $total_pages++;
}
$total_pages += ($total_items - $mod_pages) / $per_page;

if ($page > $total_pages) {$page = 1;}

$next_page = $page + 1;
$back_page = $page - 1;
if ($back_page < 1) {$back_page = 0;}

//Do back links
$back_string = "<a title=\"Previous page\" href=\"javascript:" . $js_page_func . "('" . $tab_id . "','" . $back_page . "');\">&laquo;</a> ";
if ($back_page == 0) {$back_string = "&laquo;";}

$next_string = " <a title=\"Next page\" href=\"javascript:" . $js_page_func . "('" . $tab_id . "','" . $next_page . "');\">&raquo;</a>";
if ($next_page > $total_pages) {$next_string = "&raquo;";}

//List pages
$page_list_string = "";

for ($ij = $page - 2; $ij <= $page +2; $ij++) {
if ( ($ij > 0) and  ($ij <= $total_pages) ) {
if ($ij == $page) {
$page_list_string .= "<div class=\"box\">" . $ij . "</div>";
}
else {
$page_list_string .= "<div class=\"box\"><a href=\"javascript:" . $js_page_func . "('" . $tab_id . "','" . $ij . "');\">" . $ij . "</a></div>";
}
}
}

if (($page + 2) < $total_pages) {
 $page_list_string .= '<div class="boxw">...</div><div class="box">' . "<a href=\"javascript:" . $js_page_func . "('" . $tab_id . "','" . $total_pages . "');\">" . $total_pages . "</a></div>";
}
if (($page - 2) > 1) {
 $page_list_string = '<div class="box">' . "<a href=\"javascript:" . $js_page_func . "('" . $tab_id . "','1');\">1</a></div>" . '<div class="boxw">...</div>' . $page_list_string;
}

}
?>
