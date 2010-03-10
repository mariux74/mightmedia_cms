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
	header('location: ?');
	exit();
}
//admin_login();
unset($extra);

$buttons = "<div class=\"btns\"><a href=\"".url("?id,{$_GET['id']};a,{$_GET['a']};v,6")."\" class=\"btn\"><span><img src=\"".ROOT."images/icons/sticky_note__exclamation.png\" alt=\"\" class=\"middle\"/>{$lang['admin']['news_unpublished']}</span></a> <a href=\"".url("?id,{$_GET['id']};a,{$_GET['a']};v,1")."\" class=\"btn\"><span><img src=\"".ROOT."images/icons/sticky_note__pencil.png\" alt=\"\" class=\"middle\"/>{$lang['admin']['news_create']}</span></a> <a href=\"".url("?id,{$_GET['id']};a,{$_GET['a']};v,4")."\" class=\"btn\"><span><img src=\"".ROOT."images/icons/sticky_note__pencil.png\" alt=\"\" class=\"middle\"/>{$lang['admin']['news_edit']}</span></a>
<a href=\"".url("?id,{$_GET['id']};a,{$_GET['a']};v,2")."\" class=\"btn\"><span><img src=\"".ROOT."images/icons/folder__plus.png\" alt=\"\" class=\"middle\"/>{$lang['system']['createcategory']}</span></a>
<a href=\"".url("?id,{$_GET['id']};a,{$_GET['a']};v,3")."\" class=\"btn\"><span><img src=\"".ROOT."images/icons/folder__pencil.png\" alt=\"\" class=\"middle\"/>{$lang['system']['editcategory']}</span></a>
</div>";

lentele($lang['admin']['naujienos'], $buttons);
include_once (ROOT."priedai/kategorijos.php");
kategorija("naujienos", true);
$sql = mysql_query1("SELECT * FROM  `" . LENTELES_PRIESAGA . "grupes` WHERE `kieno`='naujienos' AND `path`=0 ORDER BY `id` DESC");
if (sizeof($sql) > 0) {
	/*foreach ($sql as $row) {

		$sql2 = mysql_query1("SELECT * FROM  `" . LENTELES_PRIESAGA . "grupes` WHERE `kieno`='naujienos' AND path!=0 and `path` like '" . $row['id'] . "%' ORDER BY `id` ASC");
		if (sizeof($sql2) > 0) {
			$subcat = '';
			foreach ($sql2 as $path) {

				$subcat .= "->" . $path['pavadinimas'];
				$kategorijos[$row['id']] = $row['pavadinimas'];
				$kategorijos[$path['id']] = $row['pavadinimas'] . $subcat;


			}
		} else {
			$kategorijos[$row['id']] = $row['pavadinimas'];
		}


	}*/
	$kategorijoss=cat('naujienos', 0);
}
/*else
{
$kategorijos[] = "{$lang['system']['nocategories']}";

}*/
$kategorijos[0] = "---";
if (isset($_GET['p'])) {
	$result = mysql_query1("UPDATE `" . LENTELES_PRIESAGA . "naujienos` SET rodoma='TAIP' WHERE `id`=" . escape($_GET['p']) . ";");
	if ($result) {
		msg($lang['system']['done'], "{$lang['admin']['news_activated']}.");
	} else {
		klaida($lang['system']['error'], "{$lang['system']['error']}<br><b>" . mysql_error() . "</b>");
	}
}

//Naujienos trinimas
if (((isset($_POST['action']) && $_POST['action'] == $lang['admin']['delete']  && isset($_POST['edit_new']) && $_POST['edit_new'] > 0)) || isset($url['t'])) {
	if (isset($url['t'])) {
		$trinti = (int)$url['t'];
	} elseif (isset($_POST['edit_new'])) {
		$trinti = (int)$_POST['edit_new'];
	}
	$ar = mysql_query1("DELETE FROM `" . LENTELES_PRIESAGA . "naujienos` WHERE id=" . escape($trinti) . " LIMIT 1");
	if ($ar) {
		msg($lang['system']['done'], $lang['admin']['news_deleted']);
	} else {
		klaida($lang['system']['error'], $lang['system']['error']);
	}
	mysql_query1("DELETE FROM `" . LENTELES_PRIESAGA . "kom` WHERE pid='puslapiai/naujienos' AND kid=" . escape($trinti) . "");
	//redirect("?id,".$_GET['id'].";a,".$_GET['a'],"header");
}


