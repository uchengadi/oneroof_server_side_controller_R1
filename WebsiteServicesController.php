<?php

class WebsiteServicesController extends Controller
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
				'actions'=>array('index','view','ListAllWebsiteServices','retrieveWebsiteServicesContent','retrieveHomePageContents'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','addNewWebsiteServiceContent','updateWebsiteServiceContent',
                                    'DeleteThisServiceContent'),
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
         * This is the function that list all service content
         */
        public function actionListAllWebsiteServices(){
            
            $services = WebsiteServices::model()->findAll();
            if($services===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "service" => $services
                                        
                                
                            ));
                       
                }
        }
        
        
        /**
         * This is the function that deletes website service content
         */
        public function actionDeleteThisServiceContent(){
            
            $_id = $_POST['id'];
            $model= WebsiteServices::model()->findByPk($_id);
            
            
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
                    $msg = "Website Service content deleted successfully"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                            //"category" =>$category,
                           
                           
                          
                       ));
                       
            } else {
                    $msg = "Validation Error: Website Service content could not be deleted"; 
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
         * This is the function that add new website service content
         */
        public function actionaddNewWebsiteServiceContent(){
            
            $model=new WebsiteServices;    
            
            $model->introduction = $_POST['introduction'];
                 if(strtolower($_POST['status']) == 'active'){
                     if($model->isTheExistingServiceContentStatusDeactivated()){
                         $model->status = $_POST['status'];
                     }else{
                         $model->status = 'inactive';
                     }
                     
                 }else{
                     $model->status = $_POST['status'];
                 }
                $model->service_general = $_POST['service_general'];
                $model->service_share = $_POST['service_share'];
                $model->service_business = $_POST['service_business'];
                $model->create_time = new CDbExpression('NOW()');
                $model->create_user_id = Yii::app()->user->id;
                
                if($model->save()){
                      
                            $msg = "Website Service content was successfully added";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                            );
                           
                       
                        
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Validation Error: Could not add the website service content";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
        }
        
        
        /**
         * This is the function that updates website service content
         */
        public function actionupdateWebsiteServiceContent(){
            
             $_id = $_POST['id'];
             $model= WebsiteServices::model()->findByPk($_id);
            
             $model->introduction = $_POST['introduction'];
                 if(strtolower($_POST['status']) == 'active'){
                     if($model->isTheExistingServiceContentStatusDeactivated()){
                         $model->status = $_POST['status'];
                     }else{
                         $model->status = 'inactive';
                     }
                     
                 }else{
                     $model->status = $_POST['status'];
                 }
                $model->service_general = $_POST['service_general'];
                $model->service_share = $_POST['service_share'];
                $model->service_business = $_POST['service_business'];
                $model->update_time = new CDbExpression('NOW()');
                $model->update_user_id = Yii::app()->user->id;
                
                if($model->save()){
                      
                            $msg = "Website Service content was successfully updated";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                            );
                           
                       
                        
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Validation Error: Could not update the website service content";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
        }

        
        
          /**
         * This is the function that rerieves information about 'services' content
         */
        public function actionretrieveWebsiteServicesContent(){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='status=:status';   
            $criteria->params = array(':status'=>'active');
            $services = WebsiteServices::model()->find($criteria);
            
            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "services" => $services)
                            );
            
        }
        
        
        
           /**
         * This is the function that retrieves all home page images
         */
        public function actionretrieveHomePageContents(){
            
            $services = WebsiteServices::model()->findAll();
            if($services===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "home" => $services
                                        
                                
                            ));
                       
                }
        }
      
	
}
