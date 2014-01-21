<?php

/**
* Maintenance script to map metadata from video on video wiki to ooyala backlot
* This is one time use script
* @author Saipetch Kongkatong
*/

/**
 * Write data to file
 * @global boolean $dryRun
 * @param string $filename
 * @param string $msg
 * @param array $video
 */
function backupFile( $filename, $msg, $video ) {
	global $dryRun;

	if ( !$dryRun ) {
		file_put_contents( $filename, "$msg:\n".var_export( json_encode( $video ), true )."\n" , FILE_APPEND );
	}
}

/**
 * Map metadata
 * @global array $languageNames
 * @global array $countryNames
 * @global array $categories
 * @global boolean $iva
 * @param VideoFeedIngester $ingester
 * @param array $data
 * @return array $data
 */
function mapMetadata( $ingester, $data ) {
	global $languageNames, $countryNames, $categories, $iva;

	// default page categories
	if ( empty( $data['pagecategories'] ) ) {
		$pageCategories = array();
	} else {
		$pageCategories = array_map( 'trim', explode( ',', $data['pagecategories'] ) );
		$pageCategories = $ingester->getUniqueArray( $pageCategories );
	}

	// get name
	$keywords = array();
	if ( empty( $data['name'] ) && !empty( $data['keywords'] ) ) {
		foreach ( explode( ',' , $data['keywords'] ) as $keyword ) {
			$keyword = trim( $keyword );
			if ( empty( $keyword ) || strtolower( $keyword ) == 'the' ) {
				continue;
			}

			$keywords[] = $keyword;

			// remove page categories from keywords (for ooyala, iva)
			if ( !empty( $data['series'] ) ) {
				$categories[] = $data['series'];
			}
			if ( !empty( $data['provider'] ) ) {
				$categories[] = $data['provider'];
			}

			foreach ( $categories as $category ) {
				if ( strcasecmp( $keyword, $category ) == 0 ) {
					array_pop( $keywords );
					if ( !in_array( $category, $pageCategories ) ) {
						$pageCategories[] = $category;
					}
					break;
				}
			}
		}

		// use series if keywords is empty
		if ( empty( $keywords ) && !empty( $data['series'] ) ) {
			$keywords[] = $data['series'];
		}


		if ( !empty( $keywords ) && count( $keywords ) < 5 ) {
			$data['name'] = implode( ', ', $keywords );
			$keywords = array();
		}
	}

	// get keywords
	if ( !empty( $data['tags'] ) ) {
		if ( !empty( $keywords ) ) {
			$tags = array_map( 'trim', explode( ',', $data['tags'] ) );
			$keywords = array_merge( $keywords, $tags );
		} else {
			$data['keywords'] = preg_replace( '/,([^\s])/', ', $1', $data['tags'] );
		}
		$data['tags'] = null;
	}

	if ( !empty( $keywords ) ) {
		$keywords = $ingester->getUniqueArray( $keywords );
		foreach ( $keywords as $keyword ) {
			foreach ( $categories as $category ) {
				if ( strcasecmp( $keyword, $category ) == 0 ) {
					array_pop( $keywords );
					if ( !in_array( $category, $pageCategories ) ) {
						$pageCategories[] = $category;
					}
					break;
				}
			}
		}
		$data['keywords'] = implode( ', ', $keywords );
	}

	// get page categories
	if ( !empty( $pageCategories ) ) {
		$data['pagecategories'] = implode( ', ', $ingester->getUniqueArray( $pageCategories ) );
	}

	// get rating
	if ( !empty( $data['trailerrating'] ) ) {
		$data['industryrating'] = $data['trailerrating'];
		$data['trailerrating'] = null;
	}

	// get age_required
	if ( empty( $data['age_required'] ) && !empty( $data['agegate'] ) && is_numeric( $data['agegate'] ) ) {
		if ( $data['agegate'] > 1 ) {
			$data['age_required'] = $data['agegate'];
		} else if ( !empty( $data['industryrating'] ) ) {
			$data['age_required'] = $ingester->getAgeRequired( $data['industryrating'] );
		} else {
			$data['age_required'] = 18;	// default value for age required
		}
	}

	if ( array_key_exists( 'age_required', $data ) && $data['age_required'] < 1 ) {
		$data['age_required'] = null;
	}

	// get age gate
	if ( !empty( $data['age_required'] ) ) {
		$data['agegate'] = 1;
	} else if ( array_key_exists( 'agegate', $data ) ) {
		$data['agegate'] = null;
	}

	// get hd
	if ( !empty( $data['hd'] ) && ( strtolower( $data['hd'] ) == 'yes' || $data['hd'] == 1 ) ) {
		$data['hd'] = 1;
	} else if ( array_key_exists( 'hd', $data ) ) {
		$data['hd'] = null;
	}

	// get language
	if ( !empty( $data['lang'] ) && !empty( $languageNames ) && array_key_exists( $data['lang'], $languageNames ) ) {
		$data['lang'] = $languageNames[$data['language']];
	}

	// get subtitle
	if ( !empty( $data['subtitle'] ) && !empty( $languageNames ) && array_key_exists( $data['subtitle'], $languageNames ) ) {
		$data['subtitle'] = $languageNames[$data['subtitle']];
	}

	// get country code
	if ( !empty( $data['targetCountry'] ) && !empty( $countryNames ) && array_key_exists( $data['targetCountry'], $countryNames ) ) {
		$data['targetCountry'] = $countryNames[$data['targetCountry']];
	}

	// add page categories to metadata for IVA only
	if ( !empty( $iva ) ) {
		$data['pagecategories'] = empty( $data['pagecategories'] ) ? '' : $data['pagecategories'];
		if ( empty( $data['pagecategories'] ) ) {
			$metadata['pagecategories'] = '';
		} else {
			$categories = array_map( 'trim', explode( ',', $data['pagecategories'] ) );
			$categories = $ingester->generateCategories( $metadata, $categories );
			$metadata['pagecategories'] = implode( ', ', $categories );
		}
	}

	return $data;
}

