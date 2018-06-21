<?php

class ServiceController extends Controller
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
				'actions'=>array('index','view'. 'listallresourcetypes','ListAllResourceTypes','ListAllServices','ListAllOfTheseServices',
                                    'retrieveSomeSearchParameters','ListAllProductsAndServices','ListAllFashionAndBeautyProductsServices','ListAllWholesaleAndCommoditiesProductsServices'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('createNewService','updateService', 'DeleteThisService',
                                    'obtainServiceExtraInformation','retrieveNecessaryInformation','ListAllOfTheseServices'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('listallresourcetypes','deleteoneresourcetype'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	
	/**
	 * Creates a new service
          */
	public function actioncreateNewService()
	{
		$model=new Service;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

                $model->name = $_POST['name'];
                $model->code = $_POST['code'];
		if(is_numeric($_POST['container'])){
                    $model->container_id = $_POST['container'];
                    
                }else{
                   $model->container_id = $_POST['container_id'];
                }
                $model->description = $_POST['description'];
                $model->create_time = new CDbExpression('NOW()');
                $model->create_user_id = Yii::app()->user->id;
                  if(isset($_POST['is_available'])){
                   $model->is_available = $_POST['is_available']; 
                }else{
                    $model->is_available = 0;
                }
                  if(isset($_POST['display_category_on_store'])){
                   $model->display_category_on_store = $_POST['display_category_on_store']; 
                }else{
                    $model->display_category_on_store=0;
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
                    $icon_filename = $this->provideServiceIconWhenUnavailable($model); 
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
	 * Update service information
	  */
	public function actionupdateService()
	{
              
		$_id = $_POST['id'];
                $model=Service::model()->findByPk($_id);
		$model->name = $_POST['name'];
                $model->code = $_POST['code'];
                if(is_numeric($_POST['container'])){
                    $model->container_id = $_POST['container'];
                    
                }else{
                   $model->container_id = $_POST['container_id'];
                }
		  if(isset($_POST['is_available'])){
                   $model->is_available = $_POST['is_available']; 
                }else{
                    $model->is_available = 0;
                }
                  if(isset($_POST['display_category_on_store'])){
                   $model->display_category_on_store = $_POST['display_category_on_store']; 
                }else{
                    $model->display_category_on_store=0;
                }  
                $model->description = $_POST['description'];
                $model->update_time = new CDbExpression('NOW()');
                $model->update_user_id = Yii::app()->user->id;
               	
		 //get the domain name
                $servicename = $this->getThisServiceName($_id);
                
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
         * This is the function that gets the service name 
         */
        public function getThisServiceName($_id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';  
            $criteria->params = array(':id'=>$_id);
            $name = Service::model()->find($criteria);
            
            return $name['name'];
            
        }
        
        
	/**
	 * Deletes a particular service model.
	  */
	public function actionDeleteThisService()
	{
           $_id = $_POST['id'];
            $model=Service::model()->findByPk($_id);
            
            //get the currency name
            $service_name = $this->getThisServiceName($_id);
            if($model === null){
                 $msg = 'No Such Record Exist'; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                          
                           
                           
                          
                       ));
                                      
            }else if($model->delete()){
                    $msg = "'$service_name' Service had been deleted successfully"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                           
                           
                           
                          
                       ));
                       
            } else {
                    $msg = "Validation Error: '$service_name' Service was not deleted"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            //"selected" => $selected,
                            "msg" => $msg
                           
                           
                           
                          
                       ));
                            
                }
	}

        
                
    	/**
	 * Manages all models.
	 */
	public function actionListAllServices()
	{
            $model = new Service;
            $start = $_REQUEST['start'];
            $limit = $_REQUEST['limit'];
            $userid = Yii::app()->user->id;
          
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->order = "name";
            $criteria->offset = $start;
            $criteria->limit = $limit;     
            $service = Service::model()->findAll($criteria);
            
            $count = $model->getTheTotalServiceOnTheStore();
                if($service===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "results"=>$count,
                                     "service" => $service
                                        
                                
                            ));
                       
                }
            
            
	}
        
        
        /**
	 * Manages all models.
	 */
	public function actionListAllOfTheseServices()
	{
            $userid = Yii::app()->user->id;
          
            $service = Service::model()->findAll();
                if($service===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "service" => $service
                                        
                                
                            ));
                       
                }
            
            
	}
        
        
                
        /**
         * This is a function that determines if a user has a particular privilege assigned to him
         */
        public function determineIfAUserHasThisPrivilegeAssigned($userid, $privilegename){
            
             $allprivileges = [];
            //spool all the privileges assigned to a user
                $criteria7 = new CDbCriteria();
                $criteria7->select = 'itemname, userid';
                $criteria7->condition='userid=:userid';
                $criteria7->params = array(':userid'=>$userid);
                $priv= AuthAssignment::model()->find($criteria7);
                
                //retrieve all the children of the role
                
                $criteria = new CDbCriteria();
                $criteria->select = 'child';
                $criteria->condition='parent=:parent';
                $criteria->params = array(':parent'=>$priv['itemname']);
                $allprivs= Authitemchild::model()->findAll($criteria);
                 
                //check to see if this privilege exist for this user
                foreach($allprivs as $pris){
                    if($this->privilegeType($pris['child'])== 0){
                        $allprivileges[] = $pris['child'];
                        
                    }elseif($this->privilegeType($pris['child'])== 1){
                        
                       $allprivileges[] = $this->retrieveAllTaskPrivileges($pris['child']); 
                    }elseif($this->privilegeType($pris['child'])== 2){
                        
                        $allprivileges[] = $this->retrieveAllRolePrivileges($pris['child']);
                    }
                    
                    
                    
                    
                }
               
                
                if(in_array($privilegename, $allprivileges)){
                    
                    return true;
                     
                }else{
                    
                    return false;
                     
                }
   
        }
        
        
       /**
         * This is the function that retrieves additional information about a service
         */
        public function actionobtainServiceExtraInformation(){
            
            
            $container_id = $_REQUEST['container_id'];
            
            $container_name = $this->getThisContainerName($container_id);
            
                   
            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "container" => $container_name
                                        
                                
                            ));
            
        }
        
        
        /**
         * This is the function that retrieves the container name
         */
        public function getThisContainerName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $name = Container::model()->find($criteria);
            
            return $name['name'];
            
        }
        
        
        
        /**
         * This is the function that returns all member privileges of a task
         */
        public function retrieveAllTaskPrivileges($task){
            
            $member = [];
            
                $criteria = new CDbCriteria();
                $criteria->select = 'child';
                $criteria->condition='parent=:parent';
                $criteria->params = array(':parent'=>$task);
                $allprivs= Authitemchild::model()->findAll($criteria);
                
                foreach($allprivs as $privs){
                    if($this->privilegeType($privs['child'])== 0){
                         $member[] = $privs['child'];
                        
                    }elseif($this->privilegeType($privs['child'])== 1){
                        
                        $member[] = $this->retrieveAllTaskPrivileges($privs['child']); 
                    }
                   
                    
                }
              return $member;
               
            
        }
        
        /**
         * This is the function that returns all members in a role
         */
        public function retrieveAllRolePrivileges($role){
            
            $member = [];
            
                $criteria = new CDbCriteria();
                $criteria->select = 'child';
                $criteria->condition='parent=:parent';
                $criteria->params = array(':parent'=>$role);
                $allprivs= Authitemchild::model()->findAll($criteria);
                
                foreach($allprivs as $privs){
                    if($this->privilegeType($privs['child'])== 0){
                         $member[] = $privs['child'];
                        
                    }elseif($this->privilegeType($privs['child'])== 1){
                        
                        $member[] = $this->retrieveAllTaskPrivileges($privs['child']); 
                    }elseif($this->privilegeType($privs['child'])== 2){
                        
                        $member[] = $this->retrieveAllRolePrivileges($privs['child']); 
                    }
                   
                    
                }
              return $member;
                
            
        }
        
        
       
        
        /**
         * This is the function that determines a privilege type
         */
        public function privilegeType($privname){
            
            $criteria7 = new CDbCriteria();
                $criteria7->select = 'name, type';
                $criteria7->condition='name=:name';
                $criteria7->params = array(':name'=>$privname);
                $privs= Authitem::model()->find($criteria7);
                
                return $privs['type'];
                
                
        }
        
         /**
	 * Provided icon when unavailable
	 */
	public function provideServiceIconWhenUnavailable($model)
	{
		return 'service_unavailable.png';
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
            $icon = Service::model()->find($criteria);
            
            
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
            $icon = Service::model()->find($criteria);
            
            
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
                $icon= Service::model()->find($criteria);
                
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
                $icon= Service::model()->find($criteria);
                
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
                $icon= Service::model()->find($criteria);
                
                if($icon['icon']==$icon_filename){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        /**
         * This is the function that retreieves some information for the store during start up
         */
        public function actionretrieveNecessaryInformation(){
            
            $general_icon = $this->getTheGeneralServiceIcon();
            
            $share_icon = $this->getTheSharedServiceIcon();
            
            $business_icon = $this->getTheBusinessServiceIcon();
            
            
             header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "general_icon" => $general_icon,
                                     "share_icon"=>$share_icon,
                                     "business_icon"=>$business_icon
                                        
                                
                            ));
        }
        
        
        /**
         * This is the function that retrieves the general service icon
         */
        public function getTheGeneralServiceIcon(){
            $model = new Service;
            
            return $model->getTheGeneralServiceIcon();
        }
        
        
        /**
         * This is the function that retrieves the share service icon
         */
        public function getTheSharedServiceIcon(){
            $model = new Service;
            
            return $model->getTheSharedServiceIcon();
        }
        
        
        /**
         * This is the function that retrieves the share service icon
         */
        public function getTheBusinessServiceIcon(){
            $model = new Service;
            
            return $model->getTheBusinessServiceIcon();
        }
        
        
        
        /**
         * This is the function that retrieves some product search parameters
         */
        public function actionretrieveSomeSearchParameters(){
            $model = new Service;
            $service_code = $_REQUEST['service_code'];
            $category_code = $_REQUEST['category_code'];
            $type_code = $_REQUEST['type_code'];
            
            //get the service id
            $service_id = $model->getThisServiceIdGiveItsCode($service_code);
            
            //get the category id
            $category_id = $this->getThisCategoryIdGivenItsCode($category_code);
            
            //get the type id
            $type_id = $this->getThisTypeIdGivenItsCode($type_code);
            
            if(is_numeric($service_id)){
                $service = $service_id;
            }else{
                $service=0;
            }
            if(is_numeric($category_id)){
                $category = $category_id;
            }else{
                $category=0;
            }
            if(is_numeric($type_id)){
                $type = $type_id;
            }else{
                $type=0;
            }
            
             header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "service" => $service,
                                     "category"=>$category,
                                     "type"=>$type
                                        
                                
                            ));
            
            
        }
        
        /**
         * This is the function that retrieves the category id gicen the category code
         */
        public function getThisCategoryIdGivenItsCode($category_code){
            $model = new Category;
            return $model->getThisCategoryIdGivenItsCode($category_code);
        }
        
        /**
         * This is the function that retrieves the type id given its code
         */
        public function getThisTypeIdGivenItsCode($type_code){
            $model = new ProductType;
            return $model->getThisTypeIdGivenItsCode($type_code);
        }
        
        
        /**
         * This is the function that list all products service/categories at the home page
         */
        public function actionListAllProductsAndServices(){
            
            $model = new Service;
            $start = $_REQUEST['start'];
            $limit = $_REQUEST['limit']; 
                       
            $userid = Yii::app()->user->id;
          
             $criteria = new CDbCriteria();
             $criteria->select = '*';
             $criteria->condition='is_available=:is_available';
             $criteria->params = array(':is_available'=>1);
             $criteria->order = "name";
             $criteria->offset = $start;
             $criteria->limit = $limit;     
             $services= Service::model()->findAll($criteria);
             
             $count = $model->getTheTotalNumberOfDisplayableAndAvailableServices();
             
                if($services===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "results"=>$count,
                                     "service" => $services
                                        
                                
                            ));
                       
                }
            
            
            
        }
        
        
        
         /**
         * This is the function that retrieves all fashion and beauty products category on the store
         */
        public function actionListAllFashionAndBeautyProductsServices(){
            
            $model = new Service;
            
            $userid = Yii::app()->user->id;
          
             $criteria = new CDbCriteria();
             $criteria->select = '*';
             $criteria->condition='is_available=:is_available';
             $criteria->params = array(':is_available'=>1);
             $services= Service::model()->findAll($criteria,array('order'=>'name'));
             
             $targets = [];
             
             foreach($services as $service){
                 if($model->isThisServiceInTheFashionAndBeautySection($service['id'])){
                     $targets[] = $service;
                 }
             }
             
                if($services===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "service" => $targets
                                        
                                
                            ));
                       
                }
            
        }
        
        
        
        
        /**
         * This is the function that retrieves all wholesale and commodity products category on the store
         */
        public function actionListAllWholesaleAndCommoditiesProductsServices(){
            
            $model = new Service;
            
            $userid = Yii::app()->user->id;
          
             $criteria = new CDbCriteria();
             $criteria->select = '*';
             $criteria->condition='is_available=:is_available';
             $criteria->params = array(':is_available'=>1);
             $services= Service::model()->findAll($criteria,array('order'=>'name'));
             
             $targets = [];
             
             foreach($services as $service){
                 if($model->isThisServiceInTheWholesaleAndCommoditiesSection($service['id'])){
                     $targets[] = $service;
                 }
             }
             
                if($services===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "service" => $targets
                                        
                                
                            ));
                       
                }
            
        }
     
}
