<?php

namespace timesplinter\tsfw\i18n\gettext;

use PoParser\Parser;
use timesplinter\tsfw\common\StringUtils;
use timesplinter\tsfw\i18n\common\AbstractTranslator;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2014, TiMESPLiNTER Webdeveloper
 */
class GetTextTranslator extends AbstractTranslator
{
	protected $defaultPluralRule = 'plural=(n != 1)';
	protected $bendTextDomains = array();
	/** @var PoParserInterface|null */
	protected $poParserInterface = null;
	/** @var PoWriterInterface|null */
	protected $poWriterInterface = null;
	
	public function __construct($directory, $defaultCodeSet = 'UTF-8')
	{
		parent::__construct($directory, $defaultCodeSet);
	}
	
	/**
	 * Set the current text domain
	 *
	 * @param string $textDomain The text domain to set active
	 *
	 * @return string
	 */
	public function setTextDomain($textDomain)
	{
		return $this->currentTextDomain = textdomain($textDomain);
	}

	/**
	 * {@inheritdoc}
	 */
	public function bindTextDomain($textDomain, $codeSet = null)
	{
		$this->bendTextDomains[$textDomain] = array();
		
		$textDomainDir = bindtextdomain($textDomain, $this->directory);
		
		$translationFileDir = $textDomainDir . DIRECTORY_SEPARATOR . StringUtils::beforeFirst(setlocale(LC_MESSAGES, '0'), '.') . DIRECTORY_SEPARATOR . 'LC_MESSAGES';
		
		$moFilePath = $translationFileDir . DIRECTORY_SEPARATOR . $textDomain . '.mo';
		$poFilePath = $translationFileDir . DIRECTORY_SEPARATOR . $textDomain . '.po';

		if(file_exists($moFilePath) === true) {
			$this->bendTextDomains[$textDomain]['file_path'] = $moFilePath;
			$this->bendTextDomains[$textDomain]['type'] = 'mo';
		} elseif(file_exists($poFilePath) === true && $this->poParserInterface instanceof PoParserInterface === true) {
			$this->bendTextDomains[$textDomain]['file_path'] = $poFilePath;
			$this->bendTextDomains[$textDomain]['type'] = 'po';
			$this->bendTextDomains[$textDomain]['plural_expr'] = false;

			$this->bendTextDomains[$textDomain]['entries'] = $this->poParserInterface->extract($poFilePath);
			$this->bendTextDomains[$textDomain]['plural_expr'] = '$' . $this->defaultPluralRule . ';'; // Default plural rule
			$this->bendTextDomains[$textDomain]['plurals'] = 2;

			if(isset($this->bendTextDomains[$textDomain]['entries']['']) === true) {
				foreach($this->bendTextDomains[$textDomain]['entries']['']['msgstr'] as $meta) {
					if(preg_match('/Plural-Forms:\s+nplurals=(\d+);\s+(plural=[^;]+)/', $meta, $matches) === 0)
						continue;

					$this->bendTextDomains[$textDomain]['plurals'] = (int)$matches[1];
					$this->bendTextDomains[$textDomain]['plural_expr'] = '$' . $matches[2] . ';';
				}
			}
		} else {
			return;
		}
		
		$textDomainCodeSet = ($codeSet !== null)?$codeSet:$this->defaultCodeSet;
		
		if(bind_textdomain_codeset($textDomain, $textDomainCodeSet) === $textDomainCodeSet)
			$this->bendTextDomains[$textDomain]['code_set'] = $textDomainCodeSet;
	}
	
	/**
	 * Lookup a message in the current domain
	 *
	 * @param string $message The message being translated.
	 * @param string|null $pluralMessage
	 * @param int $n
	 *
	 * @return string Returns a translated string if one is found in the translation table, or the submitted message if not found.
	 */
	public function getText($message, $pluralMessage = null, $n = 0)
	{
		if($n !== 0 && $pluralMessage === null)
			throw new \InvalidArgumentException('You have to provide a plural message string');
		
		if(isset($this->bendTextDomains[$this->currentTextDomain]['entries']) === true) {
			$msgstrOffset = 0;
			
			if($pluralMessage !== null) {
				if(isset($this->bendTextDomains[$this->currentTextDomain]['plural_expr']) === true) {
					$plural = 0;
					
					eval(strtr($this->bendTextDomains[$this->currentTextDomain]['plural_expr'], array('n' => $n)));

					$msgstrOffset = (int)$plural;
				}
			}
			
			if(isset($this->bendTextDomains[$this->currentTextDomain]['entries'][$message]['msgstr'][$msgstrOffset])) {
				if(strlen($this->bendTextDomains[$this->currentTextDomain]['entries'][$message]['msgstr'][$msgstrOffset]) === 0)
					return ($msgstrOffset === 0) ? $message : $pluralMessage;
				
				return $this->bendTextDomains[$this->currentTextDomain]['entries'][$message]['msgstr'][$msgstrOffset];
			} else {
				if($this->poWriterInterface instanceof PoWriterInterface) {
					$this->bendTextDomains[$this->currentTextDomain]['entries'][$message]['msgid'] = $message;
					$this->bendTextDomains[$this->currentTextDomain]['entries'][$message]['msgstr'][] = '';

					if($pluralMessage !== null) {
						$this->bendTextDomains[$this->currentTextDomain]['entries'][$message]['msgid_plural'] = $pluralMessage;
						$this->bendTextDomains[$this->currentTextDomain]['entries'][$message]['msgstr'] = array_fill(0 , $this->bendTextDomains[$this->currentTextDomain]['plurals'], '');
					}
				}

				return ($msgstrOffset === 0) ? $message : $pluralMessage;
			}
		}
		
		if($pluralMessage === null)
			return gettext($message);
	
		return ngettext($message, $pluralMessage, $n);
	}
	
	public function __destruct()
	{
		if($this->poWriterInterface instanceof PoWriterInterface === false)
			return;
		
		foreach($this->bendTextDomains as $domain => $info) {
			if(isset($info['entries']) === false)
				continue;
			
			$this->poWriterInterface->write($info['file_path'], $info['entries']);
		}
	}

	/**
	 * Get the currently active text domain
	 * 
	 * @return string
	 */
	public function getTextDomain()
	{
		return textdomain(null);
	}

	/**
	 * Enables support for PO files if a parser is set
	 * 
	 * @param PoParserInterface $poParser
	 */
	public function setPoParser(PoParserInterface $poParser)
	{
		$this->poParserInterface = $poParser;
	}

	/**
	 * Enables auto creation of PO files if a writer is set
	 * 
	 * @param PoWriterInterface $poWriter
	 */
	public function setPoWriter(PoWriterInterface $poWriter)
	{
		$this->poWriterInterface = $poWriter;
	}
}

/* EOF */