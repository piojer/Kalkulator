<html><head><meta http-equiv="Content-type" content="text/html; charset=iso-8859-2" />
<script language="JavaScript">
	function pokarz(text){
		document.getElementById('komentarz').innerHTML = text;
	}
	function pokarze(id){
		document.getElementById('komentarz').innerHTML = document.getElementById(id).innerHTML;
		document.getElementById('komentarz').style.display = 'block';
	}
	function pokarzout(){
		document.getElementById('komentarz').style.display = 'none';
	}
	function rozwin(id, kogo){
		a = document.getElementById(id);
		b = document.getElementById(kogo);
		if (a == null) {
			message = "function rozwin id:" + id + " kogo:" + kogo;
			alert(message + " ERROR: Element id is null");
		} else if (b == null) {
			message = "function rozwin id:" + id + " kogo:" + kogo;
			alert(message + " ERROR: Element kogo is null");
		} else if (a.style.display == 'table-row-group') {
			a.style.visibility = 'hidden';
			a.style.display = 'none';
			b.innerHTML = '(+)';
			//alert("hide");
		} else {
			a.style.visibility = 'visible';
			a.style.display = 'table-row-group';
			b.innerHTML = '(-)';
			//alert("show");
		}//*/
	}
</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart(id, percent, percent1, percent2, percent3) {
		var percentOth = 100 - percent;
		percentOth = Math.round(percentOth*10)/10;
		percent = percent - percent1 - percent2 - percent3;
		percent = Math.round(percent*10)/10;
        var data = google.visualization.arrayToDataTable([
          ['Task', 'Percentage'],
		  ['Pa�stwo', percent],
		  ['Korporacje', percent1],
		  ['Zmarnowane', percent2],
		  ['Prywatny ubezpieczyciel', percent3],
          ['Obywatel', percentOth]
        ]);
 
        var options = {
          title: '',
		  backgroundColor: 'rgb(19,44,75)',
		  legend: {position:'none'},
		  slices: {0:{color:'red'}, 1:{color:'yellow'}, 2:{color:'orange'}, 3:{color:'blue'}, 4:{color:'green'}},
		  chartArea: {left:5,top:5,width:'110',height:'110'},
		  height: '120'
        };
 
        var chart = new google.visualization.PieChart(document.getElementById(id));
        chart.draw(data, options);
      }
</script>
<style>
	td, th {padding:1px 10px}
	td.kwota {width:60}
	table.ramka {background-color:rgb(19,44,75)}
	.ramka, .ramka td {color:white}
	tbody.rozwiniecie {display:none;visibility:hidden;font-size:small;}
	tbody.rozwiniecie td {color:rgb(150,180,200)}
	a.rozwiniecie {cursor:pointer}
	.dodatek {color:rgb(150,90,100); font-size:small;}
	tbody.naglowek {font-size:small;}
	tbody.naglowek td {font-size:small;padding-left:15}
	img {width:150;height:125;}
	input {width:120}
	wykres {width:100;height:60}
	.komentarz {
		position:absolute;
		left: 50%;
		top: 5%;
		width: 45%;
		padding: 10px;
		background-color: yellow;
		display: none;
	}
	.komentarz_tresc {display:none;visibility:hidden;position:absolute}
</style>
</head><body>
<?php
//position: absolute; height: 90%;
/*function drawChart2(id, params) {
		var percent2 = 100 - percent;
		//window.alert("id: " + id);
        var data = google.visualization.arrayToDataTable(params);
		//window.alert("params: " + params);
		
        var options = {
          title: '',
		  backgroundColor: 'rgb(19,44,75)',
		  legend: {position:'none'},
		  slices: {1:{color:'green'}, 0:{color:'red'}}
        };
 
        var chart = new google.visualization.PieChart(document.getElementById(id));
        chart.draw(data, options);
      }
	  */

include "kalkulator.php";




$nrDoKomentarz=0;
function komentarz($tekst, $komentarz){
	global $nrDoKomentarz;
	$a = "<div class='komentarz_tresc' id='k$nrDoKomentarz'>$komentarz</div>";
	$mouse = "onmouseover=\"pokarze('k$nrDoKomentarz')\" onmouseout=\"pokarzout()\"";
	
	++$nrDoKomentarz;
	return "<div $mouse>$tekst</div>$a";
	//return $tekst;
}

