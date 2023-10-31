<?php

namespace App\Libraries;
/*
PT ITSHOP BISNIS DIGITAL
Toko Online: ITShop Purwokerto (Tokopedia, Shopee, Bukalapak, Blibli)
Dibuat oleh: Hari Wicaksono, S.Kom
03-2023
*/

class Settings
{

	var $info = array();

	public function __construct()
	{
		$DB = \Config\Database::connect();
		$site = $DB->table('settings');

		foreach ($site->get()->getResult() as $set) {
			$key = $set->setting_variable;
			$value = $set->setting_value;
			$this->info[$key] = $value;
		}
	}
}
