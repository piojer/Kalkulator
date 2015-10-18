/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package javakalkulator;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

/**
 *
 * @author piotr
 */
class Podatek{
	String nazwa;
	String grupa;
	String komentarz;
	float parametr;
	boolean sprzezenie;
	
	public float licz(float kwota) {
		return 0;
	}
	
	public float liczOdwrotnie(float kwota) {
		return 0;
	}
	
	public Podatek(String grupa, String nazwa , String komentarz, float param){
		this.grupa = grupa; 
		this.nazwa = nazwa; 
		this.komentarz = komentarz;
		this.parametr = param;
	}
}

class PodatekZero extends Podatek{
	public PodatekZero(String nazwa){
		super("", nazwa , "", 0);
	}
}

class PodatekProcentowy  extends Podatek{
	public PodatekProcentowy(String grupa, String nazwa , String komentarz, float param) {super(grupa, nazwa , komentarz, param);}
	
	public float licz(float kwota){
		return kwota*(this.parametr)/100;
	}
	
	public float liczOdwrotnie(float kwota){
		return kwota*(this.parametr)/(100+this.parametr);
	}
}

class PodatekLiniowyZKwota extends Podatek{
	float b;
	public PodatekLiniowyZKwota(String grupa, String nazwa , String komentarz, float param, float b){
		super(grupa, nazwa , komentarz, param);
		this.b = b; 
	}
	public float licz(float kwota) {
		return kwota - (kwota-this.b)/(this.parametr+1);
	}
	
	public float liczOdwrotnie(float kwota) {
		return kwota*this.parametr + this.b;
	}
}

class PodatekJakVat  extends Podatek{
	public PodatekJakVat(String grupa, String nazwa , String komentarz, float param) {super(grupa, nazwa , komentarz, param);}
	
	public float licz(float kwota) {
		return kwota*(this.parametr/(this.parametr + 100));
	}
	
	public float liczOdwrotnie(float kwota) {
		return kwota*(this.parametr/100);
	}
}

class PodatekKwotowy extends Podatek{
	public PodatekKwotowy(String grupa, String nazwa , String komentarz, float param) {super(grupa, nazwa , komentarz, param);}
	
	public float licz(float kwota) {
		//echo "Kwota: " . kwota . " podatek: ". this.parametr;
		return this.parametr;
	}
	
	public float liczOdwrotnie(float kwota) {
		return this.parametr;
	}
}

class GrupaWydatkow{
	List<Podatek> podatki;
	String nazwa;
	float kwotaStosunek;
	float kwota;
	float kwotaNetto;
	
	public GrupaWydatkow(String nazwa, float s){
		this.nazwa = nazwa; 
		//this.komentarz = komentarz;
		this.podatki = new ArrayList<>();
		this.kwotaStosunek = s;
		this.kwota = 0;
		this.kwotaNetto = 0;
	}

	float sumaPodatkow() {
		return kwota - kwotaNetto;
	}
}

class GrupaWynikow{
	float suma;
	Map<String, Float> czastkowe;
	public GrupaWynikow(){
		this.suma = 0;
		this.czastkowe = new HashMap<String, Float>() {};
	}
	
	public void add(String nazwa, float wynik){
		this.suma += wynik;
		this.czastkowe.put(nazwa, wynik);
	}
}

class ZbiorPodatkow{
	public List<Podatek> prac;
	public List<Podatek> doWyn;
	public List<Podatek> odWyn;
	ZbiorPodatkow(){
		prac = new ArrayList<>();
		doWyn = new ArrayList<>();
		odWyn = new ArrayList<>();	
	}
}

class Kalkulator{
	ZbiorPodatkow podatki;
	Map<String, GrupaWydatkow> wydatki;
	
	float netto;
	float brutto;
	float pBrutto;
	float pNetto;
	float oszczednosci;
	float oszczPod;
	float wydatkiSuma;
	float wydatkiPod;
	List<GrupaWynikow> wyniki;
	
	public Kalkulator(){
		this.podatki = this.emptyPodatki();
		this.wydatki = this.defaultWydatki();
		this.netto = 0;
		this.brutto = 0;
		this.pBrutto = 0;
		this.pNetto = 0;
		this.wyniki = new ArrayList<GrupaWynikow>();
	}
	
	public ZbiorPodatkow emptyPodatki(){
		return new ZbiorPodatkow();
	}
	
