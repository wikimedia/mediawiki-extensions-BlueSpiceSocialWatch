<?php

use MediaWiki\MediaWikiServices;

return [
	'BSSocialAutoWatcherFactory' => static function ( MediaWikiServices $services ) {
		return new \BlueSpice\Social\Watch\AutoWatcherFactory();
	}
];
