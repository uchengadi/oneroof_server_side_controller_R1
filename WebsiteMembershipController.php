<?php

class WebsiteMembershipController extends Controller
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
				'actions'=>array('index','view','ListAllWebsiteMembership','retrieveWebsiteMembershipContent'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','addNewWebsiteMembershipContent','updateWebsiteMembershipContent',
                                    'DeleteThisMembershipContent'),
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
         * This is the function that list all membership content
         */
        public function actionListAllWebsiteMembership(){
            
            $membership = WebsiteMembership::model()->findAll();
            if($membership===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "membership" => $membership
                                        
                                
                            ));
                       
                }
        }
        
        
        
        /**
         * This is the function that deletes website membership content
         */
        public function actionDeleteThisMembershipContent(){
            
            $_id = $_POST['id'];
            $model= WebsiteMembership::model()->findByPk($_id);
            
            
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
                    $msg = "Website Membership content deleted successfully"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                            //"category" =>$category,
                           
                           
                          
                       ));
                       
            } else {
                    $msg = "Validation Error: Website Membership content could not be deleted"; 
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
         * This is the function that add new website membership content
         */
        public function actionaddNewWebsiteMembershipContent(){
            
            $model=new WebsiteMembership;    
            
            $model->introduction = $_POST['introduction'];
                 if(strtolower($_POST['status']) == 'active'){
                     if($model->isTheExistingMembershipContentStatusDeactivated()){
                         $model->status = $_POST['status'];
                     }else{
                         $model->status = 'inactive';
                     }
                     
                 }else{
                     $model->status = $_POST['status'];
                 }
                $model->membership_basic = $_POST['membership_basic'];
                $model->membership_business = $_POST['membership_business'];
                //$model->membership_dons = $_POST['membership_dons'];
                $model->membership_business_prime = $_POST['membership_business_prime'];
                $model->membership_basic_prime = $_POST['membership_basic_prime'];
                $model->create_time = new CDbExpression('NOW()');
                $model->create_user_id = Yii::app()->user->id;
                
                if($model->save()){
                      
                            $msg = "Website Membership content was successfully added";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                            );
                           
                       
                        
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Validation Error: Could not add the website membership content";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
        }
        
        
        
         /**
         * This is the function that updates website membership content
         */
        public function actionupdateWebsiteMembershipContent(){
            
             $_id = $_POST['id'];
             $model= WebsiteMembership::model()->findByPk($_id);
            
             $model->introduction = $_POST['introduction'];
                 if(strtolower($_POST['status']) == 'active'){
                     if($model->isTheExistingMembershipContentStatusDeactivated()){
                         $model->status = $_POST['status'];
                     }else{
                         $model->status = 'inactive';
                     }
                     
                 }else{
                     $model->status = $_POST['status'];
                 }
                $model->membership_basic = $_POST['membership_basic'];
                $model->membership_business = $_POST['membership_business'];
                //$model->membership_dons = $_POST['membership_dons'];
                $model->membership_business_prime = $_POST['membership_business_prime'];
                $model->membership_basic_prime = $_POST['membership_basic_prime'];
                $model->update_time = new CDbExpression('NOW()');
                $model->update_user_id = Yii::app()->user->id;
                
                if($model->save()){
                      
                            $msg = "Website Membership content was successfully added";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                            );
                           
                       
                        
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Validation Error: Could not add the website membership content";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
        }

        
        
          /**
         * This is the function that rerieves information about 'membership' content
         */
        public function actionretrieveWebsiteMembershipContent(){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='status=:status';   
            $criteria->params = array(':status'=>'active');
            $membership = WebsiteMembership::model()->find($criteria);
            
            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "membership" => $membership)
                            );
            
        }
        
        
}