	public ZbiorPodatkow defaultPodatki(Map<String, GrupaWydatkow> wydatki){
		ZbiorPodatkow podatki = this.emptyPodatki();
		String komentarz = "Źródło: <a href='http://wynagrodzenia.pl/kalkulator_oblicz.php'>wynagrodzenia.pl</a>";
		podatki.prac.add(new PodatekLiniowyZKwota("ZUS", "emerytalne", komentarz, 0.14f, -9.32f));
		podatki.prac.add(new PodatekLiniowyZKwota("ZUS", "rentowe", komentarz, 0.09327f, -6.16f));
		podatki.prac.add(new PodatekLiniowyZKwota("ZUS", "wypadkowe", komentarz, 0.02397f, -1.6f));
		podatki.prac.add(new PodatekLiniowyZKwota("ZUS", "fundusz pracy", komentarz, 0.03516f, -2.33f));
		podatki.prac.add(new PodatekLiniowyZKwota("ZUS", "FGŚP", komentarz, 0.00144f, -0.11f));
		
		podatki.doWyn.add(new PodatekLiniowyZKwota("ZUS", "emerytalne", komentarz, 0.14f, -9.28f));
		podatki.doWyn.add(new PodatekLiniowyZKwota("ZUS", "rentowe", komentarz, 0.02153f, -1.44f));
		podatki.doWyn.add(new PodatekLiniowyZKwota("ZUS", "chorobowe", komentarz, 0.03516f, -2.33f));
		podatki.doWyn.add(new PodatekLiniowyZKwota("ZUS", "zdrowotne", komentarz, 0.11144f, -7.37f));
		podatki.doWyn.add(new PodatekLiniowyZKwota("dochodowe", "PIT", komentarz, 0.127f, -75f));
		
		podatki.odWyn.add(new PodatekKwotowy("biurokracja", "Mandaty i opłaty skarbowe", "Według Rocznika Statystycznego GUS w 2013 r  pobrano 19431 mln z tytułu Opłaty, grzywny, odsetki i inne dochody niepodatkowe. Tą kwotę dzielimy na 31 mln dorosłych ludzi (https://pl.wikipedia.org/wiki/Ludność_Polski)", 19431/31/12));
		
		
		List<Podatek> podatkiWZakupach = new ArrayList<>();
		Podatek vat5 = new PodatekJakVat("VAT", "Vat 5%", "", 5);
		Podatek vat8 = new PodatekJakVat("VAT", "Vat 8%", "", 8);
		Podatek vat23 = new PodatekJakVat("VAT", "Vat 23%", "", 23);
		
		Podatek kosztyPozwolen = new PodatekProcentowy("pozwolenia", "Koszty pozwoleń i uzgodniełń", "W koszcie wynajmu/kredytu są sztuczne obciążenia przy budowie: Pozwolenie na budowe, na wycinki drzew. Projektant, architekt, kierownik budowy to zawody licencjonowane, więc narzucają wyłźsze ceny. Dodatkowo są uzgodnienia z monopolistami: dostawcami wody, prądu, gazu. (Trzeba do nich się podłączyą by uzyskaą pozwolenie na budowę) Przy budowie domku jednorodzinnego te koszty to ponad 30 tys. zł, co najmniej połowa bezsensowna, więc zakładam narzut 5%", 5);
		Podatek kosztyKredytow = new PodatekProcentowy("inflacja", "Koszty kredytu/inflacji", "Zakładam wkład własny ok. 40%, koszty kredytu +100%, pomniejszam o inflację. W przypadku oszczędzania na budowę nieco mniej straci się przez inflację", 30);
		Podatek kosztySztucznegoPosrednika = new PodatekProcentowy("monopol", "Koszty pośrednika", "Rolnik nie może sobie legalnie sprzedać bezpośrednio do sklepu, tylko musi do pośrednika (regulacje UE). No i np. cena skupu mleka to 1,1zł/l <a href=\"http://www.mleczarstwopolskie.pl/cgblog/1673/54/Ceny-skupu-mleka-w-czerwcu-2015-r\">z czerwca 2015</a>, a cena mleka łwiełźego w sklepach to min 1,95 zł/l. Rolnik sprzedając bezpołrednio do sklepu miałby koszt maszyny pasteryzującej/pakującej 100 tys zł/3 lat/1000litrów/dzieł = 10 gr/l + butelka 10 gr, Oczywiście sklepy maja narzut ok. 20%, czyli cena w sklepie byłaby 1,55 zł/l. Pośrednik pobiera więc min 20% ceny", 20);

		Podatek lokalnyMonopol = new PodatekProcentowy("monopol", "Lokany monopol", "", 30);
		Podatek oligopol = new PodatekProcentowy("monopol", "Oligopol", "", 20);
		Podatek dopuszczenie = new PodatekProcentowy("pozwolenia", "Dopuszczenie do sprzedaży", "", 30);
		Podatek akcyza = new PodatekProcentowy("Akcyzy", "Akcyza", "", 39.4f);
		Podatek oplataPaliwowa = new PodatekProcentowy("Akcyzy", "Opłata paliwowa", "", 5.4778f);
		
		podatkiWZakupach.add(vat23);
		podatkiWZakupach.add(new PodatekProcentowy("dochodowe", "Podatek dochodowy sprzedawcy", "Pracodawcy płacą około 19% ze swoich zysków podatku dochodowego. Gdyby tego podatku nie było, konkurencja na rynku wymusiłaby obniżke cen o ten podatek. Zakładając średni zysk firm około 5%, ceny spadłyby o około 1%", 1));
		podatkiWZakupach.add(new PodatekProcentowy("biurokracja", "Koszty biurokracji", "Według oszacowań około 2.15% wszyskich kosztów firm to sprawy związane z obsługą urzedników i urzędów ", 2.15f));
		
		for (String k : wydatki.keySet()){
			wydatki.get(k).podatki = new ArrayList<>(podatkiWZakupach);
		}
		wydatki.get("wynajem").podatki.set(0, vat8);
		wydatki.get("wynajem").podatki.add(0, kosztyKredytow);
		wydatki.get("wynajem").podatki.add(0,  kosztyPozwolen);
		
		wydatki.get("zywnosc").podatki.set(0, vat5);
		wydatki.get("zywnosc").podatki.add(0, kosztySztucznegoPosrednika);
		
		wydatki.get("woda").podatki.set(0, vat8);
		wydatki.get("woda").podatki.add(0, lokalnyMonopol);
		
		wydatki.get("energia").podatki.add(0, lokalnyMonopol);
		
		wydatki.get("med").podatki.set(0, vat8);
		wydatki.get("med").podatki.add(0, dopuszczenie);
		
		wydatki.get("benzyna").podatki.add(0, akcyza);
		wydatki.get("benzyna").podatki.add(0, oplataPaliwowa);
		
		wydatki.get("higieniczne").podatki.set(0, vat8);
		wydatki.get("higieniczne").podatki.add(0, kosztySztucznegoPosrednika);
		
		return podatki;
	}
	
