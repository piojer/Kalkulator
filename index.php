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
$nauczycielNaIleUczniów = 15;
$pomocNaIleUczniów = 50;

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
	$tytul = "Mediana wynagrodzeń";
	$podTytul = "(Na utrzymaniu rodzina 2+2)";
} else if (isset($_GET['srednia'])){
	$tytul = "Średnia krajowa";
	$podTytul = "(Na utrzymaniu rodzina 2+2)";
	$_GET = Array('netto' => 2730, 'nier' => 300000, 'zwrot' => 1000, 'dorosli' => 2, 'dzieci' => 2, 'pomoc' => 0, 'prywatna'=>0);
} else if (isset($_GET['dominanta'])){
	$tytul = "Najczęstrze wynagrodzenie";
	$podTytul = "(Na utrzymaniu rodzina 2+1)";
	$_GET = Array('netto' => 1600, 'nier' => 200000, 'zwrot' => 1000, 'dorosli' => 2, 'dzieci' => 1, 'pomoc' => 106, 'prywatna'=>0);
} else if (isset($_GET['niep'])){
	$_GET = Array('netto' => 1600, 'nier' => 200000, 'zwrot' => 1000, 'dorosli' => 2, 'dzieci' => 1, 'pomoc' => 1186, 'prywatna'=>0);
} else if (isset($_GET['wielodzietna'])){
	$tytul = "Najczęstrze wynagrodzenie";
	$podTytul = "(Na utrzymaniu rodzina 2+1)";
	$_GET = Array('netto' => 1600+1237, 'nier' => 200000, 'zwrot' => 1000, 'dorosli' => 2, 'dzieci' => 4, 'pomoc' => (106*4+80*2), 'prywatna'=>0);
}
extract($_GET);
$osoby = $dorosli + $dzieci;

function wypiszInputy(){
	echo "<form method='GET'><table>";
	wypiszInput('Zarobki miesieczne netto na etacie i zleceniu całej rodziny', 'netto', '');
	wypiszInput('Zarobki miesieczne netto na umowie o dzieło', 'nettod', '');
	wypiszInput('Szacunkowa wartość Twoich nieruchomości', 'nier', '');
	wypiszInput('Zwrot podatku w tym roku', 'zwrot', 'z minusem, jesli dopłata');
	wypiszInput('Pomoc socialna państwowa, zasiłki i ulgi podatkowe', 'pomoc', 'i ulgi podatkowe miesiecznie');
	wypiszInput('Pomoc z fundacji/rodziny', 'prywatna', '');
	wypiszInput('Ilość dorosłych w rodzinie', 'dorosli', 'łącznie z Tobą');
	wypiszInput('Ilość dzieci w rodzinie', 'dzieci', '');
	echo "</table><table><tr><td style='height:100%'><input type='submit' value='przelicz' style='height:100%;font-size:18px;padding:30px'>";
	$f = "style='font-size:12px'";
	echo "</td><td $f>zapisane: <input type='submit' value='minimalna krajowa' name='minimalna' $f>";
	echo "<input type='submit' value='najczęstsze (dominanta)' name='dominanta' $f>";
	echo "<input type='submit' value='mediana wynagrodzeń' name='mediana' $f>";
	echo "<input type='submit' value='średnia' name='srednia' $f>";
	echo "<br/><input type='submit' name='niep' value='teraz na czasie: rodzina z niepełnosprawnym dzieckiem' $f>";
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
		$wartosc .= ' zł';
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
	
	$tekst = "Czyli kwota $k zł stanowi $p% kwoty, która by była wydawana, gdyby nie było tego 'podatku'";
	$za = "%";
	return $kwota/($procent);
}

function procentowo($kwota, $procent, &$tekst, &$za){
	$p = $procent*100;
	$p = round($p, 1);
	$k = round($kwota, 0);
	$kk = round($kwota*$procent, 0);
	if ($procent > 0)
		$tekst = "Czyli od kwoty $k zł odliczamy $p% czyli $kk zł";
	else {
		$p = -$p;
		$kk = -$kk;
		$tekst = "Czyli do kwoty $k zł doliczamy $p% czyli $kk zł";
	}
	$za = "%";
	return $kwota*(1-$procent);
}

