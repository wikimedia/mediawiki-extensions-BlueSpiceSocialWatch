<?php

namespace BlueSpice\Social\Watch\Hook\BSEntitySaveComplete;
use BlueSpice\Hook\BSEntitySaveComplete;
use BlueSpice\Social\Entity;

class AutoWatch extends BSEntitySaveComplete {

	protected function getUser() {
		$oLoggedInUser = $this->getContext()->getUser();
		if( !$oLoggedInUser || $oLoggedInUser->isAnon() ) {
			return false;
		}
		return $oLoggedInUser;
	}

	protected function doProcess() {
		if( !$this->entity instanceof Entity ) {
			return true;
		}
		$oUser = $this->getUser();
		if( !$oUser ) {
			return true;
		}
		\BlueSpice\Social\Watch\Extension::watchEntity(
			$this->entity,
			null,
			$oUser
		);
		return true;
	}
}

