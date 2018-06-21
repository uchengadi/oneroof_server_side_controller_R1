<?php

class WalletController extends Controller
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
				'actions'=>array('create','update','listAllVouchersForAMemberWallet','listAllWallets','allocateFundToAWallet',
                                    'allocateFundToAConnectedMemberWallet','activateAllocateFundToAConnectedMemberWallet','suspendAllocateFundToAConnectedMemberWallet',
                                    'unsuspendAllocateFundToAConnectedMemberWallet','removeAConnectedMemberWallet','topupFundToAConnectedMemberWallet',
                                    'getTheNameOfTheOwnerOfThisWallet','listproductsexpendablevaluesonawallet','listcategoriesexpendablevaluesonawallet',
                                    'getTheAnalysisOfThisWallet','getTheTotalAvailableAndSuspendedValuesInTheWallet'),
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
         * This is the function that list all wallet
         */
        public function actionlistAllWallets(){
            
            $wallet = Wallet::model()->findAll();
                if($wallet===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"assigned" => $assigned,
                            "wallet" => $wallet)
                       );
                       
                }
        }
        
        
        /**
         * This is the function that list all vouchers in a awaalet
         */
        public function actionlistAllVouchersForAMemberWallet(){
            
            
            $model = new Wallet;
            
            $member_id = Yii::app()->user->id;
            
            $wallet_id = $model->getTheWalletIdOfMember($member_id);
          
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='wallet_id=:id';
            $criteria->params = array(':id'=>$wallet_id);
            $voucher= WalletHasVouchers::model()->findAll($criteria);
            
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
         * This is the function that allocates funds to own wallet
         */
        public function actionallocateFundToAWallet(){
            
            
            $model = new Wallet;
            $member_id = Yii::app()->user->id;
            $voucher_id = $_POST['id'];
            $voucher_value = $_POST['voucher_value'];
            $remaining_voucher_value = $_POST['remaining_voucher_value'];
            $allocated_voucher_value = $_POST['allocated_voucher_value'];
            $terms_and_conditions = $_POST['terms_and_conditions'];
            $usage_commencement_date = date("Y-m-d H:i:s", strtotime($_POST['usage_commencement_date']));
            
            if($remaining_voucher_value >= $allocated_voucher_value){
                if($model->doMemberHasWallet($member_id)){
                    $wallet_id = $model->getTheWalletIdOfMember($member_id);
                    if($this->hasThisVoucherFundedThisWalletBefore($wallet_id,$voucher_id)==false){
                        if($this->isTheFundingOfMemberWalletASucess($wallet_id,$voucher_id,$member_id,$remaining_voucher_value,$voucher_value,$allocated_voucher_value,$terms_and_conditions,$usage_commencement_date)){
                        if($this->isVoucherFundPositionModifiedSuccessfully($voucher_id,$voucher_value,$remaining_voucher_value,$allocated_voucher_value)){
                            $msg = "Congratulations,you just received the sum of =N=$allocated_voucher_value into your wallet";
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                                );
                            
                        }else{
                            //wallet funded but voucher fund position not adjusted
                             $msg = "Congratulations,you just received the sum of =N=$allocated_voucher_value into your wallet but the funding voucher fund position was not adjusted. Please contact customer care to assist you with this";
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                                );
                        }
                        
                    }else{
                        //funding of a member wallet was not successful
                         $msg = "Funding of own wallet was not successful. Please contact customer care for assistance";
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                                );
                    }
                        
                    }else{
                        $msg = "This voucher had already allocated some funds to your wallet. If you need to topup the fund, please use the topup value facility on the allocate allowances module";
                        header('Content-Type: application/json');
                        echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                        
                    }
                    
                    
                    
                }else{
                    if($model->isTheCreationOfMemberWalletSuccessful($member_id)){
                        $wallet_id = $model->getTheWalletIdOfMember($member_id);
                        if($this->hasThisVoucherFundedThisWalletBefore($wallet_id,$voucher_id)==false){
                            if($this->isTheFundingOfMemberWalletASucess($wallet_id,$voucher_id,$member_id,$remaining_voucher_value,$voucher_value,$allocated_voucher_value,$terms_and_conditions,$usage_commencement_date)){
                            if($this->isVoucherFundPositionModifiedSuccessfully($voucher_id,$voucher_value,$remaining_voucher_value,$allocated_voucher_value)){
                             $msg = "Congratulations,you just received the sum of =N=$allocated_voucher_value into your wallet";
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                                );
                            
                            }else{
                               //wallet funded but voucher fund position not adjusted
                             $msg = "Congratulations,you just received the sum of =N=$allocated_voucher_value into your wallet but the funding voucher fund position was not adjusted. Please contact customer care to assist you with this";
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                                );
                            }
                    }else{
                        //funding of a member wallet was not successful
                         $msg = "Funding of own wallet was not successful. Please contact customer care for assistance";
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                                );
                        
                    }
                            
                        }else{
                           $msg = "This voucher had already allocated some funds to your wallet. If you need to topup the fund, please use the topup value facility on the allocate allowances module";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                            
                        }
                        
                    }else{
                        //creating member wallet is not successful
                        $msg = "Could not create wallet for you.Please contact customer care for assistance";
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                                );
                    }
                    
                }
                
                
            }else{
                //the allocated amount cannot be greater than the remainin voucher amount
                 $msg = "The allocated amount cannot be greater than the remaining voucher value.Correct this and try again'";
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
            }
        }
        
        /**
         * This is the functioon that funds a members wallet
         */
        public function isTheFundingOfMemberWalletASucess($wallet_id,$voucher_id,$member_id,$remaining_voucher_value,$voucher_value,$allocated_voucher_value,$terms_and_conditions,$usage_commencement_date){
            $model = new WalletHasVouchers;
            return $model->isTheFundingOfMemberWalletASucess($wallet_id,$voucher_id,$member_id,$remaining_voucher_value,$voucher_value,$allocated_voucher_value,$terms_and_conditions,$usage_commencement_date);
        }
        
        /**
         * This is the function that ensures that the voucher fund position is adjusted after wallet funding
         */
        public function isVoucherFundPositionModifiedSuccessfully($voucher_id,$voucher_value,$remaining_voucher_value,$allocated_voucher_value){
            
            $model = new Voucher;
            return $model->isVoucherFundPositionModifiedSuccessfully($voucher_id,$voucher_value,$remaining_voucher_value,$allocated_voucher_value);
            
        }
        
        
        /**
         * This is the function that funds a connected members wallet
         */
        public function actionallocateFundToAConnectedMemberWallet(){
            
            $model = new Wallet;
            $member_id = $_POST['name'];
            $voucher_id = $_POST['voucher_id'];
            $voucher_value = $_POST['voucher_value'];
            $remaining_voucher_value = $_POST['remaining_voucher_value'];
            $allocated_voucher_value = $_POST['allocated_voucher_value'];
            $terms_and_conditions = $_POST['terms_and_conditions'];
            $usage_commencement_date = date("Y-m-d H:i:s", strtotime($_POST['usage_commencement_date']));
            
            $member_name = $this->getTheNameOfThisMember($member_id);
            
            
                if($remaining_voucher_value >= $allocated_voucher_value){
                if($model->doMemberHasWallet($member_id)){
                    $wallet_id = $model->getTheWalletIdOfMember($member_id);
                    if($this->hasThisVoucherFundedThisWalletBefore($wallet_id,$voucher_id)==false){
                        if($this->isTheFundingOfMemberWalletASucess($wallet_id,$voucher_id,$member_id,$remaining_voucher_value,$voucher_value,$allocated_voucher_value,$terms_and_conditions,$usage_commencement_date)){
                        if($this->isVoucherFundPositionModifiedSuccessfully($voucher_id,$voucher_value,$remaining_voucher_value,$allocated_voucher_value)){
                            $msg = "Congratulations,you just allocated the sum of =N=$allocated_voucher_value to $member_name";
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                                );
                            
                        }else{
                            //wallet funded but voucher fund position not adjusted
                             $msg = "Congratulations,you just allocated the sum of =N=$allocated_voucher_value to $member_name but the funding voucher fund position was not adjusted. Please contact customer care to assist you with this";
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                                );
                        }
                        
                    }else{
                        //funding of a member wallet was not successful
                         $msg = "Funding of $member_name wallet was not successful. Please contact customer care for assistance";
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                                );
                    }
                    }else{
                         $msg = "An allocation from this voucher to this member wallet is already in effect. If you need to topup the fund, please use the topup value facility instead";
                        header('Content-Type: application/json');
                        echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                    }
                    
                   
                    
                }else{
                    if($model->isTheCreationOfMemberWalletSuccessful($member_id)){
                        $wallet_id = $model->getTheWalletIdOfMember($member_id);
                        if($this->hasThisVoucherFundedThisWalletBefore($wallet_id,$voucher_id)==false){
                            if($this->isTheFundingOfMemberWalletASucess($wallet_id,$voucher_id,$member_id,$remaining_voucher_value,$voucher_value,$allocated_voucher_value,$terms_and_conditions,$usage_commencement_date)){
                            if($this->isVoucherFundPositionModifiedSuccessfully($voucher_id,$voucher_value,$remaining_voucher_value,$allocated_voucher_value)){
                             $msg = "Congratulations,you just allocated the sum of =N=$allocated_voucher_value to $member_name";
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                                );
                            
                            }else{
                               //wallet funded but voucher fund position not adjusted
                             $msg = "Congratulations,you just allocated the sum of =N=$allocated_voucher_value to $member_name but the funding voucher fund position was not adjusted. Please contact customer care to assist you with this";
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                                );
                            }
                    }else{
                        //funding of a member wallet was not successful
                         $msg = "Funding of $member_name wallet was not successful. Please contact customer care for assistance";
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                                );
                        
                    }
                            
                            
                            
                        }else{
                             $msg = "An allocation from this voucher to this member wallet is already in effect. If you need to topup the fund, please use the topup value facility instead";
                             header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                            );
                            
                        }
                        
                    }else{
                        //creating member wallet is not successful
                        $msg = "Could not create wallet for $member_name.Please contact customer care for assistance";
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                                );
                    }
                    
                }
                
                
            }else{
                //the allocated amount cannot be greater than the remainin voucher amount
                 $msg = "The allocated amount cannot be greater than the remaining voucher value.Correct this and try again'";
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
            }
                
                
            
            
        }
        
        
        /**
         * This is the function that determines if a voucher had funded a wallet before
         */
        public function hasThisVoucherFundedThisWalletBefore($wallet_id,$voucher_id){
            $model = new WalletHasVouchers;
            return $model->hasThisVoucherFundedThisWalletBefore($wallet_id,$voucher_id);
        }
        
        /**
         * This is the function that gets a member name
         */
        public function getTheNameOfThisMember($member_id){
            $model = new Members;
            return $model->getTheNameOfThisMember($member_id);
        }
        
         /**
         * This is the function that activate the funds to a connected members wallet
         */
        public function actionactivateAllocateFundToAConnectedMemberWallet(){
            
            $model = new WalletHasVouchers;
            $wallet_id = $_POST['wallet_id'];
            $voucher_id = $_POST['voucher_id'];
            $usage_commencement_date = date("Y-m-d H:i:s", strtotime($_POST['usage_commencement_date']));
            $actual_voucher_share = $_POST['allocated_voucher_value'];
                     
            $wallet_owner = $this->getTheNameOfTheWalletOwner($wallet_id);
            if($model->isTheActivationOfThisFundSuccessful($wallet_id,$voucher_id,$usage_commencement_date)){
                if($this->isTheItemsLimitInTheWalletSet($wallet_id,$voucher_id,$actual_voucher_share)){
                    $msg = "The allocated fund to $wallet_owner is activated successfully";
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                }else{
                    $msg = "The allocated fund to $wallet_owner is activated successfully but the wallet limit sets were not successful. Please contact customer care for assistance";
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                }
                
            }else{
                $msg = "The attempt to activate the allocated fund to $wallet_owner was not successfully. Please contact customer car for assistance";
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
            }
            
            
        }
        
        
        /**
         * This is the function that confirms the success of a wallet limit set
         */
        public function isTheItemsLimitInTheWalletSet($wallet_id,$voucher_id,$actual_voucher_share){
            $model = new WalletHasVouchers;
            if($model->isTheVoucherLimited($voucher_id)){
                $counter = 0;
                if($model->isThisVoucherLimitedByProduct($voucher_id)){
                   //get all the products that limited this voucher
                    $criteria = new CDbCriteria();
                    $criteria->select = '*';
                    $criteria->condition='voucher_id=:voucherid';
                    $criteria->params = array(':voucherid'=>$voucher_id);
                    $vouchers= VoucherLimitedToProducts::model()->findAll($criteria);
                    foreach($vouchers as $voucher){
                       if($this->isTheLimitingOfThisWalletByProductSuccessful($voucher['product_id'],$voucher['expendable_limits_in_percentage'],$actual_voucher_share,$wallet_id)){
                            $counter = $counter + 1;
                        }
                    }
                    
                }
               if($model->isThisVoucherLimitedByCategory($voucher_id)){
                    
                   //get all the category that limited this voucher
                    $criteria = new CDbCriteria();
                    $criteria->select = '*';
                    $criteria->condition='voucher_id=:voucherid';
                    $criteria->params = array(':voucherid'=>$voucher_id);
                    $vouchers= VoucherLimitedToCategories::model()->findAll($criteria);
                    foreach($vouchers as $voucher){
                       if($this->isTheLimitingOfThisWalletByCategorySuccessful($voucher['category_id'],$voucher['expendable_limits_in_percentage'],$actual_voucher_share,$wallet_id)){
                            $counter = $counter + 1;
                        }
                    }
                    
                }
                if($counter >0){
                    return true;
                }else{
                    return false;
                }
                
            }else{
                return true;
            }
        }
        
        
        /**
         * This is the function that limits the expendability a product in a wallet
         */
        public function isTheLimitingOfThisWalletByProductSuccessful($product_id,$expendable_limits_in_percentage,$actual_voucher_share,$wallet_id){
            $model = new WalletHasProductExpendableLimit;
            return $model->isTheLimitingOfThisWalletByProductSuccessful($product_id,$expendable_limits_in_percentage,$actual_voucher_share,$wallet_id);
            
        }
        
        
        /**
         * This is the function that limits the expendability a category in a wallet
         */
        public function isTheLimitingOfThisWalletByCategorySuccessful($category_id,$expendable_limits_in_percentage,$actual_voucher_share,$wallet_id){
            $model = new WalletHasCategoryExpendableLimit;
            return $model->isTheLimitingOfThisWalletByCategorySuccessful($category_id,$expendable_limits_in_percentage,$actual_voucher_share,$wallet_id);
            
        }
        
         /**
         * This is the function that suspends the funds to a connected members wallet
         */
        public function actionsuspendAllocateFundToAConnectedMemberWallet(){
            
            $model = new WalletHasVouchers;
            $wallet_id = $_POST['wallet_id'];
            $voucher_id = $_POST['voucher_id'];
            $usage_commencement_date = date("Y-m-d H:i:s", strtotime($_POST['usage_commencement_date']));
         
            $wallet_owner = $this->getTheNameOfTheWalletOwner($wallet_id);
            if($model->isTheSuspensionOfThisFundSuccessful($wallet_id,$voucher_id,$usage_commencement_date)){
                $msg = "The allocated fund to $wallet_owner is suspended successfully";
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
            }else{
                $msg = "The attempt to suspend the allocated fund to $wallet_owner was not successfully. Please contact customer car for assistance";
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
            }
            
        }
        
        
         /**
         * This is the function that unsuspends the funds to a connected members wallet
         */
        public function actionunsuspendAllocateFundToAConnectedMemberWallet(){
            
            $model = new WalletHasVouchers;
            $wallet_id = $_POST['wallet_id'];
            $voucher_id = $_POST['voucher_id'];
            $usage_commencement_date = date("Y-m-d H:i:s", strtotime($_POST['usage_commencement_date']));
         
            $wallet_owner = $this->getTheNameOfTheWalletOwner($wallet_id);
            if($model->isTheActivationOfThisFundSuccessful($wallet_id,$voucher_id,$usage_commencement_date)){
                $msg = "The allocated fund to $wallet_owner is unsuspended successfully";
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
            }else{
                $msg = "The attempt to unsuspend the allocated fund to $wallet_owner was not successfully. Please contact customer car for assistance";
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
            }
            
        }
        
        
          /**
         * This is the function that removes a connected members wallet from the beneficiary list
         */
        public function actionremoveAConnectedMemberWallet(){
            
            $model = new WalletHasVouchers;
            $wallet_id = $_POST['wallet_id'];
            $voucher_id = $_POST['voucher_id'];
            
            $wallet_owner = $this->getTheNameOfTheWalletOwner($wallet_id);
            if($model->isTheRemovalOfThisFundFromWalletSuccessful($wallet_id,$voucher_id)){
                $msg = "The allocated fund to $wallet_owner is successfully removed from the wallet";
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
            }else{
                $msg = "The attempt to remove the allocated fund to $wallet_owner was not successfully. Please contact customer car for assistance";
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
            }
            
        }
        
        
        
          /**
         * This is the function that topup funds to a connected members wallet
         */
        public function actiontopupFundToAConnectedMemberWallet(){
            
            $model = new WalletHasVouchers;
            $wallet_id = $_POST['wallet_id'];
            $voucher_id = $_POST['voucher_id'];
            $usage_commencement_date = date("Y-m-d H:i:s", strtotime($_POST['usage_commencement_date']));
            $topup_voucher_value = $_POST['topup_voucher_value'];
            $allocated_voucher_value = $_POST['allocated_voucher_value'];
            $voucher_value = $_POST['voucher_value'];
            $remaining_voucher_value = $_POST['remaining_voucher_value'];
            
           $new_remaining_voucher_value =$remaining_voucher_value - $topup_voucher_value; 
           $new_allocated_voucher_value = $allocated_voucher_value + $topup_voucher_value;
           $new_available_balance =$model->getTheAvailableBalanceOfThisVoucherInTheWallet($wallet_id,$voucher_id) + $topup_voucher_value;
           
          $member_name = $this->getTheNameOfTheWalletOwner($wallet_id);
           if($remaining_voucher_value>=$topup_voucher_value){
               
               if($model->isTheFundTopupOfMemberWalletASucess($wallet_id,$voucher_id,$new_allocated_voucher_value,$voucher_value,$usage_commencement_date,$new_available_balance)){
                        if($this->isVoucherFundPositionModifiedSuccessfully($voucher_id,$voucher_value,$remaining_voucher_value,$topup_voucher_value)){
                            if($this->isTheItemsLimitInTheWalletSet($wallet_id,$voucher_id,$topup_voucher_value)){
                               $msg = "Congratulations,you just topped up the sum of =N=$topup_voucher_value to $member_name";
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                                );
                                
                            }else{
                              $msg = "Congratulations,you just topped up the sum of =N=$topup_voucher_value to $member_name but do note that the wallet expendible limits were not modified. Please report this incidence to the customer care";
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                                );
                            }
                            
                            
                        }else{
                            //wallet funded but voucher fund position not adjusted
                             $msg = "Congratulations,you just topped up the sum of =N=$topup_voucher_value to $member_name but the funding voucher fund position was not adjusted. Please contact customer care to assist you with this";
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                                );
                        }
                        
                    }else{
                        //funding of a member wallet was not successful
                         $msg = "The topup of $member_name wallet was not successful. Please contact customer care for assistance";
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                                );
                    }
               
               
               
           }else{
               //the topup value cannot be greater than the remaining amount
                 $msg = "The topup amount cannot be greater than the remaining voucher value.Correct this and try again'";
                     header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
           }
            
        }
        
        
        /**
         * This is the function that gets the name of a wallet owner
         */
        public function actiongetTheNameOfTheOwnerOfThisWallet(){
            
            $model = new Wallet;
            $wallet_id = $_REQUEST['wallet_id'];
            
            $name = $model->getTheNameOfTheWalletOwner($wallet_id);
            
            if($name===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "name" => $name)
                       );
                       
                }
            
        }
        
        /**
         * This is the function that get the name of a wallet owner
         */
        public function getTheNameOfTheWalletOwner($wallet_id){
            $model = new Wallet;
            return $model->getTheNameOfTheWalletOwner($wallet_id);
        }
        
        
        /**
         * This is the function that list all products limiters in a walet
         */
        public function actionlistproductsexpendablevaluesonawallet(){
            
            $model = new Wallet;
            $member_id = Yii::app()->user->id;
            
             $operation = strtolower($_REQUEST['operation']);
             
             //get the wallet id of this member
             $wallet_id = $model->getTheWalletIdOfMember($member_id);
             
            $all_limiters = [];
            
            //get all the product limiters in a member wallet
            
            $all_limiters = [];
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='wallet_id=:id';
            $criteria->params = array(':id'=>$wallet_id);
            $limiters= WalletHasProductExpendableLimit::model()->findAll($criteria);
            
            if($operation == 'expendable'){
                foreach($limiters as $limiter){
                    if($this->isTheStatusOfThisProductLimiterActive($limiter['wallet_id'],$limiter['product_id'])){
                        $all_limiters[] = $limiter;
                    }
                }
            }else if($operation =='suspended'){
                foreach($limiters as $limiter){
                    if($this->isTheStatusOfThisProductLimiterSuspended($limiter['wallet_id'],$limiter['product_id'])){
                        $all_limiters[] = $limiter;
                    }
                }
            }
            
            if($limiters===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "wallet" => $all_limiters)
                       );
                       
                }
            
            
            
        }
        
        
        
        /**
         * This is the function that list all categories limiters in a walet
         */
        public function actionlistcategoriesexpendablevaluesonawallet(){
            
            $model = new Wallet;
            $member_id = Yii::app()->user->id;
            
             $operation = strtolower($_REQUEST['operation']);
             
             //get the wallet id of this member
             $wallet_id = $model->getTheWalletIdOfMember($member_id);
             
            $all_limiters = [];
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='wallet_id=:id';
            $criteria->params = array(':id'=>$wallet_id);
            $limiters= WalletHasCategoryExpendableLimit::model()->findAll($criteria);
            
            if($operation == 'expendable'){
                foreach($limiters as $limiter){
                    if($this->isTheStatusOfThisCategoryLimiterActive($limiter['wallet_id'],$limiter['category_id'])){
                        $all_limiters[] = $limiter;
                    }
                }
            }else if($operation =='suspended'){
                foreach($limiters as $limiter){
                    if($this->isTheStatusOfThisCategoryLimiterSuspended($limiter['wallet_id'],$limiter['category_id'])){
                        $all_limiters[] = $limiter;
                    }
                }
            }
            
            if($limiters===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "wallet" => $all_limiters)
                       );
                       
                }
            
            
            
        }
        
        /**
         * This is the function that determines if the status of a category limiter item is active
         */
        public function isTheStatusOfThisProductLimiterActive($wallet_id, $product_id){
            $model = new WalletHasVouchers;
            return $model->isTheStatusOfThisProductLimiterActive($wallet_id, $product_id);
        }
        
        
         /**
         * This is the function that determines if the status of a category limiter item is suspended
         */
        public function isTheStatusOfThisProductLimiterSuspended($wallet_id, $product_id){
            $model = new WalletHasVouchers;
            return $model->isTheStatusOfThisProductLimiterSuspended($wallet_id, $product_id);
        }
        
        
         /**
         * This is the function that determines if the status of a category limiter item is active
         */
        public function isTheStatusOfThisCategoryLimiterActive($wallet_id, $category_id){
            $model = new WalletHasVouchers;
            return $model->isTheStatusOfThisCategoryLimiterActive($wallet_id, $category_id);
        }
        
         /**
         * This is the function that determines if the status of a category limiter item is suspended
         */
        public function isTheStatusOfThisCategoryLimiterSuspended($wallet_id, $category_id){
            $model = new WalletHasVouchers;
            return $model->isTheStatusOfThisCategoryLimiterSuspended($wallet_id, $category_id);
        }
        
        /**
         * This is the function that gets all the unencumbered fund in a wallet
         */
        public function actiongetTheAnalysisOfThisWallet(){
           
            $model = new WalletHasVouchers;
            $member_id = Yii::app()->user->id;
            $wallet_id = $this->getTheWalletIdOfMember($member_id);
            $operation = strtolower($_REQUEST['operation']);
            
            if($operation == 'expendable'){
                //get the available usable fund
                $available_usable_fund = $model->getTheAvailableUsableFunds($wallet_id,$operation);
                
                //get the funds for the future
                
                $available_funds_for_future = $model->getTheAvailableFundsForTheFuture($wallet_id,$operation);
                
                
            }else if($operation == 'suspended'){
                
                //get the available uasble but suspended fund
                $available_usable_fund = $model->getTheAvailableUsableFunds($wallet_id,$operation);
                
                //get the suspended fund for the future
                
                $available_funds_for_future = $model->getTheAvailableFundsForTheFuture($wallet_id,$operation);
            }
            
             header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "available_usable_fund" => $available_usable_fund,
                                        "available_funds_for_future"=>$available_funds_for_future    
                         )
                           );
            
        }
               
        /**
         * This is the function that gets the wallet id of a member
         */
        public function getTheWalletIdOfMember($member_id){
            $model = new Wallet;
            return $model->getTheWalletIdOfMember($member_id);
        }
        
        
        /**
         * This is the function that obtains both the available and suspended balances in a wallet
         */
        public function actiongetTheTotalAvailableAndSuspendedValuesInTheWallet(){
           $model = new WalletHasVouchers;
           
           $member_id = Yii::app()->user->id;
           
           $wallet_id = $this->getTheWalletIdOfMember($member_id);
            
            //get the total wallet available value
            $wallet_available_value = $model->getTheToTalAvailableValueInThisWallet($wallet_id);
            
            //get the total suspended value in this wallet
            $wallet_suspended_value = $model->getTheTotalSuspendedValueInThisWallet($wallet_id);
            
            //get the total value in a wallet
            $wallet_total_value = $wallet_available_value + $wallet_suspended_value;
            
            header('Content-Type: application/json');
                     echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "wallet_available_value" => $wallet_available_value,
                                        "wallet_suspended_value"=>$wallet_suspended_value,
                                        "wallet_total_value"=>$wallet_total_value
                         )
                           );
            
        }
}