function rozwiniecie($id){
	return "<a id='przyc_$id' class='rozwiniecie' onclick='rozwin(\"$id\", \"przyc_$id\")'>(+)</a>";
}


if (!isset($_GET['netto'])) $_GET['mediana'] = true;


if (isset($_GET['minimalna'])) {
	$_GET = Array('netto' => 1237, 'nier' => 100000, 'zwrot' => 1000, 'dorosli' => 2, 'dzieci' => 0, 'pomoc' => 0, 'prywatna'=>0);
	$tytul = "Minimalna krajowa";
	$podTytul = "(Na utrzymaniu rodzina 2+1)";
} else if (isset($_GET['mediana'])){
	$_GET = Array('netto' => 2052, 'nier' => 100000, 'zwrot' => 1000, 'dorosli' => 2, 'dzieci' => 1, 'pomoc' => 0, 'prywatna'=>0);
	$tytul = "Mediana wynagrodze�";
	$podTytul = "(Na utrzymaniu rodzina 2+2)";
} else if (isset($_GET['srednia'])){
	$tytul = "�rednia krajowa";
	$podTytul = "(Na utrzymaniu rodzina 2+2)";
	$_GET = Array('netto' => 2730, 'nier' => 300000, 'zwrot' => 1000, 'dorosli' => 2, 'dzieci' => 2, 'pomoc' => 0, 'prywatna'=>0);
} else if (isset($_GET['dominanta'])){
	$tytul = "Najcz�strze wynagrodzenie";
	$podTytul = "(Na utrzymaniu rodzina 2+1)";
	$_GET = Array('netto' => 1600, 'nier' => 200000, 'zwrot' => 1000, 'dorosli' => 2, 'dzieci' => 1, 'pomoc' => 106, 'prywatna'=>0);
} else if (isset($_GET['niep'])){
	$_GET = Array('netto' => 1600, 'nier' => 200000, 'zwrot' => 1000, 'dorosli' => 2, 'dzieci' => 1, 'pomoc' => 1186, 'prywatna'=>0);
} else if (isset($_GET['wielodzietna'])){
	$tytul = "Najcz�strze wynagrodzenie";
	$podTytul = "(Na utrzymaniu rodzina 2+1)";
	$_GET = Array('netto' => 1600+1237, 'nier' => 200000, 'zwrot' => 1000, 'dorosli' => 2, 'dzieci' => 4, 'pomoc' => (106*4+80*2), 'prywatna'=>0);
}


extract($_GET);
$osoby = $dorosli + $dzieci;



function wypiszInputy(){
	echo "<form method='GET'><table>";
	wypiszInput('Zarobki miesieczne netto na etacie i zleceniu ca�ej rodziny', 'netto', '');
	wypiszInput('Zarobki miesieczne netto na umowie o dzie�o', 'nettod', '');
	wypiszInput('Szacunkowa warto�� Twoich nieruchomo�ci', 'nier', '');
	wypiszInput('Zwrot podatku w tym roku', 'zwrot', 'z minusem, jesli dop�ata');
	wypiszInput('Pomoc socialna pa�stwowa, zasi�ki i ulgi podatkowe', 'pomoc', 'i ulgi podatkowe miesiecznie');
	wypiszInput('Pomoc z fundacji/rodziny', 'prywatna', '');
	wypiszInput('Ilo�� doros�ych w rodzinie', 'dorosli', '��cznie z Tob�');
	wypiszInput('Ilo�� dzieci w rodzinie', 'dzieci', '');
	echo "</table><table><tr><td style='height:100%'><input type='submit' value='przelicz' style='height:100%;font-size:18px;padding:30px'>";
	$f = "style='font-size:12px'";
	echo "</td><td $f>zapisane: <input type='submit' value='minimalna krajowa' name='minimalna' $f>";
	echo "<input type='submit' value='najcz�stsze (dominanta)' name='dominanta' $f>";
	echo "<input type='submit' value='mediana wynagrodze�' name='mediana' $f>";
	echo "<input type='submit' value='�rednia' name='srednia' $f>";
	echo "<br/><input type='submit' name='niep' value='teraz na czasie: rodzina z niepe�nosprawnym dzieckiem' $f>";
	echo "<input type='submit' name='wielodzietna' value='rodzina wielodzietna' $f>";
	echo "</td></tr></table></form>";
}



