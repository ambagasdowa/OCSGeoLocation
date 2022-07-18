<?php
 /**
  * This class will show a detailed view of what's in the VCenter infrastructure
  */
 class Geolocation {

  private $tableName = 'geolocation';
  private $fieldArray = null;

  private $queryRepo = array(
    "SHOW_COLUMNS" => "SHOW COLUMNS FROM %s",
    "SELECT_FROM_TABLE" => "SELECT %s FROM %s"
  );

  public $finalQuery = null;

  public $viewList = array(
                    'Harware_Id'    => 'HARDWARE_ID',
                    'Hostname'      => 'CLIENT',
                    'Ip'            => 'IP',
                    'Country'       => 'COUNTRY',
//                    'Region'        => 'REGION',
                    'City'          => 'CITY',
                    'ZipCode'       => 'ZIP',
                    'Latitude'      => 'LATITUDE',
                    'Longitude'     => 'LONGITUDE',
//                    'Timezone'      => 'TIMEZONE',
                    'ISP'           => 'ISP',
                    'OSMAP'         => 'OSMAP',
                    'Google Maps'   => 'GOOGLE',
                    'Bing Maps'     => 'BING',
                    'Here'          => 'HERE',
//                    'Timehost'      => 'TIMEHOST',
                    'Created'       => 'CREATED'
  );





  /**
     * Prints out debug information about given variable.
     * NOTE @Ambagasdowa this function is based in debug from cakephp framework
     * @param boolean $var Variable to show debug information for.
     * @param boolean $showHtml If set to true, the method prints the debug data in a screen-friendly way.
     * @param boolean $showFrom If set to true, the method prints from where the function was called.
     */
  public function debug($var = false,$title=null, $showHtml = false, $showFrom = true) {
	
      echo "<p><bold>DEBUGGING:: </bold>".$title.'</p>';

      if ($showFrom) {
    	      $calledFrom = debug_backtrace();
    	      echo '<strong>' . substr(str_replace($_SERVER['DOCUMENT_ROOT'], '', $calledFrom[0]['file']), 1) . '</strong>';
    	      echo ' (line <strong>' . $calledFrom[0]['line'] . '</strong>)';
      }
      echo "\n<pre class=\"debug\">\n";

      $var = print_r($var, true);
      if ($showHtml) {
    	      $var = str_replace('<', '&lt;', str_replace('>', '&gt;', $var));
      }
      echo $var . "\n</pre>\n";
  }





  public function setTableName($tableName){
    $this->tableName = $tableName;
  }

  public function getTableName(){
    return $this->tableName;
  }

  private function getTableFieldList(){
     $result = mysql2_query_secure($this->queryRepo['SHOW_COLUMNS'], $_SESSION['OCS']["readServer"], $this->tableName);

    if($result != false){
      while($row = $result->fetch_assoc()){
        if($row['Field'] != "HARDWARE_ID"){
// note if viewList is defined then cath em 
           if(isset($this->viewList)){
               if(in_array($row['Field'],$this->viewList)){
                    $this->fieldArray[] = $row['Field'];
               }
           } else {
                    $this->fieldArray[] = $row['Field'];
           }

        }
      }

      return true;
	
    }else{
      return false;
    }

  }



  private function generateQueryFromFieldList(){

 
    $fieldList = implode(', ', $this->fieldArray);
     $this->finalQuery =  sprintf($this->queryRepo['SELECT_FROM_TABLE'], $fieldList, $this->tableName);   
//     $this->debug($this->finalQuery,'finalQuery');
  }

  private function generateDatatable(){

    $listFields = array();
    foreach ($this->fieldArray as $field) {
      $listFields[$field] = $field;
    }
    $defaultFields = $listFields;

    $listColCantDel = array('ID' => 'ID');

    $tabOptions['form_name'] = $this->tableName;
    $tabOptions['table_name'] = $this->tableName;

    $tableDetails = array();

    $tableDetails["listFields"] = $listFields;
    $tableDetails["defaultFields"] = $defaultFields;
    $tableDetails["tabOptions"] = $tabOptions;
    $tableDetails["listColCantDel"] = $listColCantDel;

    return $tableDetails;

  }

  public function processTable($tabName){
    if(!in_array($tabName, $this->viewList)){
      return false;
    }

    $this->setTableName($tabName);
    if($this->getTableFieldList()){
      $this->generateQueryFromFieldList();
      return($this->generateDatatable());
    }

  }



//fonction qui permet d'afficher un tableau dynamique de données
/*
 * Columns : Each available column of the table
 * $columns = array {
 * 						'NAME'=>'h.name', ...
 * 						'Column name' => Database value,
 * 						 }
 * Default_fields : Default columns displayed
 * $default_fields= array{
 * 						'NAME'=>'NAME', ...
 * 						'Column name' => 'Column name',
 * 						}
 * Option : All the options for the specific table
 * $option= array{
 * 						'form_name'=> "show_all",....
 * 						'Option' => value,
 *
 * 						}
 * List_col_cant_del : All the columns that will always be displayed
 * $list_col_cant_del= array {
 * 						'NAME'=>'NAME', ...
 * 						'Column name' => 'Column name',
 * 						}
 */
public function ajaxtab_entete_fixe_($columns, $default_fields, $option = array(), $list_col_cant_del) {
    global $protectedPost, $l, $pages_refs;

    //Translated name of the column
    $lbl_column = array("ACTIONS" => $l->g(1381),
        "CHECK" => "<input type='checkbox' name='ALL' id='checkboxALL' Onclick='checkall();'>");
    if (!isset($tab_options['NO_NAME']['NAME'])) {
        $lbl_column["NAME"] = $l->g(23);
    }

    if (!empty($option['LBL'])) {
        $lbl_column = array_merge($lbl_column, $option['LBL']);
    }
    $columns_special = array("CHECK",
        "SUP",
        "NBRE",
        "NULL",
        "MODIF",
        "SELECT",
        "ZIP",
        "OTHER",
        "STAT",
        "ACTIVE",
        "MAC",
		"EDIT_DEPLOY",
		"SHOW_DETAILS",
		"ARCHIVER",
		"RESTORE",
    );
    //If the column selected are different from the default columns
    if (!empty($_COOKIE[$option['table_name'] . "_col"])) {
        $visible_col = json_decode($_COOKIE[$option['table_name'] . "_col"]);
    }

    $input = $columns;

    //Don't allow to hide columns that should not be hidden
    foreach ($list_col_cant_del as $key => $col_cant_del) {
        unset($input[$col_cant_del]);
        unset($input[$key]);
    }
    $list_col_can_del = $input;
    $columns_unique = array_unique($columns);
    if (isset($columns['CHECK'])) {
        $column_temp = $columns['CHECK'];
        unset($columns['CHECK']);
        $columns_temp['CHECK'] = $column_temp;
        $columns = $columns_temp + $columns;
    }
    $actions = array(
        "MODIF",
		"EDIT_DEPLOY",
        "SUP",
        "ZIP",
        "STAT",
		"ACTIVE",
		"SHOW_DETAILS",
		"ARCHIVER",
		"RESTORE",
    );
    $action_visible = false;

    foreach ($actions as $action) {
        if (isset($columns[$action])) {
            $action_visible = true;
            $columns['ACTIONS'] = "h.ID";
            break;
        }
    }
    //Set the ajax requested address
    if (isset($_SERVER['QUERY_STRING'])) {
        if (isset($option['computersectionrequest'])) {
            parse_str($_SERVER['QUERY_STRING'], $addressoption);
            unset($addressoption['all']);
            unset($addressoption['cat']);
            $addressoption['option'] = $option['computersectionrequest'];
            $address = "ajax.php?" . http_build_query($addressoption);
        } else {
            $address = isset($_SERVER['QUERY_STRING']) ? "ajax.php?" . $_SERVER['QUERY_STRING'] : "";
        }
    }
    $opt = false;
    ?>

    <div align=center>
        <div class="<?php echo $option['table_name']; ?>_top_settings" style="display:none;">
        </div>
        <?php

		if (!isset ($protectedPost['COL_SEARCH'])){
			$selected_col='ALL';
		} else {
			$selected_col = $protectedPost['COL_SEARCH'];
		}

        //Display the Column selector
        if (!empty($list_col_can_del)) {
            // Sort columns show / hide select by default
            ksort($list_col_can_del);

            $opt = true;
            ?>

            <div class="row">
                <div class="col col-md-4 col-xs-offset-0 col-md-offset-4">
                    <div class="form-group">
                        <label class="control-label col-sm-4" for="select_col<?php echo $option['table_name']; ?>"><?php echo $l->g(1349); ?> :</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="select_col<?php echo $option['table_name']; ?>" name="select_col<?php echo $option['table_name']; ?>">
                                <option value="default"><?php echo $l->g(6001); ?></option>
                                <?php
                                foreach ($list_col_can_del as $key => $col) {
                                    $name = explode('.', $col);
                                    $name = explode(' as ', end($name));
                                    $value = end($name);
                                    if (!empty($option['REPLACE_COLUMN_KEY'][$key])) {
                                        $value = $option['REPLACE_COLUMN_KEY'][$key];
                                    }
                                    if (array_key_exists($key, $lbl_column)) {
                                        echo "<option value='$value'>$lbl_column[$key]</option>";
                                    } else {
                                        echo "<option value='$value'>$key</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <?php
        }
        ?>


        <div id="<?php echo $option['table_name']; ?>_csv_download"
             style="display: none">
                 <?php
                 //Display of the result count
                 if (!isset($option['no_download_result'])) {
                     echo "<div id='" . $option['table_name'] . "_csv_page'><label id='infopage_" . $option['table_name'] . "'></label> " . $l->g(90) . "<a href='index.php?" . PAG_INDEX . "=" . $pages_refs['ms_csv'] . "&no_header=1&tablename=" . $option['table_name'] . "&base=" . $tab_options['BASE'] . "'><small> (" . $l->g(183) . ")</small></a></div>";
                     echo "<div id='" . $option['table_name'] . "_csv_total'><label id='infototal_" . $option['table_name'] . "'></label> " . $l->g(90) . " <a href='index.php?" . PAG_INDEX . "=" . $pages_refs['ms_csv'] . "&no_header=1&tablename=" . $option['table_name'] . "&nolimit=true&base=" . $tab_options['BASE'] . "'><small>(" . $l->g(183) . ")</small></a></div>";
                 }
                 ?>
        </div>
        <?php
        echo "<a href='#' id='reset" . $option['table_name'] . "' onclick='delete_cookie(\"" . $option['table_name'] . "_col\");window.location.reload();' style='display: none;' >" . $l->g(1380) . "</a>";
        ?>
    </div>


    <script>
	 // Check all the checkboxes
        function checkall()
        {
            var table_id = "table#<?php echo $option['table_name']; ?>";
            $(table_id + " tbody tr td input:checkbox").each(function () {
                value = !$(this).attr('checked');
                document.getElementById($(this).attr('id')).checked = value;
            });
        }


        function decodeHtml(html) {
            var txt = document.createElement("textarea");
            txt.innerHTML = html;
            return txt.value;

        }

        $(document).ready(function () {
            var table_name = "<?php echo $option['table_name']; ?>";
            var table_id = "table#<?php echo $option['table_name']; ?>";
            var form_name = "form#<?php echo $option['form_name']; ?>";
            var csrfid = "input#CSRF_<?php echo $_SESSION['OCS']['CSRFNUMBER']; ?>";

            /*
             Table Skeleton Creation.
             A Full documentation about DataTable constructor can be found at
             https://datatables.net/manual/index
             */
            var dom = '<<"row"lf ' +
                    '<"dataTables_processing" r>><"#' + table_name + '_settings" >' +
                    't<"row" <"col-md-2" i><"col-md-10" p>>>';

            var table = $(table_id).dataTable({
                "processing": true,
                "serverSide": true,
                "dom": dom,
                "ajax": {
                    'url': '<?php echo $address; ?>&no_header=true&no_footer=true',
                    "type": "POST",
                    //Error handling
                    "error": function (xhr, error, thrown) {
                        var statusErrorMap = {
                            '400': "<?php echo $l->g(1352); ?>",
                            '401': "<?php echo $l->g(1353); ?>",
                            '403': "<?php echo $l->g(1354); ?>",
                            '404': "<?php echo $l->g(1355); ?>",
                            '414': "<?php echo $l->g(1356); ?>",
                            '500': "<?php echo $l->g(1357); ?>",
                            '503': "<?php echo $l->g(1358); ?>"
                        };
                        if (statusErrorMap[xhr.status] != undefined) {
                            if (xhr.status == 401) {
                                window.location.reload();
                            }
                        }
                    },
                    //Set the $_POST request to the ajax file. d contains all datatables needed info
                    "data": function (d) {
                        if ($(table_id).width() < $(this).width()) {
                            $(table_id).width('100%');
                            $(".dataTables_scrollHeadInner").width('100%');
                            $(".dataTables_scrollHeadInner>table").width('100%');
                        }
                        //Add CSRF
                        d.CSRF_<?php echo $_SESSION['OCS']['CSRFNUMBER']; ?> = $(csrfid).val();
                        var visible = [];
                        if (document.getElementById('checkboxALL')) {
                            document.getElementById('checkboxALL').checked = false;
                        }
                        $.each(d.columns, function (index, value) {
                            var col = "." + this['data'];
                            if ($(table_id).DataTable().column(col).visible()) {
                                visible.push(index);
                            }
                        });
                        var ocs = [];
                        //Add the actual $_POST to the $_POST of the ajax request
						<?php
						foreach ($protectedPost as $key => $value) {
							if (!is_array($value)) {
								echo "d['" . $key . "'] = '" . $value . "'; \n";
							}
							if($key == "visible_col") {
								$visible_col = $value;
							}
						}
						?>
                        ocs.push($(form_name).serialize());
                        d.visible_col = visible;
                        d.ocs = ocs;
                    },
                    "dataSrc": function (json) {
                        if (json.customized) {
                            $("#reset" + table_name).show();
                        } else {
                            $("#reset" + table_name).hide();
                        }
                        if (json.debug) {
                            $("<p>" + json.debug + "</p><hr>").hide().prependTo('#' + table_name + '_debug div').fadeIn(1000);
                            $(".datatable_request").show();
                        }


//    url = "https://wego.here.com/location?map="+json.data[property].LATITUDE+","+json.data[property].LONGITUD+",15,normal"
//    anchor = '<a href="'+ url +'">HereMap</a>'

//console.log(json.data);

                        for (var property in json.data) {

                         here = "https://wego.here.com/location?map="+json.data[property].LATITUDE+","+json.data[property].LONGITUDE+",15,normal";
                         google = "https://www.google.com/maps/search/?api=1&query="+ json.data[property].LATITUDE +","+ json.data[property].LONGITUDE;
                         osmap = "https://www.openstreetmap.org/?mlat="+json.data[property].LATITUDE+"&mlon="+json.data[property].LONGITUDE;
                         bing = 'https://www.bing.com/maps/?v=2&sp=point.'+json.data[property].LATITUDE+'_'+json.data[property].LONGITUDE+'_UbicacionActual&lvl=12';


                        console.log(here);
                        console.log(google);
                        console.log(osmap);


                            json.data[property].CLIENT = decodeHtml(json.data[property].CLIENT)
                            json.data[property].OSMAP =  '<a href="'+osmap+'" target="_blank" rel="noreferrer">OpenStreetMap</a>';
                            json.data[property].GOOGLE = '<a href="'+google+'" target="_blank" rel="noreferrer">GoogleMaps</a>';
                            json.data[property].BING = '<a href="'+bing+'" target="_blank" rel="noreferrer">BingMaps</a>';
                            json.data[property].HERE = '<a href="'+ here +'" target="_blank" rel="noreferrer">HereMap</a>';
                        }
                        return json.data;
                    },

                },

                //Save datatable state (page length, sort order, ...) in localStorage
                "stateSave": true,
                "stateDuration": 0,
                //Override search filter and page start after loading last datatable state
                "stateLoadParams": function (settings, data) {
                    data.search.search = "";
                    data.start = 0;
                },
				"conditionalPaging": true,
				"lengthMenu": [ 10, 25, 50, 100, 250, 500, 1000],
                //Column definition
                "columns": [
    <?php

		$index = 0;

    // Unset visible columns session var

    unset($_SESSION['OCS']['visible_col'][$option['table_name']]);

	//Visibility handling
    foreach ($columns as $key => $column) {


        if (!empty($visible_col)) {
            if ((in_array($index, $visible_col))) {
                // add visibles columns
                $_SESSION['OCS']['visible_col'][$option['table_name']][$key] = $column;
                $visible = 'true';
            } else {
                $visible = 'false';
            }
            $index ++;
        } else {
            if (( (in_array($key, $default_fields)) || (in_array($key, $list_col_cant_del)) || array_key_exists($key, $default_fields) || ($key == "ACTIONS" )) && !(in_array($key, $actions))) {
                // add visibles columns
                $_SESSION['OCS']['visible_col'][$option['table_name']][$key] = $column;
                $visible = 'true';
            } else {
                $visible = 'false';
            }
        }
        //Can the column be ordered
        if (in_array($key, $columns_special) || !empty($option['NO_TRI'][$key]) || $key == "ACTIONS") {
            $orderable = 'false';
        } else {
            $orderable = 'true';
        }
        //Cannot search in Delete or checkbox columns
        if (!array_key_exists($key, $columns_unique) || in_array($key, $columns_special)) {
            if (!empty($option['REPLACE_COLUMN_KEY'][$key])) {
                $key = $option['REPLACE_COLUMN_KEY'][$key];
            }
            echo "{'data' : '" . $key . "' , 'class':'" . $key . "',
'name':'" . $key . "', 'defaultContent': ' ',
'orderable':  " . $orderable . ",'searchable': false,
'visible' : " . $visible . "}, \n";
        } else {
            $name = explode('.', $column);
            $name = explode(' as ', end($name));
            $name = end($name);
            if (!empty($option['REPLACE_COLUMN_KEY'][$key])) {
                $name = $option['REPLACE_COLUMN_KEY'][$key];
            }
            echo "{ 'data' : '" . $name . "' , 'class':'" . $name . "',
'name':'" . $column . "', 'defaultContent': ' ',
'orderable':  " . $orderable . ", 'visible' : " . $visible . "},\n ";
        }
    }
    ?>
                ],
                //Translation
                "language": {
                    "sEmptyTable": "<?php echo $l->g(1334); ?>",
                    "sInfo": "<?php echo $l->g(1335); ?>",
                    "sInfoEmpty": "<?php echo $l->g(1336); ?>",
                    "sInfoFiltered": "<?php echo $l->g(1337); ?>",
                    "sInfoPostFix": "",
                    "sInfoThousands": "<?php echo $l->g(1350); ?>",
                    "decimal": "<?php echo $l->g(1351); ?>",
                    "sLengthMenu": "<?php echo $l->g(1338); ?>",
                    "sLoadingRecords": "<?php echo $l->g(1339); ?>",
                    "sProcessing": "<?php echo $l->g(1340); ?>",
                    "sSearch": "<?php echo $l->g(1341); ?>",
                    "sZeroRecords": "<?php echo $l->g(1342); ?>",
                    "oPaginate": {
                        "sFirst": "<?php echo $l->g(1343); ?>",
                        "sLast": "<?php echo $l->g(1344); ?>",
                        "sNext": "<?php echo $l->g(1345); ?>",
                        "sPrevious": "<?php echo $l->g(1346); ?>",
                    },
                    "oAria": {
                        "sSortAscending": ": <?php echo $l->g(1347); ?>",
                        "sSortDescending": ": <?php echo $l->g(1348); ?>",
                    }
                },
                "scrollX": 'true'
            });

            //Column Show/Hide
            $("#select_col" + table_name).change(function () {
                var col = "." + $(this).val();
                $(table_id).DataTable().column(col).visible(!($(table_id).DataTable().column(col).visible()));
				$(table_id).DataTable().ajax.reload();
				$("#select_col" + table_name).val('default');
            });

            //$("<span id='" + table_name + "_settings_toggle' class='glyphicon glyphicon-chevron-down table_settings_toggle'></span>").hide().appendTo("#" + table_name + "_filter label");
            $("#" + table_name + "_settings").hide();
            $("." + table_name + "_top_settings").contents().appendTo("#" + table_name + "_settings");
            $("#" + table_name + "_settings").addClass('table_settings');
            $("body").on("click", "#" + table_name + "_settings_toggle", function () {
                $("#" + table_name + "_settings_toggle").toggleClass("glyphicon-chevron-up");
                $("#" + table_name + "_settings_toggle").toggleClass("glyphicon-chevron-down");
                $("#<?php echo $option['table_name']; ?>_settings").fadeToggle();

            });
    <?php if ($opt) { ?>
                $("#" + table_name + "_settings_toggle").show();
        <?php
    }
//Csv Export
    if (!isset($option['no_download_result'])) {
        ?>
                $(table_id).on('draw.dt', function () {
                    var start = $(table_id).DataTable().page.info().start + 1;
                    var end = $(table_id).DataTable().page.info().end;
                    var total = $(table_id).DataTable().page.info().recordsDisplay;
                    //Show one line only if results fit in one page
                    if (total == 0) {
                        $('#' + table_name + '_csv_download').hide();
                        $("#" + table_name + "_settings_toggle").hide();
                    } else {
                        if (end != total || start != 1) {
                            $('#' + table_name + '_csv_page').show();
                            $('#infopage_' + table_name).text(start + "-" + end);
                        } else {
                            $('#' + table_name + '_csv_page').hide();
                        }
                        $('#infototal_' + table_name).text(total);
                        $('#' + table_name + '_csv_download').show();
                        $("#" + table_name + "_settings_toggle").show();
                    }
                });
        <?php
    }
    ?>
        });

    </script>
    <?php
    if ($titre != "") {
        printEnTete_tab($titre);
    }
    echo "<div class='tableContainer'>";
    echo "<table id='" . $option['table_name'] . "' width='100%' class='table table-striped table-condensed table-hover cell-border'><thead><tr>";
    //titre du tableau
    foreach ($columns as $k => $v) {
        if (array_key_exists($k, $lbl_column)) {
            echo "<th><font >" . $lbl_column[$k] . "</font></th>";
        } else {
            echo "<th><font >" . $k . "</font></th>";
        }
    }
    echo "</tr>
    </thead>";

    echo "</table></div>";
    echo "<input type='hidden' id='SUP_PROF' name='SUP_PROF' value=''>";
    echo "<input type='hidden' id='MODIF' name='MODIF' value=''>";
    echo "<input type='hidden' id='SELECT' name='SELECT' value=''>";
    echo "<input type='hidden' id='OTHER' name='OTHER' value=''>";
    echo "<input type='hidden' id='ACTIVE' name='ACTIVE' value=''>";
    echo "<input type='hidden' id='CONFIRM_CHECK' name='CONFIRM_CHECK' value=''>";
    echo "<input type='hidden' id='OTHER_BIS' name='OTHER_BIS' value=''>";
    echo "<input type='hidden' id='OTHER_TER' name='OTHER_TER' value=''>";
	echo "<input type='hidden' id='EDIT_DEPLOY' name='EDIT_DEPLOY' value=''>";
	echo "<input type='hidden' id='SHOW_DETAILS' name='SHOW_DETAILS' value=''>";
	echo "<input type='hidden' id='ARCHIVER' name='ARCHIVER' value=''>";
	echo "<input type='hidden' id='RESTORE' name='RESTORE' value=''>";
	
    if ($_SESSION['OCS']['DEBUG'] == 'ON') {
        ?><center>
            <div id="<?php echo $option['table_name']; ?>_debug" class="alert alert-info" role="alert">
                <b>[DEBUG]TABLE REQUEST[DEBUG]</b>
                <hr>
                <b class="datatable_request" style="display:none;">LAST REQUEST:</b>
                <div></div>
            </div>
        </center><?php
    }
    return true;
}












public function subQuery($systemid) {
    if (!is_numeric($systemid)) {
	$this->debug($systemid,':::systemid:::FALSE');
        return false;
    }

    $this->getTableFieldList();
     $fieldList = implode(', ', $this->fieldArray);
     $subqry =  sprintf($this->queryRepo['SELECT_FROM_TABLE'], $fieldList, $this->tableName);

    $this->debug($subqry); 

    $result = mysqli_query($_SESSION['OCS']["readServer"], $subqry,true) or die(mysqli_error($_SESSION['OCS']["readServer"]));

     $this->debug($result,':::RESULT:::TRUE');



    while ($valSub = mysqli_fetch_object($result)) {

        $returnVal[] = (array) $valSub;
    }

    $this->debug(var_dump($valSub),'::::VALSUB::::');
    $this->debug($returnVal,'returnVal');
    return $returnVal;
}




  public function displayBody() {
    if($this->getTableFieldList()){
         $this->generateQueryFromFieldList();
      return($this->generateDatatable());
    }

  }



  public function showVcenterLeftMenu($activeMenu){
    $menuArray = $this->viewList;

    echo '<ul class="nav nav-pills nav-stacked navbar-left">';
    foreach ($menuArray as $key=>$value){

        echo "<li ";
        if ($activeMenu == $value) {
            echo "class='active'";
        }
        echo " ><a href='?function=ms_geolocation&list=".$value."'>".$key."</a></li>";
    }
    echo '</ul>';
  }



 }
