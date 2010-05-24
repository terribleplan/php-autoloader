<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This file defines the class AutoloaderIndex.
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
 * @package   Index
 * @author    Markus Malkusch <markus@malkusch.de>
 * @copyright 2009 - 2010 Markus Malkusch
 * @license   http://php-autoloader.malkusch.de/en/license/ GPL 3
 * @version   SVN: $Id$
 * @link      http://php-autoloader.malkusch.de/en/
 */

/**
 * The AutoloaderIndex stores the location of class defintions for speeding up
 * recurring searches.
 *
 * Searching a class definition in the filesystem takes a lot of time, as every
 * file is read. To avoid these long searches, a found class definition will be
 * stored in an index. The next search for an already found class definition
 * will take no time.
 *
 * @category  Autoloader
 * @package   Index
 * @author    Markus Malkusch <markus@malkusch.de>
 * @copyright 2009 - 2010 Markus Malkusch
 * @license   http://php-autoloader.malkusch.de/en/license/ GPL 3
 * @version   Release: 1.8
 * @link      http://php-autoloader.malkusch.de/en/
 * @see       Autoloader::setIndex()
 * @see       Autoloader::getIndex()
 */
abstract class AutoloaderIndex implements Countable
{

    private
    /**
     * @var Array
     */
    $_getFilters = array(),
    /**
     * @var Array
     */
    $_setFilters = array(),
    /**
     * @var int counts how often getPath() is called
     * @see getPath()
     */
    $_getPathCallCounter = 0,
    /**
     * @var bool
     */
    $_isChanged = false;

    protected
    /**
     * @var Autoloader
     */
    $autoloader;

    /**
     * getRawPath() returns the unfiltered path to the class definition of $class.
     *
     * @param String $class The class name
     *
     * @throws AutoloaderException_Index
     * @throws AutoloaderException_Index_NotFound the class is not in the index
     * @return String The absolute path of the found class $class
     * @see getPath()
     */
    abstract protected function getRawPath($class);

    /**
     * hasPath() returns true if the class $class is already stored in the index.
     *
     * @param String $class The class name
     *
     * @throws AutoloaderException_Index
     * @return bool
     */
    abstract public function hasPath($class);

    /**
     * getPaths() returns all paths of the index.
     *
     * The returned array has the class name as keys and the paths as values.
     *
     * @throws AutoloaderException_Index
     * @return Array() All paths in the index
     */
    abstract public function getPaths();

    /**
     * delete() deletes the index.
     *
     * @throws AutoloaderException_Index
     * @return void
     */
    abstract public function delete();

    /**
     * Set the path for the class $class to $path
     *
     * This must not yet be persistent to the index. The Destructor
     * will call save() to make it persistent.
     *
     * @param String $class The class name
     * @param String $path  The path
     *
     * @throws AutoloaderException_Index
     * @see save()
     * @see unsetRawPath()
     * @return void
     */
    abstract protected function setRawPath($class, $path);

    /**
     * Unset the path for the class $class.
     *
     * This must not yet be persistent to the index. The Destructor
     * will call save() to make it persistent.
     *
     * @param String $class The class name
     *
     * @throws AutoloaderException_Index
     * @see setRawPath()
     * @see save()
     * @return void
     */
    abstract protected function unsetRawPath($class);

    /**
     * Makes the changes to the index persistent.
     *
     * The destructor is calling this method.
     *
     * @throws AutoloaderException_Index
     * @see save()
     * @return void
     */
    abstract protected function saveRaw();

    /**
     * You can add a filter which modifies the path which is read
     * from the index. This could for example produce absolute paths from
     * relative paths.
     *
     * @param AutoloaderIndexGetFilter $getFilter An AutoloaderIndexGetFilter object
     *
     * @see addSetFilter()
     * @return void
     */
    public function addGetFilter(AutoloaderIndexGetFilter $getFilter)
    {
        $this->_getFilters[] = $getFilter;
    }

