<?php
/**
 * Portfolio Exporter Class.
 */

namespace OpenLab\Portfolio\Export;

use WP_Error;
use ZipArchive;
use OpenLab\Portfolio\Iterator\UploadsIterator;

class Exporter {

	/**
	 * Files to export.
	 *
	 * @var array
	 */
	protected $files = [];

	/**
	 * Cached value of `wp_upload_dir()`.
	 *
	 * @var array
	 */
	public $uploads_dir = [];

	/**
	 * Exports directory.
	 *
	 * @var string
	 */
	public $exports_dir;

	/**
	 * Exports URL
	 *
	 * @var string
	 */
	public $exports_url;

	/**
	 * Create export object.
	 *
	 * @param array $upload_dir
	 */
	public function __construct( array $upload_dir ) {
		$this->uploads_dir = $upload_dir;
		$this->exports_dir = trailingslashit( $upload_dir['basedir'] ) . 'ol-portfolio-exports/';
		$this->exports_url = trailingslashit( $upload_dir['baseurl'] ) . 'ol-portfolio-exports/';
	}

	/**
	 * Start export process.
	 *
	 * @return void
	 */
	public function run() {
		$dest = $this->create_dest();

		if ( is_wp_error( $dest ) ) {
			return $dest;
		}

		$manifest = $this->create_manifest();
		if ( is_wp_error( $manifest ) ) {
			return $manifest;
		}

		$export = $this->create_wxp();
		if ( is_wp_error( $export ) ) {
			return $export;
		}

		$this->prepare_files( $this->uploads_dir['basedir'] );

		return $this->archive();
	}

	/**
	 * Create export destination.
	 *
	 * @return \WP_Error|bool
	 */
	protected function create_dest() {
		if ( ! wp_mkdir_p( $this->exports_dir ) ) {
			return new WP_Error( 'ol.exporter.create.dest', 'Unable to create export folder.' );
		}

		return true;
	}

	/**
	 * Prepare backups files. Image uploads, etc.
	 *
	 * @return WP_Error|void
	 */
	protected function prepare_files( $folder ) {
		$folder = trailingslashit( $folder );

		if ( ! is_dir( $folder ) ) {
			return new WP_Error(
				'ol.exporter.prepare.files',
				sprintf( 'Folder %s does not exist.', $folder )
			);
		}

		if ( ! is_readable( $folder ) ) {
			return new WP_Error(
				'ol.exporter.prepare.files',
				sprintf( 'Folder %s is not readable.', $folder )
			);
		}

		try {
			$iterator = UploadsIterator::create( $folder );

			foreach ( $iterator as $file ) {
				// Ignore thumbnails.
				if ( preg_match( "/\-[0-9]{1,4}x[0-9]{1,4}.+\.(jpg|png|gif)$/i", $file->getFilename() ) ) {
					continue;
				}

				$this->files[] = $file->getPathname();
			}
		} catch ( UnexpectedValueException $e ) {
			return new WP_Error(
				'ol.exporter.prepare.files',
				sprintf( 'Could not open path: %', $e->$e->getMessage() )
			);
		}
	}

	/**
	 * Create export WXP.
	 *
	 * @return WP_Error|bool
	 */
	protected function create_wxp() {
		$wxp = new WXP( $this->exports_dir . 'wordpress.xml' );

		if ( ! $wxp->create() ) {
			return new WP_Error(
				'ol.exporter.create.wxp',
				'Unable to create WXP export file.'
			);
		}

		return true;
	}

	/**
	 * Create exported portfolio site manifest.
	 *
	 * @return WP_Error|bool
	 */
	protected function create_manifest() {
		$manifest = [];

		$manifest['site']['url']         = home_url();
		$manifest['site']['name']        = get_bloginfo( 'name' );
		$manifest['site']['description'] = get_option( 'blogdescription' );
		$manifest['site']['admin_email'] = get_option( 'admin_email' );
		$manifest['site']['charset']     = get_bloginfo( 'charset' );
		$manifest['site']['name']        = get_bloginfo( 'name' );
		$manifest['site']['uploads']     = $this->uploads_dir;

		if ( ! file_put_contents( $this->exports_dir . 'manifest.json', json_encode( $manifest ) ) ) {
			return new WP_Error(
				'ol.exporter.create.manifest',
				'Unable to create manifest.json file.'
			);
		}

		return true;
	}

	/**
	 * Save export files into archive.
	 *
	 * @return void
	 */
	protected function archive() {
		if ( ! class_exists( 'ZipArchive' ) ) {
			return new WP_Error(
				'ol.exporter.archive',
				'Unable to generate export file. ZipArchive not available.'
			);
		}

		$archive_filename = $this->filename();
		$archive_pathname = $this->exports_dir . $archive_filename;

		if ( file_exists( $archive_pathname ) ) {
			wp_delete_file( $archive_pathname );
		}

		$zip = new ZipArchive;
		if ( true !== $zip->open( $archive_pathname, ZipArchive::CREATE ) ) {
			return new WP_Error(
				'ol.exporter.archive',
				'Unable to add data to export file.'
			);
		}

		$zip->addFile( $this->exports_dir . 'manifest.json', 'manifest.json' );
		$zip->addFile( $this->exports_dir . 'wordpress.xml', 'wordpress.xml' );

		foreach ( $this->files as $file ) {
			$zip->addFile( $file, $this->normalize_path( $file ) );
		}

		$zip->close();

		// Remove Manifest and export files.
		unlink( $this->exports_dir . 'manifest.json' );
		unlink( $this->exports_dir . 'wordpress.xml' );

		return $archive_pathname;
	}

	/**
	 * Generate export filename.
	 *
	 * @return string $filename
	 */
	protected function filename() {
		$stripped_url = sanitize_title_with_dashes( get_bloginfo( 'name' ) );
		$timestamp    = date( 'Y-m-d' );
		$filename     = "export-{$stripped_url}-{$timestamp}.zip";

		return $filename;
	}

	/**
	 * Change file path for better storing in archive.
	 *
	 * @param string $file
	 * @return string
	 */
	protected function normalize_path( $file ) {
		$abs_path = realpath( ABSPATH );
		$abs_path = trailingslashit( str_replace( '\\', '/', $abs_path ) );

		return str_replace( [ '\\', $abs_path ], '/', $file );
	}
}
