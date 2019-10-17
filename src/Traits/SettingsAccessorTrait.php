<?php

namespace Internet\InterDB\Traits;
use Internet\InterDB\Exceptions\DSNCreationException;

trait SettingsAccessorTrait {
	/** Remap an array member.
	 * @param $settings
	 * @param $from
	 * @param $to
	 */
	static function remap(&$settings, $from, $to){
		if (isset($settings[$from])){
			$settings[$to] = $settings[$from];
			if ($from !== $to){
				unset($settings[$from]);
			}
		}
	}

	/** Check for a required setting.
	 * @param $settings
	 * @param $key
	 * @return mixed
	 * @throws DSNCreationException
	 */
	static function required($settings, $key){
		if (!isset($settings[$key])){
			throw new DSNCreationException("Missing Setting Key ${key}.");
		}

		return $settings[$key];
	}

	/** Add a default setting.
	 * @param $settings
	 * @param $key
	 * @param $default
	 * @return mixed
	 */
	static function optional(&$settings, $key, $default){
		if (!isset($settings[$key])){
			$settings[$key] = $default;
		}

		return $settings[$key];
	}
}