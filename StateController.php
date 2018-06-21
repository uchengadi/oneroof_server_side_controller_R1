<?php

class StateController extends Controller
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
				'actions'=>array('index','view','ListAllStates','ListAllStatesForACountry','ListAllStatesForADeliveryCountry',
                                    'ListAllStatesForACorporateCountry','ListAllStatesInACountry'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('createnewstate','updatestate', 'ListAllStatesInCountry', 'DeleteThisState',
                                    'ListAllStates','obtainStateExtraInformation','ListAllStatesInACountry'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('listallstates','deleteonestate'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actioncreatenewstate()
	{
		$model=new State;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		$model->name = $_POST['name'];
                $model->country_id = $_POST['country'];
                $model->description = $_POST['description'];
                $model->state_code = $_POST['state_code'];
                $model->create_time = new CDbExpression('NOW()');
                $model->create_user_id = Yii::app()->user->id;
                $model->state_number = $_POST['state_number'];
                if(strlen($model->state_number) == 2){
                    if($this->isThisStateNumberNotAlreadyTaken($model->state_number)){
                        if($model->save()){
                         // $result['success'] = 'true';
                          $msg = 'Successfully created new state/region';
                          header('Content-Type: application/json');
                          echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                     }else {
                         //$result['success'] = 'false';
                         $msg = 'New state/region creation was unsuccessful';
                         header('Content-Type: application/json');
                        echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                    } 
                        
                        
                    }else{
                        $msg = 'The State Number had already been taken.';
                        header('Content-Type: application/json');
                        echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                        
                        
                    }
                     
                    
                    
                }else{
                   $msg = 'The State Number must be of two characters';
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                    
                }
               
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionupdatestate()
	{
            //get the country id
            $country= $_POST['country'];
         
            $_id = $_POST['id'];
            
            $model=State::model()->findByPk($_id);
            $model->name = $_POST['name'];
            if(is_numeric($country)){
                $model->country_id = $country;
                
            }else{
                $criteria = new CDbCriteria();
                $criteria->select = 'id';
                $criteria->condition='name=:id';
                $criteria->params = array(':id'=>$country);
                $country_name = Country::model()->find($criteria); 
                $model->country_id = $country_name->id;
                
                
            }
           
            $model->description = $_POST['description'];
            $model->state_code = $_POST['state_code'];
            $model->update_time = new CDbExpression('NOW()');
            $model->update_user_id = Yii::app()->user->id;
            $model->state_number = $_POST['state_number'];
            if(strlen($model->state_number) == 2){
                if($this->isThisStateNumberDifferentFromTheExistingOne($_id,$model->state_number)){
                     if($model->save()){
                       // $data['success'] = 'true';
                        $msg = 'State information successfully updated';
                         header('Content-Type: application/json');
                        echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                }else {
                   // $data['success'] = 'false';
                    $msg = 'State information update was unsuccessful';
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                }
                    
                }else{
                    
                    $msg = 'The State Number had already been taken.';
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                    
                }
               
            }else{
                 $msg = 'The State Number must be of two characters';
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                
                
            }
            
	}
        
        
        
         /**
         * This is the function that confirms if city number is valid
         */
        public function isThisStateNumberDifferentFromTheExistingOne($state_id,$new_state_number){
            $model = new State;
            //get the existing state number
            $existing_state_number = $model->getTheExistingStateNumber($state_id);
                
            if($existing_state_number == $new_state_number){
                return true;
            }else{
                if($this->isThisStateNumberNotAlreadyTaken($new_state_number)){
                    return true;
                }else{
                    return false;
                }
            }
            
        }
        

        
        /**
         * This is the function that confirms if a state number had not been taken
         */
        public function isThisStateNumberNotAlreadyTaken($state_number){
            
             $cmd =Yii::app()->db->createCommand();
            $cmd->select('COUNT(*)')
                    ->from('state')
                    ->where("state_number = '$state_number'");
                $result = $cmd->queryScalar();
                
                if($result == 0){
                    return true;
                }else{
                    return false;
                }
            
            
        }
        
        
	/**
	 * Deletes a particular state model.
	*/
	public function actionDeleteThisState()
	{
            
            $_id = $_POST['id'];
            $model=State::model()->findByPk($_id);
            
            //get the state name
            $state_name = $this->getTheStateName($_id);
            
            //get the state country name
            $state_country = $this->getTheStateCountryName($_id);
            if($model === null){
                $data['success'] = 'undefined';
                $data['msg'] = 'No such record exist';
                header('Content-Type: application/json');
                echo CJSON::encode($data);
                                      
            }else if($model->delete()){
                    $data['success'] = 'true';
                    $data['msg'] = "'$state_name' state/province in '$state_country' was successfully deleted";
                     header('Content-Type: application/json');
                    echo CJSON::encode($data);
            } else {
                    $data['success'] = 'false';
                    $data['msg'] = 'deletion unsuccessful';
                     header('Content-Type: application/json');
                    echo CJSON::encode($data);
                            
                }
	}
        
        
        /**
         * This is the function that gets a state name
         */
        public function getTheStateName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$id);
            $state = State::model()->find($criteria); 
            
            return $state['name'];
        }

        
        /**
         * This is the function that gets a state country name
         */
        public function getTheStateCountryName($state_id){
            //get the country id of this state
            $country_id = $this->getCountryIdOfState($state_id);
            
            //get the country name 
            $country_name = $this->getTheCountryNameOfThisCountryId($country_id);
            
            return $country_name;
            
        }
        
        /**
         * This is the function that gets the country id given the state id
         */
        public function getCountryIdOfState($state_id){
          
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$state_id);
            $state = State::model()->find($criteria); 
            
            return $state['country_id'];
            
        }
        
        
        /**
         * This is the function that gets the country name given its id
         */
        public function getTheCountryNameOfThisCountryId($country_id){
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$country_id);
            $country = Country::model()->find($criteria); 
            
            return $country['name'];
            
        }
		
	/**
	 * this is the function that list  all the states
	 */
	public function actionListAllStates()
	{
		$state = State::model()->findAll(array('order'=>'name'));
                if($state===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "state" => $state
                                        
                                
                            ));
                       
                }
	}
        
        
        
         /**
         * This is the function that lists all states belonging to a country
         */
        public function actionListAllStatesForACountry(){
            
            $country_id = $_REQUEST['country_id'];
            
            //$service_id = 2;
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='country_id=:id';
            $criteria->params = array(':id'=>$country_id);
            $criteria->order = "name";
            $state = State::model()->findAll($criteria); 
            
            if($state===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "state" => $state)
                       );
                       
                }
            
        }
        
        
         /**
         * This is the function that lists all states belonging to a delivery country
         */
        public function actionListAllStatesForADeliveryCountry(){
            
            $country_id = $_REQUEST['country_id'];
            
            //$service_id = 2;
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='country_id=:id';
            $criteria->params = array(':id'=>$country_id);
            $criteria->order = "name";
            $state = State::model()->findAll($criteria); 
            
            if($state===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "state" => $state)
                       );
                       
                }
            
        }
        
        
         /**
         * This is the function that lists all states belonging to a corporate country
         */
        public function actionListAllStatesForACorporateCountry(){
            
            $country_id = $_REQUEST['country_id'];
            
            //$service_id = 2;
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='country_id=:id';
            $criteria->params = array(':id'=>$country_id);
            $criteria->order = "name";
            $state = State::model()->findAll($criteria); 
            
            if($state===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "state" => $state)
                       );
                       
                }
            
        }
        
        
        /**
         * This is the function that list all states for a country
         */
        public function actionListAllStatesInACountry(){
            $country_id = $_REQUEST['country_id'];
            
            //$service_id = 2;
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='country_id=:id';
            $criteria->params = array(':id'=>$country_id);
            $criteria->order = "name";
            $state = State::model()->findAll($criteria); 
            
            if($state===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "state" => $state)
                       );
                       
                }
            
        }
        
        /**
         * This is the function that retrieves additional information for states
         */
        public function actionobtainStateExtraInformation(){
            
            $country_id = $_REQUEST['country_id'];
            
            //retrieve the country name
            $country_name = $this->getTheCountryNameOfThisCountryId($country_id);
          
            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "country" => $country_name
                                        
                                
                            ));
            
        }
        
        
        
      

	
}
