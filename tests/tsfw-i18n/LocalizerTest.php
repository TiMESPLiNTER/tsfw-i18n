<?php

namespace timesplinter\tsfw\i18n\tests;

use timesplinter\tsfw\i18n\common\Localizer;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2014, TiMESPLiNTER Webdevelopment
 */
class LocalizerTest extends \PHPUnit_Framework_TestCase
{
	public function testSettingSingleLocaleCategory()
	{
		$localizer = new Localizer();
		
		$defaultLocales = $localizer->getLocales();
		
		$currentLocales = $localizer->getLocales();
		$currentLocales[LC_ALL] = 'en_US.UTF-8';
		$currentLocales[LC_COLLATE] = 'en_US.UTF-8';
		$currentLocales[LC_CTYPE] = 'en_US.UTF-8';
		$currentLocales[LC_MONETARY] = 'en_US.UTF-8';
		$currentLocales[LC_NUMERIC] = 'en_US.UTF-8';
		$currentLocales[LC_TIME] = 'en_US.UTF-8';
		
		if(defined('LC_MESSAGES'))
			$currentLocales[LC_MESSAGES] = 'en_US.UTF-8';

		$this->assertEquals(array(LC_ALL => 'en_US.UTF-8'), $localizer->setLocale(array(LC_ALL => 'en_US.UTF-8')), 'Set single locale which exists');
		$this->assertEquals(array(LC_ALL => false), $localizer->setLocale(array(LC_ALL => 'foobar')), 'Set single locale which does not exist');

		$this->assertEquals($currentLocales, $localizer->getLocales(), 'Compare locale list');
		$this->assertEquals('en_US.UTF-8', $localizer->getLocale(LC_ALL), 'Compare locale category');
		
		$this->assertEquals($defaultLocales, $localizer->setLocale($defaultLocales));
		$this->assertEquals($defaultLocales, $localizer->getLocales(), 'Same as initial locales');
	}
	
	public function testSettingMultipleLocales()
	{
		$localizer = new Localizer();

		$defaultLocales = $localizer->getLocales();
		
		$currentLocales = array(
			0 => 'en_US.UTF-8/en_US.UTF-8/en_US.UTF-8/en_US.UTF-8/de_DE.UTF-8/en_US.UTF-8',
			1 => 'en_US.UTF-8',
			2 => 'en_US.UTF-8',
			3 => 'en_US.UTF-8',
			4 => 'en_US.UTF-8',
			5 => 'de_DE.UTF-8',
			6 => 'en_US.UTF-8'
		);

		$this->assertEquals(array(LC_ALL => 'en_US.UTF-8', LC_TIME => 'de_DE.UTF-8'), $localizer->setLocale(array(LC_ALL => 'en_US.UTF-8', LC_TIME => 'de_DE.UTF-8')), 'Set single locale which exists');
		$this->assertEquals(array(LC_ALL => false, LC_TIME => false), $localizer->setLocale(array(LC_ALL => 'foo', LC_TIME => 'bar')), 'Set single locale which does not exist');

		$this->assertEquals($currentLocales, $localizer->getLocales(), 'Compare locale list');
		$this->assertEquals('en_US.UTF-8/en_US.UTF-8/en_US.UTF-8/en_US.UTF-8/de_DE.UTF-8/en_US.UTF-8', $localizer->getLocale(LC_ALL), 'Compare locale category LC_ALL');
		$this->assertEquals('de_DE.UTF-8', $localizer->getLocale(LC_TIME), 'Compare locale category LC_TIME');

		$this->assertEquals($defaultLocales, $localizer->setLocale($defaultLocales));
		$this->assertEquals($defaultLocales, $localizer->getLocales(), 'Same as initial locales');
	}
	
	public function testSettingFirstInvalidLocales()
	{
		$localizer = new Localizer();
		
		$currentLocales = array(
			LC_ALL => 'en_US.UTF-8',
			LC_TIME => 'de_DE.UTF-8'
		);
		
		$this->assertEquals(array(LC_ALL => 'en_US.UTF-8'), $localizer->setLocale(array(LC_ALL => array('foo', 'en_US.UTF-8', 'bar'))), 'Illegal locales');

		$localesArr = array(
			LC_ALL => array('foo', 'en_US.UTF-8', 'bar'),
			LC_TIME => array('foo', 'de_DE.UTF-8', 'baz')
		);
		
		$this->assertEquals($currentLocales, $localizer->setLocale($localesArr), 'Illegal locales');
	}
	
	public function testSettingIllegalLocaleOnlyArray()
	{
		$localizer = new Localizer();

		$localesArr = array(
			LC_ALL => array('foo', 'bar'),
			LC_TIME => array('foo', 'baz')
		);
		
		$this->assertEquals(array(LC_ALL => false, LC_TIME => false), $localizer->setLocale($localesArr), 'Illegal locales');
	}
}

/* EOF */