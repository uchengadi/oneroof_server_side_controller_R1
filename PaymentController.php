<?php

class PaymentController extends Controller
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
				'actions'=>array('retrievePaymentDetails','retrievePaymentDetailsForUpdate','addNewPayment',
                                    'updatePayment','ListAllUnverifiedPayments','ListAllPayments','ListAllFailedPayments',
                                    'retrievePaymentDetailsForUpdate','confirmThisPayment','failThisPayment','justTest',
                                    'makeThisOrderPayment'),
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
         * This is the function that retrieves some details of payments
         */
        public function actionretrievePaymentDetails(){
            
            $order_id = $_REQUEST['order_id'];
            
            //get the gross amount
            $gross_amount = $this->getTheGrossAmountForThisOrder($order_id);
            //$gross_amount = 5777.50;
            
            //get the discounted amount
            $discount_amount = $this->getTheDiscountedAmountOfThisOrder($order_id);
           // $discount_amount = 450.99;
            
            //get the net amount 
            $net_amount = $gross_amount - $discount_amount;
            //$net_amount = 4577.98;
            
            //get the vat amount
            $vat = $this->getTheVatOfThisOrder($order_id);
            //$vat= 200;
            
            //get the expected income from this order
             $expected_income = $this->getTheExpectedIncomeFromThisOrder($order_id);
           // $expected_income = 550.99;
            
            //get the invoice number of this order
            $invoice_number = $this->generateTheInvoiceNumberForThisOrder($order_id);
            //$invoice_number = '33000aa456';
            
          
            if($order_id===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                      header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "gross_amount" => $gross_amount,
                                     "net_amount"=>$net_amount,
                                     "discount_amount"=>$discount_amount,
                                     "vat"=>$vat,
                                     "revenue"=>$expected_income,
                                       "invoice_number"=>$invoice_number,
                                                                         
                            ));
                       
                }
            
        }
        
     
        
         /**
         * This is the function that generates an invoice for this order payment
         */
        public function generateTheInvoiceNumberForThisOrder($order_id){
            $model = new Payment;
            
            return $model->generateTheInvoiceNumberForThisOrder($order_id);
        }
        
            
        
       /**
         * This is the function that gets the expected revenue in an order
         */
        public function getTheExpectedIncomeFromThisOrder($order_id){
            $model = new Payment;
            return $model->getTheExpectedIncomeFromThisOrder($order_id);
        }
        
        
        /**
         * This is the function that retrieves all the products in an order 
         */
        public function getAllTheProductsOnThisOrder($order_id){
            
            $model = new Order;
            
            return $model->getAllTheProductsOnThisOrder($order_id);
        }
        
        
        
        public function actionjustTest(){
           // $subscription_initiation_date = "2017-03-23 21:12:01";
            $amount = $this->getTheVatOfThisOrder($order_id=1);
            
             header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "amount" => $amount
                                        
                                
                            ));
            
        }
        
        /**
         * This is the function that gets the revenue amount accruable from a product sale
         */
        public function getTheRevenueAmountFromThisProduct($product){
            
            $model= new Product;
            
            return $model->getTheRevenueAmountFromThisProduct($product);
            
        }
        
        /**
         * This is the function that gets the amount of a product
         */
        public function getTheAmountOfThisProduct($product_id){
            $model = new Product;
            
            return $model->getTheAmountOfThisProduct($product_id);
            
        }
        
        
        /**
         * This is the function that gets the discount amount of a product
         */
        public function getTheDiscountAmountOfThisProduct($product){
            
            $model = new Product;
            
            return $model->getTheDiscountAmountOfThisProduct($product);
            
        }
        
        
        
        /**
         * This is the function that calculates the gross amount of an order
         */
        public function getTheGrossAmountForThisOrder($order_id){
            
            //get all the products in this order
            
           $products = $this->getAllTheProductsOnThisOrder($order_id);
            
            $gross_amount = 0;
            
            foreach($products as $product){
                
                $gross_amount = $gross_amount + $this->getTheAmountOfThisProduct($product);
            }
            
            return $gross_amount;
        }
        
        
        /**
         * This is the function that gets the discount amount of products in an order
         */
        public function getTheDiscountedAmountOfThisOrder($order_id){
            
             //get all the products in this order
            
            $products = $this->getAllTheProductsOnThisOrder($order_id);
            
            $discount_amount = 0;
            
            foreach($products as $product){
                
                $discount_amount = $discount_amount + $this->getTheDiscountAmountOfThisProduct($product);
            }
            
            return $discount_amount;
            
        }
        
        
        /**
         * This is the function that calculates the vat amount in an order  
         */
        public function getTheVatOfThisOrder($order_id){
            $model = new Payment;
            
            return $model->getTheVatOfThisOrder($order_id);
        }
        
        
        /**
         * This is the function that retreives some payment details for updates
         */
        public function actionretrievePaymentDetailsForUpdate(){
            
            $order_id = $_REQUEST['order_id'];
            $id = $_POST['id'];
           $bank_number = $this->getTheBankNumberForThisPayment($_REQUEST['bank_account_id']);
            
            $order_number = $this->getTheOrderNumber($order_id);
            
            $payee = $this->getTheNameOfThePayee($id);
            
            if($order_id===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                      header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "bank_number" => $bank_number,
                                     "order_number"=>$order_number,
                                     "payee"=>$payee
                                    
                            ));
                       
                }
            
        }
        
        
        /**
         * This is the function that gets the order number
         */
        public function getTheOrderNumber($order_id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$order_id);
                $order= Order::model()->find($criteria);
                
                return $order['order_number'];
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
            $payee= Payment::model()->find($criteria);
            
            $name = $this->getThisUsersName($payee['paid_by']);
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
         * This is the function that adds new payment
         */
        public function actionaddNewPayment(){
            
            $model = new Payment;
            
            if(is_numeric($_POST['order_number'])){
                $model->order_id = $_POST['order_number'];
            }else{
                $model->order_id = $_POST['order_id'];
            }
            if(is_numeric($_POST['bank_account'])){
                $model->bank_account_id = $_POST['bank_account'];
            }else{
                $model->bank_account_id = $_POST['bank_account_id'];
            }
            $model->discounted_amount = $_POST['discounted_amount'];
            $model->gross_amount = $_POST['gross_amount'];
            $model->invoice_number = $_POST['invoice_number'];
            $model->net_amount = $_POST['net_amount'];
            $model->status = $_POST['status'];
            $model->revenue = $_POST['revenue'];
            $model->payment_mode = strtolower($_POST['payment_mode']);
            $model->remark = strtolower($_POST['remark']);
            $model->vat = strtolower($_POST['vat']);
            $model->payment_date = date("Y-m-d H:i:s", strtotime($_POST['payment_date'])); 
            $model->paid_by = $this->getThePersonThatMadeThisOrder($model->order_id);
            
            //confirm if this order had already been paid for
            if($this->isOrderNotAlreadyPaidFor($model->order_id)){
                
                if($model->save()){
                         // $result['success'] = 'true';
                          $msg = "Payment successfully added";
                          header('Content-Type: application/json');
                          echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Addition of payment was not successful";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
                
                
                
            }else{
                 //$result['success'] = 'false';
                         $msg = "Its possible that this order may had been paid for. Please confirm from the Accounting Department";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                
            }
             
            
            
        }
        
        
        /**
         * This is the function that retrieves the person that made an order
         */
        public function getThePersonThatMadeThisOrder($order_id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$order_id);
                $order= Order::model()->find($criteria);
                
                return $order['order_initiated_by'];
            
        }
        
        
        
        /**
         * This is the function that confirms if an order had not already been paid for 
         */
        public function isOrderNotAlreadyPaidFor($order_id){
            
             $cmd =Yii::app()->db->createCommand();
            $cmd->select('COUNT(*)')
                    ->from('payment')
                    ->where("order_id = $order_id");
                $result = $cmd->queryScalar();
                
                if($result == 0){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        /**
         * This is the function that edits payments 
         */
        public function actionUpdatePayment(){
            
            $_id = $_POST['id'];
            $model=Payment::model()->findByPk($_id);
            
            
            $model->order_id = $_POST['order_id'];
            $model->bank_account_id = $_POST['bank_account_id'];
            $model->discounted_amount = $_POST['discounted_amount'];
            $model->gross_amount = $_POST['gross_amount'];
            $model->invoice_number = $_POST['invoice_number'];
            $model->net_amount = $_POST['net_amount'];
            $model->status = $_POST['status'];
            $model->revenue = $_POST['revenue'];
            $model->payment_mode = strtolower($_POST['payment_mode']);
            $model->remark = strtolower($_POST['remark']);
            $model->vat = strtolower($_POST['vat']);
            $model->payment_date = date("Y-m-d H:i:s", strtotime($_POST['payment_date'])); 
            $model->paid_by = $_POST['paid_by'];
            
             if($model->save()){
                         // $result['success'] = 'true';
                          $msg = "Payment information successfully updated";
                          header('Content-Type: application/json');
                          echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Update of payment information was not successful";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
            
            
        }
        
        
        
        /**
         * This is the function that list all payments irrespective of status
         */
        public function actionListAllPayments(){
            
            $userid = Yii::app()->user->id;
          
            $payment = Payment::model()->findAll();
                if($payment===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "payment" => $payment
                                        
                                
                            ));
                       
                }
            
        }
        
        
        /**
         * This is the function that list all unnverified payments 
         */
        public function actionListAllUnverifiedPayments(){
            
             $userid = Yii::app()->user->id; 
            //get the domain of the logged in user
            
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='status=:status';
                $criteria->params = array(':status'=>'unconfirmed');
                $payment= Payment::model()->findAll($criteria);
               
                if($payment===null) {
                    http_response_code(404);
                    $payment['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                      header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "payment" => $payment
                                        
                                
                            ));
                       
                }
            
            
        }
        
        
        
        /**
         * This is the function that list all failed payments 
         */
        public function actionListAllFailedPayments(){
            
             $userid = Yii::app()->user->id; 
            //get the domain of the logged in user
            
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='status=:status';
                $criteria->params = array(':status'=>'failed');
                $payment= Payment::model()->findAll($criteria);
               
                if($payment===null) {
                    http_response_code(404);
                    $payment['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                      header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "payment" => $payment
                                        
                                
                            ));
                       
                }
            
            
        }
        
        
        
        /**
         * This is to confirm a payment to the bank
         */
        public function actionconfirmThisPayment(){
            
            $id = $_POST['id'];
            
            $cmd =Yii::app()->db->createCommand();
            $result = $cmd->update('payment',
                         array('status'=>'confirmed',
                             'remark'=>$_POST['remark'],
                             'payment_confirmed_by'=>Yii::app()->user->id,
                             'date_of_confirmation'=>new CDbExpression('NOW()'),
                             
                        ),
                        ("id=$id")
                          
                     );
                
                if($result>0){
                    $msg = "Payment was successfully confirmed";
                          header('Content-Type: application/json');
                          echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                }else{
                    $msg = "payment could not be confirmed";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                }
            
        }
        
        
        
        
          /**
         * This is the function that fails a payment transactions
         */
        public function actionfailThisPayment(){
          
             $id = $_POST['id'];
            
            $cmd =Yii::app()->db->createCommand();
            $result = $cmd->update('payment',
                         array('status'=>'failed',
                             'reason_for_failure'=>$_POST['remark'],
                             'payment_confirmed_by'=>Yii::app()->user->id,
                             'date_of_confirmation'=>new CDbExpression('NOW()'),
                             
                        ),
                        ("id=$id")
                          
                     );
                
                if($result>0){
                    $msg = "Payment was successfully confirmed as failed";
                          header('Content-Type: application/json');
                          echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                }else{
                    $msg = "payment could not be confirmed as failed";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                }
            
            
        }
     
        
        
        
}
