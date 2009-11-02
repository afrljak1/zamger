<?php
// IZVJESTAJ/CHART_SEMESTRALNI - stranica koja generi�e grafove za semestralni izvjestaj uz pomoc GD biblioteke

require("../lib/libvedran.php");
require("../lib/zamger.php");
require("../lib/config.php");
dbconnect2($conf_dbhost,$conf_dbuser,$conf_dbpass,$conf_dbdb);


$pitanje = $_GET['pitanje'];
$semestar = $_GET['semestar'];
$studij=$_GET['studij'];

$result2077=myquery("SELECT tekst FROM anketa_pitanje WHERE id=$pitanje");
$title = mysql_result($result2077,0,0);

$l=0;
$predmeti;
// ako je za studij odabrana Prva godina studija onda izbacujemo uslov
// studij iz sljedeceg upita jer nakon zadnjih izmjena u Zamgeru ne postoji 
// vi�e studij PGS vec su studenti odmah razvrstani po smjerovima, na ovaj 
// nacin objedinjujemo razultate svih ponuda kursa za isti predmet
if ($studij == 1)
	$result409=myquery("select p.id, p.kratki_naziv from ponudakursa pk,predmet p where p.id=pk.predmet and semestar = $semestar");
else
	$result409=myquery("select p.id, p.kratki_naziv from ponudakursa pk,predmet p where p.id=pk.predmet and studij = $studij and semestar = $semestar");
while($predmet = mysql_fetch_row($result409)){

	$q6730 = myquery("SELECT sum( b.izbor_id ) / count( * ) FROM anketa_rezultat a, anketa_odgovor_rank b WHERE a.id = b.rezultat AND b.pitanje =$pitanje AND a.predmet =$predmet[0] AND 	
					zavrsena='Y'");
	$data[$l]=mysql_result($q6730,0,0);
	$predmeti[$predmet[1]] =$data[$l] ;
	
	$l++;

}
$prosjek = array_sum($predmeti)/sizeof($predmeti);
// izbacio prosjek ali ako se odkomentarise sljedeca linija koda dodaje se jos jedan dodatni bar u graf sa srednjom vrijedno�cu za to pitanje
// $predmeti['AVG']=$prosjek;

crtaj($predmeti,$title);

function crtaj ($data,$title){
		
		$nval = sizeof($data); // broj vrijednosti
		
		$width = 600;
		$height = 350;
		$image = imagecreate($width, $height);
		
		// boje
		$white = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
		$red = imagecolorallocate($image, 0xFF, 0x22, 0x22);
		$yellow = imagecolorallocate($image, 0xFF, 0xFF, 0x55);
		$navy = imagecolorallocate($image, 0x33, 0x99, 0xCC);
		$black = imagecolorallocate($image, 0x00, 0x00, 0x00);
		$gray = imagecolorallocate($image, 0xC0, 0xC0, 0xC0);
		$green = imagecolorallocate($image, 0x22, 0xCC, 0x22);
		
		// crtanje 
		
		$maxval = max($data); // maximalna vrijednost 
		
		$vmargin = 20; // vertikalna margina za vrh i dno za  x-labele
		$hmargin = 38; // lijeva horizontalna margina za y-labele
		
		$base = floor(($width - $hmargin) / $nval); // distance between columns
		
		$ysize = $height - 2 * $vmargin; // visina prostora za crtanje je visina minus dvije margine
		$xsize = $nval * $base; // velicina prostora za crtanje po x osi
		
		// crtamo okvir
		// imagerectangle(image, tlx, tly, brx, bry, color);
		imagerectangle($image, $hmargin, $vmargin,$hmargin + $xsize, $vmargin + $ysize, $black);
		
		
		/*// naslov
		$titlefont = 3;
		//$title = "Prosjeci za anketu po smjerovima ";
		
		//   velicin naslova u pixelima 
		$txtsz = imagefontwidth($titlefont) * strlen($title);
		
		$xpos = (int)($hmargin + ($xsize - $txtsz)/2); // centriramo naslov
		$xpos = max(1, $xpos); // uvijek da je pozitivan
		$ypos = 3; // udaljenost od vrha
		
		
		//ImageString(image, font, x, y, text, color);
		imagestring($image, $titlefont, $xpos, $ypos, $title , $black);
		*/
		
		// y labele i mrezne linije
		$labelfont = 2;
		$ngrid = 4; // broj mreznih linija
		
		$dydat = 1; // udaljenost izmedju mreznih linija
		$dypix = $ysize / ($ngrid + 1); // udaljenost izmedju mreznih linija u pixelima
		
		for ($i = 0; $i <= ($ngrid + 1); $i++) {
			// iteracija kroz y labele
		
			$ydat = (int)($i * $dydat); // visina mrezne linije u podacima
			$ypos = $vmargin + $ysize - (int)($i*$dypix); // visina mrezne linije u pixelima
		
			$txtsz = imagefontwidth($labelfont) * strlen($ydat); // sirina labele u pixelima
			$txtht = imagefontheight($labelfont); // visina labele u pixelima
		
			$xpos = (int)(($hmargin - $txtsz) / 2); // pozicija po x osi ista za sve ali se centrira ipak
			$xpos = max(1, $xpos); // uvijek pozitivno
		
			imagestring($image, $labelfont, $xpos, $ypos - (int)($txtht/2), $ydat, $black);
			
			// ctramo mreznu liniju osim ako je prvi i zadnji red
			if (!($i == 0) && !($i > $ngrid))
				imageline($image, $hmargin - 3, $ypos, $hmargin + $xsize, $ypos, $gray); 
				
		}
		
		// columns and x labels
		$padding = 10; // half of spacing between columns
		$yscale = $ysize / (($ngrid+1) * $dydat); // pixela po podatkovnoj jedinici
		
		for ($i = 0; list($xval, $yval) = each($data); $i++) {
		
			// vertical columns
			$ymax = $vmargin + $ysize;
			$ymin = $ymax - (int)($yval*$yscale);
			$xmax = $hmargin + ($i+1)*$base - $padding;
			$xmin = $hmargin + $i*$base + $padding;
			
			
			imagefilledrectangle($image, $xmin+1, $ymin+1, $xmax -1, $ymax-1, $navy);
			
			// x labels
			$txtsz = imagefontwidth($labelfont) * strlen($xval);
		
			$xpos = $xmin + (int)(($base - $txtsz) / 2)-5;
			$xpos = max($xmin, $xpos);
			$ypos = $ymax + 3; // distance from x axis
		
			imagestring($image, $labelfont, $xpos, $ypos, $xval, $black);
		} 
		
		// flush image
		header("Content-type: image/gif"); // or "Content-type: image/png"
		imagegif($image); // or imagepng($image)
		imagedestroy($image);
}
?>
