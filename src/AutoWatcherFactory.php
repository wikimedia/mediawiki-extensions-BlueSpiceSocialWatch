<?php

namespace BlueSpice\Social\Watch;

use BlueSpice\Social\Watch\AutoWatcher;
use BlueSpice\Social\Entity as SocialEntity;

class AutoWatcherFactory {

	/**
	 *
	 * @param BlueSpice\Social\Entity $entity
	 * @param \IContextSource $context
	 * @param \User $user
	 * @return BlueSpice\Social\Watch\AutoWatcher
	 */
	public function factory( SocialEntity $entity, \IContextSource $context = null, \User $user = null ) {
		return new AutoWatcher( $entity, $context, $user );
	}
}
