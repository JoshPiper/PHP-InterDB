<?php

namespace Internet\InterDB;
use Internet\InterValid\EnvValidator;

/**
 * Handles caching of and access to DB objects, automatically loaded from .env file.
 */
Class EnvHandler extends DBHandler {
	public function __construct(){
		$ev = new EnvValidator();
		$conf = [];

		$defaults = array_filter([
			"host" => $ev->raw("DB_HOST") ?: null,
			"username" => $ev->raw("DB_USER") ?: null,
			"password" => $ev->raw("DB_PASS") ?: null,
			"port" => $ev->raw("DB_PORT") ?: null
		]);

		$find = $ev->commaList("DB_AUTOS");
		foreach ($find as $db){
			$conf[strtolower($db)] = array_merge($defaults, array_filter([
				"host" => $ev->raw("DB_{$db}_HOST"),
				"username" => $ev->raw("DB_{$db}_USER"),
				"password" => $ev->raw("DB_{$db}_PASS"),
				"port" => $ev->raw("DB_{$db}_PORT"),
				"db" => $ev->raw("DB_{$db}_DB")
			]));
		}

		parent::__construct($conf);
	}
}