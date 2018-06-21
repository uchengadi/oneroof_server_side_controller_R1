<?php

class MembershipFeeController extends Controller
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
				'actions'=>array('index','view','retrieveSubscriptionFeeInformation'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('DeleteThisMembershipFee','createNewMembershipFee','updateMembershipFee','ListAllmembershipfees',
                                    'AssignFeeToMembershipType','obtainMembershiptypeToFeeExtraInformation','ListTheFeesForAnyMembershipType','modifyAssignFeeToMembershiptype',
                                    'retrieveSubscriptionFeeInformation','deactivateFeeToMembershiptype'),
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
         * This is the function the creates a new membership fee
         */
        public function actioncreateNewMembershipFee(){
            
            $model = new MembershipFee;
            
             $model->amount = $_POST['amount'];
             $model->amount_monthly = $_POST['amount_monthly'];
             $model->start_date = date("Y-m-d H:i:s", strtotime($_POST['start_date'])); 
             if($this->startDateIsNotGreaterThanEnddate($_POST['start_date'],$_POST['end_date'])){
                 $model->end_date = date("Y-m-d H:i:s", strtotime($_POST['end_date'])); 
             }else{
                 $model->end_date = date("Y-m-d H:i:s", strtotime($_POST['start_date'])); 
             }
             $model->create_time = new CDbExpression('NOW()');
             $model->create_user_id = Yii::app()->user->id;
            
             if($model->save()){
                         // $result['success'] = 'true';
                          $msg = "Creation of a Membership fee of '$model->amount' was successful";
                          header('Content-Type: application/json');
                          echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Creation of a Membership fee of '$model->amount' was not successful";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
            
        }
        
        
        /**
         * This is the function that updates membership fee information
         */
        public function actionupdateMembershipFee(){
            
            $_id = $_POST['id'];
            $model= MembershipFee::model()->findByPk($_id);
            
             $model->amount = $_POST['amount'];
             $model->amount_monthly = $_POST['amount_monthly'];
             $model->start_date = date("Y-m-d H:i:s", strtotime($_POST['start_date'])); 
             if($this->startDateIsNotGreaterThanEnddate($_POST['start_date'],$_POST['end_date'])){
                 $model->end_date = date("Y-m-d H:i:s", strtotime($_POST['end_date'])); 
             }else{
                 $model->end_date = date("Y-m-d H:i:s", strtotime($_POST['start_date'])); 
             }
             
             $model->update_time = new CDbExpression('NOW()');
             $model->update_user_id = Yii::app()->user->id;
            
             if($model->save()){
                         // $result['success'] = 'true';
                          $msg = "Membership fee of '$model->amount' update was successful";
                          header('Content-Type: application/json');
                          echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Membership fee of '$model->amount' update was not successful";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
        }
        
        
        /**
         * This is the function that determines if start date is not greater than end date
         */
        public function startDateIsNotGreaterThanEnddate($this_start_date,$this_end_date){
            
            $end_date = getdate(strtotime($this_end_date));
            $start_date = getdate(strtotime($this_start_date));
            
            if(($end_date['year'] - $start_date['year'])<=0){
                if(($end_date['mon'] - $start_date['mon'])<=0){
                    if(($end_date['mday'] - $start_date['mday'])<=0){
                        return false;
                    }else{
                        return true;
                    }
                }else{
                    return true;
                }
                
            }else{
                return true;
            }
            
        }
        
        
        /**
         * This is the function that deletes a membership fee
         */
        public function actionDeleteThisMembershipFee(){
            
            //delete a membership fee
            $_id = $_POST['id'];
            $model=  MembershipFee::model()->findByPk($_id);
            if($model === null){
                $msg = "This model is null and there no data to delete";
                        header('Content-Type: application/json');
                        echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            
                            "msg" => $msg)
                       );
                                      
            }elseif($model->delete()){
                    
                      $msg = "Membership fee was successfully deleted";
                        header('Content-Type: application/json');
                        echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            
                            "msg" => $msg)
                       );
                        
                
            } else {
                    $msg = "Membership fee could not be";
                        header('Content-Type: application/json');
                        echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            
                            "msg" => $msg)
                       );
                            
                }
        }
        
        
        
        /**
         * This is the function that list all membershiop fees
         */
        public function actionListAllMembershipfees(){
            
             $userid = Yii::app()->user->id;
          
            $fee = MembershipFee::model()->findAll();
                if($fee===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "fee" => $fee
                                        
                                
                            ));
                       
                }
            
            
        }
        
        
        
        
        /**
         * This is the function that assigns fee to membership type 
         */
        public function actionAssignFeeToMembershipType(){
            
            $cmd =Yii::app()->db->createCommand();  
            $result = $cmd->delete('membershiptype_has_fees', 'membership_type_id=:typeid and fee_id=:feeid', array(':typeid'=>$_POST['membertype'], ':feeid'=>$_POST['fee'] ));
            
           // $amount = $this->getTheFeeAmount($_POST['fee']);
            $yearly_amount = $this->getTheYearlyFeeAmount($_POST['yearly_fee']);
            $member_type = $this->getTheMembershipType($_POST['membertype']);
            
            if($this->isFeeAssignedToThisType($_POST['membertype'],$_POST['fee'],$_POST['yearly_fee'],$_POST['status'])){
                 $msg = "This fees are successfully assigned to the '$member_type' membership type";
                 header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                           "msg" =>$msg,
                       ));
            }else{
                $msg = "These fees could not be assigned to the '$member_type' membership type";
                header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                           "msg" =>$msg,
                       ));
            }
            
        }
        
        
       
         /**
         * This is the function that retrieves the fee amount
         */
        public function getTheYearlyFeeAmount($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $fee= MembershipFee::model()->find($criteria);
                
                return $fee['amount'];
            
        }
        
        
        
        /**
         * This is the function that retrieves the yearly fee amount
         */
        public function getTheMonthlyFeeAmount($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $fee= MembershipFee::model()->find($criteria);
                
                return $fee['amount_monthly'];
            
        }
        
         /**
         * This is the function that retrieves the membership type
         */
        public function getTheMembershipType($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $fee= Membershiptype::model()->find($criteria);
                
                return $fee['name'];
            
        }
        
        
        /**
         * This is the function that assigns fee to membership type
         */
        public function isFeeAssignedToThisType($membership_type_id,$fee_id,$yearly_fee_id,$status){
          if($this->deactivationOfTheActiveFeeToMembertypeIsSuccessful($membership_type_id)){
               $cmd =Yii::app()->db->createCommand();
            $result = $cmd->insert('membershiptype_has_fees',
                         array('membership_type_id'=>$membership_type_id,
                                'fee_id' =>$fee_id,
                             'yearly_fee_id' =>$yearly_fee_id,
                             'status'=>$status,
                             'create_time'=>new CDbExpression('NOW()'),
                             'create_user_id'=>Yii::app()->user->id
                        )
                          
                     );
            
            if($result > 0){
                return true;
            }else{
                return false;
            }
            
               
           }else{
               return false;
           } 
         
            
        }
        
        
        /**
         * This is the function that list all fees assigned to membership type
         */
        public function actionListTheFeesForAnyMembershipType(){
            
            $userid = Yii::app()->user->id;
          
            $fee = MembershiptypeHasFees::model()->findAll();
                if($fee===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "fee" => $fee
                                        
                                
                            ));
                       
                }
            
            
        }
        
        
         /**
         * This is the function that retrieves additional information for fees assigned to membership types
         */
        public function actionobtainMembershiptypeToFeeExtraInformation(){
            
            $amount = $this->getTheYearlyFeeAmount($_POST['fee_id']);
            $monthly_amount = $this->getTheMonthlyFeeAmount($_POST['fee_id']);
            $member_type = $this->getTheMembershipType($_POST['membership_type_id']);
          
            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "yearly_fee" => $amount,
                                        "monthly_fee" => $monthly_amount,
                                        "membertype"=>$member_type
                                        
                                
                            ));
            
        }
        
        
        
        /**
         * This is the function that modifies fees to membership type assignment
         */
        public function actionmodifyAssignFeeToMembershiptype(){
            
            $amount = $this->getTheMonthlyFeeAmount($_POST['fee_id']);
            $yearly_amount = $this->getTheYearlyFeeAmount($_POST['fee_id']);
            $member_type = $this->getTheMembershipType($_POST['membership_type_id']);
            
            if($this->isActivationOfFeeAssignedToTypeSuccessful($_POST['membership_type_id'],$_POST['fee_id'],$_POST['status'])){
                 $msg = "The activation of fees assigned to '$member_type' membership type is successfully";
                 header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                           "msg" =>$msg,
                       ));
            }else{
                $msg = "The activation of the fees assigned to '$member_type' membership type is not successful";
                header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                           "msg" =>$msg,
                       ));
            }
            
            
            
            
        }
        
        
         /**
         * This is the function that deactivates fees to membership type
         */
        public function actiondeactivateFeeToMembershiptype(){
            
            $amount = $this->getTheMonthlyFeeAmount($_POST['fee_id']);
            $yearly_amount = $this->getTheYearlyFeeAmount($_POST['fee_id']);
            $member_type = $this->getTheMembershipType($_POST['membership_type_id']);
            
            if($this->isDeactivationOfFeeAssignedToTypeSuccessful($_POST['membership_type_id'],$_POST['fee_id'],$_POST['status'])){
                 $msg = "The deactivation of fees assigned to '$member_type' membership type is successfully";
                 header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                           "msg" =>$msg,
                       ));
            }else{
                $msg = "The deactivation of the fees assigned to '$member_type' membership type is not successful";
                header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                           "msg" =>$msg,
                       ));
            }
            
            
            
            
        }
        
        
        /**
         * This is the function that activates the fee assigned to membership type
         */
        public function isActivationOfFeeAssignedToTypeSuccessful($membership_type_id,$fee_id,$status){
            
            if($this->deactivationOfTheActiveFeeToMembertypeIsSuccessful($membership_type_id)){
                $cmd =Yii::app()->db->createCommand();
                $result = $cmd->update('membershiptype_has_fees',
                         array('status'=>$status,
                             'update_time'=>new CDbExpression('NOW()'),
                             'update_user_id'=>Yii::app()->user->id
                        ),
                    ("membership_type_id=$membership_type_id && fee_id=$fee_id")
                          
                     );
            
            if($result > 0){
                return true;
            }else{
                return false;
            }
                
                
            }else{
                return false;
            }
            
        }
        
        
        /**
         * This is the function that activates the fee assigned to membership type
         */
        public function isDeactivationOfFeeAssignedToTypeSuccessful($membership_type_id,$fee_id,$status){
            
            
                $cmd =Yii::app()->db->createCommand();
                $result = $cmd->update('membershiptype_has_fees',
                         array('status'=>$status,
                             'update_time'=>new CDbExpression('NOW()'),
                             'update_user_id'=>Yii::app()->user->id
                        ),
                    ("membership_type_id=$membership_type_id && fee_id=$fee_id")
                          
                     );
            
            if($result > 0){
                return true;
            }else{
                return false;
            }
                
                
            
            
        }
        
        
        
        
        /**
         * This is the function that deactivates  fee to a membership type
         */
        public function deactivationOfTheActiveFeeToMembertypeIsSuccessful($membership_type_id){
            if($this->isThisMembershipAlreadyWithFee($membership_type_id)){
                $cmd =Yii::app()->db->createCommand();
                $result = $cmd->update('membershiptype_has_fees',
                         array('status'=>'inactive',
                             'update_time'=>new CDbExpression('NOW()'),
                             'update_user_id'=>Yii::app()->user->id
                        ),
                    ("membership_type_id=$membership_type_id")
                          
                     );
            
            if($result > 0){
                return true;
            }else{
                return false;
            }
            }else{
                return true;
            }
            
            
        }
        
        
        /**
         * This is the function that determines if a membership type already has a fee assigned
         */
        public function isThisMembershipAlreadyWithFee($membership_type_id){
             $cmd =Yii::app()->db->createCommand();
            $cmd->select('COUNT(*)')
                    ->from('membershiptype_has_fees')
                    ->where("membership_type_id = $membership_type_id");
                $result = $cmd->queryScalar();
                
                if($result > 0){
                    return true;
                }else{
                    return false;
                }
        }
        
        
        
        /**
         * This is the function that retrieves information about membership type fees
         */
        public function actionretrieveSubscriptionFeeInformation(){
            
            $membership_type_id = $_REQUEST['membership_type_id'];
            $number_of_years = $_REQUEST['number_of_years'];
            $number_of_months = $_REQUEST['number_of_months'];
            $type = $_REQUEST['type'];
            
            if($type == strtolower('yearly')){
                 $gross = $this->getTheGrossAmountOfThisMembershipType($membership_type_id,$number_of_years);
                 $discount = $this->getThisApplicableDiscountForThisMembershiptype($membership_type_id,$number_of_years);
                 $net = $gross - $discount;
            }else if($type == strtolower('monthly')){
                $gross = $this->getTheGrossAmountOfThisMembershipTypeForMonthly($membership_type_id,$number_of_months);
                 $discount = $this->getThisApplicableDiscountForThisMembershiptypeForMonthly($membership_type_id,$number_of_months);
                 $net = $gross - $discount;
            }
            
            //get the membership type code
            $membership_code = $this->getThisMembershipTypeCode($membership_type_id);
            
           
            
            
            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "gross" => $gross,
                                     "discounted_amount"=>$discount,
                                     "net_amount"=>$net,
                                     "membership_code"=>$membership_code
                                        
                                
                            ));
      
        }
        
        /**
         * This is the function that retrieves the membership type code
         */
        public function getThisMembershipTypeCode($membership_type_id){
            $model = new Membershiptype;
            return $model->getThisMembershipTypeCode($membership_type_id);
        }
        
        /**
         * This is the function that gets monthly gross subsciption
         */
        public function getTheGrossAmountOfThisMembershipTypeForMonthly($membership_type_id,$number_of_months){
            $model = new MembershiptypeHasFees;
            return $model->getTheGrossAmountOfThisMembershipTypeForMonthly($membership_type_id,$number_of_months);
        }
        
        
        /**
         * This is the function that gets the discounts for monthly subscription
         */
        public function getThisApplicableDiscountForThisMembershiptypeForMonthly($membership_type_id,$number_of_months){
            $model = new PlatformSettings;
            return $model->getThisApplicableDiscountForThisMembershiptypeForMonthly($membership_type_id,$number_of_months);
        }
        
        
        /**
         * This is the function that gets the gross amount for a membership type
         */
        public function getTheGrossAmountOfThisMembershipType($membership_type_id,$number_of_years){
            
            $model = new MembershiptypeHasFees;
            
            return $model->getTheGrossAmountOfThisMembershipType($membership_type_id,$number_of_years);
        }
        
        
        /**
         * This is the function that gets an applicable discount for a membership type
         */
        public function getThisApplicableDiscountForThisMembershiptype($membership_type_id,$nunmber_of_years){
            $model = new PlatformSettings;
            
            return $model->getThisApplicableDiscountForThisMembershiptype($membership_type_id,$nunmber_of_years);
        }
}
