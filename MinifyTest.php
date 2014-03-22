<?php

class MinifyTest extends PHPUnit_Framework_TestCase {

	const PATH_FILES = '/tmp/phpunit';

	public function setUp() {
		require_once 'Minify.php';
		$this->Minify = new Minify();

		if ( ! is_dir(self::PATH_FILES) ) {
			mkdir(self::PATH_FILES, 0777, true);
		}
	}

	/**
	 * Supprime tous les fichiers de test
	 * @return [void]
	 */
	private function _remove_test_files() {
		$dir = opendir(self::PATH_FILES);
		
		while ( false !== ( $file = readdir($dir) ) ) {
			$path = self::PATH_FILES."/".$file;

			if ( ($file != '..') && ($file != '.') ) {
				unlink($path);
			}
		}
		closedir($dir);
	}

	public function tearDown() {
		$this->_remove_test_files();
	}

	/**
	 * Permet de créer un fichier avec du contenu
	 * @param  [string] $name : nom du fichier (test.css)
	 * @param  [text] $content : contenu du fichier
	 * @return [void]
	 */
	private function _createFileForTest($name, $content) {
		$file = fopen(self::PATH_FILES.'/'.$name, "w+");
		fputs($file, $content);
	}

	public function test_siLeFichierEstUnCSSOnTraiteEtOnRetourneTrue() {
		$file_name = 'test.css';
		$this->_createFileForTest($file_name, 'a');
		$this->assertEquals('a', $this->Minify->minify_file(self::PATH_FILES.'/'.$file_name) );
	}

	public function test_siLeFichierEstUnJSOnTraiteEtOnRetourneTrue() {
		$file_name = 'test.js';
		$this->_createFileForTest($file_name, 'a');
		$this->assertEquals('a', $this->Minify->minify_file(self::PATH_FILES.'/'.$file_name) );
	}

	public function test_siLeFichierNEstNiUnJSNiUnCSSFalse() {
		$file_name = 'test.txt';
		$this->_createFileForTest($file_name, 'du texte');
		$this->assertFalse( $this->Minify->minify_file(self::PATH_FILES.'/'.$file_name) );
	}

	public function test_css_supprimerLesCommentaires() {
		$file_name = 'test.css';
		$content = "/* commentaire */.class{color:red;}";
		$this->_createFileForTest($file_name, $content);

		$expected = ".class{color:red;}";
		$result = $this->Minify->minify_file(self::PATH_FILES.'/'.$file_name);

		$this->assertEquals($expected, $result);
	}

	public function test_js_supprimerLesCommentairesInline() {
		$file_name = 'test.js';
		$content = "var a=1;// commentaire";
		$this->_createFileForTest($file_name, $content);

		$expected = "var a=1;";
		$result = $this->Minify->minify_file(self::PATH_FILES.'/'.$file_name);

		$this->assertEquals($expected, $result);
	}

	public function test_css_supprimerLesTabulation() {
		$file_name = 'test.css';
		$content = "\t.class{color:red;}";
		$this->_createFileForTest($file_name, $content);

		$expected = ".class{color:red;}";
		$result = $this->Minify->minify_file(self::PATH_FILES.'/'.$file_name);

		$this->assertEquals($expected, $result);
	}

	public function test_css_supprimerLesFinDeLigne() {
		$file_name = 'test.css';
		$content = ".class{color:red;}\n.class2{color:blue;}";
		$this->_createFileForTest($file_name, $content);

		$expected = ".class{color:red;}.class2{color:blue;}";
		$result = $this->Minify->minify_file(self::PATH_FILES.'/'.$file_name);

		$this->assertEquals($expected, $result);
	}

	public function test_css_supprimerLesRetourALaLigne() {
		$file_name = 'test.css';
		$content = ".class{color:red;}\r.class2{color:blue;}";
		$this->_createFileForTest($file_name, $content);

		$expected = ".class{color:red;}.class2{color:blue;}";
		$result = $this->Minify->minify_file(self::PATH_FILES.'/'.$file_name);

		$this->assertEquals($expected, $result);
	}

	public function test_css_supprimerLesEnchainemensDeRetourALaligneEtFinDeLigne() {
		$file_name = 'test.css';
		$content = ".class{color:red;}\r\n.class2{color:blue;}";
		$this->_createFileForTest($file_name, $content);

		$expected = ".class{color:red;}.class2{color:blue;}";
		$result = $this->Minify->minify_file(self::PATH_FILES.'/'.$file_name);

		$this->assertEquals($expected, $result);
	}

