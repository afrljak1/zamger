package ba.unsa.etf.zamger.beans;

// Generated May 20, 2015 3:15:09 PM by Hibernate Tools 3.4.0.CR1

/**
 * Log2Modul generated by hbm2java
 */
public class Log2Modul implements java.io.Serializable {

	private Integer id;
	private String naziv;

	public Log2Modul() {
	}

	public Log2Modul(String naziv) {
		this.naziv = naziv;
	}

	public Integer getId() {
		return this.id;
	}

	public void setId(Integer id) {
		this.id = id;
	}

	public String getNaziv() {
		return this.naziv;
	}

	public void setNaziv(String naziv) {
		this.naziv = naziv;
	}

}