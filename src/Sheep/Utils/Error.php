<?php
declare(strict_types = 1);


namespace Sheep\Utils;


class Error {
	const E_UNKNOWN = 0;

	const E_PLUGIN_URI_INVALID         = 1;
	const E_PLUGIN_ALREADY_INSTALLED   = 2;
	const E_PLUGIN_MULTIPLE_CANDIDATES = 3;
	const E_PLUGIN_NO_CANDIDATES	   = 4;

	const E_CURL_ERROR = 5;

	protected $message;
	protected $code;

	public function __construct(string $message, int $code = Error::E_UNKNOWN) {
		$this->message = $message;
		$this->code = $code;
	}

	public function __toString() : string {
		return $this->message;
	}
}