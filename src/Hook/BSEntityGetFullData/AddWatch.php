<?php

namespace BlueSpice\Social\Watch\Hook\BSEntityGetFullData;

use BlueSpice\Hook\BSEntityGetFullData;
use BlueSpice\Social\Entity;
use MediaWiki\MediaWikiServices;

class AddWatch extends BSEntityGetFullData {

	protected function checkEntity() {
		if ( !$this->entity instanceof Entity ) {
			return false;
		}
		if ( !MediaWikiServices::getInstance()->getWatchlistManager()->isWatchable( $this->entity->getTitle() ) ) {
			return false;
		}
		if ( !$this->entity->getConfig()->get( 'IsWatchable' ) ) {
			return false;
		}
		return true;
	}

	protected function doProcess() {
		if ( !$this->checkEntity() ) {
			return true;
		}

		$this->data['watch'] = false;
		$oUser = $this->getContext()->getUser();
		if ( !$oUser || $oUser->isAnon() ) {
			return true;
		}
		$this->data['watch'] = MediaWikiServices::getInstance()->getWatchlistManager()->isWatchedIgnoringRights(
			$oUser,
			$this->entity->getTitle()
		);
		return true;
	}
}
