<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This file defines the AutoloaderException_InternalClassNotLoadable.
 *
 * PHP version 5
 *
 * LICENSE: This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.
 * If not, see <http://php-autoloader.malkusch.de/en/license/>.
 *
 * @category  Autoloader
 * @package   Exception
 * @author    Markus Malkusch <markus@malkusch.de>
 * @copyright 2009 - 2010 Markus Malkusch
 * @license   http://php-autoloader.malkusch.de/en/license/ GPL 3
 * @version   SVN: $Id$
 * @link      http://php-autoloader.malkusch.de/en/
 */

/**
 * The parent class must be loaded. As this exception might be raised in the
 * InternalAutoloader, it is loaded by require_once.
 */
require_once dirname(__FILE__) . '/AutoloaderException.php';

/**
 * AutoloaderException_InternalClassNotLoadable is raised if the InternalAutoloader
 * couldn't load a class.
 *
 * @category  Autoloader
 * @package   Exception
 * @author    Markus Malkusch <markus@malkusch.de>
 * @copyright 2009 - 2010 Markus Malkusch
 * @license   http://php-autoloader.malkusch.de/en/license/ GPL 3
 * @version   Release: 1.8
 * @link      http://php-autoloader.malkusch.de/en/
 * @see       InternalAutoloader::doAutoload()
 */
class AutoloaderException_InternalClassNotLoadable extends AutoloaderException
{
    
}