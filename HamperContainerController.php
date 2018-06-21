<?php

class HamperContainerController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','retrieveAllHamperContainers','AddNewHamperContainer','UpdateNewHamperContainer','removeThisHamperContainer',
                                    'retrieveAHamperContainer'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

        /**
         * This is the function that retrieves all hamper containers
         */
        public function actionretrieveAllHamperContainers(){
            
            $hamper = HamperContainer::model()->findAll(array('order'=>'name'));
                if($hamper===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"assigned" => $assigned,
                            "hamper" => $hamper)
                       );
                       
                }
            
            
        }
        
        /**
         * This is the function that adds a new hamper container
         */
        public function actionAddNewHamperContainer(){
            $model=new HamperContainer;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

                $model->name = $_POST['name'];
                $model->code = $_POST['code'];
                if(isset($_POST['description'])){
                   $model->description = $_POST['description']; 
                }
               $model->amount = $_POST['amount'];
               $model->weight = $_POST['weight'];
               $model->service_charge_in_percentages = $_POST['service_charge_in_percentages'];
               $model->minimum_service_charge = $_POST['minimum_service_charge'];
                $model->create_time = new CDbExpression('NOW()');
                $model->create_user_id = Yii::app()->user->id;
                
                $icon_error_counter = 0;
                
                 if($_FILES['icon']['name'] != ""){
                    //if($this->isIconTypeAndSizeLegal()){
                        
                       $icon_filename = $_FILES['icon']['name'];
                      $icon_size = $_FILES['icon']['size'];
                        
                  /**  }else{
                       
                        $icon_error_counter = $icon_error_counter + 1;
                         
                    }//end of the determine size and type statement
                   * 
                   */
                }else{
                    $icon_filename = $model->provideHamperContainerIconWhenUnavailable();   
                   //$icon_size = 0;
             
                }//end of the if icon is empty statement
                if($icon_error_counter ==0){
                   if($model->validate()){
                           $model->icon = $model->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                         //  $model->flag_size = $icon_size;
                           
                       if($model->save()) {
                        
                                $msg = "'$model->name' hamper container was created successful";
                                 header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() == 0,
                                  "msg" => $msg)
                            );
                         
                        }else{
                            //delete all the moved files in the directory when validation error is encountered
                            $msg = 'Validaion Error: Check your file fields for correctness';
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                               );
                          }
                            }else{
                                
                                //delete all the moved files in the directory when validation error is encountered
                            $msg = "Validation Error: '$model->name'  hamper container was not created successful";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                            );
                          }
                        }elseif($icon_error_counter > 0){
                        //get the platform assigned icon width and height
                            $platform_width = $this->getThePlatformSetIconWidth();
                            $platform_height = $this->getThePlatformSeticonHeight();
                            $icon_types = $this->retrieveAllTheIconMimeTypes();
                            $icon_types = json_encode($icon_types);
                            $msg = "Please check your icon file type or size as icon must be of width '$platform_width'px and height '$platform_height'px. Icon is of types '$icon_types'";
                            header('Content-Type: application/json');
                                    echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                    "msg" => $msg)
                            );
                         
                        } 
            
            
        }
        
        
        /**
         * This is the function that updates a hamper container information
         */
        public function actionUpdateNewHamperContainer(){
            
            $_id = $_POST['id'];
             $model=  HamperContainer::model()->findByPk($_id);
             
             $model->name = $_POST['name'];
             $model->code = $_POST['code'];
              if(isset($_POST['description'])){
                   $model->description = $_POST['description']; 
                }
               $model->amount = $_POST['amount'];
               $model->weight = $_POST['weight'];
               $model->service_charge_in_percentages = $_POST['service_charge_in_percentages'];
               $model->minimum_service_charge = $_POST['minimum_service_charge'];
               $model->update_time = new CDbExpression('NOW()');
               $model->update_user_id = Yii::app()->user->id;
                
               
                //get the existing hamper container name
                $hamper_container_name = $model->getThisExistingHamperContainerName($_id);
               
               
                $icon_error_counter  = 0;
                
                if($_FILES['icon']['name'] != ""){
                   // if($this->isIconTypeAndSizeLegal()){
                        
                       $icon_filename = $_FILES['icon']['name'];
                       $icon_size = $_FILES['icon']['size'];
                        
                  /**  }else{
                       
                        $icon_error_counter = $icon_error_counter + 1;
                         
                    }//end of the determine size and type statement
                   * 
                   */
                }else{
                    $icon_filename = $model->retrieveThePreviousIconName($_id);
                   
             
                }//end of the if icon is empty statement
                if($icon_error_counter ==0){
                   if($model->validate()){
                           $model->icon = $model->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                          // $model->flag_size = $icon_size;
                           
                       if($model->save()) {
                        
                                $msg = "'$hamper_container_name' hamper container Information was successfully updated";
                                 header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() == 0,
                                  "msg" => $msg)
                            );
                         
                        }else{
                            //delete all the moved files in the directory when validation error is encountered
                            $msg = 'Validaion Error: Check your file fields for correctness';
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                               );
                          }
                            }else{
                                
                                //delete all the moved files in the directory when validation error is encountered
                            $msg = "Validation Error: '$hamper_container_name' hamper information information update was not successful";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                            );
                          }
                        }elseif($icon_error_counter > 0){
                        //get the platform assigned icon width and height
                            $platform_width = $this->getThePlatformSetIconWidth();
                            $platform_height = $this->getThePlatformSeticonHeight();
                            $icon_types = $this->retrieveAllTheIconMimeTypes();
                            $icon_types = json_encode($icon_types);
                            $msg = "Please check your icon file type or size as icon must be of width '$platform_width'px and height '$platform_height'px. Icon is of types '$icon_types'";
                            header('Content-Type: application/json');
                                    echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                    "msg" => $msg)
                            );
                         
                        } 
        }
        
        
        /**
         * This is the function that removes a hamper container
         */
        public function actionremoveThisHamperContainer(){
            
            $_id = $_POST['id'];
            $model=  HamperContainer::model()->findByPk($_id);
            
            //get the hamper container name
            $hamper_container_name = $model->getThisExistingHamperContainerName($_id);
            if($model === null){
                 $msg = 'No Such Record Exist'; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                           
                           
                          
                       ));
                                      
            }else if($model->delete()){
                    $msg = "'$hamper_container_name' hamper container had been removed successfully"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                           
                           
                           
                          
                       ));
                       
            } else {
                    $msg = "Validation Error: '$hamper_container_name' hamper container could not be removed"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            //"selected" => $selected,
                            "msg" => $msg
                           
                           
                           
                          
                       ));
                            
                }
        }
        
        
            /**
         * This is the function that determines the type and size of icon file
         */
        public function isIconTypeAndSizeLegal(){
            
           $size = []; 
            if(isset($_FILES['icon']['name'])){
                $tmpName = $_FILES['icon']['tmp_name'];
                $iconFileName = $_FILES['icon']['name'];    
                $iconFileType = $_FILES['icon']['type'];
                $iconFileSize = $_FILES['icon']['size'];
            } 
           if (isset($_FILES['icon'])) {
             $filename = $_FILES['icon']['tmp_name'];
             list($width, $height) = getimagesize($filename);
           }
      

           
            $platform_width = $this->getThePlatformSetIconWidth();
            $platform_height = $this->getThePlatformSeticonHeight();
            
            $width = $width;
            $height = $height;
           
            //$size = $width * $height;
           
            $icontypes = $this->retrieveAllTheIconMimeTypes();
            
          
           
            //if(($iconFileType === 'image/png'|| $iconFileType === 'image/jpg' || $iconFileType === 'image/jpeg') && ($iconFileSize = 256 * 256)){
            if((in_array($iconFileType,$icontypes)) && ($platform_width <= $width && $platform_height <= $height)){
                return true;
               
            }else{
                return false;
            }
            
        }
        
        
        	 /**
         * This is the function that gets the platform height setting
         */
        public function getThePlatformSeticonHeight(){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
           // $criteria->condition='id=:id';
           // $criteria->params = array(':id'=>$id);
            $icon = PlatformSettings::model()->find($criteria); 
            
            return $icon['icon_height'];
        }
		
		
		
		 /**
         * This is the function that gets the platform icon set width
         */
        public function getThePlatformSetIconWidth(){
            
           $criteria = new CDbCriteria();
            $criteria->select = '*';
           // $criteria->condition='id=:id';
           // $criteria->params = array(':id'=>$id);
            $icon = PlatformSettings::model()->find($criteria); 
            
            return $icon['icon_width'];
        }
		
		
		
		/**
         * This is the function that retrieves all icon mime types in the platform
         */
        public function retrieveAllTheIconMimeTypes(){
            
            $icon_mimetype = [];
            $icon_types = [];
            $criteria = new CDbCriteria();
            $criteria->select = '*';
           // $criteria->condition='id=:id';
           // $criteria->params = array(':id'=>$id);
            $icon_mime = PlatformSettings::model()->find($criteria); 
            
            $icon_mimetype = explode(',',$icon_mime['icon_mime_type']);
            foreach($icon_mimetype as $icon){
                $icon_types[] =$icon; 
                
            }
            
            return $icon_types;
            
        }
        
        
         /**
         * This is the function that retrieves a single hamper container details
         */
        public function actionretrieveAHamperContainer(){
            
            $container_id = $_REQUEST['container_id'];
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$container_id);
            $hamper= HamperContainer::model()->find($criteria);
            
            if($hamper===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "hamper" => $hamper
                                        
                                
                            ));
                       
                }
            
        }
}