function wiersz($tekst, $kwota){
	//echo "wiersz($tekst, $kwota)\n";
	echo "<tr><td>$tekst</td><td colspan = \"2\">".myround($kwota)." z�</td></tr>";
}
function wierszp($tekst, $tekst1){
	//echo "wiersz2p($tekst, $tekst1, $tekst2)";
	echo "<tr><td>$tekst</td><td colspan = \"2\">$tekst1</td></tr>";
}
function wiersz2($tekst, $kwota, $kwota2){
	//echo "wiersz2($tekst, $kwota, $kwota2\n";
	echo "<tr><td>$tekst</td><td>".myround($kwota)." z�</td><td class='dodatek'><div class='dodatek'>".myround($kwota2)." z�</div></td></tr>";
}
function wiersz2p($tekst, $tekst1, $tekst2){
	//echo "wiersz2p($tekst, $tekst1, $tekst2)";
	echo "<tr><td>$tekst</td><td>$tekst1</td><td class='dodatek'><div class='dodatek'>$tekst2</div></td></tr>";
}

function wiersza($tekst, $a, $pole, $komentarz = '', $post = ' z�', $dod = '', $prefix = ''){
	echo "<tr><td>$tekst</td>";
	foreach ($a as $k){
		if ($komentarz != "")
			$v = komentarz($prefix.myround($k->$pole).$post, $komentarz);
		else
			$v = $prefix.myround($k->$pole).$post;
		echo "<td colspan = \"2\">$v $dod</td>";
		$dod = '';
	}
	echo "</tr>";
}

function wiersza0($tekst, $a, $pole, $komentarz = '', $post = ' z�', $dod = '', $prefix = ''){
	echo "<tr><td>$tekst</td>";
	
	foreach ($a as $k){
		$w = myround($k->$pole);
		if ($w == 0) {
			$v = ''; $dod = '';
		} else 	if ($komentarz != "")
			$v = komentarz($prefix.$w.$post, $komentarz);
		else
			$v = $prefix.$w.$post;
		echo "<td colspan = \"2\">$v $dod</td>";
		$dod = '';
	}
	echo "</tr>";
}
function wiersz2a($tekst, $a, $pole, $pole2){
	echo "<tr><td>$tekst</td>";
	foreach ($a as $k){
		echo "<td>".myround($k->$pole)." z�</td></td><td class='dodatek'><div class='dodatek'>".myround($k->$pole2)." z�</div></td>";
	}
	echo "</tr>";
}


function wierszWyniki($tekst, $id, $a, $idx){
	if ($tekst != ""){
		$vis = "visibility='hidden'";
		echo "<tr><td>$tekst ". rozwiniecie($id) ."</td>";
		foreach ($a as $k){
			echo "<td colspan = \"2\">".myround($k->wyniki[$idx]->suma)." z�</td>";
		}
		echo "</tr>";
	}
	else {
		$vis = "style='display:table-row-group;visibility:visible'";
	}
	echo "<tbody class='rozwiniecie' id='$id' $vis>";
	$keys = array();
	foreach ($a as $k){
		$kk = array_keys($k->wyniki[$idx]->czastkowe);
		$keys = array_unique(array_merge($kk, $keys));
	}
	foreach ($keys as $nazwa) {
		echo "<tr><td>$nazwa</td>";
		foreach ($a as $k){
			if (isset($k->wyniki[$idx]) && isset($k->wyniki[$idx]->czastkowe[$nazwa]))
				echo "<td colspan = \"2\">".myround($k->wyniki[$idx]->czastkowe[$nazwa])." z�</td>";
			else
				echo "<td colspan = \"2\">0 z�</td>";
		}
		echo "</tr>";
	}
	echo "</tbody>";
}

function wiersz2pA($tekst, $tekst1, $tekst2, $count){
	echo "<tr><td>$tekst</td>";
	for ($i = 0; $i < $count; ++$i){
		echo "<td>$tekst1</td><td class='dodatek'><div class='dodatek'>$tekst2</div></td>";
	}
	echo "</tr>";
}

