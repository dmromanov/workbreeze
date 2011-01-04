<?php

/**
 * Class for home page
 * @author Kalashnikov Igor <igor.kalashnikov@gmail.com>
 * @license Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported
 */
class MHome extends PageModule {

	protected function getLanguage() {
		return array(
			'feed/at' => 'at',
			'common/yesterday' => 'y'
		);
	}
	
	protected function getExternalJS() {
		return array(
			'compat', 'storage', 'filter', 'lang', 'sites', 'categories', 'keywords', 'ajax', 'notifier', 'feed'
		);
	}

	protected function getContent() {
		return <<<EOF
	<div id="wrapper">
		<ul id="pp">
			<li id="pause"><a href="#">{$this->tr('feed/pause')}</a></li>
			<li id="play"><a href="#">{$this->tr('feed/start')}</a></li>
		</ul>
		
		<div id="logo">Workbreeze</div>
		<ul id="menu">
			<li id="help" class="i i_help" title="{$this->tr('feed/hints')}"></li>
			<li title="{$this->tr('common/statistics')}"><a href="/stats" class="i i_stats"></a></li>
		</ul>
		
		<div id="filter">
			<input type="text" id="keywords" class="fw" placeholder="{$this->tr('common/keywords')}" />
			<div class="help fw">{$this->tr('hints/keywords')}</div>
		
			<ul id="sites">
			</ul>
			<div class="help fw">{$this->tr('hints/sites')}</div>

			<ul id="categories">
			</ul>
			<div class="help fw">{$this->tr('hints/categories')}</div>

			<ul id="mode">
				<li id="mode_f" class="checkable fw">{$this->tr('feed/filter_mode')}</li>
			</ul>
			<div class="help fw">{$this->tr('hints/filter_mode')}</div>
		</div>
	</div>
	
	<div id="right">
		<div class="help">{$this->tr('hints/offers')}</div>
		<div id="jobs">
			<ul class="job">
				<li class="title"></li>
				<li class="desc"></li>
				<li class="k"></li>
				<li class="time"></li>
				<li class="money"></li>
				<li class="cl"></li>
			</ul>
		</div>
	</div>

	<ul id="bfoot">
		<li><a href="http://github.com/silentroach/workbreeze/commit/#githash">github</a></li>
		<li><a href="http://twitter.com/workbreeze">twitter</a></li>
		<li><a href="http://www.facebook.com/pages/workbreezecom/103214773075102">facebook</a></li>
		<li><a href="http://vk.com/club19804722">vk</a></li>
		<li><a href="http://digg.com/news/business/workbreeze">digg</a></li>
		<li><a href="mailto:workbreeze@gmail.com">e-mail</a></li>
	</ul>

	<div id="shadow"></div>

	<div id="previewm">
		<div id="preview">
			<a href="#">{$this->tr('common/close')}</a>
			<iframe></iframe>
		</div>
	</div>
EOF;
	}
	
}
