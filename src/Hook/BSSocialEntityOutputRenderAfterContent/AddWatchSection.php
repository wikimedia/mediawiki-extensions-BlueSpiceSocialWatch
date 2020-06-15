<?php

namespace BlueSpice\Social\Watch\Hook\BSSocialEntityOutputRenderAfterContent;

use BlueSpice\Social\Entity;
use BlueSpice\Social\Hook\BSSocialEntityOutputRenderAfterContent;

/**
 * Adds a watch button to the entities
 */
class AddWatchSection extends BSSocialEntityOutputRenderAfterContent {

	protected function doProcess() {
		$oUser = $this->getContext()->getUser();
		if ( !$oUser || $oUser->isAnon() ) {
			return true;
		}

		$oEntity = $this->oEntityOutput->getEntity();
		if ( !$oEntity instanceof Entity ) {
			return true;
		}
		if ( !$oEntity->exists() || $oEntity->hasParent() ) {
			return true;
		}
		if ( !$oEntity->getConfig()->get( 'IsWatchable' ) ) {
			return true;
		}
		$aEntity = $oEntity->getFullData();
		$aClasses = [ 'bs-social-entityaftercontent-watch' ];
		$sMsg = 'bs-socialwatch-watchtext';

		if ( isset( $aEntity['watch'] ) && $aEntity['watch'] === true ) {
			$sMsg = 'bs-socialwatch-unwatchtext';
			$aClasses[] = 'bs-socialwatch-watched';
		} else {
			$aClasses[] = 'bs-socialwatch-unwatched';
		}
		$sView = '';
		$sView .= \Html::openElement( "a", [
			'class' => implode( ' ', $aClasses )
		] );

		$sView .= \Html::element( 'span', [], wfMessage( $sMsg )->parse() );

		$sView .= \Html::closeElement( "a" );

		$this->aViews[] = $sView;
		return true;
	}
}
