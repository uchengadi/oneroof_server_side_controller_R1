<?php

class CountryController extends Controller
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
				'actions'=>array('index','view','ListAllCountries'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','getCountryName','DeleteThisCountry','UpdateCountry','createnewcountry',
                                    'listallcountries'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('listallcountries','deleteonecountry'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	
	/**
	 * Creates a new country
	 */
	public function actioncreatenewcountry()
	{
		$model=new Country;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

                $model->name = $_POST['name'];
                $model->continent = $_POST['continent'];
                if(isset($_POST['description'])){
                   $model->description = $_POST['description']; 
                }
                if(isset($_POST['enable_vat_collection'])){
                     $model->enable_vat_collection = $_POST['enable_vat_collection'];
                }
              if(isset($_POST['vat_rate'])){
                   $model->vat_rate = $_POST['vat_rate'];
               }
               if(isset($_POST['country_default_vat_rate'])){
                   $model->country_default_vat_rate = $_POST['country_default_vat_rate'];
               }
                if(isset($_POST['prevailing_vat_policy'])){
                   $model->prevailing_vat_policy = strtolower($_POST['prevailing_vat_policy']);
                }
                $model->country_code = $_POST['country_code'];
                $model->create_time = new CDbExpression('NOW()');
                $model->create_user_id = Yii::app()->user->id;
                
                $icon_error_counter = 0;
                
                 if($_FILES['flag']['name'] != ""){
                    if($this->isIconTypeAndSizeLegal()){
                        
                       $icon_filename = $_FILES['flag']['name'];
                      $icon_size = $_FILES['flag']['size'];
                        
                    }else{
                       
                        $icon_error_counter = $icon_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    $icon_filename = $this->provideCountryIconWhenUnavailable($model);   
                   $icon_size = 0;
             
                }//end of the if icon is empty statement
                if($icon_error_counter ==0){
                   if($model->validate()){
                           $model->flag = $this->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                           $model->flag_size = $icon_size;
                           
                       if($model->save()) {
                        
                                $msg = "'$model->name' was created successful";
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
                            $msg = "Validation Error: '$model->name'  was not created successful";
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
                            $msg = "Please check your flag file type or size as flag must be of width '$platform_width'px and height '$platform_height'px. Flag is of types '$icon_types'";
                            header('Content-Type: application/json');
                                    echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                    "msg" => $msg)
                            );
                         
                        } 
	}

	/**
	 * Updates a particular country model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdateCountry()
	{
		
                // Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
            
             $_id = $_POST['id'];
             $model=Country::model()->findByPk($_id);
             $model->name = $_POST['name'];
             $model->continent = $_POST['continent'];
              if(isset($_POST['description'])){
                   $model->description = $_POST['description']; 
                }
                if(isset($_POST['enable_vat_collection'])){
                     $model->enable_vat_collection = $_POST['enable_vat_collection'];
                }
               if(isset($_POST['vat_rate'])){
                   $model->vat_rate = $_POST['vat_rate'];
               }
                if(isset($_POST['prevailing_vat_policy'])){
                   $model->prevailing_vat_policy = strtolower($_POST['prevailing_vat_policy']);
                }
                if(isset($_POST['country_default_vat_rate'])){
                   $model->country_default_vat_rate = $_POST['country_default_vat_rate'];
               }
             $model->country_code = $_POST['country_code'];
             $model->update_time = new CDbExpression('NOW()');
             $model->update_user_id = Yii::app()->user->id;
                
             //get the domain name
                $country_name = $this->getThisCountryName($_id);
                
                $icon_error_counter  = 0;
                
                if($_FILES['flag']['name'] != ""){
                    if($this->isIconTypeAndSizeLegal()){
                        
                       $icon_filename = $_FILES['flag']['name'];
                       $icon_size = $_FILES['flag']['size'];
                        
                    }else{
                       
                        $icon_error_counter = $icon_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    //$model->icon = $this->retrieveThePreviousIconName($_id);
                    $icon_filename = $this->retrieveThePreviousIconName($_id);
                    $icon_size = $this->retrieveThePrreviousIconSize($_id);
             
                }//end of the if icon is empty statement
                if($icon_error_counter ==0){
                   if($model->validate()){
                           $model->flag = $this->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                           $model->flag_size = $icon_size;
                           
                       if($model->save()) {
                        
                                $msg = "'$country_name' Information was successfully updated";
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
                            $msg = "Validation Error: '$country_name' information update was not successful";
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
                            $msg = "Please check your flag file type or size as flag must be of width '$platform_width'px and height '$platform_height'px. Flag is of types '$icon_types'";
                            header('Content-Type: application/json');
                                    echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                    "msg" => $msg)
                            );
                         
                        }  
	}

	/**
	 * Deletes a particular country model.
	*/
	public function actionDeleteThisCountry()
	{
            $_id = $_POST['id'];
            $model=Country::model()->findByPk($_id);
            
            //get the country name
            $country_name = $this->getThisCountryName($_id);
            if($model === null){
                $data['success'] = 'undefined';
                $data['msg'] = 'No such record exist';
                header('Content-Type: application/json');
                echo CJSON::encode($data);
                                      
            }else if($model->delete()){
                    $data['success'] = 'true';
                    $data['msg'] = "'$country_name' was successfully deleted";
                     header('Content-Type: application/json');
                    echo CJSON::encode($data);
            } else {
                    $data['success'] = 'false';
                    $data['msg'] = 'deletion was unsuccessful';
                     header('Content-Type: application/json');
                    echo CJSON::encode($data);
                            
                }
	}

	
	/**
	 * Manages all models.
	 */
	public function actionListAllCountries()
	{
		$country = Country::model()->findAll();
                if($country===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode($country);
                       
                }
	}
        
        /**
         * This is the function that gets the country name
         */
        public function actiongetCountryName(){
            
            //get the country id
           $id = $_REQUEST['id'];
            
            //retrieve the name of the country
            $country_name = $this->getThisCountryName($id);
            
            header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "country_name"=>$country_name
                       ));
            
        }
        
        
        /**
         * This is the function that gets the name of a country
         */
        public function getThisCountryName($country_id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$country_id);
            $country = Country::model()->find($criteria); 
            
            return $country['name'];
        }
        
          /**
        * Provide icon when unavailable
	 */
	public function provideCountryIconWhenUnavailable($model)
	{
		return 'country_unavailable.png';
	}
        
        
        
             /**
         * This is the function that determines the type and size of icon file
         */
        public function isIconTypeAndSizeLegal(){
            
           $size = []; 
            if(isset($_FILES['flag']['name'])){
                $tmpName = $_FILES['flag']['tmp_name'];
                $iconFileName = $_FILES['flag']['name'];    
                $iconFileType = $_FILES['flag']['type'];
                $iconFileSize = $_FILES['flag']['size'];
            } 
           if (isset($_FILES['flag'])) {
             $filename = $_FILES['flag']['tmp_name'];
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
            $icon = Country::model()->find($criteria);
            
            
            return $icon['flag'];
            
            
        }
        
        /**
         * This is the function that retrieves the previous icon size
         */
        public function retrieveThePrreviousIconSize($id){
           
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = Country::model()->find($criteria);
            
            
            return $icon['flag_size'];
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
            
            if(isset($_FILES['flag']['name'])){
                        $tmpName = $_FILES['flag']['tmp_name'];
                        $iconName = $_FILES['flag']['name'];    
                        $iconType = $_FILES['flag']['type'];
                        $iconSize = $_FILES['flag']['size'];
                  
                   }
                    
                    if($iconName !== null) {
                        if($model->id === null){
                          //$iconFileName = $icon_filename;  
                          if($icon_filename != 'country_unavailable.png'){
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
                            if($icon_filename != 'country_unavailable.png'){
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
                $icon= Country::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "c:\\xampp\htdocs\appspace_assets\\icons\\";
               // $iconpath = '..\appspace_assets\icons'.$icon['icon'];
                $filepath =$directoryPath.$icon['flag'];
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
                $icon= Country::model()->find($criteria);
                
                if($icon['flag'] == 'country_unavailable.png' || $icon['flag'] ===NULL){
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
                $icon= Country::model()->find($criteria);
                
                if($icon['flag']==$icon_filename){
                    return true;
                }else{
                    return false;
                }
            
        }

	
}
