<?php

class WebsiteAboutUsController extends Controller
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
				'actions'=>array('index','view','ListAllAboutUs','retrieveWebsiteAboutUs'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','DeleteThisAboutUsContent','AddNewAboutUsContent',
                                    'UpdateAboutUsContent','retrieveWebsiteAboutUs'),
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
         * This is the function that list all about us content
         */
        public function actionListAllAboutUs(){
            
            $aboutus = WebsiteAboutUs::model()->findAll();
                if($aboutus===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "aboutus" => $aboutus
                                        
                                
                            ));
                       
                }
        }
        
        
        /**
         * This is the function that ddeletes about us content
         */
        public function actionDeleteThisAboutUsContent(){
            
            $_id = $_POST['id'];
            $model=  WebsiteAboutUs::model()->findByPk($_id);
            
            
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
                    $msg = "About Us content deleted successfully"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                            //"category" =>$category,
                           
                           
                          
                       ));
                       
            } else {
                    $msg = "Validation Error: About Us content could not be deleted"; 
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
         * This is the function that adds new about us content
         */
        public function actionAddNewAboutUsContent(){
            
            $model=new WebsiteAboutUs;    
            
            $model->introduction = $_POST['introduction'];
                 if(strtolower($_POST['status']) == 'active'){
                     if($model->isTheExistingAboutUsStatusDeactivated()){
                         $model->status = $_POST['status'];
                     }else{
                         $model->status = 'inactive';
                     }
                     
                 }else{
                     $model->status = $_POST['status'];
                 }
                $model->who_we_are = $_POST['who_we_are'];
                $model->who_we_serve = $_POST['who_we_serve'];
                $model->our_mission_and_vision = $_POST['our_mission_and_vision'];
                $model->create_time = new CDbExpression('NOW()');
                $model->create_user_id = Yii::app()->user->id;
                
                if($model->save()){
                      
                            $msg = "About Us content was successfully added";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                            );
                           
                       
                        
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Validation Error: Could not add the about us content";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
        }
        
        
        
        
        /**
         * This is the function that updates about us content
         */
        public function actionUpdateAboutUsContent(){
            
            $_id = $_POST['id'];
             $model=  WebsiteAboutUs::model()->findByPk($_id);
             
             $model->introduction = $_POST['introduction'];
                 if(strtolower($_POST['status']) == 'active'){
                     if($model->isTheExistingAboutUsStatusDeactivated()){
                         $model->status = $_POST['status'];
                     }else{
                         $model->status = 'inactive';
                     }
                     
                 }else{
                     $model->status = $_POST['status'];
                 }
                $model->who_we_are = $_POST['who_we_are'];
                $model->who_we_serve = $_POST['who_we_serve'];
                $model->our_mission_and_vision = $_POST['our_mission_and_vision'];
                $model->update_time = new CDbExpression('NOW()');
                $model->update_user_id = Yii::app()->user->id;
                
               if($model->save()){
                      
                            $msg = "About Us content was successfully updated";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                            );
                           
                       
                        
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Validation Error: Could not update the about us content";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
             
        }
        
        
          /**
         * This is the function that rerieves information about 'about us ' content
         */
        public function actionretrieveWebsiteAboutUs(){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='status=:status';   
            $criteria->params = array(':status'=>'active');
            $aboutus = WebsiteAboutUs::model()->find($criteria);
            
            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "aboutus" => $aboutus)
                            );
            
        }
	
}
