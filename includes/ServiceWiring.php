<?php

use MediaWiki\MediaWikiServices;

return [
	'BSSocialAutoWatcher' => function ( MediaWikiServices $services ) {
		return new \BlueSpice\Social\Watch\AutoWatcher();
	}
];
