<?php

namespace DataMap\Output;

interface Formatter
{
	/**
     * @param array<string, mixed> $output
	 * @return mixed
	 */
	public function format(array $output);
}
