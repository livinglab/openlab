<?php

/**
 * Recurses each directory and runs PHP's lint function against each file
 * to test for parse errors.
 *
 * @param	string	$dir	the directory you would like to start from
 * @return 	array		the files that did not pass the test
 */
function lint( $dir = 'C:\dev\\' )
{
	static $failed = array();

	$excluded_dirs = [
		'/wp-content/blogs.dir',
		'/wp-content/uploads',
		'/wp-content/plugins/buddypress/bp-forums/bbpress',
		'/wp-content/plugins/wp-document-revisions/tests',
		'/wp-content/plugins/anthologize/templates/epub/pear_ext', // Not loaded because we have zlib.
	];

	$excluded_files = [
		'/wp-content/plugins/btcnew/parser_php4.php', // Not loaded on later PHP.
		'/wp-content/plugins/backtype-connect/parser_php4.php', // Not loaded on later PHP.
	];

	foreach ( new RecursiveDirectoryIterator($dir) as $path => $objSplFileInfo )
	{
		// recurse if dir
		if ( $objSplFileInfo->isDir() )
		{
			if ( stristr( $objSplFileInfo->getFileName(), '.svn' ) !== false )
			{
				continue;
			}

			if ( '.' === $objSplFileInfo->getFileName() || '..' === $objSplFileInfo->getFileName() ) {
				continue;
			}

			$relativePath = getRelativePath( $objSplFileInfo->getRealPath() );

			if ( in_array( $relativePath, $excluded_dirs, true ) ) {
				continue;
			}

			lint( $objSplFileInfo->getPathName() );

			continue;
		}

		// are there any non-dirs that aren't files?
		if ( !$objSplFileInfo->isFile() )
		{
			throw new UnexpectedValueException( 'Not a dir and not a file?' );
		}

		// skip non-php files
		if ( preg_match( '#\.php$#', $objSplFileInfo->getFileName() ) !== 1 )
		{
			continue;
		}

		// Blacklist.
		$relativePath = getRelativePath( $objSplFileInfo );
		if ( in_array( $relativePath, $excluded_files, true ) ) {
			continue;
		}

		// perform the lint check
		$result = exec( 'php -l '. escapeshellarg($objSplFileInfo) );
		if ( preg_match( '#^No syntax errors detected in#', $result ) !== 1 )
		{
			$failed[ $objSplFileInfo->getPathName() ] = $result;
		}

	}

	echo '.';
	return $failed;
}

function getRelativePath( $path ) {
	return str_replace( realpath( __DIR__ . '/../' ), '', $path );
}

echo "Linting...";
$failed = lint( realpath( __DIR__ . '/../' ) );
echo "\n";
if ( empty( $failed ) ) {
	echo "All checks passed.";
	exit( 0 );
} else {
	echo "Errors found in the following files:";
	print_r( $failed );
	exit( 1 );
}
