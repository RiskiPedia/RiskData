<?php

use MediaWiki\Hook\ParserFirstCallInitHook;
use MediaWiki\Installer\Hook\LoadExtensionSchemaUpdatesHook;
use MediaWiki\Page\Hook\ArticleDeleteHook;
use MediaWiki\Page\Hook\RevisionFromEditCompleteHook;

class RiskDataHooks implements
	ArticleDeleteHook,
	LoadExtensionSchemaUpdatesHook,
	RevisionFromEditCompleteHook,
	ParserFirstCallInitHook
{

	/** @inheritDoc */
	public function onArticleDelete(
		WikiPage $wikiPage,
		\MediaWiki\User\User $user,
		&$reason,
		&$error,
		\MediaWiki\Status\Status &$status,
		$suppress
	) {
		RiskData::singleton()->onArticleDelete( $wikiPage, $user, $reason, $error );
	}

	/** @inheritDoc */
	public function onLoadExtensionSchemaUpdates( $updater ) {
		RiskData::singleton()->onLoadExtensionSchemaUpdates( $updater );
	}

	/** @inheritDoc */
	public function onParserFirstCallInit( $parser ) {
		RiskData::singleton()->onParserFirstCallInit( $parser );
	}

	/** @inheritDoc */
	public function onRevisionFromEditComplete( $wikiPage, $rev, $originalRevId, $user, &$tags ) {
		RiskData::singleton()->onRevisionFromEditComplete( $wikiPage, $rev, $originalRevId, $user );
	}

}
