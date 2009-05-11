<?php

/**
 * @Projektas: MightMedia TVS
 * @Puslapis: www.coders.lt
 * @$Author$
 * @copyright CodeRS ©2008
 * @license GNU General Public License v2
 * @$Revision$
 * @$Date$
 **/


//Sarašas kur ieškoti
$kur = array();
if (isset($conf['puslapiai']['naujienos.php']['id'])) {
	$kur['naujienos'] = $lang['search']['news'];
}
if (isset($conf['puslapiai']['straipsnis.php']['id'])) {
	$kur['str'] = $lang['search']['articles'];
}
if (isset($conf['puslapiai']['siustis.php']['id'])) {
	$kur['siunt'] = $lang['search']['downloads'];
}
if (isset($conf['puslapiai']['frm.php']['id'])) {
	$kur['frmt'] = $lang['search']['forum_topics'];
}
if (isset($conf['puslapiai']['frm.php']['id'])) {
	$kur['frm'] = $lang['search']['forum_messages'];
}
if (isset($conf['puslapiai']['galerija.php']['id'])) {
	$kur['galerija'] = $lang['search']['images'];
}
if (isset($conf['puslapiai']['reg.php']['id'])) {
	$kur['memb'] = $lang['search']['members'];
}
$kur['kom'] = $lang['search']['comments'];
$kur['page'] = $lang['search']['pages'];
//$kur['vis'] = $lang['search']['everything'];
$box="";
foreach($kur as $name=>$check){
$box.="<label><input type=\"checkbox\" name=\"$name\" value=\"$name\"/> $check</label><br /> ";
}
$box.="<label><input type='checkbox' name='vis' onclick='checkedAll(\"search\");'/> {$lang['search']['everything']}<label>";
//Paieškos forma
$search = array("Form" => array("action" => "", "method" => "post", "enctype" => "", "id" => "search", "name" => "search"), " " => array("type" => "text", "value" => (isset($_POST['s']) ? input($_POST['s']) : ''), "name" => "s", "style" => "width:100%"), "{$lang['search']['for']}:" => array("type" => "string", "value" => $box), "" => array("type" => "submit", "class" => "submit", "name" => "subsearch", "value" => $lang['search']['search']));
$text = '';
//Nupiešiam paieškos formą
include_once ("priedai/class.php");
$bla = new forma();
lentele($lang['search']['search'], $bla->form($search));
$i = 0;
//Atliekam paiešką
//print_r($_POST);
if (isset($_POST['s'])) {
	if (strlen(str_replace(array(" ", "\r", "\n", "<", ">", "\"", "'", "."), "", $_POST['s'])) >= 3) {
		if ((isset($_POST['naujienos'])||isset($_POST['vis']))&& isset($conf['puslapiai']['naujienos.php']['id'])) {
			$sqlas3 = mysql_query1("SELECT * FROM `" . LENTELES_PRIESAGA . "naujienos` WHERE `pavadinimas` LIKE " . escape("%" . $_POST['s'] . "%") . " AND `rodoma`='TAIP' OR `naujiena` LIKE " . escape("%" . $_POST['s'] . "%") . " AND `rodoma`='TAIP' LIMIT 0,100") or die(klaida( mysql_error()));
			if (mysql_num_rows($sqlas3) > 0) {
				$text .= "<b>{$lang['search']['news']}</b><br />";
			}
			while ($row3 = mysql_fetch_assoc($sqlas3)) {
				$i++;
				$text .= "<a href='?id," . $conf['puslapiai']['naujienos.php']['id'] . ";k," . $row3['id'] . "'>" . trimlink(input($row3['pavadinimas']), 40) . "...</a><br />";
			}
		}
		if ((isset($_POST['frmt'])||isset($_POST['vis'])) && isset($conf['puslapiai']['frm.php']['id'])) {
			$sqlas4 = mysql_query1("SELECT * FROM `" . LENTELES_PRIESAGA . "d_straipsniai` WHERE `pav` LIKE " . escape("%" . $_POST['s'] . "%") . "LIMIT 0,100") or die(klaida( mysql_error()));
			if (mysql_num_rows($sqlas4) > 0) {
				$text .= "<b>{$lang['search']['forum_topics']}</b><br />";
			}
			while ($row4 = mysql_fetch_assoc($sqlas4)) {
				$i++;
				$text .= "<a href='?id," . $conf['puslapiai']['frm.php']['id'] . ";t," . $row4['id'] . ";s," . $row4['tid'] . "'>" . trimlink(input($row4['pav']), 40) . "...</a><br />";
			}
		}
		if ((isset($_POST['frm'])||isset($_POST['vis'])) && isset($conf['puslapiai']['frm.php']['id'])) {
			$sqlas5 = mysql_query1("SELECT * FROM `" . LENTELES_PRIESAGA . "d_zinute` WHERE `zinute` LIKE " . escape("%" . $_POST['s'] . "%") . "LIMIT 0,100") or die(klaida( mysql_error()));
			if (mysql_num_rows($sqlas5) > 0) {
				$text .= "<b>{$lang['search']['forum_messages']}</b><br />";
			}
			while ($row5 = mysql_fetch_assoc($sqlas5)) {
				$i++;
				$text .= "<a href='?id," . $conf['puslapiai']['frm.php']['id'] . ";t," . $row5['sid'] . ";s," . $row5['tid'] . "'>" . trimlink(input($row5['zinute']), 40) . "...</a><br />";
			}

		}
		if ((isset($_POST['str'])||isset($_POST['vis'])) && isset($conf['puslapiai']['straipsnis.php']['id'])) {
			$sqlas6 = mysql_query1("SELECT * FROM `" . LENTELES_PRIESAGA . "straipsniai` WHERE `t_text` LIKE " . escape("%" . $_POST['s'] . "%") . " AND `rodoma`='TAIP' or `f_text` LIKE " . escape("%" . $_POST['s'] . "%") . " AND `rodoma`='TAIP' or `pav` LIKE " . escape("%" . $_POST['s'] . "%") . " AND `rodoma`='TAIP' LIMIT 0,100") or die(klaida( mysql_error()));
			if (mysql_num_rows($sqlas6) > 0) {
				$text .= "<b>{$lang['search']['articles']}</b><br />";
			}
			while ($row6 = mysql_fetch_assoc($sqlas6)) {
				$i++;
				$text .= "<a href='?id," . $conf['puslapiai']['straipsnis.php']['id'] . ";k," . $row6['kat'] . ";m," . $row6['id'] . "'>" . trimlink(input($row6['pav']), 40) . "...</a><br />";
			}

		}
		if ((isset($_POST['siunt'])||isset($_POST['vis'])) && isset($conf['puslapiai']['siustis.php']['id'])) {
			$sqlas7 = mysql_query1("SELECT * FROM `" . LENTELES_PRIESAGA . "siuntiniai` WHERE  `pavadinimas` LIKE " . escape("%" . $_POST['s'] . "%") . " AND `rodoma`='TAIP' or `apie` LIKE " . escape("%" . $_POST['s'] . "%") . " AND `rodoma`='TAIP' LIMIT 0,100") or die(klaida( mysql_error()));
			if (mysql_num_rows($sqlas7) > 0) {
				$text .= "<b>{$lang['search']['downloads']}</b><br />";
			}
			while ($row7 = mysql_fetch_assoc($sqlas7)) {
				$i++;
				$text .= "<a href='?id," . $conf['puslapiai']['siustis.php']['id'] . ";k," . $row7['categorija'] . ";v," . $row7['ID'] . "'>" . trimlink(input($row7['pavadinimas']), 40) . "...</a><br />";
			}

		}
		if ((isset($_POST['galerija'])||isset($_POST['vis'])) && isset($conf['puslapiai']['galerija.php']['id'])) {
			$sqlas7 = mysql_query1("SELECT * FROM `" . LENTELES_PRIESAGA . "galerija` WHERE `pavadinimas` LIKE " . escape("%" . $_POST['s'] . "%") . " AND `rodoma`='TAIP' or `apie` LIKE " . escape("%" . $_POST['s'] . "%") . " AND `rodoma`='TAIP' LIMIT 0,100") or die(klaida( mysql_error()));
			if (mysql_num_rows($sqlas7) > 0) {
				$text .= "<b>{$lang['search']['images']}</b><br />";
			}
			while ($row7 = mysql_fetch_assoc($sqlas7)) {
				$i++;
				$text .= "<a href='?id," . $conf['puslapiai']['galerija.php']['id'] . ";m," . $row7['ID'] . "'>" . trimlink(input($row7['pavadinimas']), 40) . "...</a><br />";
			}

		}
if ((isset($_POST['memb'])||isset($_POST['vis']))&& isset($conf['puslapiai']['reg.php']['id'])) {
			$sqlas9 = mysql_query1("SELECT id,nick,levelis FROM `" . LENTELES_PRIESAGA . "users` WHERE `nick` LIKE " . escape("%" . $_POST['s'] . "%") . "") or die(klaida( mysql_error()));
			if (mysql_num_rows($sqlas9) > 0) {
				$text .= "<b>{$lang['search']['members']}</b><br />";
			}
			while ($row9 = mysql_fetch_assoc($sqlas9)) {
				$i++;
				
				$text .= user($row9['nick'],$row9['id'],$row9['levelis'])."<br />";
			}

		}
if (isset($_POST['page'])||isset($_POST['vis'])) {
			$sqlas10 = mysql_query1("SELECT id,pavadinimas FROM `" . LENTELES_PRIESAGA . "page` WHERE `pavadinimas` LIKE " . escape("%" . $_POST['s'] . "%") . "") or die(klaida( mysql_error()));
			if (mysql_num_rows($sqlas10) > 0) {
				$text .= "<b>{$lang['search']['pages']}</b><br />";
			}
			while ($row10 = mysql_fetch_assoc($sqlas10)) {
				$i++;
				
				$text .= "<a href=\"?id,{$row10['id']}\">{$row10['pavadinimas']}</a><br />";
			}

		}	
		
		if (isset($_POST['kom'])||isset($_POST['vis'])) {
			$sqlas2 = mysql_query1("SELECT * FROM `" . LENTELES_PRIESAGA . "kom` WHERE `zinute` LIKE " . escape("%" . $_POST['s'] . "%") . "LIMIT 0,100") or die(klaida( mysql_error()));
			if (mysql_num_rows($sqlas2) > 0) {
				$text .= "<b>{$lang['search']['comments']}</b><br />";
			}
			while ($row2 = mysql_fetch_assoc($sqlas2)) {
				if ($row2['pid'] == 'puslapiai/naujienos' && isset($conf['puslapiai']['naujienos.php']['id'])) {
					$link = "k," . $row2['kid'];
				} elseif ($row2['pid'] == 'puslapiai/view_user' && isset($conf['puslapiai']['view_user.php']['id'])) {
					$link = "m," . $row2['kid'];
				} elseif ($row2['pid'] == 'puslapiai/galerija' && isset($conf['puslapiai']['view_user.php']['id'])) {
					$link = "m," . $row2['kid'];
				} elseif ($row2['pid'] == 'puslapiai/straipsnis' && isset($conf['puslapiai']['straipsnis.php']['id'])) {
					$link = "m," . $row2['kid'] . "";
				} elseif ($row2['pid'] == 'puslapiai/siustis' && isset($conf['puslapiai']['siustis.php']['id'])) {

					$linkas = mysql_fetch_assoc(mysql_query1("SELECT categorija FROM `" . LENTELES_PRIESAGA . "siuntiniai` WHERE `ID`='" . $row2['kid'] . "'LIMIT 1"));
					$link = "k," . $linkas['categorija'] . "v," . $row2['kid'] . "";
				} else {
					$link = "";
				}


				$i++;
				$file = str_replace('puslapiai/', '', $row2['pid']);
				if (isset($conf['puslapiai']['' . $file . '.php']['id'])) {
					$text .= "<a href=?id," . $conf['puslapiai']['' . $file . '.php']['id'] . ";" . $link . "#" . $row2['id'] . ">" . substr(input($row2['zinute']), 0, 200) . "...</a><br />";
				}
			}
		}
		if ($i > 0) {
			//$kiek = mysql_num_rows($sqlas);
			//msg($lang['system']['done'],"<b>".input(str_replace("%"," ",$_POST['s']))."</b><br/>Rasta atikmenų: ".$i);
			lentele($lang['search']['results'], $text);
		} else {
			klaida($lang['system']['sorry'], "<b>" . input(str_replace("%", " ", $_POST['s'])) . "</b> {$lang['search']['notfound']}");
		}
	} else {
		klaida($lang['system']['warning'], $lang['search']['short']);
	}
}

unset($kur, $ka, $link, $link2, $link3, $text, $row, $search, $kuriam, $iskur, $iskurdar, $sqlas, $bla, $forma);

?>