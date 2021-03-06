<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php

error_reporting(E_ALL & ~E_NOTICE);
@apache_setenv('no-gzip', 1);
define("DEBUG", 0);

include("lib.database.php");

/**
 * Greek string to uppercase
 *
 * Correctly converts greek letters to uppercase.
 *
 * @access	public
 * @param	string
 * @return	string
 */
function grstrtoupper($string) {
  $latin_check = '/[\x{0030}-\x{007f}]/u';

  if (preg_match($latin_check, $string)) {
    $string = strtoupper($string);
  }

  $letters = array('α', 'β', 'β', 'B', 'γ', 'δ', 'ε', 'ζ', 'η', 'θ', 'ι', 'κ', 'λ', 'μ', 'ν', 'ξ', 'ο', 'π', 'ρ', 'σ', 'τ', 'υ', 'φ', 'χ', 'ψ', 'ω');
  $letters_accent = array('ά', 'έ', 'ή', 'ί', 'ό', 'ύ', 'ώ');
  $letters_upper_accent = array('Ά', 'Έ', 'Ή', 'Ί', 'Ό', 'Ύ', 'Ώ');
  $letters_upper_solvents = array('ϊ', 'ϋ');
  $letters_other 							= array('ς');

  $letters_to_uppercase = array('Α', 'Β', 'Β', 'Β', 'Γ', 'Δ', 'Ε', 'Ζ', 'Η', 'Θ', 'Ι', 'Κ', 'Λ', 'Μ', 'Ν', 'Ξ', 'Ο', 'Π', 'Ρ', 'Σ', 'Τ', 'Υ', 'Φ', 'Χ', 'Ψ', 'Ω');
  $letters_accent_to_uppercase 			= array('Α', 'Ε', 'Η', 'Ι', 'Ο', 'Υ', 'Ω');
  $letters_upper_accent_to_uppercase 		= array('Α', 'Ε', 'Η', 'Ι', 'Ο', 'Υ', 'Ω');
  $letters_upper_solvents_to_uppercase 	= array('Ι', 'Υ');
  $letters_other_to_uppercase 			= array('Σ');

  $lowercase = array_merge($letters, $letters_accent, $letters_upper_accent, $letters_upper_solvents, $letters_other);
  $uppercase = array_merge($letters_to_uppercase, $letters_accent_to_uppercase, $letters_upper_accent_to_uppercase, $letters_upper_solvents_to_uppercase, $letters_other_to_uppercase);

  $uppecase_string = str_replace($lowercase, $uppercase, $string);

  return $uppecase_string;

}



$DB = new db("references", "localhost", "root", "*****");
$DB->q('SET NAMES %s', 'UTF8');

global $fek, $nomos;

$etos = $DB->q('COLUMN SELECT reg_exp FROM regexp_tests WHERE outputs = %s;', 'etos');
$arthro = $DB->q('COLUMN SELECT reg_exp FROM regexp_tests WHERE outputs = %s;', 'arthro');
$nomos = $DB->q('COLUMN SELECT reg_exp FROM regexp_tests WHERE outputs = %s;', 'nomos');
$periptwsi = $DB->q('COLUMN SELECT reg_exp FROM regexp_tests WHERE outputs = %s;', 'periptwsi');
$paragrafos = $DB->q('COLUMN SELECT reg_exp FROM regexp_tests WHERE outputs = %s;', 'paragrafos');
$edafio = $DB->q('COLUMN SELECT reg_exp FROM regexp_tests WHERE outputs = %s;', 'edafio');
$pd = $DB->q('COLUMN SELECT reg_exp FROM regexp_tests WHERE outputs = %s;', 'pd');
$fek = $DB->q('COLUMN SELECT reg_exp FROM regexp_tests WHERE outputs = %s;', 'fek');
$apofasi = $DB->q('COLUMN SELECT reg_exp FROM regexp_tests WHERE outputs = %s;', 'apofasi');
$diataksi = $DB->q('COLUMN SELECT reg_exp FROM regexp_tests WHERE outputs = %s;', 'diataksi');
$basiliko_diatagma = $DB->q('COLUMN SELECT reg_exp FROM regexp_tests WHERE outputs = %s;', 'basiliko_diatagma');

