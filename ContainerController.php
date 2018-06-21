<?php

class ContainerController extends Controller
{
	 private $_id;
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
				'actions'=>array('index','view','listallcontainers'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','createnewcontainer','updatecontainer','DeleteThisContainer'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('listallcontainers','deleteonecontainer'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actioncreatenewcontainer()
	{
		$model=new Container;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		$model->name = $_POST['name'];
                $model->description = $_POST['description'];
                //$model->create_time = new CDbExpression('NOW()');
                //$model->icon = $_FILES['icon']['name'][0];
                
                //declare a universal error message variable
               $icon_error_counter = 0;
               $poster_error_counter =0;
               
                //working with the icon file
                if($_FILES['icon']['name'] != ""){
                    if($this->isIconTypeAndSizeLegal()){
                        
                       $icon_filename = $_FILES['icon']['name'];
                       $icon_size = $_FILES['icon']['size'];
                        
                    }else{
                       
                        $icon_error_counter = $icon_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    $icon_filename = $this->provideContainerIconWhenUnavailable($model);
                   $icon_size = 0;
             
                }//end of the if icon is empty statement
                
                //Working with the poster file
                 if($_FILES['image']['name'] != ""){
                    if($this->isPosterTypeAndSizeLegal()){
                        
                       $poster_filename = $_FILES['image']['name'];
                       $poster_size = $_FILES['image']['size'];
                        
                    }else{
                        $poster_error_counter = $poster_error_counter + 1;
                        
                    }//end of the determine size and type statement
                }else{
                     $poster_filename = $this->provideContainerIconWhenUnavailable($model);
                    $poster_size = 0;
                }//end of the if icon is empty statement
                //Ensure that the files variables all validates
                if(($icon_error_counter ==0 && $poster_error_counter == 0)){
                    if($model->validate()){
                        $model->icon = $this->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                        $model->image = $this->moveThePosterToItsPathAndReturnThePosterName($model,$poster_filename);
                        $model->icon_size = $icon_size;
                        $model->image_size = $poster_size;  
                        if($model->save()) {
                        
                                $msg = "'$model->name' container was created successful";
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
                            $msg = "Validation Error: '$model->name' container  was not created successful";
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
                }else if($poster_error_counter >0){
                    //get the platform settings for this property
                    $platform_width = $this->getThePlatformSetPosterWidth();
                    $platform_height =$this->getThePlatformSetPosterHeight();
                    $poster_types = $this->retrieveAllThePosterMimeTypes();
                    $poster_types =  json_encode($poster_types);
                   $msg = "Please check your image file type or size as image must be of width '$platform_width'px and height '$platform_height'px.Image  of types '$poster_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );
                }       
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdatecontainer()
	{
            // Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
                      
                
                
                            
            $_id = $_POST['id'];
            $model=Container::model()->findByPk($_id);
            $model->name = $_POST['name'];
            $model->description = $_POST['description'];
                
            //get the container's name
            $container_name = $this->getTheNameOfThisContainer($_id);
                 //declare a universal error message variable
               $icon_error_counter = 0;
               $poster_error_counter =0;
               
                //working with the icon file
                if($_FILES['icon']['name'] != ""){
                    if($this->isIconTypeAndSizeLegal()){
                        
                       $icon_filename = $_FILES['icon']['name'];
                       $icon_size = $_FILES['icon']['size'];
                        
                    }else{
                       
                        $icon_error_counter = $icon_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    $icon_filename = $this->retrieveThePreviousIconName($_id);
                    $icon_size = $this->retrieveThePrreviousIconSize($_id);
             
                }//end of the if icon is empty statement
                
                //Working with the poster file
                 if($_FILES['image']['name'] != ""){
                    if($this->isPosterTypeAndSizeLegal()){
                        
                       $image_filename = $_FILES['image']['name'];
                       $image_size = $_FILES['image']['size'];
                        
                    }else{
                        $poster_error_counter = $poster_error_counter + 1;
                        
                    }//end of the determine size and type statement
                }else{
                    $image_filename = $this->retrieveThePreviousPosterName($_id);
                    $image_size = $this->retrieveThePreviousPosterSize($_id);
                }//end of the if icon is empty statement
                //Ensure that the files variables all validates
                if(($icon_error_counter ==0 && $poster_error_counter == 0)){
                    if($model->validate()){
                        $model->icon = $this->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                        $model->image = $this->moveThePosterToItsPathAndReturnThePosterName($model,$image_filename);
                        $model->icon_size = $icon_size;
                        $model->image_size = $image_size;  
                        if($model->save()) {
                        
                                $msg = "'$container_name' container was updated successful";
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
                            $msg = "Validation Error: '$container_name' container  was not updated successful";
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
                }else if($poster_error_counter >0){
                    //get the platform settings for this property
                    $platform_width = $this->getThePlatformSetPosterWidth();
                    $platform_height =$this->getThePlatformSetPosterHeight();
                    $poster_types = $this->retrieveAllThePosterMimeTypes();
                    $poster_types =  json_encode($poster_types);
                   $msg = "Please check your image file type or size as image must be of width '$platform_width'px and height '$platform_height'px.Image  of types '$poster_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );
                }  
	}

	/**
	 * Deletes a particular container model.
	  */
	public function actionDeleteThisContainer()
	{
            
            $_id = $_POST['id'];
            $model=Container::model()->findByPk($_id);
            //get the name of this container
            $container_name = $this->getTheNameOfThisContainer($_id);
            
            if($model === null){
                $data['success'] = 'undefined';
                $data['msg'] = 'No such record exist';
                header('Content-Type: application/json');
                echo CJSON::encode($data);
                                      
            }else if($model->delete()){
                    $data['success'] = 'true';
                    $data['msg'] = "'$container_name' was successfully deleted";
                     header('Content-Type: application/json');
                    echo CJSON::encode($data);
            } else {
                    $data['success'] = 'false';
                    $data['msg'] = 'deletion unsuccessful';
                     header('Content-Type: application/json');
                    echo CJSON::encode($data);
                            
                }
	}
        
        
        /**
         * This is the function that gets the name of a container
         */
        public function getTheNameOfThisContainer($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$id);
            $name= Container::model()->find($criteria);
            
            return $name['name'];
            
        }

	
	/**
	 * Manages all models.
	 */
	public function actionListAllContainers()
	{
	      $container = Container::model()->findAll();
                if($container===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode($container);
                       
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
         * This is the function that retrieves the previous icon of the task in question
         */
        public function retrieveThePreviousIconName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = Container::model()->find($criteria);
            
            
            return $icon['icon'];
            
            
        }
        
        /**
         * This is the function that retrieves the previous icon size
         */
        public function retrieveThePrreviousIconSize($id){
           
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = Container::model()->find($criteria);
            
            
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
                          if($icon_filename != 'container_unavailable.png'){
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
                             if($icon_filename != 'container_unavailable.png'){
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
                $icon= Container::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "c:\\xampp\htdocs\appspace_assets\\icons\\";
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
         * This is the function that determines if  a tooltype icon is the default
         */
        public function isTheIconNotTheDefault($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= Container::model()->find($criteria);
                
                if($icon['icon'] == 'container_unavailable.png' || $icon['icon'] ===NULL){
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
                $criteria->select = 'id, icon';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= Container::model()->find($criteria);
                
                if($icon['icon']==$icon_filename){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        
        /**
	 * Provide icon when unavailable
	 */
	public function provideContainerIconWhenUnavailable($model)
	{
		return 'container_unavailable.png';
	}

         /**
         * This is the function that determines the type and size of poster file
         */
        public function isPosterTypeAndSizeLegal(){
          
            if(isset($_FILES['image']['name'])){
                $tmpName = $_FILES['image']['tmp_name'];
                $posterFileName = $_FILES['image']['name'];    
                $posterFileType = $_FILES['image']['type'];
                $posterFileSize = $_FILES['image']['size'];
            } 
            //obtain the poster sizes for this domain
           if (isset($_FILES['image'])) {
             $filename = $_FILES['image']['tmp_name'];
             list($width, $height) = getimagesize($filename);
           }
            
            $platform_width = $this->getThePlatformSetPosterWidth();
            $platform_height = $this->getThePlatformSetPosterHeight();
            
            $width = $width;
            $height =  $height;
            
           // $max_poster_size = $this->retrieveTheMaxPosterSizeForThisDomain($domainid);
            $postertypes = $this->retrieveAllThePosterMimeTypes();
            
                       
           // if(($posterFileType === 'image/png'|| $posterFileType === 'image/jpg' || $posterFileType === 'image/jpeg') && ($posterFileSize <= 256 * 256 * 2)){
           if(in_array($posterFileType,$postertypes) && ($platform_width<=$width && $platform_height<=$height)){
                return true;
            }else{
                return false;
            }
            
        }
        
        
         /**
         * This is the function that retrieves the previous poster of the task in question
         */
        public function retrieveThePreviousPosterName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $poster = Container::model()->find($criteria);
            
            
            return $poster['image'];
            
            
            
        }
        
        /**
         * This is the function that returns the previous poster size
         */
        public function retrieveThePreviousPosterSize($id){
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $poster = Container::model()->find($criteria);
            
            
            return $poster['image_size'];
            
        }
        
        
         /**
         * This is the function that gets the platform height setting
         */
        public function getThePlatformSetPosterHeight(){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
           // $criteria->condition='id=:id';
           // $criteria->params = array(':id'=>$id);
            $poster = PlatformSettings::model()->find($criteria); 
            
            return $poster['poster_height'];
        }
        
        
         /**
         * This is the function that gets the platform poster set width
         */
        public function getThePlatformSetPosterWidth(){
            
           $criteria = new CDbCriteria();
            $criteria->select = '*';
           // $criteria->condition='id=:id';
           // $criteria->params = array(':id'=>$id);
            $poster = PlatformSettings::model()->find($criteria); 
            
            return $poster['poster_width'];
        }
        
        
        /**
         * This is the function that retrieves all poster mime types in the platform
         */
        public function retrieveAllThePosterMimeTypes(){
           
             //retrieve the poster mime types
            $poster_mimetype = [];
            $poster_types = [];
            $criteria = new CDbCriteria();
            $criteria->select = '*';
           // $criteria->condition='id=:id';
           // $criteria->params = array(':id'=>$id);
            $poster_mime = PlatformSettings::model()->find($criteria); 
            
            $poster_mimetype = explode(',',$poster_mime['poster_mime_type']);
            foreach($poster_mimetype as $poster){
                $poster_types[] =$poster; 
                
            }
            
            return $poster_types;
            
        }
        
        
        
        /**
         * This is the function that moves poster files to its directory
         */
        public function moveThePosterToItsPathAndReturnThePosterName($model,$poster_filename){
            
            if(isset($_FILES['image']['name'])){
                        $tmpName = $_FILES['image']['tmp_name'];
                        $posterName = $_FILES['image']['name'];    
                        $posterType = $_FILES['image']['type'];
                        $posterSize = $_FILES['image']['size'];
                  
                    }
                    
                    if( $posterName !== null) {
                      if($model->id === null){
                          //$posterFileName = $poster_filename;
                          if($poster_filename != 'container_unavailable.png'){
                                $posterFileName = time().'_'.$poster_filename;  
                            }else{
                                $posterFileName= $poster_filename;  
                            }
                          
                           // upload the poster file
                        if( $posterName !== null){
                            	$posterPath = Yii::app()->params['posters'].$posterFileName;
				move_uploaded_file($tmpName,  $posterPath);
                                        
                        
                                return $posterFileName;
                        }else{
                            
                            return $poster_filename;
                            
                            
                        } // validate to save file
                      }else{
                          if($this->noNewPosterFileProvided($model->id,$poster_filename)){
                                $posterFileName = $poster_filename;
                                return $posterFileName;
                            }else{
                              if($poster_filename != 'container_unavailable.png'){
                                   if($this->removeTheExistingPosterFile($model->id)){
                                 
                                     $posterFileName = time().'_'.$poster_filename;
                                     $posterPath = Yii::app()->params['posters'].$posterFileName;
                                     move_uploaded_file($tmpName,  $posterPath);
                                     return $posterFileName;
                               }
                                
                              }
                               
                                
                               //return $poster_filename;
                            }
                       
                      }
                        
                     }else{
                         $posterFileName = $poster_filename;
                          return $posterFileName;
                     }
					
                       
                               
        }
        
        
        /**
         * This is the function to ascertain if a new poster was provided or not
         */
        public function noNewPosterFileProvided($id,$poster_filename){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $poster= Container::model()->find($criteria);
                
                if($poster['image']==$poster_filename){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
         /**
         * This is the function that removes an existing video file
         */
        public function removeTheExistingPosterFile($id){
            
            //retreve the existing zip file from the database
            if($this->isThePosterNotTheDefault($id)){
                
                 $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $poster= Container::model()->find($criteria);
                
               // $directoryPath =  dirname(Yii::app()->request->scriptFile);
                $directoryPath = "c:\\xampp\htdocs\appspace_assets\\posters\\";
                //$posterpath = '..\appspace_assets\posters'.$poster['poster'];
               $filepath =  $directoryPath.$poster['image'];
               // $filepath = $directoryPath.$posterpath;
                
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
         * This is the function that determines if  a tooltype icon is the default
         */
        public function isThePosterNotTheDefault($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= Container::model()->find($criteria);
                
                if($icon['image'] == 'container_unavailable.png' || $icon['image'] ===NULL){
                    return false;
                }else{
                    return true;
                }
        }
        
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Container the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Container::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Container $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='container-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
