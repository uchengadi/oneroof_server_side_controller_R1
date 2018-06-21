<?php

class TermsAndConditionsController extends Controller
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
				'actions'=>array('index','view','retrieveTermsAndConditionContent'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','ListAllTermsAndConditions','DeleteThisTermAndConditionContent',
                                    'addNewTermsAndConditionsContent','updateTermsAndConditionsContent'),
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
         * This is the function that list all terms and conditions
         */
        public function actionListAllTermsAndConditions(){
            
            $terms = TermsAndConditions::model()->findAll();
            if($terms===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "terms" => $terms
                                        
                                
                            ));
                       
                }
            
        }
        
        
        /**
         * This is the function that deletes terms & conditions' content
         */
        public function actionDeleteThisTermAndConditionContent(){
            
            $_id = $_POST['id'];
            $model= TermsAndConditions::model()->findByPk($_id);
            
            
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
                    $msg = "This Terms & Conditions content deleted successfully"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                            //"category" =>$category,
                           
                           
                          
                       ));
                       
            } else {
                    $msg = "Validation Error: Terms & Conditions content could not be deleted"; 
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
         * This is the function that add new terms & condition content
         */
        public function actionaddNewTermsAndConditionsContent(){
            
            $model=new TermsAndConditions;    
            
            $model->membership_terms_and_conditions = $_POST['membership_terms_and_conditions'];
                 if(strtolower($_POST['status']) == 'active'){
                     if($model->isTheExistingTermsAndConditionContentStatusDeactivated()){
                         $model->status = $_POST['status'];
                     }else{
                         $model->status = 'inactive';
                     }
                     
                 }else{
                     $model->status = $_POST['status'];
                 }
                $model->purchase_terms_and_conditions = $_POST['purchase_terms_and_conditions'];
                $model->generic_terms_and_conditions = $_POST['generic_terms_and_conditions'];
                $model->create_time = new CDbExpression('NOW()');
                $model->create_user_id = Yii::app()->user->id;
                
                if($model->save()){
                      
                            $msg = "Terms & Conditions content was successfully added";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                            );
                           
                       
                        
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Validation Error: Could not add the Terms & conditions content";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
        }
        
        
        
        /**
         * This is the function that updatess terms & condition content
         */
        public function actionupdateTermsAndConditionsContent(){
            
             $_id = $_POST['id'];
             $model= TermsAndConditions::model()->findByPk($_id);
            
            $model->membership_terms_and_conditions = $_POST['membership_terms_and_conditions'];
                 if(strtolower($_POST['status']) == 'active'){
                     if($model->isTheExistingTermsAndConditionContentStatusDeactivated()){
                         $model->status = $_POST['status'];
                     }else{
                         $model->status = 'inactive';
                     }
                     
                 }else{
                     $model->status = $_POST['status'];
                 }
                $model->purchase_terms_and_conditions = $_POST['purchase_terms_and_conditions'];
                $model->generic_terms_and_conditions = $_POST['generic_terms_and_conditions'];
                $model->update_time = new CDbExpression('NOW()');
                $model->update_user_id = Yii::app()->user->id;
                
                if($model->save()){
                      
                            $msg = "Terms & Conditions content was successfully updated";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                            );
                           
                       
                        
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Validation Error: Could not update the Terms & conditions content";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
        }
	
        
          /**
         * This is the function that rerieves information about Terms & condition content
         */
        public function actionretrieveTermsAndConditionContent(){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='status=:status';   
            $criteria->params = array(':status'=>'active');
            $terms = TermsAndConditions::model()->find($criteria);
            
            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "terms" => $terms)
                            );
            
        }
}
