<?php

class CrunchyrollApiWrapper extends IngestionApiWrapper {

	protected static $API_URL = 'http://www.crunchyroll.com/syndication/feed?type=episodes&id=$1&affiliate_code=af-90111-uhny';
	protected static $CACHE_KEY = 'crunchyrollapi';
	protected static $CACHE_KEY_VERSION = 0.2;
	protected static $aspectRatio = 1.7777778;

	protected $ingestion = true;

	/**
	 * @inheritdoc
	 */
	public static function isMatchingHostname( $hostname ) {
		return endsWith( $hostname, "crunchyroll.com" );
	}

	/**
	 * @inheritdoc
	 */
	public static function newFromUrl( $url ) {
		wfProfileIn( __METHOD__ );

		$apiWrapper = null;

		$url = trim( $url );

		// check for customized video
		$parsed = explode( "?", $url );
		if ( !empty( $parsed ) && is_array( $parsed ) ) {
			$query = explode( 'clipid=', array_pop( $parsed ) );
			$videoId = array_pop( $query );

			$apiWrapper = new static( $videoId );
			$apiWrapper->ingestion = false;
		}

		wfProfileOut( __METHOD__ );

		return $apiWrapper;
	}

	/**
	 * @inheritdoc
	 */
	public function isIngestion() {
		return $this->ingestion;
	}

	/**
	 * @inheritdoc
	 */
	public function getDescription() {
		wfProfileIn( __METHOD__ );

		$description = $this->getOriginalDescription();

		wfProfileOut( __METHOD__ );

		return $description;
	}

	/**
	 * @inheritdoc
	 */
	public function getThumbnailUrl() {
		if ( !empty( $this->metadata['thumbnail'] ) ) {
			return $this->metadata['thumbnail'];
		}

		if ( !empty( $this->interfaceObj['thumbnail'] ) ) {
			return $this->interfaceObj['thumbnail'];
		}

		return '';
	}

	/**
	 * @inheritdoc
	 */
	public function getVideoTitle() {
		if ( !empty( $this->videoName ) ) {
			return $this->videoName;
		}

		return self::getClipName( $this->interfaceObj );
	}

	public static function getClipName( $content ) {
		$videoName = '';
		if ( !empty( $content['title']['name'] ) ) {
			$videoName = $content['title']['name'];
		}

		if ( !empty( $content['name'] ) ) {
			if ( !empty( $videoName ) ) {
				$videoName .= ' - ';
			}
			$videoName .= $content['name'];
		}

		return $videoName;
	}

	/**
	 * @inheritdoc
	 */
	protected function loadMetadata( array $overrideFields = [] ) {
		parent::loadMetadata( $overrideFields );

		if ( $this->isAgeGate() ) {
			throw new WikiaException( wfMsg( "videohandler-error-restricted-video" ) );
		}

		if ( !isset( $this->metadata['uniqueName'] ) ) {
			$this->metadata['uniqueName'] = $this->getUniqueName();
		}
		if ( !isset( $this->metadata['videoUrl'] ) ) {
			$this->metadata['videoUrl'] = $this->getVideoUrl();
		}
	}

	/**
	 * @return string
	 */
	protected function getOriginalDescription() {
		if ( !empty( $this->metadata['description'] ) ) {
			return $this->metadata['description'];
		}

		return '';
	}

	/**
	 * @inheritdoc
	 */
	protected function getVideoDuration() {
		if ( !empty( $this->metadata['duration'] ) ) {
			return $this->metadata['duration'];
		}

		return '';
	}

	/**
	 * @inheritdoc
	 */
	public function getAspectRatio() {
		return self::$aspectRatio;
	}

	/**
	 * @inheritdoc
	 */
	protected function getVideoPublished() {
		if ( !empty( $this->metadata['published'] ) ) {
			return $this->metadata['published'];
		}

		if ( !empty( $this->interfaceObj['title']['releaseDate'] ) ) {
			return ( $this->interfaceObj['title']['releaseDate'] / 1000 );
		}

		return '';
	}

	/**
	 * @inheritdoc
	 */
	protected function getVideoCategory() {
		if ( !empty( $this->metadata['category'] ) ) {
			return $this->metadata['category'];
		}

		return 'Movies';
	}

