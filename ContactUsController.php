<?php

class ContactUsController extends Controller
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
				'actions'=>array('index','view','addThisNewContactUsRequest'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
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
         * This is the function that saves a contact us request from the site/store 
         */
        public function actionaddThisNewContactUsRequest(){
            
            $model = new ContactUs;
            
            $model->requester_name = $_POST['requester_name'];
            $model->requester_type = strtolower($_POST['requester_type']);
            if(isset($_POST['requester_institution'])){
                $model->requester_institution = $_POST['requester_institution'];
            }
            
            $model->requester_email = $_POST['requester_email'];
            if(isset($_POST['requester_landline'])){
               $model->requester_landline = $_POST['requester_landline']; 
            }
            if(isset($_POST['requester_mobile_number'])){
                $model->requester_mobile_number = $_POST['requester_mobile_number'];
            }
            $model->how_to_contact_requester = strtolower($_POST['how_to_contact_requester']);
            $model->best_time_to_contact_requester = strtolower($_POST['best_time_to_contact_requester']);
            $model->best_day_to_contact_requester = strtolower($_POST['best_day_to_contact_requester']);
            $model->subject = $_POST['subject'];
            $model->request = $_POST['request'];
            $model->request_time = new CDbExpression('NOW()');
            if($model->save()){
                         // $result['success'] = 'true';
                          $msg = 'Thank you for contacting us. We will certainly get back to you';
                          header('Content-Type: application/json');
                          echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                 }else {
                         //$result['success'] = 'false';
                         $msg = 'Your Request could not be recieved. Please send a new request';
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  }  
                    
            
        }
}
