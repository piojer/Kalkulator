<html><head><meta http-equiv="Content-type" content="text/html; charset=iso-8859-2" />
<script language="JavaScript">
	function pokarz(text){
		document.getElementById('komentarz').innerHTML = text;
	}
	function pokarze(id){
		document.getElementById('komentarz').innerHTML = document.getElementById(id).innerHTML;
	}
</script>
<style>
	td, th {padding:1px 10px}
	td.kwota {width:60}
</style>
</head><body>
<?php

// Parametry
$kocztyNiepracownicze = 40;
$ulamekZatrudnionychUmowaOPrace = 12.5/13.2;
$kocztyBiurokratyczne = 5;
$podNier=0.001;

$wynagrodzenieSpec = 5000;
$wynagrodzeniePomoc = 2500;
$remontySali = 2000;
$zysk = 1.1;
$nauczycielNaIleUczni�w = 15;
$pomocNaIleUczni�w = 50;

$kosztWizytyWszpitalu = 1000;
$czestotliwosc = 3*12;
$kosztWizytyWszpitalu2 = 35000;
$czestotliwosc2 = 50*12;

$doWypisania = '';

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

function myround($a){
	$b = round($a);
	if (abs($b) < 10) {
		
		$poziom = 1;
		do {
			$b = round($a, $poziom);
			$poziom++;
		} while (abs($b) == 0);
		$b = round($a, $poziom);
	}
	//echo "myround($a) = $b<br/>";
	return $b;
}

function wypiszInput($tekst, $pole, $komentarz=''){
	$v= "";
	if (isset($_GET[$pole])) 
		$v = $_GET[$pole];
	echo "<tr><td onmouseover='pokarz(\"$komentarz\")'>$tekst: </td><td><input type='text' name='$pole' value='$v'/></td></tr>";
}



$nrDoWypisz = 1;
function wypisz($tekst, $wartosc, $komentarz=''){
	global $nrDoWypisz, $doWypisania;
	if (is_numeric($wartosc)) {
		$wartosc = round($wartosc, 0);
		$wartosc .= ' z�';
	}
	$mouse = "onmouseover=\"pokarze('w$nrDoWypisz')\"";
	$doWypisania .=  "<tr><td colspan='2' $mouse>$tekst: </td><td $mouse><b>$wartosc</b></td></tr>";
	$doWypisania .= "<td style='visibility:hidden;position:absolute' id='w$nrDoWypisz'>$komentarz</td></tr>";
	$nrDoWypisz++;
}

function jakVat($kwota, $procent, &$tekst, &$za){
	
	$procent += 1;
	$p = myround($procent*100);
	$k = round($kwota, 0);
	
	$tekst = "Czyli kwota $k z� stanowi $p% kwoty, kt�ra by by�a wydawana, gdyby nie by�o tego 'podatku'";
	$za = "%";
	return $kwota/($procent);
}

function procentowo($kwota, $procent, &$tekst, &$za){
	$p = $procent*100;
	$p = round($p, 1);
	$k = round($kwota, 0);
	$kk = round($kwota*$procent, 0);
	if ($procent > 0)
		$tekst = "Czyli od kwoty $k z� odliczamy $p% czyli $kk z�";
	else {
		$p = -$p;
		$kk = -$kk;
		$tekst = "Czyli do kwoty $k z� doliczamy $p% czyli $kk z�";
	}
	$za = "%";
	return $kwota*(1-$procent);
}

function kwotowo($kwota, $procent, &$tekst, &$za){
	$procent *= 10000;
	$k = round($kwota, 0);
	$kk = round($procent, 0);
	if ($procent > 0) {
		$tekst = "Czyli od kwoty $k z� odliczamy $kk z�";
	} else {
		$kk = -$kk;
		$tekst = "Czyli do kwoty $k z� doliczamy $kk z�";
	}
	$za = " z�";
	return $kwota-$procent;
}

