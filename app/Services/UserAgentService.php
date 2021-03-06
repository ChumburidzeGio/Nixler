<?php

namespace App\Services;

class UserAgentService
{
	/**
	 * Parses a user agent string into its important parts
	 *
	 * @return string
	 */
	function forHumans( $u_agent = null ) {

		$agent = $this->parse($u_agent);

		return array_get($agent, 'browser') . ' on ' . array_get($agent, 'platform') . ' ' . array_get($agent, 'platform_version');

	}



	/**
	 * Parses a user agent string into its important parts
	 *
	 * @author Jesse G. Donat <donatj@gmail.com>
	 * @link https://github.com/donatj/PhpUserAgent
	 * @link http://donatstudios.com/PHP-Parser-HTTP_USER_AGENT
	 * @param string|null $u_agent User agent string to parse or null. Uses $_SERVER['HTTP_USER_AGENT'] on NULL
	 * @throws InvalidArgumentException on not having a proper user agent to parse.
	 * @return string[] an array with browser, version and platform keys
	 */
	function parse( $u_agent = null ) {

		$u_agent = $u_agent ?? request()->header('User-Agent');

		$platform = null;
		$browser  = null;
		$version  = null;

		$platform_version = null;

		$empty = array( 'platform' => $platform, 'browser' => $browser, 'version' => $version, 'platform_version' => $platform_version );

		if( !$u_agent ) return $empty;

		if( preg_match('/\((.*?)\)/im', $u_agent, $parent_matches) ) {

			$platform = $this->getPlatform($parent_matches);

		}

		preg_match_all('%(?P<browser>Camino|Kindle(\ Fire)?|Firefox|Iceweasel|Safari|MSIE|Trident|AppleWebKit|TizenBrowser|Chrome|
			Vivaldi|IEMobile|Opera|OPR|Silk|Midori|Edge|CriOS|
			Baiduspider|Googlebot|YandexBot|bingbot|Lynx|Version|Wget|curl|
			NintendoBrowser|PLAYSTATION\ (\d|Vita)+)
			(?:\)?;?)
			(?:(?:[:/ ])(?P<version>[0-9A-Z.]+)|/(?:[A-Z]*))%ix',
			$u_agent, $result, PREG_PATTERN_ORDER);

		// If nothing matched, return null (to avoid undefined index errors)
		if( !isset($result['browser'][0]) || !isset($result['version'][0]) ) {
			if( preg_match('%^(?!Mozilla)(?P<browser>[A-Z0-9\-]+)(/(?P<version>[0-9A-Z.]+))?%ix', $u_agent, $result) ) {
				return array( 'platform' => $platform ?: null, 'browser' => $result['browser'], 'version' => isset($result['version']) ? $result['version'] ?: null : null, 'platform_version' => null );
			}

			return $empty;
		}

		if( preg_match('/rv:(?P<version>[0-9A-Z.]+)/si', $u_agent, $rv_result) ) {
			$rv_result = $rv_result['version'];
		}

		$browser = $result['browser'][0];
		$version = $result['version'][0];

		$lowerBrowser = array_map('strtolower', $result['browser']);

		$find = function ( $search, &$key ) use ( $lowerBrowser ) {
			$xkey = array_search(strtolower($search), $lowerBrowser);
			if( $xkey !== false ) {
				$key = $xkey;

				return true;
			}

			return false;
		};

		$key  = 0;
		$ekey = 0;
		if( $browser == 'Iceweasel' ) {
			$browser = 'Firefox';
		} elseif( $find('Playstation Vita', $key) ) {
			$platform = 'PlayStation Vita';
			$browser  = 'Browser';
		} elseif( $find('Kindle Fire', $key) || $find('Silk', $key) ) {
			$browser  = $result['browser'][$key] == 'Silk' ? 'Silk' : 'Kindle';
			$platform = 'Kindle Fire';
			if( !($version = $result['version'][$key]) || !is_numeric($version[0]) ) {
				$version = $result['version'][array_search('Version', $result['browser'])];
			}
		} elseif( $find('NintendoBrowser', $key) || $platform == 'Nintendo 3DS' ) {
			$browser = 'NintendoBrowser';
			$version = $result['version'][$key];
		} elseif( $find('Kindle', $key) ) {
			$browser  = $result['browser'][$key];
			$platform = 'Kindle';
			$version  = $result['version'][$key];
		} elseif( $find('OPR', $key) ) {
			$browser = 'Opera Next';
			$version = $result['version'][$key];
		} elseif( $find('Opera', $key) ) {
			$browser = 'Opera';
			$find('Version', $key);
			$version = $result['version'][$key];
		} elseif( $find('Midori', $key) ) {
			$browser = 'Midori';
			$version = $result['version'][$key];
		} elseif( $browser == 'MSIE' || ($rv_result && $find('Trident', $key)) || $find('Edge', $ekey) ) {
			$browser = 'MSIE';
			if( $find('IEMobile', $key) ) {
				$browser = 'IEMobile';
				$version = $result['version'][$key];
			} elseif( $ekey ) {
				$version = $result['version'][$ekey];
			} else {
				$version = $rv_result ?: $result['version'][$key];
			}

			if( version_compare($version, '12', '>=') ) {
				$browser = 'Edge';
			}
		} elseif( $find('Vivaldi', $key) ) {
			$browser = 'Vivaldi';
			$version = $result['version'][$key];
		} elseif( $find('Chrome', $key) || $find('CriOS', $key) ) {
			$browser = 'Chrome';
			$version = $result['version'][$key];
		} elseif( $browser == 'AppleWebKit' ) {
			if( ($platform == 'Android' && !($key = 0)) ) {
				$browser = 'Android Browser';
			} elseif( strpos($platform, 'BB') === 0 ) {
				$browser  = 'BlackBerry Browser';
				$platform = 'BlackBerry';
			} elseif( $platform == 'BlackBerry' || $platform == 'PlayBook' ) {
				$browser = 'BlackBerry Browser';
			} elseif( $find('Safari', $key) ) {
				$browser = 'Safari';
			} elseif( $find('TizenBrowser', $key) ) {
				$browser = 'TizenBrowser';
			}

			$find('Version', $key);

			$version = $result['version'][$key];
		} elseif( $key = preg_grep('/playstation \d/i', array_map('strtolower', $result['browser'])) ) {
			$key = reset($key);

			$platform = 'PlayStation ' . preg_replace('/[^\d]/i', '', $key);
			$browser  = 'NetFront';
		}

		if( $platform == 'Kindle' && $find('Kindle', $key) ) {
			$platform_version = $result['version'][$key];
		} elseif( !empty($parent_matches[1]) && preg_match('/(?:Mac OS X (?P<version>[0-9_.]+))|(?:Windows (?:NT|Phone)*(?: OS)* *(?P<version2>[0-9_.]+))|(?:Android (?P<version3>[^;)]+))|(?:Linux (?P<version4>[^;)]+))|(?:(?:iPhone|CPU) OS (?P<version5>[0-9_.]+))/i', $parent_matches[1], $regs) ) {

			$platform_version = @trim($regs['version'] . $regs['version1'] . $regs['version2'] . $regs['version3'] . $regs['version4'] . $regs['version5']);

			if( $platform == 'Windows' ) {
				$ver = array( '5.0' => '2000', '5.1' => 'XP', '5.2' => 'XP64', '6.0' => 'Vista', '6.1' => '7', '6.2' => '8', '6.3' => '8.1', '6.4' => '10.0' );

				$platform_version = isset($ver[$platform_version]) ? $ver[$platform_version] : $platform_version;
			}

			if( $platform == 'Macintosh' ) {

				$platform = 'macOS';

				$platform_version = array_first(explode('_', $platform_version));

			}

			$platform_version = str_replace('_', '.', $platform_version);
		} else {
			$result = "";
		}

		return array( 'platform' => $platform ?: null, 'browser' => $browser ?: null, 'version' => $version ?: null, 'platform_version' => $platform_version ?: null );
	}


	/**
	 * Parses platform
	 *
	 * @return string
	 */
	function getPlatform( $parent_matches ) {

		$platform = null;

		preg_match_all('/(?P<platform>BB\d+;|Android|CrOS|Tizen|iPhone|iPad|iPod|Linux|Macintosh|Windows
			(\ Phone)?|Silk|linux-gnu|BlackBerry|PlayBook|(New\ )?Nintendo\ (WiiU?|3?DS)|Xbox(\ One)?)
			(?:\ [^;]*)?
			(?:;|$)/imx', $parent_matches[1], $result, PREG_PATTERN_ORDER);

		$priority = array( 'Xbox One', 'Xbox', 'Windows Phone', 'Tizen', 'Android' );

		$result['platform'] = array_unique($result['platform']);

		if( count($result['platform']) > 1 ) {

			if( $keys = array_intersect($priority, $result['platform']) ) {
				$platform = reset($keys);
			} else {
				$platform = $result['platform'][0];
			}

		} elseif( isset($result['platform'][0]) ) {
			$platform = $result['platform'][0];
		}

		if( $platform == 'linux-gnu' ) {
			$platform = 'Linux';
		} elseif( $platform == 'CrOS' ) {
			$platform = 'Chrome OS';
		}

		return $platform;

	}
}