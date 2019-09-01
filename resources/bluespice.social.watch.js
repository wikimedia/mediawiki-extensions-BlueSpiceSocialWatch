/**
 *
 * @author     Patric Wirth <wirth@hallowelt.com>
 * @package    BluespiceSocial
 * @subpackage BlueSpiceSocialWatch
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 */

$( document ).bind( 'BSSocialEntityInit', function( event, Entity ) {
	if( !Entity.getConfig().IsWatchable ) {
		return;
	}
	if( Entity.hasParent() ) {
		return;
	}
	var $lnk = Entity.getEl().find( '.bs-social-entityaftercontent-watch' );
	var disabled = false;
	$lnk.on( 'click', function() {
		if( disabled ) {
			return false;
		}
		disabled = true;
		var bWatch = !$(this).hasClass( 'bs-socialwatch-watched' );
		var $element = $(this);
		Entity.showLoadMask();
		bs.api.tasks.execSilent(
			'socialwatch',
			'editWatch',
			{ id: Entity.id, watch: bWatch }
		).done( function( response ) {
			if( !response.success ) {
				return;
			}
			var msg = 'bs-socialwatch-unwatchtext',
				rmvClass = 'bs-socialwatch-unwatched',
				addClass = 'bs-socialwatch-watched';

			if( !bWatch ) {
				msg = 'bs-socialwatch-watchtext';
				rmvClass = 'bs-socialwatch-watched',
				addClass = 'bs-socialwatch-unwatched';
			}
			$element.addClass( addClass );
			$element.removeClass( rmvClass );
			$element.html( mw.message( msg ).plain() );
		})
		.then(function(){
			disabled = false;
			Entity.hideLoadMask();
		});
		return false;
	});
});