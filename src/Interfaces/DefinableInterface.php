<?php


namespace Internet\InterDB\Interfaces;


interface DefinableInterface {
	public function toSQL(): string;
}