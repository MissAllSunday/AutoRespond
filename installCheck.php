<?php

function installCheck(): void
{
	if (version_compare(\PHP_VERSION, '7.4.0', '<')) {
		fatal_error('This mod needs PHP 7.4 or greater.
		 You will not be able to install/use this mod,contact your host and ask for a PHP upgrade.');
	}
}

installCheck();