<?php

class CityController extends Controller
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
				'actions'=>array('index','view','ListAllCities','ListAllTheCitiesInState','ListAllCitiesForAState',
                                    'ListAllCitiesForADeliveryState','ListAllCitiesForACorporateState','ListAllCitiesInAState'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('createnewcity','updatecity', 'ListAllTheStateCities','ListAllTheCitiesInState','ListAllCities',
                                    'DeleteThisCity','obtainCityExtraInformation','isPaymentOnDeliveryPossibleInThisCity','ListAllCitiesInAState'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','DeleteOneCity'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreatenewcity()
	{
		$model=new City;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
                $model->name = $_POST['name'];
                if(is_numeric($_POST['state'])) {
                $model->state_id = $_POST['state'];
                
                 }else{
                    $model->state_id =$_POST['state_id'];
                
                
                 }
                $model->description = $_POST['description'];
                $model->create_time = new CDbExpression('NOW()');
                $model->zip_code = $_POST['zip_code'];
                $model->create_user_id = Yii::app()->user->id;
                $model->city_number = $_POST['city_number'];
                $model->top_priority_delivery_in_percentage = $_POST['top_priority_delivery_in_percentage'];
                $model->priority_delivery_in_percentage = $_POST['priority_delivery_in_percentage'];
                $model->standard_delivery_in_percentage = $_POST['standard_delivery_in_percentage'];
                $model->minimum_top_priority_delivery_amount = $_POST['minimum_top_priority_delivery_amount'];
                $model->minimum_priority_delivery_amount = $_POST['minimum_priority_delivery_amount'];
                $model->minimum_standard_delivery_amount = $_POST['minimum_standard_delivery_amount'];
                $model->estimated_number_of_days_for_top_priority_delivery = $_POST['estimated_number_of_days_for_top_priority_delivery'];
                $model->estimated_number_of_days_for_priority_delivery = $_POST['estimated_number_of_days_for_priority_delivery'];
                $model->estimated_number_of_days_for_standard_delivery = $_POST['estimated_number_of_days_for_standard_delivery'];
                if(isset($_POST['accept_payment_on_delivery'])){
                    $model->accept_payment_on_delivery = $_POST['accept_payment_on_delivery'];
                }else{
                    $model->accept_payment_on_delivery = 0;
                }
                if(strlen($model->city_number) == 2){
                    if($this->isThisCityNumberNotAlreadyTaken($model->city_number,$model->state_id)){
                            if($model->save()){
                         // $result['success'] = 'true';
                          $msg = 'New City Successfully Created';
                          header('Content-Type: application/json');
                          echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                 }else {
                         //$result['success'] = 'false';
                         $msg = 'Creation on a new city was unsuccessful';
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  }  
                    }else{
                          //$data['success'] = 'false';
                    $msg = 'The City Number had already been taken.';
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                    }
                     
                    
                }else{
                     //$data['success'] = 'false';
                    $msg = 'The City Number must be of two characters';
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                }
               
	}

	/**
	 * Updates a particular city  model.
	  */
	public function actionupdatecity()
	{
            
            //get the state id
            $state = $_POST['state'];
          
            
            $_id = $_POST['id'];
            $model=City::model()->findByPk($_id);
            $model->name = $_POST['name'];
            if(is_numeric($state)) {
                $model->state_id = $state;
                
            }else{
               $model->state_id =$_POST['state_id'];
                
                
            }
            
            $model->description = $_POST['description'];
            $model->zip_code = $_POST['zip_code'];
            $model->update_time = new CDbExpression('NOW()');
            $model->update_user_id = Yii::app()->user->id;
            $model->city_number = $_POST['city_number'];
            $model->top_priority_delivery_in_percentage = $_POST['top_priority_delivery_in_percentage'];
            $model->priority_delivery_in_percentage = $_POST['priority_delivery_in_percentage'];
            $model->standard_delivery_in_percentage = $_POST['standard_delivery_in_percentage'];
            $model->minimum_top_priority_delivery_amount = $_POST['minimum_top_priority_delivery_amount'];
            $model->minimum_priority_delivery_amount = $_POST['minimum_priority_delivery_amount'];
            $model->minimum_standard_delivery_amount = $_POST['minimum_standard_delivery_amount'];
            $model->estimated_number_of_days_for_top_priority_delivery = $_POST['estimated_number_of_days_for_top_priority_delivery'];
            $model->estimated_number_of_days_for_priority_delivery = $_POST['estimated_number_of_days_for_priority_delivery'];
            $model->estimated_number_of_days_for_standard_delivery = $_POST['estimated_number_of_days_for_standard_delivery'];
            if(isset($_POST['accept_payment_on_delivery'])){
                    $model->accept_payment_on_delivery = $_POST['accept_payment_on_delivery'];
                }else{
                    $model->accept_payment_on_delivery = 0;
                }
            if(strlen($model->city_number) == 2){
                 if($this->isThisCityNumberDifferentFromTheExistingOne($_id,$model->city_number,$model->state_id)){
                     if($model->save()){
                        //$data['success'] = 'true';
                        $msg = 'Update of this city information was successful';
                         header('Content-Type: application/json');
                        echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                }else {
                    //$data['success'] = 'false';
                    $msg = 'Update of this city information was not successful';
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                }
                     
                     
                 }else{
                     //$data['success'] = 'false';
                    $msg = 'The City Number had already been taken';
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                     
                 }
                
                
            }else{
                 //$data['success'] = 'false';
                    $msg = 'The City Number must be of two characters';
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
        public function isThisCityNumberDifferentFromTheExistingOne($city_id,$new_city_number,$state_id){
            $model = new City;
            //get the existing city number
            $existing_city_number = $model->getTheExistingCityNumber($city_id);
                
            if($existing_city_number == $new_city_number){
                return true;
            }else{
                if($this->isThisCityNumberNotAlreadyTaken($new_city_number, $state_id)){
                    return true;
                }else{
                    return false;
                }
            }
            
        }
        
        
        /**
         * This is the function that confirms if a city number had already been taken
         */
        public function isThisCityNumberNotAlreadyTaken($city_number,$state_id){
            
            //get all the cities in this state
            $cities = $this->getAllCitiesInThisState($state_id);
            
            $counter = 0;
            foreach($cities as $city){
                if($this->thisCityHasThisNumber($city,$city_number)){
                    $counter = $counter + 1;
                }
            }
            if($counter == 0){
                return true;
            }else{
                return false;
            }
            
            
        }
        
        
        /**
         * This is the function that pools all cities in a stste
         */
        public function getAllCitiesInThisState($state_id){
            
            $cities = [];
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='state_id=:id';
            $criteria->params = array(':id'=>$state_id);
            $allcity = City::model()->findAll($criteria);
            
            foreach($allcity as $city){
                 $cities[] = $city['id'];
            }
           return $cities;
   
        }

        
        /**
         * This is the function that determines if a city has a particular city number
         */
        public function thisCityHasThisNumber($city_id,$city_number){
            
             $cmd =Yii::app()->db->createCommand();
            $cmd->select('COUNT(*)')
                    ->from('city')
                    ->where("id = $city_id and city_number='$city_number'");
                $result = $cmd->queryScalar();
                
                if($result> 0){
                    return true;
                }else{
                    return false;
                }
        }
	/**
	 * Deletes a particular city model.
	
	 */
	public function actionDeleteThisCity()
	{
            $_id = $_POST['id'];
            $model=City::model()->findByPk($_id);
            
            //get the name of this city
            $city_name = $this->getTheNameOfThisCity($_id);
            
            //get the name of the state/province where the city is located
            $state_name = $this->getTheStateName($_id);
            
            //get the  country of this city
            $country_name = $this->getTheCountryName($_id);
            if($model === null){
                $data['success'] = 'undefined';
                $data['msg'] = 'No such record exist';
                header('Content-Type: application/json');
                echo CJSON::encode($data);
                                      
            }else if($model->delete()){
                    $data['success'] = 'true';
                    $data['msg'] = "'$city_name' city in '$state_name' state/province of ' $country_name' is successfully deleted";
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
         * This is the function that gets the city name given its id
         */
        public function getTheNameOfThisCity($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$id);
            $city = City::model()->find($criteria); 
            
            return $city['name'];
            
        }
        
        /**
         * This is the function that gets the state name
         */
        public function getTheStateName($city_id){
            //get the state id of this city
            $state_id = $this->getTheStateIdOfThisCity($city_id);
            
            //get the state name of this state
            $state_name = $this->getTheStateNameGivenItsId($state_id);
            
            return $state_name;
        }
        
        /**
         * This is the function that gets the state id of a city
         */
        public function getTheStateIdOfThisCity($city_id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$city_id);
            $city = City::model()->find($criteria); 
            
            return $city['state_id'];
        }
        
        
        /**
         * This is the function that gets the name of a state
         */
        public function getTheStateNameGivenItsId($state_id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$state_id);
            $state = State::model()->find($criteria); 
            
            return $state['name'];
        }
        
        /**
         * This is the function that gets the country name of a city
         */
        public function getTheCountryName($city_id){
            //get the state id of this city
            $state_id = $this->getTheStateIdOfThisCity($city_id);
            
            //get the country id of this state
            $country_id = $this->getCountryIdOfThisState($state_id);
            
            //get the name this country
            $country_name = $this->getTheCountryNameGivenItsId($country_id);
            
            return $country_name;
            
        }
        
        /**
         * This is the function that gets the country id of a state  
         */
        public function getCountryIdOfThisState($state_id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$state_id);
            $state = State::model()->find($criteria); 
            
            return $state['country_id'];
            
        }
        
        
        /**
         * This is the function that gets the country name of a country given its id
         */
        public function getTheCountryNameGivenItsId($country_id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$country_id);
            $country = Country::model()->find($criteria); 
            
            return $country['name'];
        }
               
        
        /**
	 * This is the function that list all cities 
	 */
	public function actionListAllCities()
	{
		$city = City::model()->findAll(array('order'=>'name'));
                if($city===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"assigned" => $assigned,
                            "city" => $city)
                       );
                       
                }
	}
        
        /**
	 * List all the cities that belong to a particular state
	 */
	public function actionListAllTheCitiesInState()
	{
		
                $_id = $_REQUEST['id'];
                //$_id = 1;
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='state_id=:id';
                $criteria->params = array(':id'=>$_id);
                $criteria->order = "name";
                $city= City::model()->findAll($criteria);
                if($city===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                             "city" => $city)
                       );
                       
                }
	}
        
        
        
         /**
         * This is the function that retrieves additional information for cities
         */
        public function actionobtainCityExtraInformation(){
            
            $state_id = $_REQUEST['state_id'];
            
            //retrieve the country name
            $state_name = $this->getTheStateNameGivenItsId($state_id);
          
            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "state" => $state_name
                                        
                                
                            ));
            
        }
        
        
        
         /**
         * This is the function that lists all cities belonging to a state
         */
        public function actionListAllCitiesForAState(){
            
            $state_id = $_REQUEST['state_id'];
            
            //$service_id = 2;
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='state_id=:id';
            $criteria->params = array(':id'=>$state_id);
            $criteria->order = "name";
            $city = City::model()->findAll($criteria); 
            
            if($city===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "city" => $city)
                       );
                       
                }
            
        }
        
        
           /**
         * This is the function that lists all cities belonging to a delivery state
         */
        public function actionListAllCitiesForADeliveryState(){
            
            $state_id = $_REQUEST['state_id'];
            
            //$service_id = 2;
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='state_id=:id';
            $criteria->params = array(':id'=>$state_id);
            $criteria->order = "name";
            $city = City::model()->findAll($criteria); 
            
            if($city===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "city" => $city)
                       );
                       
                }
            
        }
        
        
          /**
         * This is the function that lists all cities belonging to a corporate state
         */
        public function actionListAllCitiesForACorporateState(){
            
            $state_id = $_REQUEST['state_id'];
            
            //$service_id = 2;
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='state_id=:id';
            $criteria->params = array(':id'=>$state_id);
            $criteria->order = "name";
            $city = City::model()->findAll($criteria); 
            
            if($city===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "city" => $city)
                       );
                       
                }
  
        }
        
        /**
         * This is the function that list all cities in a state 
         */
        public function actionListAllCitiesInAState(){
            
            $state_id = $_REQUEST['state_id'];
            
            $country_id = $_REQUEST['country_id'];
            
            $all_city_state = [];
                                  
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='state_id=:id';
            $criteria->params = array(':id'=>$state_id);
            $criteria->order = "name";
            $cities = City::model()->findAll($criteria); 
            
            foreach($cities as $city){
                if($this->isThisStateInThisCountry($country_id,$city['state_id'])){
                    $all_city_state[] = $city;
                }
            }
                      
            if($all_city_state===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "city" => $all_city_state)
                       );
                       
                }
            
        }
        
        /**
         * This is the function that confirms if a state is in a country
         */
        public function isThisStateInThisCountry($country_id,$state_id){
            
            $model = new State;
            return $model->isThisStateInThisCountry($country_id,$state_id);
        }
        
        
        
         /**
                 * This is the function that confirms if payment on delivery is possible in a city
                 */
                public function actionisPaymentOnDeliveryPossibleInThisCity(){
                    $model = new City;
                    $city_id = $_REQUEST['city_id'];
                    if($model->isPaymentOnDeliveryAllowedInThisCity($city_id)){
                       header('Content-Type: application/json');
                        echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "ondelivery_payment" => true)
                       );
                    }else{
                        header('Content-Type: application/json');
                        echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "ondelivery_payment" => false)
                       );
                    }
                }
        
}
