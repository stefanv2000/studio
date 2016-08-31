<?php

namespace Entities\Iterator;
use Doctrine\Common\Collections\Collection;

class RecursiveSectionIterator implements \RecursiveIterator{
	
	private $_data;
	
	public function __construct(Collection $data)
	{
		$this->_data = $data;
	}
	
	public function hasChildren()
	{
		return ( ! $this->_data->current()->getSubsections()->isEmpty());
	}
	
	public function getChildren()
	{
		return new RecursiveSectionIterator($this->_data->current()->getSubsections());
	}
	
	public function current()
	{
		return $this->_data->current();
	}
	
	public function next()
	{
		$this->_data->next();
	}
	
	public function key()
	{
		return $this->_data->key();
	}
	
	public function valid()
	{
		return $this->_data->current() instanceof \Entities\Entity\Section;
	}
	
	public function rewind()
	{
		$this->_data->first();
	}	
}

?>