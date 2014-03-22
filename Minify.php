<?php

class Minify {

	private $validExtensions = ['css', 'js'];

	private $path            = '';
	private $extension       = '';
	private $content         = '';
	private $contentMinify   = '';

	/**
	 * Minifier un fichier js ou css
	 * @param  [string] $path : chemin du fichier
	 * @return boolean
	 */
	public function minify_file($path) {
		$this->path = $path;

		if ( ! $this->_isValidFile() ) {
			return false;
		}

		$this->_loadContent();
		$this->_minify();

		return $this->contentMinify;
	}

	/**
	 * Indique si le fichier envoyé est valide
	 * @return boolean
	 */
	private function _isValidFile() {
		$this->_loadExtension();

		return in_array($this->extension, $this->validExtensions);
	}

	/**
	 * Récupère l'extension du fichier et la stocke en paramètre de la classe
	 * @return void
	 */
	private function _loadExtension() {
		$SplFileInfo     = new SplFileInfo($this->path);
		$this->extension = $SplFileInfo->getExtension();
	}

	/**
	 * Récupère le contenu et le stocke en paramètre de la classe
	 * @return void
	 */
	private function _loadContent() {
		$file          = fopen($this->path, 'r');
		$this->content = $this->contentMinify = fread($file, filesize($this->path));
	}

	/**
	 * Lance les opérations de minification des fichiers
	 * @return void
	 */
	private function _minify() {
		$this->_remove_comments();
		$this->_remove_tabsAndLinefeed();
		$this->_remove_spaces();
	}

	/**
	 * Supprime les commentaires
	 * @return void
	 */
	private function _remove_comments() {
		$this->contentMinify = preg_replace('/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/', '', $this->contentMinify);
	}

	/**
	 * Supprime les tabulations, retour à la ligne, retour chariot
	 * @return void
	 */
	private function _remove_tabsAndLinefeed() {
		$this->contentMinify = str_replace(["\t", "\n", "\r"], '', $this->contentMinify);
	}

	/**
	 * Supprime les espaces inutile
	 * @return void
	 */
	private function _remove_spaces() {
		$this->contentMinify = str_replace([' {', '{ '], '{', $this->contentMinify);
		$this->contentMinify = str_replace([' }', '} '], '}', $this->contentMinify);
		$this->contentMinify = str_replace([' ;', '; '], ';', $this->contentMinify);
		$this->contentMinify = str_replace([' (', '( '], '(', $this->contentMinify);
		$this->contentMinify = str_replace([' )', ') '], ')', $this->contentMinify);
		$this->contentMinify = str_replace([' ,', ', '], ',', $this->contentMinify);
		$this->contentMinify = str_replace([' :', ': '], ':', $this->contentMinify);
		$this->contentMinify = str_replace([' =', '= '], '=', $this->contentMinify);
		$this->contentMinify = str_replace([' ==', '== '], '==', $this->contentMinify);
		$this->contentMinify = str_replace([' ===', '=== '], '===', $this->contentMinify);
	}
}

?>