<?php

/**
 * @brief [Scribunto](https://www.mediawiki.org/wiki/Extension:Scribunto)
 * Lua library for the @ref Extensions-RiskData.
 *
 * @file
 *
 * @ingroup Extensions
 * @ingroup Extensions-RiskData
 *
 * @author [RV1971](https://www.mediawiki.org/wiki/User:RV1971)
 */

/**
 * @brief [Scribunto](https://www.mediawiki.org/wiki/Extension:Scribunto)
 * Lua library for the @ref Extensions-RiskData.
 *
 * @ingroup Extensions-RiskData
 */

class Scribunto_LuaRiskDataLibrary extends Scribunto_LuaLibraryBase {

	/* == private data members == */

	private $database_; ///< See @ref getDatabase().

	/* == magic methods == */

	/**
	 * @brief Constructor.
	 *
	 * Initialize data members.
	 *
	 * @param Scribunto_LuaEngine $engine Scribunto engine.
	 */
	public function __construct( $engine ) {
		parent::__construct( $engine );

		$this->database_ = new RiskDataDatabase;
	}

	/* == accessors == */

	/// Get the instance of RiskDataDatabase.
	public function getDatabase() {
		return $this->database_;
	}

	/* == special functions == */

	/// Register this library.
	public function register() {
		$lib = [
			'select' => [ $this, 'select' ]
		];

		$this->getEngine()->registerInterface(
			__DIR__ . '/../lua/RiskData.lua',
			$lib, [] );
	}

	/* == functions to be called from Lua == */

	/**
	 * @brief Select records from the database.
	 *
	 * @param string $table Logical table to select from.
	 *
	 * @param string|null $where WHERE clause or null.
	 *
	 * @param string|null $orderBy ORDER BY clause or null.
	 *
	 * @return array Numerically-indexed array (with indexes
	 * starting at 1) of associative arrays, each of which represents
	 * a record. False if the table does not exist.
	 */
	public function select( $table, $where, $orderBy = null ) {
		/** Increment the expensive function count. */
		$this->incrementExpensiveFunctionCount();

		/** Get the records. */
		$tableObj = RiskDataParser::table2title( $table );

		$records = $this->database_->select( $tableObj, $where, $orderBy,
			$pages, __METHOD__ );

		/** Renumber the records starting with 1, to match the Lua
		 * convention.
		 */
		if ( $records ) {
			$records = array_combine( range( 1, count( $records ) ),
				$records );
		}

		/** Call RiskData::addDependencies_(). */
		RiskData::singleton()->addDependencies(
			$this->getParser(), $pages, $tableObj );

		return [ $records ];
	}
}
