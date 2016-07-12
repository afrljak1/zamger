<?

// WS/LABGRUPA - spisak grupa na predmetu ili studenata u grupi



function ws_labgrupa() {
	global $userid, $user_nastavnik, $user_siteadmin;

	// Listanje grupa i studenata u grupama mogu raditi osobe u statusu nastavnika na predmetu
	if (!$user_siteadmin && !$user_nastavnik) {
		print json_encode( array( 'success' => 'false', 'code' => 'ERR002', 'message' => 'Permission denied' ) );
		return;
	} 
	
	$rezultat = array( 'success' => 'true', 'data' => array() );
	
	if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0) {
		$grupa = intval($_REQUEST['id']);
		
		if ($user_nastavnik) {
			$q20 = myquery("select np.nivo_pristupa from nastavnik_predmet np, labgrupa lg where np.nastavnik=$userid and np.predmet=lg.predmet and np.akademska_godina=lg.akademska_godina and lg.id=$grupa");
			if (mysql_num_rows($q20)<1) {
				print json_encode( array( 'success' => 'false', 'code' => 'ERR002', 'message' => 'Permission denied' ) );
				return;
			}
		}
		
		$q20 = myquery("SELECT naziv FROM labgrupa WHERE id=$grupa");
		$rezultat['data']['naziv'] = mysql_result($q20,0,0);
		
		$q10 = myquery("SELECT o.ime, o.prezime, o.brindexa, a.login, o.id FROM osoba o, student_labgrupa as sl, auth a WHERE sl.labgrupa=$grupa AND sl.student=o.id AND o.id=a.id ORDER BY o.prezime, o.ime");
		$studenti = array();
		while ($r10 = mysql_fetch_row($q10))
			$studenti[$r10[4]] = array( 'ime' => $r10[0], 'prezime' => $r10[1], 'brindexa' => $r10[2], 'login' => $r10[3] );
		$rezultat['data']['studenti'] = $studenti;
		
		print json_encode($rezultat); 
		return; 
	}
	
	if (isset($_REQUEST['predmet'])) {
		$predmet = intval($_REQUEST['predmet']);
		$ag = intval($_REQUEST['ag']);
		if ($ag == 0) { // ag nije zadana, uzimamo aktuelnu
			$q10 = myquery("SELECT id FROM akademska_godina WHERE aktuelna=1");
			$ag = mysql_result($q10,0,0);
		}
		
		if ($user_nastavnik) {
			$q20 = myquery("select nivo_pristupa from nastavnik_predmet where nastavnik=$userid and predmet=$predmet and akademska_godina=$ag");
			if (mysql_num_rows($q20)<1) {
				print json_encode( array( 'success' => 'false', 'code' => 'ERR002', 'message' => 'Permission denied' ) );
				return;
			}
		}
	
		$q100 = myquery("SELECT lg.id, lg.naziv FROM labgrupa lg WHERE lg.predmet=$predmet AND lg.akademska_godina=$ag");
		while ($r100 = mysql_fetch_row($q100))
			$rezultat['data'][$r100[0]] = $r100[1];
		
		print json_encode($rezultat); 
		return; 
	}
	
	print json_encode(array( 'success' => 'false', 'code' => 'ERR006', 'message' => 'Not implemented yet' ) );

}


?>