$indexObnizenie=1;
$indexPodwyzszenie=1;
function obnizenie($funkcja, $procent, $procentDotyczy, &$kwota, $nazwa, $komentarz='', $pelnaKwota){
	global $indexObnizenie, $indexPodwyzszenie, $doWypisania, $noweKoszty;
	$tekst = '';
	$za = '';
	$k = $funkcja($kwota, $procent*$procentDotyczy/10000, $tekst, $za);
	$r = $kwota-$k;
	$kw = round($kwota, 0);
	$kk = round($k, 0);
	$prC = 100*(1 - $k/$kwota);
	$kwota = $k;
	
	$sumaPod = 1-$kwota/$pelnaKwota;
	$sumaPod*=100;
	$sumaPod = round($sumaPod, 1);
	$prC  = round($prC, 1);
	
	$kom = "<h3>$nazwa</h3><p>$komentarz</p><p>";
	$procent = round($procent, 1);
	
	if ($procent < 0) {
		$procent = -$procent;
		$k = -$k;
		$prC = -$prC;
		if ($indexPodwyzszenie<2) {
			$doWypisania .=  "<tr><th>podwy�szenie cen</th><th>efektywny wzrost</th>";
			$doWypisania .=  "<th>kwota</th><th>Ca�kowity spadek</th>";
			$doWypisania .=  "</tr>";
		}
		$indexPodwyzszenie++;
		$indexObnizenie++;
		$tekstZaw = '�rednio b�dziemy mie� koszty';
	} else {
		if ($indexObnizenie<2) {
			$doWypisania .=  "<tr><th>obni�enie cen</th><th>efektywny spadek</th>";
			$doWypisania .=  "<th>kwota</th><th>Ca�kowity spadek</th>";
			$doWypisania .=  "</tr>";
		}
		$indexObnizenie++;
		$tekstZaw = '�rednio mamy zawy�one koszty o';
	}
	if ($noweKoszty) $tekstZaw = 'Poniesiemy koszty r�wne oko�o';
	
	$p = $procentDotyczy*$procent;
	$procentDotyczy = myround($procentDotyczy);
	
	
	if ($za == ' z�') {
		$procent = round($procent, 0);
	$prC = $p = round($p, 0);
		$kom .= "$tekstZaw $procentDotyczy razy $procent z� czyli <b>$p z�</b>";
	} else {
		if ($procentDotyczy > 99) {
			if (!$noweKoszty)
				$kom .=	"�rednio kupowane przez nas produkty maj� ";
			else 
				$kom .=	"�rednio kupowane przez nas produkty b�d� mie� ";
		} else
			$kom .=	"Oko�o <b>$procentDotyczy%</b> kupowanych przez nas produkt�w ma ";
		$kom .=	"zwi�kszon� cen� o <b>$procent$za</b>";
	}
	$kom .=	"</p><p>$tekst</p>";
	
	$mouse = "onmouseover=\"pokarze('id$indexObnizenie')\"";
	
	
	
	$doWypisania .=  "<tr><td $mouse>- $nazwa: </td><td $mouse>$prC$za</td>";
	$doWypisania .=  "<td $mouse class='kwota'><b>$kk z�</b></td><td $mouse>$sumaPod%</td>";
	$doWypisania .=  "<td style='visibility:hidden;position:absolute' id='id$indexObnizenie'>$kom</td></tr>";
	
	
	return $sumaPod;
}


