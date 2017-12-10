<?php

namespace DataMap\Output;

interface Formatter
{
	/**
	 * @return mixed
	 */
	public function format(array $output);
}
