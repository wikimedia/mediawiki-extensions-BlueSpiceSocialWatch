<?php

namespace BlueSpice\Social\Watch;

use BlueSpice\Entity;

class AutoWatcher {
	/**
	 *
	 * @var \BlueSpice\Entity
	 */
	protected $entity;

	/**
	 *
	 * @var \User
	 */
	protected $user = null;

	/**
	 *
	 * @var \IContextSource
	 */
	protected $context;

	protected $autoWatchDone = false;

	/**
	 *
	 * @param \IContextSource $context
	 */
	public function setContext( \IContextSource $context ) {
		$this->context = $context;
	}

	/**
	 *
	 * @param Entity $entity
	 */
	public function setEntity( Entity $entity ) {
		if( $this->entity == null ) {
			$this->entity = $entity;
			return;
		}

		if( $this->entity->get( Entity::ATTR_ID )
				!= $entity->get( Entity::ATTR_ID ) ) {
			$this->entity = $entity;
			$this->reset();
		}
	}

	/**
	 *
	 * @param \User $user
	 */
	public function setUser( \User $user ) {
		$this->user = $user;
	}

	/**
	 * Gets if the watching has already been done
	 * for this entity
	 *
	 * @return boolean
	 */
	public function isAutoWatchDone() {
		return $this->autoWatchDone;
	}

	/**
	 * Adds necessary pages to users watchlist
	 * @return boolean
	 */
	public function autoWatch() {
		if( $this->autoWatchDone ) {
			return true;
		}

		if( $this->entity == null ) {
			return false;
		}

		$this->checkAndSetUser();

		if( $this->user == null ) {
			return false;
		}

		\BlueSpice\Social\Watch\Extension::watchEntity(
			$this->entity,
			null,
			$this->user
		);

		$this->autoWatchDone = true;

		return true;
	}

	/**
	 * If user is not set, gets currently logged
	 * user and sets it
	 */
	protected function checkAndSetUser() {
		if( $this->user == null ) {
			$user = $this->getUser();
			if( $user instanceof \User ) {
				$this->setUser( $user );
			}
		}
	}

	/**
	 * Gets currently logged in user
	 *
	 * @return \User|false
	 */
	protected function getUser() {
		if( $this->context == null ) {
			return false;
		}

		$loggedInUser = $this->context->getUser();
		if( !$loggedInUser || $loggedInUser->isAnon() ) {
			return false;
		}
		return $loggedInUser;
	}

	/**
	 * If entity is changed, we need to
	 * clear values
	 */
	protected function reset() {
		$this->user = null;
		$this->autoWatchDone = false;
	}
}
