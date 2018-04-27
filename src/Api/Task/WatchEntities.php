<?php
/**
 * Provides the base api for BlueSpiceSocialWatch.
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
 * @package    BluespiceSocial
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */
namespace BlueSpice\Social\Watch\Api\Task;
use BlueSpice\Social\Entity;

/**
 * Api base class for simple tasks in BlueSpice
 * @package BlueSpiceSocial
 */
class WatchEntities extends \BSApiTasksBase {

	/**
	 * Methods that can be called by task param
	 * @var array
	 */
	protected $aTasks = array(
		'editWatch',
	);

	protected function getRequiredTaskPermissions() {
		return array(
			'editWatch' => [ 'read' ],
		);
	}

	public function task_editWatch( $vTaskData, $aParams ) {
		$oResult = $this->makeStandardReturn();
		$this->checkPermissions();

		if( empty( $vTaskData->id ) ) {
			$vTaskData->id = 0;
		}
		if( empty( $vTaskData->watch ) ) {
			$vTaskData->watch = false;
		}

		if( $this->getUser()->isAnon() ) {
			return $oResult;
		}
		$oEntity = Entity::newFromID( $vTaskData->id );
		if( !$oEntity instanceof Entity || !$oEntity->exists() ) {
			return $oResult;
		}

		if( !$vTaskData->watch ) {
			$oStatus = \WatchAction::doUnwatch(
				$oEntity->getTitle(),
				$this->getUser()
			);
		} else {
			$oStatus = \WatchAction::doWatch(
				$oEntity->getTitle(),
				$this->getUser()
			);
		}
		if( !$oStatus->isOK() ) {
			$oResult->message = $oStatus->getHTML();
			return $oResult;
		}

		$oResult->success = true;
		$oResult->payload['entity'] = \FormatJson::encode( $oEntity );
		$oResult->payload['entityconfig'][$oEntity->get( Entity::ATTR_TYPE )]
			= \FormatJson::encode( $oEntity->getConfig() );
		$oResult->payload['view'] = $oEntity->render();
		return $oResult;
	}

	/**
	 * Returns the bsic description for this module
	 * @return type
	 */
	public function getDescription() {
		return array(
			'BSApiTasksBase: This should be implemented by subclass'
		);
	}

	/**
	 * Returns the basic example
	 * @return type
	 */
	public function getExamples() {
		return array(
			'api.php?action='.$this->getModuleName().'&task='.$this->aTasks[0].'&taskData={someKey:"someValue",isFalse:true}',
		);
	}
}