function wiersz2wydatki($nazwa, $a, $key){
	echo "<tr><td>$nazwa</td>";
	foreach ($a as $k){
		$wyd = $k->wydatki[$key];
		$kw = myround($wyd->kwota);
		$pod = myround($wyd->kwota - $wyd->kwotaNetto) . ' z�';
		$kom = "<h2>$nazwa</h2>";
		$kom .= "<table><tr><td>brutto:</td><td>$kw z�</td></td></tr>";
		foreach($wyd->wyniki->czastkowe as $n => $v){
			$kom .= "<tr><td>$n</td><td>".myround($v)." z�</td></td></tr>";
		}
		$kom .= "<tr><td>Prawdziwa warto��</td><td>".myround($wyd->kwotaNetto)." z�</td></td></tr>";
		$kom .= "</table>";
		$pod = komentarz($pod, $kom);
		echo "<td>$kw z�</td><td class='dodatek'><div class='dodatek'>$pod</div></td>";
	}
	echo "</tr>";
}

function wierszLinia(){
	echo "<tr><td colspan = \"100\"><hr/></td></tr>";
}

function wierszPrzerwa(){
	echo "<tr><td colspan ='100'><div style='height:30px'</td></tr>";
}

//Inicjowanie 
/////////////////////////////////////////////////////////////
$k = new Kalkulator();
$k->podatki = $k->defaultPodatki($k->wydatki);
//$k->podatki = $k->podatkiKORWiN($k->wydatki);
//$k->netto = 2052;
//$k->netto = 1286.17; // Minimalna krajowa
$k->netto = 1600;
if (isset($_GET['netto']))
	$k->netto = $_GET['netto'];

$k->liczOdNetto();
$k2 = $k->copy();
$k2->podatki = $k2->podatkiKORWiN($k2->wydatki);
$k2->liczOdPrawdziewBrutto();

$k->logo = 'po_logo.jpg';
$k2->logo = 'korwin.jpg';

// Wyswietlanie
///////////////////////////////////////////////////////////
function wyswietl1($k) {
	echo "<table class='ramka'>";
	wiersz("P�aci pracodawca<br/><b>(prawdziwe brutto)</b>", $k->pBrutto);
	wiersz("Koszty pracodawcy ZUS " . rozwiniecie('pracod'), $k->wyniki[0]->suma); // "Koszty pracodawcy "
	echo "<tbody class='rozwiniecie' id='pracod' visibility='hidden'>";
	foreach ($k->wyniki[0]->czastkowe as $nazwa => $wartosc)
		wiersz($nazwa, $wartosc);
	echo "</tbody>";
	wiersz("Brutto", $k->brutto);
	wiersz("Sk�adki na ZUS " . rozwiniecie('pracow'), $k->wyniki[1]->suma); //"Sk�adki po stronie pracownika "
	echo "<tbody class='rozwiniecie' id='pracow' visibility='hidden'>";
	foreach ($k->wyniki[1]->czastkowe as $nazwa => $wartosc)
		wiersz($nazwa, $wartosc);
	echo "</tbody>";
	wiersz("Netto", $k->netto);
	foreach ($k->wyniki[2]->czastkowe as $nazwa => $wartosc)
		wiersz($nazwa, $wartosc);
	echo "<tbody class='naglowek'>";
	wiersz2p("Przyk�adowe wydatki:", "koszt", "w tym<br/>podatki");
	echo "</tbody>";


	foreach ($k->wydatki as $wyd){
		wiersz2($wyd->nazwa, $wyd->kwota, $wyd->kwota - $wyd->kwotaNetto);
	}
	wiersz2("Oszcz�dno�ci", $k->oszczednosci, $k->oszczPod);
	wiersz2("Suma", $k->wydatkiSuma, $k->wydatkiPod);
	wiersz("<b>Prawdziwe netto</b>", $k->pNetto);
	wiersz2p("Suma podatk�w, op�at, itp.", myround(100*(1- $k->pNetto/$k->pBrutto)).'%', "");
	echo "</table>";
}

