<?php

namespace BlueSpice\Social\Watch;

use BlueSpice\Entity as SocialEntity;

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

	/**
	 * Context or User must be supplied
	 *
	 * @param Entity $entity
	 * @param \IContextSource $context
	 * @param \User $user
	 */
	public function __construct( SocialEntity $entity, \IContextSource $context = null, \User $user = null ) {
		$this->entity = $entity;
		$this->context = $context;
		$this->user = $user;
	}

	/**
	 * Adds necessary pages to users watchlist
	 * @return boolean
	 */
	public function autoWatch() {
		if( $this->entity == null ) {
			return false;
		}

		$this->checkAndSetUser();

		if( $this->user == null ) {
			return false;
		}

		$this->watchEntity();

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
				$this->user = $user;
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

	protected function watchEntity() {
		if( $this->entity->get( SocialEntity::ATTR_TYPE ) == 'profile' ) {
			return false;//TODO: make notifications about another users actions!
		}
		if( $this->entity->getConfig()->get( 'IsWatchable' ) ) {
			//Watch your own new entries
			if( $this->entity->userIsOwner( $this->user )
				&& $this->entity->getTitle()->isNewPage() ) {
				$status = \WatchAction::doWatch(
					$this->entity->getTitle(),
					$this->user
				);
			} elseif( !$this->entity->userIsOwner( $this->user ) ){
				//autowatch the not owned stuff you edited/created
				$status = \WatchAction::doWatch(
					$this->entity->getTitle(),
					$this->user
				);
			}
			if( $this->entity->getTitle()->isNewPage() ) {
				//get all users, who are watching the related title and make
				//them watch this entity
				$this->autoWatchFromRelatedTitle();
			}
		}
		if( $this->entity->hasParent() ) {
			//recursive parent entity watching.
			//f.e. the entity you commented on
			$this->entity = $this->entity->getParent();
			$this->watchEntity();
		}
		
	}

	/**
	 * Very slow :(
	 * @return boolean
	 */
	public function autoWatchFromRelatedTitle() {
		$title = $this->entity->getRelatedTitle();
		if( !$title || !$title->exists() ) {
			return true;
		}

		$res = wfGetDB( DB_SLAVE )->select(
			'watchlist',
			'wl_user',
			[
				'wl_namespace' => $title->getNamespace(),
				'wl_title' => $title->getText()
			],
			__METHOD__
		);
		if( !$res ) {
			//:(
			return true;
		}
		foreach( $res as $row ) {
			$status = \WatchAction::doWatch(
				$this->entity->getTitle(),
				\User::newFromId( $row->wl_user )
			);
		}
		return true;
	}
}
