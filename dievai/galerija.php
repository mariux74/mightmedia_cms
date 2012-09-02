<?php

/**
 * @Projektas: MightMedia TVS
 * @Puslapis: www.coders.lt
 * @$Author: p.dambrauskas $
 * @copyright CodeRS ©2008
 * @license GNU General Public License v2
 * @$Revision: 366 $
 * @$Date: 2009-12-03 20:46:01 +0200 (Thu, 03 Dec 2009) $
 **/

if (!defined("OK") || !ar_admin(basename(__file__))) {
	redirect('location: http://' . $_SERVER["HTTP_HOST"]);
}
//ini_set("memory_limit", "50M");
if (isset($url['p']) && isnum($url['p']) && $url['p'] > 0) {
	$p = escape(ceil((int)$url['p']));
} else {
	$p = 0;
}
if(count($_GET) < 3) $_GET['v'] = 1;
$buttons="
<div id=\"admin_menu\" class=\"btns\">
	<a class=\"btn\" href=\"".url("?id,{$_GET['id']};a,{$_GET['a']};v,6")."\"><span><img src=\"".ROOT."images/icons/photo_album__arrow.png\" alt=\"\" class=\"middle\"/>{$lang['admin']['gallery_conf']}</span></a>
	<a class=\"btn\" href=\"".url("?id,{$_GET['id']};a,{$_GET['a']};v,7")."\"><span><img src=\"".ROOT."images/icons/picture__exclamation.png\" alt=\"\" class=\"middle\"/>{$lang['admin']['gallery_unpublished']}</span></a>
	<a class=\"btn\" href=\"".url("?id,{$_GET['id']};a,{$_GET['a']};v,1")."\"><span><img src=\"".ROOT."images/icons/picture__plus.png\" alt=\"\" class=\"middle\"/>{$lang['admin']['gallery_add']}</span></a>
		".($_SESSION['level'] == 1 ? "<a class=\"btn\" href=\"".url("?id,{$_GET['id']};a,{$_GET['a']};v,9")."\"><span><img src=\"".ROOT."images/icons/pictures__plus.png\" alt=\"\" class=\"middle\"/>{$lang['admin']['gallery_group_add']}</span></a>":"")."
	<a class=\"btn\" href=\"".url("?id,{$_GET['id']};a,{$_GET['a']};v,8")."\"><span><img src=\"".ROOT."images/icons/picture__pencil.png\" alt=\"\" class=\"middle\"/>{$lang['admin']['gallery_edit']}</span></a>
	<a class=\"btn\" href=\"".url("?id,{$_GET['id']};a,{$_GET['a']};v,2")."\"><span><img src=\"".ROOT."images/icons/folder__plus.png\" alt=\"\" class=\"middle\"/>{$lang['system']['createcategory']}</span></a>
	<a class=\"btn\" href=\"".url("?id,{$_GET['id']};a,{$_GET['a']};v,3")."\"><span><img src=\"".ROOT."images/icons/folder__pencil.png\" alt=\"\" class=\"middle\"/>{$lang['system']['editcategory']}</span></a>
	
</div>";

if (empty($url['s'])) {
	$url['s'] = 0;
}
if (empty($url['v'])) {
	$url['v'] = 0;
}

lentele($lang['admin']['galerija'], $buttons);

