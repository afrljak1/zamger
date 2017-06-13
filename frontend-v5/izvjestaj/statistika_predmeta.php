<?

// IZVJESTAJ/STATISTIKA_PREDMETA - neke sumarne statistike za dati predmet

// v4.0.9.1 (2009/04/22) + Novi izvjestaj napravljen na osnovu koda iz izvjestaj/ispit
// v4.0.9.2 (2009/09/05) + Optimizovan izvjestaj koristeci komponente
// v4.0.9.3 (2009/09/22) + Dodan broj ponovaca



function izvjestaj_statistika_predmeta() {

global $userid,$user_nastavnik,$user_studentska,$user_siteadmin;


?>
<p>Univerzitet u Sarajevu<br/>
Elektrotehnički fakultet Sarajevo</p>
<p>Datum i vrijeme izvještaja: <?=date("d. m. Y. H:i");?></p>
<?



$predmet = intval($_REQUEST['predmet']);
$ag = intval($_REQUEST['ag']); // akademska godina


// Provjera permisija

/*if (!$user_nastavnik && !$user_studentska && !$user_siteadmin) {
	biguglyerror("Nemate permisije za pristup ovom izvještaju");
	zamgerlog ("pristup izvjestaju a nije NBA",3); // 3 = error
	return;
}*/
if (!$user_studentska && !$user_siteadmin) {
	$q2 = myquery("select nivo_pristupa from nastavnik_predmet where nastavnik=$userid and predmet=$predmet and akademska_godina=$ag");
	if (mysql_num_rows($q2) < 1) {
		biguglyerror("Nemate permisije za pristup ovom izvještaju");
		zamgerlog ("nije admin predmeta pp$predmet, godina ag$ag",3); // 3 = error
		return;
	}
}



// Naziv predmeta, akademske godine

$q10 = myquery("select naziv from predmet where id=$predmet");
if (mysql_num_rows($q10)<1) {
	biguglyerror("Nepoznat predmet");
	zamgerlog ("nepoznat predmet $predmet", 3);
	return;
}

$q12 = myquery("select tippredmeta from akademska_godina_predmet where predmet=$predmet and akademska_godina=$ag");
if (mysql_num_rows($q10)<1) {
	biguglyerror("Nepoznat predmet");
	zamgerlog ("nepoznat predmet $predmet", 3);
	return;
}
$tippredmeta = mysql_result($q12,0,0);

$q15 = myquery("select naziv from akademska_godina where id=$ag");
if (mysql_num_rows($q15)<1) {
	biguglyerror("Nepoznata akademska godina");
	zamgerlog ("nepoznat akademska godina $ag", 3);
	return;
}
?>
	<p>&nbsp;</p>
	<h1><?=mysql_result($q10,0,0)?> <?=mysql_result($q15,0,0)?></h1>
	<h3>Sumarna statistika za sve ispite</h3>
	<p>(Detaljnije informacije možete dobiti koristeći Puni izvještaj)</p>
<?




// Tijelo izvjestaja


// Osnovne statistike
$q30 = myquery("select sp.student from student_predmet as sp, ponudakursa as pk where sp.predmet=pk.id and pk.predmet=$predmet and pk.akademska_godina=$ag");
$slusa_predmet = mysql_num_rows($q30);

$q40 = myquery("select id from ispit where predmet=$predmet and akademska_godina=$ag");
$odrzano_ispita = mysql_num_rows($q40);

$upisano_puta=array();
$maxput=0;
$upisano_puta[0]=$upisano_puta[1]=$upisano_puta[3]=$upisano_puta[4]=$upisano_puta[5]=0;

// Ako nije održan nijedan ispit, ipak je dobro da vidimo neke stastistike
//if ($odrzano_ispita>0) {

	// Spisak ispita
	$ispiti=array();
	while ($r40 = mysql_fetch_row($q40)) array_push($ispiti,$r40[0]);

	// Spisak komponenti
	$knazivi=$kprolaz=$ktip=$kpolozilo=$kfalisamo=array();
	$q50 = myquery("select k.id, k.gui_naziv, k.prolaz, k.tipkomponente, k.uslov from komponenta as k, tippredmeta_komponenta as tpk where tpk.tippredmeta=$tippredmeta and tpk.komponenta=k.id and k.gui_naziv != 'Usmeni'");
	while ($r50 = mysql_fetch_row($q50)) {
		$knazivi[$r50[0]]=$r50[1]; // k.gui_naziv
		$kprolaz[$r50[0]]=$r50[2]; // k.prolaz
		$ktip[$r50[0]]=$r50[3]; // k.tipkomponente
		$kpolozilo[$r50[0]]=0;
		$kuslov[$r50[0]]=$r50[4];
	}

	// Prolazimo kroz studente
	$uslov40=$uslov35=$uslov0=$nisu_izlazili=$polozilo=$integralno=$usmeni=$puk=0;
	$uslovkomponente=0;
	while ($r30 = mysql_fetch_row($q30)) {
		$student = $r30[0];
		$uslovUslov=1;

		// Da li je polozio predmet?
		$q52 = myquery("select count(*) from konacna_ocjena where student=$student and predmet=$predmet and akademska_godina=$ag and ocjena>5");
		$polozio_predmet = mysql_result($q52,0,0);
		if ($polozio_predmet>0) $polozilo++;

		// Odredjujem ponudukursa
		$q55 = myquery("select pk.id from ponudakursa as pk, student_predmet as sp where sp.student=$student and sp.predmet=pk.id and pk.predmet=$predmet and pk.akademska_godina=$ag");
		$ponudakursa = mysql_result($q55,0,0);

		// Koliko puta je slusao predmet?
		$q58 = myquery("select count(*) from student_predmet as sp, ponudakursa as pk where sp.student=$student and sp.predmet=pk.id and pk.predmet=$predmet and pk.akademska_godina<$ag");
		$puta = intval(mysql_result($q58,0,0));
		if ($puta>$maxput) $maxput=$puta;
		$upisano_puta[$puta]++;

		// Komponente
		$sumbodovi=$pao=0;
		$komponente=$polozene_komponente=array();
		foreach($ktip as $komponenta=>$tip) {
			$q60 = myquery("select bodovi from komponentebodovi where student=$student and predmet=$ponudakursa and komponenta=$komponenta");
			if (mysql_num_rows($q60)>0) {
				$komponente[$komponenta]=1;
				$bodovi = mysql_result($q60,0,0);
				if ($tip==1 || $tip==2) $izasao++;
				if ($kprolaz[$komponenta]==0) {
					$kpolozilo[$komponenta]++;
					$polozene_komponente[$komponenta]=1;
				}
				else if ($bodovi>=$kprolaz[$komponenta]) {
					$kpolozilo[$komponenta]++;
					$polozene_komponente[$komponenta]=1;
				}
				else {
					$pao++;
					if($kuslov[$komponenta]==1)
						$uslovUslov=0;
				}

				$sumbodovi += $bodovi;

			// Ako student nije imao bodova, neće postojati zapis u tabeli komponentebodovi
			} else if ($kprolaz[$komponenta]==0) {
				// Komponenta ne traži bodove za prolaz
				$kpolozilo[$komponenta]++;
				$polozene_komponente[$komponenta]=1;

			} else if ($tip!=2) { // tip 2 = integralni ispit
				$pao++;
				if($kuslov[$komponenta]==1)
					$uslovUslov=0;
			}
		}

		// Da li je zadovoljio uslove?
		if ($pao==0) {
			if ($sumbodovi>=40) $uslov40++;
			if ($sumbodovi>=35) $uslov35++;
			if($uslovUslov==1) $uslovkomponente++;
			$uslov0++;
			if ($polozio_predmet==0) $usmeni++;

		} else if ($pao==1) {
			// Studenti kojima je ostao samo jedan ispit i koji
			if ($polozio_predmet==0) {
				foreach ($ktip as $komponenta => $tip) {
					if ($tip!=1) continue;
					if ($polozene_komponente[$komponenta]!=1)
						$kfalisamo[$komponenta]++;
				}
			}

		// PUK
		} else if ($sumbodovi<20) $puk++;

		// Ostali izlaze integralno
		else if ($polozio_predmet==0) $integralno++;

		// Studenti koji nikada nisu izašli niti na jedan ispit
		if ($izasao==0) $nisu_izlazili++;
	}

	$stvarno_slusa = $slusa_predmet-$nisu_izlazili;
//}



?>
<p>Ukupno upisalo predmet: <b><?=$slusa_predmet?></b> studenata.<br/>
<ul>
<?

for ($i=0; $i<=$maxput; $i++) {
	if ($upisano_puta[$i]==0) continue;
	print "<li>".($i+1).". put: <b>".$upisano_puta[$i]."</b> studenata</li>\n";
}

print "</ul>\n";

if ($odrzano_ispita==0) {
	?>Nije održan nijedan ispit.</p>
	<p>Položilo (konačna ocjena 6 ili više): <b><?=$polozilo?></b> studenata (<b><?=procenat($polozilo,$slusa_predmet)?></b>).</p><?
	return;

} else {
	?>
	Nije izašlo ni na jedan ispit: <b><?=$nisu_izlazili?></b> studenata.<br/>
	Položilo (konačna ocjena 6 ili više): <b><?=$polozilo?></b> studenata (<b><?=procenat($polozilo,$slusa_predmet)?></b>).<br/>
	Zadovoljilo uslove za usmeni *:<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;svi ispiti i min. 40 bodova: <b><?=$uslov40?></b> studenata (<b><?=procenat($uslov40,$slusa_predmet)?></b>).<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;svi ispiti i min. 35 bodova: <b><?=$uslov35?></b> studenata (<b><?=procenat($uslov35,$slusa_predmet)?></b>).<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;svi ispiti: <b><?=$uslov0?></b> studenata (<b><?=procenat($uslov0,$slusa_predmet)?></b>).<br/>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;svi uslovni ispiti: <b><?=$uslovkomponente?></b> studenata (<b><?=procenat($uslovkomponente,$slusa_predmet)?></b>).<br/>
	<br/>
	<?

	// Komponente
	foreach ($ktip as $komponenta=>$tip) {
		if ($kpolozilo[$komponenta]==0) continue; // ova komponenta nije u funkciji
		if ($tip==1 || $tip==2) {
			?>
			Položilo <?=$knazivi[$komponenta]?> ispit: <b><?=$kpolozilo[$komponenta]?></b> studenata  (<b><?=procenat($kpolozilo[$komponenta],$slusa_predmet)?></b>).<br/>
			<?
		}
	}

	print "<br/>\n";

	// Ostalo samo
	foreach ($ktip as $komponenta=>$tip) {
		if ($kpolozilo[$komponenta]==0) continue; // ova komponenta nije u funkciji
		if ($tip==1) {
			if ($kfalisamo[$komponenta]==0) $kfalisamo[$komponenta]="0";
			?>
			Ostao samo <?=$knazivi[$komponenta]?> ispit: <b><?=$kfalisamo[$komponenta]?></b> studenata.<br/>
			<?
		}
	}

	?>
	Ostao integralni ispit: <b><?=$integralno?></b> studenata.<br/>
	Ostao usmeni ispit**: <b><?=$usmeni?></b> studenata.<br/>
	Ponovo upisuje kurs***: <b><?=$puk?></b> studenata.<br/>
	</p>

	<p><b>Napomene:</b><br>
	* - Pod "uslov za usmeni" misli se na uobičajenu šemu dva parcijalna ispita + jedan integralni ispit.<br>
	** - Ovaj broj je određen pod pretpostavkom da ne postoji minimalan broj bodova kao uslov za usmeni ispit. Ukoliko postoji takav uslov, profesor posebno definiše na koji način ovi studenti mogu prikupiti preostale potrebne bodove.<br>
	*** - Studenti koji nisu skupili 20 bodova ne mogu pristupiti popravnom ispitu. Ukoliko se ovo pravilo ne odnosi na ovaj predmet, ove studente treba pribrojiti studentima koji izlaze na ispit integralno.</p>
	<?
	return;
}


}

?>