function wyswietlKilka(array $a) {
	echo "<form method='GET'><table class='ramka'>";
	//Wyswietlanie loga
	echo "<tr><td></td>";
	foreach ($a as $k){
		$l = $k->logo;
		echo "<td colspan = '2'><img src='$l'/></td>";
	}
	echo "</tr>";
	wiersza("P�aci pracodawca<br/><b>(prawdziwe brutto)</b>", $a, 'pBrutto');
	wierszWyniki("Koszty pracodawcy ZUS ", 'pracod', $a, 0);
	wiersza("Brutto", $a, 'brutto');
	wierszWyniki("Sk�adki na ZUS i PIT ", 'pracow', $a, 1);
	wiersza("Netto", $a, 'netto', '', 'z� ', rozwiniecie('netto'));
	echo "<tbody class='rozwiniecie' id='netto'>";
	wierszp("umowa o prac�", "<input type='text' name='netto' value='{$a[0]->netto}'/> z�");
	wierszp("inne umowy", "b�d� wkr�tce :)");
	//wierszp("widok", "<select name='widok'><option value='domyslny'>domy�lny</option><option value='pelny'>pe�ny</option><option value='zwarty'>zwarty</option></select>");
	wierszp("", "<input type='submit'  value='zmie�'/>");
	echo "</tbody>";
	wierszWyniki("","dodatkowe", $a, 2);
	wierszPrzerwa();
	//echo "<tbody class='naglowek'>";
	wiersz2pA("Przyk�adowe wydatki:", "koszt", "w tym<br/>podatki", count($a));
	//echo "</tbody>";

	wierszLinia();
	foreach ($a[0]->wydatki as $key => $wyd){
		wiersz2wydatki($wyd->nazwa, $a, $key);
	}
	
	wiersz2a("Oszcz�dno�ci", $a, 'oszczednosci', 'oszczPod');
	wierszLinia();
	wiersz2a("RAZEM:", $a, 'wydatkiSuma', 'wydatkiPod');
	wierszPrzerwa();
	wiersza("<b>Prawdziwe netto</b>", $a, 'pNetto');
	wiersza("Suma podatk�w, op�at, itp.", $a, 'sumaPodatkow');
	wiersza(" procentowo", $a, 'procentPodatkow', '', '%');
	
	// Wykresy
	echo "<tr><td></td>";
	$i = 0;
	foreach ($a as $k){
		$l = $k->logo;
		$i++;
		echo "<td colspan = '2'><div class='wykres' id='id$i'></div></td>";
		$pr = myround($k->procentPodatkow);
		$gr = $k->grupyProcentowo;
		//$pr = myround($gr['Pa�stwo']);
		if (isset($gr['Korporacje'])) $pr1 = myround($gr['Korporacje']); else $pr1 = 0;
		if (isset($gr['Zmarnowane'])) $pr2 = myround($gr['Zmarnowane']); else $pr2 = 0;
		if (isset($gr['Prywatny ubezpieczyciel'])) $pr3 = myround($gr['Prywatny ubezpieczyciel']); else $pr3 = 0;
		echo "<script> drawChart('id$i', $pr, $pr1, $pr2, $pr3); </script>";
		//$parms = $k->getGoogleChartParams();
		//$echo "<script> drawChart2('id$i', '$parms'); </script>";
	}
	echo "</tr>";
	wiersza0("Miesi�czny zysk", $a, 'zysk');
	wiersza0("Za swoje wynagrodzenie kupisz", $a, 'ilePWiecej', '', '% wi�cej', '', 'o ');
	echo "</table></form>";
	echo "<div class='komentarz' id='komentarz'></div>";
}
wyswietlKilka(array($k, $k2));
//wyswietl1($k);
//wyswietl1($k2);

// 286 roboczogodzin roczie na dope�nienie obowi�zk�w zwi�zanych z podatkami: https://www.facebook.com/czerwonatasma/photos/a.395300367316115.1073741828.370459393133546/435449533301198/?type=1&fref=nf
// 286 rh / 160*12 = 15% czasu przedsiembiorcy
// 2,3 mln aktywnych firm http://www.coig.com.pl/spis-polskich-firm_katalog_polskich_firm.php
// 16 mln zatrudnionych http://rynekpracy.org/x/1002424
// Wed�ug mapy wydatk�w w sektorze publicznym http://www.mapawydatkow.pl/wp-content/plugins/downloads-manager/upload/Mapa%20Zatrudnienia%20w%20Sektorze%20Publicznym%20-%20analiza.pdf
// od 16 mln zatrudnionych nale�y odja� (1,9 mln zatrudnionych w sektorze publicznym - 0,6 edukacja - 0,3 s�u�ba zdrwia)
// Wychodzi nam 15 mln zatrudnionych
// 2,3 / 16 * 15% = 2,15%
?>