	public function test_css_supprimerLesEspacesInutilesAccolades() {
		$file_name = 'test.css';
		$content = ".class { color:red; }";
		$this->_createFileForTest($file_name, $content);

		$expected = ".class{color:red;}";
		$result = $this->Minify->minify_file(self::PATH_FILES.'/'.$file_name);

		$this->assertEquals($expected, $result);
	}

	public function test_js_supprimerLesEspacesInutilesPointsVirgule() {
		$file_name = 'test.js';
		$content = "var test='hello'; ";
		$this->_createFileForTest($file_name, $content);

		$expected = "var test='hello';";
		$result = $this->Minify->minify_file(self::PATH_FILES.'/'.$file_name);

		$this->assertEquals($expected, $result);
	}

	public function test_js_supprimerLesEspacesInutilesParentheses() {
		$file_name = 'test.js';
		$content = "if ( ( i==1 )&&( j==1 ) ){";
		$this->_createFileForTest($file_name, $content);

		$expected = "if((i==1)&&(j==1)){";
		$result = $this->Minify->minify_file(self::PATH_FILES.'/'.$file_name);

		$this->assertEquals($expected, $result);
	}

	public function test_js_supprimerLesEspacesInutilesVirgulse() {
		$file_name = 'test.js';
		$content = "var test={1, 2 ,3 , 4};";
		$this->_createFileForTest($file_name, $content);

		$expected = "var test={1,2,3,4};";
		$result = $this->Minify->minify_file(self::PATH_FILES.'/'.$file_name);

		$this->assertEquals($expected, $result);
	}

	public function test_js_supprimerLesEspacesInutilesSimpleEgale() {
		$file_name = 'test.js';
		$content = "var test = 2;";
		$this->_createFileForTest($file_name, $content);

		$expected = "var test=2;";
		$result = $this->Minify->minify_file(self::PATH_FILES.'/'.$file_name);

		$this->assertEquals($expected, $result);
	}

	public function test_js_supprimerLesEspacesInutilesDoubleEgale() {
		$file_name = 'test.js';
		$content = "if(i == 1){";
		$this->_createFileForTest($file_name, $content);

		$expected = "if(i==1){";
		$result = $this->Minify->minify_file(self::PATH_FILES.'/'.$file_name);

		$this->assertEquals($expected, $result);
	}

	public function test_js_supprimerLesEspacesInutilesTripleEgale() {
		$file_name = 'test.js';
		$content = "if(i === 1){";
		$this->_createFileForTest($file_name, $content);

		$expected = "if(i===1){";
		$result = $this->Minify->minify_file(self::PATH_FILES.'/'.$file_name);

		$this->assertEquals($expected, $result);
	}

	public function test_css_supprimerLesEspacesInutilesDeuxPoints() {
		$file_name = 'test.css';
		$content = ".class{color: red;}.class2{color : blue;}";
		$this->_createFileForTest($file_name, $content);

		$expected = ".class{color:red;}.class2{color:blue;}";
		$result = $this->Minify->minify_file(self::PATH_FILES.'/'.$file_name);

		$this->assertEquals($expected, $result);
	}

	public function test_css_exampleComplet() {
		$file_name = 'test.css';
		$content = ".class {\ncolor: red;\n}\n/*Un commentaire*/\n#monId .class2 > a\n{\ncolor : blue;\ntext-align:center;\n}";
		$this->_createFileForTest($file_name, $content);

		$expected = ".class{color:red;}#monId .class2 > a{color:blue;text-align:center;}";
		$result = $this->Minify->minify_file(self::PATH_FILES.'/'.$file_name);

		$this->assertEquals($expected, $result);
	}

	public function test_js_exampleComplet() {
		$file_name = 'test.css';
		$content = "function test () {\nif ( a == b ) {\nreturn [1, 2, 3];\n}\n} ";
		$this->_createFileForTest($file_name, $content);

		$expected = "function test(){if(a==b){return [1,2,3];}}";
		$result = $this->Minify->minify_file(self::PATH_FILES.'/'.$file_name);

		$this->assertEquals($expected, $result);
	}


}

?>