<?php
namespace AppBundle\Util;

class StringOperations{

	/**
	 * Return the randomly generated seed for registration key
	 *
	 * @param int $length
	 * @return string
	 */
	public static function generate_random_string($length=32){
		//Allowed random string characters
		$seeds='abcdefghijklmnopqrstuvwxyz0123456789';

		//generate the random string
		$str="";
		$count=strlen($seeds);
		for($i=0;$i<$length;$i++){
			$str.=$seeds[mt_rand(0,$count-1)];
		}
		return $str;
	}
}