//Naujienos redagavimas
elseif (((isset($_POST['edit_new']) && isNum($_POST['edit_new']) && $_POST['edit_new'] > 0)) || isset($url['h'])) {
	if (isset($url['h'])) {
		$redaguoti = (int)$url['h'];
	} elseif (isset($_POST['edit_new'])) {
		$redaguoti = (int)$_POST['edit_new'];
	}

	$extra = mysql_query1("SELECT * FROM `" . LENTELES_PRIESAGA . "naujienos` WHERE `id`=" . escape($redaguoti) . " LIMIT 1");
} elseif (isset($_POST['Kategorijos_id']) && isNum($_POST['Kategorijos_id']) && $_POST['Kategorijos_id'] > 0 && isset($_POST['Kategorija']) && $_POST['Kategorija'] == $lang['admin']['edit']) {
	$extra = mysql_query1("SELECT * FROM `" . LENTELES_PRIESAGA . "grupes` WHERE `id`=" . escape((int)$_POST['Kategorijos_id']) . " LIMIT 1");
}

//Išsaugojam redaguojamą naujieną
elseif (isset($_POST['action']) && $_POST['action'] == $lang['admin']['edit']) {
	$naujiena = $_POST['naujiena'];
	$placiau =$_POST['placiau'];
$komentaras = (isset($_POST['kom']) ? $_POST['kom'] : 'taip');
	$kategorija = (int)$_POST['kategorija'];
	$pavadinimas = strip_tags($_POST['pav']);
	$id = ceil((int)$_POST['news_id']);

	if ($komentaras == 'ne') {
		mysql_query1("DELETE FROM `" . LENTELES_PRIESAGA . "kom` WHERE pid=" . escape((int)$_GET['id']) . " AND kid=" . escape($id));
	}

	mysql_query1("UPDATE `" . LENTELES_PRIESAGA . "naujienos` SET
			`pavadinimas` = " . escape($pavadinimas) . ",
			`kategorija` = " . escape($kategorija) . ",
			`naujiena` = " . escape($naujiena) . ",
			`daugiau` = " . escape($placiau) . ",
			`kom` = " . escape($komentaras) . "
			WHERE `id`=" . escape($id) . ";
			");
	msg($lang['system']['done'], $lang['admin']['news_created']);
}

//Išsaugojam naujieną
elseif (isset($_POST['action']) && $_POST['action'] == $lang['admin']['news_create']) {
	$naujiena = $_POST['naujiena'];
	$placiau =  $_POST['placiau'];
	$komentaras = (isset($_POST['kom']) ? $_POST['kom'] : 'taip');
	$pavadinimas = strip_tags($_POST['pav']);
	$kategorija = (int)$_POST['kategorija'];

	if (empty($naujiena) || empty($pavadinimas)) {
		$error = $lang['admin']['news_required'];
	}
	if (!isset($error)) {
		$result = mysql_query1("INSERT INTO `" . LENTELES_PRIESAGA . "naujienos` (pavadinimas, naujiena, daugiau, data, autorius, kom, kategorija, rodoma) VALUES (" . escape($pavadinimas) . ", " . escape($naujiena) . ", " . escape($placiau) . ",  '" . time() . "', '" . $_SESSION['username'] . "', " . escape($komentaras) . ", " . escape($kategorija) . ", 'TAIP')");
		if ($result) {
			msg($lang['system']['done'], "{$lang['admin']['news_created']}");
		} else {
			klaida($lang['system']['error'], "<b>" . mysql_error() . "</b>");
		}
	} else {
		klaida($lang['system']['error'], $error);
	}


	//Trinam kategoriją

	unset($naujiena, $placiau, $komentaras, $pavadinimas, $result, $error, $_POST['action']);
	redirect("?id," . $_GET['id'] . ";a," . $_GET['a'] . "", "meta");

}

$sql = mysql_query1("SELECT id, pavadinimas FROM  `" . LENTELES_PRIESAGA . "naujienos` ORDER BY ID DESC");
foreach ($sql as $row) {
	$naujienos[$row['id']] = $row['pavadinimas'];
}


if (isset($_GET['v'])) {
	include_once (ROOT."priedai/class.php");
	$bla = new forma();

	if ($_GET['v'] == 4) {
		if (isset($naujienos)) {
			$redagavimas = array("Form" => array("action" => "?id,{$_GET['id']};a,{$_GET['a']};v,1", "method" => "post", "name" => "reg"), "{$lang['admin']['news_name']}:" => array("type" => "select", "value" => $naujienos, "name" => "edit_new"), "{$lang['admin']['edit']}:" => array("type" => "submit", "name" => "action", "value" => "{$lang['admin']['edit']}"), "{$lang['admin']['delete']}:" => array("type" => "submit", "name" => "action", "extra" => "onClick=\"return confirm('" . $lang['admin']['delete'] . "?')\"","value" => "{$lang['admin']['delete']}"));
			lentele($lang['admin']['edit'], $bla->form($redagavimas));
		} else {
			klaida($lang['system']['warning'], $lang['system']['no_items']);
		}
	} elseif ($_GET['v'] == 1 || isset($_GET['h'])) {
		if ($i = 1) {
			$kom = array('taip' => $lang['admin']['yes'], 'ne' => $lang['admin']['no']);
			$naujiena = array("Form" => array("action" => "?id," . $_GET['id'] . ";a," . $_GET['a'] . "", "method" => "post", "name" => "reg"), "{$lang['admin']['news_name']}:" => array("type" => "text", "value" => input((isset($extra)) ? $extra['pavadinimas'] : ''), "name" => "pav", "class" => "input"), $lang['admin']['komentarai'] => array("type" => "select", "selected" => input((isset($extra)) ? $extra['kom'] : ''), "value" => $kom, "name" => "kom", "class" => "input", "class" => "input"), "{$lang['admin']['news_category']}:" => array("type" => "select", "value" => $kategorijos, "name" => "kategorija", "class" => "input", "class" => "input", "selected" => (isset($extra['kategorija']) ? input($extra['kategorija']) : '')), "{$lang['admin']['news_text']}:" => array("type" => "string", "value" =>
				editorius('jquery', 'standartinis', array('naujiena' => $lang['admin']['news_preface'], 'placiau' => $lang['admin']['news_more']), array('naujiena' => (isset($extra)) ? $extra['naujiena'] : $lang['admin']['news_preface'], 'placiau' => (isset($extra)) ? $extra['daugiau'] : $lang['admin']['news_more']))), (isset($extra)) ? $lang['admin']['edit'] : $lang['admin']['news_create'] => array("type" => "submit", "name" => "action", "value" => (isset($extra)) ? $lang['admin']['edit'] : $lang['admin']['news_create']));

			if (isset($extra)) {
				$naujiena[''] = array("type" => "hidden", "name" => "news_id", "value" => (isset($extra) ? input($extra['id']) : ''));
			}

			lentele($lang['admin']['news_create'], $bla->form($naujiena));
		} else {
			klaida($lang['system']['warning'], $lang['system']['nocategories']);
		}
	} elseif ($_GET['v'] == 6) {

		$q = mysql_query1("SELECT * FROM `" . LENTELES_PRIESAGA . "naujienos` WHERE rodoma='NE'");
		if (sizeof($q) > 0) {

			include_once (ROOT."priedai/class.php");
			$bla = new Table();
			$info = array();
			foreach ($q as $sql) {
				//$sql2 = mysql_fetch_assoc(mysql_query1("SELECT nick FROM `".LENTELES_PRIESAGA."users` WHERE id='".$sql['autorius']."'"));

				$info[] = array("ID" => $sql['id'], "Naujiena:" => '<a href="#" title="<b>' . $sql['pavadinimas'] . '</b>
			<br /><br />
			' . $lang['admin']['news_author'] . ': <b>' . $sql['autorius'] . '</b><br />
			' . $lang['admin']['news_date'] . ': <b>' . date('Y-m-d H:i:s ', $sql['data']) . ' - ' . kada(date('Y-m-d H:i:s ', $sql['data'])) . '</b>" target="_blank">' . $sql['pavadinimas'] . '</a>', "{$lang['admin']['action']}:" => "<a href='".url("?id,{$_GET['id']};a,{$_GET['a']};p," . $sql['id'] ). "'title='{$lang['admin']['acept']}'><img src='".ROOT."images/icons/tick_circle.png' border='0'></a> <a href='".url("?id,{$_GET['id']};a,{$_GET['a']};t," . $sql['id'] ). "' title='{$lang['admin']['delete']}' onClick=\"return confirm('" . $lang['admin']['delete'] . "?')\"><img src='".ROOT."images/icons/stop.png' border='0'></a> <a href='".url("?id,{$_GET['id']};a,{$_GET['a']};h," . $sql['id'] ). "' title='{$lang['admin']['edit']}'><img src='".ROOT."images/icons/pencil.png' border='0'></a>");

			}
			lentele($lang['admin']['news_unpublished'], $bla->render($info));

		} else {
			klaida($lang['system']['warning'], $lang['system']['no_items']);
		}
	}


}


unset($sql, $extra, $row);
//unset($_POST);


?>