unset($buttons, $extra, $text);
include_once (ROOT."priedai/kategorijos.php");
kategorija("galerija", true);
//kategorijos
$kategorijos = cat('galerija', 0);
$kategorijos[0] = "--";
//foto aktyvavimas
if (isset($_GET['p'])) {
	$result = mysql_query1("UPDATE `" . LENTELES_PRIESAGA . "galerija` SET rodoma='TAIP' 
			WHERE `id`=" . escape($_GET['p']) . ";
			");
	if ($result) {
		msg($lang['system']['done'], "{$lang['admin']['gallery_activated']}.");
	} else {
		klaida("{$lang['system']['error']}", " <br><b>" . mysql_error() . "</b>");
	}
}
//foto salinimas
if (((isset($_POST['action']) && $_POST['action'] == $lang['admin']['delete']  && isset($_POST['edit_new']) && $_POST['edit_new'] > 0)) || isset($url['t'])) {
	if (isset($url['t'])) {
		$trinti = (int)$url['t'];
	} elseif (isset($_POST['edit_new'])) {
		$trinti = (int)$_POST['edit_new'];
	}
	$sql = mysql_query1("SELECT `file` FROM `" . LENTELES_PRIESAGA . "galerija` WHERE `ID` = " . escape($trinti) . " LIMIT 1");

	if (isset($row['file']) && !empty($row['file'])) {
		@unlink(ROOT."images/galerija/" . $row['file']);
		@unlink(ROOT."images/galerija/mini/" . $row['file']);
		@unlink(ROOT."images/galerija/originalai/" . $row['file']);
	}
	mysql_query1("DELETE FROM `" . LENTELES_PRIESAGA . "galerija` WHERE id=" . escape($trinti) . " LIMIT 1");


	if (mysql_affected_rows() > 0) {
		msg($lang['system']['done'], $lang['admin']['gallery_deleted']);
	} else {
		klaida("{$lang['system']['error']}", " <br><b>" . mysql_error() . "</b>");
	}

	mysql_query1("DELETE FROM `" . LENTELES_PRIESAGA . "kom` WHERE pid='puslapiai/galerija' AND kid=" . escape($trinti) . "");
	//redirect("?id,".$_GET['id'].";a,".$_GET['a'],"header");
}

//foto redagavimas
elseif (((isset($_POST['edit_new']) && isNum($_POST['edit_new']) && $_POST['edit_new'] > 0)) || isset($url['h'])) {
	if (isset($url['h'])) {
		$redaguoti = (int)$url['h'];
	} elseif (isset($_POST['edit_new'])) {
		$redaguoti = (int)$_POST['edit_new'];
	}

	$extra = mysql_query1("SELECT * FROM `" . LENTELES_PRIESAGA . "galerija` WHERE `id`=" . escape($redaguoti) . " LIMIT 1");

} elseif (isset($_POST['action']) && $_POST['action'] == $lang['admin']['edit']) {
	
	$apie = strip_tags($_POST['Aprasymas']);
	$pavadinimas = strip_tags($_POST['Pavadinimas']);
	$kategorija = (int)$_POST['cat'];
	$id = ceil((int)$_POST['news_id']);
	$komentaras = (isset($_POST['kom']) && $_POST['kom'] == 'TAIP' ? 'TAIP' : 'NE');


	$result = mysql_query1("UPDATE `" . LENTELES_PRIESAGA . "galerija` SET
			`pavadinimas` = " . escape($pavadinimas) . ",
			`categorija` = " . escape($kategorija) . ",
			`apie` = " . escape($apie) . "
			WHERE `id`=" . escape($id) . ";
			");
	if ($result) {
		msg($lang['system']['done'], $lang['admin']['gallery_updated']);
		redirect($_SERVER['HTTP_REFERER']);
   	} else {
		klaida($lang['system']['error'], " <br><b>" . mysql_error() . "</b>");
	}

} elseif (isset($_POST['action']) && $_POST['action'] == $lang['admin']['gallery_add']) {

	if (isset($_FILES['failas']['name'])) {

		$big_img = ROOT."images/galerija/";			//Kur bus saugomi didesni paveiksliukai
		$mini_img = ROOT."images/galerija/mini";	//Kur bus saugomos miniatiuros
		
		$img_thumb_width = $conf['minidyd']; //Mini paveiksliukų dydis

		//Sarašas leidžiamų failų
        $limitedext = array(".jpg", ".JPG", ".jpeg", ".JPEG", ".png", ".PNG", ".gif", ".GIF", ".bmp", ".BMP");
		
		$file_type = $_FILES['failas']['type'];
		$file_name = $_FILES['failas']['name'];
		$file_size = $_FILES['failas']['size'];
		$file_tmp = $_FILES['failas']['tmp_name'];
		
		//Patikrinam ar failas įkeltas sėkmingai
		if (!is_uploaded_file($file_tmp)) {
			klaida($lang['system']['warning'], $lang['admin']['gallery_nofile']);
		} else {
			//gaunamm failo galunę
			$ext = strrchr($file_name, '.');
			$ext = strtolower($ext);

			//Tikrinam ar tinkamas failas
			if (!in_array($ext, $limitedext)) {
				klaida($lang['system']['warning'], $lang['admin']['gallery_notimg']);
			}

			//create a random file name
			$rand_pre = random();
			$rand_name = $rand_pre . time();

			//the new width variable
			$ThumbWidth = $img_thumb_width;
			if ($file_size) {
				if ($file_type == "image/pjpeg" || $file_type == "image/jpeg") {
					$img = imagecreatefromjpeg($file_tmp);
				} elseif ($file_type == "image/x-png" || $file_type == "image/png") {
					$img = imagecreatefrompng($file_tmp);
				} elseif ($file_type == "image/gif") {
					$img = imagecreatefromgif($file_tmp);
				} elseif ($file_type == "image/bmp") {
					$img = imagecreatefrombmp($file_tmp);
				}
				//list the width and height and keep the height ratio.
				$width = imageSX($img);
				$height = imageSY($img);
				
				// Build the thumbnail
				$target_width = $conf['minidyd'];
				$target_height = $conf['minidyd'];
				$target_ratio = $target_width / $target_height;

				$img_ratio = $width / $height;

				//calculate the image ratio
				$imgratio = $width / $height;
				
				if ($target_ratio > $img_ratio) {
					$new_height = $target_height;
					$new_width = $img_ratio * $target_height;
				} else {
					$new_height = $target_width / $img_ratio;
					$new_width = $target_width;
				}

				if ($new_height > $target_height) {
					$new_height = $target_height;
				}
				if ($new_width > $target_width) {
					$new_height = $target_width;
				}
				
				
				$new_img = ImageCreateTrueColor($conf['minidyd'], $conf['minidyd']);
				if (!@imagefilledrectangle($new_img, 0, 0, $target_width-1, $target_height-1, 0)) {	// Fill the image black
					klaida($lang['system']['error'], 'GD v2+' . $lang['system']['error']);
					exit(0);
				}

				if (!@imagecopyresampled($new_img, $img, ($target_width-$new_width)/2, ($target_height-$new_height)/2, 0, 0, $new_width, $new_height, $width, $height)) {
					klaida($lang['system']['error'], 'GD v2+' . $lang['system']['error']);
					exit(0);
				}

			 imagejpeg($new_img, $mini_img."/".$rand_name.$ext, 95);

				chmod($mini_img."/".$rand_name.$ext,0777);
				ImageDestroy($img);
				ImageDestroy($new_img);

			}

			if ($file_size) {
				if ($file_type == "image/pjpeg" || $file_type == "image/jpeg") {
					$new_img = imagecreatefromjpeg($file_tmp);
				} elseif ($file_type == "image/x-png" || $file_type == "image/png") {
					$new_img = imagecreatefrompng($file_tmp);
				} elseif ($file_type == "image/gif") {
					$new_img = imagecreatefromgif($file_tmp);
				} elseif ($file_type == "image/bmp") {
					$new_img = imagecreatefrombmp($file_tmp);
				}
				$bigsize = $conf['fotodyd'];
				list($width, $height) = getimagesize($file_tmp);
				//calculate the image ratio
				$imgratio = $width / $height;
				if ($width > $bigsize) {
					if ($imgratio > 1) {
						$newwidth = $bigsize;
						$newheight = $bigsize / $imgratio;
					} else {
						$newheight = $bigsize;
						$newwidth = $bigsize * $imgratio;
					}
				} else {
					$newwidth = $width;
					$newheight = $height;
				}
				$resized_imgbig = imagecreatetruecolor($newwidth, $newheight);
				imagecopyresampled($resized_imgbig, $new_img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

				//finally, save the image

				ImageJpeg($resized_imgbig, $big_img."/".$rand_name.$ext, 95);
				chmod($big_img."/".$rand_name.$ext,0777);
				ImageDestroy($resized_imgbig);
				ImageDestroy($new_img);

				move_uploaded_file($file_tmp, $big_img."/originalai/".$rand_name.$ext);
            chmod($big_img."/originalai/".$rand_name.$ext,0777);

				$result = mysql_query1("INSERT INTO `" . LENTELES_PRIESAGA . "galerija` (`pavadinimas`,`file`,`apie`,`autorius`,`data`,`categorija`,`rodoma`, `lang`) VALUES (" . escape($_POST['Pavadinimas']) . "," . escape($rand_name . $ext) . "," . escape(strip_tags($_POST['Aprasymas'])) . "," . escape($_SESSION['id']) . ",'" . time() . "'," . escape($_POST['cat']) . ",'TAIP', ".escape(lang()).")");

				if ($result) {
					msg($lang['system']['done'], "{$lang['admin']['gallery_added']}");
				} else {
					klaida("{$lang['system']['error']}", " <br><b>" . mysql_error() . "</b>");
				}
				unset($_FILES['failas'], $filename, $_POST['action']);
				redirect(url("?id," . $_GET['id'] . ";a," . $_GET['a'] . ";v,1"), "meta");

			}
		}
	}

}

//foto kategoriju saraso rodymas
if (isset($_GET['v'])) {
	include_once (ROOT."priedai/class.php");
	$bla = new forma();
	if ($_GET['v'] == 8) {
      $text = "<fieldset><legend>{$lang['system']['categories']}:</legend><ul>";
      foreach($kategorijos as $id => $kategorija){
         $text .= "<li class=\"drag_block\"><a href=\"".url('?id,'.$_GET['id'].';a,'.$_GET['a'].';v,8;k,'.$id)."\">".str_replace('-', '&nbsp;&nbsp;', $kategorija)."</a></li>";
      }
      $text .= "</ul></fieldset>";
		$limit = 10;		
		$sql2 = mysql_query1("SELECT * FROM  `" . LENTELES_PRIESAGA . "galerija` WHERE `lang` = ".escape(lang())." AND `categorija`=".escape((isset($_GET['k'])? $_GET['k'] : 0))." ORDER BY `".$conf['galorder']."` ".$conf['galorder_type']);
//foto pagal kategorijas rodymas
		if (sizeof($sql2) > 0) {

			$text .= "<table width=\"80%\" border=\"0\">
	<tr>
		<td >
";

			foreach ($sql2 as $row2) {
				if (isset($row['Nick'])) {
					$autorius = $row2['Nick'];
				} else {
					$autorius = $lang['system']['guest'];
				}
//Jog veiktų normaliai tenka čia talpinti :)
				$text.='
	<!-- Add jQuery library -->
	<script type="text/javascript" src="../javascript/jquery/fancybox/jquery-1.7.2.min.js"></script>
	<!-- Add mousewheel plugin (this is optional) -->
	<script type="text/javascript" src="../javascript/jquery/fancybox/jquery.mousewheel-3.0.6.pack.js"></script>
	<!-- Add fancyBox main JS and CSS files -->
	<script type="text/javascript" src="../javascript/jquery/fancybox/jquery.fancybox.js?v=2.0.6"></script>
	<link rel="stylesheet" type="text/css" href="../stiliai/jquery.fancybox.css?v=2.0.6" media="screen" />
	<script type="text/javascript">
		$(document).ready(function() {
			$(".fancybox").fancybox();
			// Remove padding, set opening and closing animations, close if clicked and disable overlay
			$(".fancybox-effects-d").fancybox({
				padding: 0,
				openEffect : "elastic",
				openSpeed  : 150,
				closeEffect : "elastic",
				closeSpeed  : 150,
				closeClick : true,
				helpers : {
					overlay : {
						css : {
							"background" : "#fff"
						}
					}
				}
			});
		});
	</script>
        <script type="text/javascript" src="js/superfish.js"></script>
		<script src="js/jquery.treeview.js" type="text/javascript"></script>
	';
				$text .= "
			<div class=\"gallery img_left\" >
				<a class=\"fancybox-effects-d\" href=\"".ROOT."images/galerija/" . $row2['file'] . "\" title=\"" . (!empty($row2['pavadinimas'])?$row2['pavadinimas'] . "<br>":'') . trimlink(strip_tags($row2['apie']), 50) . "\">
					<img src=\"".ROOT."images/galerija/mini/" . $row2['file'] . "\" alt=\"\" />
				</a>
				<div class='gallery_menu'>
					<a href=\"#\" title=\"{$lang['admin']['gallery_date']}: " . date('Y-m-d H:i:s ', $row2['data']) . "\"><img src='".ROOT."images/icons/information.png' border='0' alt='info' /></a>
					<a href=\"".url("?id," . $url['id'] . ";a," . $url['a'] . ";t," . $row2['ID'] ). "\" onclick=\"if (confirm('{$lang['system']['delete_confirm']}')) { $.get('".url("?id," . $url['id'] . ";a," . $url['a'] . ";t," . $row2['ID']). "'); $(this).parent().parent().remove(); return false } else { return false }\" title=\"{$lang['admin']['delete']}\"><img src='".ROOT."images/icons/cross.png'  border='0'></a>
				    <a href=\"".url("?id," . $url['id'] . ";a," . $url['a'] . ";h," . $row2['ID'] ). "\" title=\"{$lang['admin']['edit']}\"><img src='".ROOT."images/icons/picture_edit.png'  border='0'></a>";
					$text .= "
				</div>
				<div class='gallery_title'>
					" . trimlink((!empty($row2['pavadinimas'])?$row2['pavadinimas']:''),10) . "
				</div>
			</div>
		";
			}
			$text .= '</td>
	</tr>
</table>';
      }
			lentele($lang['admin']['gallery_edit'], $text);
			$visos = kiek('galerija', "WHERE `lang` = ".escape(lang())." AND `categorija`=".escape((isset($_GET['k'])? $_GET['k'] : 0))."");
		
	} elseif ($_GET['v'] == 1 || isset($url['h'])) {

		if (sizeof($sql) > 0) {

			$forma = array(
				"Form" => array("enctype" => "multipart/form-data", "action" => url("?id," . $_GET['id'] . ";a," . $_GET['a']), "method" => "post", "name" => "action"),
				(!isset($extra)) ? "{$lang['admin']['gallery_file']}:" : "" => array("name" => "failas", "type" => (!isset($extra)) ? "file" : "hidden", "value" => ""),
				"{$lang['admin']['gallery_title']}:" => array("type" => "text", "value" => (isset($extra['pavadinimas'])) ? input($extra['pavadinimas']) : '', "name" => "Pavadinimas"), 
				"{$lang['system']['category']}:" => array("type" => "select", "value" => $kategorijos, "name" => "cat", "class" => "input", "selected" => (isset($extra['categorija']) ? input($extra['categorija']) : '')), 
				"{$lang['admin']['gallery_about']}:" =>	array("type" => "textarea", "name" => "Aprasymas",  "rows" => "3", "class" => "input", "value" => (isset($extra['apie'])) ? input($extra['apie']) : ''), 
				(isset($extra)) ? $lang['admin']['edit'] : $lang['admin']['gallery_add'] => array("type" => "submit", "name" => "action", "value" => (isset($extra)) ? $lang['admin']['edit'] : $lang['admin']['gallery_add'])
			);
			if (isset($extra)) {
				$forma[''] = array("type" => "hidden", "name" => "news_id", "value" => (isset($extra) ? input($extra['ID']) : ''));
			}
			lentele(((isset($extra)) ? $lang['admin']['edit'] : $lang['admin']['gallery_add']), '<a name="edit"></a>'.((isset($extra['file'])) ? '<center><img src="'.ROOT.'images/galerija/' . input($extra['file']) . '"></center>' : '') . $bla->form($forma));
		} else {
			klaida($lang['system']['warning'], "{$lang['system']['nocategories']}");
		}
	} elseif ($_GET['v'] == 6) {
		if (isset($_POST) && !empty($_POST) && isset($_POST['Konfiguracija'])) {
			$q = array();
			$q[] = "UPDATE `" . LENTELES_PRIESAGA . "nustatymai` SET `val` = " . escape((int)$_POST['fotodyd']) . " WHERE `key` = 'fotodyd' LIMIT 1 ; ";
			$q[] = "UPDATE `" . LENTELES_PRIESAGA . "nustatymai` SET `val` = " . escape((int)$_POST['minidyd']) . " WHERE `key` = 'minidyd' LIMIT 1 ; ";
			//$q[] = "UPDATE `" . LENTELES_PRIESAGA . "nustatymai` SET `val` = " . escape((int)$_POST['galbalsuot']) . " WHERE `key` = 'galbalsuot' LIMIT 1 ; ";
			$q[] = "UPDATE `" . LENTELES_PRIESAGA . "nustatymai` SET `val` = " . escape((int)$_POST['fotoperpsl']) . " WHERE `key` = 'fotoperpsl' LIMIT 1 ; ";
			$q[] = "UPDATE `" . LENTELES_PRIESAGA . "nustatymai` SET `val` = " . escape((int)$_POST['galkom']) . " WHERE `key` = 'galkom' LIMIT 1 ; ";
			$q[] = "INSERT INTO `" . LENTELES_PRIESAGA . "nustatymai` (`val`,`key`) VALUES (" . escape($_POST['order']) . ",'galorder')  ON DUPLICATE KEY UPDATE `val`=" . escape($_POST['order']);
			$q[] = "INSERT INTO `" . LENTELES_PRIESAGA . "nustatymai` (`val`,`key`) VALUES (" . escape($_POST['order_type']) . ",'galorder_type')  ON DUPLICATE KEY UPDATE `val`=" . escape($_POST['order_type']);
			foreach ($q as $sql) {
				mysql_query1($sql);
			}
			redirect(url('?id,'.$_GET['id'].';a,' . $url['a'] . ';v,6'));
		}
		$nustatymai = array(
			"Form" => array("action" => "", "method" => "post", "enctype" => "", "id" => "", "class" => "", "name" => "reg"),
			"{$lang['admin']['gallery_maxwidth']}:" => array("type" => "text", "value" => input($conf['fotodyd']), "name" => "fotodyd"),
			"{$lang['admin']['gallery_minwidth']}:" => array("type" => "text", "value" => input($conf['minidyd']), "name" => "minidyd"),
			//"{$lang['admin']['gallery_rate']}:" => array("type" => "select", "value" => array("1" => "{$lang['admin']['yes']}", "0" => "{$lang['admin']['no']}"), "selected" => input($conf['galbalsuot']), "name" => "galbalsuot"),
			"{$lang['admin']['gallery_comments']}:" => array("type" => "select", "value" => array("1" => "{$lang['admin']['yes']}", "0" => "{$lang['admin']['no']}"), "selected" => input($conf['galkom']), "name" => "galkom"),
			"{$lang['admin']['gallery_images_per_page']}:" => array("type" => "text", "value" => input((int)$conf['fotoperpsl']), "name" => "fotoperpsl"),
			
			"{$lang['admin']['gallery_order']}:" => array("type" => "select", "selected" =>(isset($conf['galorder'])?$conf['galorder']:''), "value" => array('data' => $lang['admin']['gallery_date'], 'pavadinimas' => $lang['admin']['gallery_title'], 'autorius' => $lang['admin']['gallery_author']), "name" => "order"),
			
			"{$lang['admin']['gallery_order_type']}:" => array("type" => "select", "selected" =>(isset($conf['galorder_type'])?$conf['galorder_type']:''), "value" => array('DESC' => $lang['admin']['gallery_from_biggest'], 'ASC' => $lang['admin']['gallery_from_smallest']), "name" => "order_type"),
			"" => array("type" => "submit", "name" => "Konfiguracija", "value" => "{$lang['admin']['save']}")
		);

		include_once (ROOT."priedai/class.php");
		$bla = new forma();
		lentele($lang['admin']['gallery_conf'], $bla->form($nustatymai));

	} elseif ($_GET['v'] == 7) {

		$q = mysql_query1("SELECT
  `" . LENTELES_PRIESAGA . "galerija`.`pavadinimas`,
  `" . LENTELES_PRIESAGA . "galerija`.`id` ,
  `" . LENTELES_PRIESAGA . "galerija`.`apie`,
  `" . LENTELES_PRIESAGA . "galerija`.`data`,
  `" . LENTELES_PRIESAGA . "users`.`nick` AS `Nick`,
  `" . LENTELES_PRIESAGA . "galerija`.`file`
  FROM
  `" . LENTELES_PRIESAGA . "galerija`
  
  Inner Join `" . LENTELES_PRIESAGA . "users` ON `" . LENTELES_PRIESAGA . "galerija`.`autorius` = `" . LENTELES_PRIESAGA . "users`.`id`
  WHERE  
   `" . LENTELES_PRIESAGA . "galerija`.`rodoma` =  'NE' 
    ORDER BY
  `" . LENTELES_PRIESAGA . "galerija`.`".$conf['galorder']."` ".$conf['galorder_type']."
  ");
		if ($q) {

			include_once (ROOT."priedai/class.php");
			$bla = new Table();
			$info = array();
			if (sizeof($q) > 0) {
				foreach ($q as $row) {
					if (isset($row['Nick'])) {
						$autorius = $row['Nick'];
					} else {
						$autorius = $lang['system']['guest'];
					}

					$info[] = array( //"ID"=> $row['ID'],
						"{$lang['admin']['gallery_image']}:" => "<a href='".url("?id,{$_GET['id']};a,{$_GET['a']}")."' title='<img src=".ROOT."images/galerija/" . $row['file'] . "><br><b>{$lang['admin']['gallery_author']}:</b> " . $autorius . "<br>
		<b>{$lang['admin']['gallery_date']}:</b> " . date('Y-m-d H:i:s ', $row['data']) . "<br>
		<b>{$lang['admin']['gallery_about']}:</b> " . $row['apie'] . "'>" . $row['pavadinimas'] . " ...</a>", "{$lang['admin']['action']}:" => "<a href='".url("?id,{$_GET['id']};a,{$_GET['a']};p," . $row['id'] ). "'title='{$lang['admin']['acept']}'><img src='".ROOT."images/icons/tick_circle.png' border='0'></a> <a href='".url("?id,{$_GET['id']};a,{$_GET['a']};t," . $row['id'] ). "' title='{$lang['admin']['delete']}' onClick=\"return confirm('" . $lang['system']['delete_confirm'] . "')\"><img src='".ROOT."images/icons/cross.png' border='0'></a> <a href='".url("?id,{$_GET['id']};a,{$_GET['a']};h," . $row['id'] ). "' title='{$lang['admin']['edit']}'><img src='".ROOT."images/icons/picture_edit.png' border='0'></a>");

				}
				lentele($lang['admin']['gallery_unpublished'], $bla->render($info));

			}
		}
	} elseif ($_GET['v'] == 9 && $_SESSION['level'] == 1){
      $text = "
      <link href=\"uploadify/css/default.css\" rel=\"stylesheet\" type=\"text/css\" />
<link href=\"uploadify/css/uploadify.css\" rel=\"stylesheet\" type=\"text/css\" />
<script type=\"text/javascript\" src=\"uploadify/scripts/swfobject.js\"></script>
<script type=\"text/javascript\" src=\"uploadify/scripts/jquery.uploadify.v2.1.0.min.js\"></script>
<script type=\"text/javascript\">
$(document).ready(function() {
	$(\"#uploadify\").uploadify({
		'uploader'       : 'uploadify/scripts/uploadify.swf',
		'script'         : '".adresas()."/uploadify/scripts/uploadify.php',
		'cancelImg'      : 'uploadify/cancel.png',
		'folder'         : 'images/galerija',
		'scriptData'     : {'PHPSESSID': '".session_id()."'},
		'queueID'        : 'fileQueue',
		'auto'           : true,
		'multi'          : true,
		'buttonText'     : '".$lang['admin']['file_new']."'
		  
	});
});
</script>
      <fieldset><legend>{$lang['system']['categories']}:</legend><select id=\"cat\" onchange=\"$('#uploadify').uploadifySettings('scriptData', {'cat':$(this).val()});\" >";
      foreach($kategorijos as $id => $kategorija){
         $text .="<option value=\"$id\">$kategorija";
      }
      $text .= "</select></fieldset>
            


<div id=\"fileQueue\"></div>
<input type=\"file\" name=\"uploadify\" id=\"uploadify\" />
<p><a href=\"javascript:jQuery('#uploadify').uploadifyClearQueue()\">".$lang['admin']['cancel']."</a></p>"
;
      lentele($lang['admin']['gallery_group_add'],$text);
	}

}
unset($sql, $extra, $row);
//unset($_POST);


?>