<?php

class MUp extends Module {

	const VSITES = 5;
	const VCATS  = 1;
	
	private function getLang($ver = 0) {
		if ($ver == Language::VERSION)
			return false;
			
		return array(
			'v'  => Language::VERSION,
			'a'  => Language::$list, 
			'vl' => Language::getLang()
		);
	}	
	
	private function getCats($ver = 0) {
		if (self::VCATS === $ver)
			return false;
			
		$items = array();
		
		foreach(array(
			Job::CAT_OTHER, Job::CAT_AUDIOVIDEO, Job::CAT_DESIGN, Job::CAT_PHOTO, Job::CAT_PROGRAMMING, 
			Job::CAT_WEBPROG, Job::CAT_TRANSLATE, Job::CAT_TEXT, Job::CAT_ADVERTISING, Job::CAT_SYSADM
		) as $el) {
			$items[] = array(
				'i' => $el,
				'l' => 'c' . $el
			);
		}
			
		return array(
			'v' => self::VCATS,
			'vl' => $items
		);
	}

	private function getSites($ver = 0) {
		if (self::VSITES === $ver)
			return false;
	
		$db = $this->db();
		
		$c = $db->sites;
		
		$sites = array();
		
		$cursor = $c->
			find();
		
		while ($site = $cursor->getNext()) {
			$sites[] = array(
				'i' => $site['code'], 
				'f' => $site['folder'], 
				'n' => $site['name'], 
				'u' => $site['url']
			);
		}
		
		return array(
			'v'  => self::VSITES,
			'vl' => $sites
		);
	}

	private function getJobs($stamp) {
		$db = $this->db();
	
		$c = $db->jobs;
	
		$jobs = array();
		
		if ($stamp < 0) {
			$mod = -1;
			$st = array('$lt' => -$stamp);
		} else {
			$mod = 1;
			$st = array('$gt' => $stamp);
		}
		
		$filter = array(
			'stamp' => $st
		);
		
		if (isset($_POST['filter_sites'])) {
			$sites = explode(',', $_POST['filter_sites']);
			
			foreach($sites as $key => &$val) {
				$val = intval($val);
				
				if (
					$val < 0
					|| $val > 20
				) {
					unset($sites[$key]);
				}
			}
			
			if (count($sites)) {
				$filter['site'] = array(
					'$in' => $sites
				);
			}
		}
		
		if (isset($_POST['filter_cats'])) {
			$cats = explode(',', $_POST['filter_cats']);
			
			foreach($cats as $key => &$val) {
				$val = intval($val);
				
				if (
					$val < 0
					|| $val > 30
				) {
					unset($cats[$key]);
				}
			}
			
			if (count($cats)) {
				$filter['cats'] = array(
					'$in' => $cats
				);
			}
		}
		
		if (isset($_POST['filter_keys'])) {
			// query to sphinx
		}

		if (1 == count($filter)) { // only stamp
			$res = Cache::get('j' . $stamp);

			if ($res) {
				return $res;
			}
		}
				
		$cursor = $c->find(
			$filter,
			array('site', 'id', 'stamp', 'title', 'cats', 'short', 'desc', 'money')
		);
		$cursor->sort(array('stamp' => -1));
		$cursor->limit(25);
		
		while ($job = $cursor->getNext()) {
			$item = Job::prepareJSON($job, $mod);

			$jobs[] = $item;
		}
		
		if (0 == count($jobs))
			return false;
		
		if ($stamp < 0) {
			$jobs = array_reverse($jobs);
		}

		if (1 == count($filter)) {
			Cache::set('j' . $stamp, $jobs, 30);
		}
		
		return $jobs;
	}
	
	private function getIntParam($name) {
		if (!isset($_POST[$name]))
			return false;
			
		return intval($_POST[$name]);
	}

	protected function runModule($query) {
		if (count($query) != 0) {
			return false;
		}

		$r = array();

		// check for outdated language pack
		$vlang = $this->getIntParam('lang');
		if (false !== $vlang) {
			$lang = $this->getLang($vlang);
			
			if ($lang) {
				$r['l'] = $lang;
			}
		}
	
		// check for outdates sites
		$vsites = $this->getIntParam('sites');
		if (false !== $vsites) {
			$sites = $this->getSites($vsites);
			
			if ($sites) {
				$r['s'] = $sites;
			}
		}

		// check for outdates categories
		$vcats = $this->getIntParam('cats');
		if (false !== $vcats) {
			$cats = $this->getCats($vcats);
			
			if ($cats) {
				$r['c'] = $cats;
			}
		}
		
		// check for jobs
		$jstamp = $this->getIntParam('jstamp');
		if (false !== $jstamp) {
			$jobs = $this->getJobs($jstamp);
			
			if ($jobs) {
				$r['j'] = $jobs;
			}
		}

		return $r;	
	}

}