/**
 * Mapping additional metadata for IVA
 * @param VideoFeedIngester $ingester
 * @param array $data
 * @param array $metadata
 */
function mapMetadataIva( $ingester, $data, &$metadata ) {
	// get category and type
	$metadata['category'] = $ingester->getCategory( $metadata['category'] );
	if ( !empty( $data['category'] ) ) {
		$metadata['type'] = $ingester->getStdType( $data['category'] );
	}

	// add page categories to metadata
	$metadata['pagecategories'] = empty( $data['pagecategories'] ) ? '' : $data['pagecategories'];
	if ( empty( $data['pagecategories'] ) ) {
		$metadata['pagecategories'] = '';
	} else {
		$categories = array_map( 'trim', explode( ',', $data['pagecategories'] ) );
		$categories = $ingester->generateCategories( $metadata, $categories );
		$metadata['pagecategories'] = implode( ', ', $categories );
	}
}


// ----------------------------- Main ------------------------------------

ini_set( "include_path", dirname( __FILE__ )."/../../" );

require_once( "commandLine.inc" );

if ( isset( $options['help'] ) ) {
	die( "Usage: php mapVideoMetadataOoyalaBacklot.php [--help] [--dry-run] [--backup=path/filename] [--name=abc] [--limit=10]
	--dry-run       dry run
	--name          specific video title only (optional)
	--iva           iva provider only
	--limit         mapping limit
	--backup        backup all videos data to file
	--help          you are reading it right now\n\n" );
}

if ( empty( $wgCityId ) ) {
	die( "Error: Invalid wiki id.\n" );
}

$dryRun = isset( $options['dry-run'] );
$videoTitle = isset( $options['name'] ) ? $options['name'] : '';
$iva = isset( $options['iva'] );
$backupFile = isset( $options['backup'] ) ? $options['backup'] : '';
$limit = isset( $options['limit'] ) ? $options['limit'] : 0;

$apiPageSize = 100;
if ( !empty( $limit ) && $limit < $apiPageSize ) {
	$apiPageSize = $limit;
}

echo "Wiki: $wgCityId ($wgDBname)\n";
if ( !empty( $videoTitle ) ) {
	echo "Video Title: $videoTitle\n";
}
echo "Provider: ".( empty( $iva ) ? 'ALL except IVA' : 'IVA' )."\n";
if ( !empty( $limit ) ) {
	echo "Limit: $limit\n";
}
if ( !empty( $backupFile ) ) {
	echo "Backup File: $backupFile\n";
}

// include cldr extension for language code ($languageNames), country code ($countryNames)
include( dirname( __FILE__ ).'/../../../extensions/cldr/CldrNames/CldrNamesEn.php' );

$extraCond[] = "created_at < '2013-08-29T00:00:00Z'";

if ( !empty( $videoTitle ) ) {
	$extraCond[] = "name='$videoTitle'";
}

if ( $iva ) {
	$provider = 'iva';
	$isRemoteAsset = true;
	$extraCond[] = "asset_type='remote_asset'";
} else {
	$provider = 'ooyala';
	$isRemoteAsset = false;
	$extraCond[] = "asset_type!='remote_asset'";
}

$ingester = VideoFeedIngester::getInstance( $provider );
// get WikiFactory data
$ingestionData = $ingester->getWikiIngestionData();
if ( empty( $ingestionData ) ) {
	die( "No ingestion data found in wikicities. Aborting.\n" );
}

// keywords for page categories
$categories = array(
	'International', 'Foreign', 'Movies', 'TV', 'Gaming', 'Games', 'Others', 'Entertainment', 'Trailers', 'Webinars', 'Support',
	'Community', 'Clips', 'Anime', 'Gameplay', 'Books', 'Trailer', 'Television', 'Interviews', 'SciFi', 'Featurettes', 'Featurette',
	'Community Support', 'How-to', 'How To', 'Intro', 'Comedy', 'Animation', 'Overhaul', 'Wikia Webinars', 'Epic', 'SOE', 'EA', 'Konami',
	'ubisoft', 'WBIE', 'Comics', 'Comic-Con', 'Food', 'Walkthroughs', 'Walkthrough', 'Remake', 'Expert Showcase', 'Wikia Productions',
	'Adventure', 'Pixar',
	'Disney', 'RPG', 'Namco Bandai', 'Cartoon', 'Wikia Fan Media', 'Action', 'Multiplayer', 'Online', 'Marvel', 'Walt Disney', 'Shooter',
);

$nextPage = '';
$page = 1;
$total = 0;
$failed = 0;
$skipped = 0;

do {
	// connect to provider API
	$url = OoyalaAsset::getApiUrlAssets( $apiPageSize, $nextPage, $extraCond );
	echo "\nConnecting to $url...\n" ;

	$response = OoyalaAsset::getApiContent( $url );
	if ( $response === false ) {
		exit();
	}

	$videos = empty( $response['items'] ) ? array() : $response['items'];
	$nextPage = empty( $response['next_page'] ) ? '' : $response['next_page'];

	$total += count( $videos );

	$cnt = 0;
	foreach ( $videos as $video ) {
		$cnt++;
		$title = trim( $video['name'] );
		$msg = "[Page $page: $cnt of $total] Video: $title ({$video['embed_code']})";
		echo "\n$msg";

		// backup data to csv file
		if ( !empty( $backupFile ) ) {
			backupFile( $backupFile, $msg, $video );
		}

		// skip if 'name' field exists
		if ( array_key_exists( 'name', $video['metadata'] ) ) {
			echo " ... SKIPPED ('name' field found in metadata. metadata already mapped).\n";
			$skipped++;
			continue;
		}

		// get provider
		$label = OoyalaApiWrapper::getProviderName( $video['labels'] );
		$label = empty( $label ) ? "No provider name" : OoyalaApiWrapper::formatProviderName( $label );
		echo " ($label)";

		$newMeta = mapMetadata( $ingester, $video['metadata'] );
		if ( !empty( $iva ) ) {
			mapMetadataIva( $ingester, $video['metadata'], $newMeta );
		}

		if ( !array_key_exists( 'name', $newMeta ) ) {
			$newMeta['name'] = '';
		}

		echo " ... DONE \n\tNEW Metadata:\n";
		$fields = array_unique( array_merge( array_keys( $newMeta ), array_keys( $video['metadata'] ) ) );
		foreach ( $fields as $field ) {
			if ( ( !isset( $newMeta[$field] ) || is_null( $newMeta[$field] ) ) && isset( $video['metadata'][$field] ) ) {
				echo "\t\t[DELETED] $field: ".$video['metadata'][$field]."\n";
			} else if ( isset( $newMeta[$field] ) && !isset( $video['metadata'][$field] ) ) {
				echo "\t\t[NEW] $field: $newMeta[$field]\n";
			} else if ( strcasecmp( $video['metadata'][$field], $newMeta[$field] ) == 0 ) {
				echo "\t\t$field: $newMeta[$field]\n";
			} else {
				echo "\t\t[UPDATED]$field: $newMeta[$field] (Old value: ".$video['metadata'][$field].")\n";
			}
		}

		if ( !$dryRun ) {
			$resp = OoyalaAsset::updateMetadata( $video['embed_code'], $newMeta );
			if ( !$resp ) {
				$failed++;
			}
		}
	}

	$page++;
} while( !empty( $nextPage ) && $total < $limit );

echo "\nTotal videos: ".$total.", Success: ".( $total - $failed - $skipped ).", Failed: $failed, Skipped: $skipped\n\n";
