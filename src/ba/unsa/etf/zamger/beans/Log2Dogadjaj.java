package ba.unsa.etf.zamger.beans;

// Generated May 20, 2015 3:15:09 PM by Hibernate Tools 3.4.0.CR1

/**
 * Log2Dogadjaj generated by hbm2java
 */
public class Log2Dogadjaj implements java.io.Serializable {

	private Integer id;
	private String opis;
	private byte nivo;

	public Log2Dogadjaj() {
	}

	public Log2Dogadjaj(String opis, byte nivo) {
		this.opis = opis;
		this.nivo = nivo;
	}

	public Integer getId() {
		return this.id;
	}

	public void setId(Integer id) {
		this.id = id;
	}

	public String getOpis() {
		return this.opis;
	}

	public void setOpis(String opis) {
		this.opis = opis;
	}

	public byte getNivo() {
		return this.nivo;
	}

	public void setNivo(byte nivo) {
		this.nivo = nivo;
	}

}