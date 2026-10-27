<?php

/**
 * Data access component
 *
 * @author Rad Cirskis (nad2000@gmail.com)
 */
class DataSource {

	static private $_user = 'root';
	static private $_pass = NULL;
	static private $_connectionString = 'mysql:host=localhost;dbname=taxtool';
	static private $_dbh = NULL;

	/**
	 *
	 * @return type
	 */
	function __construct() {
		return;
	}

	static function db() {
		if ( empty ( static::$_dbh) ) {
			static::$_dbh = new PDO(
						static::$_connectionString,
						static::$_user,
						static::$_pass );
		}
		return static::$_dbh;
	}

	/**
	 *
	 * @param int $userId User ID
	 * @param int $taxYear Tax Year
	 * @return array()
	 */
	static function getData(
			$userId = 2,
			$taxYear = 2010) {

		try {
			$dbh = static::db();
			$stmt = $dbh->prepare(
					"SELECT
						r.*,
						SUBSTRING_INDEX(line_id,'_',2) AS line_type, -- Line ID prefix
						SUBSTRING_INDEX(line_id,'_',-1) AS field_code -- Field code
					FROM result_data AS r
					WHERE user_id = ? AND tax_year = ?");
			$stmt->execute( array( $userId, $taxYear ));
			$rows = $stmt->fetchAll(PDO::FETCH_CLASS);
			//$dbh = null;
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}

		return $rows;
	}

	/**
	 * Retrieves and returns SFD data set with linked partners
	 * @param int $userId User ID
	 * @param int $taxYear Tax Year
	 * @return array()
	 */
	static function getSFDData(
			$userId = 2,
			$taxYear = 2010) {

		try {
			$dbh = static::db();
			$stmt = $dbh->prepare(
					"SELECT s.*
					FROM sfd_data AS s
					WHERE s.user_id = ? AND s.tax_year = ?");
			$stmt->execute( array( $userId, $taxYear ));

			$rows = $stmt->fetchAll(PDO::FETCH_CLASS);

			// Partners
			$stmt = $dbh->prepare(
					"SELECT p.*
					FROM partner AS p JOIN sfd_data AS s
					  ON s.id = p.SFD_ID
					WHERE s.user_id = ? AND s.tax_year = ?");
			$stmt->execute( array( $userId, $taxYear ));

			$partnerRows = $stmt->fetchAll(PDO::FETCH_CLASS);
			// array of arrays:
			$partners = array();
			foreach ($partnerRows as $row) {
				$partners[$row->SFD_ID][] = $row;
			}
			// link partners to SFD rows:
			foreach ( $rows as $row ) {
				if (!empty($partners[$row->id])) $row->partners = $partners[$row->id];
			}

			//$dbh = null;
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}

		return $rows;
	}
}