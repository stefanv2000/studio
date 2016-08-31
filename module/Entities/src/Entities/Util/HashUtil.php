<?php

namespace Entities\Util;

class HashUtil {
	public static function generateHash(){
		return bin2hex(mcrypt_create_iv(22, MCRYPT_DEV_URANDOM));
	}
}

?>