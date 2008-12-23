<?

// STUDENTSKA/OBAVIJEST + slanje obavjestenja za studentsku sluzbu

// v3.9.1.0 (2008/09/02) + Kopiran common/inbox u studentska/obavijest
// v3.9.1.1 (2008/10/03) + Postrozen uslov za slanje na POST


// TODO: popraviti slanje svim studentima na godini studija



function studentska_obavijest() {

global $userid,$conf_ldap_domain;

// LEGENDA tabele poruke
// Tip:
//    1 - obavjestenja
//    2 - lične poruke
// Opseg:
//    0 - svi korisnici Zamgera
//    1 - svi studenti
//    2 - svi nastavnici
//    3 - svi studenti na studiju (primalac - id studija)
//    4 - svi studenti na godini (primalac - id akademske godine)
//    5 - svi studenti na predmetu (primalac - id predmeta)
//    6 - svi studenti na labgrupi (primalac - id labgrupe)
//    7 - korisnik (primalac - user id)



// Podaci potrebni kasnije

// Zadnja akademska godina
$q20 = myquery("select id,naziv from akademska_godina order by id desc limit 1");
$ag = mysql_result($q20,0,0);

// Studij koji student trenutno sluša
$studij=0;
if ($user_student) {
	$q30 = myquery("select studij,semestar from student_studij where student=$userid and akademska_godina=$ag order by semestar desc limit 1");
	if (mysql_num_rows($q30)>0) {
		$studij = mysql_result($q30,0,0);
	}
}



// Pravimo neki okvir za sajt

?>
<center>
<table width="80%" border="0"><tr><td>

<h1>Slanje obavijesti</h1>

<?



//////////////////////
// Slanje poruke
//////////////////////

if ($_POST['akcija']=='send' && check_csrf_token()) {
	// Ko je primalac
	$primalac = intval($_REQUEST['primalac']);
	$opseg = intval($_REQUEST['opseg']);
	$ref = intval($_REQUEST['ref']);

	if ($ref>0) {
		$q310 = myquery("update poruka set tip=1, opseg=$opseg, primalac=$primalac, naslov='".my_escape($_REQUEST['naslov'])."', tekst='".my_escape($_REQUEST['tekst'])."' where id=$ref");
		nicemessage("Obavijest uspješno izmijenjena");
		zamgerlog("izmijenjena obavijest $ref",2);
	} else {
		// Nije edit - nova obavijest
		$naslov = my_escape($_REQUEST['naslov']);
		$tekst = my_escape($_REQUEST['tekst']);

		$q310 = myquery("insert into poruka set tip=1, opseg=$opseg, primalac=$primalac, posiljalac=$userid, naslov='$naslov', tekst='$tekst'");

		// Saljem mail studentima...
		if ($opseg == 1) {
			// necemo sve korisnike tipa student, nego samo one koji slusaju neki studij u aktuelnoj godini
			$upit = "select o.email, a.login, o.ime, o.prezime from osoba as o, auth as a, student_studij as ss, akademska_godina as ag where ss.student=o.id and ss.student=a.id and ss.akademska_godina=ag.id and ag.aktuelna=1";
			$subject = "OBAVJEŠTENJE: Svi studenti";
		} else if ($opseg == 2) {
			$upit = "select o.email, a.login, o.ime, o.prezime from osoba as o, auth as a, privilegije as priv where priv.osoba=o.id and priv.osoba=a.id and priv.privilegija='nastavnik'";
			$subject = "OBAVJEŠTENJE: Svi nastavnici";
		} else if ($opseg == 3) {
			$upit = "select o.email, a.login, o.ime, o.prezime from osoba as o, auth as a, student_studij as ss, akademska_godina as ag where ss.student=o.id and ss.student=a.id and ss.studij=$primalac and ss.akademska_godina=ag.id and ag.aktuelna=1";
			$q320 = myquery("select naziv from studij where id=$primalac");
			$subject = "OBAVJEŠTENJE: Svi studenti na ".mysql_result($q320,0,0);
		} else if ($opseg == 5) {
			$upit = "select o.email, a.login, o.ime, o.prezime from osoba as o, auth as a, student_predmet as sp where sp.predmet=$predmet and sp.student=o.id and sp.student=a.id";
			$q330 = myquery("select p.naziv from predmet as p, ponudakursa as pk where pk.id=$primalac and pk.predmet=p.id");
			$subject = "OBAVJEŠTENJE: Svi studenti na ".mysql_result($q330,0,0);
		}

		$subject = iconv("UTF-8", "ISO-8859-2", $subject); // neki mail klijenti ne znaju prikazati utf-8 u subjektu
		$preferences = array(
			"input-charset" => "ISO-8859-2",
			"output-charset" => "ISO-8859-2",
			"line-length" => 76,
			"line-break-chars" => "\n"
		);
		$preferences["scheme"] = "Q"; // quoted-printable
		$subject = iconv_mime_encode("", $subject, $preferences);

		$mail_body = "\n=== OBAVJEŠTENJE ZA STUDENTE ===\n\nStudentska služba ETFa poslala vam je sljedeće obavještenje:\n\n$naslov\n\n$tekst";
		if ($opseg == 2)
			$mail_body = "\n=== OBAVJEŠTENJE ZA NASTAVNIKE I SARADNIKE ===\n\nStudentska služba ETFa poslala vam je sljedeće obavještenje:\n\n$naslov\n\n$tekst";

		$q9 = myquery("select o.ime, o.prezime, o.email, a.login from osoba as o, auth as a where o.id=$userid and a.id=$userid");
		$imeprezime = mysql_result($q9,0,0)." ".mysql_result($q9,0,1);
		$email = mysql_result($q9,0,2);
		if (!(strpos($email,"@"))) $email = mysql_result($q9,0,3) . $conf_ldap_domain;
		
		$add_header = "From: $email ($imeprezime)\r\nContent-Type: text/plain; charset=utf-8\r\n";

		$mailto = "";
		$broj=0;
		$q7 = myquery($upit);
		while ($r7 = mysql_fetch_row($q7)) {
			$mailto .= "$r7[1]$conf_ldap_domain ($r7[2] $r7[3]); ";
			$broj++;
			if ($r7[0]!="$r7[1]$conf_ldap_domain") {
				$mailto .= "$r7[0] ($r7[2] $r7[3]); ";
				$broj++;
			}
			if ($broj>10) {
				mail("vljubovic@etf.unsa.ba", $subject, $mail_body, "$add_header"."Bcc: $mailto");
				$mailto=""; $broj=0;
			}
		}
		if ($broj>0)
			mail("vljubovic@etf.unsa.ba", $subject, $mail_body, "$add_header"."Bcc: $mailto");

		nicemessage("Obavijest uspješno poslana");
		zamgerlog("poslana obavijest, opseg $opseg primalac $primalac",2);
	}
}



if ($_REQUEST['akcija']=='compose' || $_REQUEST['akcija']=='izmjena') {
	$opseg=0;
	if ($_REQUEST['akcija']=='izmjena') {
		$poruka = intval($_REQUEST['poruka']);
		$q200 = myquery("select primalac, naslov, tekst, opseg from poruka where id=$poruka");
		if (mysql_num_rows($q200) < 1) {
			niceerror("Poruka ne postoji");
			zamgerlog("pokusaj izmjene na nepostojece poruke $poruka",3);
			return;
		}

/*		// Ko je poslao originalnu poruku (tj. kome odgovaramo)
		$prim_id = mysql_result($q200,0,0);
		$q210 = myquery("select a.login,o.ime,o.prezime from auth as a, osoba as o where a.id=o.id and o.id=$prim_id");
		if (mysql_num_rows($q210)<1) {
			niceerror("Nepoznat pošiljalac");
			zamgerlog("poruka $poruka ima nepoznatog posiljaoca $prim_id (prilikom odgovora na poruku)",3);
			return;
		} else
			$primalac = mysql_result($q210,0,0)." (".mysql_result($q210,0,1)." ".mysql_result($q210,0,2).")"; */
		
		// Prepravka naslova i teksta
		$primalac = mysql_result($q200,0,0);
		$naslov = mysql_result($q200,0,1);
		$tekst = mysql_result($q200,0,2);
		$opseg = mysql_result($q200,0,3);
	}
		
	?>
	<a href="?sta=studentska/obavijest">Nazad na obavijesti</a><br/>
	<h3>Slanje obavijesti</h3>
	<?=genform("POST")?>
	<?
	if ($_REQUEST['akcija']=='izmjena') {
		?>
		<input type="hidden" name="ref" value="<?=$poruka?>"><?
	}
	?>
	<input type="hidden" name="akcija" value="send">
	<script language="JavaScript">
	function spisak_primalaca(opseg) {
		var lista=document.getElementById('primalac');
		while (lista.length>0)
			lista.options[0]=null;
		if (opseg==1 || opseg==2) {
			// Nista
		} else if (opseg==3) {
			<?
			$q210 = myquery("select id,naziv from studij");
			while ($r210 = mysql_fetch_row($q210)) {
				print "	lista.options[lista.length]=new Option(\"$r210[1]\",\"$r210[0]\"";
				if ($opseg==3 && $primalac==$r210[0]) print ",true";
				print ");\n";
			}
			?>
		} else if (opseg==4) {
			// Godini!?
		} else if (opseg==5) {
			<?
			$q220 = myquery("select pk.id, p.naziv, s.kratkinaziv from ponudakursa as pk, predmet as p, studij as s, akademska_godina as ag where pk.predmet=p.id and pk.studij=s.id and pk.akademska_godina=ag.id and ag.aktuelna=1 order by pk.studij, pk.semestar, p.naziv");
			while ($r220 = mysql_fetch_row($q220)) {
				print "	lista.options[lista.length]=new Option(\"$r220[1] ($r220[2])\",\"$r220[0]\"";
				if ($opseg==5 && $primalac==$r220[0]) print ",true";
				print ");\n";
			}
			?>
		}
	}
	</script>

	<p><b>Tip primaoca:</b> 
		<select name="opseg" id="opseg" onchange="spisak_primalaca(this.value)">
		<option value="1" <? if ($opseg==1) print "selected"; ?>>Svi studenti</option>
		<option value="2">Svi nastavnici</option>
		<option value="3" <? if ($opseg==3) print "selected"; ?>>Svi studenti na studiju</option>
		<option value="5" <? if ($opseg==5) print "selected"; ?>>Svi studenti na predmetu</option>
		</select><br/>
	&nbsp;<br/>
	<b>Primalac:</b>
		<select name="primalac" id="primalac"></select>
	</p>

 	<? if ($opseg==3 || $opseg==5) {
		?><script language="JavaScript">
		spisak_primalaca(<?=$opseg?>);
		</script><?
	}
	?>


	<br/>
	Skraćeni tekst obavijesti:<br/>
	<textarea name="naslov" rows="10" cols="81"><?=$naslov?></textarea>
	<br/>&nbsp;<br/>
	Nastavak teksta obavijesti:<br/>
	<textarea name="tekst" rows="10" cols="81"><?=$tekst?></textarea>
	<br/>&nbsp;<br/>
	<input type="submit" value=" Pošalji "> <input type="reset" value=" Poništi ">
	</form>
	<?
	return;
}



?>
<p><a href="?sta=studentska/obavijest&akcija=compose">Pošalji novu obavijest</a></p>
<?



//////////////////////
// Čitanje poruke
//////////////////////


$mjeseci = array("", "januar", "februar", "mart", "april", "maj", "juni", "juli", "avgust", "septembar", "oktobar", "novembar", "decembar");

$dani = array("Nedjelja", "Ponedjeljak", "Utorak", "Srijeda", "Četvrtak", "Petak", "Subota");

$poruka = intval($_REQUEST['poruka']);
if ($poruka>0) {
	// Dobavljamo podatke o poruci
	$q10 = myquery("select opseg, primalac, posiljalac, UNIX_TIMESTAMP(vrijeme), naslov, tekst from poruka where id=$poruka and tip=1");
	if (mysql_num_rows($q10)<1) {
		niceerror("Poruka ne postoji");
		zamgerlog("pristup nepostojecoj poruci $poruka",3);
		return;
	}

	// Posiljalac
	$opseg =  mysql_result($q10,0,0);
	$prim_id = mysql_result($q10,0,1);
	$pos_id = mysql_result($q10,0,2);

	$q20 = myquery("select ime,prezime from osoba where id=$pos_id");
	if (mysql_num_rows($q20)<1) {
		$posiljalac = "Nepoznato!?";
		zamgerlog("poruka $poruka ima nepoznatog posiljaoca $pos_id",3);
	} else
		$posiljalac = mysql_result($q20,0,0)." ".mysql_result($q20,0,1);

	// Primalac
	if ($opseg==0)
		$primalac="Svi korisnici Zamgera";
	else if ($opseg==1)
		$primalac="Svi studenti";
	else if ($opseg==2)
		$primalac="Svi nastavnici i saradnici";
	else if ($opseg==3) {
		$q30 = myquery("select naziv from studij where id=$prim_id");
		if (mysql_num_rows($q30)<1) {
			$primalac="Nepoznato!?";
			zamgerlog("poruka $poruka ima nepoznatog primaoca $prim_id (opseg: studij)",3);
		} else {
			$primalac = "Svi studenti na: ".mysql_result($q30,0,0);
		}
	}
	else if ($opseg==4) {
		$q40 = myquery("select naziv from akademska_godina where id=$prim_id");
		if (mysql_num_rows($q40)<1) {
			$primalac="Nepoznato!?";
			zamgerlog("poruka $poruka ima nepoznatog primaoca $prim_id (opseg: akademska godina)",3);
		} else {
			$primalac = "Svi studenti na akademskoj godini: ".mysql_result($q40,0,0);
		}
	}
	else if ($opseg==5) {
		$q50 = myquery("select p.naziv,ag.naziv from ponudakursa as pk, predmet as p, akademska_godina as ag where pk.id=$prim_id and pk.predmet=p.id and pk.akademska_godina=ag.id");
		if (mysql_num_rows($q50)<1) {
			$primalac="Nepoznato!?";
			zamgerlog("poruka $poruka ima nepoznatog primaoca $prim_id (opseg: predmet)",3);
		} else {
			$primalac = "Svi studenti na predmetu: ".mysql_result($q50,0,0)." (".mysql_result($q50,0,1).")";
		}
	}
	else if ($opseg==6) {
		$q55 = myquery("select p.naziv,l.naziv from ponudakursa as pk, predmet as p, labgrupa as l where l.id=$prim_id and l.predmet=pk.id and pk.predmet=p.id");
		if (mysql_num_rows($q55)<1) {
			$primalac="Nepoznato!?";
			zamgerlog("poruka $poruka ima nepoznatog primaoca $prim_id (opseg: labgrupa)",3);
		} else {
			$primalac = "Svi studenti u grupi ".mysql_result($q55,0,1)." (".mysql_result($q55,0,0).")";
		}
	}
	else if ($opseg==7) {
		$q60 = myquery("select ime,prezime from osoba where id=$prim_id");
		if (mysql_num_rows($q60)<1) {
			$primalac = "Nepoznato!?";
			zamgerlog("poruka $poruka ima nepoznatog primaoca $prim_id (opseg: korisnik)",3);
		} else
			$primalac = mysql_result($q60,0,0)." ".mysql_result($q60,0,1);
	}
	else {
		$primalac = "Nepoznato!?";
		zamgerlog("poruka $poruka ima nepoznat opseg $opseg",3);
	}

	// Fini datum
	$vr = mysql_result($q10,0,3);
	if (date("d.m.Y",$vr)==date("d.m.Y")) $vrijeme = "<i>danas</i> - ";
	else if (date("d.m.Y",$vr+3600*24)==date("d.m.Y")) $vrijeme = "<i>juče</i> - ";
	$vrijeme .= $dani[date("w",$vr)].date(", j. ",$vr).$mjeseci[date("n",$vr)].date(" Y. H:i",$vr);

	// Naslov
	$naslov = mysql_result($q10,0,4);
	if (!preg_match("/\S/",$naslov)) $naslov = "[Bez naslova]";

	?><h3>Prikaz obavijesti</h3>
	<table cellspacing="0" cellpadding="0" border="0"  style="border:1px;border-color:silver;border-style:solid;"><tr><td bgcolor="#f2f2f2">
		<table border="0">
			<tr><td><b>Vrijeme slanja:</b></td><td><?=$vrijeme?></td></tr>
			<tr><td><b>Pošiljalac:</b></td><td><?=$posiljalac?></td></tr>
			<tr><td><b>Primalac:</b></td><td><?=$primalac?></td></tr>
			<tr><td>&nbsp;</td><td><a href="?sta=studentska/obavijest&akcija=izmjena&poruka=<?=$poruka?>">Izmijeni ovo obavještenje</a></td></tr>
		</table>
	</td></tr><tr><td>
		<br/>
		<table border="0" cellpadding="5"><tr><td>
		<?
		print str_replace("\n","<br/>\n",$naslov);
		?>
		</td><tr></table>
	</td></tr><tr><td>
		<br/>
		<table border="0" cellpadding="5"><tr><td>
		<?
		print str_replace("\n","<br/>\n",mysql_result($q10,0,5));
		?>
		</td><tr></table>
	</td></tr></table>
	<br/>
	<br/><hr><br/><?
}



//////////////////////
// OUTBOX
//////////////////////


	
?>
<h3>Poslana obavještenja</h3>
<table border="0" width="100%" style="border:1px;border-color:silver;border-style:solid;">
	<thead>
	<tr bgcolor="#cccccc"><td width="15%"><b>Datum</b></td><td width="15%"><b>Pošiljalac</b></td><td width="30%"><b>Primalac</b></td><td width="40%"><b>Naslov</b></td></tr>
	</thead>
	<tbody>
<?


$vrijeme_poruke = array();

$q100 = myquery("select id, UNIX_TIMESTAMP(vrijeme), opseg, primalac, naslov, posiljalac from poruka where tip=1 order by vrijeme desc");
while ($r100 = mysql_fetch_row($q100)) {
	$id = $r100[0];
	$opseg = $r100[2];
	$prim_id = $r100[3];
	$pos_id = $r100[5];

	$vrijeme_poruke[$id]=$r100[1];
	$naslov = $r100[4];
	if (strlen($naslov)>60) $naslov = substr($naslov,0,55)."...";
	if (!preg_match("/\S/",$naslov)) $naslov = "[Bez naslova]";

	// Primalac
	if ($opseg==0)
		$primalac="Svi korisnici Zamgera";
	else if ($opseg==1)
		$primalac="Svi studenti";
	else if ($opseg==2)
		$primalac="Svi nastavnici i saradnici";
	else if ($opseg==3) {
		$q30 = myquery("select naziv from studij where id=$prim_id");
		if (mysql_num_rows($q30)<1) {
			$primalac="Nepoznat studij!?";
		} else {
			$primalac = "Svi studenti na:<br/> ".mysql_result($q30,0,0);
		}
	}
	else if ($opseg==4) {
		$q40 = myquery("select naziv from akademska_godina where id=$prim_id");
		if (mysql_num_rows($q40)<1) {
			$primalac="Nepoznata akademska godina!?";
		} else {
			$primalac = "Svi studenti na akademskoj godini:<br/> ".mysql_result($q40,0,0);
		}
	}
	else if ($opseg==5) {
		$q50 = myquery("select p.naziv,s.kratkinaziv from ponudakursa as pk, predmet as p, studij as s where pk.id=$prim_id and pk.predmet=p.id and pk.studij=s.id");
		if (mysql_num_rows($q50)<1) {
			$primalac="Nepoznat predmet!?";
		} else {
			$primalac = "Svi studenti na predmetu:<br/> ".mysql_result($q50,0,0)." (".mysql_result($q50,0,1).")";
		}
	}
	else if ($opseg==6) {
		$q55 = myquery("select p.naziv,l.naziv from ponudakursa as pk, predmet as p, labgrupa as l where l.id=$prim_id and l.predmet=pk.id and pk.predmet=p.id");
		if (mysql_num_rows($q55)<1) {
			$primalac="Nepoznata labgrupa!?";
		} else {
			$primalac = "Svi studenti u grupi<br/> ".mysql_result($q55,0,1)." (".mysql_result($q55,0,0).")";
		}
	}
	else if ($opseg==7) {
		$q60 = myquery("select ime,prezime from osoba where id=$prim_id");
		if (mysql_num_rows($q60)<1) {
			$primalac = "Nepoznata osoba!?";
		} else
			$primalac = mysql_result($q60,0,0)." ".mysql_result($q60,0,1);
	}
	else {
		$primalac = "Nepoznato!?";
	}

	// Posiljalac
	$q70 = myquery("select ime,prezime from osoba where id=$pos_id");
	if (mysql_num_rows($q70)<1) {
		$posiljalac = "Nepoznata osoba!?";
	} else
		$posiljalac = mysql_result($q70,0,0)." ".mysql_result($q70,0,1);
	

	// Fino vrijeme
	$vr = $vrijeme_poruke[$id];
	$vrijeme="";
	if (date("d.m.Y",$vr)==date("d.m.Y")) $vrijeme = "<i>danas</i>, ";
	else if (date("d.m.Y",$vr+3600*24)==date("d.m.Y")) $vrijeme = "<i>juče</i>, ";
	else $vrijeme .= date("j. ",$vr).$mjeseci[date("n",$vr)].", ";
	$vrijeme .= date("H:i",$vr);

	if ($_REQUEST['poruka'] == $id) $bgcolor="#EEEECC"; else $bgcolor="#FFFFFF";

	$code_poruke[$id]="<tr bgcolor=\"$bgcolor\" onmouseover=\"this.bgColor='#EEEEEE'\" onmouseout=\"this.bgColor='$bgcolor'\"><td>$vrijeme</td><td>$posiljalac</td><td>$primalac</td><td><a href=\"?sta=studentska/obavijest&poruka=$id&mode=outbox\">$naslov</a></td></tr>\n";
}

// Sortiramo po vremenu
arsort($vrijeme_poruke);
$count=0;
foreach ($vrijeme_poruke as $id=>$vrijeme) {
	print $code_poruke[$id];
	$count++;
	// if ($count==20) break; // prikazujemo 20 poruka  -- TODO: stranice
}
if ($count==0) {
	print "<li>Nemate nijednu poruku.</li>\n";
}

print "</tbody></table>";

?>
</td></tr></table></center>
<?




}


?>