function kwotowo($kwota, $procent, &$tekst, &$za){
	$procent *= 10000;
	$k = round($kwota, 0);
	$kk = round($procent, 0);
	if ($procent > 0) {
		$tekst = "Czyli od kwoty $k zł odliczamy $kk zł";
	} else {
		$kk = -$kk;
		$tekst = "Czyli do kwoty $k zł doliczamy $kk zł";
	}
	$za = " zł";
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
			$doWypisania .=  "<tr><th>podwyższenie cen</th><th>efektywny wzrost</th>";
			$doWypisania .=  "<th>kwota</th><th>Całkowity spadek</th>";
			$doWypisania .=  "</tr>";
		}
		$indexPodwyzszenie++;
		$indexObnizenie++;
		$tekstZaw = 'Średnio będziemy mieć koszty';
	} else {
		if ($indexObnizenie<2) {
			$doWypisania .=  "<tr><th>obniżenie cen</th><th>efektywny spadek</th>";
			$doWypisania .=  "<th>kwota</th><th>Całkowity spadek</th>";
			$doWypisania .=  "</tr>";
		}
		$indexObnizenie++;
		$tekstZaw = 'Średnio mamy zawyżone koszty o';
	}
	if ($noweKoszty) $tekstZaw = 'Poniesiemy koszty równe około';
	
	$p = $procentDotyczy*$procent;
	$procentDotyczy = myround($procentDotyczy);
	
	
	if ($za == ' zł') {
		$procent = round($procent, 0);
	$prC = $p = round($p, 0);
		$kom .= "$tekstZaw $procentDotyczy razy $procent zł czyli <b>$p zł</b>";
	} else {
		if ($procentDotyczy > 99) {
			if (!$noweKoszty)
				$kom .=	"Średnio kupowane przez nas produkty mają ";
			else 
				$kom .=	"Średnio kupowane przez nas produkty będą mieć ";
		} else
			$kom .=	"Około <b>$procentDotyczy%</b> kupowanych przez nas produktów ma ";
		$kom .=	"zwiększoną cenę o <b>$procent$za</b>";
	}
	$kom .=	"</p><p>$tekst</p>";
	
	$mouse = "onmouseover=\"pokarze('id$indexObnizenie')\"";
	
	
	
	$doWypisania .=  "<tr><td $mouse>- $nazwa: </td><td $mouse>$prC$za</td>";
	$doWypisania .=  "<td $mouse class='kwota'><b>$kk zł</b></td><td $mouse>$sumaPod%</td>";
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
	wypisz("Twój przychód miesięczny", $netto_r, "Do wynagrodzenia netto dodane są: pomoc socialna, zwrot i ulgi w podatku");
	wypisz("Twoje wynagrodzenie brutto", $brutto, "Wzięte z kalkulatorów wynagrodzenia brutto-netto: <br/> netto z umów o pracę i umów zlecenie jest mnożone razy 1.435 i odejmowane 95 zł.<br/> netto z umowy o dzieło jest mnożone razy 1.19");
	
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
	
	
	wypisz("Ceny będą niższe: <b>To co dziś kupujesz za</b>", $netto_r, "Poniżej analiza powodów najbardziej prawdopodobnego spadku cen");
	$kwota = $netto_r;
	$vat23 = 2000/3785 * 100;
	$vat8 = 1785/3785 * 50;
	$vat5 = 1785/3785 * 50;
	obnizenie('jakVat',23, $vat23, &$kwota, 'Vat 23%', 'Dotyczy tylko stawki Vat 23%. <BR/> Tą stawką VAT jest obciązone '.round($vat23, 1).' % kupowanych przez nas produktów (źródło <a href="http://www.ebroker.pl/wiedza/ile-vat-u-jest-w-kajzerce/3535">ebroker.pl</a>)', $netto_r);
	obnizenie('jakVat', 8, $vat8, &$kwota, 'Vat 8%', '(m.in. używana w budownictwie)<BR/> Tą stawką VAT jest obciązone '.round($vat8, 1).' % kupowanych przez nas produktów (źródło <a href="http://www.ebroker.pl/wiedza/ile-vat-u-jest-w-kajzerce/3535">ebroker.pl</a>)', $netto_r);
	obnizenie('jakVat', 5, $vat5, &$kwota, 'Vat 5%', '(głównie produkty spożywcze)<BR/> Tą stawką VAT jest obciązone '.round($vat5, 1).' % kupowanych przez nas produktów (źródło <a href="http://www.ebroker.pl/wiedza/ile-vat-u-jest-w-kajzerce/3535">ebroker.pl</a>)', $netto_r);
	$vatk = $netto_r - $kwota;
	$vatCPr = ($netto_r/$kwota - 1)*100;
	$p = 50; // Część wpływu 
	obnizenie('kwotowo', $vatk, $p/100, &$kwota, 'Akcyzy', "Nie znam dokładnych kwot i obciążeń procentowych akcyz. ".
	"Ponieważ wpływy z akcyz do budżetu wynoszą około $p % wpływów z VATu, szacuję że dla jednej osoby stosunek będzie układał się podobnie.", $netto_r);
	$przedZerowym = $kwota;
	//obnizenie('jakVat', 1, 100, &$kwota, 'Inflacja', "Średnio inflacja to 2% w skali roku. \n", $netto_r);
	obnizenie('jakVat', 19*0.05, 100, &$kwota, 'Podatek dochodowy', "Podatek dochodowy dla firm zwiększa też ceny produktów. Szacuję, że średnio firmy osiągają 5% zysku i z tego zysku odliczamy 19% podatku", $netto_r);
	obnizenie('jakVat', 50, 50, &$kwota, 'Licencje i monopole', "Bardzo dużo produktów, które kupujemy jest ograniczonych licencjami, pozwoleniami na produkcję, oraz państwowymi monopolami. <br\>
	Demonstrację wzrostu przez to cen widzieliśmy latem 2013r. przy tz. 'ustawie śmieciowej'. 
	Wcześniej firmy wyworzące śmieci dogadywały się bezpośredno z nami, więc dbały o niskie ceny i zadowolenie klienta. 
	Teraz dogadują się raz z urzędem i mają monopol na obszarze miasta/gminy. O ile ceny wzrosły? W Katowicach o prawie 100%. <br/>
	Jakie produkty są ograniczone? <br/>
	- Produkty spożywcze (czyli 26% produktów jak z VATu), <br/> 
	- Budowlane (trzeba mieć pozwolenia na budowę), <br/>
	- Samochody (dopuszczenie do ruchu), <br/>
	- Media (Prąd - opłaty za przesył energii zmonopolizowane, Węgiel - kopalnie państwowe, Ropa,Gaz - import wynegocjowany politycznie), <br/>
	- itp. <br/> Szacuję, że:
	", $netto_r);
	$pp = (1 - 2000/2369.60)*100;
	$pC = round($pp);
	$pracC = round(100-$kocztyNiepracownicze);
	$uZoPc = round(100*$ulamekZatrudnionychUmowaOPrace);
	obnizenie('procentowo', $pp*$ulamekZatrudnionychUmowaOPrace*(100-$kocztyNiepracownicze)/100, 100, &$kwota, 'Koszty zatrudnienia pracownika', "Szacuję, ze około $pracC % kosztów pracodawcy to koszty związane z wynagrodzeniami (Uwaga: wartość wyssana z palca: potrzebuję źródła) Z tego $uZoPc % to umowy o pracę (źródło: <a href='http://www.bankier.pl/wiadomosc/Umowy-smieciowe-to-mit-Pracuje-na-nich-tylko-600-tys-osob-2794604.html' >bankier.pl</a>).<br/>"
	."Około $pC % tego, co wydaja na umowy o pracę to są różne składki na ZUS po stronie pracodawcy (z <a href='http://prawo.rp.pl/temat/846545.html'>kalkulatorów wynagrodzenia dla pracowawcy np. prawo.rp.pl</a>)", $netto_r);
	obnizenie('procentowo', $kocztyBiurokratyczne, 100, &$kwota, 'Koszty księgowe i biurokratyczne', "Po uproszczeniu przepisów podatkowych spadną znacznie koszty firm. Większość Przedsiębiorstw nie będzie musiała ponosić kosztów księgowych, nie będzie obciążana obowiązkami sprawozdawczymi itp. Szacuję że koszty spadną o $kocztyBiurokratyczne% (Uwaga: Wartość tą wziąłem z subiektywnych obserwacji - nie mam źródła)", $netto_r);
	
	
	
	// Sprzężenie zwrotne dodatnie
	$skokSp = $przedZerowym/$kwota-1;
	$skokSp *= 100;
	$pozSp = $kocztyNiepracownicze;
	for ($i = 1; $pozSp > 0.5; $i++){
		
		obnizenie('jakVat', $skokSp, $pozSp, &$kwota, "Sprzężenie $i", "Przedsiębiorstwa też kupują produkty/podzespoły o zawyżonych cenach. Ceny dla przedsiębiorstw zawyża : Inflacja, Podatek dochodowy, Licencje i monopole, Koszty zatrudnienia, Koszty księgowe i biurokratyczne firm od których kupują produkty/usługi.", $netto_r);
		$pozSp *= $kocztyNiepracownicze/100;
	}
	
	$noweKoszty = true;
	$przedVAT = $kwota;
	obnizenie('procentowo', -15, 100, &$kwota, 'VAT 15% na wszystko', "W Programie Nowej Prawicy planowana jest jedna stawka VAT równa minimalnemu podatkowi VAT w Unii Europejskiej.", $netto_r);
	obnizenie('kwotowo',$przedVAT-$kwota , $p/100, &$kwota, 'Akcyzy wymagane przez UE', "Unia Europejska przewiduje minimalne stawki akcyz na wiele produktów. Mimo, że III RP pobiera zwykle większe akcyzy niż minimalne, to pesymistycznie zakładam taki sam stosunek pobieranych podatków z VAT i akcyz jak dzisiaj.", $netto_r);
	wypisz("<b>Dobra za ".round($netto_r)." zł za Nowej Prawicy kupisz za</b>", $kwota, "To co dzisiaj kupujesz za ".round($netto_r, 0)." zł. po wprowadzeniu programu Nowej Prawicy kupisz za ".round($kwota. 0)." zł.");
	$spadekCen = $kwota/$netto_r;
	$spadekCenNapis = round($spadekCen*100, 0) .'%';
	wypisz("Średnie ceny w stosunku do dzisiejszych", $spadekCenNapis, "Czyli ceny spadną drastycznie: Bułka kosztująca dziś 50gr będzie kosztować znów około ".round(50*$spadekCen, 0). "gr"); 
	wypisz("Wynagrodzenie netto będzie równe temu brutto", $brutto, "Program Nowej Prawicy m.in."
		." przewiduje likwidację podatku PIT i składek na ZUS. To w efekcie daje, że otrzymujesz na rękę kwotę, którą masz w umowie o pracę"."<br/><br/><font style='font-size:small'>Składki ZUS, itp. po stronie pracodawcy też zostaną zlikfidowane, lecz je uwzględniłem w obniżonych kosztach pracodawcy i dodają swoją cegiełkę przy obniżce cen. <br/> Moim zdaniem najbardziej prawdopodonym jest scenariusz, w którym podział między pracodawcą a pracownikiem zysku ze zniesienia kosztów zatrudnienia odbędzie się właśnie na kwocie brutto: takie rozwiązanie nie wymaga renegocjacji umów.</font>"); 
	$brutto += $prywatna;
	$przedObn = $brutto;
	wypisz("Twój miesięczny przychód", $brutto, "Twój miesięczny przychód to wynagrodzenie brutto, plus pomoc od prywatnych ludzi i instytucji.<br/>Ponieważ ogólnie ludzie będą mieć więcej pieniędzy, to pomoc od prywatnych ludzi i instytucji będzie co najmniej taka jak dzisiaj.");
	wypisz("<b>Dodatkowe koszty miesięcznie:</b>", "", "");
	
	obnizenie('kwotowo', $nier/1000, 1/12, &$brutto, "Maksymalny podatek od nieruchomości", "Program Nowej Prawicy proponuje podatek równy maksymalnie 1 promilowi wartości nieruchomości w skali roku. Właściciel sam określa wartość swojej nieruchomości, lecz może być ona wykupiona po cenie 2 razy wyższej niż zadeklarowana. W tym przeliczeniu podatek dzielimy dodatkowo przez 12 miesięcy", $przedObn);
	
	$leczenie = ($wynagrodzenieSpec+$wynajemSali)/16/20;
	$leczenieC = $leczenie*$osoby*2;
	obnizenie('kwotowo',$leczenieC , $spadekCen, &$brutto, "Prywatny lekarz", "Z wynagrodzenia trzeba będzie opłacić wizyty u lekarza. Pesymistycznie załóżmy, że będą one 2 razy w miesiącu na osobę. Ile będzie kosztowała wizyta u lekarza? A ile by kosztowała dzisiaj, gdyby nie NFZ? Koszty prywatnego lekarza, przyjmującego u siebie w domu: swoje wynagrodzenie: np. $wynagrodzenieSpec  zł + remonty gabinetu i media np.: $remontySali zł/mies. Może przyjąć spokojnie 16 pacjentów dziennie, czyli 320 miesięcznie. Więc jedna wizyta dziś mogłaby kosztować ".round($leczenie, 1). " zł.<br/><br/>Koszt jednej wizyty mnożymy razy ilość osób ($osoby) i 2 wizyty w miesącu (Wychodzi ".round($leczenieC, 0)." zł). Całość mnożymy razy średni poziom cen $spadekCenNapis." , $przedObn);
	
	$szpital = $kosztWizytyWszpitalu/$czestotliwosc + $kosztWizytyWszpitalu2/$czestotliwosc2;
	$szpitalC = $szpital*$osoby;
	obnizenie('kwotowo',$szpitalC , $spadekCen, &$brutto, "Prywatne szpitale", "Z wynagrodzenia trzeba będzie też opłacić wizyty w szpitalu. Są one zwykle rzadko: raz na kilka lat (np. co 3 lata), ale też wiążą się z dużym wydatkiem (załużmy, że średnio $kosztWizytyWszpitalu zł), dodatkowo raz w życiu doliczam naprawdę ciężką chorobę: np. rak $kosztWizytyWszpitalu2 zł (wg. <a href = 'http://www.rynekzdrowia.pl/Finanse-i-zarzadzanie/Ile-kosztuje-leczenie-raka-piersi-w-Polsce,119986,1.html'>rynekzdrowia.pl</a>) . Według tych szacunków koszt miesięczny w szpitalach na 1 człowieka to ".round($szpital, 0)." zł Koszt miesięczny mnożymy razy ilość osób ($osoby). Wychodzi ".round($szpitalC, 0)." zł. (Ta kwota jest podobna do kosztów prywatnego ubezpieczenia zdrowotnego)<br/> Całość mnożymy razy średni poziom cen $spadekCenNapis.",$przedObn);
	
	$kosztSzkoly = $wynagrodzenieSpec/$nauczycielNaIleUczniów + $wynagrodzeniePomoc/$pomocNaIleUczniów+690/2; 
	$kosztSzkolyC = $kosztSzkoly*$dzieci*$zysk;
	obnizenie('kwotowo',$kosztSzkolyC , $spadekCen, &$brutto, "Prywatna szkoła","Ile kosztuje prywatna szkoła? Według artykułu na <a href = 'http://wyborcza.biz/biznes/1,101562,12497582,Ile_to_kosztuje_i_dlaczego_tak_drogo__Czesne_w_szkole.html'>http://wyborcza.biz</a> koszty wynajmu sali i inwestycji i remontów wynoszą na jednego ucznia 50% ze średniej kwoty 690zł. Co z pensjami? Jeden nauczyciel może przypadać na około $nauczycielNaIleUczniów uczniów (Zwykle w klasie jest powyżej 25 uczniów, ale nauczyciele mają okienka i przygowowują sie do lekcji), ponadto jeszcze ktoś do sprzątania  (Dzisiaj dyrektor, wicedyrektor, sekretarka zajmują się prawie wyłącznie kontaktami z urzędnikami, a nauczyciele 1/3 czasu zużywają na wypełnianie \"papierków\") Podsumowując 50% z 690 + 1 etat nauczycielski $wynagrodzenieSpecna na $nauczycielNaIleUczniów uczniów + 1 etat pomocniczy na $pomocNaIleUczniów uczniów daje to ".round($kosztSzkoly, 0)." zł na jednego ucznia. Oczywiściwe 'wstrętny kapitalista' weźmie zysk (np. 10%) to daje w sumie ".round($kosztSzkolyC, 0)." zł na $dzieci dzieci<br/> Całość mnożymy razy średni poziom cen $spadekCenNapis.",$przedObn);
	obnizenie('kwotowo', 390, -$dzieci, &$brutto, "Bon edukacyjny", "W programie Kongresu Nowej Prawicy znajduje się bon edukacyjny. Nie są sprecyzowane szczegóły, ale mogę się domyślać, że najprościej to zrobić jako przelew pieniędzy na konto rodziców w zamian za zobowiązanie się wyedukowania dziecka do poziomu gwarantowanego przez konstucję. Jak sprawdzać, czy rodzice się wywiązali? Pozwy sądowe dorosłych dzieci na złych rodziców? Zachować egzaminy gimnazjalne? \nInne rozwiązania bonu edukacyjnego - np. karty, gdzie kwota idze bezpośrednio do szkoły, pozostawiają problemy w postaci: co to jest szkoła? Kto będzie te szkoły kontrolować, czy faktycznie uczą, czy tylko ściągają kasę?. Myślę, że kontrolę zostawić rodzicom. A kontrolą, czy rodzice wywiązują się z obowiązku edukacji dzieci, może być groźba kary, gdy przed sądem ktoś udowodni, że nie uczyli oni swych dzieci.\n\n Jaka będzie wysokość bonu edukacyjnego? Myślę, że równa cenom najtańszych szkół. Tutaj założyłem, że będzie równa kosztom tych zwykłych najtańszych szkół (np. 390 zł/mies.)\n\n\n",$przedObn);
	wypisz('<b>Podsumowanie</b>', "", "");
	$zysk = $brutto - $kwota;
	$zyskR = round($zysk);
	wypisz('<b>Twój miesięczny zysk</b>', "<b>$zyskR zł</b>", "Za Nowej Prawicy zostanie Ci miesięcznie $zyskR zł po odliczeniu kosztów tego co dziś kupujesz za swoje wynagrodzenie. Z kwoty ".round($brutto)." zł na 'życie' odliczam kwotę miesięcznych wydatków (".round($netto_r)." zł) obnizoną o średnie zawyżenie dzisiejszych cen $spadekCenNapis, czyli ".round($kwota)." zł. ");
	$zyskNaCeny = $zysk / $spadekCen;
	$zyskNaCenyR = round($zyskNaCeny);
	wypisz("<b><font style='font-size:18'>Twój miesięczny zysk na dzisiejsze ceny</font></b>", "<b><font style='font-size:20'>$zyskNaCenyR zł</font></b>", 
	"Kwota $zyskR zł będzie warta więcej, niż dzisiaj, gdyż średno ceny będą miały wartość $spadekCenNapis dzisiejszych. <br/> Aby uzyskać prawdziwą wartość tych pieniędzy dzielę je przez $spadekCenNapis. <br/><br/><b>Za $zyskR zł będzie można kupić tyle dóbr, ile dziś za $zyskNaCenyR zł.</b>");
	
	echo "<H3 style='text-align:center;background-color:yellow'> Wersja testowa. Proszę o komentarze: piotr.jerzykowski@gmail.com </H3>";
	echo "<H1 style='text-align:center'> Kalkulator wynagrodzeń po obniżce podatków  </H1>";
	echo "<table>";
	echo "<tr><td width='50%' rowspan='1'>";
	$n = $netto + $nettod;
	echo "<font style='font-size:24'>Jeśli zarabiasz netto $n zł</font><font style='font-size:24'> i masz rodzinę $dorosli+$dzieci </font><br/><font style='font-size:30'>to oferuję Ci ";
	if ($zyskNaCenyR >= 0) echo " podwyżkę <b>+$zyskNaCenyR zł</b></font>";
	else echo " obniżkę <b>$zyskNaCenyR zł</b></font>";
	
	echo "<br/><font style='font-size:20'> z samej tylko obniżki podatków i deregulacji zawodów, czyli: jedna stawka VAT 15%, usunięcie PIT i CIT wg. <a href='http://www.nowaprawicajkm.pl/info/program-wyborczy/program-kongresu-nowej-prawicy/item/program-kongresu-nowej-prawicy'>programu gospodarczego Kongresu Nowej Prawicy</a></font>";
	
	$os = round($n / ($dorosli+$dzieci));
	if ($os < 350)
		echo "<div style='color:red'>Jak Ci się udaje wyżyć za $os zł/osobę?</div>";
		
	
	//echo "<br/><font style='font-size:20'> z samej tylko obniżki podatków i deregulacji zawodów, czyli po wprowadzeniu <a href='http://www.nowaprawicajkm.pl/info/program-wyborczy/program-kongresu-nowej-prawicy/item/program-kongresu-nowej-prawicy'>programu gospodarczego Kongresu Nowej Prawicy</a></font>"; 
	
	echo "<br/>(Powyzsza kwota została przeliczona na dzisiejszą wartość złotówki. Wyjaśnienie poniżej)";
	
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
	echo "<br/>Dotyczy osób rzetelnie wykonujących potrzebną pracę. 
		(Osoby zatrudnione dla celów biurokracji mogą dostać takie wynagrodzenie po przekwalifikowaniu się)<br/>
		Obliczenia są szacunkowe i nikt nie wie dokładnie o ile przedsiębiorcy obniżą cenę, a o ile podwyższą wynagrodzenia. 
		(Może się zdarzyć że zamiast wypłaty " . round($przedObn) . " zł zobaczysz ją np. dwa razy wyższą lub niższą, lecz ceny też zmienią się proporcjonalnie)";
	echo "<br/><b>Kalkulator prezentuje wyniki moich przeliczeń i oszacowań. Nie został jeszcze sprawdzony przez KNP</b>";
	echo "<br/></br/><b>Bardzo proszę o komentarze: piotr.jerzykowski@gmail.com</b>";
	echo "<h3> Uwaga ! </h3> Potrzebuję źródeł i dokładniejszych wartości poniższych parametrów:<br/><lu>";
	echo "<li><b>Średni procent kosztów zatrudnienia pracowników w stosunku do wszystkich kosztów firmy (Założyłem, że 60%)</b></li>";
	echo "<li><b>Średni procent kosztów na biurokrację (księgowe, kontakty z księgowością, papiery do urzedów itp. - Założyłem, że jest to 5%)</b></li>";
	echo "<li><b>Oszacowanie, ile średnio kupujemy produktów ograniczonych licencjami, pozwoleniami, sztucznymi monpolami (oligopolami) (Założyłem, że połowę miesięcznych wydatków wydajemy na takie produkty, których ceny są przez to zawyżone średnio o 50%)</b></li>";
	echo "<li>Koszty leczenia w prywatnych szpitalach (Założyłem, że jest 1000zł co 3 lata + 35 tys.zł raz na 50 lat)</li>";
	echo "<li>Ile przypadałoby uczniów na 1 nauczyciela (Gdyby nauczyciel nie marnotrawił 1/3 czasu na papierkologię - założyłem, że 15)</li>";
	
	echo "Pozostałe parametry:<br/>";
	echo "<li>Zatrudnienie specialisty 5000 zł/mies</li>";
	echo "<li>Zatrudnienie sekretarki/woźnego 2500 zł/mies</li>";
	echo "</lu> Jak widzicie potrzebuję również grafika :)";
	
//} //*/	
?>