	Map<String, GrupaWydatkow> defaultWydatki(){
		Map<String, GrupaWydatkow> wydatki = new HashMap<>();
		wydatki.put("wynajem", new GrupaWydatkow("Wynajem mieszkania/rata kredytu", 700f/2000));
		wydatki.put("zywnosc", new GrupaWydatkow("Żywność", 400f/2000));
		wydatki.put("woda", new GrupaWydatkow("Woda", 50f/2000));
		wydatki.put("energia", new GrupaWydatkow("Energia", 250f/2000));
		wydatki.put("wyposarzenie", new GrupaWydatkow("Wyposażenie mieszkania", 50f/2000));
		wydatki.put("med", new GrupaWydatkow("Art. medyczne", 100f/2000));
		wydatki.put("benzyna", new GrupaWydatkow("Benzyna", 250f/2000));
		wydatki.put("sam", new GrupaWydatkow("Eksp. samochodu", 50f/2000));
		wydatki.put("higieniczne", new GrupaWydatkow("Art. higieniczne", 150f/2000));
		return wydatki;
	}
	
	
	
	void liczOdNetto(){
		this.wyniki.clear();
		GrupaWynikow podPrac = new GrupaWynikow();
		for (Podatek p : this.podatki.prac){
			float a = p.liczOdwrotnie(this.netto);
			podPrac.add(p.nazwa, a);
		}
		this.wyniki.add(podPrac);
		GrupaWynikow podDoWyn = new GrupaWynikow();
		for (Podatek p : this.podatki.doWyn){
			float a = p.liczOdwrotnie(this.netto);
			podDoWyn.add(p.nazwa, a);
		}
		this.wyniki.add(podDoWyn);
		this.brutto = this.netto + podDoWyn.suma;
		this.pBrutto = this.brutto + podPrac.suma;
		GrupaWynikow podOdWyn =  new GrupaWynikow();
		for (Podatek p : this.podatki.odWyn){
			float a = p.licz(this.netto);
			podOdWyn.add(p.nazwa, a);
		}
		this.wyniki.add(podOdWyn);
		float _netto = this.netto - podOdWyn.suma;
		
		// Liczenie wydatków
		float sumaPNetto = 0;
		float sumaNetto = 0;
		this.wydatkiSuma = 0;
		for (GrupaWydatkow wyd : this.wydatki.values()){
			float k = (Math.round((_netto*wyd.kwotaStosunek)/10))*10;
			System.out.println("netto: " + _netto + " kwotaStosunek: " + wyd.kwotaStosunek + " kwota: " + k);
			wyd.kwota = k;
			sumaNetto += k;
			this.wydatkiSuma += k;
			for (Podatek podatek : wyd.podatki){
				
				float w = podatek.licz(k);
				k -= w;
				System.out.println(" - podatek: " + podatek.nazwa + " wartosc: " + w + " kwota po: " + k);
				// TODO: zapisac do grupy podatków
			}
			this.wydatkiPod += wyd.kwota - k;
			wyd.kwotaNetto = k;
			sumaPNetto += k;
		}
		float sredniaPod = 1 - sumaPNetto/sumaNetto;
		this.oszczednosci = _netto - sumaNetto;
		this.oszczPod = this.oszczednosci*sredniaPod;
		this.wydatkiSuma += this.oszczednosci;
		this.wydatkiPod += this.oszczPod;
		this.pNetto = sumaPNetto + this.oszczednosci*(1 - sredniaPod);
	}
}
