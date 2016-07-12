<?

// WS/PRIJAVA_ISPITA - prijavljivanje i odjavljivanje sa ispita



function ws_prijava_ispita() {
	global $userid, $user_student;

	$rezultat = array( 'success' => 'true', 'data' => array() );

	// Odjava sa termina
	if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
		$termin = intval($_REQUEST['termin']);
		$q10 = myquery("select i.predmet, i.akademska_godina, UNIX_TIMESTAMP(ist.deadline) from student_ispit_termin as sit, 
				ispit_termin as ist, ispit as i where sit.student=$userid and sit.ispit_termin=$termin and ist.id=$termin and ist.ispit=i.id");
		if (mysql_num_rows($q10)<1) {
			$rezultat = array( 'success' => 'false', 'code' => 'ERR902', 'message' => 'Niste prijavljeni na termin' );
			echo json_encode($rezultat);
			return;
		}
		
		$predmet = mysql_result($q10,0,0);
		$q20 = myquery("DELETE FROM student_ispit_termin WHERE student=$userid AND ispit_termin=$termin");
		zamgerlog("odjavljen sa ispita (pp$predmet)", 2);
		
		$rezultat['message'] = "Student odjavljen sa ispita";
		echo json_encode($rezultat);
		return;
	}

	// Prijava na termin
	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$termin = intval($_REQUEST['termin']);
		
		// Da li je student upisan na predmet?
		$q30 = myquery ("SELECT i.predmet, it.maxstudenata, it.ispit 
		FROM ispit_termin as it, ispit as i, ponudakursa as pk, student_predmet as sp 
		WHERE it.id=$termin AND it.ispit=i.id AND i.predmet=pk.predmet AND pk.akademska_godina=i.akademska_godina and pk.id=sp.predmet AND sp.student=$userid");
		if (mysql_num_rows($q30)<1) {
			$rezultat = array( 'success' => 'false', 'code' => 'ERR903', 'message' => 'Niste upisani na predmet' );
			echo json_encode($rezultat);
			return;
		}
		$predmet = mysql_result($q30,0,0); // koristi se kod upisa u log
		$maxstudenata = mysql_result($q30,0,1);
		$ispit = mysql_result($q30,0,2);
		
		//provjera da li ima dovoljno termina za prijavu
		$q40 = myquery("SELECT count(*) FROM student_ispit_termin WHERE ispit_termin=$termin");
		if (mysql_result($q40,0,0) >= $maxstudenata) {
			$rezultat = array( 'success' => 'false', 'code' => 'ERR904', 'message' => 'Ispitni termin je popunjen' );
			echo json_encode($rezultat);
			return;
		} else {
			// Da li je već prijavio termin na istom ispitu?
			$q65 = myquery("select count(*) from student_ispit_termin as sit, 
			ispit_termin as it where sit.student=$userid and sit.ispit_termin=it.id and it.ispit=$ispit");
			if (mysql_result($q65,0,0)>0) {
				$rezultat = array( 'success' => 'false', 'code' => 'ERR905', 'message' => 'Već ste prijavljeni na neki termin za ovaj ispit' );
				echo json_encode($rezultat);
				return;
			} else {
				$q70 = myquery("INSERT INTO student_ispit_termin (student,ispit_termin) VALUES ($userid, $termin)");
				zamgerlog("prijavljen na termin za ispit/događaj (pp$predmet)", 2);
				
				$rezultat['message'] = "Student prijavljen na ispit";
				echo json_encode($rezultat);
			}
		}
		return;
	}

	// Default akcija - prikaz spiska termina
	$q10=myquery("SELECT it.id, p.id, k.id, i.id, p.naziv, UNIX_TIMESTAMP(it.datumvrijeme), UNIX_TIMESTAMP(it.deadline), k.gui_naziv, it.maxstudenata, p.kratki_naziv
	FROM ispit_termin as it, ispit as i, predmet as p, komponenta as k, osoba as o, student_predmet as sp, ponudakursa as pk 
	WHERE it.ispit=i.id AND i.komponenta=k.id AND i.predmet=p.id AND pk.predmet=p.id and pk.akademska_godina=i.akademska_godina 
	AND o.id=$userid AND o.id=sp.student AND sp.predmet=pk.id AND it.datumvrijeme>=NOW() ORDER BY it.datumvrijeme");
	$termini = array();
	while ($r10=mysql_fetch_row($q10)) {
		$termin = array();
		
		$id_termina = $r10[0];
		$predmet = $r10[1];
		$ispit = $r10[3];
		$max_studenata = $r10[8];
		
		$termin['predmet'] = $r10[1];
		$termin['komponenta'] = $r10[2];
		$termin['ispit'] = $r10[3];
		$termin['naziv_predmeta'] = $r10[4];
		$termin['vrijeme'] = date("d.m.Y. H:i",date($r10[5]));
		$termin['rok'] = date("d.m.Y. H:i",date($r10[6]));
		$termin['naziv_komponente'] = $r10[7];
		
		// Ne vraćamo ispite za predmete koje je student položio
		$q20 = myquery("select count(*) from konacna_ocjena where student=$userid and predmet=$predmet and ocjena>=6");
		if (mysql_result($q20,0,0)>0) continue;
		
		// Da li je termin popunjen?
		$termin['popunjen'] = "false";
		$q30 = myquery("SELECT count(*) FROM student_ispit_termin WHERE ispit_termin=$id_termina");
		if (mysql_result($q30,0,0)>=$max_studenata) 
			$termin['popunjen'] = "true"; 
		
		// Da li je već prijavio ovaj ispit u nekom od termina?
		$termin['prijavljen'] = "false";
		$q40 = myquery("select count(*) from student_ispit_termin as sit, 
						ispit_termin as it where sit.student=$userid and sit.ispit_termin=it.id and it.ispit=$ispit");
		if (mysql_result($q40,0,0)>0) {
			$q45 = myquery("SELECT COUNT(*) FROM student_ispit_termin WHERE student=$userid AND ispit_termin=$id_termina");
			if (mysql_result($q45,0,0) > 0) 
				$termin['prijavljen'] = "true";
			else 
				$termin['prijavljen'] = "neki_drugi";
		}

		if ($r10[6]<time())
			$termin['rok_istekao'] = "true";
		else
			$termin['rok_istekao'] = "false";
			
		$termini[$id_termina] = $termin;
	}

	$rezultat['data']['termini'] = $termini;
	echo json_encode($rezultat);
}


?>