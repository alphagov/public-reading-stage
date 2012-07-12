<?php
/*
Plugin Name: Public Reading Stage Fragment Importer
Plugin URI: http://www.helpfultechnology.com
Description: Imports document 'fragments' into a site from a specially-formatted CSV file
Author: Steph Gray
Version: 0.1
Author URI: http://www.helpfultechnology.com
*/

if(!function_exists('str_getcsv')) {
    function str_getcsv($input, $delimiter = ",", $enclosure = '"', $escape = "\\") {
        $fp = fopen("php://memory", 'r+');
        fputs($fp, $input);
        rewind($fp);
        $data = fgetcsv($fp, null, $delimiter, $enclosure); // $escape only got added in 5.3.0
        fclose($fp);
        return $data;
    }
}

add_action('admin_menu', 'ht_prsimporter_menu');

function ht_prsimporter_menu() {
  add_submenu_page('tools.php','PRS Fragment Importer', 'PRS Fragment Importer', 'manage_options', 'prs-fragment-importer', 'ht_prsimporter_options');
}

function ht_prsimporter_options() {

  if (!current_user_can('manage_options'))  {
	wp_die( __('You do not have sufficient permissions to access this page.') );
  }

  ob_start();
  
	echo "<div class='wrap'>";
	screen_icon(); 
	echo "<h2>" . __( ' Public Reading Stage Fragment Importer' ) . "</h2>";
	
  if ($_REQUEST['action'] == "processimport") {
    
    $fragarray = explode("\n",$_REQUEST['rawfragments']);
    
    foreach((array)$fragarray as $f) {
    	$fragments[] = str_getcsv(stripslashes($f));
    }
      
	foreach((array)$fragments as $parsedf) {
	
		if (is_numeric($parsedf[4])) {
			$parent = get_page_by_title($parsedf[4], OBJECT, 'fragment' );
			$parentid = ($parent->ID) ? $parent->ID : null;
		} else {
			$parentid = null;
		}
			
		$fragment = array(
			'menu_order' => trim($parsedf[0]),
			'ping_status' => 'closed',
			'post_title' => trim($parsedf[1]),
			'post_content' => trim($parsedf[2]),
			'post_status' => 'publish',
			'post_parent' => trim($parentid),
			'post_type' => 'fragment',
		);
    
   	 	$insertid = wp_insert_post($fragment);
    	add_post_meta($insertid, '_notes', $parsedf[3]);
    	
    	$counter[] = $parsedf[1];
    	sleep(0.5); // small 1/2 sec delay to let things catch up
	}
	    	
	echo "<p>Successfully created " . count($counter) . " fragments:</p><p>" . implode("<br />",$counter) . "</p>";  	
	    
  } elseif ($_REQUEST['action'] == "showinfo") {
	
  } else {
	
	echo "
		<p></p> 
		 <form method='post'>
		 	<p><label for='rawfragments'>Paste CSV fragment file contents here:</label></p>
			<p><textarea class='widefat' rows='20' cols='50' name='rawfragments' id='rawfragments'></textarea></p>
			<p><input type='submit' value='Import fragments' class='button-primary' /></p>
			<input type='hidden' name='page' value='prs-fragment-importer' />
			<input type='hidden' name='action' value='processimport' />
		  </form><br />
		"; 		

  }

	echo "</div>";  

 	ob_end_flush();
}

?>
