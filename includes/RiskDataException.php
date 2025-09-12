<?php

/**
 * @brief Exception handling for the @ref Extensions-RiskData.
 *
 * @file
 *
 * @ingroup Extensions
 * @ingroup Extensions-RiskData
 *
 * @author [RV1971](https://www.mediawiki.org/wiki/User:RV1971)
 *
 */

/**
 * @brief Exception class for the @ref Extensions-RiskData.
 *
 * @ingroup Extensions-RiskData
 */
class RiskDataException extends MWException {
	/**
	 * @brief Constructor.
	 *
	 * @param string $message Message ID.
	 *
	 * @param mixed ...$params Further parameters to wfMessage().
	 *
	 * @sa [MediaWiki Manual:Messages API]
	 * (https://www.mediawiki.org/wiki/Manual:Messages_API)
	 */
	public function __construct( $message, ...$params ) {
		parent::__construct( wfMessage( $message, $params )->text() );
	}

	/// Return formatted message as html.
	public function getHTML() {
		return wfMessage( 'riskdata-error', $this->getMessage() )->parse();
	}

	/// Return formatted message as static wikitext.
	public function getText() {
		return wfMessage( 'riskdata-error', $this->getMessage() )->text();
	}
}
