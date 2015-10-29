<?php
function myround($a){
	if ($a == 0) return $a;
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

class Podatek{
	public $nazwa;
	public $grupa;
	public $komentarz;
	public $parametr;
	public $sprzezenie;
	
	public function licz($kwota) {
		return 0;
	}
	
	public function liczOdwrotnie($kwota) {
		return 0;
	}
	
	public function __construct($grupa, $nazwa , $komentarz, $param){
		$this->grupa = $grupa; 
		$this->nazwa = $nazwa; 
		$this->komentarz = $komentarz;
		$this->parametr = $param;
	}
}

class PodatekZero extends Podatek{
	public function __construct($nazwa){
		parent::__construct ('', $nazwa , '', 0);
	}
}

class PodatekProcentowy  extends Podatek{
	public function licz($kwota){
		return $kwota*($this->parametr)/100;
	}
	
	public function liczOdwrotnie($kwota){
		return $kwota*($this->parametr)/(100+$this->parametr);
	}
}

class PodatekLiniowyZKwota extends Podatek{
	public $b;
	public function __construct($grupa, $nazwa , $komentarz, $param, $b){
		parent::__construct ($grupa, $nazwa , $komentarz, $param);
		$this->b = $b; 
	}
	public function licz($kwota) {
		return $kwota - ($kwota-$this->b)/($this->parametr+1);
	}
	
	public function liczOdwrotnie($kwota) {
		return $kwota*$this->parametr + $this->b;
	}
}

class PodatekJakVat  extends Podatek{
	public function licz($kwota) {
		return $kwota*($this->parametr/($this->parametr + 100));
	}
	
	public function liczOdwrotnie($kwota) {
		return $kwota*($this->parametr/100);
	}
}

class PodatekKwotowy extends Podatek{
	public function licz($kwota) {
		//echo "Kwota: " . $kwota . " podatek: ". $this->parametr;
		return $this->parametr;
	}
	
	public function liczOdwrotnie($kwota) {
		return $this->parametr;
	}
}

class GrupaWydatkow{
	public $podatki;
	public $nazwa;
	public $kwotaStosunek;
	public $kwota;
	public $kwotaNetto;
	public $wyniki;
	//public $komentarz;
	/*public function __construct($nazwa, $podatki, $kwotaStosunek){
		$this->nazwa = $nazwa; 
		//$this->komentarz = $komentarz;
		$this->podatki = $podatki;
		$this->kwotaStosunek = $kwotaStosunek;
		$this->kwota = 0;
		$this->kwotaNetto = 0;
	}*/
	
	public function __construct($nazwa, $kwotaStosunek){
		$this->nazwa = $nazwa; 
		//$this->komentarz = $komentarz;
		$this->podatki = array();
		$this->kwotaStosunek = $kwotaStosunek;
		$this->kwota = 0;
		$this->kwotaNetto = 0;
		$this->wyniki = new GrupaWynikow();
	}
}

class GrupaWynikow{
	public $suma;
	public $czastkowe;
	public function __construct(){
		$this->suma = 0;
		$this->czastkowe = array();
	}
	public function add($nazwa, $wynik){
		$this->suma += $wynik;
		if (isset($this->czastkowe[$nazwa]))
			$this->czastkowe[$nazwa] += $wynik;
		else
			$this->czastkowe[$nazwa] = $wynik;
	}
}

class Kalkulator{
	public $podatki;
	public $wydatki;
	
	public $netto;
	public $brutto;
	public $pBrutto;
	public $pNetto;
	public $oszczednosci;
	public $oszczPod;
	public $wydatkiSuma;
	public $wydatkiPod;
	public $procentPodatkow;
	public $sumaPodatkow;
	public $grupy;
	public $grupyProcentowo;
	
	public $logo;
	public $wyniki;
	public $pNettoPierwszego;
	public $zysk;
	public $ilePWiecej;
	
	
	public function __construct(){
		$this->podatki = $this->emptyPodatki();
		$this->wydatki = $this->defaultWydatki();
		$this->netto = 0;
		$this->brutto = 0;
		$this->pBrutto = 0;
		$this->pNetto = 0;
		$this->kosztyPodatkow = array();
		$this->pNettoPierwszego = 0;
		$this->zysk = 0;
		$this->ilePWiecej = 0;
		$this->grupy = array();
		$this->grupyProcentowo = array();
	}
	
	public function emptyPodatki(){
		$podatki = array();
		$podatki['prac'] = array();
		$podatki['doWyn'] = array();
		$podatki['odWyn'] = array();
	}
	
	public function defaultPodatki(array &$wydatki){
		$podatki = $this->emptyPodatki();
		$komentarz = "¬ród³‚o: <a href='http://wynagrodzenia.pl/kalkulator_oblicz.php'>wynagrodzenia.pl</a>";
		$podatki['prac'][] = new PodatekLiniowyZKwota("Pañstwo.ZUS", "emerytalne", $komentarz, 0.14, -9.32);
		$podatki['prac'][] = new PodatekLiniowyZKwota("Pañstwo.ZUS", "rentowe", $komentarz, 0.09327, -6.16);
		$podatki['prac'][] = new PodatekLiniowyZKwota("Pañstwo.ZUS", "wypadkowe", $komentarz, 0.02397, -1.6);
		$podatki['prac'][] = new PodatekLiniowyZKwota("Pañstwo.ZUS", "fundusz pracy", $komentarz, 0.03516, -2.33);
		$podatki['prac'][] = new PodatekLiniowyZKwota("Pañstwo.ZUS", "FG¦šP", $komentarz, 0.00144, -0.11);
		
		$podatki['doWyn'][] = new PodatekLiniowyZKwota("Pañstwo.ZUS", "emerytalne", $komentarz, 0.14, -9.28);
		$podatki['doWyn'][] = new PodatekLiniowyZKwota("Pañstwo.ZUS", "rentowe", $komentarz, 0.02153, -1.44);
		$podatki['doWyn'][] = new PodatekLiniowyZKwota("Pañstwo.ZUS", "chorobowe", $komentarz, 0.03516, -2.33);
		$podatki['doWyn'][] = new PodatekLiniowyZKwota("Pañstwo.ZUS", "zdrowotne", $komentarz, 0.11144, -7.37);
		$podatki['doWyn'][] = new PodatekLiniowyZKwota("Pañstwo.dochodowe", "PIT", $komentarz, 0.127, -75);
		
		$podatki['odWyn'][] = new PodatekKwotowy("Pañstwo.biurokracja", "Mandaty i op³‚aty skarbowe", "Wed³‚ug Rocznika Statystycznego GUS w 2013 r  pobrano 19431 mln z tytu³‚u Op³‚aty, grzywny, odsetki i inne
dochody niepodatkowe. T± kwotê™ dzielimy na 31 mln doros³‚ych ludzi (https://pl.wikipedia.org/wiki/Ludno¶æ_Polski)", 19431/31/12);
		
		
		$podatkiWZakupach = array();
		$vat5 = new PodatekJakVat("Pañstwo.VAT", "Vat 5%", "", 5);
		$vat8 = new PodatekJakVat("Pañstwo.VAT", "Vat 8%", "", 8);
		$vat23 = new PodatekJakVat("Pañstwo.VAT", "Vat 23%", "", 23);
		
		$kosztyPozwolen = new PodatekProcentowy("Pañstwo.pozwolenia", "Koszty pozwoleñ„ i uzgodnie³ñ", "W koszcie wynajmu/kredytu s±… sztuczne obci±…¿enia przy budowie: Pozwolenie na budowe, na wycinki drzew. Projektant, architekt, kierownik budowy to zawody licencjonowane, wi±™c narzucaj±… wy³¼sze ceny. Dodatkowo s±… uzgodnienia z monopolistami: dostawcami wody, pr±…du, gazu. (Trzeba do nich si±™ pod³‚±…czy±‡ by uzyska±‡ pozwolenie na budow±™) Przy budowie domku jednorodzinnego te koszty to ponad 30 tys. z³‚, co najmniej po³‚owa bezsensowna, wi±™c zak³‚adam narzut 5%", 5);
		$kosztyKredytow = new PodatekProcentowy("Pañstwo.inflacja", "Koszty kredytu/inflacji", "Zak³‚adam wk³‚ad w³‚asny ok. 40%, koszty kredytu +100%, pomniejszam o inflacj±™. W przypadku oszcz±™dzania na budow±™ nieco mniej straci si±™ przez inflacj±™", 30);
		$kosztySztucznegoPosrednika = new PodatekProcentowy("Korporacje.monopol", "Koszty po¶›rednika", "Rolnik nie mo¿e sobie legalnie sprzedaæ‡ bezpo¶rednio do sklepu, tylko musi do po¶rednika (regulacje UE). No i np. cena skupu mleka to 1,1z³‚/l <a href=\"http://www.mleczarstwopolskie.pl/cgblog/1673/54/Ceny-skupu-mleka-w-czerwcu-2015-r\">z czerwca 2015</a>, a cena mleka ³›wie³¼ego w sklepach to min 1,95 z³‚/l. Rolnik sprzedaj±…c bezpo³›rednio do sklepu mia³‚by koszt maszyny pasteryzuj±…cej/pakuj±…cej 100 tys z³‚/3 lat/1000litrÃ³w/dzie³„ = 10 gr/l + butelka 10 gr, Oczywi¶cie sklepy maja narzut ok. 20%, czyli cena w sklepie by³‚aby 1,55 z³‚/l. Po¶rednik pobiera wiêc min 20% ceny", 20);

		$lokalnyMonopol = new PodatekProcentowy("Korporacje.monopol", "Lokany monopol", "", 30);
		$oligopol = new PodatekProcentowy("Korporacje.monopol", "Oligopol", "", 20);
		$dopuszczenie = new PodatekProcentowy("Korporacje.pozwolenia", "Dopuszczenie do sprzeda¿y", "", 30);
		$akcyza = new PodatekProcentowy("Pañstwo.Akcyzy", "Akcyza", "", 39.4);
		$oplataPaliwowa = new PodatekProcentowy("Pañstwo.Akcyzy", "Op³‚ata paliwowa", "", 5.4778);
		
		$podatkiWZakupach[] = $vat23;
		$podatkiWZakupach[] = new PodatekProcentowy("Pañstwo.dochodowe", "Podatek dochodowy sprzedawcy", "Pracodawcy p³‚ac±… oko³‚o 19% ze swoich zyskÃ³w podatku dochodowego. Gdyby tego podatku nie by³‚o, konkurencja na rynku wymusi³‚aby obni³¼ke cen o ten podatek. Zak³‚adaj±…c ¶redni zysk firm oko³‚o 5%, ceny spad³‚yby o oko³‚o 1%", 1);
		$podatkiWZakupach[] = new PodatekProcentowy("Zmarnowane.biurokracja", "Koszty biurokracji", "Wed³‚ug oszacowañ„ oko³‚o 2.15% wszyskich kosztów firm to sprawy zwi±…zane z obs³‚ug±… urzedników i urz±™dów ", 2.15);
		
		foreach ($wydatki as $k => $g){
			$wydatki[$k]->podatki = $podatkiWZakupach;
		}
		
		$wydatki['wynajem']->podatki[0] = $vat8;
		array_splice($wydatki['wynajem']->podatki, 1, 0, array($kosztyKredytow, $kosztyPozwolen));
		$wydatki['zywnosc']->podatki[0] = $vat5;
		array_splice($wydatki['zywnosc']->podatki,  1, 0, array($kosztySztucznegoPosrednika));
		$wydatki['woda']->podatki[0] = $vat8;
		array_splice($wydatki['woda']->podatki,  1, 0, array($lokalnyMonopol));
		array_splice($wydatki['energia']->podatki,  1, 0, array($lokalnyMonopol));
		$wydatki['med']->podatki[0] = $vat8;
		array_splice($wydatki['med']->podatki,  1, 0, array($dopuszczenie));
		array_splice($wydatki['benzyna']->podatki,  1, 0, array($akcyza, $oplataPaliwowa));
		$wydatki['higieniczne']->podatki[0] = $vat8;
		array_splice($wydatki['higieniczne']->podatki,  1, 0, array($kosztySztucznegoPosrednika));
		
		return $podatki;
	}
	
	public function podatkiKORWiN(array &$wydatki){
		$podatki = $this->emptyPodatki();
		
		$vat = new PodatekJakVat("Pañstwo.VAT", "Vat 15%", "", 15);
		$akcyza = new PodatekProcentowy("Pañstwo.Akcyzy", "Akcyza", "", 39.4);
		$podatkiWZakupach = array($vat);
		$podatki['odWyn'][] = new PodatekKwotowy("Prywatny ubezpieczyciel.ubezpieczenia", "Prywatne ubezpieczenie medyczne", "",  350);
		//$wydatki["leczenie"] = new GrupaWydatkow("Ubezpieczenie medyczne", 0);
		//$wydatki["leczenie"]->kwota = 350;
		foreach ($wydatki as $k => $g){
			$wydatki[$k]->podatki = $podatkiWZakupach;
		}
		array_unshift($wydatki['benzyna']->podatki, $akcyza);
		return $podatki;
	}
	
	function defaultWydatki(){
		$wydatki = array();
		$wydatki['wynajem'] = new GrupaWydatkow("Wynajem mieszkania/rata kredytu", 700/2000);
		$wydatki['zywnosc'] = new GrupaWydatkow("¯ywno¶æ‡", 400/2000);
		$wydatki['woda'] = new GrupaWydatkow("Woda", 50/2000);
		$wydatki['energia'] = new GrupaWydatkow("Energia", 250/2000);
		$wydatki['wyposarzenie'] = new GrupaWydatkow("Wyposa¿enie mieszkania", 50/2000);
		$wydatki['med'] = new GrupaWydatkow("Art. medyczne", 100/2000);
		$wydatki['benzyna'] = new GrupaWydatkow("Benzyna", 250/2000);
		$wydatki['sam'] = new GrupaWydatkow("Eksp. samochodu", 50/2000);
		$wydatki['higieniczne'] = $a = new GrupaWydatkow("Art. higieniczne", 150/2000);
		return $wydatki;
	}
	
	//function licz
	
	function dodajDoGrup($p, $v){
		$k = explode('.', $p->grupa);
		$k1 = $k[0];
		if ($k > 1) $k2 = $k[1]; else $k2 = "";
		if (!isset($this->grupy[$k1])) $this->grupy[$k1] = new GrupaWynikow();
		$this->grupy[$k1]->add($k2, $v);
	}
	
	
	function getGoogleChartParams(){
		$str = '[[\'Task\', \'Percentage\'],';
		$sep ='';
		$sum = 0;
		foreach ($this->grupyProcentowo as $key => $procent){
			$sum += $procent;
			$p = myround($procent);
			$str .= "{$sep}['$key', $p]";
			$sep = ',';
		}
		$sum = myround(100 - $sum);
		$str .= "{$sep}['Obywatel', $sum]";
		$str .= ']';
		return $str;
	}
	
	function liczWydatki($netto) {
		// Liczenie wydatków
		$sumaPNetto = 0;
		$sumaNetto = 0;
		$this->wydatkiSuma = 0;
		foreach ($this->wydatki as $wyd){
			if ($wyd->kwotaNetto != 0 && $wyd->kwota == 0) {
				$k = $wyd->kwotaNetto;
				$sumaPNetto += $k;
				foreach (array_reverse($wyd->podatki) as $podatek){
					$w = $podatek->liczOdwrotnie($k);
					$k += $w;
					$wyd->wyniki->add($podatek->nazwa, $w);
					$this->dodajDoGrup($podatek, $w);
					// TODO: zapisac do grupy podatków
				}
				$wyd->wyniki->czastkowe = array_reverse($wyd->wyniki->czastkowe, true);
				$wyd->kwota = $k;
				$this->wydatkiPod += $wyd->kwota - $wyd->kwotaNetto;
				$sumaNetto += $k;
				$this->wydatkiSuma += $k;
			} else {
				if ($wyd->kwota == 0)
					$wyd->kwota = $k = round($netto*$wyd->kwotaStosunek, -1);
				else
					$k = $wyd->kwota;
				
				$sumaNetto += $k;
				$this->wydatkiSuma += $k;
				foreach ($wyd->podatki as $podatek){
					$w = $podatek->licz($k);
					$k -= $w;
					$wyd->wyniki->add($podatek->nazwa, $w);
					$this->dodajDoGrup($podatek, $w);
					//echo "Podatek: ",  $podatek->nazwa, " zabiera: ", $w, " zostaje: ", $k;
					// TODO: zapisac do grupy podatków
				}
				$this->wydatkiPod += $wyd->kwota - $k;
				$wyd->kwotaNetto = $k;
				$sumaPNetto += $k;
			}
		}
		$sredniaPod = 1 - $sumaPNetto/$sumaNetto;
		$this->oszczednosci = $netto - $sumaNetto;
		//if ($this->oszczednosci < 0) $this->oszczednosci = 0;
		$this->oszczPod = $this->oszczednosci*$sredniaPod;
		$this->wydatkiSuma += $this->oszczednosci;
		$this->wydatkiPod += $this->oszczPod;
		$this->pNetto = $sumaPNetto + $this->oszczednosci*(1 - $sredniaPod);
		
		$this->sumaPodatkow = $this->pBrutto - $this->pNetto;
		$this->procentPodatkow = (1 - $this->pNetto/$this->pBrutto)*100;
		
		foreach ($this->grupy as $key => $wyn){
			$this->grupyProcentowo[$key] = ($wyn->suma/$this->pBrutto)*100;
		}
		
		if ($this->pNettoPierwszego  != 0) {
			$this->zysk = $this->pNetto - $this->pNettoPierwszego;
			$this->ilePWiecej = $this->zysk/$this->pNettoPierwszego*100;
		}
	}
	
	
	function liczOdPrawdziewBrutto(){
		$this->wyniki = array();
		$this->grupy = array();
		$this->grupyProcentowo = array();
		$podPrac = new GrupaWynikow();
		foreach ($this->podatki['prac'] as $p){
			$a = $p->licz($this->pBrutto);
			$podPrac->add($p->nazwa, $a);
			$this->dodajDoGrup($p, $a);
		}
		$this->wyniki[] = $podPrac;
		$podDoWyn = new GrupaWynikow();
		foreach ($this->podatki['doWyn'] as $p){
			$a = $p->licz($this->pBrutto);
			$podDoWyn->add($p->nazwa, $a);
			$this->dodajDoGrup($p, $a);
		}
		$this->wyniki[] = $podDoWyn;
		$this->brutto = $this->pBrutto - $podPrac->suma;
		$this->netto = $this->brutto - $podDoWyn->suma;
		$podOdWyn =  new GrupaWynikow();
		foreach ($this->podatki['odWyn'] as $p){
			$a = $p->licz($this->netto);
			$podOdWyn->add($p->nazwa, $a);
			$this->dodajDoGrup($p, $a);
		}
		$this->wyniki[] = $podOdWyn;
		$netto = $this->netto - $podOdWyn->suma;
		
		$this->liczWydatki($netto);
	}
	
	function liczOdNetto(){
		$this->wyniki = array();
		$this->grupy = array();
		$this->grupyProcentowo = array();
		$podPrac = new GrupaWynikow();
		foreach ($this->podatki['prac'] as $p){
			$a = $p->liczOdwrotnie($this->netto);
			$podPrac->add($p->nazwa, $a);
			$this->dodajDoGrup($p, $a);
		}
		$this->wyniki[] = $podPrac;
		$podDoWyn = new GrupaWynikow();
		foreach ($this->podatki['doWyn'] as $p){
			$a = $p->liczOdwrotnie($this->netto);
			$podDoWyn->add($p->nazwa, $a);
			$this->dodajDoGrup($p, $a);
		}
		$this->wyniki[] = $podDoWyn;
		$this->brutto = $this->netto + $podDoWyn->suma;
		$this->pBrutto = $this->brutto + $podPrac->suma;
		$podOdWyn =  new GrupaWynikow();
		foreach ($this->podatki['odWyn'] as $p){
			$a = $p->licz($this->netto);
			$podOdWyn->add($p->nazwa, $a);
			$this->dodajDoGrup($p, $a);
		}
		$this->wyniki[] = $podOdWyn;
		$netto = $this->netto - $podOdWyn->suma;
		
		$this->liczWydatki($netto);
	}
	
	function copy() {
		$k = new Kalkulator();
		$k->pBrutto = $this->pBrutto;
		foreach ($this->wydatki as $key => $wyd){
			$k->wydatki[$key]->kwotaNetto = $wyd->kwotaNetto;
		}
		if ($this->pNettoPierwszego != 0) 
			$k->pNettoPierwszego = $this->pNettoPierwszego;
		else 
			$k->pNettoPierwszego = $this->pNetto;
		
		return $k;
	}
}

?>