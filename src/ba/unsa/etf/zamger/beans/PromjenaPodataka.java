package ba.unsa.etf.zamger.beans;

// Generated May 20, 2015 3:15:09 PM by Hibernate Tools 3.4.0.CR1

import java.util.Date;

/**
 * PromjenaPodataka generated by hbm2java
 */
public class PromjenaPodataka implements java.io.Serializable {

	private Integer id;
	private int osoba;
	private String ime;
	private String prezime;
	private String imeoca;
	private String prezimeoca;
	private String imemajke;
	private String prezimemajke;
	private String spol;
	private String brindexa;
	private Date datumRodjenja;
	private int mjestoRodjenja;
	private int nacionalnost;
	private int drzavljanstvo;
	private boolean borackeKategorije;
	private String jmbg;
	private String adresa;
	private int adresaMjesto;
	private String telefon;
	private int kanton;
	private String slika;
	private Date vrijemeZahtjeva;

	public PromjenaPodataka() {
	}

	public PromjenaPodataka(int osoba, String ime, String prezime,
			String imeoca, String prezimeoca, String imemajke,
			String prezimemajke, String spol, String brindexa,
			Date datumRodjenja, int mjestoRodjenja, int nacionalnost,
			int drzavljanstvo, boolean borackeKategorije, String jmbg,
			String adresa, int adresaMjesto, String telefon, int kanton,
			String slika, Date vrijemeZahtjeva) {
		this.osoba = osoba;
		this.ime = ime;
		this.prezime = prezime;
		this.imeoca = imeoca;
		this.prezimeoca = prezimeoca;
		this.imemajke = imemajke;
		this.prezimemajke = prezimemajke;
		this.spol = spol;
		this.brindexa = brindexa;
		this.datumRodjenja = datumRodjenja;
		this.mjestoRodjenja = mjestoRodjenja;
		this.nacionalnost = nacionalnost;
		this.drzavljanstvo = drzavljanstvo;
		this.borackeKategorije = borackeKategorije;
		this.jmbg = jmbg;
		this.adresa = adresa;
		this.adresaMjesto = adresaMjesto;
		this.telefon = telefon;
		this.kanton = kanton;
		this.slika = slika;
		this.vrijemeZahtjeva = vrijemeZahtjeva;
	}

	public Integer getId() {
		return this.id;
	}

	public void setId(Integer id) {
		this.id = id;
	}

	public int getOsoba() {
		return this.osoba;
	}

	public void setOsoba(int osoba) {
		this.osoba = osoba;
	}

	public String getIme() {
		return this.ime;
	}

	public void setIme(String ime) {
		this.ime = ime;
	}

	public String getPrezime() {
		return this.prezime;
	}

	public void setPrezime(String prezime) {
		this.prezime = prezime;
	}

	public String getImeoca() {
		return this.imeoca;
	}

	public void setImeoca(String imeoca) {
		this.imeoca = imeoca;
	}

	public String getPrezimeoca() {
		return this.prezimeoca;
	}

	public void setPrezimeoca(String prezimeoca) {
		this.prezimeoca = prezimeoca;
	}

	public String getImemajke() {
		return this.imemajke;
	}

	public void setImemajke(String imemajke) {
		this.imemajke = imemajke;
	}

	public String getPrezimemajke() {
		return this.prezimemajke;
	}

	public void setPrezimemajke(String prezimemajke) {
		this.prezimemajke = prezimemajke;
	}

	public String getSpol() {
		return this.spol;
	}

	public void setSpol(String spol) {
		this.spol = spol;
	}

	public String getBrindexa() {
		return this.brindexa;
	}

	public void setBrindexa(String brindexa) {
		this.brindexa = brindexa;
	}

	public Date getDatumRodjenja() {
		return this.datumRodjenja;
	}

	public void setDatumRodjenja(Date datumRodjenja) {
		this.datumRodjenja = datumRodjenja;
	}

	public int getMjestoRodjenja() {
		return this.mjestoRodjenja;
	}

	public void setMjestoRodjenja(int mjestoRodjenja) {
		this.mjestoRodjenja = mjestoRodjenja;
	}

	public int getNacionalnost() {
		return this.nacionalnost;
	}

	public void setNacionalnost(int nacionalnost) {
		this.nacionalnost = nacionalnost;
	}

	public int getDrzavljanstvo() {
		return this.drzavljanstvo;
	}

	public void setDrzavljanstvo(int drzavljanstvo) {
		this.drzavljanstvo = drzavljanstvo;
	}

	public boolean isBorackeKategorije() {
		return this.borackeKategorije;
	}

	public void setBorackeKategorije(boolean borackeKategorije) {
		this.borackeKategorije = borackeKategorije;
	}

	public String getJmbg() {
		return this.jmbg;
	}

	public void setJmbg(String jmbg) {
		this.jmbg = jmbg;
	}

	public String getAdresa() {
		return this.adresa;
	}

	public void setAdresa(String adresa) {
		this.adresa = adresa;
	}

	public int getAdresaMjesto() {
		return this.adresaMjesto;
	}

	public void setAdresaMjesto(int adresaMjesto) {
		this.adresaMjesto = adresaMjesto;
	}

	public String getTelefon() {
		return this.telefon;
	}

	public void setTelefon(String telefon) {
		this.telefon = telefon;
	}

	public int getKanton() {
		return this.kanton;
	}

	public void setKanton(int kanton) {
		this.kanton = kanton;
	}

	public String getSlika() {
		return this.slika;
	}

	public void setSlika(String slika) {
		this.slika = slika;
	}

	public Date getVrijemeZahtjeva() {
		return this.vrijemeZahtjeva;
	}

	public void setVrijemeZahtjeva(Date vrijemeZahtjeva) {
		this.vrijemeZahtjeva = vrijemeZahtjeva;
	}

}