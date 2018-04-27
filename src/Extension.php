<?php
/**
 * BlueSpiceSocial base extension for BlueSpice
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * This file is part of BlueSpice MediaWiki
 * For further information visit http://bluespice.com
 *
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    BlueSpiceSocial
 * @subpackage BlueSpiceSocialWatch
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 */
namespace BlueSpice\Social\Watch;

use BlueSpice\Social\Entity;

class Extension extends \BlueSpice\Extension {

	public static function watchEntity( Entity $oEntity, $oStatus, \User $oUser ) {
		if( $oEntity->get( Entity::ATTR_TYPE ) == 'profile' ) {
			return false;//TODO: make notifications about another users actions!
		}
		if( $oEntity->getConfig()->get( 'IsWatchable' ) ) {
			//Watch your own new entries
			if( $oEntity->userIsOwner( $oUser )
				&& $oEntity->getTitle()->isNewPage() ) {
				$oStatus = \WatchAction::doWatch(
					$oEntity->getTitle(),
					$oUser
				);
			} elseif( !$oEntity->userIsOwner( $oUser ) ){
				//autowatch the not owned stuff you edited/created
				$oStatus = \WatchAction::doWatch(
					$oEntity->getTitle(),
					$oUser
				);
			}
			if( $oEntity->getTitle()->isNewPage() ) {
				//get all users, who are watching the related title and make
				//them watch this entity
				static::autoWatchFromRelatedTitle(
					$oEntity
				);
			}
		}
		if( $oEntity->hasParent() ) {
			//recursive parent entity watching.
			//f.e. the entity you commented on
			static::watchEntity(
				$oEntity->getParent(),
				$oStatus,
				$oUser
			);
		}
		
	}

	/**
	 * Very slow :(
	 * @param Entity $oEntity
	 * @return boolean
	 */
	public static function autoWatchFromRelatedTitle( Entity $oEntity ) {
		$oTitle = $oEntity->getRelatedTitle();
		if( !$oTitle || !$oTitle->exists() ) {
			return true;
		}

		$oRes = wfGetDB( DB_SLAVE )->select(
			'watchlist',
			'wl_user',
			[
				'wl_namespace' => $oTitle->getNamespace(),
				'wl_title' => $oTitle->getText()
			],
			__METHOD__
		);
		if( !$oRes ) {
			//:(
			return true;
		}
		foreach( $oRes as $oRow ) {
			$oStatus = \WatchAction::doWatch(
				$oEntity->getTitle(),
				\User::newFromId( $oRow->wl_user )
			);
		}
		return true;
	}
}