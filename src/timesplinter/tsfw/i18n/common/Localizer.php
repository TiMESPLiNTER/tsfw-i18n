<?php

namespace timesplinter\tsfw\i18n\common;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2014, TiMESPLiNTER Webdevelopment
 */
class Localizer
{
	protected $localeCategories;
	protected $categories;

	public function __construct()
	{
		$this->initLocaleCategories();
	}

	/**
	 * @param string $acceptedLanguagesString
	 * @param array $fallBacks
	 * @param string $default
	 * @param array|null $categories
	 *
	 * @return Localizer
	 */
	public static function fromAcceptedLanguages($acceptedLanguagesString, array $fallBacks = array(), $default = 'C', array $categories = array(LC_ALL))
	{
		$acceptedLanguageArray = array($default => 0.0);
				
		foreach(explode(',', $acceptedLanguagesString) as $lang) {
			if(preg_match('/([a-z-]{2,})(?:;q=(.+))?/i', $lang, $matches) === 0)
				continue;
			
			$localeParts = explode('_', strtr($matches[1], array('-' => '_')));
			$localeCode = strtolower($localeParts[0]) . (isset($localeParts[1]) ? '_' . strtoupper($localeParts[1]) : null);
			
			$acceptedLanguageArray[$localeCode] = isset($matches[2]) ? (float)$matches[2] : 1.0;
		}
		
		uasort($acceptedLanguageArray, function($a, $b) {
			if($a === $b) return 0;
			elseif($a < $b) return 1;
			else return 0;
		});
		
		sort($categories);
		
		$localesToSet = array_keys($acceptedLanguageArray);
		
		$localizer = new Localizer();
		$localeArr = array_fill_keys($categories, $localesToSet);
		
		if(isset($localeArr[LC_ALL]) === true) {
			// special case wa?
			foreach($fallBacks as $cat => $localeMap) {
				$localeArr[$cat] = $localeArr[LC_ALL];
			}
		}
		
		foreach($localeArr as $cat => $locales) {
			for($i = 0; $i < count($locales); ++$i) {
				if(isset($fallBacks[$cat][$locales[$i]]) === false)
					continue;

				$localeArr[$cat][$i] = $fallBacks[$cat][$locales[$i]];
			}
			
			$localeArr[$cat] = array_unique($localeArr[$cat]);
		}
		
		$localizer->setLocale($localeArr);
		
		return $localizer;
	}
	
	protected function initLocaleCategories()
	{
		$this->localeCategories = array(
			LC_ALL, LC_COLLATE, LC_CTYPE, LC_MONETARY, LC_NUMERIC, LC_TIME
		);
		
		if(defined('LC_MESSAGES') === true)
			$this->localeCategories[] = LC_MESSAGES;
	}

	/**
	 * @param array $locales A single locale value as string or an array with category and corresponding locale(s)
	 *
	 * @return array|bool Returns an array of the specific locale set for the provided categories or false
	 * 
	 * @throws \UnexpectedValueException
	 */
	public function setLocale(array $locales)
	{
		$localesSet = array();
				
		foreach($locales as $category => $catLocales) {
			if(in_array($category, $this->localeCategories) === false)
				throw new \UnexpectedValueException('Invalid category: ' . $category);
			
			if(($setCatLocale = setlocale($category, $catLocales)) !== false && in_array($category, array(LC_ALL, LC_MESSAGES)) === true)
				putenv('LANG=' . $setCatLocale);
			
			$localesSet[$category] = $setCatLocale;
		}

		return $localesSet;
	}

	/**
	 * Get all categories with its corresponding locale set
	 * 
	 * @return array List of set locales. Category as key and its corresponding locale as value.
	 */
	public function getLocales()
	{
		$currentLocales = array();
		
		foreach($this->localeCategories as $category) {
			$currentLocales[$category] = setlocale($category, '0');
		}
		
		return $currentLocales;
	}

	/**
	 * Get the current locale value set for a specific category
	 *
	 * @param int $category
	 *
	 * @return string The current locale for this category
	 */
	public function getLocale($category)
	{
		return setlocale($category, '0');
	}
}

/* EOF */