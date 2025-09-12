<?php

/**
 * @brief Special page RiskDataData for the @ref
 * Extensions-RiskData.
 *
 * @file
 *
 * @ingroup Extensions
 * @ingroup Extensions-RiskData
 *
 * @author [RV1971](https://www.mediawiki.org/wiki/User:RV1971)
 *
 * @sa Largely inspired from SpecialListusers.php.
 */

use MediaWiki\MediaWikiServices;

/**
 * @brief Pager used in SpecialRiskDataData.
 *
 * @ingroup Extensions-RiskData
 *
 * @sa [MediaWiki Manual:Pager.php]
 * (https://www.mediawiki.org/wiki/Manual:Pager.php)
 */
class RiskDataDataPager extends RiskDataPager {

	/* == public data members == */

	/** @brief Second parameter appended to the special page URL, or
	 * REQUEST variable 'pagename'.
	 *
	 * @var string
	 */
	public $pagename;

	/** @brief Third parameter appended to the special page URL, or
	 * REQUEST variable 'data'.
	 *
	 * @var string
	 */
	public $dataFrom;

	/* == private data members == */

	/// Whether the next row to format is the first one on the page.
	private $firstRow_ = true;

	private $columns_; ///< Column names.
	private $columnCount_; ///< count( @ref $columns_ ).

	/* == magic methods == */

	/**
	 * @brief Constructor.
	 *
	 * @param IContextSource|null $context Context.
	 *
	 * @param string|null $par Parameters of the form
	 * *table*[//<i>page</i>[//<i>data</i>]] so that data are selected
	 * for *table* and *page* starting at *data*.
	 *
	 * @xrefitem userdoc "User Documentation" "User Documentation" The
	 * special page <b>RiskDataData</b> accepts three parameters,
	 * which can either be appended to the URL
	 * (e.g. Special:RiskDataPages/Employees//Kampala//Smith) or
	 * given as the REQUEST parameters <tt>tablename</tt>,
	 * <tt>pagename</tt> and <tt>data</tt>. The former take
	 * precedence. The separator between parameters appended to the
	 * URL is configured with the global variable @ref
	 * $wgSpecialRiskDataPageParSep. The page will display for the
	 * given table and page those records where the content of the
	 * first field is greater or equal to the given one.
	 */
	public function __construct( ?IContextSource $context = null,
		$par = null ) {
		global $wgSpecialRiskDataPageParSep;

		$param = explode( $wgSpecialRiskDataPageParSep, $par, 3 );

		$this->pagename = isset( $param[1] ) && $param[1] != ''
			? $param[1] :
			$this->getRequest()->getText( 'pagename' );

		$this->dataFrom = isset( $param[2] ) && $param[2] != ''
			? $param[2] :
			$this->getRequest()->getText( 'data' );

		parent::__construct( $context, $param[0] );

		/** Set @ref $columns_ from RiskDataDatabase::getColumns(). */
		$this->columns_ = RiskData::singleton()->getDatabase()->getColumns(
			$this->tableDbKey );

		/** Cache count( @ref $columns_ ) in @ref $columnCount_ since
		 *	it is needed in formatRow().
		 */
		$this->columnCount_ = count( $this->columns_ );
	}

	/* == overriding methods == */

	/// Specify the database query to be run by AlphabeticPager.
	public function getQueryInfo() {
		global $wgRiskDataReadSrc;

		$conds = [ 'dtd_table' => $this->tableDbKey,
			'dtd_page = page_id' ];

		$dbr = MediaWikiServices::getInstance()->getDBLoadBalancer()->getConnection( DB_REPLICA );

		if ( $this->pagename != '' ) {
			$title = Title::newFromText( $this->pagename );

			$conds['page_namespace'] = $title->getNamespace();
			$conds['page_title'] = $title->getDBkey();
		}

		if ( $this->dataFrom != '' ) {
			$conds[] = $this->getIndexField() . ' >= '
				. $dbr->addQuotes( $this->dataFrom );
		}

		return [
			'tables' => [ 'd' => $wgRiskDataReadSrc, 'page' ],
			'fields' => [ 'page_namespace', 'page_title', 'd.*' ],
			'conds' => $conds
		];
	}

	/// Specify the first data column as the index field for AlphabeticPager.
	public function getIndexField() {
		return RiskDataDatabase::dataCol( 1 );
	}

	/**
	 * @brief Format a data row.
	 *
	 * @param stdClass $row Database row object.
	 *
	 * @return string Wikitext.
	 */
	public function formatRow( $row ) {
		$text = '';

		if ( $this->firstRow_ ) {
			global $wgSpecialRiskDataDataClasses;

			$classes = implode( ' ', $wgSpecialRiskDataDataClasses );
			$text .= "<table class='$classes'>\n<tr>\n";

			foreach ( $this->columns_ as $name ) {
				$text .= "<th>$name</th>\n";
			}

			$text .= "<th>{$this->msg( 'riskdatadata-page-column-title' )->text()}</th>\n</tr>\n";

			$this->firstRow_ = false;
		}

		$text .= "<tr>\n";

		for ( $i = 1; $i <= $this->columnCount_; $i++ ) {
			$column = RiskDataDatabase::dataCol( $i );
			$text .= "<td>{$row->$column}</td>\n";
		}

		$text .= "<td>[[" . Title::makeTitle( $row->page_namespace,
			$row->page_title ) . "]]</td>\n";

		return $text . "</tr>\n";
	}

	/**
	 * @brief Provide wikitext to close the table.
	 *
	 * @return string Wikitext.
	 */
	public function getEndBody() {
	 /* Return an empty string if still at first row, i.e. the
	  * AlphabeticPager did not return any records. */
		return $this->firstRow_ ? '' : "</table>\n";
	}

	/**
	 * @brief Provide the page header, which contains a form to select data.
	 *
	 * @return string html code.
	 */
	public function getPageHeader() {
		$content = Html::rawElement( 'label',
			[ 'for' => 'data' ],
			$this->msg( 'riskdatadata-from' )->parse() ) . '&#160'
			. Xml::input( 'data', 20, $this->dataFrom,
				[ 'id' => 'data' ] ) . ' '
			. Html::rawElement( 'label',
				[ 'for' => 'tablename' ],
				$this->msg( 'riskdatadata-table' )->parse() ) . '&#160'
			. Xml::input( 'tablename', 20, $this->tablename ?? '',
				[ 'id' => 'tablename' ] ) . ' '
			. Html::rawElement( 'label',
				[ 'for' => 'pagename' ],
				$this->msg( 'riskdatadata-page' )->parse() ) . '&#160'
			. Xml::input( 'pagename', 20, $this->pagename,
				[ 'id' => 'pagename' ] );

		return $this->buildPageHeader( 'data', $content );
	}
}

/**
 * @brief Special page RiskDataData for the @ref
 * Extensions-RiskData.
 *
 * @ingroup Extensions-RiskData
 */
class SpecialRiskDataData extends SpecialRiskData {
	public function __construct() {
		parent::__construct( 'RiskDataData' );
	}
}
