package ba.unsa.etf.zamger.beans;

// Generated May 20, 2015 3:15:09 PM by Hibernate Tools 3.4.0.CR1

/**
 * AutotestRezultatId generated by hbm2java
 */
public class AutotestRezultatId implements java.io.Serializable {

	private int autotest;
	private int student;

	public AutotestRezultatId() {
	}

	public AutotestRezultatId(int autotest, int student) {
		this.autotest = autotest;
		this.student = student;
	}

	public int getAutotest() {
		return this.autotest;
	}

	public void setAutotest(int autotest) {
		this.autotest = autotest;
	}

	public int getStudent() {
		return this.student;
	}

	public void setStudent(int student) {
		this.student = student;
	}

	public boolean equals(Object other) {
		if ((this == other))
			return true;
		if ((other == null))
			return false;
		if (!(other instanceof AutotestRezultatId))
			return false;
		AutotestRezultatId castOther = (AutotestRezultatId) other;

		return (this.getAutotest() == castOther.getAutotest())
				&& (this.getStudent() == castOther.getStudent());
	}

	public int hashCode() {
		int result = 17;

		result = 37 * result + this.getAutotest();
		result = 37 * result + this.getStudent();
		return result;
	}

}