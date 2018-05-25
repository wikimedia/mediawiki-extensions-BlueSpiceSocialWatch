<?php

namespace BlueSpice\Social\Watch\Hook\BSEntitySaveComplete;
use BlueSpice\Hook\BSEntitySaveComplete;
use BlueSpice\Social\Entity;

class AutoWatch extends BSEntitySaveComplete {

	protected function doProcess() {
		if( !$this->entity instanceof Entity ) {
			return true;
		}

		$autoWatcher = \MediaWiki\MediaWikiServices::getInstance()
			->getService( 'BSSocialAutoWatcher' );

		$autoWatcher->setEntity( $this->entity );
		$autoWatcher->setContext( $this->getContext() );
		$autoWatcher->autoWatch();

		return true;
	}
}

