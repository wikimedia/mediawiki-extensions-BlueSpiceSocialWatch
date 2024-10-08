/*
* @author     Stefan Kühn
* @package    BluespiceSocial
* @subpackage BlueSpiceSocial
* @copyright  Copyright (C) 2020 Hallo Welt! GmbH, All rights reserved.
* @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
*/

bs.social = bs.social || {};
bs.social.EntityActionMenuWatch = bs.social.EntityActionMenu || {};

bs.social.EntityActionMenuWatch.Watch = function ( entityActionMenu, data ) {
	OO.EventEmitter.call( this );
	var me = this;
	me.data = data || {};
	me.entityActionMenu = entityActionMenu;
	me.$element = null;

	var isWatched = me.entityActionMenu.entity.data.get( 'watch', false );
	if ( isWatched ) {
		me.$element = $( '<li class=""><a class="'
			+ ' bs-social-entity-action-watch'
			+ ' bs-socialwatch-watched highlight'
			+ ' dropdown-item"'
			+ ' tabindex="0"'
			+ ' role="button">'
			+ '<span>' + mw.message( 'bs-socialwatch-unwatchtext' ).plain() + '</span>'
			+ '</a></li>'
		);
	} else {
		me.$element = $( '<li class="dropdown-item"><a class="'
			+ ' bs-social-entity-action-watch'
			+ ' bs-socialwatch-watch'
			+ ' dropdown-item"'
			+ ' tabindex="0"'
			+ ' role="button">'
			+ '<span>' + mw.message( 'bs-socialwatch-watchtext' ).plain() + '</span>'
			+ '</a></li>'
		);
	}

	me.$element.on( 'click', function( e ) { me.click( e ); } );
	me.priority = 5;
};

OO.initClass( bs.social.EntityActionMenuWatch.Watch );
OO.mixinClass( bs.social.EntityActionMenuWatch.Watch, OO.EventEmitter );

bs.social.EntityActionMenuWatch.Watch.prototype.click = function ( e ) {
	var me = this;
	if( !me.entityActionMenu.entity.getConfig().IsWatchable ) {
		return;
	}
	if( me.entityActionMenu.entity.hasParent() ) {
		return;
	}

	var bWatch = !me.entityActionMenu.entity.data.get( 'watch', false );
	me.entityActionMenu.entity.showLoadMask();
	bs.api.tasks.execSilent(
		'socialwatch',
		'editWatch',
		{ id: me.entityActionMenu.entity.id, watch: bWatch }
	).done( function( response ) {
		if( !response.success ) {
			return;
		}
		me.entityActionMenu.entity.reload();
	});

	e.preventDefault();

	return false;
};
