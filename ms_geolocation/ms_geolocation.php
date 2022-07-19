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



  $listFieldsColums = array(
//                    'Harware_Id'    => 'HARDWARE_ID',
                    'Hostname'      => 'CLIENT',
                    'Ip'            => 'IP',
                    'Country'       => 'COUNTRY',
                    'Address'       => 'ADDRESS',
                    'City'          => 'CITY',
                    'ZipCode'       => 'ZIPCODE',
//                    'Latitude'      => 'LATITUDE',
//                    'Longitude'     => 'LONGITUDE',
                    'ISP'           => 'ISP',
                    'OSMAP'         => 'OSMAP',
                    'Google Maps'   => 'GOOGLE',
                    'Bing Maps'     => 'BING',
                    'Here'          => 'HERE',
                    'LastUpdate'    => 'CREATED'
  );













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
 $tableDetails = $details->displayBody($list_fields);

 $tableDetails['listFields'] = $listFieldsColums;
 $tableDetails['defaultFields'] = $listFieldsColums;
 $tableDetails['listColCantDel'] = $listFieldsColums;

// $details->debug($tableDetails);
// $details->debug($listFieldsColums);


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



