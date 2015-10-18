/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package javakalkulator;

import java.util.Map.Entry;

/**
 *
 * @author piotr
 */
public class JavaKalkulator {

	static void wyswietlWyniki(String komentarz, GrupaWynikow w){
		
		if (!(komentarz == "" || w.czastkowe.size() < 2)) 
			System.out.println(komentarz + w.suma);
		
		for (Entry<String, Float> kv : w.czastkowe.entrySet()){
			System.out.println(" - " +  kv.getKey() + ": " + kv.getValue());
		}
	}
	
    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {
        Kalkulator k = new Kalkulator();
		k.podatki = k.defaultPodatki(k.wydatki);
		k.netto = 1600;
		k.liczOdNetto();
		System.out.println("Koszt pracowdawcy (prawdziwe brutto): " + k.pBrutto);
		wyswietlWyniki("Składki ZUS po stronie pracodawcy: ", k.wyniki.get(0));
		System.out.println("Brutto: " + k.brutto);
		wyswietlWyniki("Składki ZUS po stronie pracownika: ", k.wyniki.get(1));
		System.out.println("Netto: " + k.netto);
		wyswietlWyniki("", k.wyniki.get(2));
		System.out.println();
		System.out.println("Przykładowe wydatki: "); 
		for (Entry<String, GrupaWydatkow> kv : k.wydatki.entrySet()){
			System.out.println(" - " + kv.getValue().nazwa + ": " + kv.getValue().kwota + " (w tym podatki: " + kv.getValue().sumaPodatkow() + ")");
		}
		System.out.println(" - Oszczędnosci: " + k.oszczednosci + " (w tym podatki: " + k.oszczPod + ")");
		System.out.println("Suma wydatkow: " + k.wydatkiSuma);
		System.out.println("Podatki ukryte w cenach produktów: " + k.wydatkiPod);
		System.out.println("Prawdziwe netto: " + k.pNetto);
    }
    
}
