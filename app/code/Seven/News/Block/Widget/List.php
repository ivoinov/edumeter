<?php 

	class News_Block_Widget_List extends Core_Block_Widget_Grid {
		
		public function __construct($data = array()) {
			if(!isset($data['template']))
				$data['template'] = "Seven/News/list.phtml";
			if(!isset($data['pager']))
				$data['pager'] = false;
			if(!isset($data['filterable']))
				$data['filterable'] = false;
			if(!isset($data['add_default_actions']))
				$data['add_default_actions'] = false;
			parent::__construct($data);
		}
		
		public function getCollection() {
			$collection = Seven::getCollection('news/entity')->order('date DESC');
			
			if($this->getLimit() || $this->getStartAt())
				$collection->limit($this->getLimit(), $this->getStartAt());
				
			return $collection;
		}
		
	}