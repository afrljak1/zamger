package ba.unsa.etf.zamger.beans;

// Generated May 20, 2015 3:15:09 PM by Hibernate Tools 3.4.0.CR1

/**
 * UgovoroucenjuIzborniId generated by hbm2java
 */
public class UgovoroucenjuIzborniId implements java.io.Serializable {

	private int ugovoroucenju;
	private int predmet;

	public UgovoroucenjuIzborniId() {
	}

	public UgovoroucenjuIzborniId(int ugovoroucenju, int predmet) {
		this.ugovoroucenju = ugovoroucenju;
		this.predmet = predmet;
	}

	public int getUgovoroucenju() {
		return this.ugovoroucenju;
	}

	public void setUgovoroucenju(int ugovoroucenju) {
		this.ugovoroucenju = ugovoroucenju;
	}

	public int getPredmet() {
		return this.predmet;
	}

	public void setPredmet(int predmet) {
		this.predmet = predmet;
	}

	public boolean equals(Object other) {
		if ((this == other))
			return true;
		if ((other == null))
			return false;
		if (!(other instanceof UgovoroucenjuIzborniId))
			return false;
		UgovoroucenjuIzborniId castOther = (UgovoroucenjuIzborniId) other;

		return (this.getUgovoroucenju() == castOther.getUgovoroucenju())
				&& (this.getPredmet() == castOther.getPredmet());
	}

	public int hashCode() {
		int result = 17;

		result = 37 * result + this.getUgovoroucenju();
		result = 37 * result + this.getPredmet();
		return result;
	}

}