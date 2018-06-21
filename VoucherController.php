<?php

class VoucherController extends Controller
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
				'actions'=>array('create','update','listAllVouchers','listAllVouchersCreatedByMember',
                                    'listAllProductsAssignedToVoucher','creatingNewVoucherForMember','toppingupthisvoucher',
                                    'limitthisvouchertothiscategory','listAllCategoriesAssignedToVoucher','listAllProductsAssignedToVoucher',
                                    'limitthisvouchertothisproduct','changeTheStatusCategoryLimiterOfThisVoucher',
                                    'changeTheStatusProductLimiterOfThisVoucher','removeProductLimiterOfThisVoucher','removeThisCategoryLimiterOfThisVoucher',
                                    'listofvoucherbeneficiaries'),
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
         * This is the function that list all vouchers for a member
         */
        public function actionlistAllVouchers(){
            
             $voucher = Voucher::model()->findAll();
                if($voucher===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"assigned" => $assigned,
                            "voucher" => $voucher)
                       );
                       
                }
            
            
        }
        
        
        /**
         * This is the function that list all vouchers created by a member
         */
        public function actionlistAllVouchersCreatedByMember(){
            
            $member_id = Yii::app()->user->id;
          
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='create_by=:id';
            $criteria->params = array(':id'=>$member_id);
            $voucher= Voucher::model()->findAll($criteria);
            
             if($voucher===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "voucher" => $voucher)
                       );
                       
                }
        }
        
        
        /**
         * This is the function that limits a voucher to a set of products
         */
        public function actionlistAllProductsAssignedToVoucher(){
            
            $voucher_id = $_REQUEST['voucher_id'];
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='voucher_id=:id';
            $criteria->params = array(':id'=>$voucher_id);
            $product= VoucherLimitedToProducts::model()->findAll($criteria);
            
             if($product===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "product" => $product)
                       );
                       
                }
            
            
        }
        
        
        /**
         * This is the function that creates new voucher for a member
         */
        public function actioncreatingNewVoucherForMember(){
            
            $model = new Voucher;
            $member_id  = Yii::app()->user->id;
            
            //$model->vouncher_number = $model->generateThisVoucherNumber($member_id);
            $model->voucher_number = 'AABB0011CCGG';
            $model->purpose = $_POST['purpose'];
            $model->status = $_POST['status'];
            $model->voucher_type = $_POST['voucher_type'];
            $model->voucher_value = $_POST['voucher_value'];
            $model->remaining_voucher_value = $_POST['voucher_value'];
            $model->accepted_voucher_creation_and_user_terms = $_POST['terms_and_conditions'];
            $model->date_created = new CDbExpression('NOW()');
            $model->create_by =$member_id;
            
            //get the member's membership number
            $membership_number = $this->getTheMembershipNumberOfThisMember($member_id);
            
            //get the email of this member
            $email_address = $this->getTheRegisteredEmailOfThisMember($member_id);
            if($model->save()) {
                         $msg = "You just create a voucher of number '$model->voucher_number' and value '$model->voucher_value'. Buying on Oneroof wouldn't get much easier. Enjoy!";
                               header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                        "success" => mysql_errno() == 0,
                                       // "msg" => $msg,
                                         "voucher_number"=>$model->voucher_number,
                                         "value"=>$model->voucher_value,
                                         "membership_number"=>$membership_number,
                                         "email"=>$email_address
                                        )
                                 );
                     
                         
              }else{
                            $msg = "This voucher could not be created. It could be due to field validation error. Please contact customer care for assistance.";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                               );
                          }
            
            
            
        }
        
        /**
         * This is the function that returns the membership number of a member
         */
        public function getTheMembershipNumberOfThisMember($member_id){
            $model = new Members;
            return $model->getTheMembershipNumberOfThisMember($member_id);
        }
        
        
        /**
         * This is the function that gets the registered email of this member
         */
        public function getTheRegisteredEmailOfThisMember($member_id){
            $model = new Members;
            return $model->getTheRegisteredEmailOfThisMember($member_id);
        }
        
      
        /**
         * This is the function that tops up a voucher
         */
        public function actiontoppingupthisvoucher(){
            
            
           $member_id = Yii::app()->user->id;
            
            $voucher_id = $_POST['id'];
            $model=Voucher::model()->findByPk($voucher_id);
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$voucher_id);
            $voucher= Voucher::model()->find($criteria);
            
            
            $model->accepted_voucher_topup_terms = $_POST['terms_and_conditions'];
            $model->topup_date= new CDbExpression('NOW()');
            $model->toppedup_by= $member_id;
            $model->topup_value_status = strtolower('inactive');
            $model->is_voucher_toppedup = 1;
            
            if($model->isThereAnExistingUnconfrimedTopupValue($voucher_id)){
                $model->topup_value = $_POST['topup_voucher_value'] + $voucher['topup_value'];
            }else{
               $model->topup_value = $_POST['topup_voucher_value']; 
            }
            
             //get the member's membership number
            $membership_number = $this->getTheMembershipNumberOfThisMember($member_id);
            
            //get the email of this member
            $email_address = $this->getTheRegisteredEmailOfThisMember($member_id);
            
            if($model->save()){
                        //$data['success'] = 'true';
                        $msg = 'You have successfully topped up this voucher. However the topup value will be credited after the payment is confirmed';
                         header('Content-Type: application/json');
                        echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                        "voucher_number"=>$voucher['voucher_number'],
                                         "value"=>$_POST['topup_voucher_value'],
                                         "membership_number"=>$membership_number,
                                         "email"=>$email_address,
                                         
                                        )
                           );
                }else {
                    //$data['success'] = 'false';
                    $msg = 'The topup request on this voucher was not successful. Please try again or contact the customer care for assistance';
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                }
            
        }
        
        
        /**
         * This is the function that limits a voucher by category
         */
        public function actionlimitthisvouchertothiscategory(){
            
            $model = new VoucherLimitedToCategories;
            
            $voucher_id = $_POST['voucher_id'];
            $category_id = $_POST['category_id'];
            
            $expendable_limits_in_percentage = $_POST['category_expendable_limit_in_percentages'];
            
            $voucher_number = $this->getThisVoucherNumber($voucher_id);
            
            $category_name = $this->getCategoryName($category_id);
            
           if($category_id !=""){
                
                if($model->isThisCategoryNotAlreadyLimitingThisVoucher($voucher_id,$category_id) == false){
                if($model->isThisVoucherLimitingToThisCategorySuccessful($voucher_id,$category_id,$expendable_limits_in_percentage)){
                    //limiting voucher to category is successful
                  $msg = "Your request to limit voucher number '$voucher_number' to this '$category_name' category is successful ";
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                    
                }else{
                    //could not limit this voucher to this category
                    $msg = "Your request to limit voucher number '$voucher_number' to this '$category_name' category was not successful.Please contact customer care for assistance ";
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                }
                
            }else{
                //category already limiting voucher
                $msg = "'$category_name' category  is already limiting voucher number '$voucher_number'";
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
            }
                
                
                
           }else{
                
                 $msg = "Please select a category and try again.'";
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
            }
        
           
            
    
        }
        
        
        
        /**
         * This is the function that limits a voucher by a product
         */
        public function actionlimitthisvouchertothisproduct(){
            
            
            $model = new VoucherLimitedToProducts;
            
            $voucher_id = $_POST['voucher_id'];
            $product_code = $_POST['product_code'];
            
            if($product_code !=""){
                $product_id = $this->getTheProductIdOfThisProductGivenItsProductCode($product_code);
            
            $expendable_limits_in_percentage = $_POST['product_expendable_limit_in_percentages'];
            
            $voucher_number = $this->getThisVoucherNumber($voucher_id);
            
            $product_name = $this->getThisProductName($product_id);
            
            
            
                        
                if($model->isThisProductNotAlreadyLimitingThisVoucher($voucher_id,$product_id) == false){
                if($model->isThisVoucherLimitingToThisProductSuccessful($voucher_id,$product_id,$expendable_limits_in_percentage)){
                    //limiting voucher to category is successful
                  $msg = "Your request to limit voucher number '$voucher_number' to this '$product_name' product is successful ";
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                    
                }else{
                    //could not limit this voucher to this category
                    $msg = "Your request to limit voucher number '$voucher_number' to this '$product_name' product was not successful.Please contact customer care for assistance ";
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                }
                
            }else{
                //category already limiting voucher
                $msg = "'$product_name' product is already limiting voucher number '$voucher_number'";
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
            }
                
            }else{
                 $msg = "Please provide a valid product code and try again";
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
            }
            
            
                
                
        
            
        }
        
         /**
             * This is the function that gets a vouchers number
             */
            public function getThisVoucherNumber($voucher_id){
                $model = new Voucher;
                return $model->getThisVoucherNumber($voucher_id);
            }
            
            
            /**
             * This is the function that gets a actegory name
             */
            public function getCategoryName($category_id){
                $model = new Category;
                return $model->getCategoryName($category_id);
            }
            
            /**
             * This is the function that gets a product name
             */
            public function getThisProductName($product_id){
                $model = new Product;
                return $model->getThisProductName($product_id);
            }
            
            
            /**
             * This is the function that gets a product id when product is given
             */
            public function getTheProductIdOfThisProductGivenItsProductCode($product_code){
                $model = new Product;
                return $model->getTheProductIdOfThisProductGivenItsProductCode($product_code);
            }
            
            
            /**
             * This is the function that list all categories that limits a voucher
             */
            public function actionlistAllCategoriesAssignedToVoucher(){
                
                $voucher_id = $_REQUEST['voucher_id'];
               
                 $criteria = new CDbCriteria();
                 $criteria->select = '*';
                 $criteria->condition='voucher_id=:id';
                 $criteria->params = array(':id'=>$voucher_id);
                 $category= VoucherLimitedToCategories::model()->findAll($criteria);
            
             if($category===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "category" => $category)
                       );
                       
                }
                
            }
            
            
            /**
             * This is the function that changes the status of category limiter in a voucher
             */
            public function actionchangeTheStatusCategoryLimiterOfThisVoucher(){
                
                $model = new VoucherLimitedToCategories;
                
                $category_id = $_REQUEST['category_id'];
                $voucher_id = $_REQUEST['voucher_id'];
                $existing_status = $_REQUEST['existing_status'];
                
                if($existing_status == strtolower('active')){
                    $status = strtolower('inactive');
                }else{
                    $status = strtolower('active');
                }
                $category_name = $this->getCategoryName($category_id);
                
                
                
                if($model->isCategoryLimiterStatusChangeASuccess($category_id,$voucher_id,$status)){
                    if($status ==strtolower('active')){
                     $msg = "'$category_name' category limiter is activated successfully";
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                    }else{
                        $msg = "'$category_name' category limiter is deactivated successfully";
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                    }
                    
                    
                }else{
                    if($status == strtolower('active')){
                        $msg = "'$category_name' category limiter could not be activated. Please contact customer care for assistance";
                        header('Content-Type: application/json');
                        echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                    }else{
                        $msg = "'$category_name' category limiter could not be deactivated. Please contact customer care for assistance";
                        header('Content-Type: application/json');
                        echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                    }
                    
                }
            }
            
            
            
            /**
             * This is the function that changes the status of product limiter in a voucher
             */
            public function actionchangeTheStatusProductLimiterOfThisVoucher(){
                
                $model = new VoucherLimitedToProducts;
                
                $product_id = $_REQUEST['product_id'];
                $voucher_id = $_REQUEST['voucher_id'];
                $existing_status = $_REQUEST['existing_status'];
                
                if($existing_status == strtolower('active')){
                    $status = strtolower('inactive');
                }else{
                    $status = strtolower('active');
                }
                $product_name = $this->getThisProductName($product_id);
                
                
                
                if($model->isProductLimiterSatusChangeASuccess($product_id,$voucher_id,$status)){
                    if($status ==strtolower('active')){
                     $msg = "'$product_name' product limiter is activated successfully";
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                    }else{
                        $msg = "'$product_name' product limiter is deactivated successfully";
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                    }
                    
                    
                }else{
                    if($status == strtolower('active')){
                        $msg = "'$product_name' product limiter could not be activated. Please contact customer care for assistance";
                        header('Content-Type: application/json');
                        echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                    }else{
                        $msg = "'$product_name' product limiter could not be deactivated. Please contact customer care for assistance";
                        header('Content-Type: application/json');
                        echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                    }
                    
                }
            }
            
            
           /**
            * This is the function that removes category limiter from a voucher
            */ 
            public function actionremoveThisCategoryLimiterOfThisVoucher(){
                $model = new VoucherLimitedToCategories;
                
                $category_id = $_REQUEST['category_id'];
                $voucher_id = $_REQUEST['voucher_id'];
                
                $category_name = $this->getCategoryName($category_id);
                
                if($model->isTheRemovalOfThisCategoryLimiterFromVoucherSuccessful($category_id,$voucher_id)){
                    $msg = "'$category_name' category limiter is successfully removed from this voucher";
                        header('Content-Type: application/json');
                        echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                }else{
                     $msg = "'$category_name' category limiter could not be removed from this voucher.Its possible it no longer exist";
                        header('Content-Type: application/json');
                        echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                }
                
            }
            
            
            
            /**
            * This is the function that removes product limiter from a voucher
            */ 
            public function actionremoveProductLimiterOfThisVoucher(){
                $model = new VoucherLimitedToProducts;
                
                $product_id = $_REQUEST['product_id'];
                $voucher_id = $_REQUEST['voucher_id'];
                
               $product_name = $this->getThisProductName($product_id);
                
                if($model->isTheRemovalOfThisProductLimiterFromVoucherSuccessful($product_id,$voucher_id)){
                    $msg = "'$product_name' product limiter is successfully removed from this voucher";
                        header('Content-Type: application/json');
                        echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                }else{
                     $msg = "'$product_name' product limiter could not be removed from this voucher.Its possible it no longer exist";
                        header('Content-Type: application/json');
                        echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                }
                
            }
            
            
            /**
             * This is the function that list the beneficiaries of a voucher
             */
            public function actionlistofvoucherbeneficiaries(){
                
                $voucher_id = $_REQUEST['voucher_id'];
                
                 $criteria = new CDbCriteria();
                 $criteria->select = '*';
                 $criteria->condition='voucher_id=:id';
                 $criteria->params = array(':id'=>$voucher_id);
                 $voucher= WalletHasVouchers::model()->findAll($criteria);
                 
                  if($voucher===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "wallet" => $voucher)
                       );
                       
                }
                
 
            }

}