//if (isset($_GET['netto'])) {
	if ($zwrot == '') $zwrot = 0;
	if ($pomoc == '') $pomoc = 0;
	
	$netto_r = $zwrot/12 + $netto + $pomoc + $prywatna + $nettod;
	
	$brutto = $netto*1.435 - 95;
	$brutto += $nettod*1.19;
	
	
	$noweKoszty = false;
	wypisz("Tw�j przych�d miesi�czny", $netto_r, "Do wynagrodzenia netto dodane s�: pomoc socialna, zwrot i ulgi w podatku");
	wypisz("Twoje wynagrodzenie brutto", $brutto, "Wzi�te z kalkulator�w wynagrodzenia brutto-netto: <br/> netto z um�w o prac� i um�w zlecenie jest mno�one razy 1.435 i odejmowane 95 z�.<br/> netto z umowy o dzie�o jest mno�one razy 1.19");
	
	wypisz("<b>Za Nowej Prawicy</b>", "", "");
	/*
	$podatekNP = $wartosc*$pr_podatek_po;
	$podatekNP2 = $nier*$podNier/12;
	$podatekNP4 = $podatekNP*57964/120832;
	$podatekNP3 = $podatekNP + $podatekNP2 + $podatekNP4;
	
	$podatekNP4 = round($podatekNP4, 0);
	$podatekNP2 = round($podatekNP2, 0);
	$podatekNP = round($podatekNP, 0);
	$przychodNP = $wartosc-$podatekNP3;
	$dofinansowaniaZUE = 82500000 * 4.1538 / 7 / 12 / $dzielnikLudzi;
	$dofinansowaniaZUEef = $dofinansowaniaZUE * $sprawnosc *0.5;
	$nettoNP = 	$przychodNP - $mojSocial - $dofinansowaniaZUEef;
	*/
	
	
	wypisz("Ceny b�d� ni�sze: <b>To co dzi� kupujesz za</b>", $netto_r, "Poni�ej analiza powod�w najbardziej prawdopodobnego spadku cen");
	$kwota = $netto_r;
	$vat23 = 2000/3785 * 100;
	$vat8 = 1785/3785 * 50;
	$vat5 = 1785/3785 * 50;
	obnizenie('jakVat',23, $vat23, &$kwota, 'Vat 23%', 'Dotyczy tylko stawki Vat 23%. <BR/> T� stawk� VAT jest obci�zone '.round($vat23, 1).' % kupowanych przez nas produkt�w (�r�d�o <a href="http://www.ebroker.pl/wiedza/ile-vat-u-jest-w-kajzerce/3535">ebroker.pl</a>)', $netto_r);
	obnizenie('jakVat', 8, $vat8, &$kwota, 'Vat 8%', '(m.in. u�ywana w budownictwie)<BR/> T� stawk� VAT jest obci�zone '.round($vat8, 1).' % kupowanych przez nas produkt�w (�r�d�o <a href="http://www.ebroker.pl/wiedza/ile-vat-u-jest-w-kajzerce/3535">ebroker.pl</a>)', $netto_r);
	obnizenie('jakVat', 5, $vat5, &$kwota, 'Vat 5%', '(g��wnie produkty spo�ywcze)<BR/> T� stawk� VAT jest obci�zone '.round($vat5, 1).' % kupowanych przez nas produkt�w (�r�d�o <a href="http://www.ebroker.pl/wiedza/ile-vat-u-jest-w-kajzerce/3535">ebroker.pl</a>)', $netto_r);
	$vatk = $netto_r - $kwota;
	$vatCPr = ($netto_r/$kwota - 1)*100;
	$p = 50; // Cz�� wp�ywu 
	obnizenie('kwotowo', $vatk, $p/100, &$kwota, 'Akcyzy', "Nie znam dok�adnych kwot i obci��e� procentowych akcyz. ".
	"Poniewa� wp�ywy z akcyz do bud�etu wynosz� oko�o $p % wp�yw�w z VATu, szacuj� �e dla jednej osoby stosunek b�dzie uk�ada� si� podobnie.", $netto_r);
	$przedZerowym = $kwota;
	//obnizenie('jakVat', 1, 100, &$kwota, 'Inflacja', "�rednio inflacja to 2% w skali roku. \n", $netto_r);
	obnizenie('jakVat', 19*0.05, 100, &$kwota, 'Podatek dochodowy', "Podatek dochodowy dla firm zwi�ksza te� ceny produkt�w. Szacuj�, �e �rednio firmy osi�gaj� 5% zysku i z tego zysku odliczamy 19% podatku", $netto_r);
	obnizenie('jakVat', 50, 50, &$kwota, 'Licencje i monopole', "Bardzo du�o produkt�w, kt�re kupujemy jest ograniczonych licencjami, pozwoleniami na produkcj�, oraz pa�stwowymi monopolami. <br\>
	Demonstracj� wzrostu przez to cen widzieli�my latem 2013r. przy tz. 'ustawie �mieciowej'. 
	Wcze�niej firmy wyworz�ce �mieci dogadywa�y si� bezpo�redno z nami, wi�c dba�y o niskie ceny i zadowolenie klienta. 
	Teraz dogaduj� si� raz z urz�dem i maj� monopol na obszarze miasta/gminy. O ile ceny wzros�y? W Katowicach o prawie 100%. <br/>
	Jakie produkty s� ograniczone? <br/>
	- Produkty spo�ywcze (czyli 26% produkt�w jak z VATu), <br/> 
	- Budowlane (trzeba mie� pozwolenia na budow�), <br/>
	- Samochody (dopuszczenie do ruchu), <br/>
	- Media (Pr�d - op�aty za przesy� energii zmonopolizowane, W�giel - kopalnie pa�stwowe, Ropa,Gaz - import wynegocjowany politycznie), <br/>
	- itp. <br/> Szacuj�, �e:
	", $netto_r);
	$pp = (1 - 2000/2369.60)*100;
	$pC = round($pp);
	$pracC = round(100-$kocztyNiepracownicze);
	$uZoPc = round(100*$ulamekZatrudnionychUmowaOPrace);
	obnizenie('procentowo', $pp*$ulamekZatrudnionychUmowaOPrace*(100-$kocztyNiepracownicze)/100, 100, &$kwota, 'Koszty zatrudnienia pracownika', "Szacuj�, ze oko�o $pracC % koszt�w pracodawcy to koszty zwi�zane z wynagrodzeniami (Uwaga: warto�� wyssana z palca: potrzebuj� �r�d�a) Z tego $uZoPc % to umowy o prac� (�r�d�o: <a href='http://www.bankier.pl/wiadomosc/Umowy-smieciowe-to-mit-Pracuje-na-nich-tylko-600-tys-osob-2794604.html' >bankier.pl</a>).<br/>"
	."Oko�o $pC % tego, co wydaja na umowy o prac� to s� r�ne sk�adki na ZUS po stronie pracodawcy (z <a href='http://prawo.rp.pl/temat/846545.html'>kalkulator�w wynagrodzenia dla pracowawcy np. prawo.rp.pl</a>)", $netto_r);
	obnizenie('procentowo', $kocztyBiurokratyczne, 100, &$kwota, 'Koszty ksi�gowe i biurokratyczne', "Po uproszczeniu przepis�w podatkowych spadn� znacznie koszty firm. Wi�kszo�� Przedsi�biorstw nie b�dzie musia�a ponosi� koszt�w ksi�gowych, nie b�dzie obci��ana obowi�zkami sprawozdawczymi itp. Szacuj� �e koszty spadn� o $kocztyBiurokratyczne% (Uwaga: Warto�� t� wzi��em z subiektywnych obserwacji - nie mam �r�d�a)", $netto_r);
	
	
	
	// Sprz�enie zwrotne dodatnie
	$skokSp = $przedZerowym/$kwota-1;
	$skokSp *= 100;
	$pozSp = $kocztyNiepracownicze;
	for ($i = 1; $pozSp > 0.5; $i++){
		
		obnizenie('jakVat', $skokSp, $pozSp, &$kwota, "Sprz�enie $i", "Przedsi�biorstwa te� kupuj� produkty/podzespo�y o zawy�onych cenach. Ceny dla przedsi�biorstw zawy�a : Inflacja, Podatek dochodowy, Licencje i monopole, Koszty zatrudnienia, Koszty ksi�gowe i biurokratyczne firm od kt�rych kupuj� produkty/us�ugi.", $netto_r);
		$pozSp *= $kocztyNiepracownicze/100;
	}
	
	$noweKoszty = true;
	$przedVAT = $kwota;
	obnizenie('procentowo', -15, 100, &$kwota, 'VAT 15% na wszystko', "W Programie Nowej Prawicy planowana jest jedna stawka VAT r�wna minimalnemu podatkowi VAT w Unii Europejskiej.", $netto_r);
	obnizenie('kwotowo',$przedVAT-$kwota , $p/100, &$kwota, 'Akcyzy wymagane przez UE', "Unia Europejska przewiduje minimalne stawki akcyz na wiele produkt�w. Mimo, �e III RP pobiera zwykle wi�ksze akcyzy ni� minimalne, to pesymistycznie zak�adam taki sam stosunek pobieranych podatk�w z VAT i akcyz jak dzisiaj.", $netto_r);
	wypisz("<b>Dobra za ".round($netto_r)." z� za Nowej Prawicy kupisz za</b>", $kwota, "To co dzisiaj kupujesz za ".round($netto_r, 0)." z�. po wprowadzeniu programu Nowej Prawicy kupisz za ".round($kwota. 0)." z�.");
	$spadekCen = $kwota/$netto_r;
	$spadekCenNapis = round($spadekCen*100, 0) .'%';
	wypisz("�rednie ceny w stosunku do dzisiejszych", $spadekCenNapis, "Czyli ceny spadn� drastycznie: Bu�ka kosztuj�ca dzi� 50gr b�dzie kosztowa� zn�w oko�o ".round(50*$spadekCen, 0). "gr"); 
	wypisz("Wynagrodzenie netto b�dzie r�wne temu brutto", $brutto, "Program Nowej Prawicy m.in."
		." przewiduje likwidacj� podatku PIT i sk�adek na ZUS. To w efekcie daje, �e otrzymujesz na r�k� kwot�, kt�r� masz w umowie o prac�"."<br/><br/><font style='font-size:small'>Sk�adki ZUS, itp. po stronie pracodawcy te� zostan� zlikfidowane, lecz je uwzgl�dni�em w obni�onych kosztach pracodawcy i dodaj� swoj� cegie�k� przy obni�ce cen. <br/> Moim zdaniem najbardziej prawdopodonym jest scenariusz, w kt�rym podzia� mi�dzy pracodawc� a pracownikiem zysku ze zniesienia koszt�w zatrudnienia odb�dzie si� w�a�nie na kwocie brutto: takie rozwi�zanie nie wymaga renegocjacji um�w.</font>"); 
	$brutto += $prywatna;
	$przedObn = $brutto;
	wypisz("Tw�j miesi�czny przych�d", $brutto, "Tw�j miesi�czny przych�d to wynagrodzenie brutto, plus pomoc od prywatnych ludzi i instytucji.<br/>Poniewa� og�lnie ludzie b�d� mie� wi�cej pieni�dzy, to pomoc od prywatnych ludzi i instytucji b�dzie co najmniej taka jak dzisiaj.");
	wypisz("<b>Dodatkowe koszty miesi�cznie:</b>", "", "");
	
	obnizenie('kwotowo', $nier/1000, 1/12, &$brutto, "Maksymalny podatek od nieruchomo�ci", "Program Nowej Prawicy proponuje podatek r�wny maksymalnie 1 promilowi warto�ci nieruchomo�ci w skali roku. W�a�ciciel sam okre�la warto�� swojej nieruchomo�ci, lecz mo�e by� ona wykupiona po cenie 2 razy wy�szej ni� zadeklarowana. W tym przeliczeniu podatek dzielimy dodatkowo przez 12 miesi�cy", $przedObn);
	
	$leczenie = ($wynagrodzenieSpec+$wynajemSali)/16/20;
	$leczenieC = $leczenie*$osoby*2;
	obnizenie('kwotowo',$leczenieC , $spadekCen, &$brutto, "Prywatny lekarz", "Z wynagrodzenia trzeba b�dzie op�aci� wizyty u lekarza. Pesymistycznie za��my, �e b�d� one 2 razy w miesi�cu na osob�. Ile b�dzie kosztowa�a wizyta u lekarza? A ile by kosztowa�a dzisiaj, gdyby nie NFZ? Koszty prywatnego lekarza, przyjmuj�cego u siebie w domu: swoje wynagrodzenie: np. $wynagrodzenieSpec  z� + remonty gabinetu i media np.: $remontySali z�/mies. Mo�e przyj�� spokojnie 16 pacjent�w dziennie, czyli 320 miesi�cznie. Wi�c jedna wizyta dzi� mog�aby kosztowa� ".round($leczenie, 1). " z�.<br/><br/>Koszt jednej wizyty mno�ymy razy ilo�� os�b ($osoby) i 2 wizyty w mies�cu (Wychodzi ".round($leczenieC, 0)." z�). Ca�o�� mno�ymy razy �redni poziom cen $spadekCenNapis." , $przedObn);
	
	$szpital = $kosztWizytyWszpitalu/$czestotliwosc + $kosztWizytyWszpitalu2/$czestotliwosc2;
	$szpitalC = $szpital*$osoby;
	obnizenie('kwotowo',$szpitalC , $spadekCen, &$brutto, "Prywatne szpitale", "Z wynagrodzenia trzeba b�dzie te� op�aci� wizyty w szpitalu. S� one zwykle rzadko: raz na kilka lat (np. co 3 lata), ale te� wi��� si� z du�ym wydatkiem (za�u�my, �e �rednio $kosztWizytyWszpitalu z�), dodatkowo raz w �yciu doliczam naprawd� ci�k� chorob�: np. rak $kosztWizytyWszpitalu2 z� (wg. <a href = 'http://www.rynekzdrowia.pl/Finanse-i-zarzadzanie/Ile-kosztuje-leczenie-raka-piersi-w-Polsce,119986,1.html'>rynekzdrowia.pl</a>) . Wed�ug tych szacunk�w koszt miesi�czny w szpitalach na 1 cz�owieka to ".round($szpital, 0)." z� Koszt miesi�czny mno�ymy razy ilo�� os�b ($osoby). Wychodzi ".round($szpitalC, 0)." z�. (Ta kwota jest podobna do koszt�w prywatnego ubezpieczenia zdrowotnego)<br/> Ca�o�� mno�ymy razy �redni poziom cen $spadekCenNapis.",$przedObn);
	
	$kosztSzkoly = $wynagrodzenieSpec/$nauczycielNaIleUczni�w + $wynagrodzeniePomoc/$pomocNaIleUczni�w+690/2; 
	$kosztSzkolyC = $kosztSzkoly*$dzieci*$zysk;
	obnizenie('kwotowo',$kosztSzkolyC , $spadekCen, &$brutto, "Prywatna szko�a","Ile kosztuje prywatna szko�a? Wed�ug artyku�u na <a href = 'http://wyborcza.biz/biznes/1,101562,12497582,Ile_to_kosztuje_i_dlaczego_tak_drogo__Czesne_w_szkole.html'>http://wyborcza.biz</a> koszty wynajmu sali i inwestycji i remont�w wynosz� na jednego ucznia 50% ze �redniej kwoty 690z�. Co z pensjami? Jeden nauczyciel mo�e przypada� na oko�o $nauczycielNaIleUczni�w uczni�w (Zwykle w klasie jest powy�ej 25 uczni�w, ale nauczyciele maj� okienka i przygowowuj� sie do lekcji), ponadto jeszcze kto� do sprz�tania  (Dzisiaj dyrektor, wicedyrektor, sekretarka zajmuj� si� prawie wy��cznie kontaktami z urz�dnikami, a nauczyciele 1/3 czasu zu�ywaj� na wype�nianie \"papierk�w\") Podsumowuj�c 50% z 690 + 1 etat nauczycielski $wynagrodzenieSpecna na $nauczycielNaIleUczni�w uczni�w + 1 etat pomocniczy na $pomocNaIleUczni�w uczni�w daje to ".round($kosztSzkoly, 0)." z� na jednego ucznia. Oczywi�ciwe 'wstr�tny kapitalista' we�mie zysk (np. 10%) to daje w sumie ".round($kosztSzkolyC, 0)." z� na $dzieci dzieci<br/> Ca�o�� mno�ymy razy �redni poziom cen $spadekCenNapis.",$przedObn);
	obnizenie('kwotowo', 390, -$dzieci, &$brutto, "Bon edukacyjny", "W programie Kongresu Nowej Prawicy znajduje si� bon edukacyjny. Nie s� sprecyzowane szczeg�y, ale mog� si� domy�la�, �e najpro�ciej to zrobi� jako przelew pieni�dzy na konto rodzic�w w zamian za zobowi�zanie si� wyedukowania dziecka do poziomu gwarantowanego przez konstucj�. Jak sprawdza�, czy rodzice si� wywi�zali? Pozwy s�dowe doros�ych dzieci na z�ych rodzic�w? Zachowa� egzaminy gimnazjalne? \nInne rozwi�zania bonu edukacyjnego - np. karty, gdzie kwota idze bezpo�rednio do szko�y, pozostawiaj� problemy w postaci: co to jest szko�a? Kto b�dzie te szko�y kontrolowa�, czy faktycznie ucz�, czy tylko �ci�gaj� kas�?. My�l�, �e kontrol� zostawi� rodzicom. A kontrol�, czy rodzice wywi�zuj� si� z obowi�zku edukacji dzieci, mo�e by� gro�ba kary, gdy przed s�dem kto� udowodni, �e nie uczyli oni swych dzieci.\n\n Jaka b�dzie wysoko�� bonu edukacyjnego? My�l�, �e r�wna cenom najta�szych szk�. Tutaj za�o�y�em, �e b�dzie r�wna kosztom tych zwyk�ych najta�szych szk� (np. 390 z�/mies.)\n\n\n",$przedObn);
	wypisz('<b>Podsumowanie</b>', "", "");
	$zysk = $brutto - $kwota;
	$zyskR = round($zysk);
	wypisz('<b>Tw�j miesi�czny zysk</b>', "<b>$zyskR z�</b>", "Za Nowej Prawicy zostanie Ci miesi�cznie $zyskR z� po odliczeniu koszt�w tego co dzi� kupujesz za swoje wynagrodzenie. Z kwoty ".round($brutto)." z� na '�ycie' odliczam kwot� miesi�cznych wydatk�w (".round($netto_r)." z�) obnizon� o �rednie zawy�enie dzisiejszych cen $spadekCenNapis, czyli ".round($kwota)." z�. ");
	$zyskNaCeny = $zysk / $spadekCen;
	$zyskNaCenyR = round($zyskNaCeny);
	wypisz("<b><font style='font-size:18'>Tw�j miesi�czny zysk na dzisiejsze ceny</font></b>", "<b><font style='font-size:20'>$zyskNaCenyR z�</font></b>", 
	"Kwota $zyskR z� b�dzie warta wi�cej, ni� dzisiaj, gdy� �redno ceny b�d� mia�y warto�� $spadekCenNapis dzisiejszych. <br/> Aby uzyska� prawdziw� warto�� tych pieni�dzy dziel� je przez $spadekCenNapis. <br/><br/><b>Za $zyskR z� b�dzie mo�na kupi� tyle d�br, ile dzi� za $zyskNaCenyR z�.</b>");
	
	echo "<H3 style='text-align:center;background-color:yellow'> Wersja testowa. Prosz� o komentarze: piotr.jerzykowski@gmail.com </H3>";
	echo "<H1 style='text-align:center'> Kalkulator wynagrodze� po obni�ce podatk�w  </H1>";
	echo "<table>";
	echo "<tr><td width='50%' rowspan='1'>";
	$n = $netto + $nettod;
	echo "<font style='font-size:24'>Je�li zarabiasz netto $n z�</font><font style='font-size:24'> i masz rodzin� $dorosli+$dzieci </font><br/><font style='font-size:30'>to oferuj� Ci ";
	if ($zyskNaCenyR >= 0) echo " podwy�k� <b>+$zyskNaCenyR z�</b></font>";
	else echo " obni�k� <b>$zyskNaCenyR z�</b></font>";
	
	echo "<br/><font style='font-size:20'> z samej tylko obni�ki podatk�w i deregulacji zawod�w, czyli: jedna stawka VAT 15%, usuni�cie PIT i CIT wg. <a href='http://www.nowaprawicajkm.pl/info/program-wyborczy/program-kongresu-nowej-prawicy/item/program-kongresu-nowej-prawicy'>programu gospodarczego Kongresu Nowej Prawicy</a></font>";
	
	$os = round($n / ($dorosli+$dzieci));
	if ($os < 350)
		echo "<div style='color:red'>Jak Ci si� udaje wy�y� za $os z�/osob�?</div>";
		
	
	//echo "<br/><font style='font-size:20'> z samej tylko obni�ki podatk�w i deregulacji zawod�w, czyli po wprowadzeniu <a href='http://www.nowaprawicajkm.pl/info/program-wyborczy/program-kongresu-nowej-prawicy/item/program-kongresu-nowej-prawicy'>programu gospodarczego Kongresu Nowej Prawicy</a></font>"; 
	
	echo "<br/>(Powyzsza kwota zosta�a przeliczona na dzisiejsz� warto�� z�ot�wki. Wyja�nienie poni�ej)";
	
	echo "</td><td rowspan='2' style='vertical-align:top;'>";
	wypiszInputy();
	echo "</td></tr>"; //<tr></tr>
	echo "<tr><td rowspan='2'>";
	echo "Uzasadnienie:<table style='border-style:outset'><tr style='border-bottom-style:solid;'><th colspan='2'>TERAZ</th></tr>"; //
	echo $doWypisania;
	echo "</table>";
	echo "</td></tr><tr><td width='50%' style='vertical-align: middle;'><div id='komentarz' style='vertical-align:middle'></div></td></tr>";
	//echo "<tr><td>";
	//echo "</td></tr>";
	echo "</table>";
	echo "<br/>Dotyczy os�b rzetelnie wykonuj�cych potrzebn� prac�. 
		(Osoby zatrudnione dla cel�w biurokracji mog� dosta� takie wynagrodzenie po przekwalifikowaniu si�)<br/>
		Obliczenia s� szacunkowe i nikt nie wie dok�adnie o ile przedsi�biorcy obni�� cen�, a o ile podwy�sz� wynagrodzenia. 
		(Mo�e si� zdarzy� �e zamiast wyp�aty " . round($przedObn) . " z� zobaczysz j� np. dwa razy wy�sz� lub ni�sz�, lecz ceny te� zmieni� si� proporcjonalnie)";
	echo "<br/><b>Kalkulator prezentuje wyniki moich przelicze� i oszacowa�. Nie zosta� jeszcze sprawdzony przez KNP</b>";
	echo "<br/></br/><b>Bardzo prosz� o komentarze: piotr.jerzykowski@gmail.com</b>";
	echo "<h3> Uwaga ! </h3> Potrzebuj� �r�de� i dok�adniejszych warto�ci poni�szych parametr�w:<br/><lu>";
	echo "<li><b>�redni procent koszt�w zatrudnienia pracownik�w w stosunku do wszystkich koszt�w firmy (Za�o�y�em, �e 60%)</b></li>";
	echo "<li><b>�redni procent koszt�w na biurokracj� (ksi�gowe, kontakty z ksi�gowo�ci�, papiery do urzed�w itp. - Za�o�y�em, �e jest to 5%)</b></li>";
	echo "<li><b>Oszacowanie, ile �rednio kupujemy produkt�w ograniczonych licencjami, pozwoleniami, sztucznymi monpolami (oligopolami) (Za�o�y�em, �e po�ow� miesi�cznych wydatk�w wydajemy na takie produkty, kt�rych ceny s� przez to zawy�one �rednio o 50%)</b></li>";
	echo "<li>Koszty leczenia w prywatnych szpitalach (Za�o�y�em, �e jest 1000z� co 3 lata + 35 tys.z� raz na 50 lat)</li>";
	echo "<li>Ile przypada�oby uczni�w na 1 nauczyciela (Gdyby nauczyciel nie marnotrawi� 1/3 czasu na papierkologi� - za�o�y�em, �e 15)</li>";
	
	echo "Pozosta�e parametry:<br/>";
	echo "<li>Zatrudnienie specialisty 5000 z�/mies</li>";
	echo "<li>Zatrudnienie sekretarki/wo�nego 2500 z�/mies</li>";
	echo "</lu> Jak widzicie potrzebuj� r�wnie� grafika :)";
	
//} //*/	
?>
