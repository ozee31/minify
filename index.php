<?php 
header('Content-Type: text/html; charset=utf-8');
require_once 'Minify.php';
define('CSS_FILE', 'demo.css');
define('CSS_FILE_MIN', 'demo.min.css');
define('JS_FILE', 'demo.js');
define('JS_FILE_MIN', 'demo.min.js');

// Minification d'une portion de bootstrap.css
if ( file_exists(CSS_FILE) ) {
	$MinifyCss = new Minify();
	$path      = realpath(CSS_FILE); // chemin absolu	
	$cssMin    = $MinifyCss->minify_file($path); // cssMin contient le résultat minifié

	// On ouvre le fichier min.css et on y colle le contenu
	$file = fopen(CSS_FILE_MIN, "w+"); 
	
	if ( fputs($file, $cssMin) !== false ) {
		echo "Le fichier ".CSS_FILE." a été minifié.\n";
	}
}

// Minification d'un module de bootstrap
if ( file_exists(JS_FILE) ) {
	$MinifyJs = new Minify();
	$path      = realpath(JS_FILE); // chemin absolu	
	$jsMin    = $MinifyJs->minify_file($path); // jsMin contient le résultat minifié

	// On ouvre le fichier min.js et on y colle le contenu
	$file = fopen(JS_FILE_MIN, "w+"); 
	
	if ( fputs($file, $jsMin) !== false ) {
		echo "Le fichier ".JS_FILE." a été minifié.\n";
	}
}

?>