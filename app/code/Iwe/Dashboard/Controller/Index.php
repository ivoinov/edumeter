<?php 

	class Iwe_Dashboard_Controller_Index extends Core_Controller_Index {
		
		public function indexAction() {
            $this->redirect('school',301);
            $this->getLayout()->addTag("dashboard");
			$this->render();			
		}
		
	}