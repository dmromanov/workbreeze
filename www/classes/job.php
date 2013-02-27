<?php

/**
 * Job
 * @author Kalashnikov Igor <igor.kalashnikov@gmail.com>
 * @license Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported
 */
class Job {
	
	const CUR_RUBLE  = 0;
	const CUR_DOLLAR = 1;
	const CUR_EURO   = 2;

	const CAT_OTHER  = 0;

	const CAT_AUDIOVIDEO  = 1;
	const CAT_DESIGN      = 2;
	const CAT_PHOTO       = 3;
	const CAT_PROGRAMMING = 4;
	const CAT_WEBPROG     = 5;
	const CAT_TRANSLATE   = 6;
	const CAT_TEXT        = 7;
	const CAT_ADVERTISING = 8;
	const CAT_SYSADM      = 9;
	
	private static $catlinks = array(
		self::CAT_OTHER       => array(
			array('other', 'другое', 'прочее', 'менеджмент')
		),
		self::CAT_AUDIOVIDEO  => array(
			array(
				'аудио', 'звук', 'audio', 'sound', 'видео', 'клип', 'video', 'clip', 'анимация',
				'fruity loops'
			)
		),
		self::CAT_DESIGN      => array(
			array(
				'дизайн', 'design', 'баннер', 'banner', 'графика', 'graphic', 'flash',
				'логотип', 'logo', 'арт', 'худож', 'art', 'иллюстра', 'photoshop', 'illustrat',
				'флеш'
			)
		),
		self::CAT_PHOTO       => array(
			array('фото', 'photo ') // space to disallow photoshop
		),
		self::CAT_PROGRAMMING => array(
			array(
				'программир', 'programm', 'разраб', 'software', 'delphi', 'c++', 'visual basic',
				'development', 'cocoa', 'obj-c', 'objective-c', 'java', 'ipad', 'iphone'),
			array('веб', 'web', 'сайт')
		),
		self::CAT_WEBPROG     => array(
			array(
				'скрипт', 'веб-разр', 'верстк', 'веб-прило', 'разработка сайт', 'script', 'web-prog', 
				'webprog', 'веб-программ', 'php', 'asp', 'wordpress', 'joomla', 'ajax', 'javascript', 
				'ruby', 'ecommerce', 'создание сайт', 'системы админист', 'drupal', 'framework', 'web prog',
				'web devel', 'css', 'dreamveawer', 'oscommerce'
			)
		),
		self::CAT_TRANSLATE   => array(
			array('перевод', 'translat')
		),
		self::CAT_TEXT        => array(
			array('текст', 'рерайтинг', 'text', 'writing', 'статьи', 'обзор', 'реферат', 'диплом', 'posting', 
			'data-process', 'корректировка', 'data process')
		),
		self::CAT_ADVERTISING => array(
			array('реклам', 'advertising', 'seo', 'раскрутка', 'маркетинг', 'marketing', 'social')
		),
		self::CAT_SYSADM      => array(
			array(
				'admin', 'админ', 'windows', 'linux', 'freebsd', 'unix', 'sql', 'plesk',
				'настройка сервер', 'asterisk', 'voip'
			),
			array('системы админ')
		)
	);

	private $site;
	private $id;
	private $url;
	
	private $stamp;
	
	private $title;
	
	private $categories = array(
		self::CAT_OTHER
	);
	
	private $description;
	private $description_short = '';

	private $stem = array();

	private $money = array();

	public function __construct() {
		$this->stamp = time();
	}
	
	public static function create() {
		return new Job();
	}
	
	public static function createBySite($site) {
		$job = new Job();
		$job->setSite($site);
		return $job;
	}
	
	public static function loadBySiteAndId($site, $id) {
		$job = new Job();
		$job->load($site, $id);
		return $job;
	}
	
	public function load($site, $id) {
	
	}
	
