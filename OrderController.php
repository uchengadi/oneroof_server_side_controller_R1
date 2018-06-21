<?php

class OrderController extends Controller
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
				'actions'=>array('ListAllIncompleteOrders','ListAllClosedOrders','ListAllYetToBeAssignedForDeliveryOrders','ListAllOrders',
                                    'retrieveOrderDetails','AssignThisOrderForDelivery','ListProductsForAnOrder','makeThisOrderPayment',
                                    'addProductWithConstituentsToCart','addingProductToCart','producthistoryoccurrencenotexceedingsixmonths','producthistoryoccurrencebeyondsixmonths',
                                    'addingThisHamperToCart','redirectingThisHamperToPreferredLocation','makeThisOrderPaymentFromWallet','makeThisOrderPaymentOnDelivery',
                                   'makeThisOrderScheduledPayment','makeThisOrderScheduledPaymentFromWallet','makeThisOrderScheduledOnlineAndOndeliveryPayment',
                                    'makeThisOrderPaymentFromWalletAndOnDelivery'),
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
         * This is the function that list all orders on the platform
         */
        public function actionListAllOrders(){
            
             $userid = Yii::app()->user->id;
          
            $order = Order::model()->findAll();
                if($order===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "order" => $order,
                                    
                                        
                                
                            ));
                       
                }
            
      
        }
        
        
        /**
         * This is the function that list all incomplete or open orders on the platform
         */
        public function actionListAllIncompleteOrders(){
            
            $userid = Yii::app()->user->id; 
            //get the domain of the logged in user
            
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='status=:status';
                $criteria->params = array(':status'=>'open');
                $order= Order::model()->findAll($criteria);
                
                if($order===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                      header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "order" => $order
                                        
                                
                            ));
                       
                }
            
        }
	
        
        
        /**
         * This is the function that list all closed orders on the platform
         */
        public function actionListAllClosedOrders(){
            
            $userid = Yii::app()->user->id; 
            //get the domain of the logged in user
            
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='status=:status';
                $criteria->params = array(':status'=>'closed');
                $order= Order::model()->findAll($criteria);
                
                if($order===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                      header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "order" => $order
                                        
                                
                            ));
                       
                }
            
        }
        
        
         /**
         * This is the function that list all unassigned orders for delivery on the platform
         */
        public function actionListAllYetToBeAssignedForDeliveryOrders(){
            
          
               $userid = Yii::app()->user->id; 
            //get the domain of the logged in user
                        
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='status=:status';
                $criteria->params = array(':status'=>'closed');
                $orders= Order::model()->findAll($criteria);
                
                $unassigned_orders = [];
                foreach($orders as $order){
                   if($this->isOrderNotAssignedForDelivery($order['id'])){
                       
                       $unassigned_orders[] = $order;
                   } 
                }
                
                if($orders===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                      header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "order" => $unassigned_orders
                                        
                                
                            ));
                       
                }
            
        }
        
        
        
        /**
         * This is the function that determines if order is unsaaigned
         */
        public function isOrderNotAssignedForDelivery($order_id){
            
             $cmd =Yii::app()->db->createCommand();
            $cmd->select('COUNT(*)')
                    ->from('assigning_order_for_delivery')
                    ->where("order_id = $order_id");
                $result = $cmd->queryScalar();
                
                if($result == 0){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        /**
         * This is the function that retreives extra information about an order
         */
        public function actionretrieveOrderDetails(){
            
            $order_inititated_by = $_POST['order_initiated_by'];
            
            
            $requester_name = $this->getTheRequesterName($order_inititated_by);
            $city_name = $this->getThisCityName($_POST['delivery_city_id']);
            $state_name = $this->getThisStateName($_POST['delivery_state_id']);
            $country_name = $this->getThisCountryName($_POST['delivery_country_id']);
            
            //get the payment status of this order
            
            $payment_status = $this->getThePaymentStatusOfThisOrder($_REQUEST['order_id']);
            
             //get the delivery type
            
            $delivery_type = $this->getTheOrderDeliveryType($_REQUEST['order_id']);
            
             header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                       "requester"=>$requester_name,
                                       "city"=>$city_name,
                                       "state"=>$state_name,
                                       "country"=>$country_name,
                                       "payment_status"=>$payment_status,
                                       "delivery_type"=>$delivery_type
                             ));
            
        
        }
        
        
        /**
         * This is the function that retrieves the payment status of an order
         */
        public function getThePaymentStatusOfThisOrder($order_id){
            
            $model = new Payment;
            return $model->getThePaymentStatusOfThisOrder($order_id);
            
        }
        
        
        
        /**
         * This is the function that retrieves the order delivery type
         */
        public function getTheOrderDeliveryType($order_id){
            
            $model = new Payment;
            return $model->getTheOrderDeliveryType($order_id);
            
        }
        
        
             /**
         * This is the function that retrieves the name of the member
         */
        public function getTheRequesterName($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $user= Members::model()->find($criteria);
                
                $name = $user['lastname'] .  ' '. $user['middlename'] . ' ' . $user['firstname'] ;
                
                return $name;
            
        }
        
        /**
        * This is the function that retrieves the name of the city
         */
        public function getThisCityName($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $name= City::model()->find($criteria);
                
                             
                return $name['name'];
            
        }
        
        
        
         /**
        * This is the function that retrieves the name of the state
         */
        public function getThisStateName($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $name= State::model()->find($criteria);
                
                             
                return $name['name'];
            
        }
        
         /**
        * This is the function that retrieves the name of the country
         */
        public function getThisCountryName($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $name= Country::model()->find($criteria);
                
                             
                return $name['name'];
            
        }
        
        /**
         * This is the function that assigns orders for delivery
         */
        public function actionAssignThisOrderForDelivery(){
            
            //assigning this order to the courier offier
            if($this->isOrderAssignmentToCourierOfficerSucessful($_POST['id'],$_POST['courier_officer'])){
                //open up a customer response form
                if($this->isCustomerResponseFormNotYetOpened($_POST['id'])){
                    $msg = "This order had been assigned to a courier officer and the customer response form is also opened";
                    header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                       "msg"=>"$msg",
                                     
                             ));
                    
                }else{
                     $msg = "This order had been assigned to a courier officer but the customer response form was not opened";
                    header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                       "msg"=>"$msg",
                                     
                             ));
                }
            }else{
                 $msg = "The assignment of this order to a courier officer was not successful. Please contact the support tean urgently";
                    header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                       "msg"=>"$msg",
                                     
                             ));
            }
            
            
        }
        
        
        
        /**
         * This is the function that determines if order assignment to a courier officer is successful
         */
        public function isOrderAssignmentToCourierOfficerSucessful($order_id,$courier_officer){
            if($this->isOrderNotAssignedForDelivery($order_id)){
               
                 $cmd =Yii::app()->db->createCommand();
                 $result = $cmd->insert('assigning_order_for_delivery',
                         array('member_id'=>$courier_officer,
                                'order_id' =>$order_id,
                                 'status'=>'pending',
                                 'order_assigned_by'=>Yii::app()->user->id,
                                 'order_assigned_to'=>$courier_officer,
                                 'date_of_assignment'=>new CDbExpression('NOW()')
                           
                        )
                          
                     );
                 
                 if($result >0){
                     return true;
                 }else{
                     return false;
                 }
                
                
            }else{
                return false;
            }  
            
          
            
            
        }
        
        
        /**
         * This is the function that confirms if a customer response form had been opened
         */
        public function isCustomerResponseFormNotYetOpened($order_id){
            
           if($this->isCustomerOrderResponseFormNotOpened($order_id)){
               $cmd =Yii::app()->db->createCommand();
                 $result = $cmd->insert('order_delivery',
                         array('order_id'=>$order_id,
                               'status'=>'unconfirmed',
                           
                        )
                          
                     );
               
           }else{
               return false;
           }
        }
        
        
        /**
         * This is the function that confirms that response form is already opened
         */
        public function isCustomerOrderResponseFormNotOpened($order_id){
            
           $cmd =Yii::app()->db->createCommand();
            $cmd->select('COUNT(*)')
                    ->from('order_delivery')
                    ->where("order_id = $order_id");
                $result = $cmd->queryScalar();
                
                if($result == 0){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        /**
         * Thjis is the function  that list all products in an order
         */
        public function actionListProductsForAnOrder(){
           $order_id = $_REQUEST['order_id'];
            //$order_id = 1;
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='order_id=:orderid';
                $criteria->params = array(':orderid'=>$order_id);
                $order= OrderHasProducts::model()->findAll($criteria);
                
                if($order===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                      header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "order" => $order
                                        
                                
                            ));
                       
                }
            
            
            
            
        }
        
        
        /**
         * This is the function that effects the purchase payments from the cart
         */
        public function actionmakeThisOrderPayment(){
            
            $_id = $_POST['order_id'];
                  
            $model=Order::model()->findByPk($_id);
            
         /**   if($_POST['address'] == 'primary'){
                $model->delivery_address1 = $_POST['address1'];
                $model->delivery_address2 = $_POST['address2'];
                $model->delivery_country_id = $_POST['country_id'];
                $model->delivery_state_id = $_POST['state_id'];
                $model->delivery_city_id = $_POST['city_id'];
                $model->person_in_care_of =$_POST['primary_reciever_name'];
                $model->delivery_mobile_number =$_POST['primary_reciever_mobile_number'];
                $model->address_landmark =$_POST['primary_address_landmark'];
                $model->nearest_bus_stop =$_POST['primary_address_nearest_bus_stop'];
            }else if($_POST['address'] == 'permanent'){
                $model->delivery_address1 = $_POST['delivery_address1'];
                $model->delivery_address2 = $_POST['delivery_address2'];
                $model->delivery_country_id = $_POST['delivery_country_id'];
                $model->delivery_state_id = $_POST['delivery_state_id'];
                $model->delivery_city_id = $_POST['delivery_city_id'];
                $model->person_in_care_of =$_POST['permanent_reciever_name'];
                $model->delivery_mobile_number =$_POST['permanent_reciever_mobile_number'];
                $model->address_landmark =$_POST['permanent_address_landmark'];
                $model->nearest_bus_stop =$_POST['permanent_address_nearest_bus_stop'];
            }else if($_POST['address']== 'corporate'){
                $model->delivery_address1 = $_POST['corporate_address1'];
                $model->delivery_address2 = $_POST['corporate_address2'];
                $model->delivery_country_id = $_POST['corporate_country_id'];
                $model->delivery_state_id = $_POST['corporate_state_id'];
                $model->delivery_city_id = $_POST['corporate_city_id'];
                $model->person_in_care_of =$_POST['corporate_reciever_name'];
                $model->delivery_mobile_number =$_POST['corporate_reciever_mobile_number'];
                $model->address_landmark =$_POST['corporate_address_landmark'];
                $model->nearest_bus_stop =$_POST['corporate_address_nearest_bus_stop'];
            }else if($_POST['address']== 'special'){
                $model->delivery_address1 = $_POST['order_address1'];
                $model->delivery_address2 = $_POST['order_address2'];
                $model->delivery_country_id = $_POST['order_country'];
                $model->delivery_state_id = $_POST['order_state'];
                $model->delivery_city_id = $_POST['order_city'];
                $model->person_in_care_of =$_POST['special_reciever_name'];
                $model->delivery_mobile_number =$_POST['special_reciever_mobile_number'];
                $model->address_landmark =$_POST['order_address_landmark'];
                $model->nearest_bus_stop =$_POST['order_address_nearest_bus_stop'];
            }   
          * 
          */
            $member_id = $_POST['id'];
            $model->is_term_acceptable = $_POST['is_term_acceptable'];
            
            if(isset($_POST['escrow_charge_for_computation'])){
                $escrow_charges = $_POST['escrow_charge_for_computation'];
            }else{
               $escrow_charges = 0; 
            }
            
            if($this->isOrderPaymentNotAlreadyEffected($_id)){
                
                if($model->save()) {
                        
                          if($this->isThisOrderPaymentSuccessful($_id,$_POST['cart_amount_for_computation'],$_POST['delivery_charges_for_computation'],$escrow_charges,$_POST['delivery'],$member_id,$payment_mode='online',$remark='Online purchase payment')){
                              
                               if($this->isRemovalOfConstituentsAmendmentsByThisUserSuccessful($member_id)){
                                   
                                    //get the invoice number of this payment    
                              $invoice_number = $this->getTheInvoiceNumberOfThisPayment($_id);
                              
                              $email = $this->getThisMemberRegisteredEmailAddress($member_id);
                              
                              $membership_number = $this->getTheMembershipNumberOfThisMember($member_id);
                              
                              //get the order number of this order
                              $order_number  = $this->getTheOrderNumberOfThisOrder($_id);
                              
                              if($this->isOrderClosed($_id)){
                                   // $result['success'] = 'true';
                                    $msg = "Using your '$membership_number' membership number and '$invoice_number' invoice number, effect the online payment on the redirected payment platform";
                                    header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                        "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                         "invoice_number"=>$invoice_number,
                                         "membership_number"=>$membership_number,
                                          "order_number"=> $order_number,
                                           "email"=>$email,
                                            "amount"=>$_POST['cart_amount_for_computation'] + $_POST['delivery_charges_for_computation']
                                                )
                                    );
                              }else{
                                   $this->sendAnEmailToTheHelpDesk($order_id,$membership_number);
                                  
                              }
                              
                                   
                               }else{
                                    //get the invoice number of this payment    
                              $invoice_number = $this->getTheInvoiceNumberOfThisPayment($_id);
                              
                              $email = $this->getThisMemberRegisteredEmailAddress($member_id);
                              
                              $membership_number = $this->getTheMembershipNumberOfThisMember($member_id);
                              
                                //get the order number of this order
                              $order_number  = $this->getTheOrderNumberOfThisOrder($_id);
                              
                              if($this->isOrderClosed($_id)){
                                   // $result['success'] = 'true';
                                    $msg = "Using your '$membership_number' membership number and '$invoice_number' invoice number, effect the online payment on the redirected payment platform";
                                    header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                        "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                         "invoice_number"=>$invoice_number,
                                         "membership_number"=>$membership_number,
                                          "order_number"=> $order_number,
                                          "email"=>$email,
                                           "amount"=>$_POST['cart_amount_for_computation'] + $_POST['delivery_charges_for_computation']  
                                                )
                                    );
                              }else{
                                   $this->sendAnEmailToTheHelpDesk($order_id,$membership_number);
                                  
                              }
                              
                                   
                               }
                             
                              
                                 } else {
                                     $msg = 'payment for this transaction was not successful. Please contact the Customer Service Help Desk';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                                     
                                     
                                 } 
                                  
                                          
                     }else {
                         //$result['success'] = 'false';
                         $msg = 'payment for this transaction was not successful. Please contact the Customer Service Help Desk';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                     }
                
                
            }else{
                $msg = 'payment for this transaction had already been effeced before. Please contact the Customer Service Help Desk for assistance';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                     
            }
               
                        
                 
            
        }
        
        
        /**
         * This is the function that retrieves a member's email address
         */
        public function getThisMemberRegisteredEmailAddress($member_id){
            $model = new Members;
            return $model->getTheRegisteredEmailOfThisMember($member_id);
        }
        
        /**
         * This is the function that gets the order number of an order
         */
        public function getTheOrderNumberOfThisOrder($order_id){
            $model = new Order;
            return $model->getTheOrderNumberOfThisOrder($order_id);
        }
        
        
        /**
         * This is the function that verifies if payment of an order had already been effected
         */
        public function isOrderPaymentNotAlreadyEffected($order_id){
            $model = new Payment;
            
            return $model->isOrderPaymentNotAlreadyEffected($order_id);
            
        }
      
        
        /**
         * This is the function that gets the invoice number of an order payment
         */
        public function getTheInvoiceNumberOfThisPayment($order_id){
            $model = new Payment;
            
            return $model->getTheInvoiceNumberOfThisPayment($order_id);
        }
        
        
        /**
         * This is the function that gets the membership number of a member
         */
        public function getTheMembershipNumberOfThisMember($member_id){
            $model = new Members;
            
            return $model->getTheMembershipNumberOfThisMember($member_id);
        }
        
        
        
        /**
         * This is the function that effects order payment
         */
        public function isThisOrderPaymentSuccessful($order_id,$cart_amount_for_computation,$delivery_charges_for_computation,$escrow_charges,$delivery_type,$member_id,$payment_mode,$remark){
            $model = new Payment;
            
            return $model->isThisOrderPaymentSuccessful($order_id,$cart_amount_for_computation,$delivery_charges_for_computation,$escrow_charges,$delivery_type,$member_id,$payment_mode,$remark);
            
        }
        
        
        /**
         * This is the function that sends an email to the help desk 
         */
        public function sendAnEmailToTheHelpDesk($order_id,$membership_number){
            
            
        }
        
        
        /**
         * This is the function that confirms if an order is closed or stioll open
         */
        public function isOrderClosed($_id){
            $model = new Order;
            return $model->isOrderClosed($_id);
        }
        
        
        /**
         * This is the function that adds a product to a cart 
         */
        public function actionaddingProductToCart(){
            $model = new Order;
            
            $user_id = Yii::app()->user->id;
            
            $product_id = $_POST['product_id'];
            
              //get this product name
            $product_name = $this->getThisProductName($product_id);
            
            if($model->isMemberWithOpenOrder($user_id)){
                $order_id = $model->getTheOpenOrderInitiatedByMember($user_id);
            }else{
                $order_id = $model->createNewOrderForThisMember($user_id);
            }
            $is_mainstore = $_POST['is_mainstore'];
            
        if($_REQUEST['decision'] != 'faas'){
            $quantity_of_purchase = $_POST['quantity_of_purchase'];
            $amount_saved_on_purchase = $_POST['amount_save_on_purchase'];
            $prevailing_retail_selling_price = $_POST['prevailing_retail_selling_price'];
            $cobuy_member_selling_price = $_POST['per_portion_price'];
            $start_price_validity_period = date("Y-m-d H:i:s", strtotime($_POST['start_price_validity_period'])); 
            $end_price_validity_period = date("Y-m-d H:i:s", strtotime($_POST['end_price_validity_period'])); 
            $is_escrow_only = $_POST['is_escrow_only'];
            $is_quote_only = $_POST['is_quote_only'];
            $is_quote_and_escrow = $_POST['is_quote_and_escrow'];
            $is_presubscription = $_POST['is_presubscription'];
            $is_presubscription_and_drawdown = $_POST['is_presubscription_and_drawdown'];
            $is_presubscription_and_escrow = $_POST['is_presubscription_and_escrow'];
            $is_postsubscription = $_POST['is_postsubscription'];
            $is_postsubscription_and_escrow = $_POST['is_postsubscription_and_escrow'];
            $is_hamper = $_POST['is_hamper'];
            
            $is_escrow_accepted = 0;
            $is_for_presubscription = $_POST['is_for_presubscription'];
            $is_for_presubscription_topup = $_POST['is_for_presubscription_topup'];
            $subscription_type = $_POST['subscription_type'];
            if(isset($_POST['future_trading'])){
                $future_trading = $_POST['future_trading'];
            }else{
                $future_trading =0;
            }
            if(isset($_POST['month_of_delivery'])){
                $month_of_delivery = $_POST['month_of_delivery'];
            }else{
                $month_of_delivery = "";
            }
            if(isset($_POST['year_of_delivery'])){
                $year_of_delivery = $_POST['year_of_delivery'];
            }else{
                $year_of_delivery = "";
            }
            if(isset($_POST['initial_payment_rate'])){
                $initial_payment_rate = $_POST['initial_payment_rate'];
            }else{
                $initial_payment_rate = 0;
            }
            if(isset($_POST['payment_frequency'])){
                $payment_frequency = $_POST['payment_frequency'];
            }else{
                $payment_frequency = "";
            }
            
            $decision = $_POST['decision'];
            $monthly_paas_subscription_cost= $_POST['monthly_paas_subscription_cost'];
            $minimum_quantity_for_paas_subscription= $_POST['minimum_quantity_for_paas_subscription'];
            $maximum_quantity_for_paas_subscription= $_POST['maximum_quantity_for_paas_subscription'];
            $rent_cost_per_day= $_POST['rent_cost_per_day'];
            $maximum_rent_quantity_per_cycle= $_POST['maximum_rent_quantity_per_cycle'];
            $minimum_rent_duration= $_POST['minimum_rent_duration'];
            $minimum_rent_quantity_per_cycle= $_POST['minimum_rent_quantity_per_cycle'];
            $actual_rent_duration= $_POST['rent_duration'];
            $actual_rent_quantity= $_POST['quantity_for_rent'];
            $paas_product_quantity= $_POST['paas_product_quantity'];
            $minimum_paas_duration= $_POST['minimum_paas_duration'];
            $maximum_paas_duration= $_POST['maximum_paas_duration'];
            $actual_paas_duration= $_POST['actual_paas_duration'];
                
         
            if($this->isThisOrderSuccessfullyAddedToCart($order_id,$product_id,$quantity_of_purchase,$amount_saved_on_purchase,$prevailing_retail_selling_price,$cobuy_member_selling_price,$start_price_validity_period,$end_price_validity_period,$is_escrow_only,$is_escrow_accepted,$is_quote_only,$is_quote_and_escrow,$is_presubscription,$is_presubscription_and_escrow,$is_presubscription_and_drawdown,$is_postsubscription,$is_postsubscription_and_escrow,$is_hamper,$is_mainstore,$is_for_presubscription_topup,$future_trading,$month_of_delivery,$year_of_delivery,$initial_payment_rate,$payment_frequency,$decision,$monthly_paas_subscription_cost,$minimum_quantity_for_paas_subscription,$maximum_quantity_for_paas_subscription,$rent_cost_per_day,$maximum_rent_quantity_per_cycle,$minimum_rent_duration,$minimum_rent_quantity_per_cycle,$actual_rent_duration,$actual_rent_quantity,$paas_product_quantity,$minimum_paas_duration,$maximum_paas_duration,$actual_paas_duration)){
                 
                if($is_for_presubscription_topup == 0){
                    
                  if($is_for_presubscription == 1){
                    if($this->isTheSuscriptionOfProductToMemberASuccess($user_id,$product_id,$subscription_type,$quantity_of_purchase)){
                        $msg = "'$product_name' product is successfully added to cart and the subscription information also updated";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            'success' => mysql_errno() == 0,
                                            'msg' => $msg,
                                            'is_for_presubscription'=>$is_for_presubscription,
                                         'is_for_presubscription_topup'=>$is_for_presubscription_topup,
                                           
                                         )
                                        );
                    }else{
                       $msg ="'$product_name' product is successfully added to cart but the subscription table was not updated. Please contact customer care for sssistance";
                                     //header('Content-Type: application/json');
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            'success'=>mysql_errno()==0,
                                            //'msg'=>$msg,
                                            "msg"=> $msg,
                                         
                                            
                                         )
                                        );
                      
                        
                    }
                    
                }else{
                  $msg ="'$product_name' product is successfully added to cart";
                                    // header('Content-Type: application/json');
                                    header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                     'success'=>mysql_errno()==0,
                                            //'msg'=>$msg
                                            "msg"=>$msg,
                                            
                                         
                                          
                                        ));
                                     
                }
                    
                    
                }else{
                    if($this->isProductSubscriptionTopupSuccessful($user_id,$product_id,$quantity_of_purchase,$subscription_type)){
                        $msg = "'$product_name' product quantity is successfully topped up. but you need to consummate the transaction on your cart";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg,
                                            "is_for_presubscription"=>$is_for_presubscription,
                                          "is_for_presubscription_topup"=>$is_for_presubscription_topup,
                                         )
                                        );
                        
                    }
                    
                }
                
                
            }else{
                 $msg = "'$product_name' product could not be added to cart. Possible it is already in the cart. Check your cart or contact the customer service desk for assistance";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg,
                                            
                                         )
                                        );
            }
            }else{
                
                $decision = $_POST['decision'];
                $monthly_faas_subscription_cost= $_POST['monthly_faas_subscription_cost'];
                $minimum_quantity_for_faas_subscription= $_POST['minimum_quantity_for_faas_subscription'];
                $maximum_quantity_for_faas_subscription= $_POST['maximum_quantity_for_faas_subscription'];
                $faas_product_quantity= $_POST['faas_product_quantity'];
                $minimum_faas_duration= $_POST['minimum_faas_duration'];
                $maximum_faas_duration= $_POST['maximum_faas_duration'];
                $actual_faas_duration= $_POST['actual_faas_duration'];
                
                
                if($this->isThisFaasOrderSuccessfullyAddedToCart($order_id,$product_id,$decision,$monthly_faas_subscription_cost,$minimum_quantity_for_faas_subscription,$maximum_quantity_for_faas_subscription,$faas_product_quantity,$minimum_faas_duration,$maximum_faas_duration,$actual_faas_duration,$is_mainstore)){
                     $msg ="'$product_name' product is successfully added to cart";
                                    // header('Content-Type: application/json');
                                    header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                     'success'=>mysql_errno()==0,
                                            //'msg'=>$msg
                                            "msg"=>$msg,
                                            
                                         
                                          
                                        ));
                    
                }else{
                     $msg = "'$product_name' product could not be added to cart. Possible it is already in the cart. Check your cart or contact the customer service desk for assistance";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg,
                                            
                                         )
                                        );
                    
                }
                
                
               
            }
            
                     
            
            
            
        }
        
        
        /**
         * This is the function that adds faas products to cart
         */
        public function isThisFaasOrderSuccessfullyAddedToCart($order_id,$product_id,$decision,$monthly_faas_subscription_cost,$minimum_quantity_for_faas_subscription,$maximum_quantity_for_faas_subscription,$faas_product_quantity,$minimum_faas_duration,$maximum_faas_duration,$actual_faas_duration,$is_mainstore){
            $model = new OrderHasProducts;
            return $model->isThisFaasOrderSuccessfullyAddedToCart($order_id,$product_id,$decision,$monthly_faas_subscription_cost,$minimum_quantity_for_faas_subscription,$maximum_quantity_for_faas_subscription,$faas_product_quantity,$minimum_faas_duration,$maximum_faas_duration,$actual_faas_duration,$is_mainstore);
        }
        
        
        /**
         * This is the product that top up a pre subscription
         */
        public function isProductSubscriptionTopupSuccessful($user_id,$product_id,$quantity_of_purchase,$subscription_type){
            
            $model = new MemberSubscribedToProducts;
            return $model->isProductSubscriptionTopupSuccessful($user_id,$product_id,$quantity_of_purchase,$subscription_type);
        }
        
        /**
         * This is the function that updates the subscription table after the product had been added to cart
         */
        public function isTheSuscriptionOfProductToMemberASuccess($user_id,$product_id,$subscription_type,$quantity_of_purchase){
            $model = new MemberSubscribedToProducts;
            return $model->isTheSuscriptionOfProductToMemberASuccess($user_id,$product_id,$subscription_type,$quantity_of_purchase);
        }
       
        
        /**
         * This is the function that gets a products name
         */
        public function getThisProductName($product_id){
            $model = new Product;
            return $model->getThisProductName($product_id);
        }
        
        
        /**
         * This is the function that determines if an order was successfully added to cart
         */
        public function isThisOrderSuccessfullyAddedToCart($order_id,$product_id,$quantity_of_purchase,$amount_saved_on_purchase,$prevailing_retail_selling_price,$cobuy_member_selling_price,$start_price_validity_period,$end_price_validity_period,$is_escrow_only,$is_escrow_accepted,$is_quote_only,$is_quote_and_escrow,$is_presubscription,$is_presubscription_and_escrow,$is_presubscription_and_drawdown,$is_postsubscription,$is_postsubscription_and_escrow,$is_hamper,$is_mainstore,$is_for_presubscription_topup,$future_trading,$month_of_delivery,$year_of_delivery,$initial_payment_rate,$payment_frequency,$decision,$monthly_paas_subscription_cost,$minimum_quantity_for_paas_subscription,$maximum_quantity_for_paas_subscription,$rent_cost_per_day,$maximum_rent_quantity_per_cycle,$minimum_rent_duration,$minimum_rent_quantity_per_cycle,$actual_rent_duration,$actual_rent_quantity,$paas_product_quantity,$minimum_paas_duration,$maximum_paas_duration,$actual_paas_duration){
            
            $model = new OrderHasProducts;
            return $model->isThisOrderSuccessfullyAddedToCart($order_id,$product_id,$quantity_of_purchase,$amount_saved_on_purchase,$prevailing_retail_selling_price,$cobuy_member_selling_price,$start_price_validity_period,$end_price_validity_period,$is_escrow_only,$is_escrow_accepted,$is_quote_only,$is_quote_and_escrow,$is_presubscription,$is_presubscription_and_escrow,$is_presubscription_and_drawdown,$is_postsubscription,$is_postsubscription_and_escrow,$is_hamper,$is_mainstore,$is_for_presubscription_topup,$future_trading,$month_of_delivery,$year_of_delivery,$initial_payment_rate,$payment_frequency,$decision,$monthly_paas_subscription_cost,$minimum_quantity_for_paas_subscription,$maximum_quantity_for_paas_subscription,$rent_cost_per_day,$maximum_rent_quantity_per_cycle,$minimum_rent_duration,$minimum_rent_quantity_per_cycle,$actual_rent_duration,$actual_rent_quantity,$paas_product_quantity,$minimum_paas_duration,$maximum_paas_duration,$actual_paas_duration);
            
        }
        
        /**
         * This is the function that adds a product with constituents to a cart
         */
        public function actionaddProductWithConstituentsToCart(){
            
            $model = new Order;

            $user_id = Yii::app()->user->id;

            $product_id = $_POST['product_id'];
            $quantity_of_purchase = $_POST['quantity_of_purchase'];
            $amount_saved_on_purchase = $_POST['amount_save_on_purchase'];
            $prevailing_retail_selling_price = $_POST['prevailing_retail_selling_price'];
            $cobuy_member_selling_price = $_POST['per_portion_price'];
            $start_price_validity_period = date("Y-m-d H:i:s", strtotime($_POST['start_price_validity_period'])); 
            $end_price_validity_period = date("Y-m-d H:i:s", strtotime($_POST['end_price_validity_period'])); 
             $is_escrow_only = $_POST['is_escrow_only'];
            $is_quote_only = $_POST['is_quote_only'];
            $is_quote_and_escrow = $_POST['is_quote_and_escrow'];
            $is_presubscription = $_POST['is_presubscription'];
            $is_presubscription_and_drawdown = $_POST['is_presubscription_and_drawdown'];
            $is_presubscription_and_escrow = $_POST['is_presubscription_and_escrow'];
            $is_postsubscription = $_POST['is_postsubscription'];
            $is_postsubscription_and_escrow = $_POST['is_postsubscription_and_escrow'];
            $is_hamper = $_POST['is_hamper'];
            $is_mainstore = $_POST['is_mainstore'];
            $is_escrow_accepted = 0;
            if(isset($_POST['is_for_presubscription_topup'])){
                $is_for_presubscription_topup = $_POST['is_for_presubscription_topup'];
            }
            $future_trading = $_POST['future_trading'];
            $month_of_delivery = $_POST['month_of_delivery'];
            $year_of_delivery = $_POST['year_of_delivery'];
            $initial_payment_rate = $_POST['initial_payment_rate'];
            $payment_frequency = $_POST['payment_frequency'];
            
            //get this product name
            $product_name = $this->getThisProductName($product_id);
            
            if($model->isMemberWithOpenOrder($user_id)){
                $order_id = $model->getTheOpenOrderInitiatedByMember($user_id);
            }else{
                $order_id = $model->createNewOrderForThisMember($user_id);
               
            }
            
            if($this->isThisProductWithConstituentOrderSuccessfullyAddedToCart($order_id,$product_id,$quantity_of_purchase,$amount_saved_on_purchase,$prevailing_retail_selling_price,$cobuy_member_selling_price,$start_price_validity_period,$end_price_validity_period,$is_escrow_only,$is_escrow_accepted,$is_quote_only,$is_quote_and_escrow,$is_presubscription,$is_presubscription_and_escrow,$is_presubscription_and_drawdown,$is_postsubscription,$is_postsubscription_and_escrow,$is_hamper,$is_mainstore,$is_for_presubscription_topup,$future_trading,$month_of_delivery,$year_of_delivery,$initial_payment_rate,$payment_frequency)){
                            
                     $msg = "'$product_name' product is successfully added to cart";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                        );
                    
                
            }else{
                 $msg = "'$product_name' product could not be added to cart. Possible it is already in the cart. Check your cart or contact the customer service desk for assistance";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
            }
            
            
    }
    
    
    /**
     * This is the function that ensures that every constituents amendments are remove from the database
     */
    public function isRemovalOfConstituentsAmendmentsByThisUserSuccessful($member_id){
        
        $model =new MemberAmendedConstituents;
        return $model->isRemovalOfConstituentsAmendmentsByThisUserSuccessful($member_id);
    }
    
    
    /**
     * This is the function that confirms the addition of a products and its constituents to the cart
     */
    public function isThisProductWithConstituentOrderSuccessfullyAddedToCart($order_id,$product_id,$quantity_of_purchase,$amount_saved_on_purchase,$prevailing_retail_selling_price,$cobuy_member_selling_price,$start_price_validity_period,$end_price_validity_period,$is_escrow_only,$is_escrow_accepted,$is_quote_only,$is_quote_and_escrow,$is_presubscription,$is_presubscription_and_escrow,$is_presubscription_and_drawdown,$is_postsubscription,$is_postsubscription_and_escrow,$is_hamper,$is_mainstore,$is_for_presubscription_topup,$future_trading,$month_of_delivery,$year_of_delivery,$initial_payment_rate,$payment_frequency){
        
        $model = new OrderHasProducts;
        
        return $model->isThisProductWithConstituentOrderSuccessfullyAddedToCart($order_id,$product_id,$quantity_of_purchase,$amount_saved_on_purchase,$prevailing_retail_selling_price,$cobuy_member_selling_price,$start_price_validity_period,$end_price_validity_period,$is_escrow_only,$is_escrow_accepted,$is_quote_only,$is_quote_and_escrow,$is_presubscription,$is_presubscription_and_escrow,$is_presubscription_and_drawdown,$is_postsubscription,$is_postsubscription_and_escrow,$is_hamper,$is_mainstore,$is_for_presubscription_topup,$future_trading,$month_of_delivery,$year_of_delivery,$initial_payment_rate,$payment_frequency);
        
    }
    
    
    /**
     * This is the function that retrieves the last six months product transaction  history for user
     */
    public function actionproducthistoryoccurrencenotexceedingsixmonths(){
        $model = new Order;
        
        $member_id = Yii::app()->user->id;
        $product_id = $_REQUEST['product_id'];
        
        $last_six_months_orders = $model->getAllMemberOrdersNotExceedingSixMonths($member_id);
        
        $this_product_orders = [];
        $this_member_orders = [];
        
       $criteria = new CDbCriteria();
       $criteria->select = '*';
       $criteria->condition='product_id=:productid';
       $criteria->params = array(':productid'=>$product_id);
       $memberorders= OrderHasProducts::model()->findAll($criteria);
       
       foreach($memberorders as $memorder){
           if(in_array($memorder['order_id'],$last_six_months_orders)){
             $criteria = new CDbCriteria();
             $criteria->select = '*';
             $criteria->condition='order_id=:orderid and product_id=:productid';
             $criteria->params = array(':orderid'=>$memorder['order_id'],':productid'=>$product_id);
             $memprod= OrderHasProducts::model()->find($criteria);
             
             $this_product_orders[] = $memprod;
           }
       }
        
        header('Content-Type: application/json');
        echo CJSON::encode(array(
           "success" => mysql_errno() == 0,
            "order" => $this_product_orders,
           "all_otders"=>$last_six_months_orders
                ));
    }
    
    
    
    /**
     * This is the function that retrieves the beyond six  months product transaction  history for user
     */
    public function actionproducthistoryoccurrencebeyondsixmonths(){
        
        $model = new Order;
        
        $member_id = Yii::app()->user->id;
        $product_id = $_REQUEST['product_id'];
        
        $beyond_six_months_orders = $model->getAllMemberOrdersBeyondSixMonths($member_id);
        
        $this_product_orders = [];
        $this_member_orders = [];
        
       $criteria = new CDbCriteria();
       $criteria->select = '*';
       $criteria->condition='product_id=:productid';
       $criteria->params = array(':productid'=>$product_id);
       $memberorders= OrderHasProducts::model()->findAll($criteria);
       
       foreach($memberorders as $memorder){
           if(in_array($memorder['order_id'],$beyond_six_months_orders)){
             $criteria = new CDbCriteria();
             $criteria->select = '*';
             $criteria->condition='order_id=:orderid and product_id=:productid';
             $criteria->params = array(':orderid'=>$memorder['order_id'],':productid'=>$product_id);
             $memprod= OrderHasProducts::model()->find($criteria);
             
             $this_product_orders[] = $memprod;
           }
       }
        
        header('Content-Type: application/json');
        echo CJSON::encode(array(
           "success" => mysql_errno() == 0,
            "order" => $this_product_orders,
           "all_otders"=>$beyond_six_months_orders
                ));
        
    }
    
    
    /**
     * This is the function that adds hamper to cart
     */
    public function actionaddingThisHamperToCart(){
        
        
            $model = new Order;
            
            $user_id = Yii::app()->user->id;
            
            $hamper_id = $_POST['hamper_id'];
            $quantity_of_purchase = $_POST['total_number_of_items_for_delivery'];
            $prevailing_retail_selling_price = $_POST['cost_per_hamper_for_computation'];
            $cobuy_member_selling_price = $_POST['cost_per_hamper_for_computation'];
            $is_hamper = 1;
            $hamper_terms_and_condition = $_POST['terms_and_conditions'];
            //$is_escrowed = 0;
           // $is_escrow_accepted = 0;
          
                     
            //get this hamper label name
            $hamper_label = $_POST['hamper_label'];
            
            if($model->isMemberWithOpenOrder($user_id)){
                $order_id = $model->getTheOpenOrderInitiatedByMember($user_id);
            }else{
                $order_id = $model->createNewOrderForThisMember($user_id);
            }
            
            if($this->isProductNotAlreadyInTheCart($order_id,$hamper_id)){
                
                if($this->isThisHamperOrderSuccessfullyAddedToCart($order_id,$hamper_id,$quantity_of_purchase,$prevailing_retail_selling_price,$cobuy_member_selling_price,$is_hamper,$hamper_terms_and_condition)){
                 
                     $msg = "'$hamper_label' hamper is successfully added to cart";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg,
                                         )
                                      );
                        
                
            }else{
                 $msg = "'$hamper_label' hamper could not be added to cart. Possible it is already in the cart. Check your cart or contact the customer service desk for assistance";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
            }
                
            }else{
                 $msg = "'$hamper_label' hamper is already in your cart.";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                
            }
            
            
        
        
    }
    
    /**
     * This is the function that confirms if a hamper is already in the cart
     */
    public function isThisHamperOrderSuccessfullyAddedToCart($order_id,$hamper_id,$quantity_of_purchase,$prevailing_retail_selling_price,$cobuy_member_selling_price,$is_hamper,$hamper_terms_and_condition){
        $model = new OrderHasProducts;
        return $model->isThisHamperOrderSuccessfullyAddedToCart($order_id,$hamper_id,$quantity_of_purchase,$prevailing_retail_selling_price,$cobuy_member_selling_price,$is_hamper,$hamper_terms_and_condition);
    }
    
    
    /**
     * This is the function that confirms if a hamper is already in the cart
     */
    public function isProductNotAlreadyInTheCart($order_id,$product_id){
        $model = new OrderHasProducts;
        return $model->isProductNotAlreadyInTheCart($order_id,$product_id);
    }
    
    
    /**
     * This is the function that redirects a hamper to another location
     */
    public function actionredirectingThisHamperToPreferredLocation(){
        $model = new Members;
        
        $member_id = Yii::app()->user->id;
        
        $hamper_id = $_POST['hamper_id'];
        $hamper_label = $_POST['hamper_label'];
        $number_of_hampers_delivered = $_POST['number_of_hampers_delivered'];
        $beneficiary_id = $_POST['beneficiary_id'];
        $delivery_type = $_POST['delivery_type'];
        $terms_and_conditions = $_POST['terms_and_conditions'];
        //get the initial number of hampers for this beneficiary
        $initial_number_of_hampers_for_delivery = $this->getTheNumberOfHampersForDelivery($hamper_id,$beneficiary_id);
        
        //get the redirect invoice number
        $invoice_number = $this->generateTheHamperRedirectInvoiceNumber($hamper_id,$beneficiary_id);
        
        
        if(isset($_POST['scheduled_delivery_address'])){
            $delivery_preference = $_POST['scheduled_delivery_address'];
        }else{
           $delivery_preference = 0; 
        }
        
        
                
        if($delivery_preference == 1){
            $delivery_country = $_POST['country'];
            $delivery_state = $_POST['state'];
            $delivery_city = $_POST['city'];
            $place_of_delivery = $_POST['place_of_delivery'];
     
        }else{
            if($_POST['delivery_address_option'] == strtolower('registered_address')){
                $delivery_country = $model->getThisMemberPrimaryCountryId($member_id);
                $delivery_state = $model->getThisMemberPrimaryStateId($member_id);
                $delivery_city = $model->getThisMemberPrimaryCityId($member_id);
                $place_of_delivery = $model->getThisMemberPrimaryAddess($member_id);
            }else{
                $delivery_country = $_POST['new_delivery_country'];
                $delivery_state = $_POST['new_delivery_state'];
                $delivery_city = $_POST['new_delivery_city'];
                $place_of_delivery = $_POST['new_delivery_address'];
                
            }
            
        }
        //required delivery charges
         $delivery_charges = $this->getTheCostOfHamperDeliveryToThisCity($hamper_id,$delivery_city,$delivery_type);
        //initial delivery type 
         $initial_delivery_type = $this->getTheInitaiDeliveryTypeOfThisHamper($hamper_id,$beneficiary_id);
        
        if($this->isTheRedirectionOfThisHamperASuccess($hamper_id,$beneficiary_id,$delivery_type,$delivery_country,$delivery_state,$delivery_city,$place_of_delivery,$delivery_charges)){
            
           if($delivery_preference == 1){
               if(($initial_delivery_type == $delivery_type)){
                   $msg = "You just redirected this '$hamper_label' hamper to the same scheduled delivery address. No charges is incurred as the delivery type remains the same";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg,
                                            "value"=>0,
                                            "invoice_number"=>$invoice_number,
                                            
                                             )
                                        );
               }else{
                   //$msg = "You just redirected this '$hamper_label' hamper to the same scheduled delivery address . No charges is incurred as the delivery type remains the same";
                           header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "value" => $delivery_charges,
                                            "invoice_number"=>$invoice_number,
                                            
                                             )
                                           
                                        );
               }
           }else{
                            header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "value" => $delivery_charges,
                                            "invoice_number"=>$invoice_number,
                                           
                                             )
                                        );
           }
             
            
        }else{
            $msg = "The redirection of this '$hamper_label' hamper is not successful. Its possible that you are trying to redirect to the same address with same delivery type. If this is not the case, please contact the customer service desk for assistance";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
        }
    }
    
    
    /**
     * This is the function that gets the initial number of hampers for delivery
     */
    public function getTheNumberOfHampersForDelivery($hamper_id,$beneficiary_id){
        $model = new HamperHasBeneficiary;
        return $model->getTheNumberOfHampersForDelivery($hamper_id,$beneficiary_id);
    }
    
    /**
     * This is the function that generates the invoice number of a hamper redirection order
     */
    public function generateTheHamperRedirectInvoiceNumber($hamper_id,$beneficiary_id){
       $model = new HamperHasBeneficiary;
       return $model->generateTheHamperRedirectInvoiceNumber($hamper_id,$beneficiary_id);
    }
    
    
    /**
     * This is the function that gets the delivery charges for a delivery type during redirect
     */
    public function getTheCostOfHamperDeliveryToThisCity($hamper_id,$city_id,$delivery_type){
        $model = new City;
        return $model->getTheCostOfHamperDeliveryToThisCity($hamper_id,$city_id,$delivery_type);
    }
    
    /**
     * This is the function that confirms if the redirection of a hamper is a success
     */
    public function isTheRedirectionOfThisHamperASuccess($hamper_id,$beneficiary_id,$delivery_type,$delivery_country,$delivery_state,$delivery_city,$place_of_delivery,$delivery_charges){
        
        $model = new HamperHasBeneficiary;
        return $model->isTheRedirectionOfThisHamperASuccess($hamper_id,$beneficiary_id,$delivery_type,$delivery_country,$delivery_state,$delivery_city,$place_of_delivery,$delivery_charges);
    }
    
    /**
     * This is the function that gets scheduled initial delivery type of a hamper
     */
    public function getTheInitaiDeliveryTypeOfThisHamper($hamper_id,$beneficiary_id){
        $model = new HamperHasBeneficiary;
        return $model->getTheInitaiDeliveryTypeOfThisHamper($hamper_id,$beneficiary_id);
    }
    
    
    /**
     * This is the function that makes payment from a member wallet
     */
    public function actionmakeThisOrderPaymentFromWallet(){
        
         $_id = $_POST['order_id'];
                  
            $model=Order::model()->findByPk($_id);
            
            if($_POST['address'] == 'primary'){
                $model->delivery_address1 = $_POST['address1'];
                $model->delivery_address2 = $_POST['address2'];
                $model->delivery_country_id = $_POST['country_id'];
                $model->delivery_state_id = $_POST['state_id'];
                $model->delivery_city_id = $_POST['city_id'];
                $model->person_in_care_of =$_POST['primary_reciever_name'];
                $model->delivery_mobile_number =$_POST['primary_reciever_mobile_number'];
                $model->address_landmark =$_POST['primary_address_landmark'];
                $model->nearest_bus_stop =$_POST['primary_address_nearest_bus_stop'];
            }else if($_POST['address'] == 'permanent'){
                $model->delivery_address1 = $_POST['delivery_address1'];
                $model->delivery_address2 = $_POST['delivery_address2'];
                $model->delivery_country_id = $_POST['delivery_country_id'];
                $model->delivery_state_id = $_POST['delivery_state_id'];
                $model->delivery_city_id = $_POST['delivery_city_id'];
                $model->person_in_care_of =$_POST['permanent_reciever_name'];
                $model->delivery_mobile_number =$_POST['permanent_reciever_mobile_number'];
                $model->address_landmark =$_POST['permanent_address_landmark'];
                $model->nearest_bus_stop =$_POST['permanent_address_nearest_bus_stop'];
            }else if($_POST['address']== 'corporate'){
                $model->delivery_address1 = $_POST['corporate_address1'];
                $model->delivery_address2 = $_POST['corporate_address2'];
                $model->delivery_country_id = $_POST['corporate_country_id'];
                $model->delivery_state_id = $_POST['corporate_state_id'];
                $model->delivery_city_id = $_POST['corporate_city_id'];
                $model->person_in_care_of =$_POST['corporate_reciever_name'];
                $model->delivery_mobile_number =$_POST['corporate_reciever_mobile_number'];
                $model->address_landmark =$_POST['corporate_address_landmark'];
                $model->nearest_bus_stop =$_POST['corporate_address_nearest_bus_stop'];
            }else if($_POST['address']== 'special'){
                $model->delivery_address1 = $_POST['order_address1'];
                $model->delivery_address2 = $_POST['order_address2'];
                $model->delivery_country_id = $_POST['order_country_id'];
                $model->delivery_state_id = $_POST['order_state_id'];
                $model->delivery_city_id = $_POST['order_city_id'];
                $model->person_in_care_of =$_POST['special_reciever_name'];
                $model->delivery_mobile_number =$_POST['special_reciever_mobile_number'];
                $model->address_landmark =$_POST['order_address_landmark'];
                $model->nearest_bus_stop =$_POST['order_address_nearest_bus_stop'];
            }   
            $member_id = $_POST['id'];
            $model->is_term_acceptable = $_POST['is_term_acceptable'];
            
            //get the wallet id of this member
            $wallet_id = $this->getTheWalletIdOfMember($member_id);
            
            if($this->isTheSettlementOfThisAmountPossibleWithMemberWallet($member_id,$_POST['cart_amount_for_computation'],$_POST['delivery_charges_for_computation'])){
                if($this->isOrderPaymentNotAlreadyEffected($_id)){
                
                if($model->save()) {
                        
                          switch($settlement_status = $this->isThisOrderPaymentFromWalletSuccessful($_id,$_POST['cart_amount_for_computation'],$_POST['delivery_charges_for_computation'],$_POST['delivery'],$member_id,$payment_mode='wallet',$remark='purchase payment from wallet')){
                              case 0:{//successful ordering and reconstruction
                                  if($this->isRemovalOfConstituentsAmendmentsByThisUserSuccessful($member_id)){
                                   
                                        //get the invoice number of this payment    
                                        $invoice_number = $this->getTheInvoiceNumberOfThisPayment($_id);
                              
                                        $membership_number = $this->getTheMembershipNumberOfThisMember($member_id);
                              
                                        //get the order number of this order
                                        $order_number  = $this->getTheOrderNumberOfThisOrder($_id);
                              
                                        if($this->isOrderClosed($_id)){
                                            // $result['success'] = 'true';
                                            $msg = "You have successfully effected payment on this order using your wallet. Please let us know via our Customer care desk if you had any difficulty during payment.";
                                            header('Content-Type: application/json');
                                                echo CJSON::encode(array(
                                                    "success" => mysql_errno() == 0,
                                                    "msg" => $msg,
                                                    "invoice_number"=>$invoice_number,
                                                    "membership_number"=>$membership_number,
                                                    "order_number"=> $order_number 
                                                )
                                            );
                              }else{
                                   $this->sendAnEmailToTheHelpDesk($order_id,$membership_number);
                                  
                              }
                              
                                   
                               }else{
                                        //get the invoice number of this payment    
                                        $invoice_number = $this->getTheInvoiceNumberOfThisPayment($_id);
                              
                                        $membership_number = $this->getTheMembershipNumberOfThisMember($member_id);
                              
                                        //get the order number of this order
                                        $order_number  = $this->getTheOrderNumberOfThisOrder($_id);
                              
                                        if($this->isOrderClosed($_id)){
                                            // $result['success'] = 'true';
                                            $msg = "You have successfully effected payment on this order using your wallet. Please let us know via our Customer care if you had any difficulty during payment.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                                  "success" => mysql_errno() == 0,
                                                  "msg" => $msg,
                                                  "invoice_number"=>$invoice_number,
                                                  "membership_number"=>$membership_number,
                                                  "order_number"=> $order_number,
                                                "order_number"=> $order_number,
                                                )
                                            );
                                     }else{
                                        $this->sendAnEmailToTheHelpDesk($order_id,$membership_number);
                                  
                                    }
                              
                                   
                               }
                               break;   
                              }
                              
                              case 1:{//payment was successful but some products will not be delivered as there are limited funds in the wallet
                                  if($this->isRemovalOfConstituentsAmendmentsByThisUserSuccessful($member_id)){
                                   
                                        //get the invoice number of this payment    
                                        $invoice_number = $this->getTheInvoiceNumberOfThisPayment($_id);
                              
                                        $membership_number = $this->getTheMembershipNumberOfThisMember($member_id);
                              
                                        //get the order number of this order
                                        $order_number  = $this->getTheOrderNumberOfThisOrder($_id);
                              
                                        if($this->isOrderClosed($_id)){
                                            // $result['success'] = 'true';
                                            $msg = "A partial payment of this order was made using your wallet as some products could not be settled probably because of limit issues. Go to your 'Purchase' tab in 'My Oneroof' to see the list of these unsettled products.  Please contact Customer Care for detail";
                                            header('Content-Type: application/json');
                                                echo CJSON::encode(array(
                                                    "success" => mysql_errno() == 0,
                                                    "msg" => $msg,
                                                    "invoice_number"=>$invoice_number,
                                                    "membership_number"=>$membership_number,
                                                    "order_number"=> $order_number 
                                                )
                                            );
                              }else{
                                   $this->sendAnEmailToTheHelpDesk($order_id,$membership_number);
                                  
                              }
                              
                                   
                               }else{
                                        //get the invoice number of this payment    
                                        $invoice_number = $this->getTheInvoiceNumberOfThisPayment($_id);
                              
                                        $membership_number = $this->getTheMembershipNumberOfThisMember($member_id);
                              
                                        //get the order number of this order
                                        $order_number  = $this->getTheOrderNumberOfThisOrder($_id);
                              
                                        if($this->isOrderClosed($_id)){
                                            // $result['success'] = 'true';
                                            $msg = "A partial payment of this order was made using your wallet as some products could not be settled probably because of limit issues. Go to your 'Purchase' tab in 'My Oneroof' to see the list of these unsettled products. Please contact Customer Care for detail";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                                  "success" => mysql_errno() == 0,
                                                  "msg" => $msg,
                                                  "invoice_number"=>$invoice_number,
                                                  "membership_number"=>$membership_number,
                                                  "order_number"=> $order_number 
                                                )
                                            );
                                     }else{
                                        $this->sendAnEmailToTheHelpDesk($order_id,$membership_number);
                                  
                                    }
                              
                                   
                               }
                                 break;  
                              }
                             
                              case 2:{//Order of these products were not success, however wallet reconstruction were successful for all products
                                  $msg = 'payment for this transaction was not successful. Its possible the order had been closed. Please re-order the products and try again.';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                                break;  
                              }
                              
                              case 3:{//Ordering of these products and the reconstruction of the members wallet were not successful
                                 $msg = 'payment for this transaction was not successful. Please contact customer care if there is any deductions on your wallet';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                                break;
                              } 
                              case 4:{//Ordering of these products were not successful however the reconstruction of the wallets is successful
                                   $msg = 'payment for this transaction was not successful. Could be due to insufficient fund on your wallet. Please fund your wallet and re-order the products';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                                  
                                break;
                              }
                               case 5:{//ordering of these products and the wallet reconstruction were not successful
                                   $msg = 'payment for this transaction was not successful. Could be due to insufficient fund on your wallet. Please fund your wallet and re-order the products. However, contact customer for any deduction on your wallet base on this transaction';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                                  
                                break;
                              }
                              
                                  
                          }
                                  
                                          
                     }else {
                         //$result['success'] = 'false';
                         $msg = 'payment for this transaction was not successful.Please contact the Customer Service Help Desk ';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                     }
                
                
            }else{
                $msg = 'payment for this transaction had already been effeced before. Please contact the Customer Service Help Desk for assistance';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                     
            }
                
            }else{
                $msg = 'Insufficient fund in the wallet. Please load the wallet and try again or make payment using the online payment option';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
            }
            
        
    }
    
    
    /**
     * This is the function that gets wallet id of a member
     */
    public function getTheWalletIdOfMember($member_id){
        $model = new Wallet;
        return $model->getTheWalletIdOfMember($member_id);
    }
    
    
    /**
     * This is the function that determines if an payment from a wallet is successful
     */
    public function isThisOrderPaymentFromWalletSuccessful($_id,$cart_amount_for_computation,$delivery_charges_for_computation,$delivery,$member_id,$payment_mode,$remark){
        $model = new Payment;
        return $model->isThisOrderPaymentFromWalletSuccessful($_id,$cart_amount_for_computation,$delivery_charges_for_computation,$delivery,$member_id,$payment_mode,$remark);
        
    }
    
    /**
     * This is the function that confirms if te settlement of a transaction is possible using members wallet
     */
    public function isTheSettlementOfThisAmountPossibleWithMemberWallet($member_id,$cart_amount_for_computation,$delivery_charges_for_computation){
        $model = new Wallet;
        return $model->isTheSettlementOfThisAmountPossibleWithMemberWallet($member_id,$cart_amount_for_computation,$delivery_charges_for_computation);
    }
    
    
    /**
     * This is the function that confirms the reduction of values in the wallet after purchase
     */
    public function isTheRecalculationOfWalletPositionASuccess($wallet_id,$order_id,$cart_amount_for_computation,$delivery_charges_for_computation){
        $model = new Wallet;
        return $model->isTheRecalculationOfWalletPositionASuccess($wallet_id,$order_id,$cart_amount_for_computation,$delivery_charges_for_computation);
    }
    
    
    
    /**
     * this is the function that register the payment to be made on delivery
     */
    public function actionmakeThisOrderPaymentOnDelivery(){
        
        $_id = $_POST['order_id'];
                  
            $model=Order::model()->findByPk($_id);
            
            if($_POST['address'] == 'primary'){
                $model->delivery_address1 = $_POST['address1'];
                $model->delivery_address2 = $_POST['address2'];
                $model->delivery_country_id = $_POST['country_id'];
                $model->delivery_state_id = $_POST['state_id'];
                $model->delivery_city_id = $_POST['city_id'];
                $model->person_in_care_of =$_POST['primary_reciever_name'];
                $model->delivery_mobile_number =$_POST['primary_reciever_mobile_number'];
                $model->address_landmark =$_POST['primary_address_landmark'];
                $model->nearest_bus_stop =$_POST['primary_address_nearest_bus_stop'];
            }else if($_POST['address'] == 'permanent'){
                $model->delivery_address1 = $_POST['delivery_address1'];
                $model->delivery_address2 = $_POST['delivery_address2'];
                $model->delivery_country_id = $_POST['delivery_country_id'];
                $model->delivery_state_id = $_POST['delivery_state_id'];
                $model->delivery_city_id = $_POST['delivery_city_id'];
                $model->person_in_care_of =$_POST['permanent_reciever_name'];
                $model->delivery_mobile_number =$_POST['permanent_reciever_mobile_number'];
                $model->address_landmark =$_POST['permanent_address_landmark'];
                $model->nearest_bus_stop =$_POST['permanent_address_nearest_bus_stop'];
            }else if($_POST['address']== 'corporate'){
                $model->delivery_address1 = $_POST['corporate_address1'];
                $model->delivery_address2 = $_POST['corporate_address2'];
                $model->delivery_country_id = $_POST['corporate_country_id'];
                $model->delivery_state_id = $_POST['corporate_state_id'];
                $model->delivery_city_id = $_POST['corporate_city_id'];
                $model->person_in_care_of =$_POST['corporate_reciever_name'];
                $model->delivery_mobile_number =$_POST['corporate_reciever_mobile_number'];
                $model->address_landmark =$_POST['corporate_address_landmark'];
                $model->nearest_bus_stop =$_POST['corporate_address_nearest_bus_stop'];
            }else if($_POST['address']== 'special'){
                $model->delivery_address1 = $_POST['order_address1'];
                $model->delivery_address2 = $_POST['order_address2'];
                $model->delivery_country_id = $_POST['order_country_id'];
                $model->delivery_state_id = $_POST['order_state_id'];
                $model->delivery_city_id = $_POST['order_city_id'];
                $model->person_in_care_of =$_POST['special_reciever_name'];
                $model->delivery_mobile_number =$_POST['special_reciever_mobile_number'];
                $model->address_landmark =$_POST['order_address_landmark'];
                $model->nearest_bus_stop =$_POST['order_address_nearest_bus_stop'];
            }   
            $member_id = $_POST['id'];
            $model->is_term_acceptable = $_POST['is_term_acceptable'];
            
            //get the maximum allowable cash transaction
            if($this->getTheMaximumAllowableCashTransaction()>=($_POST['ondelivery_cost_of_items_for_computation'] + $_POST['ondelivery_delivery_charges_for_computation'])){
                $msg = "Your transaction is successful. On delivery, you can either pay by cash or by using any recognised bank debit card on our POS. Thank you for choosing Oneroof";
            }else{
                $msg = "Your transaction is successful. On delivery, payment can only be made using any recognised bank debit card on our POS. Thank you for choosing Oneroof";
            }
            $escrow_charges = 0; 
         
            
            if($this->isOrderPaymentNotAlreadyEffected($_id)){
                
                if($model->save()) {
                        
                          if($this->isThisOrderPaymentSuccessful($_id,$_POST['ondelivery_cost_of_items_for_computation'],$_POST['ondelivery_delivery_charges_for_computation'],$escrow_charges,$_POST['delivery'],$member_id,$payment_mode='ondelivery',$remark='Payment on delivery')){
                              
                               if($this->isRemovalOfConstituentsAmendmentsByThisUserSuccessful($member_id)){
                                   
                                    //get the invoice number of this payment    
                              $invoice_number = $this->getTheInvoiceNumberOfThisPayment($_id);
                              
                              $membership_number = $this->getTheMembershipNumberOfThisMember($member_id);
                              
                              //get the order number of this order
                              $order_number  = $this->getTheOrderNumberOfThisOrder($_id);
                              
                              if($this->isOrderClosed($_id)){
                                   // $result['success'] = 'true';
                                    //$msg = "Your transaction is successful. On delivery, payment will be made using any recognised bank debit card on our POS. Thank you for choosing Oneroof";
                                  $msg=$msg;  
                                  header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                        "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                         "invoice_number"=>$invoice_number,
                                         "membership_number"=>$membership_number,
                                          "order_number"=> $order_number 
                                                )
                                    );
                              }else{
                                   $this->sendAnEmailToTheHelpDesk($order_id,$membership_number);
                                  
                              }
                              
                                   
                               }else{
                                    //get the invoice number of this payment    
                              $invoice_number = $this->getTheInvoiceNumberOfThisPayment($_id);
                              
                              $membership_number = $this->getTheMembershipNumberOfThisMember($member_id);
                              
                                //get the order number of this order
                              $order_number  = $this->getTheOrderNumberOfThisOrder($_id);
                              
                              if($this->isOrderClosed($_id)){
                                   // $result['success'] = 'true';
                                    //$msg = "Your transaction is successful. On delivery, payment will be made using any recognised bank debit card on our POS. Thank you for choosing Oneroof";
                                  $msg=$msg;   
                                  header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                        "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                         "invoice_number"=>$invoice_number,
                                         "membership_number"=>$membership_number,
                                          "order_number"=> $order_number 
                                                )
                                    );
                              }else{
                                   $this->sendAnEmailToTheHelpDesk($order_id,$membership_number);
                                  
                              }
                              
                                   
                               }
                             
                              
                                 } else {
                                     $msgg = 'payment for this transaction was not successful. Please contact the Customer Service Help Desk';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msgg)
                                        );
                         
                                     
                                     
                                 } 
                                  
                                          
                     }else {
                         //$result['success'] = 'false';
                         $msgg = 'payment for this transaction was not successful. Please contact the Customer Service Help Desk';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msgg)
                                        );
                         
                     }
                
                
            }else{
                $msgg = 'payment for this transaction had already been effeced before. Please contact the Customer Service Help Desk for assistance';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msgg)
                                        );
                         
                     
            }
        
    }
    
    
    /**
     * This is the function that gets the maximum amount allowed for cash transaction
     */
    public function getTheMaximumAllowableCashTransaction(){
        $model = new PlatformSettings;
        return $model->getTheMaximumAllowableCashTransaction();
    }
    
    
    /**
     * This is the function that effect an order using both ondelivery and online payment method
     */
    public function actionmakeThisOrderScheduledOnlineAndOndeliveryPayment(){
        
        $_id = $_POST['order_id'];
                  
            $model=Order::model()->findByPk($_id);
            
            if($_POST['address'] == 'primary'){
                $model->delivery_address1 = $_POST['address1'];
                $model->delivery_address2 = $_POST['address2'];
                $model->delivery_country_id = $_POST['country_id'];
                $model->delivery_state_id = $_POST['state_id'];
                $model->delivery_city_id = $_POST['city_id'];
                $model->person_in_care_of =$_POST['primary_reciever_name'];
                $model->delivery_mobile_number =$_POST['primary_reciever_mobile_number'];
                $model->address_landmark =$_POST['primary_address_landmark'];
                $model->nearest_bus_stop =$_POST['primary_address_nearest_bus_stop'];
            }else if($_POST['address'] == 'permanent'){
                $model->delivery_address1 = $_POST['delivery_address1'];
                $model->delivery_address2 = $_POST['delivery_address2'];
                $model->delivery_country_id = $_POST['delivery_country_id'];
                $model->delivery_state_id = $_POST['delivery_state_id'];
                $model->delivery_city_id = $_POST['delivery_city_id'];
                $model->person_in_care_of =$_POST['permanent_reciever_name'];
                $model->delivery_mobile_number =$_POST['permanent_reciever_mobile_number'];
                $model->address_landmark =$_POST['permanent_address_landmark'];
                $model->nearest_bus_stop =$_POST['permanent_address_nearest_bus_stop'];
            }else if($_POST['address']== 'corporate'){
                $model->delivery_address1 = $_POST['corporate_address1'];
                $model->delivery_address2 = $_POST['corporate_address2'];
                $model->delivery_country_id = $_POST['corporate_country_id'];
                $model->delivery_state_id = $_POST['corporate_state_id'];
                $model->delivery_city_id = $_POST['corporate_city_id'];
                $model->person_in_care_of =$_POST['corporate_reciever_name'];
                $model->delivery_mobile_number =$_POST['corporate_reciever_mobile_number'];
                $model->address_landmark =$_POST['corporate_address_landmark'];
                $model->nearest_bus_stop =$_POST['corporate_address_nearest_bus_stop'];
            }else if($_POST['address']== 'special'){
                $model->delivery_address1 = $_POST['order_address1'];
                $model->delivery_address2 = $_POST['order_address2'];
                $model->delivery_country_id = $_POST['order_country_id'];
                $model->delivery_state_id = $_POST['order_state_id'];
                $model->delivery_city_id = $_POST['order_city_id'];
                $model->person_in_care_of =$_POST['special_reciever_name'];
                $model->delivery_mobile_number =$_POST['special_reciever_mobile_number'];
                $model->address_landmark =$_POST['order_address_landmark'];
                $model->nearest_bus_stop =$_POST['order_address_nearest_bus_stop'];
            }   
            $member_id = $_POST['id'];
            $model->is_term_acceptable = $_POST['is_term_acceptable'];
            
             if(isset($_POST['non_ondelivery_escrow_charges_for_computation'])){
                $escrow_charges = $_POST['non_ondelivery_escrow_charges_for_computation'];
            }else{
               $escrow_charges = 0; 
            }
            $ondelivery_escrow_charges = 0; 
            if($this->isOrderPaymentNotAlreadyEffected($_id)){
                
                if($model->save()) {
                       if($this->isThisOrderPaymentSuccessful($_id,$_POST['ondelivery_cost_of_items_for_computation'],$_POST['ondelivery_delivery_charges_for_computation'],$ondelivery_escrow_charges,$_POST['delivery'],$member_id,$payment_mode='ondelivery',$remark='Payment on delivery')){
                            


                          if($this->isThisOrderPaymentSuccessful($_id,$_POST['non_ondelivery_cost_of_items_for_computation'],$_POST['non_ondelivery_delivery_charges_for_computation'],$escrow_charges,$_POST['delivery'],$member_id,$payment_mode='online',$remark='Online payment')){
                              
                               if($this->isRemovalOfConstituentsAmendmentsByThisUserSuccessful($member_id)){
                                   
                                    //get the invoice number of this payment    
                              $invoice_number = $this->getTheInvoiceNumberOfThisPayment($_id);
                              
                              $membership_number = $this->getTheMembershipNumberOfThisMember($member_id);
                              
                              //get the order number of this order
                              $order_number  = $this->getTheOrderNumberOfThisOrder($_id);
                              
                              if($this->isOrderClosed($_id)){
                                   // $result['success'] = 'true';
                                    $msg = "Using your '$membership_number' membership number and '$invoice_number' invoice number, effect the online payment on the redirected payment platform";
                                    header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                        "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                         "invoice_number"=>$invoice_number,
                                         "membership_number"=>$membership_number,
                                          "order_number"=> $order_number 
                                                )
                                    );
                              }else{
                                   $this->sendAnEmailToTheHelpDesk($order_id,$membership_number);
                                  
                              }
                              
                                   
                               }else{
                                    //get the invoice number of this payment    
                              $invoice_number = $this->getTheInvoiceNumberOfThisPayment($_id);
                              
                              $membership_number = $this->getTheMembershipNumberOfThisMember($member_id);
                              
                                //get the order number of this order
                              $order_number  = $this->getTheOrderNumberOfThisOrder($_id);
                              
                              if($this->isOrderClosed($_id)){
                                   // $result['success'] = 'true';
                                    $msg = "Using your '$membership_number' membership number and '$invoice_number' invoice number, effect the online payment on the redirected payment platform";
                                    header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                        "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                         "invoice_number"=>$invoice_number,
                                         "membership_number"=>$membership_number,
                                          "order_number"=> $order_number 
                                                )
                                    );
                              }else{
                                   $this->sendAnEmailToTheHelpDesk($order_id,$membership_number);
                                  
                              }
                              
                                   
                               }
                             
                              
                                 } else {
                                     $msg = 'payment for this transaction was not successful. Please contact the Customer Service Help Desk';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                                     
                                     
                                 } 
                            
                            
                        }else{
                            //failed payment on delivery
                             //registration of payment om delivery not successful
                            $msg = 'registration of on-delivery payment for this transaction was not successful.Please contact the Customer Service Help Desk ';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                        }
                          
                                  
                                          
                     }else {
                         //$result['success'] = 'false';
                         $msg = 'payment for this transaction was not successful. Please contact the Customer Service Help Desk';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                     }
                
                
            }else{
                $msg = 'payment for this transaction had already been effeced before. Please contact the Customer Service Help Desk for assistance';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                     
            }
        
    }
    
    
    /**
     * This is the function that settles order payment usibg both on-delivery and wallet method
     */
    public function actionmakeThisOrderPaymentFromWalletAndOnDelivery(){
        
        $_id = $_POST['order_id'];
                  
            $model=Order::model()->findByPk($_id);
            
            if($_POST['address'] == 'primary'){
                $model->delivery_address1 = $_POST['address1'];
                $model->delivery_address2 = $_POST['address2'];
                $model->delivery_country_id = $_POST['country_id'];
                $model->delivery_state_id = $_POST['state_id'];
                $model->delivery_city_id = $_POST['city_id'];
                $model->person_in_care_of =$_POST['primary_reciever_name'];
                $model->delivery_mobile_number =$_POST['primary_reciever_mobile_number'];
                $model->address_landmark =$_POST['primary_address_landmark'];
                $model->nearest_bus_stop =$_POST['primary_address_nearest_bus_stop'];
            }else if($_POST['address'] == 'permanent'){
                $model->delivery_address1 = $_POST['delivery_address1'];
                $model->delivery_address2 = $_POST['delivery_address2'];
                $model->delivery_country_id = $_POST['delivery_country_id'];
                $model->delivery_state_id = $_POST['delivery_state_id'];
                $model->delivery_city_id = $_POST['delivery_city_id'];
                $model->person_in_care_of =$_POST['permanent_reciever_name'];
                $model->delivery_mobile_number =$_POST['permanent_reciever_mobile_number'];
                $model->address_landmark =$_POST['permanent_address_landmark'];
                $model->nearest_bus_stop =$_POST['permanent_address_nearest_bus_stop'];
            }else if($_POST['address']== 'corporate'){
                $model->delivery_address1 = $_POST['corporate_address1'];
                $model->delivery_address2 = $_POST['corporate_address2'];
                $model->delivery_country_id = $_POST['corporate_country_id'];
                $model->delivery_state_id = $_POST['corporate_state_id'];
                $model->delivery_city_id = $_POST['corporate_city_id'];
                $model->person_in_care_of =$_POST['corporate_reciever_name'];
                $model->delivery_mobile_number =$_POST['corporate_reciever_mobile_number'];
                $model->address_landmark =$_POST['corporate_address_landmark'];
                $model->nearest_bus_stop =$_POST['corporate_address_nearest_bus_stop'];
            }else if($_POST['address']== 'special'){
                $model->delivery_address1 = $_POST['order_address1'];
                $model->delivery_address2 = $_POST['order_address2'];
                $model->delivery_country_id = $_POST['order_country_id'];
                $model->delivery_state_id = $_POST['order_state_id'];
                $model->delivery_city_id = $_POST['order_city_id'];
                $model->person_in_care_of =$_POST['special_reciever_name'];
                $model->delivery_mobile_number =$_POST['special_reciever_mobile_number'];
                $model->address_landmark =$_POST['order_address_landmark'];
                $model->nearest_bus_stop =$_POST['order_address_nearest_bus_stop'];
            }   
            $member_id = $_POST['id'];
            $model->is_term_acceptable = $_POST['is_term_acceptable'];
            
            //get the wallet id of this member
            $wallet_id = $model->getTheWalletIdOfMember($member_id);
            
            if($this->isTheSettlementOfThisAmountPossibleWithMemberWallet($member_id,$_POST['non_ondelivery_cost_of_items_for_computation'],$_POST['non_ondelivery_delivery_charges_for_computation'])){
                if($this->isOrderPaymentNotAlreadyEffected($_id)){
                
                if($model->save()) {
                        if($this->isThisOrderPaymentSuccessful($_id,$_POST['ondelivery_cost_of_items_for_computation'],$_POST['ondelivery_delivery_charges_for_computation'],$_POST['delivery'],$member_id,$payment_mode='ondelivery',$remark='Payment on delivery')){
                            

                        switch($settlement_status = $this->isThisOrderPaymentFromWalletSuccessful($_id,$_POST['non_ondelivery_cost_of_items_for_computation'],$_POST['non_ondelivery_delivery_charges_for_computation'],$_POST['delivery'],$member_id,$payment_mode='wallet',$remark='purchase payment from wallet')){
                              case 0:{//successful ordering and reconstruction
                                  if($this->isRemovalOfConstituentsAmendmentsByThisUserSuccessful($member_id)){
                                   
                                        //get the invoice number of this payment    
                                        $invoice_number = $this->getTheInvoiceNumberOfThisPayment($_id);
                              
                                        $membership_number = $this->getTheMembershipNumberOfThisMember($member_id);
                              
                                        //get the order number of this order
                                        $order_number  = $this->getTheOrderNumberOfThisOrder($_id);
                              
                                        if($this->isOrderClosed($_id)){
                                            // $result['success'] = 'true';
                                            $msg = "You have successfully effected payment on this order using your wallet. Please us know via our Customer care if you had any difficulty during payment.";
                                            header('Content-Type: application/json');
                                                echo CJSON::encode(array(
                                                    "success" => mysql_errno() == 0,
                                                    "msg" => $msg,
                                                    "invoice_number"=>$invoice_number,
                                                    "membership_number"=>$membership_number,
                                                    "order_number"=> $order_number 
                                                )
                                            );
                              }else{
                                   $this->sendAnEmailToTheHelpDesk($order_id,$membership_number);
                                  
                              }
                              
                                   
                               }else{
                                        //get the invoice number of this payment    
                                        $invoice_number = $this->getTheInvoiceNumberOfThisPayment($_id);
                              
                                        $membership_number = $this->getTheMembershipNumberOfThisMember($member_id);
                              
                                        //get the order number of this order
                                        $order_number  = $this->getTheOrderNumberOfThisOrder($_id);
                              
                                        if($this->isOrderClosed($_id)){
                                            // $result['success'] = 'true';
                                            $msg = "You have successfully effected payment on this order using your wallet. Please us know via our Customer care if you had any difficulty during payment.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                                  "success" => mysql_errno() == 0,
                                                  "msg" => $msg,
                                                  "invoice_number"=>$invoice_number,
                                                  "membership_number"=>$membership_number,
                                                  "order_number"=> $order_number 
                                                )
                                            );
                                     }else{
                                        $this->sendAnEmailToTheHelpDesk($order_id,$membership_number);
                                  
                                    }
                              
                                   
                               }
                               break;   
                              }
                              
                              case 1:{//payment was successful but some products will not be delivered as there are limited funds in the wallet
                                  if($this->isRemovalOfConstituentsAmendmentsByThisUserSuccessful($member_id)){
                                   
                                        //get the invoice number of this payment    
                                        $invoice_number = $this->getTheInvoiceNumberOfThisPayment($_id);
                              
                                        $membership_number = $this->getTheMembershipNumberOfThisMember($member_id);
                              
                                        //get the order number of this order
                                        $order_number  = $this->getTheOrderNumberOfThisOrder($_id);
                              
                                        if($this->isOrderClosed($_id)){
                                            // $result['success'] = 'true';
                                            $msg = "A partial payment of this order was made using your wallet as some products could not be settled probably because of limit issues. Go to your 'Purchase' tab in 'My Oneroof' to see the list of these unsettled products.  Please contact Customer Care for detail";
                                            header('Content-Type: application/json');
                                                echo CJSON::encode(array(
                                                    "success" => mysql_errno() == 0,
                                                    "msg" => $msg,
                                                    "invoice_number"=>$invoice_number,
                                                    "membership_number"=>$membership_number,
                                                    "order_number"=> $order_number 
                                                )
                                            );
                              }else{
                                   $this->sendAnEmailToTheHelpDesk($order_id,$membership_number);
                                  
                              }
                              
                                   
                               }else{
                                        //get the invoice number of this payment    
                                        $invoice_number = $this->getTheInvoiceNumberOfThisPayment($_id);
                              
                                        $membership_number = $this->getTheMembershipNumberOfThisMember($member_id);
                              
                                        //get the order number of this order
                                        $order_number  = $this->getTheOrderNumberOfThisOrder($_id);
                              
                                        if($this->isOrderClosed($_id)){
                                            // $result['success'] = 'true';
                                            $msg = "A partial payment of this order was made using your wallet as some products could not be settled probably because of limit issues. Go to your 'Purchase' tab in 'My Oneroof' to see the list of these unsettled products. Please contact Customer Care for detail";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                                  "success" => mysql_errno() == 0,
                                                  "msg" => $msg,
                                                  "invoice_number"=>$invoice_number,
                                                  "membership_number"=>$membership_number,
                                                  "order_number"=> $order_number 
                                                )
                                            );
                                     }else{
                                        $this->sendAnEmailToTheHelpDesk($order_id,$membership_number);
                                  
                                    }
                              
                                   
                               }
                                 break;  
                              }
                             
                              case 2:{//Order of these products were not success, however wallet reconstruction were successful for all products
                                  $msg = 'payment for this transaction was not successful. Its possible the order had been closed. Please re-order the products and try again.';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                                break;  
                              }
                              
                              case 3:{//Ordering of these products and the reconstruction of the members wallet were not successful
                                 $msg = 'payment for this transaction was not successful. Please contact customer care if there is any deductions on your wallet';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                                break;
                              } 
                              case 4:{//Ordering of these products were not successful however the reconstruction of the wallets is successful
                                   $msg = 'payment for this transaction was not successful. Could be due to insufficient fund on your wallet. Please fund your wallet and re-order the products';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                                  
                                break;
                              }
                               case 5:{//ordering of these products and the wallet reconstruction were not successful
                                   $msg = 'payment for this transaction was not successful. Could be due to insufficient fund on your wallet. Please fund your wallet and re-order the products. However, contact customer for any deduction on your wallet base on this transaction';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                                  
                                break;
                              }
                              
                                  
                          }
                            
                            
                        }else{
                            
                            //registration of payment om delivery not successful
                            $msg = 'registration of on-delivery payment for this transaction was not successful.Please contact the Customer Service Help Desk ';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                        }
                          
                                  
                                          
                     }else {
                         //$result['success'] = 'false';
                         $msg = 'payment for this transaction was not successful.Please contact the Customer Service Help Desk ';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                     }
                
                
            }else{
                $msg = 'payment for this transaction had already been effeced before. Please contact the Customer Service Help Desk for assistance';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                     
            }
                
            }else{
                $msg = 'Insufficient fund in the wallet. Please load the wallet and try again';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
            }
        
    }
}