$folder = "/media/Data3/FEK_txt/";
$splitter_pat = "/.*(?<fek>ΦΕΚ +(?<tipos>.*) +(?<arithmos>[0-9]+) (- )*(?<imerominia>[0-9]{1,2}[.−-][0-9]{1,2}[.−-](?<etos>[0-9]{2,4})))/ui";

$it = new RecursiveDirectoryIterator($folder);
$fout = fopen($folder . "all_connections.txt", "w");
fclose($fout);

$sf = 0;

foreach(new RecursiveIteratorIterator($it) as $file) {
  if (strtolower(array_pop(explode('.', $file))) != 'txt')
    continue;
  
  $sf += 1;
  ini_set('max_execution_time', 30);
  // $file = "/media/Data3/FEK_txt/2000.12/ΦΕΚ A 286 - 29.12.2000.txt";
  if ($sf % 100 == 1) {
    echo $sf . " : " . time() . " : " . $file . "<BR>";
    flush();
  }
  
  $export_list = array();
  $matches = array();
  $a = preg_match_all($splitter_pat, $file, $matches, PREG_OFFSET_CAPTURE);
  if (empty($matches['fek'])) {
    continue;
  }
  $matches['tipos'][0][0] = str_replace(".", '', $matches['tipos'][0][0]);
  $matches['tipos'][0][0] = str_replace("-", '', $matches['tipos'][0][0]);
  $matches['tipos'][0][0] = grstrtoupper($matches['tipos'][0][0]);
  
  $cfek_ref = "FEK." . $matches['arithmos'][0][0] . '.' . $matches['tipos'][0][0] . '.' . $matches['etos'][0][0];

  $contents = file_get_contents($file);
  $contents = str_replace("Á", "Α", $contents);
  $contents = str_replace("¢", "Ά", $contents);
  $contents = str_replace("Â", "Β", $contents);
  $contents = str_replace("Ã", "Γ", $contents);
  $contents = str_replace("Ä", "Δ", $contents);
  $contents = str_replace("Å", "Ε", $contents);
  $contents = str_replace("÷", "Έ", $contents);
  $contents = str_replace("¸", "Έ", $contents);
  $contents = str_replace("", "Ζ", $contents);
  $contents = str_replace("Ç", "Η", $contents);
  $contents = str_replace("È", "Θ", $contents);
  $contents = str_replace("É", "Ι", $contents);
  $contents = str_replace("Ê", "Κ", $contents);
  $contents = str_replace("Ë", "Λ", $contents);
  $contents = str_replace("Ì", "Μ", $contents);
  $contents = str_replace("Í", "Ν", $contents);
  $contents = str_replace("Î", "Ξ", $contents);
  $contents = str_replace("Ï", "Ο", $contents);
  $contents = str_replace("Ð", "Π", $contents);
  $contents = str_replace("Ñ", "Ρ", $contents);
  $contents = str_replace("Ó", "Σ", $contents);
  $contents = str_replace("Ô", "Τ", $contents);
  $contents = str_replace("Õ", "Υ", $contents);
  $contents = str_replace("Ö", "Φ", $contents);
  $contents = str_replace("×", "Χ", $contents);
  $contents = str_replace("", "Ψ", $contents);
  $contents = str_replace("Ù", "Ω", $contents);

  $contents = str_replace("á", "α", $contents);
  $contents = str_replace("Ü", "ά", $contents);
  $contents = str_replace("â", "β", $contents);
  $contents = str_replace("ã", "γ", $contents);
  $contents = str_replace("ä", "δ", $contents);
  $contents = str_replace("å", "ε", $contents);
  $contents = str_replace("Ý", "έ", $contents);
  $contents = str_replace("æ", "ζ", $contents);
  $contents = str_replace("ç", "η", $contents);
  $contents = str_replace("Þ", "ή", $contents);
  $contents = str_replace("è", "θ", $contents);
  $contents = str_replace("é", "ι", $contents);
  $contents = str_replace("ß", "ί", $contents);
  $contents = str_replace("ú", "ϊ", $contents);
  $contents = str_replace("ê", "κ", $contents);
  $contents = str_replace("ë", "λ", $contents);
  $contents = str_replace("ì", "μ", $contents);
  $contents = str_replace("í", "ν", $contents);
  $contents = str_replace("î", "ξ", $contents);
  $contents = str_replace("ï", "ο", $contents);
  $contents = str_replace("ü", "ό", $contents);
  $contents = str_replace("ð", "π", $contents);
  $contents = str_replace("ñ", "ρ", $contents);
  $contents = str_replace("ó", "σ", $contents);
  $contents = str_replace("ò", "ς", $contents);
  $contents = str_replace("ô", "τ", $contents);
  $contents = str_replace("õ", "υ", $contents);
  $contents = str_replace("ý", "ύ", $contents);
  $contents = str_replace("û", "ϋ", $contents);
  $contents = str_replace("ö", "φ", $contents);
  $contents = str_replace("Έ", "χ", $contents);
  $contents = str_replace("ø", "ψ", $contents);
  $contents = str_replace("ù", "ω", $contents);
  $contents = str_replace("þ", "ώ", $contents);
  $contents = str_replace("´", "'", $contents);
  $contents = str_replace("΄", "'", $contents);

  $contents = str_replace("", "'", $contents);

  $al = array();
  foreach($fek as $fekpatid => $fekpat) {
    $matches = array();
    $a = preg_match_all($fekpat, $contents, $matches, PREG_OFFSET_CAPTURE);
    if (empty($matches['fek'])) {
      continue;
    }
    
    foreach($matches['fek'] as $id => $rfek) {
      $md = md5($rfek[0]);
      if (isset($al[$md]))
        continue;
      $al[$md] = 'FEK';
      
      $arithmos = $matches['arithmos'][$id][0];
      $tipos = $matches['tipos'][$id][0];
      $tipos = str_replace('´', '', $tipos);
      $tipos = str_replace('΄', '', $tipos);
      $tipos = str_replace('’', '', $tipos);
      $tipos = str_replace("'", '', $tipos);
      $tipos = str_replace(".", '', $tipos);
      $tipos = str_replace("-", '', $tipos);
      $tipos = grstrtoupper($tipos);
      
      
      $imerominia = $matches['imerominia'][$id][0];
      $imerominia = str_replace('-', '.', $imerominia);
      $imerominia = str_replace('−', '.', $imerominia);
      $parts = explode('.', $imerominia);
      if (count($parts) > 1)
        $etos = $parts[2];
      else
        $etos = $parts[0];
      
      if (strlen($etos) <= 2 && intval($etos < 20))
        $etos = intval($etos) + 2000;
      else if (strlen($etos) <= 2 && intval($etos) < 100)
        $etos = intval($etos) + 1900;
      
      $l = <<<EOD
<a href="/fek/{$imerominia}/{$tipos}/{$arithmos}" title="ΦΕΚ {$arithmos}/{$tipos}/{$imerominia}">[ΦΕΚ]</a>
EOD;
      $times = substr_count($contents, $rfek[0]);
      $contents = str_replace($rfek[0], $rfek[0] . $l, $contents);
      
      $to_ref = "FEK." . $arithmos . '.' . $tipos . '.' . $etos;
      $export_list[] = implode("\t", array($cfek_ref, $times, $to_ref));
    }
  }

  /*
  $al = array();
  foreach($nomos as $nomospat) {
    $matches = array();
    $a = preg_match_all($nomospat, $contents, $matches, PREG_OFFSET_CAPTURE);
    if (empty($matches['nomos'])) {
      continue;
    }
    // print_r($matches);
    
    foreach($matches['nomos'] as $id => $rfek) {
      $md = md5($rfek[0]);
      if (isset($al[$md]))
        continue;
      $al[$md] = 'NOMOS';
    
      $arithmos = $matches['arithmos'][$id][0];
      $imerominia = $matches['imerominia'][$id][0];
      if (strlen($imerominia) <= 2 && intval($imerominia < 20))
        $imerominia = intval($imerominia) + 2000;
      else if (strlen($imerominia) <= 2 && intval($imerominia) < 100)
        $imerominia = intval($imerominia) + 1900;
        
      $l = <<<EOD
<a href="/nomos/{$imerominia}/{$arithmos}" title="ΝΟΜΟΣ {$arithmos}/{$imerominia}">[ΝΟΜΟΣ]</a>
EOD;
      $contents = str_replace($rfek[0], $rfek[0] . $l, $contents);      
    }
  }
  */
  
  // if (count($export_list) > 3000)
  //    break;
  $fout = fopen($folder . "all_connections.txt", "a");
  fwrite($fout, implode("\n", $export_list) . "\n");
  fclose($fout);
}

// file_put_contents(, implode("\n", $export_list));

// echo $contents;
?>

  </body>
</html>
