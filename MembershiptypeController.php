<?php

class MembershiptypeController extends Controller
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
				'actions'=>array('index','view','ListAllmembershiptypes'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('DeleteThisMembershiptype','addNewMembershipType','updateMembershipType','ListAllmembershiptypes'),
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
         * This is the function that adds new membership type
         */
        public function actionaddNewMembershipType(){
            
            $model=new Membershiptype;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

                $model->name = $_POST['name'];
                $model->code = $_POST['code'];
                if(isset($_POST['description'])){
                    $model->description = $_POST['description'];
                }
		$model->create_time = new CDbExpression('NOW()');
                $model->create_user_id = Yii::app()->user->id;
                                              
                $icon_error_counter = 0;
                 if($_FILES['icon']['name'] != ""){
                    if($this->isIconTypeAndSizeLegal()){
                        
                       $icon_filename = $_FILES['icon']['name'];
                      $icon_size = $_FILES['icon']['size'];
                        
                    }else{
                       
                        $icon_error_counter = $icon_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    $icon_filename = $this->provideMembershipTypeIconWhenUnavailable($model); 
                   $icon_size = 0;
             
                }//end of the if icon is empty statement
                if($icon_error_counter ==0){
                   if($model->validate()){
                           $model->icon = $this->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                           $model->icon_size = $icon_size;
                           
                       if($model->save()) {
                        
                                $msg = "'$model->name' service was successful created";
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
                            $msg = "Validation Error: '$model->name' Service  was not created successful";
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
                            $msg = "Please check your icon file type or size as icon must be of width '$platform_width'px and height '$platform_height'px. Icon type is of types '$icon_types'";
                            header('Content-Type: application/json');
                                    echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                    "msg" => $msg)
                            );
                         
                        }
            
            
            
        }
        
        
        /**
         * This is the function that updates membership type
         */
        public function actionupdateMembershipType(){
            
                $_id = $_POST['id'];
                $model=  Membershiptype::model()->findByPk($_id);
		$model->name = $_POST['name'];
                $model->code = $_POST['code'];
                if(isset($_POST['description'])){
                    $model->description = $_POST['description'];
                }
                $model->update_time = new CDbExpression('NOW()');
                $model->update_user_id = Yii::app()->user->id;
               	
		 //get the domain name
                $servicename = $this->getThisMembershipTypeName($_id);
                
                $icon_error_counter  = 0;
                
                if($_FILES['icon']['name'] != ""){
                    if($this->isIconTypeAndSizeLegal()){
                        
                       $icon_filename = $_FILES['icon']['name'];
                       $icon_size = $_FILES['icon']['size'];
                        
                    }else{
                       
                        $icon_error_counter = $icon_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    //$model->icon = $this->retrieveThePreviousIconName($_id);
                    $icon_filename = $this->retrieveThePreviousIconName($_id);
                    $icon_size = $this->retrieveThePreviousIconSize($_id);
             
                }//end of the if icon is empty statement
                if($icon_error_counter ==0){
                   if($model->validate()){
                           $model->icon = $this->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                           $model->icon_size = $icon_size;
                           
                       if($model->save()) {
                        
                                $msg = "'$servicename' service Information was successfully updated";
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
                            $msg = "Validation Error: '$servicename' service information update was not successful";
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
                            $msg = "Please check your icon file type or size as icon must be of width '$platform_width'px and height '$platform_height'px. Icon type is of types '$icon_types'";
                            header('Content-Type: application/json');
                                    echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                    "msg" => $msg)
                            );
                         
                        }
            
            
        }
        
        
        
        /**
         * This is the function that retrieves the name of the membershiptype
         */
        public function getThisMembershipTypeName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';  
            $criteria->params = array(':id'=>$id);
            $name = Membershiptype::model()->find($criteria);
            
            return $name['name'];
            
            
        }
        
        
        /**
         * This is the function that deletes a membership type
         */
        public function actionDeleteThisMembershiptype(){
            
            $_id = $_POST['id'];
            $model=  Membershiptype::model()->findByPk($_id);
            
            //get the currency name
            $type_name = $this->getThisMembershipTypeName($_id);
            if($model === null){
                 $msg = 'No Such Record Exist'; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                          
                           
                           
                          
                       ));
                                      
            }else if($model->delete()){
                    $msg = "'$type_name' Membership type had been deleted successfully"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                           
                           
                           
                          
                       ));
                       
            } else {
                    $msg = "Validation Error: '$type_name' Membership type was not deleted"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            //"selected" => $selected,
                            "msg" => $msg
                           
                           
                           
                          
                       ));
                            
                }
            
            
        }
        
        
        /**
         * This is the function that list all membership types
         */
        public function actionListAllmembershiptypes(){
            
            $userid = Yii::app()->user->id;
          
            $type = Membershiptype::model()->findAll();
                if($type===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "membershiptype" => $type
                                        
                                
                            ));
                       
                }
            
            
            
            
        }
        
        
        
          /**
	 * Provided icon when unavailable
	 */
	public function provideMembershipTypeIconWhenUnavailable($model)
	{
		return 'membershiptype_unavailable.png';
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
         * This is the function that retrieves the previous icon of the task in question
         */
        public function retrieveThePreviousIconName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = Membershiptype::model()->find($criteria);
            
            
            return $icon['icon'];
            
            
        }
        
        /**
         * This is the function that retrieves the previous icon size
         */
        public function retrieveThePreviousIconSize($id){
           
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = Membershiptype::model()->find($criteria);
            
            
            return $icon['icon_size'];
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
         * This is the function that moves icons to its directory
         */
        public function moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename){
            
            if(isset($_FILES['icon']['name'])){
                        $tmpName = $_FILES['icon']['tmp_name'];
                        $iconName = $_FILES['icon']['name'];    
                        $iconType = $_FILES['icon']['type'];
                        $iconSize = $_FILES['icon']['size'];
                  
                   }
                    
                    if($iconName !== null) {
                        if($model->id === null){
                          //$iconFileName = $icon_filename;  
                          if($icon_filename != 'service_unavailable.png'){
                                $iconFileName = time().'_'.$icon_filename;  
                            }else{
                                $iconFileName = $icon_filename;  
                            }  
                          
                           // upload the icon file
                        if($iconName !== null){
                            	$iconPath = Yii::app()->params['icons'].$iconFileName;
				move_uploaded_file($tmpName,  $iconPath);
                                        
                        
                                return $iconFileName;
                        }else{
                            $iconFileName = $icon_filename;
                            return $iconFileName;
                        } // validate to save file
                        }else{
                            if($this->noNewIconFileProvided($model->id,$icon_filename)){
                                $iconFileName = $icon_filename; 
                                return $iconFileName;
                            }else{
                            if($icon_filename != 'service_unavailable.png'){
                                if($this->removeTheExistingIconFile($model->id)){
                                 $iconFileName = time().'_'.$icon_filename; 
                                 //$iconFileName = time().$icon_filename;  
                                   $iconPath = Yii::app()->params['icons'].$iconFileName;
                                   move_uploaded_file($tmpName,$iconPath);
                                   return $iconFileName;
                                    
                                   // $iconFileName = time().'_'.$icon_filename;  
                                    
                             }
                                
                            }
                                
                                
                            }
                            
                            //$iconFileName = $icon_filename; 
                                              
                            
                        }
                      
                     }else{
                         $iconFileName = $icon_filename;
                         return $iconFileName;
                     }
					
                       
                               
        }
        
		
		
		 /**
         * This is the function that removes an existing video file
         */
        public function removeTheExistingIconFile($id){
            
            //retreve the existing zip file from the database
            
            if($this->isTheIconNotTheDefault($id)){
                
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= Membershiptype::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "c:\\xampp\htdocs\cobuy_images\\icons\\";
               // $iconpath = '..\appspace_assets\icons'.$icon['icon'];
                $filepath =$directoryPath.$icon['icon'];
                //$filepath = $directoryPath.$iconpath;
                
                if(unlink($filepath)){
                    return true;
                }else{
                    return false;
                }
                
            }else{
                return true;
            }
                
            
            
        }
        
	
        /**
         * This is the function that determines if  a membership type icon is the default
         */
        public function isTheIconNotTheDefault($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= Membershiptype::model()->find($criteria);
                
                if($icon['icon'] == 'service_unavailable.png' || $icon['icon'] ===NULL){
                    return false;
                }else{
                    return true;
                }
        }
		
		
		
		/**
         * This is the function to ascertain if a new icon was provided or not
         */
        public function noNewIconFileProvided($id,$icon_filename){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= Membershiptype::model()->find($criteria);
                
                if($icon['icon']==$icon_filename){
                    return true;
                }else{
                    return false;
                }
            
        }
	
}
