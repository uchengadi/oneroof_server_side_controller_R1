<?php

class CurrenciesController extends Controller
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
				'actions'=>array('index','view','ListAllCurrencies'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','ListAllCurrencies','DeleteThisCurrency','AddNewCurrency',
                                    'UpdateCurrency','retrieveThisCurrencyCountry','retrieveBaseCurrencyName','retrieveCurrencyPairNames',
                                    'RemoveThisCurrencyPair','addNewCurrencyPairExchange','updateCurrencyPairExchange','ListAllCurrenciesAndExchanges',
                                    'obtainCurrencyExtraInformation'),
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
         * This is the function to create new currency
         */
        public function actionAddNewCurrency(){
            
            $model=new Currencies;
            
            //get the logged in user id
            $userid = Yii::app()->user->id;
            
            //determine the domain of the logged in user
           // $domainid = $this->determineAUserDomainIdGiven($userid);
                    
            $model->currency_name = $_POST['currency_name'];
            $model->currency_code = $_POST['currency_code'];
            $model->currency_symbol = $_POST['currency_symbol'];
            $model->country_id = $_POST['country'];
            if(isset($_POST['description'])){
                $model->description = $_POST['description'];
            }
            $model->create_user_id = $userid;
            $model->create_time = new CDbExpression('NOW()');
                      
           
             if($model->save()){
                      
                            $msg = "'$model->currency_name' currency was Successfully Added";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                            );
                           
                       
                        
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Validation Error: '$model->currency_name' currency Not Added";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
            
            
        }
        
        /**
         * This is the function that update a currency information
         */
        public function actionUpdateCurrency(){
            
            $_id = $_POST['id'];
            $model=Currencies::model()->findByPk($_id);
            
             //get the logged in user id
            $userid = Yii::app()->user->id;
            
            
            $model->currency_name = $_POST['currency_name'];
            $model->currency_code = $_POST['currency_code'];
            $model->currency_symbol = $_POST['currency_symbol'];
            if(is_numeric($_POST['country'])){
                 $model->country_id = $_POST['country'];
            }else{
                $model->country_id = $_POST['country_id'];
            }
           
            if(isset($_POST['description'])){
                $model->description = $_POST['description'];
            }
            $model->update_user_id = $userid;
            $model->update_time = new CDbExpression('NOW()');
                      
           
             if($model->save()){
                      
                            $msg = "'$model->currency_name' currency was Successfully updated";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                            );
                           
                       
                        
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Validation Error: '$model->currency_name' currency Not updated";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
            
        }
        
        
	/**
         * This is the function to delete one currency
         */
        public function actionDeleteThisCurrency(){
            
            $_id = $_POST['id'];
            $model=Currencies::model()->findByPk($_id);
            
            //get the currency name
            $currency_name = $this->getTheCurrencyName($_id);
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
                    $msg = "'$currency_name' Currency had been deleted successfully"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                            //"category" =>$category,
                           
                           
                          
                       ));
                       
            } else {
                    $msg = "Validation Error: '$currency_name' Currency was not deleted"; 
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
         * This is the function that retrieves the name of the country of the cuirrency
         */
        public function actionRetrieveThisCurrencyCountry(){
            
            $countryid = $_REQUEST['country_id'];
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$countryid);
            $country = Country::model()->find($criteria); 
            
             if($country===null) {
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
                            "country" =>$country['name']
                       ));
                       
                } 
            
            
            
        }
        /**
         * This is the function that list all currencies in the system
         */
        public function actionListAllCurrencies(){
            
             //obtain the id of the logged in user
            $userid = Yii::app()->user->id;
            
            //determine the domain of the logged in user
           // $domainid = $this->determineAUserDomainIdGiven($userid);
            
           //spool the products/technologies for this domain
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            //$criteria->condition='domain_id=:id';
            //$criteria->params = array(':id'=>$domainid);
            $currency= Currencies::model()->findAll($criteria);
            
                if($currency===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "currency" => $currency
                          
                           
                           
                          
                       ));
                       
                }
            
        }
        
        
        /**
         * This is the function that adds new exchange rate for a currency pair
         */
        public function actionaddNewCurrencyPairExchange(){
            
            $base_currency_id = $_POST['base_currency_id'];
            if(is_numeric($_POST['currency'])){
                $currency_id = $_POST['currency'];
            }else{
                $currency_id = $_POST['currency_id'];
            }
            $exchange_rate = $_POST['exchange_rate'];
            
            $base_currency_name = $this->getTheCurrencyName($base_currency_id);
             $currency_name = $this->getTheCurrencyName($currency_id);
            
            if($this->currencyPairNotAlreadyInTheDatabase($base_currency_id,$currency_id)){
                //insert the new currency pair and exchange rate
                if($this->isCurrencyPairAddedSuccessfully($base_currency_id,$currency_id,$exchange_rate)){
                    $msg = "$base_currency_name and $currency_name Currency Pair and the Exchange rate successfully added";
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg" => $msg
                          
                           
                           
                          
                       ));
                }else{
                    $msg = "Could not add the $base_currency_name and $currency_name currency pair to the database";
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg
                          
                           
                           
                          
                       ));
                }
            }else{
                $msg = "$base_currency_name and $currency_name Currency pair is already in the database";
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg
                          
                           
                           
                          
                       ));
            }
            
            
            
        }
        
        /**
         * This is the function that updates a currency pair exchange rate information
         */
        public function actionupdateCurrencyPairExchange(){
            
            $base_currency_id = $_POST['base_currency_id'];
            if(is_numeric($_POST['currency'])){
                $currency_id = $_POST['currency'];
            }else{
                $currency_id = $_POST['currency_id'];
            }
            $exchange_rate = $_POST['exchange_rate'];
            
             $base_currency_name = $this->getTheCurrencyName($base_currency_id);
             $currency_name = $this->getTheCurrencyName($currency_id);
           
                //edit the currency pair and exchange rate
           if($this->isCurrencyPairUpdatedSuccessfully($base_currency_id,$currency_id,$exchange_rate)){
                    $msg = "$base_currency_name and $currency_name Currency Pair and the Exchange rate were successfully updated ";
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg" => $msg
                          
                           
                           
                          
                       ));
                }else{
                    $msg = "Could not update the $base_currency_name and $currency_name currency pair on the database";
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg
                          
                           
                           
                          
                       ));
                }
            
            
        }
        
        /**
         * This is the function that list all currency pairs from currency exchange table
         */
        public function actionListAllCurrenciesAndExchanges(){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            //$criteria->condition='domain_id=:id';
            //$criteria->params = array(':id'=>$domainid);
            $currency= CurrencyExchange::model()->findAll($criteria);
            
                if($currency===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "currency" => $currency
                          
                           
                           
                          
                       ));
                       
                }
            
        }
        
        /** 
         * This is the function that removes one currency pair from the currency exchange table
         */
        public function actionRemoveThisCurrencyPair(){
            
            $base_currency_id = $_POST['base_currency_id'];
            $currency_id = $_POST['currency_id'];
            
            $base_currency_name = $this->getTheCurrencyName($base_currency_id);
            $currency_name = $this->getTheCurrencyName($currency_id);
          
            $cmd =Yii::app()->db->createCommand();  
            $result = $cmd->delete('currency_exchange', 'base_currency_id=:baseid and currency_id=:currencyid', array(':baseid'=>$base_currency_id, ':currencyid'=>$currency_id ));
            
            if($result>0){
                $msg = "Successfully deleted the $base_currency_name and $currency_name currency pair";
                 header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                           "msg" =>$msg,
                       ));
            }else{
                $msg = "Could not remove the $base_currency_name and $currency_name currency pair";
                header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                           "msg" =>$msg,
                       ));
            }
            
            
            
            
        }

        
        /**
         * This is the function that retrieves the base currency name
         */
        public function actionretrieveBaseCurrencyName(){
            
            //get the base currency id
            $base_currency_id = $this->getThePlatformBaseCurrency();
            
            //retrieve the name of the base currency
            $base_currency_name = $this->getTheCurrencyName($base_currency_id);
            
            header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                           "basecurrency" =>$base_currency_name,
                           "base_currency_id"=> $base_currency_id  
                       ));
        }
        
        /**
         * This is the function that retrieves the name of a currency pair
         */
        public function actionretrieveCurrencyPairNames(){
            
            $base_currency_id = $_REQUEST['base_currency_id'];
            
            $currency_id = $_REQUEST['currency_id'];
            
            //get their names
            $base_currency_name = $this->getTheCurrencyName($base_currency_id);
            $curreny_name = $this->getTheCurrencyName($currency_id);
            
             header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                           "basecurrency" =>$base_currency_name,
                           "currency"=> $curreny_name
                       ));
            
            
            
            
        }
        
        
        
        
        /**
         * This is the function that retrieves a currency name
         */
        public function getTheCurrencyName($currency_id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$currency_id);
            $currency = Currencies::model()->find($criteria); 
            
            return $currency['currency_name'];
            
        }
        
        /**
         * This is the function that retrieves the platform's base currency
         */
        public function getThePlatformBaseCurrency(){
                     $criteria = new CDbCriteria();
                     $criteria->select = '*';
                    // $criteria->condition='country_id=:id';
                   //  $criteria->params = array(':id'=>$country_id);
                     $platform= PlatformSettings::model()->find($criteria);
                     
                     return $platform['platform_default_currency_id'];
            
        }
        
        /**
         * This is the function that determines if a currency pair is not already in the exchange database
         */
        public function currencyPairNotAlreadyInTheDatabase($base_currency_id,$currency_id){
            
            $cmd =Yii::app()->db->createCommand();
            $cmd->select('COUNT(*)')
                    ->from('currency_exchange')
                    ->where("base_currency_id = $base_currency_id && currency_id=$currency_id");
                $result = $cmd->queryScalar();
                
                if($result <= 0){
                    return true;
                }else{
                    return false;
                }
        }
        
        /**
         * This is the function that inserts new currency pair to the database
         */
        public function isCurrencyPairAddedSuccessfully($base_currency_id,$currency_id,$exchange_rate){
            
            $cmd =Yii::app()->db->createCommand();
            $result = $cmd->insert('currency_exchange',
                         array('base_currency_id'=>$base_currency_id,
                                'currency_id' =>$currency_id,
                             'exchange_rate'=>$exchange_rate,
                             'create_time'=>new CDbExpression('NOW()'),
                             'created_by'=>Yii::app()->user->id
                        )
                          
                     );
            
            if($result > 0){
                return true;
            }else{
                return false;
            }
            
            
        }
        
        /**
         * This is the function that determines if currency pair is updated successfully
         */
        public function isCurrencyPairUpdatedSuccessfully($base_currency_id,$currency_id,$exchange_rate){
            $cmd =Yii::app()->db->createCommand();
            $result = $cmd->update('currency_exchange',
                         array('exchange_rate'=>$exchange_rate,
                             'update_time'=>new CDbExpression('NOW()'),
                             'updated_by'=>Yii::app()->user->id
                        ),
                    ("base_currency_id=$base_currency_id && currency_id=$currency_id")
                          
                     );
            
            if($result > 0){
                return true;
            }else{
                return false;
            }
            
        }
        
        
        
        
        
         /**
         * This is the function that retrieves additional information for currencies
         */
        public function actionobtainCurrenyExtraInformation(){
            
            $country_id = $_REQUEST['country_id'];
            
            //retrieve the country name
            $country_name = $this->getTheStateNameGivenItsId($state_id);
          
            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "country" => $country_name
                                        
                                
                            ));
            
        }
        
	
}
