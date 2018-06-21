<?php

class CategoryController extends Controller
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
				'actions'=>array('index','view','ListAllProductsCategory','createNewProductCategory','ListAllProductsCategoryForAService',
                                    'ListAllTheseCategory','ListAllProductsCategoryForThisService','ListAllBabyProductsCategory','ListAllGroceryProductsCategory',
                                    'ListAllFashionAndBeautyProductsCategory','ListAllOfficeProductsCategory','ListAllSmartphoneProductsCategory',
                                    'ListAllComputerProductsCategory','ListAllHomeservicesProductsCategory','ListAllStationaryAndLearningToolsCategory',
                                    'ListAllFaasservicesProductsCategory'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','obtainCategoryExtraInformation','ListAllProductsCategory','createNewProductCategory',
                                    'updateProductCategory', 'DeleteThisProductCategory','ListAllTheseCategory','ListAllTheseCategory'),
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
         * This is the function that retrieves additional information about a category
         */
        public function actionobtainCategoryExtraInformation(){
            
            $model = new Service;
            $id = $_REQUEST['id'];
            $service_id = $_REQUEST['service_id'];
            
            $service_name = $model->getServiceName($service_id);
            
                   
            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "id" => $id,
                                        "service"=>$service_name
                                        
                                
                            ));
            
        }
        
        
        /**
         * This is the function that retrieves all categories on the platform
         */
        public function actionListAllProductsCategory(){
            
            $model = new Category;
            $start = $_REQUEST['start'];
            $limit = $_REQUEST['limit'];
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->order = "name";
            $criteria->offset = $start;
            $criteria->limit = $limit;     
            $category = Category::model()->findAll($criteria);
            
            $count = $model->getTheTotalCategoryOnTheStore();
                if($category===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "results"=>$count,
                                        "category" => $category
                                        
                                
                            ));
                       
                }
            
            
        }
        
        
        
         /**
         * This is the function that retrieves all categories on the platform
         */
        public function actionListAllTheseCategory(){
            
            $category = Category::model()->findAll();
                if($category===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "category" => $category
                                        
                                
                            ));
                       
                }
            
            
        }
        
        
        /**
         * This is the function that lists all product category for a service
         */
        public function actionListAllProductsCategoryForAService(){
            
           
            
            //$service_id = 2;
            
          //$service_id = $_REQUEST['service_id'];
          
          if(is_numeric($_REQUEST['service_id'])){
                $service_id = $_REQUEST['service_id'];
            }else{
                $service_id = 0;
            }
        // if(isset($_REQUEST['service_id'])){
           if($service_id == 0){
                $category = Category::model()->findAll(array('order'=>'name'));
                if($category===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "category" => $category
                                        
                                
                            ));
                       
                }
                       
                
            }else{
                
               $criteria1 = new CDbCriteria();
                $criteria1->select = '*';
                $criteria1->condition='service_id=:id';
                $criteria1->params = array(':id'=>$service_id);
                $criteria1->order = "name";
                $category = Category::model()->findAll($criteria1); 
            
                if($category===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"assigned" => $assigned,
                            "category" => $category)
                       );
					   }
            }
              
          //}
        
            
        }
        
        
        /**
         * This is the function that creates new categories
         */
        public function actioncreateNewProductCategory(){
            
            
            $model=new Category;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

                $model->name = $_POST['name'];
                if(isset($_POST['description'])){
                   $model->description = $_POST['description']; 
                }
                if(isset($_POST['code'])){
                   $model->code = $_POST['code']; 
                }
               if(is_numeric($_POST['service'])){
                    $model->service_id = $_POST['service']; 
                }else{
                     $model->service_id = $_POST['service_id']; 
                }
                 if(isset($_POST['is_available'])){
                   $model->is_available = $_POST['is_available']; 
                }else{
                    $model->is_available = 0;
                }
                  if(isset($_POST['display_types_on_store'])){
                   $model->display_types_on_store = $_POST['display_types_on_store']; 
                }else{
                    $model->display_types_on_store=0;
                }
                  if(isset($_POST['is_faas'])){
                   $model->is_faas = $_POST['is_faas']; 
                }else{
                    $model->is_faas=0;
                }
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
                    $icon_filename = $this->provideCategoryIconWhenUnavailable($model);   
                   //$icon_size = 0;
             
                }//end of the if icon is empty statement
                if($icon_error_counter ==0){
                   if($model->validate()){
                           $model->icon = $this->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                         //  $model->flag_size = $icon_size;
                           
                       if($model->save()) {
                        
                                $msg = "'$model->name' category was created successful";
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
                            $msg = "Validation Error: '$model->name'  category was not created successful";
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
         * This is the function that updates new categories
         */
        public function actionupdateProductCategory(){
            
             // Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
            
             $_id = $_POST['id'];
             $model=Category::model()->findByPk($_id);
             $model->name = $_POST['name'];
              if(isset($_POST['description'])){
                   $model->description = $_POST['description']; 
                }
                if(isset($_POST['code'])){
                   $model->code = $_POST['code']; 
                }
                if(is_numeric($_POST['service'])){
                    $model->service_id = $_POST['service']; 
                }else{
                     $model->service_id = $_POST['service_id']; 
                }
                 if(isset($_POST['is_available'])){
                   $model->is_available = $_POST['is_available']; 
                }else{
                    $model->is_available = 0;
                }
                  if(isset($_POST['display_types_on_store'])){
                   $model->display_types_on_store = $_POST['display_types_on_store']; 
                }else{
                    $model->display_types_on_store=0;
                }
                  if(isset($_POST['is_faas'])){
                   $model->is_faas = $_POST['is_faas']; 
                }else{
                    $model->is_faas=0;
                }
            $model->update_time = new CDbExpression('NOW()');
             $model->update_user_id = Yii::app()->user->id;
                
             //get the domain name
                $category_name = $this->getThisCategoryName($_id);
                
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
                    //$model->icon = $this->retrieveThePreviousIconName($_id);
                    $icon_filename = $this->retrieveThePreviousIconName($_id);
                    //$icon_size = $this->retrieveThePreviousIconSize($_id);
             
                }//end of the if icon is empty statement
                if($icon_error_counter ==0){
                   if($model->validate()){
                           $model->icon = $this->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                          // $model->flag_size = $icon_size;
                           
                       if($model->save()) {
                        
                                $msg = "'$category_name' Information was successfully updated";
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
                            $msg = "Validation Error: '$category_name' information update was not successful";
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
         * This is the function that deletes a product category
         */
        public function actionDeleteThisProductCategory(){
            
            $_id = $_POST['id'];
            $model=Category::model()->findByPk($_id);
            
            //get the currency name
            $category_name = $this->getThisCategoryName($_id);
            if($model === null){
                 $msg = 'No Such Record Exist'; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                            //"category" =>$category,
                           
                           
                          
                       ));
                                      
            }else if($model->delete()){
                    $msg = "'$category_name' Categry had been deleted successfully"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                            //"category" =>$category,
                           
                           
                          
                       ));
                       
            } else {
                    $msg = "Validation Error: '$category_name' Category was not deleted"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            //"selected" => $selected,
                            "msg" => $msg
                            //"category" =>$category,
                           
                           
                          
                       ));
                            
                }
            
            
        }
        
        
        
        /**
         * This is the function that gets the name of a category
         */
        public function getThisCategoryName($country_id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$country_id);
            $category = Category::model()->find($criteria); 
            
            return $category['name'];
        }
        
          /**
        * Provide icon when unavailable
	 */
	public function provideCategoryIconWhenUnavailable($model)
	{
		return 'category_unavailable.png';
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
            $icon = Category::model()->find($criteria);
            
            
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
            $icon = Category::model()->find($criteria);
            
            
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
                          if($icon_filename != 'category_unavailable.png'){
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
                            if($icon_filename != 'category_unavailable.png'){
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
                $icon= Category::model()->find($criteria);
                
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
         * This is the function that determines if  a category icon is the default
         */
        public function isTheIconNotTheDefault($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= Category::model()->find($criteria);
                
                if($icon['icon'] == 'category_unavailable.png' || $icon['icon'] ===NULL){
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
                $icon= Category::model()->find($criteria);
                
                if($icon['icon']==$icon_filename){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        /**
         * This is the function that retrieves a service category using the service code
         */
        public function actionListAllProductsCategoryForThisService(){
            
            $model = new Category;
            
            $service_id = $_REQUEST['service_id'];
            $start = $_REQUEST['start'];
            $limit = $_REQUEST['limit']; 
            
                     
             $criteria = new CDbCriteria();
             $criteria->select = '*';
             $criteria->condition='service_id=:id and is_available=:is_available';
             $criteria->params = array(':id'=>$service_id,'is_available'=>1);
             $criteria->order = "name";
             $criteria->offset = $start;
             $criteria->limit = $limit;     
             $categories= Category::model()->findAll($criteria,array('order'=>'name'));
             
             //get all total number of categories for this service
             $count = $model->getTheTotalNumberOfCategoriesForAService($service_id);
             
             if($categories===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "results"=>$count,
                                        "category" => $categories
                                        
                                
                            ));
                       
                }
             
            
        }
        
        
        
        
        /**
         * This is the function that retrieves all baby products category on the store
         */
        public function actionListAllBabyProductsCategory(){
            
            $model = new Service;
            
            //get the service id of the baby products service
            $service_id = $model->getThisServiceIdGiveItsCode('BABYPRODUCT');
            
                     
             $criteria = new CDbCriteria();
             $criteria->select = '*';
             $criteria->condition='service_id=:id and is_available=:is_available';
             $criteria->params = array(':id'=>$service_id,'is_available'=>1);
             $categories= Category::model()->findAll($criteria,array('order'=>'name'));
             
             if($categories===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "category" => $categories
                                        
                                
                            ));
                       
                }
             
            
        }
        
        
        
        
         /**
         * This is the function that retrieves all grocery products category on the store
         */
        public function actionListAllGroceryProductsCategory(){
            
            $model = new Service;
            
            //get the service id of the baby products service
            $service_id = $model->getThisServiceIdGiveItsCode('GROCERY');
            
                     
             $criteria = new CDbCriteria();
             $criteria->select = '*';
             $criteria->condition='service_id=:id and is_available=:is_available';
             $criteria->params = array(':id'=>$service_id,'is_available'=>1);
             $categories= Category::model()->findAll($criteria,array('order'=>'name'));
             
             if($categories===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "category" => $categories
                                        
                                
                            ));
                       
                }
             
            
        }
        
        
        
        
         /**
         * This is the function that retrieves all office products category on the store
         */
        public function actionListAllOfficeProductsCategory(){
            
            $model = new Service;
            
            //get the service id of the baby products service
            $service_id = $model->getThisServiceIdGiveItsCode('OFFICE');
            
                     
             $criteria = new CDbCriteria();
             $criteria->select = '*';
             $criteria->condition='service_id=:id and is_available=:is_available';
             $criteria->params = array(':id'=>$service_id,'is_available'=>1);
             $categories= Category::model()->findAll($criteria,array('order'=>'name'));
             
             if($categories===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "category" => $categories
                                        
                                
                            ));
                       
                }
             
            
        }
        
        
        
          /**
         * This is the function that retrieves all office products category on the store
         */
        public function actionListAllSmartphoneProductsCategory(){
            
            $model = new Service;
            
            //get the service id of the baby products service
            $service_id = $model->getThisServiceIdGiveItsCode('MOBILES');
            
                     
             $criteria = new CDbCriteria();
             $criteria->select = '*';
             $criteria->condition='service_id=:id and is_available=:is_available';
             $criteria->params = array(':id'=>$service_id,'is_available'=>1);
             $categories= Category::model()->findAll($criteria,array('order'=>'name'));
             
             if($categories===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "category" => $categories
                                        
                                
                            ));
                       
                }
             
            
        }
        
        
         /**
         * This is the function that retrieves all computer products category on the store
         */
        public function actionListAllComputerProductsCategory(){
            
            $model = new Service;
            
            //get the service id of the baby products service
            $service_id = $model->getThisServiceIdGiveItsCode('COMPUTERS');
            
                     
             $criteria = new CDbCriteria();
             $criteria->select = '*';
             $criteria->condition='service_id=:id and is_available=:is_available';
             $criteria->params = array(':id'=>$service_id,'is_available'=>1);
             $categories= Category::model()->findAll($criteria,array('order'=>'name'));
             
             if($categories===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "category" => $categories
                                        
                                
                            ));
                       
                }
             
            
        }
        
        
        
         
          /**
         * This is the function that retrieves all home services products category on the store
         */
        public function actionListAllHomeservicesProductsCategory(){
            
            $model = new Service;
            
            //get the service id of the baby products service
            $service_id = $model->getThisServiceIdGiveItsCode('HOMESERVICES');
            
                     
             $criteria = new CDbCriteria();
             $criteria->select = '*';
             $criteria->condition='service_id=:id and is_available=:is_available';
             $criteria->params = array(':id'=>$service_id,'is_available'=>1);
             $categories= Category::model()->findAll($criteria,array('order'=>'name'));
             
             if($categories===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "category" => $categories
                                        
                                
                            ));
                       
                }
             
            
        }
        
        
         /**
         * This is the function that retrieves all stationaries and learning tool products category on the store
         */
        public function actionListAllStationaryAndLearningToolsCategory(){
            
            $model = new Service;
            
            //get the service id of the learning products service
            $service_id = $model->getThisServiceIdGiveItsCode('LEARNINGTOOLS');
            
                     
             $criteria = new CDbCriteria();
             $criteria->select = '*';
             $criteria->condition='service_id=:id and is_available=:is_available';
             $criteria->params = array(':id'=>$service_id,'is_available'=>1);
             $categories= Category::model()->findAll($criteria,array('order'=>'name'));
             
             if($categories===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "category" => $categories
                                        
                                
                            ));
                       
                }
             
            
        }
        
        
        /**
         * This is the function that list all faas categories on the store
         */
        public function actionListAllFaasservicesProductsCategory(){
            
           $model = new Category;
           $start = $_REQUEST['start'];
           $limit = $_REQUEST['limit'];
           
           
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='is_faas=:isfaas and is_available=:is_available';
            $criteria->params = array(':isfaas'=>1,'is_available'=>1);
            $criteria->order = "name";
            $criteria->offset = $start;
            $criteria->limit = $limit;     
            $categories= Category::model()->findAll($criteria);
            
            //get the total number of faas products on the store
            $counts = $model->getTheTotalNumberOfFaasProductsOnTheStore();
             
             if($categories===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "results"=>$counts,
                                        "category" => $categories
                                        
                                
                            ));
                       
                }
           
           
                    
                 
        }
        
        
}


