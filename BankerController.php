<?php

class BankerController extends Controller
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
				'actions'=>array('index','view','ListAllBanksInThePlatform'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','AddNewBankAccount','ListAllBanksInThePlatform','UpdateBankAccount',
                                    'DeleteOneBankAccount','retrieveCountryName','AssignThisAccountToCountry','ListTheBankersForCountries',
                                    'retrieveCountryWithBankAccount','updateCountryBankAccount','approveOrDisapproveCountryBankAccount',
                                    'DisapproveCountryBankAccount','approveCountryBankAccount','ActivateOrDeactivateCountryBankAccount',
                                    'DeleteOneBankAccountFromCountry'),
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
         * This is the function that will add new bank account number on the platform
         */
        public function actionAddNewBankAccount(){
            
            $model = new Banker;
            
            $model->account_name = $_POST['account_name'];
            
            $model->account_number = $_POST['account_number'];
            
            $model->bank_name = $_POST['bank_name'];
            if(isset($_POST['default_bank'])){
                if($this->isSureThatNoDefaultAccountExist()){
                    $model->default_bank = $_POST['default_bank']; 
                }
               
            }else{
                $model->default_bank = 0;
            }
            $model->sort_code = $_POST['sort_code'];
            $model->swift_code = $_POST['swift_code'];
            $model->type = $_POST['type'];
            $model->create_time = new CDbExpression('NOW()');
            $model->create_user_id = Yii::app()->user->id;
            
            if($model->save()){
               $msg = "Successfully added '$model->account_number' bank account number on the platform";
               header('Content-Type: application/json');
               echo CJSON::encode(array(
                      "success" => mysql_errno() == 0,
                       "msg" => $msg
                       ));
           }else{
                $msg = "'$model->account_number' account number could not be added";
               header('Content-Type: application/json');
               echo CJSON::encode(array(
                      "success" => mysql_errno() != 0,
                       "msg" => $msg
                       ));
           }  
        }
        
        
        /**
         * This is the functon that ensures that no other default account statement exist
         */
        public function isSureThatNoDefaultAccountExist(){
            
           if($this->doesDefaultAccountAlreadyExist()){
               if($this->resetTheDefaultBankAccount()){
                   return true;
               }else{
                   return false;
               }
               
           }else{
               return true;
           }
        }
        
        
        
        /**
         * This is the function that determines if default account already existed
         */
        public function doesDefaultAccountAlreadyExist(){
             $cmd =Yii::app()->db->createCommand();
                $cmd->select('COUNT(*)')
                    ->from('banker')
                    ->where("default_bank = 1");
                $result = $cmd->queryScalar();
                
                if($result > 0){
                    return true;
                }else{
                    return false;
                }
        }
        
        
        /**
         * This is the function that resets the default bank number
         */
        public function resetTheDefaultBankAccount(){
            
            $account_id = $this->getTheCurrentDefaultAccount();
            
            if($this->isCurrentDefaultAccountResetted($account_id)){
                return true;
            }else{
                return false;
            }
        }
        
        
        /**
         * This is the function that gets the current default account
         */
        public function getTheCurrentDefaultAccount(){
                    $criteria = new CDbCriteria();
                    $criteria->select = '*';
                    $criteria->condition='default_bank=:default';
                    $criteria->params = array(':default'=> 1);
                    $default = Banker::model()->find($criteria);   
                    
                    return $default['id'];
        }
        
        
        /**
         * This is the function that resets the current default account
         */
        public function isCurrentDefaultAccountResetted($account_id){
            
            $cmd =Yii::app()->db->createCommand();
             $result = $cmd->update('banker',
                                  array(
                                    'default_bank'=>0,
                                    
                            ),
                     ("id=$account_id")
                );
             if($result>0){
                 return true;
             }else{
                 return false;
             }
        }
        
        
        /**
         * This is the function that updates bank account information
         */
        public function actionUpdateBankAccount(){
            
            $_id = $_POST['id'];
            $model=Banker::model()->findByPk($_id);
            
            $model->account_name = $_POST['account_name'];
            
            $model->account_number = $_POST['account_number'];
            
            $model->bank_name = $_POST['bank_name'];
            
            //get the existing default_bank status of  this bank account
            $current_bank_status = $this->getTheDefaultAccountStatusOfThisAccount($_id);
            if(isset($_POST['default_bank'])){
                if($this->isSureThatNoDefaultAccountExist()){
                    $model->default_bank = $_POST['default_bank']; 
                }
            }else{
                $model->default_bank = $current_bank_status ;
            }
            $model->sort_code = $_POST['sort_code'];
            $model->swift_code = $_POST['swift_code'];
            $model->type = $_POST['type'];
            $model->update_time = new CDbExpression('NOW()');
            $model->update_user_id = Yii::app()->user->id;
            
            if($model->save()){
               $msg = "Successfully updated '$model->account_number' bank account number on the platform";
               header('Content-Type: application/json');
               echo CJSON::encode(array(
                      "success" => mysql_errno() == 0,
                       "msg" => $msg
                       ));
           }else{
                $msg = "'$model->account_umber' account number could not be updated";
               header('Content-Type: application/json');
               echo CJSON::encode(array(
                      "success" => mysql_errno() != 0,
                       "msg" => $msg
                       ));
           }  
            
            
        }
        
        
        /**
         * This is the function that gets the value of the default_bank status of a given account number 
         */
        public function getTheDefaultAccountStatusOfThisAccount($id){
            
                    $criteria = new CDbCriteria();
                    $criteria->select = '*';
                    $criteria->condition='id=:id';
                    $criteria->params = array(':id'=> $id);
                    $default = Banker::model()->find($criteria);   
                    
                    return $default['default_bank'];
            
        }
        
        
        /**
         * This is the function that deletes one bank account from the platform
         */
        public function actionDeleteOneBankAccount(){
            
            
            $_id = $_POST['id'];
            //get the name of this designation
            $account = $this->getThisAccountNumber($_id);
            $model=Banker::model()->findByPk($_id);
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
                    $msg = "'$account' account number was successfully deleted"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                            //"category" =>$category,
                           
                           
                          
                       ));
                       
            } else {
                    $msg = "'$account' account number was not deleted"; 
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
         * This is the function that gets the account number given the account id
         */
        public function getThisAccountNumber($id){
            
                    $criteria = new CDbCriteria();
                    $criteria->select = '*';
                    $criteria->condition='id=:id';
                    $criteria->params = array(':id'=> $id);
                    $account = Banker::model()->find($criteria);   
                    
                    return $account['account_number'];
            
        }
        
        
        /**
         * This is the function that liat all bank accounts on the platform
         */
        public function actionListAllBanksInThePlatform(){
            
            $criteria = new CDbCriteria();
               $criteria->select = '*';
               $criteria->condition='type!=:type';
               $criteria->params = array(':type'=>'special_report');
               $accounts =Banker::model()->findAll($criteria);
                 
            if($accounts===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "bank" => $accounts
            
                       ));
                       
                }
            
        }
        
        
        /**
         * This is the function that gets a country name given the id
         * 
         */
        public function actionretrieveCountryName(){
            
            $country_id = $_REQUEST['id'];
            
            $criteria = new CDbCriteria();
               $criteria->select = '*';
               $criteria->condition='id=:id';
               $criteria->params = array(':id'=>$country_id);
               $country =Country::model()->find($criteria);
                 
            if($country===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "country" => $country['name']
            
                       ));
                       
                }
            
            
        }
        
        
        /**
         * This is the function that assigns an account number to a country
         */
        public function actionAssignThisAccountToCountry(){
            
            $country_id = $_REQUEST['id'];
            
            $bank_id = $_REQUEST['account'];
            
            $payment_mode = $_REQUEST['payment_mode'];
            
            //get the account number
            $account_number = $this->getThisAccountNumber($bank_id);
            
            //get the country name
            $country_name = $this->getThisCountryName($country_id);
            
            //confirm if that account had already been assigned to that country
            if($this->isThisAccountNumberNotAlreadyAssignedToThisCountry($bank_id,$country_id )){
                $cmd =Yii::app()->db->createCommand();
             $result = $cmd->insert('bank_collect_for_country',
                                  array(
                                    'country_id'=>$country_id,
                                    'bank_id'=>$bank_id,
                                    'payment_mode'=>$payment_mode, 
                                   'initiator_create_time' =>new CDbExpression('NOW()'),
                                   'initiator_create_user_id'=>Yii::app()->user->id,
          
                            )
			
                        );
             if($result===0) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg" => "Assignment of '$account_number' Bank Account number to '$country_name' was successful"
            
                       ));
                       
                }
            }else{
               header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => "Duplicate assignment not possible as '$account_number' Bank Account number is already assigned to '$country_name'."
            
                       )); 
            }
             
        }
        
        
        /**
         * This is the function that verifies if an account number is already assigned to a country
         */
        public function isThisAccountNumberNotAlreadyAssignedToThisCountry($bank_id,$country_id){
            
            $cmd =Yii::app()->db->createCommand();
                $cmd->select('COUNT(*)')
                    ->from('bank_collect_for_country')
                    ->where("bank_id = $bank_id and country_id = $country_id");
                $result = $cmd->queryScalar();
                
                if($result <= 0){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        /**
         * This is the function that gets the country name given its id
         */
        public function getThisCountryName($country_id){
            
                    $criteria = new CDbCriteria();
                    $criteria->select = '*';
                    $criteria->condition='id=:id';
                    $criteria->params = array(':id'=> $country_id);
                    $country = Country::model()->find($criteria);   
                    
                    return $country['name'];
            
        }
        
        
        /**
         * This is the function list all bank accounts assigned to a country
         */
        public function actionListTheBankersForCountries(){
            
            $country_id = $_REQUEST['id'];
            
            $criteria = new CDbCriteria();
               $criteria->select = '*';
               $criteria->condition='country_id=:id';
               $criteria->params = array(':id'=>$country_id);
               $accounts =  BankCollectForCountry::model()->findAll($criteria);
                 
            if($accounts===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "account" => $accounts
            
                       ));
                       
                }
            
        }
        
        
        /**
         * This is the function that list the features of an account assigned to country
         */
        public function actionretrieveCountryWithBankAccount(){
            
            $country_id = $_REQUEST['country_id'];
            
            $bank_id = $_REQUEST['bank_id'];
            
              $criteria = new CDbCriteria();
               $criteria->select = '*';
               $criteria->condition='id=:id';
               $criteria->params = array(':id'=>$bank_id);
               $accounts =Banker::model()->findAll($criteria);
               
               //get the country name
               $country_name = $this->getThisCountryName($country_id);
               
               
                 
            if($accounts===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "bank" => $accounts,
                            "country"=>$country_name   
            
                       ));
                       
                }
            
            
            
        }
        
        /**
         * This is the function that updates an account information assigned to a country
         */
        public function actionupdateCountryBankAccount(){
            
            $country_id = $_REQUEST['country_id'];
            
            $bank_id = $_REQUEST['bank_id'];
            
            $payment_mode = $_REQUEST['payment_mode'];
            
                        
            //get the account number
            $account_number = $this->getThisAccountNumber($bank_id);
            
            //get the country name
            $country_name = $this->getThisCountryName($country_id);
           
            $cmd =Yii::app()->db->createCommand();
             $result = $cmd->update('bank_collect_for_country',
                                  array(
                                    'payment_mode'=>$payment_mode, 
                                   'initiator_update_time' =>new CDbExpression('NOW()'),
                                   'initiator_update_user_id'=>Yii::app()->user->id,
          
                            ),
                     ("country_id = $country_id and bank_id = $bank_id" )
			
                        );
             if($result===0) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg" => "Assignment of '$account_number' Bank Account number to '$country_name' was successful"
            
                       ));
                       
                }
               
           
            
        }
        
        
        /**
         * This is the function that approves or disapproves bank account number to country
         */
        public function actionapproveCountryBankAccount(){
            
            $country_id = $_REQUEST['country_id'];
            
            $bank_id = $_REQUEST['bank_id'];
            
            $payment_mode = $_REQUEST['payment_mode'];
           if(isset($_REQUEST['approved'])){
               if($_REQUEST['approved'] == 1){
                $approve = 1;
            }else if($_REQUEST['approved'] == 0){
                $approve = 0;
            }
            
            //get the account number
            $account_number = $this->getThisAccountNumber($bank_id);
            
            //get the country name
            $country_name = $this->getThisCountryName($country_id);
           
            
            $cmd =Yii::app()->db->createCommand();
             $result = $cmd->update('bank_collect_for_country',
                                  array(
                                    'payment_mode'=>$payment_mode, 
                                   'approved_time' =>new CDbExpression('NOW()'),
                                   'approved_user_id'=>Yii::app()->user->id,
                                    'approved'=>$approve 
          
                            ),
                     ("country_id = $country_id and bank_id = $bank_id" )
			
                        );
            if($result===0) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg" => "Assignment of '$account_number' Bank Account number to '$country_name' is approved"
            
                       ));
                       
                } 
           }else{
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => "Please check the 'Approve This Account for this country'radio button to approve"
            
                       ));
                       
           } 
            
            
                        
            
             
             
            
            
        }
        
        
                   
       /**
         * This is the function that approves or disapproves bank account number to country
         */
        public function actionDisapproveCountryBankAccount(){
            
            $country_id = $_REQUEST['country_id'];
            
            $bank_id = $_REQUEST['bank_id'];
            
            $payment_mode = $_REQUEST['current_payment_mode'];
            
            if($_REQUEST['approved'] == 1){
                $approve = 0;
            }else if($_REQUEST['approved'] == 0){
                $approve = 0;
            }
            
                        
            //get the account number
            $account_number = $this->getThisAccountNumber($bank_id);
            
            //get the country name
            $country_name = $this->getThisCountryName($country_id);
           
            $cmd =Yii::app()->db->createCommand();
             $result = $cmd->update('bank_collect_for_country',
                                  array(
                                    'payment_mode'=>$payment_mode, 
                                   'approved_time' =>new CDbExpression('NOW()'),
                                   'approved_user_id'=>Yii::app()->user->id,
                                    'disapproved_user_id'=> Yii::app()->user->id,
                                    'disapproved_time'=> new CDbExpression('NOW()'),
                                    'approved'=>$approve 
          
                            ),
                     ("country_id = $country_id and bank_id = $bank_id" )
			
                        );
             
              if($result===0) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg" => "Assignment of '$account_number' Bank Account number to '$country_name' is disapproved"
            
                       ));
                       
                }
                 
     
            
        }
        
        
        /**
         * This is the function that activate or deactivates an account for country
         */
        public function actionActivateOrDeactivateCountryBankAccount(){
            
             $country_id = $_REQUEST['country_id'];
            
            $bank_id = $_REQUEST['bank_id'];
            
            $payment_mode = $_REQUEST['current_payment_mode'];
            
            if(isset($_REQUEST['approved'])){
               $approved = $_REQUEST['approved']; 
            }
            
            if(isset($_REQUEST['status'])){
                 $status = $_REQUEST['status'];
            }else{
                $status = $this->getTheStatusOfThisCountryAccount($country_id,$bank_id);
            }
           
            
            if($this->isThereAnyCurrentActiveAccountForThisCountry($country_id)){
                //make that account inactive
                $this->makeTheAccountInactive($country_id);
            }
            
            
                        
            //get the account number
            $account_number = $this->getThisAccountNumber($bank_id);
            
            //get the country name
            $country_name = $this->getThisCountryName($country_id);
            
         
             
                $cmd =Yii::app()->db->createCommand();
             $result = $cmd->update('bank_collect_for_country',
                                  array(
                                    'payment_mode'=>$payment_mode, 
                                   'activated_time' =>new CDbExpression('NOW()'),
                                   'activated_user_id'=>Yii::app()->user->id,
                                    'status'=>$status 
          
                            ),
                     ("country_id = $country_id and bank_id = $bank_id" )
			
                        );
             if($status == 'active'){
                 if($result===0) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg" => "Assignment of '$account_number' Bank Account number to '$country_name' is activated"
            
                       ));
                       
                }
             }else if($status == 'inactive'){
                 
                 if($result===0) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg" => "Assignment of '$account_number' Bank Account number to '$country_name' is deactivated"
            
                       ));
                       
                }
                 
             }
                
                
            
            
        }
        
        
        /**
         * This is the function that confirms this country already has an active account
         */
        public function isThereAnyCurrentActiveAccountForThisCountry($country_id){
            
            $cmd =Yii::app()->db->createCommand();
                $cmd->select('COUNT(*)')
                    ->from('bank_collect_for_country')
                    ->where("country_id = $country_id and status='active'");
                $result = $cmd->queryScalar();
                
                if($result > 0){
                    return true;
                }else{
                    return false;
                }
        }
        
        
        /**
         * This is the function that resets an active account for a ountry
         */
        public function makeTheAccountInactive($country_id){
            
               $criteria = new CDbCriteria();
               $criteria->select = '*';
               $criteria->condition='country_id=:id and status=:status';
               $criteria->params = array(':id'=>$country_id,':status'=>'active');
               $account =  BankCollectForCountry::model()->find($criteria);
               
               //reset that active account
               if($this->resetTheCountryActiveAccount($country_id, $account['bank_id'])){
                   return true;
               }else{
                   return false;
               }
            
            
        }
        
        
        /**
         * This is the function that actually reset the active accounts
         */
        public function resetTheCountryActiveAccount($country_id, $bank_id){
            
            $cmd =Yii::app()->db->createCommand();
            $result = $cmd->update('bank_collect_for_country',
                                  array(
                                    'status'=>'inactive'
          
                            ),
                     ("country_id = $country_id and bank_id = $bank_id" )
			
                        );
            
            if($result > 0){
                return true;
            }else{
                return false;
            }
        }
        
        
        /**
         * This is the funcrion that removes one account number from a country
         */
        public function actionDeleteOneBankAccountFromCountry(){
            
            $country_id = $_REQUEST['country_id'];
            
            $bank_id = $_REQUEST['bank_id'];
            
            //get the account number
            $account_number = $this->getThisAccountNumber($bank_id);
            
            //get the country name
            $country_name = $this->getThisCountryName($country_id);
            
             $cmd =Yii::app()->db->createCommand();  
                $result = $cmd->delete('bank_collect_for_country', 'country_id=:countryid  and bank_id=:bankid', array(':countryid'=>$country_id,':bankid'=>$bank_id ));
            
                if($result>0){
                header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg" => "'$account_number' Bank Account number is successfully removed from '$country_name'"
            
                       ));
                }else{
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => "'$account_number' Bank Account number could not be  removed from '$country_name'"
            
                       ));
                }
        }
        
        
        /**
         * This is the function that retrieves the status of an account to a country
         */
        public function getTheStatusOfThisCountryAccount($country_id,$bank_id){
            
               $criteria = new CDbCriteria();
               $criteria->select = '*';
               $criteria->condition='country_id=:id and bank_id=:bankid';
               $criteria->params = array(':id'=>$country_id,':bankid'=>$bank_id);
               $status =  BankCollectForCountry::model()->find($criteria);
               
               return $status['status'];
            
        }
}
