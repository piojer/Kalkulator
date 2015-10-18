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
	public $logo;
	public $wyniki;
	
	public function __construct(){
		$this->podatki = $this->emptyPodatki();
		$this->wydatki = $this->defaultWydatki();
		$this->netto = 0;
		$this->brutto = 0;
		$this->pBrutto = 0;
		$this->pNetto = 0;
		$this->kosztyPodatkow = array();
	}
	
	public function emptyPodatki(){
		$podatki = array();
		$podatki['prac'] = array();
		$podatki['doWyn'] = array();
		$podatki['odWyn'] = array();
	}
	
	public function defaultPodatki(array &$wydatki){
		$podatki = $this->emptyPodatki();
		$komentarz = "�r�d��o: <a href='http://wynagrodzenia.pl/kalkulator_oblicz.php'>wynagrodzenia.pl</a>";
		$podatki['prac'][] = new PodatekLiniowyZKwota("ZUS", "emerytalne", $komentarz, 0.14, -9.32);
		$podatki['prac'][] = new PodatekLiniowyZKwota("ZUS", "rentowe", $komentarz, 0.09327, -6.16);
		$podatki['prac'][] = new PodatekLiniowyZKwota("ZUS", "wypadkowe", $komentarz, 0.02397, -1.6);
		$podatki['prac'][] = new PodatekLiniowyZKwota("ZUS", "fundusz pracy", $komentarz, 0.03516, -2.33);
		$podatki['prac'][] = new PodatekLiniowyZKwota("ZUS", "FG��P", $komentarz, 0.00144, -0.11);
		
		$podatki['doWyn'][] = new PodatekLiniowyZKwota("ZUS", "emerytalne", $komentarz, 0.14, -9.28);
		$podatki['doWyn'][] = new PodatekLiniowyZKwota("ZUS", "rentowe", $komentarz, 0.02153, -1.44);
		$podatki['doWyn'][] = new PodatekLiniowyZKwota("ZUS", "chorobowe", $komentarz, 0.03516, -2.33);
		$podatki['doWyn'][] = new PodatekLiniowyZKwota("ZUS", "zdrowotne", $komentarz, 0.11144, -7.37);
		$podatki['doWyn'][] = new PodatekLiniowyZKwota("dochodowe", "PIT", $komentarz, 0.127, -75);
		
		$podatki['odWyn'][] = new PodatekKwotowy("biurokracja", "Mandaty i op��aty skarbowe", "Wed��ug Rocznika Statystycznego GUS w 2013 r  pobrano 19431 mln z tytu��u Op��aty, grzywny, odsetki i inne
dochody niepodatkowe. T� kwot� dzielimy na 31 mln doros��ych ludzi (https://pl.wikipedia.org/wiki/Ludno��_Polski)", 19431/31/12);
		
		
		$podatkiWZakupach = array();
		$vat5 = new PodatekJakVat("VAT", "Vat 5%", "", 5);
		$vat8 = new PodatekJakVat("VAT", "Vat 8%", "", 8);
		$vat23 = new PodatekJakVat("VAT", "Vat 23%", "", 23);
		
		$kosztyPozwolen = new PodatekProcentowy("pozwolenia", "Koszty pozwole� i uzgodnie��", "W koszcie wynajmu/kredytu s�� sztuczne obci���enia przy budowie: Pozwolenie na budowe, na wycinki drzew. Projektant, architekt, kierownik budowy to zawody licencjonowane, wi��c narzucaj�� wy��sze ceny. Dodatkowo s�� uzgodnienia z monopolistami: dostawcami wody, pr��du, gazu. (Trzeba do nich si�� pod����czy�� by uzyska�� pozwolenie na budow��) Przy budowie domku jednorodzinnego te koszty to ponad 30 tys. z��, co najmniej po��owa bezsensowna, wi��c zak��adam narzut 5%", 5);
		$kosztyKredytow = new PodatekProcentowy("inflacja", "Koszty kredytu/inflacji", "Zak��adam wk��ad w��asny ok. 40%, koszty kredytu +100%, pomniejszam o inflacj��. W przypadku oszcz��dzania na budow�� nieco mniej straci si�� przez inflacj��", 30);
		$kosztySztucznegoPosrednika = new PodatekProcentowy("monopol", "Koszty po��rednika", "Rolnik nie mo�e sobie legalnie sprzeda� bezpo�rednio do sklepu, tylko musi do po�rednika (regulacje UE). No i np. cena skupu mleka to 1,1z��/l <a href=\"http://www.mleczarstwopolskie.pl/cgblog/1673/54/Ceny-skupu-mleka-w-czerwcu-2015-r\">z czerwca 2015</a>, a cena mleka ��wie��ego w sklepach to min 1,95 z��/l. Rolnik sprzedaj��c bezpo��rednio do sklepu mia��by koszt maszyny pasteryzuj��cej/pakuj��cej 100 tys z��/3 lat/1000litrów/dzie�� = 10 gr/l + butelka 10 gr, Oczywi�cie sklepy maja narzut ok. 20%, czyli cena w sklepie by��aby 1,55 z��/l. Po�rednik pobiera wi�c min 20% ceny", 20);

		$lokalnyMonopol = new PodatekProcentowy("monopol", "Lokany monopol", "", 30);
		$oligopol = new PodatekProcentowy("monopol", "Oligopol", "", 20);
		$dopuszczenie = new PodatekProcentowy("pozwolenia", "Dopuszczenie do sprzeda�y", "", 30);
		$akcyza = new PodatekProcentowy("Akcyzy", "Akcyza", "", 39.4);
		$oplataPaliwowa = new PodatekProcentowy("Akcyzy", "Op��ata paliwowa", "", 5.4778);
		
		$podatkiWZakupach[] = $vat23;
		$podatkiWZakupach[] = new PodatekProcentowy("dochodowe", "Podatek dochodowy sprzedawcy", "Pracodawcy p��ac�� oko��o 19% ze swoich zysków podatku dochodowego. Gdyby tego podatku nie by��o, konkurencja na rynku wymusi��aby obni��ke cen o ten podatek. Zak��adaj��c �redni zysk firm oko��o 5%, ceny spad��yby o oko��o 1%", 1);
		$podatkiWZakupach[] = new PodatekProcentowy("biurokracja", "Koszty biurokracji", "Wed��ug oszacowa� oko��o 2.15% wszyskich koszt�w firm to sprawy zwi��zane z obs��ug�� urzednik�w i urz��d�w ", 2.15);
		
		foreach ($wydatki as $k => $g){
			$wydatki[$k]->podatki = $podatkiWZakupach;
		}
		$wydatki['wynajem']->podatki[0] = $vat8;
		array_unshift($wydatki['wynajem']->podatki, $kosztyKredytow, $kosztyPozwolen);
		$wydatki['zywnosc']->podatki[0] = $vat5;
		array_unshift($wydatki['zywnosc']->podatki, $kosztySztucznegoPosrednika);
		$wydatki['woda']->podatki[0] = $vat8;
		array_unshift($wydatki['woda']->podatki, $lokalnyMonopol);
		array_unshift($wydatki['energia']->podatki, $lokalnyMonopol);
		$wydatki['med']->podatki[0] = $vat8;
		array_unshift($wydatki['med']->podatki, $dopuszczenie);
		array_unshift($wydatki['benzyna']->podatki, $akcyza, $oplataPaliwowa);
		$wydatki['higieniczne']->podatki[0] = $vat8;
		array_unshift($wydatki['higieniczne']->podatki, $kosztySztucznegoPosrednika);
		
		return $podatki;
	}
	
	public function podatkiKORWiN(array &$wydatki){
		$podatki = $this->emptyPodatki();
		
		$vat = new PodatekJakVat("VAT", "Vat 15%", "", 15);
		$podatkiWZakupach = array($vat);
		$podatki['odWyn'][] = new PodatekKwotowy("ubezpieczenia", "Prywatne ubezpieczenie medyczne", "",  350);
		//$wydatki["leczenie"] = new GrupaWydatkow("Ubezpieczenie medyczne", 0);
		//$wydatki["leczenie"]->kwota = 350;
		foreach ($wydatki as $k => $g){
			$wydatki[$k]->podatki = $podatkiWZakupach;
		}
		return $podatki;
	}
	
	function defaultWydatki(){
		$wydatki = array();
		$wydatki['wynajem'] = new GrupaWydatkow("Wynajem mieszkania/rata kredytu", 700/2000);
		$wydatki['zywnosc'] = new GrupaWydatkow("�ywno��", 400/2000);
		$wydatki['woda'] = new GrupaWydatkow("Woda", 50/2000);
		$wydatki['energia'] = new GrupaWydatkow("Energia", 250/2000);
		$wydatki['wyposarzenie'] = new GrupaWydatkow("Wyposa�enie mieszkania", 50/2000);
		$wydatki['med'] = new GrupaWydatkow("Art. medyczne", 100/2000);
		$wydatki['benzyna'] = new GrupaWydatkow("Benzyna", 250/2000);
		$wydatki['sam'] = new GrupaWydatkow("Eksp. samochodu", 50/2000);
		$wydatki['higieniczne'] = $a = new GrupaWydatkow("Art. higieniczne", 150/2000);
		return $wydatki;
	}
	
	//function licz
	
	function liczWydatki($netto) {
		// Liczenie wydatk�w
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
					// TODO: zapisac do grupy podatk�w
				}
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
					//echo "Podatek: ",  $podatek->nazwa, " zabiera: ", $w, " zostaje: ", $k;
					// TODO: zapisac do grupy podatk�w
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
		
		$this->procentPodatkow = (1 - $this->pNetto/$this->pBrutto)*100;
	}
	
	function liczOdPrawdziewBrutto(){
		$this->wyniki = array();
		$podPrac = new GrupaWynikow();
		foreach ($this->podatki['prac'] as $p){
			$a = $p->licz($this->pBrutto);
			$podPrac->add($p->nazwa, $a);
		}
		$this->wyniki[] = $podPrac;
		$podDoWyn = new GrupaWynikow();
		foreach ($this->podatki['doWyn'] as $p){
			$a = $p->licz($this->pBrutto);
			$podDoWyn->add($p->nazwa, $a);
		}
		$this->wyniki[] = $podDoWyn;
		$this->brutto = $this->pBrutto - $podPrac->suma;
		$this->netto = $this->brutto - $podDoWyn->suma;
		$podOdWyn =  new GrupaWynikow();
		foreach ($this->podatki['odWyn'] as $p){
			$a = $p->licz($this->netto);
			$podOdWyn->add($p->nazwa, $a);
		}
		$this->wyniki[] = $podOdWyn;
		$netto = $this->netto - $podOdWyn->suma;
		
		$this->liczWydatki($netto);
	}
	
	function liczOdNetto(){
		$this->wyniki = array();
		$podPrac = new GrupaWynikow();
		foreach ($this->podatki['prac'] as $p){
			$a = $p->liczOdwrotnie($this->netto);
			$podPrac->add($p->nazwa, $a);
		}
		$this->wyniki[] = $podPrac;
		$podDoWyn = new GrupaWynikow();
		foreach ($this->podatki['doWyn'] as $p){
			$a = $p->liczOdwrotnie($this->netto);
			$podDoWyn->add($p->nazwa, $a);
		}
		$this->wyniki[] = $podDoWyn;
		$this->brutto = $this->netto + $podDoWyn->suma;
		$this->pBrutto = $this->brutto + $podPrac->suma;
		$podOdWyn =  new GrupaWynikow();
		foreach ($this->podatki['odWyn'] as $p){
			$a = $p->licz($this->netto);
			$podOdWyn->add($p->nazwa, $a);
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
		return $k;
	}
}

?>