    /**
     * You can add a filter which modifies the path which is stored
     * into the index. This could for example store relative paths instead
     * of absolute paths.
     *
     * @param AutoloaderIndexSetFilter $setFilter An AutoloaderIndexSetFilter object
     *
     * @see addGetFilter()
     * @return void
     */
    public function addSetFilter(AutoloaderIndexSetFilter $setFilter)
    {
        $this->_setFilters[] = $setFilter;
    }

    /**
     * addFilter() adds an AutoloaderIndexFilter instance.
     * 
     * These filters are used to modify the stored and read paths.
     *
     * @param AutoloaderIndexFilter $filter An AutoloaderIndexFilter filter
     *
     * @see addGetFilter()
     * @see addSetFilter()
     * @return void
     */
    public function addFilter(AutoloaderIndexFilter $filter)
    {
        $this->addSetFilter($filter);
        $this->addGetFilter($filter);
    }

    /**
     * getPath() returns the path of a class definition.
     *
     * All AutoloaderIndexGetFilter instances are applied on the returned path.
     *
     * If no path is stored in der index, an AutoloaderException_Index_NotFound
     * is thrown.
     *
     * @param String $class The class name
     *
     * @throws AutoloaderException_Index
     * @throws AutoloaderException_Index_NotFound the class is not in the index
     * @return String The absolute path of the found class $class
     * @see getRawPath()
     * @see addGetFilter()
     */
    final public function getPath($class)
    {
        $this->_getPathCallCounter++;
        $path = $this->getRawPath($class);
        foreach ($this->_getFilters as $filter) {
            $path = $filter->filterGetPath($path);

        }
        return $path;
    }

    /**
     * getGetPathCallCounter() returns how often class definitions were read
     * from the index.
     *
     * @return int A counter how often getPath() has been called
     * @see getPath()
     */
    public function getGetPathCallCounter()
    {
        return $this->_getPathCallCounter;
    }

    /**
     * save() makes the changes to the index persistent.
     *
     * The destructor is calling this method.
     *
     * @throws AutoloaderException_Index
     * @see setRawPath()
     * @see unsetRawPath()
     * @see __destruct()
     * @see saveRaw()
     * @return void
     */
    public function save()
    {
        if (! $this->_isChanged) {
            return;

        }
        $this->saveRaw();
        $this->_isChanged = false;
    }

    /**
     * The Autoloader calls this to set itself to this index.
     *
     * @param Autoloader $autoloader an Autoloader instance
     *
     * @see Autoloader::setIndex()
     * @see $autoloader
     * @return void
     */
    public function setAutoloader(Autoloader $autoloader)
    {
        $this->autoloader = $autoloader;
    }

    /**
     * Destruction of this index will make changes persistent.
     *
     * @throws AutoloaderException_Index
     * @see save()
     */
    public function __destruct()
    {
        $this->save();
    }

    /**
     * setPath() sets the path for the class $class to $path.
     *
     * This must not yet be persistent to the index. The Destructor
     * will call save() to make it persistent.
     *
     * All AutoloaderIndexSetFilter are applied before saving.
     *
     * @param String $class The class name
     * @param String $path  The path to the class definition
     *
     * @throws AutoloaderException_Index
     * @see save()
     * @see __destruct()
     * @see setRawPath()
     * @see unsetPath()
     * @see addSetFilter()
     * @return void
     */
    final public function setPath($class, $path)
    {
        foreach ($this->_setFilters as $filter) {
            $path = $filter->filterSetPath($path);

        }
        $this->setRawPath($class, $path);
        $this->_isChanged = true;
    }

    /**
     * Unset the path for the class
     *
     * This must not yet be persistent to the index. The Destructor
     * will call save() to make it persistent.
     *
     * @param String $class The class name
     *
     * @throws AutoloaderException_Index
     * @see unsetRawPath()
     * @see __destruct()
     * @see setPath()
     * @see save()
     * @return void
     */
    public function unsetPath($class)
    {
        $this->unsetRawPath($class);
        $this->_isChanged = true;
    }

    /**
     * The Autoloader class path context
     *
     * Only Autoloaders with an equal class path work in the same context.
     *
     * @return String A context to distinguish different autoloaders
     */
    protected function getContext()
    {
        return md5($this->autoloader->getPath());
    }

}