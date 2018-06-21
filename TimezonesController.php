<?php

class TimezonesController extends Controller
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
				'actions'=>array('index','view','ListAllTimeZones'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','ListAllTimezones','DeleteThisTimezone','createnewtimezone',
                                    'updatetimezone','retrieveTheTimezoneName'),
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
         * This is the function that will create new timezone to the database
         */
        public function actioncreatenewtimezone(){
            
            $model=new Timezones;
            
            //get the logged in user id
            $userid = Yii::app()->user->id;
            
            //determine the domain of the logged in user
           // $domainid = $this->determineAUserDomainIdGiven($userid);
                    
            $model->timezone = $_POST['timezone'];
            $model->offset = $_POST['offset'];
            if(isset($_POST['description'])){
                $model->description = $_POST['description'];
            }
            $model->create_user_id = $userid;
            $model->create_time = new CDbExpression('NOW()');
                      
           
             if($model->save()){
                      
                            $msg = "'$model->timezone' timezone was Successfully added";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                            );
                           
                       
                        
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Validation Error: $model->timezone timezone not Added";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
        }
        
        
        /**
         * This is the function that will update the timezone information
         */
        public function actionupdatetimezone(){
            
            $_id = $_POST['id'];
            $model=Timezones::model()->findByPk($_id);
            
             //get the logged in user id
            $userid = Yii::app()->user->id;
            
             //determine the domain of the logged in user
           // $domainid = $this->determineAUserDomainIdGiven($userid);
                    
            $model->timezone = $_POST['timezone'];
            $model->offset = $_POST['offset'];
            if(isset($_POST['description'])){
                $model->description = $_POST['description'];
            }
            $model->update_user_id = $userid;
            $model->update_time = new CDbExpression('NOW()');
                      
           
             if($model->save()){
                      
                            $msg = "'$model->timezone' timezone was Successfully updated";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                            );
                           
                       
                        
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Validation Error: $model->timezone timezone not updated";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
        }
        
	/**
         * This is the function that deletes one timezone from the database
         */
        public function actionDeleteThisTimezone(){
            
            $_id = $_POST['id'];
            $model=Timezones::model()->findByPk($_id);
            //get the name of the timezone
            $timezone_name = $this->getTheTimezoneName($_id);
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
                    $msg = "'$timezone_name' timezone had been deleted successfully"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                            //"category" =>$category,
                           
                           
                          
                       ));
                       
            } else {
                    $msg = "Validation Error: '$timezone_name' Timezone was not deleted"; 
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
         * This is the function that gets the name of a timezone
         */
        public function getTheTimezoneName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$id);
            $timezone =Timezones::model()->find($criteria); 
            
            return $timezone['timezone'];
            
        }
        
        /**
         * This is the function that list all timezones in the system
         */
        public function actionListAllTimezones(){
            
             //obtain the id of the logged in user
            $userid = Yii::app()->user->id;
            
            //determine the domain of the logged in user
           // $domainid = $this->determineAUserDomainIdGiven($userid);
            
           //spool the products/technologies for this domain
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            //$criteria->condition='domain_id=:id';
            //$criteria->params = array(':id'=>$domainid);
            $timezone= Timezones::model()->findAll($criteria);
            
                if($timezone===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "timezone" => $timezone
                          
                           
                           
                          
                       ));
                       
                }
        }
        
        /**
         * This is the function that retrieves the timezone given the id
         */
        public function actionRetrieveTheTimezoneName(){
            $id = $_REQUEST['id'];
            
             //get the timezone given the timezone id
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$id);
            $timezone = Timezones::model()->find($criteria); 
            
            if($id===null) {
                    http_response_code(404);
                    $msg ='No record found';
                   header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg" =>$msg
                       ));
                       
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "timezone" =>$timezone['timezone']
                           
                       ));
                       
                } 
            
        }

	
}
