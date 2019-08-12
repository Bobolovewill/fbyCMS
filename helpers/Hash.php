<?php

	class Hash {
		public static function password_hash_function($password) {
			$options = [
				'cost' => 11,
				'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM),
			];
			return password_hash($password, PASSWORD_BCRYPT, $options);
		}

		public static function password_verify_function($password, $hash) {
			return password_verify($password, $hash);
		}
	}