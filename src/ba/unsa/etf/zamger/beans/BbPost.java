package ba.unsa.etf.zamger.beans;

// Generated May 20, 2015 3:15:09 PM by Hibernate Tools 3.4.0.CR1

import java.util.Date;
import java.util.HashSet;
import java.util.Set;

/**
 * BbPost generated by hbm2java
 */
public class BbPost implements java.io.Serializable {

	private int id;
	private Osoba osoba;
	private BbTema bbTema;
	private String naslov;
	private Date vrijeme;
	private Set<BbTema> bbTemasForZadnjiPost = new HashSet<BbTema>(0);
	private Set<BbTema> bbTemasForPrviPost = new HashSet<BbTema>(0);
	private BbPostText bbPostText;

	public BbPost() {
	}

	public BbPost(int id, Osoba osoba, BbTema bbTema, String naslov,
			Date vrijeme) {
		this.id = id;
		this.osoba = osoba;
		this.bbTema = bbTema;
		this.naslov = naslov;
		this.vrijeme = vrijeme;
	}

	public BbPost(int id, Osoba osoba, BbTema bbTema, String naslov,
			Date vrijeme, Set<BbTema> bbTemasForZadnjiPost,
			Set<BbTema> bbTemasForPrviPost, BbPostText bbPostText) {
		this.id = id;
		this.osoba = osoba;
		this.bbTema = bbTema;
		this.naslov = naslov;
		this.vrijeme = vrijeme;
		this.bbTemasForZadnjiPost = bbTemasForZadnjiPost;
		this.bbTemasForPrviPost = bbTemasForPrviPost;
		this.bbPostText = bbPostText;
	}

	public int getId() {
		return this.id;
	}

	public void setId(int id) {
		this.id = id;
	}

	public Osoba getOsoba() {
		return this.osoba;
	}

	public void setOsoba(Osoba osoba) {
		this.osoba = osoba;
	}

	public BbTema getBbTema() {
		return this.bbTema;
	}

	public void setBbTema(BbTema bbTema) {
		this.bbTema = bbTema;
	}

	public String getNaslov() {
		return this.naslov;
	}

	public void setNaslov(String naslov) {
		this.naslov = naslov;
	}

	public Date getVrijeme() {
		return this.vrijeme;
	}

	public void setVrijeme(Date vrijeme) {
		this.vrijeme = vrijeme;
	}

	public Set<BbTema> getBbTemasForZadnjiPost() {
		return this.bbTemasForZadnjiPost;
	}

	public void setBbTemasForZadnjiPost(Set<BbTema> bbTemasForZadnjiPost) {
		this.bbTemasForZadnjiPost = bbTemasForZadnjiPost;
	}

	public Set<BbTema> getBbTemasForPrviPost() {
		return this.bbTemasForPrviPost;
	}

	public void setBbTemasForPrviPost(Set<BbTema> bbTemasForPrviPost) {
		this.bbTemasForPrviPost = bbTemasForPrviPost;
	}

	public BbPostText getBbPostText() {
		return this.bbPostText;
	}

	public void setBbPostText(BbPostText bbPostText) {
		this.bbPostText = bbPostText;
	}

}