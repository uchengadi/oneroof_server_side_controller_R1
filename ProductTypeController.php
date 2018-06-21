<?php

class ProductTypeController extends Controller
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
				'actions'=>array('index','view','ListAllProducttypesForACategory','ListAllTheseProducttypes',
                                    'ListAllAvailableProducttypesForThisCategory','ListAllBookProductsType','obtainProductTypeExtraInformation','ListAllPaasBuckets',
                                    'ListAllAvailableProducttypesForThisFaasCategory','retrieveTheNameOfThisFassRegion'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('DeleteThisProducttype','createnewproducttype','updateproducttype',
                                    'ListAllProducttypes','obtainProductTypeExtraInformation','ListAllTheseProducttypes',
                                    'ListAllProducttypesForThisCategory'),
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
         * This is the function that deletes a product type
         */
        public function actionDeleteThisProducttype(){
            
             $_id = $_POST['id'];
            $model= ProductType::model()->findByPk($_id);
            
            //get the product type name
            $type_name = $this->getThisProductTypeName($_id);
            if($model === null){
                 $msg = 'No Such Record Exist'; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                          
                           
                           
                          
                       ));
                                      
            }else if($model->delete()){
                    $msg = "'$type_name' Product Type had been deleted successfully"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                           
                           
                           
                          
                       ));
                       
            } else {
                    $msg = "Validation Error: '$type_name' Product Type was not deleted"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            //"selected" => $selected,
                            "msg" => $msg
                           
                           
                           
                          
                       ));
                            
                }
            
            
        }
        
        /**
         * This is the function that list all product type
         */
        public function actionListAllProducttypes(){
            
            $model = new ProductType;
            $start = $_REQUEST['start'];
            $limit = $_REQUEST['limit'];
            $userid = Yii::app()->user->id;
          
           $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->order = "name";
            $criteria->offset = $start;
            $criteria->limit = $limit;     
            $type = ProductType::model()->findAll($criteria);
            
            $count = $model->getTheTotalProductTypeOnTheStore();
                if($type===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "results"=>$count,
                                     "type" => $type
                                        
                                
                            ));
                       
                }
            
        }
        
        
        
        /**
         * This is the function that list all product type
         */
        public function actionListAllTheseProducttypes(){
            
            $userid = Yii::app()->user->id;
          
            $type = ProductType::model()->findAll();
                if($type===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "type" => $type
                                        
                                
                            ));
                       
                }
            
        }
        
        
        /**
         * This is the function that list all types for a category
         */
        public function actionListAllProducttypesForACategory(){
            
           
            if(is_numeric($_REQUEST['category_id'])){
                $category_id = $_REQUEST['category_id'];
            }else{
                $category_id = 0;
            }
            
            if(is_numeric($_REQUEST['service_id'])){
                $service_id = $_REQUEST['service_id'];
            }else{
                $service_id = 0;
            }
            
            
            //$category_id = 3;
            
            if($category_id == 0 and $service_id == 0){
                $type = ProductType::model()->findAll(array('order'=>'name'));
                if($type===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "type" => $type
                                        
                                
                            ));
                       
                }
                
            }else if($category_id == 0 and $service_id != 0){
                $all_types = [];
                
                $criteria3 = new CDbCriteria();
                $criteria3->select = '*';
               // $criteria3->condition='category_id=:id';
               // $criteria3->params = array(':id'=>$category_id);
                //$criteria3->order = array('order'=>'name');
                $criteria3->order = "name";
                $types = ProductType::model()->findAll($criteria3);
                
                foreach($types as $type){
                    if($this->isTypeCategoryInThisService($type['category_id'],$service_id)){
                        $all_types[] = $type;
                    }
                }
            
                if($types===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"assigned" => $assigned,
                            "type" => $all_types)
                       );
                       
                }
                
                
            }else if($category_id != 0 and $service_id == 0){
                
                $criteria1 = new CDbCriteria();
                $criteria1->select = '*';
                $criteria1->condition='category_id=:id';
                $criteria1->params = array(':id'=>$category_id);
               // $criteria1->order = array('order'=>'name');
                $criteria1->order = "name";
                $type = ProductType::model()->findAll($criteria1,array('order'=>'name')); 
            
                if($type===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"assigned" => $assigned,
                            "type" => $type)
                       );
                       
                }
                
            }else if($category_id != 0 and $service_id != 0){
                $all_types = [];
                
                $criteria2 = new CDbCriteria();
                $criteria2->select = '*';
                $criteria2->condition='category_id=:id';
                $criteria2->params = array(':id'=>$category_id);
                $criteria2->order = 'name';
                $types = ProductType::model()->findAll($criteria2);
                
                foreach($types as $type){
                    if($this->isTypeCategoryInThisService($type['category_id'],$service_id)){
                        $all_types[] = $type;
                    }
                }
            
                if($types===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"assigned" => $assigned,
                            "type" => $all_types)
                       );
                       
                }
                
                
            }
               
                
                
            }
            
            
             /**
         * This is the function that list all types for a category
         */
        public function actionListAllProducttypesForThisCategory(){
            
           
            if(is_numeric($_REQUEST['category_id'])){
                $category_id = $_REQUEST['category_id'];
            }else{
                $category_id = 0;
            }
            if($category_id > 0){
                
                $criteria1 = new CDbCriteria();
                $criteria1->select = '*';
                $criteria1->condition='category_id=:id';
                $criteria1->params = array(':id'=>$category_id);
               // $criteria1->order = array('order'=>'name');
                $criteria1->order = "name";
                $type = ProductType::model()->findAll($criteria1,array('order'=>'name')); 
            
                if($type===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"assigned" => $assigned,
                            "type" => $type)
                       );
                       
                }
            }
               
               
                
                
            }
            
            
            /**
             * This is a function that confirms if a category type belongs to a service
             */
            public function isTypeCategoryInThisService($category_id,$service_id){
                $model  = new Category;
                return $model->isTypeCategoryInThisService($category_id,$service_id);
            }
            
            
            
        
        
        
        /**
         * This is the function that creates  a new product type
         */
        public function actioncreatenewproducttype(){
            
            $model=new ProductType;
            
            $model->name = $_POST['name'];
            $model->description = $_POST['description'];
            $model->code = $_POST['code'];
            $model->vat_rate = $_POST['vat_rate'];
            $model->sales_tax_rate = $_POST['sales_tax_rate'];
            if(isset($_POST['is_available'])){
                $model->is_available = $_POST['is_available'];
            }else{
                $model->is_available = 0;
            }
            $model->sales_tax_rate = $_POST['sales_tax_rate'];
            if(isset($_POST['decision']) == strtolower("is_paas")){
                $model->is_paas = 1;
                $model->is_faas = 0;
                $model->monthly_paas_subscription_cost = $_POST['monthly_paas_subscription_cost'];
                $model->minimum_quantity_for_paas_subscription = $_POST['minimum_quantity_for_paas_subscription'];
                $model->maximum_quantity_for_paas_subscription = $_POST['maximum_quantity_for_paas_subscription'];
                $model->minimum_paas_duration = $_POST['minimum_paas_duration'];
                $model->maximum_paas_duration = $_POST['maximum_paas_duration'];
            }else if(isset($_POST['decision']) == strtolower("is_faas")){
                 $model->is_faas = 1;
                 $model->is_paas = 0;
            }else{
                $model->is_paas = 0;
                $model->is_faas= 0;
            }
            $model->vat_rate_commencement_date = date("Y-m-d H:i:s", strtotime($_POST['vat_rate_commencement_date'])); 
            $model->sales_tax_rate_commencement_date = date("Y-m-d H:i:s", strtotime($_POST['sales_tax_rate_commencement_date'])); 
            $model->create_time = new CDbExpression('NOW()');
            $model->create_user_id = Yii::app()->user->id;
            if(is_numeric($_POST['category'])){
                    $model->category_id = $_POST['category']; 
                }else{
                     $model->category_id = $_POST['category_id']; 
                }
                $icon_error_counter = 0;
                 if($_FILES['icon']['name'] != ""){
                    if($this->isIconTypeAndSizeLegal()){
                        
                       $icon_filename = $_FILES['icon']['name'];
                      $icon_size = $_FILES['icon']['size'];
                        
                    }else{
                       
                        $icon_error_counter = $icon_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    $icon_filename = $this->provideProductTypeIconWhenUnavailable($model); 
                   $icon_size = 0;
             
                }//end of the if icon is empty statement
                if($icon_error_counter ==0){
                   if($model->validate()){
                           $model->icon = $this->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                           $model->icon_size = $icon_size;
                           
                       if($model->save()) {
                        
                                $msg = "'$model->name' product type/bucket was successful created";
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
                            $msg = "Validation Error: '$model->name' product type/bucket   was not created successful";
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
         * This is the function that updates a product type
         */
        public function actionupdateproducttype(){
            
            $_id = $_POST['id'];
            $model= ProductType::model()->findByPk($_id);
            
            $model->name = $_POST['name'];
            $model->description = $_POST['description'];
            $model->code = $_POST['code'];
            $model->vat_rate = $_POST['vat_rate'];
             if(isset($_POST['is_available'])){
                $model->is_available = $_POST['is_available'];
            }else{
                $model->is_available = 0;
            }
            $model->sales_tax_rate = $_POST['sales_tax_rate'];
           if(isset($_POST['decision']) == strtolower("is_paas")){
                $model->is_paas = 1;
                $model->is_faas = 0;
                $model->monthly_paas_subscription_cost = $_POST['monthly_paas_subscription_cost'];
                $model->minimum_quantity_for_paas_subscription = $_POST['minimum_quantity_for_paas_subscription'];
                $model->maximum_quantity_for_paas_subscription = $_POST['maximum_quantity_for_paas_subscription'];
                $model->minimum_paas_duration = $_POST['minimum_paas_duration'];
                $model->maximum_paas_duration = $_POST['maximum_paas_duration'];
            }else if(isset($_POST['decision']) == strtolower("is_faas")){
                 $model->is_faas = 1;
                 $model->is_paas = 0;
            }else{
                $model->is_paas = 0;
                $model->is_faas= 0;
            }
            $model->vat_rate_commencement_date = date("Y-m-d H:i:s", strtotime($_POST['vat_rate_commencement_date'])); 
            $model->sales_tax_rate_commencement_date = date("Y-m-d H:i:s", strtotime($_POST['sales_tax_rate_commencement_date'])); 
            $model->update_time = new CDbExpression('NOW()');
            $model->update_user_id = Yii::app()->user->id;
             
             //get the product type.bucket name
                $typename = $this->getThisProductTypeName($_id);
                
            if(is_numeric($_POST['category'])){
                    $model->category_id = $_POST['category']; 
                }else{
                     $model->category_id = $_POST['category_id']; 
                }
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
                        
                                $msg = "'$typename' product type/bucket Information was successfully updated";
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
                            $msg = "Validation Error: '$typename' product type/bucket information update was not successful";
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
         * This is the function that retrieves the product type name
         */
        public function getThisProductTypeName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $name = ProductType::model()->find($criteria);
            
            return $name['name'];
            
            
        }
        
        
        /**
         * This is the function that retrieves additional information about a product type
         */
        public function actionobtainProductTypeExtraInformation(){
            
            $model = new Category;
            $id = $_REQUEST['id'];
            $category_id = $_REQUEST['category_id'];
            
            $category_name = $model->getCategoryName($category_id);
            
           //get the service id of this category
            $service_id = $model->getTheServiceIdOfThisCategory($category_id);
            
                           
            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "id" => $id,
                                        "category"=>$category_name,
                                        "service_id"=>$service_id
                                        
                                
                            ));
            
        }
        
        /**
         * This is the function that list all available products types for a home page linage
         *
         */
        public function actionListAllAvailableProducttypesForThisCategory(){
            
            $model = new ProductType;
            $start = $_REQUEST['start'];
            $limit = $_REQUEST['limit'];
            $category_id = $_REQUEST['category_id']; 
            
            
            $criteria = new CDbCriteria();
             $criteria->select = '*';
             $criteria->condition='category_id=:id and is_available=:is_available';
             $criteria->params = array(':id'=>$category_id,':is_available'=>1);
             $criteria->order = "name";
             $criteria->offset = $start;
             $criteria->limit = $limit;    
             $type = ProductType::model()->findAll($criteria,array('order'=>'name')); 
             
             //get the total number of types for this category
             $counts = $model->getTheTotalNumberOfTypesForThisCategory($category_id);
            
                if($type===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                           "results"=>$counts,
                            "type" => $type)
                       );
                       
                }
            
            
        }
        
        
        /**
         * This is the function that list all paas buckets on the store
         */
        public function actionListAllPaasBuckets(){
            
            $model = new ProductType;
            $start = $_REQUEST['start'];
            $limit = $_REQUEST['limit'];
                
             $criteria = new CDbCriteria();
             $criteria->select = '*';
             $criteria->condition='is_paas=:ispaas and is_available=:is_available';
             $criteria->params = array(':ispaas'=>1,':is_available'=>1);
             $criteria->order = "name";
             $criteria->offset = $start;
             $criteria->limit = $limit;     
             $type = ProductType::model()->findAll($criteria,array('order'=>'name')); 
             
             //get the total number of paas buckets in the store
             $count = $model->getTheTotalNumberOfPaasBuckets();
            
                if($type===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "results"=>$count,
                            "type" => $type)
                       );
                       
                }
            
            
        }
        
        
        
         /**
	 * Provided icon when unavailable
	 */
	public function provideProductTypeIconWhenUnavailable($model)
	{
		return 'producttype_unavailable.png';
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
            $icon = ProductType::model()->find($criteria);
            
            
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
            $icon = ProductType::model()->find($criteria);
            
            
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
                          if($icon_filename != 'producttype_unavailable.png'){
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
                            if($icon_filename != 'producttype_unavailable.png'){
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
                $icon= ProductType::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "/admin.oneroof.com.ng/cobuy_images/icons/";
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
                $icon= ProductType::model()->find($criteria);
                
                if($icon['icon'] == 'producttype_unavailable.png'){
                    return false;
                }else if($icon['icon'] == NULL){
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
                $icon= ProductType::model()->find($criteria);
                
                if($icon['icon']==$icon_filename){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        /**
         * This is the function that lists all available product types for a faas category
         */
        public function actionListAllAvailableProducttypesForThisFaasCategory(){
            
            $model = new ProductType;
            $start = $_REQUEST['start'];
            $limit = $_REQUEST['limit'];
            $category_id = $_REQUEST['category_id']; 
            
            
            $criteria = new CDbCriteria();
             $criteria->select = '*';
             $criteria->condition='category_id=:id and (is_available=:is_available and is_faas=:isfaas)';
             $criteria->params = array(':id'=>$category_id,':is_available'=>1,':isfaas'=>1);
             $criteria->order = "name";
             $criteria->offset = $start;
             $criteria->limit = $limit;    
             $type = ProductType::model()->findAll($criteria); 
             
             //get the total number of types for this category
             $counts = $model->getTheTotalNumberOfTypesForThisFaasCategory($category_id);
            
                if($type===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                           "results"=>$counts,
                            "type" => $type)
                       );
                       
                }
        }
        
        
        /**
         * This is the function that gets the name of a faas region 
         */
        public function actionretrieveTheNameOfThisFassRegion(){
            $model = new ProductType;
            
            $type_id = $_REQUEST['type_id'];
            
            $type = $model->getTheFaasNameGivenItsID($type_id);
            
            if($type===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "faasName" => $type)
                       );
                       
                }
            
        }
        
}
