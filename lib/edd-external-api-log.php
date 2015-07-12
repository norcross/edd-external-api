<?php
/**
 * EDD_External_API logging functions
 *
 */
if ( ! class_exists( 'EDD_External_Purchase_API_Log' ) ) {

class EDD_External_Purchase_API_Log {

	/**
	 * create our new table for storing the log entries
	 *
	 * @return void
	 */
	public static function create_table() {

		// bail if disabled
		if ( false === apply_filters( 'edd_external_logging', true ) ) {
			return;
		}

		// call the global
		global $wpdb;

		// set the name
		$name   = $wpdb->prefix . 'edd_external_log';

		// set blank collate
		$charset_collate = '';

		// set charset
		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
		}

		// set collate
		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE {$wpdb->collate}";
		}

		// create the table
		$table  = "CREATE TABLE $name (
			ID int(11) NOT NULL AUTO_INCREMENT,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			type varchar(20) DEFAULT NULL,
			trans_id int(12) DEFAULT NULL,
			request text DEFAULT NULL,
			result bool DEFAULT NULL,
			error text DEFAULT NULL,
			PRIMARY KEY  (ID)
		) $charset_collate;";

		// include the upgrade file
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		// make it
		dbDelta( $table );

		// and bail
		return;
	}

	/**
	 * create a log entry
	 *
	 * @param  string $type [description]
	 *
	 * @return [type]       [description]
	 */
	public static function create_log_entry( $type = 'purchase', $info = array() ) {

		// bail if disabled
		if ( false === apply_filters( 'edd_external_logging', true ) ) {
			return;
		}

		// call the global
		global $wpdb;

		// set the name
		$name   = $wpdb->prefix . 'edd_external_log';

		// create the entry
		$wpdb->insert(
			// table name
			$name,
			// data fields themselves
			array(
				'ID'        => '',
				'time'      => date( 'Y-m-d H:i:s', strtotime( 'NOW', current_time( 'timestamp' ) ) ),
				'type'      => $type,
				'trans_id'  => '',
				'request'   => serialize( $info ),
				'result'    => '',
				'error'     => ''
			),
			// field types (string or numeric)
			array(
				'%d',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s'
			)
		);

		// send back the item
		return $wpdb->insert_id;
	}

	/**
	 * update an existing log entry
	 *
	 * @param  integer $log_id     [description]
	 * @param  string  $type       [description]
	 * @param  integer $trans_id   [description]
	 * @param  string  $result     [description]
	 * @param  string  $error      [description]
	 * @return [type]              [description]
	 */
	static function update_log_entry( $log_id = 0, $type = 'purchase', $trans_id = 0, $result = '', $error = '' ) {

		// bail if disabled
		if ( false === apply_filters( 'edd_external_logging', true ) ) {
			return;
		}

		// bail without a log ID
		if ( empty( $log_id ) ) {
			return;
		}

		// call the global
		global $wpdb;

		// set the name
		$name   = $wpdb->prefix . 'edd_external_log';

		// create the entry
		$wpdb->update(
			// table name
			$name,
			// data fields themselves
			array(
				'type'      => $type,
				'trans_id'  => $trans_id,
				'result'    => $result,
				'error'     => $error
			),
			// our ID to update
			array( 'ID' => $log_id ),
			// field types (string or numeric)
			array(
				'%s',
				'%d',
				'%d',
				'%s'
			)
		);

		// and finish
		return;
	}

// end class
}

// end exists check
}

// load our class
new EDD_External_Purchase_API_Log();