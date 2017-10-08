<?php namespace Test;

function apigen($data) {
	$return = array();
	foreach($data as $key=>$val) {
		$key = explode(':', $key);
		$sz = isset($key[1])? $key[1]: rand(0, 30);
		$key = $key[0];
		$return[$key] = isset($return[$key])? $return[$key]: array();
		if (is_callable($val)) {
			for($x=0; $x<$sz; $x++) {
				$return[$key][] = call_user_func($val, $x);
			}
		}
	}
	return $return;
}

class Aaa
{

	public function apiSearch()
	{
		return apigen(array(
			'users' => function($i) {
				$name = explode(',', 'ma,na,br');
				// array_shuffle($name);
				// $name = implode(' ', $name);
				return array(
					'name' => $name,
					'email' => "user-$i@mail.com",
				);
			},
		));
	}

}