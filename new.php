<html><head><meta http-equiv="Content-type" content="text/html; charset=iso-8859-2" />
<script language="JavaScript">
	function pokarz(text){
		document.getElementById('komentarz').innerHTML = text;
	}
	function pokarze(id){
		document.getElementById('komentarz').innerHTML = document.getElementById(id).innerHTML+"\n\n\n";
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
</style>
</head><body>
<?php
include "kalkulator.php";

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
	echo "<tr><td>$tekst</td><td colspan = \"2\">".myround($kwota)." z�</td></tr>";
}

function wiersz2($tekst, $kwota, $kwota2){
	echo "<tr><td>$tekst</td><td>".myround($kwota)." z�</td><td class='dodatek'><div class='dodatek'>".myround($kwota2)." z�</div></td></tr>";
}
function wiersz2p($tekst, $tekst1, $tekst2){
	echo "<tr><td>$tekst</td><td>$tekst1</td><td class='dodatek'><div class='dodatek'>$tekst2</div></td></tr>";
}




//Inicjowanie 
/////////////////////////////////////////////////////////////
$k = new Kalkulator();
$k->podatki = $k->defaultPodatki($k->wydatki);
//$k->netto = 2052;
$k->netto = 2700;
$k->liczOdNetto();

// Wyswietlanie
///////////////////////////////////////////////////////////

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

// 286 roboczogodzin roczie na dope�nienie obowi�zk�w zwi�zanych z podatkami: https://www.facebook.com/czerwonatasma/photos/a.395300367316115.1073741828.370459393133546/435449533301198/?type=1&fref=nf
// 286 rh / 160*12 = 15% czasu przedsiembiorcy
// 2,3 mln aktywnych firm http://www.coig.com.pl/spis-polskich-firm_katalog_polskich_firm.php
// 16 mln zatrudnionych http://rynekpracy.org/x/1002424
// Wed�ug mapy wydatk�w w sektorze publicznym http://www.mapawydatkow.pl/wp-content/plugins/downloads-manager/upload/Mapa%20Zatrudnienia%20w%20Sektorze%20Publicznym%20-%20analiza.pdf
// od 16 mln zatrudnionych nale�y odja� (1,9 mln zatrudnionych w sektorze publicznym - 0,6 edukacja - 0,3 s�u�ba zdrwia)
// Wychodzi nam 15 mln zatrudnionych
// 2,3 / 16 * 15% = 2,15%
?>