	/**
	 * @inheritdoc
	 */
	protected function getVideoName() {
		if ( !empty( $this->metadata['name'] ) ) {
			return $this->metadata['name'];
		}

		if ( !empty( $this->interfaceObj['title']['name'] ) ) {
			return $this->interfaceObj['title']['name'];
		}

		return '';
	}

	/**
	 * @inheritdoc
	 */
	protected function getIndustryRating() {
		if ( !empty( $this->metadata['industryRating'] ) ) {
			return $this->metadata['industryRating'];
		}

		if ( !empty( $this->interfaceObj['title']['mpaaRating'] ) ) {
			return $this->interfaceObj['title']['mpaaRating'];
		}

		return '';
	}

	/**
	 * @inheritdoc
	 */
	protected function getLanguage() {
		if ( !empty( $this->metadata['language'] ) ) {
			return $this->metadata['language'];
		}

		if ( !empty( $this->interfaceObj['language'] ) ) {
			return $this->interfaceObj['language'];
		}

		return '';
	}

	/**
	 * @inheritdoc
	 */
	protected function isHdAvailable() {
		if ( !empty( $this->metadata['hd'] ) ) {
			return $this->metadata['hd'];
		}

		return 0;
	}

	/**
	 * @inheritdoc
	 */
	protected function getAgeRequired() {
		if ( !empty( $this->metadata['ageRequired'] ) ) {
			return $this->metadata['ageRequired'];
		}

		// set default age required
		if ( !empty( $this->interfaceObj['restrictions'] ) ) {
			return 18;
		}

		return 0;
	}

	/**
	 * @inheritdoc
	 */
	protected function isAgeGate() {
		if ( !empty( $this->metadata['ageGate'] ) ) {
			return true;
		}

		if ( !empty( $this->interfaceObj['restrictions'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @inheritdoc
	 */
	protected function getVideoKeywords() {
		if ( !empty( $this->metadata['keywords'] ) ) {
			return $this->metadata['keywords'];
		}

		if ( !empty( $this->interfaceObj['tags'] ) ) {
			$tags = [];
			if ( is_array( $this->interfaceObj['tags'] ) ) {
				foreach ( $this->interfaceObj['tags'] as $tag ) {
					$tags[] = $tag['val'];
				}
			}

			return implode( ', ', $tags );
		}

		return '';
	}

	/**
	 * @inheritdoc
	 */
	protected function getGenres() {
		if ( !empty( $this->metadata['genres'] ) ) {
			return $this->metadata['genres'];
		}

		if ( !empty( $this->interfaceObj['title']['genres'] ) && is_array( $this->interfaceObj['title']['genres'] ) ) {
			return implode( ', ', $this->interfaceObj['title']['genres'] );
		}

		return '';
	}

	/**
	 * @inheritdoc
	 */
	protected function getActors() {
		if ( !empty( $this->metadata['actors'] ) ) {
			return $this->metadata['actors'];
		}

		if ( !empty( $this->interfaceObj['actors'] ) && is_array( $this->interfaceObj['actors'] ) ) {
			$actors = [];
			foreach ( $this->interfaceObj['actors'] as $actor ) {
				$actors[] = $actor['name'];
			}
			return implode( ', ', $actors );
		}

		return '';
	}

	protected function getUniqueName() {
		if ( !empty( $this->metadata['uniqueName'] ) ) {
			return $this->metadata['uniqueName'];
		}

		if ( !empty( $this->interfaceObj['uniqueName'] ) ) {
			return $this->interfaceObj['uniqueName'];
		}

		return '';
	}

	protected function getVideoUrl() {
		if ( !empty( $this->metadata['videoUrl'] ) ) {
			return $this->metadata['videoUrl'];
		}

		if ( !empty( $this->interfaceObj['urls'][0]['url'] ) ) {
			return $this->interfaceObj['urls'][0]['url'];
		}

		return '';
	}

	/**
	 * @inheritdoc
	 */
	protected function getVideoType() {
		if ( !empty( $this->metadata['type'] ) ) {
			return $this->metadata['type'];
		}

		return 'Clip';
	}

}