	public function insert() {
		$arr = array(
			'stamp'  => $this->getStamp(),
			'site'   => $this->getSite(),
			'id'     => (string) $this->getId(), // STRING!!!
			'url'    => $this->getUrl(),
			'title'  => $this->getTitle(),
			'cats'   => $this->getCategories(),
			'desc'   => $this->getDescription()
		);
		
		if ($this->getShortDescription()) {
			$arr['short'] = $this->getShortDescription();
		}
		
		if (count($this->money)) {
			$arr['money'] = $this->getMoney();
		}

		if (count($this->stem)) {
			$arr['stem'] = $this->stem;
		}
		
		Database::jobs()->insert($arr);

		return true;
	}
	
	public function getUrl() {
		return $this->url;
	}
	
	public function setUrl($url) {
		$this->url = $url;
		return $this;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	
	public function setMoney($val) {
		$this->money = $val;
	}
	
	public function getMoney() {
		return $this->money;
	}
	
	public function setCategoriesByText($text) {
		$tmp = mb_strtolower($text);
		
		$tmp = strip_tags($tmp);
		$tmp = html_entity_decode($tmp);
		
		$cats = array();
		
		foreach (self::$catlinks as $cat => $wa) {
			$words = array_shift($wa);
			
			$foundcat = -1;
		
			foreach($words as $word) {
				if (false !== mb_strpos($tmp, $word)) {
					$foundcat = $cat;
					break;
				}
			}
			
			$non = array_shift($wa);
			
			if (
				$foundcat != -1
				&& $non
			) {
				foreach($non as $word) {
					if (false !== mb_strpos($tmp, $word)) {
						$foundcat = -1;
						break;
					}
				}
			}
			
			if ($foundcat != -1)
				$cats[] = $foundcat;
		}
		
		if (0 == count($cats))	{
			echo '[' . date('H:m:s') . '] Can\'t find category for: ' . $tmp . "\n";
			$cats[] = self::CAT_OTHER;
		}
		
		$this->categories = $cats;
	}
	
	public function getCategories() {
		return $this->categories;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function setTitle($title) {
		$title = strip_tags($title);
		$title = preg_replace('/(\040|\t)+/', ' ', $title);

		$this->title = trim($title);
		
		return $this;
	}
	
	public function getStamp() {
		return $this->stamp;
	}
	
	public function getSite() {
		return $this->site;
	}
	
	public function setSite($id) {
		$this->site = $id;
		return $this;
	}
	
	public function getShortDescription() {
		if ('' != $this->description_short) {
			return $this->description_short;
		}
		
		return false;
	}

	public function getHTMLDescription() {
		return str_replace("\n", '<br />', $this->getDescription());
	}

	public function getDescription() {
		return $this->description;
	}

	public function setDescription($text = '') {
		$tmp = Beautifier::HTMLPrepare($text);
	
		$this->description = $tmp;

		// stemmer
		$tmp .= ' ' . $this->getTitle();
		$words = Stemmer::ExtractWords($tmp);
		$this->stem = Stemmer::Stem($words);
		// /stemmer

		if (preg_match('/([^ \n\r]+[ \n\r]+){30}/s', $this->description, $match))
			$this->description_short = trim(str_replace("\n\n", "\n", $match[0])) . '...';
		
		return $this;
	}
	
	public static function prepareJSON($job, $mod = 1) {
		$description = isset($job['short']) ? $job['short'] : $job['desc'];

		// for accurate strip_tags
		$description = str_replace(
			array('<', '>'),
			array(' <', '> '),
			$description);

		$description = strip_tags($description, '<a>');

		$item = array(
			's'  => $job['site'],
			'i'  => $job['id'],
			'st' => $job['stamp'] * $mod,
			't'  => $job['title'],
			'c'  => $job['cats'],
			'd'  => $description
		);

		if (isset($job['money'])) {
			$item['m'] = $job['money'];
		}
		
		return $item;
	}

}
