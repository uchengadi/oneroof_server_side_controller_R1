<?php

class SubscriptionPaymentController extends Controller
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
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('ListAllSubscriptionPayments','ListAllUnverifiedSubscriptionPayments','ListAllFailedSubscriptionPayments',
                                    'retrievePaymentDetails','retrievePaymentDetailsForUpdate','addNewSubscriptionPayment','updateSubscriptionPayment',
                                    'ListAllMembersWithFreshSubscriptionPayments','confirmThisSubscriptionPayment',
                                    'failThisSubscriptionPayment','justTest'),
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
         * This is the function that retrieves some subscription payment information 
         */
       
        public function actionretrievePaymentDetails(){
            
            $member_id = $_REQUEST['member_id'];
            
                      
            //get the membership sunsvription fee 
           $fee = $this->getTheMembersipFeeForThisType($member_id);
           // $fee = 4577.98;
            //get the discounted amount
            $discount_amount = $this->getTheDiscountedAmountOfThisMemberSubscription($member_id);
           // $discount_amount = 450.99;
            
            //get the net subscription amount 
            $net_amount = ( $fee - $discount_amount);
            
                  
            //get the invoice number of this order
            $invoice_number = $this->generateTheInvoiceNumberForThisMemberSubscription($member_id);
            //$invoice_number = '33000bb456';
            
            //get the current membership type of this member
            $membership_type_id = $this->getTheMembershipTypeIdOfThisMember($member_id);
             
            $membership_type = $this->getThisMembershipTypeName($membership_type_id);
            
             //get the membership number
            $membership_number = $this->getThisMemberMembershipNumber($member_id);
            
                    
            if($member_id===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                      header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "amount" => $fee,
                                     "discount_amount"=>$discount_amount,
                                     "invoice_number"=>$invoice_number,
                                     "membership_type"=>$membership_type,
                                     "membership_type_id"=>$membership_type_id,
                                     "membership_number"=>$membership_number,
                                     "net_amount"=>$net_amount
                                                                         
                            ));
                       
                }
            
        }
        
        
         /**
         * This is the function that generates an invoice number for subscription payment
         */
        public function generateTheInvoiceNumberForThisMemberSubscription($member_id){
            $model = new SubscriptionPayment;
            
            return $model->generateTheInvoiceNumberForThisMemberSubscription($member_id);
        }
        
     
        
        
        public function actionjustTest(){
           // $subscription_initiation_date = "2017-03-23 21:12:01";
            $city = $this->getThisMemberCityNumber($member_id=2);
            
             header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "city_number" => $city
                                        
                                
                            ));
            
        }
        
       
        /**
         * This is the function that gets the discounted amount of a members subscription
         */
        public function getTheDiscountedAmountOfThisMemberSubscription($member_id){
            
            if($this->isDiscountPermittedForSubscriptions()){
                if($this->getThisMemberSubscriptionPeriod($member_id) >= $this->platformRequiredMinNumberOYearsForDiscount()){
                
                    $discounted_amount = $this->getTheMembersipFeeForThisType($member_id) * ($this->getTheDiscountRate() * .01);
                    return $discounted_amount;
                
                }else{
                    return 0.00;
                }
                
            }else{
                return 0.00;
                
            }
            
        }
        
        
        /**
         * This is the function that gets the discount rate
         */
        public function getTheDiscountRate(){
             $criteria = new CDbCriteria();
              $criteria->select = '*';
              //  $criteria->condition='id=:id';
              //  $criteria->params = array(':id'=>$fee_id);
                $discountable= PlatformSettings::model()->find($criteria);
                
                return $discountable['discount_rate'];
            
        }
        
        
        /**
         * This is the function that gets  the required number of years for discount to apply
         */
        public function platformRequiredMinNumberOYearsForDiscount(){
            
               $criteria = new CDbCriteria();
                $criteria->select = '*';
              //  $criteria->condition='id=:id';
              //  $criteria->params = array(':id'=>$fee_id);
                $discountable= PlatformSettings::model()->find($criteria);
                
                return $discountable['min_years_required_for_discount'];
            
        }
        
        
        /**
         * This is the function that determines if discount is allowed for subscriptions
         */
        public function isDiscountPermittedForSubscriptions(){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
              //  $criteria->condition='id=:id';
              //  $criteria->params = array(':id'=>$fee_id);
                $discountable= PlatformSettings::model()->find($criteria);
                
                return $discountable['effect_discount_for_subscription'];
            
        }
        
        
        
        /**
         * This is the function that gets the membership fee for a particular type
         */
        public function getTheMembersipFeeForThisType($member_id){
            
                //get the membership type of this member
            
              $type_id = $this->getTheMembershipTypeIdOfThisMember($member_id);
              
              //get the subscription period
              $period = $this->getThisMemberSubscriptionPeriod($member_id);
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='membership_type_id=:typeid and status=:status';
                $criteria->params = array(':typeid'=>$type_id,':status'=>'active');
                $fee= MembershiptypeHasFees::model()->find($criteria);
                
                $amount = $this->getTheFeeAmount($fee['fee_id']);
                
                return ($amount * $period) ;
            
        }
        
        
        /**
         * this is the function that retrieves a fee amount
         */
        public function getTheFeeAmount($fee_id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$fee_id);
                $amount= MembershipFee::model()->find($criteria);
                
                return $amount['amount'];
            
            
        }
        
        /**
         * This is the function that list all subscription
         */
        public function actionListAllSubscriptionPayments(){
            
             $userid = Yii::app()->user->id;
          
            $payment = SubscriptionPayment::model()->findAll();
                if($payment===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "subscription" => $payment
                                        
                                
                            ));
                       
                }
            
            
            
        }
        
        
        /**
         * This is the function that list all unverified subscriptions
         */
        public function actionListAllUnverifiedSubscriptionPayments(){
            
             $userid = Yii::app()->user->id; 
            //get the domain of the logged in user
            
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='status=:status';
                $criteria->params = array(':status'=>'unconfirmed');
                $payment= SubscriptionPayment::model()->findAll($criteria);
               
                if($payment===null) {
                    http_response_code(404);
                    $payment['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                      header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "subscription" => $payment
                                        
                                
                            ));
                       
                }
            
        }
        
        
        /**
         * This is the function that list all failed transactions
         */
        public function actionListAllFailedSubscriptionPayments(){
            
             $userid = Yii::app()->user->id; 
            //get the domain of the logged in user
            
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='status=:status';
                $criteria->params = array(':status'=>'failed');
                $payment= SubscriptionPayment::model()->findAll($criteria);
               
                if($payment===null) {
                    http_response_code(404);
                    $payment['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                      header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "subscription" => $payment
                                        
                                
                            ));
                       
                }
        }
        
        
        
        
        
         /**
         * This is the function that retreives some subscription payment details for updates
         */
        public function actionretrievePaymentDetailsForUpdate(){
            
           $member_id = $_REQUEST['member_id'];
           $id = $_POST['id'];
           $bank_number = $this->getTheBankNumberForThisPayment($_REQUEST['bank_account_id']);
            
           //get the current membership type of this member
            $membership_type_id = $this->getTheMembershipTypeIdOfThisMember($member_id);
             
            $membership_type = $this->getThisMembershipTypeName($membership_type_id);
            
            $member_name = $this->getThisUsersName($member_id);
            
            //get the membership number
            $membership_number = $this->getThisMemberMembershipNumber($member_id);
            
            $payee = $this->getTheNameOfThePayee($id);
            
            if($member_id===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                      header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "bank_number" => $bank_number,
                                     "membership_type"=>$membership_type,
                                     "payee"=>$payee,
                                     "member"=>$member_name,
                                     "membership_number"=>$membership_number
                                
                                    
                            ));
                       
                }
            
        }
        
        
        /**
         * This is the function that gets the membership number of a member
         */
        public function getThisMemberMembershipNumber($member_id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$member_id);
                $member= Members::model()->find($criteria);
                
                return $member['membership_number'];
            
        }
        
        
             
        
          /**
         * This is the function that retrieves bank's account numberr
         */
        public function getTheBankNumberForThisPayment($bank_account_id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$bank_account_id);
                $bank= Banker::model()->find($criteria);
                
                return $bank['account_number'];
        }
        
        
        
        /**
         * This is the functin that gets the name of the payee
         */
        public function getTheNameOfThePayee($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$id);
            $payee= SubscriptionPayment::model()->find($criteria);
            
            $name = $this->getThisUsersName($payee['paid_by_id']);
            return $name;           
            
        }
        
        
        /**
         * This is the function that gets a users name
         */
        public function getThisUsersName($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $user= Members::model()->find($criteria);
                
                $name = $user['firstname']." ".$user['middlename']. " ". $user['lastname'];
                
                return $name;
        }
        
        
         /**
         * This is the function that gets the membership type name
         */
        public function getTheMembershipTypeIdOfThisMember($member_id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='member_id=:id and expecting_payment=:expecting';
            $criteria->params = array(':id'=>$member_id,':expecting'=>true);
            $type= MembershipSubscription::model()->find($criteria);
            
                   
            return $type['membership_type_id'];
            
        }
        
        
        
                
         /**
         * This is the function that gets the membership number of years
         */
        public function getThisMemberSubscriptionPeriod($member_id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='member_id=:id and expecting_payment=:expecting';
            $criteria->params = array(':id'=>$member_id,':expecting'=>true);
            $years= MembershipSubscription::model()->find($criteria);
            
                   
            return $years['number_of_years'];
            
        }
        
        
        
        /**
         * This is the function that gets the membership type name
         */
        public function getThisMemberMembershipType($member_id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='member_id=:id and expecting_payment=:expecting';
            $criteria->params = array(':id'=>$member_id,':expecting'=>true);
            $type= MembershipSubscription::model()->find($criteria);
            
            $type = $this->getThisMembershipTypeName($type['membership_type_id']);
            
            return $type;
            
        }
        
        
        /**
         * This is the function that gets a membership type
         */
        public function getThisMembershipTypeName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$id);
            $type= Membershiptype::model()->find($criteria);
            return $type['name'];
            
        }
        
        
        
        /**
         * This is the function that adds new subscription payment
         */
        public function actionaddNewSubscriptionPayment(){
            
            $model = new SubscriptionPayment;
            
            $model->membership_type_id = $_POST['membership_type_id'];
            if(is_numeric($_POST['bank_account'])){
                $model->bank_account_id = $_POST['bank_account'];
            }else{
                $model->bank_account_id = $_POST['bank_account_id'];
            }
            if(is_numeric($_POST['member'])){
                $model->member_id = $_POST['member'];
            }else{
                $model->member_id = $_POST['member_id'];
            }
            $model->discounted_amount = $_POST['discounted_amount'];
            $model->amount = $_POST['amount'];
            $model->net_amount = $_POST['net_amount'];
            $model->member_id = $_POST['member_id'];
            $model->invoice_number = $_POST['invoice_number'];
            $model->status = strtolower($_POST['status']);
            $model->payment_mode = strtolower($_POST['payment_mode']);
            $model->remark = strtolower($_POST['remark']);
            $model->payment_date = date("Y-m-d H:i:s", strtotime($_POST['payment_date'])); 
            $model->paid_by_id = $this->getThePersonThatMadeThisSubscriptionPayment($model->member_id);
            
            //confirm if this order had already been paid for
            //if($this->isOrderNotAlreadyPaidFor($model->order_id)){
                
                if($model->save()){
                         // $result['success'] = 'true';
                          $msg = "Subscription Payment successfully made";
                          header('Content-Type: application/json');
                          echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Addition of subscription payment was not successful. Please contact the support team";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
                
                
                
          /**  }else{
                 //$result['success'] = 'false';
                         $msg = "Its possible that this order may had been paid for. Please confirm from the Accounting Department";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                
            }
           * 
           */
             
            
            
        }
        
        
        
        
        /**
         * This is the function that edits subscription payments 
         */
        public function actionupdateSubscriptionPayment(){
            
            $_id = $_POST['id'];
            $model=  SubscriptionPayment::model()->findByPk($_id);
            
             if(is_numeric($_POST['member'])){
                $model->member_id = $_POST['member'];
            }else{
                $model->member_id = $_POST['member_id'];
            }
            $model->membership_type_id = $_POST['membership_type_id'];
            $model->bank_account_id = $_POST['bank_account_id'];
            $model->discounted_amount = $_POST['discounted_amount'];
            $model->amount = $_POST['amount'];
            $model->net_amount = $_POST['net_amount'];
            $model->status = strtolower($_POST['status']);
            $model->invoice_number = $_POST['invoice_number'];
            $model->payment_mode = strtolower($_POST['payment_mode']);
            $model->remark = strtolower($_POST['remark']);
            $model->payment_date = date("Y-m-d H:i:s", strtotime($_POST['payment_date'])); 
            $model->paid_by_id = $_POST['paid_by_id'];
            
             if($model->save()){
                         // $result['success'] = 'true';
                          $msg = "Subscription Payment information successfully updated";
                          header('Content-Type: application/json');
                          echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Update of subscription payment information was not successful";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
            
            
        }
        
        
        /**
         * This is the function that retrieves the person that made the subscription
         */
        public function getThePersonThatMadeThisSubscriptionPayment($member_id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='member_id=:id and expecting_payment=:expecting';
                $criteria->params = array(':id'=>$member_id,':expecting'=>true);
                $member= MembershipSubscription::model()->find($criteria);
                
                return $member['member_id'];
            
        }
        
        
        
        
        /**
         * This is the list of members with awiting payment status
         */
        public function actionListAllMembersWithFreshSubscriptionPayments(){
            
               $criteria = new CDbCriteria();
                $criteria->select = '*';
                //$criteria->condition='id=:id';
                //$criteria->params = array(':id'=>$member_id);
                $members= Members::model()->findAll($criteria);
                
                $expected_members = [];
                
                foreach($members as $member){
                    if($this->isThisMemberAwaitingPaymentConfirmation($member['id'])){
                        
                        $expected_members[] = $member;
                    }
                }
            
                if($expected_members===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                      header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "member"=>$expected_members
                                
                                    
                            ));
                       
                }
            
        }
        
        
        /**
         * This is the function that retrieves the members with awaiting payment status
         */
        public function isThisMemberAwaitingPaymentConfirmation($member_id){
            
            $cmd =Yii::app()->db->createCommand();
            $cmd->select('COUNT(*)')
                    ->from('membership_subscription')
                    ->where("member_id = $member_id and expecting_payment=1");
                $result = $cmd->queryScalar();
                
                if($result > 0){
                    return true;
                }else{
                    return false;
                }
                
                   
        }
        
        
        
        
        /**
         * This is to confirm a subscription payment to the bank
         */
        public function actionconfirmThisSubscriptionPayment(){
            
            $id = $_POST['id'];
            
            $cmd =Yii::app()->db->createCommand();
            $result = $cmd->update('subscription_payment',
                         array('status'=>'confirmed',
                             'remark'=>$_POST['remark'],
                             'payment_confirmed_by'=>Yii::app()->user->id,
                             'date_of_confirmation'=>new CDbExpression('NOW()'),
                             
                        ),
                        ("id=$id")
                          
                     );
                
                if($result>0){
                    $msg = "Subscription Payment was successfully confirmed";
                          header('Content-Type: application/json');
                          echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                }else{
                    $msg = "Subscription payment could not be confirmed";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                }
            
        }
        
        
        
        
         /**
         * This is the function that fails a subscription payment transactions
         */
        public function actionfailThisSubscriptionPayment(){
          
             $id = $_POST['id'];
            
            $cmd =Yii::app()->db->createCommand();
            $result = $cmd->update('subscription_payment',
                         array('status'=>'failed',
                             'reason_for_failure'=>$_POST['remark'],
                             'payment_confirmed_by'=>Yii::app()->user->id,
                             'date_of_confirmation'=>new CDbExpression('NOW()'),
                             
                        ),
                        ("id=$id")
                          
                     );
                
                if($result>0){
                    $msg = "Subscription Payment was successfully confirmed as failed";
                          header('Content-Type: application/json');
                          echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                }else{
                    $msg = "Subscription payment could not be confirmed as failed";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                }
            
            
        }
        
	
}
