<?php
/*
 
 */

 if (AJAX) {
     parse_str($protectedPost['ocs']['0'], $params);
     $protectedPost += $params;
     ob_start();
     $ajax= true;
 } else {
     $ajax = false;	
 }


 require("class/geolocation.class.php");
 require("require/function_machine.php");
 
// print a title for the table
print_item_header("GeoLocation");

if (!isset($protectedPost['SHOW'])) {
    $protectedPost['SHOW'] = 'NOSHOW';
}

/*
 // Process Get
 if(isset($_GET['list'])){
  $activeMenu = $_GET['list'];
 }else{
  $activeMenu = "GeoLocation";
 }
 */
 // Generate left menu
 $details = new Geolocation();



/*
// echo "<div class='col-md-2'>";
// $details->showVcenterLeftMenu($activeMenu);
// echo "</div>";

 */
 $tabOptions = $protectedPost;
/*

 // Generate Right Tab with data
// $tableDetails = $details->processTable($activeMenu);
//
*/ 

 $details->debug($list_fields);

 $tableDetails = $details->displayBody($list_fields);


 $details->debug($tableDetails);


 $tabOptions['table_name'] = $tableDetails['tabOptions']['table_name'];
 $tabOptions['form_name'] = $tableDetails['tabOptions']['form_name'];

 echo "<div class='col-md-12'>";
 echo open_form($tabOptions['table_name'], '', '', 'form-horizontal');
 $details->ajaxtab_entete_fixe_($tableDetails['listFields'], $tableDetails['defaultFields'], $tabOptions,  $tableDetails['listColCantDel']);
 echo close_form();
 echo "</div>";

 
 
 if (AJAX) {
  ob_end_clean();
  tab_req($tableDetails['listFields'], $tableDetails['defaultFields'], $tableDetails['listColCantDel'], $details->finalQuery, $tabOptions);
  ob_start();
 }


?>



