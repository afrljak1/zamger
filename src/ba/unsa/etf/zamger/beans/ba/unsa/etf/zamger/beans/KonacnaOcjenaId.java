package ba.unsa.etf.zamger.beans;

// Generated May 26, 2015 12:09:40 PM by Hibernate Tools 3.4.0.CR1

/**
 * KonacnaOcjenaId generated by hbm2java
 */
public class KonacnaOcjenaId implements java.io.Serializable {

	private int student;
	private int predmet;

	public KonacnaOcjenaId() {
	}

	public KonacnaOcjenaId(int student, int predmet) {
		this.student = student;
		this.predmet = predmet;
	}

	public int getStudent() {
		return this.student;
	}

	public void setStudent(int student) {
		this.student = student;
	}

	public int getPredmet() {
		return this.predmet;
	}

	public void setPredmet(int predmet) {
		this.predmet = predmet;
	}

}