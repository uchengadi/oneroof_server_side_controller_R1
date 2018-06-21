<?php

class MembershipSubscriptionController extends Controller
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
				'actions'=>array('index','view','ListMembershipSubscription'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('obtainSubscriptionExtraInformation','DeleteThisSubscription','addNewMembershipSubscription','updateMembershipSubscription',
                                    'activateMembershipSubscription','deactivateMembershipSubscription','suspendMembershipSubscription','ListMembershipSubscription',
                                    'extendMembershipSubscription','renewMembershipSubscription','DateTesting'),
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
         * This is the function that adds a membership subscription
         */
        public function actionaddNewMembershipSubscription(){
            
            $model=new MembershipSubscription;
            
            if(is_numeric($_POST['name'])){
                $model->member_id = $_POST['name'];
            }else{
                 $model->member_id = $_POST['member_id'];
            }
            if(is_numeric($_POST['membertype'])){
                $model->membership_type_id = $_POST['membertype'];
            }else{
                 $model->membership_type_id = $_POST['membership_type_id'];
            }
            $model->status = strtolower('inactive');
            $model->number_of_years = $_POST['number_of_years'];
            $model->membership_start_date = date("Y-m-d H:i:s", strtotime($_POST['membership_start_date'])); 
            $model->membership_end_date = $this->getTheSubscriptionEndDate($_POST['membership_start_date'],$_POST['number_of_years']);
            $model->subscription_initiation_date = new CDbExpression('NOW()');
            $model->subscription_initiated_by = Yii::app()->user->id;
            
            //get the name of this member
            $member_name = $this->getTheNameOfThisMember($model->member_id);
             if($model->save()){
                         // $result['success'] = 'true';
                          $msg = "Subscription of '$member_name' is successful and its waiting for activation";
                          header('Content-Type: application/json');
                          echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Subscription of '$member_name' is unsuccessful. Contact the Support Desk";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
                   
            
        }
        
        
        /**
         * This is the function that retrieves the name of the member
         */
        public function getTheNameOfThisMember($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $user= Members::model()->find($criteria);
                
                $name = $user['lastname'] .  ' '. $user['middlename'] . ' ' . $user['firstname'] ;
                
                return $name;
            
        }
        
        
        
        /**
         * This is the function that updates subscription information
         */
        public function actionupdateMembershipSubscription(){
            $_id = $_POST['id'];
            $model=  MembershipSubscription::model()->findByPk($_id);
            
             if(is_numeric($_POST['name'])){
                $model->member_id = $_POST['name'];
            }else{
                 $model->member_id = $_POST['member_id'];
            }
            if(is_numeric($_POST['membertype'])){
                $model->membership_type_id = $_POST['membertype'];
            }else{
                 $model->membership_type_id = $_POST['membership_type_id'];
            }
            $model->status = strtolower('inactive');
            $model->number_of_years = $_POST['number_of_years'];
            $model->membership_start_date = date("Y-m-d H:i:s", strtotime($_POST['membership_start_date'])); 
            $model->membership_end_date = $this->getTheSubscriptionEndDate($_POST['membership_start_date'],$_POST['number_of_years']);
            $model->subscription_initiation_date = new CDbExpression('NOW()');
            $model->subscription_initiated_by = Yii::app()->user->id;
            
            //get the name of this member
            $member_name = $this->getTheNameOfThisMember($model->member_id);
             if($model->save()){
                         // $result['success'] = 'true';
                          $msg = "Subscription of '$member_name' is successfully updated and its waiting for activation";
                          header('Content-Type: application/json');
                          echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Subscription of '$member_name' was not unsuccessfully updated. Contact the Support Desk";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
        }
	
        
        
        /**
         * This is the function that gets a subscription end date
         */
        public function getTheSubscriptionEndDate($membership_start_date,$number_of_months){
            $model = new MembershipSubscription;
            
            return $model->getTheSubscriptionEndDate($membership_start_date,$number_of_months);
        }
        
        /**
         * just testing stuff date
         */
        public function actionDateTesting(){
            $new_date = $this->getTheSubscriptionEndDate($membership_start_date="16/02/2026",$number_of_months=24);
            header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "date" => $new_date)
                           );
        }
        
        
        /**
         * This is the function that activates an inactive subscription
         */
        public function actionactivateMembershipSubscription(){
            
            $id = $_POST['id'];
            $status = strtolower($_POST['status']);
            $user_name = $this->getTheNameOfThisMember($_POST['member_id']);
            $membership_start_date  = new CDbExpression('NOW()');
            $membership_end_date = $this->getTheSubscriptionEndDate($_POST['membership_start_date'],$_POST['number_of_months']);
            
            if($status == 'active'){
                $msg = "'$user_name' subscription is currently active " ;
                header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() != 0,
                               "msg"=>$msg
                                  
                ));
                
            }else{
                if($this->isSubscriptionActivationSuccessful($id,$membership_start_date,$membership_end_date)){
                    
                     $msg = "The Subscription of '$user_name' is successful" ;
                     header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                               "msg"=>$msg
                                  
                        ));
                    
                }else{
                     $msg = "The attempt to activate the Subscription of '$user_name' was  not successful" ;
                     header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() != 0,
                               "msg"=>$msg
                                  
                ));
                }
            
            
            
        }
        }
        
        
        
        /**
         * This is the function that deactivates an inactive subscription
         */
        public function actiondeactivateMembershipSubscription(){
            
            $id = $_POST['id'];
            $status = strtolower($_POST['status']);
            $user_name = $this->getTheNameOfThisMember($_POST['member_id']);
            
            if($status == 'inactive'){
                $msg = "'$user_name' subscription is currently inactive " ;
                header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() != 0,
                               "msg"=>$msg
                                  
                ));
                
            }else{
                if($this->isSubscriptionDeactivationSuccessful($id)){
                    
                     $msg = "The Subscription of '$user_name' is successful deactivated" ;
                     header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                               "msg"=>$msg
                                  
                        ));
                    
                }else{
                     $msg = "The attempt to deactivate the Subscription of '$user_name' was  not successful" ;
                     header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() != 0,
                               "msg"=>$msg
                                  
                ));
                }
            
            
            
        }
        }
        
        
        
         /**
         * This is the function that suspend an inactive subscription
         */
        public function actionsuspendMembershipSubscription(){
            
            $id = $_POST['id'];
            $status = strtolower($_POST['status']);
            $user_name = $this->getTheNameOfThisMember($_POST['member_id']);
            
            if($status == 'suspended'){
                $msg = "'$user_name' subscription is currently suspended " ;
                header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() != 0,
                               "msg"=>$msg
                                  
                ));
                
            }else{
                if($this->isSubscriptionSuspensionSuccessful($id)){
                    
                     $msg = "The Subscription of '$user_name' is successful suspended" ;
                     header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                               "msg"=>$msg
                                  
                        ));
                    
                }else{
                     $msg = "The attempt to suspend the Subscription of '$user_name' was  not successful" ;
                     header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() != 0,
                               "msg"=>$msg
                                  
                ));
                }
            
            
            
        }
        }
        
        
         /**
         * This is the function that extend membership subscription
         */
        public function actionextendMembershipSubscription(){
            
            $id = $_POST['id'];
            $status = strtolower($_POST['status']);
            $user_name = $this->getTheNameOfThisMember($_POST['member_id']);
            $new_end_date = $this->getTheSubscriptionEndDate($_POST['membership_end_date'],$_POST['number_of_months']);
            
            $existing_number_of_months = $this->getTheCurrentNumberOfMonths($id);
            
            $new_extended_month = $existing_number_of_months + $_POST['number_of_months'];
            
           if($this->isSubscriptionExtentionSuccessful($id,$new_end_date,$new_extended_month)){
                    
                     $msg = "The Subscription of '$user_name' is successful entended" ;
                     header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                               "msg"=>$msg
                                  
                        ));
                    
                }else{
                     $msg = "The attempt to extend the Subscription of '$user_name' was  not successful" ;
                     header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() != 0,
                               "msg"=>$msg
                                  
                ));
                }
            
            
            
        
        }
        
        
        
        
        /**
         * This is the function that renews membership subscription
         */
        public function actionrenewMembershipSubscription(){
            
            $id = $_POST['id'];
            $status = strtolower($_POST['status']);
            $user_name = $this->getTheNameOfThisMember($_POST['member_id']);
            $new_start_date = date("Y-m-d H:i:s", strtotime($_POST['membership_start_date'])); 
            $new_end_date = $this->getTheSubscriptionEndDate($_POST['membership_start_date'],$_POST['number_of_months']); 
            
                       
           if($this->isSubscriptionRenewalSuccessful($id,$new_start_date,$new_end_date,$_POST['number_of_months'],$_POST['membership_type_id'])){
                    
                     $msg = "The subscription of '$user_name' is successful renewed" ;
                     header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                               "msg"=>$msg
                                  
                        ));
                    
                }else{
                     $msg = "The attempt to renew the subscription of '$user_name' was  not successful" ;
                     header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() != 0,
                               "msg"=>$msg
                                  
                ));
                }
            
            
            
        
        }
        
        
        /**
         * This is the function that effects the activation of a subscription
         */
        public function isSubscriptionActivationSuccessful($id,$membership_start_date,$membership_end_date){
            
            $cmd =Yii::app()->db->createCommand();
             $result = $cmd->update('membership_subscription',
                                  array(
                                    'status'=>'active',
                                     'membership_start_date'=>$membership_start_date,
                                      'membership_end_date'=>$membership_end_date,
                                      'date_activated'=>new CDbExpression('NOW()'),
                                      'activated_by_id'=>Yii::app()->user->id
                                   
                               
		
                            ),
                     ("id=$id"));
            
           if($result>0){
               return true;
           }else{
               return false;
           }
            
        }
        
        
        
        /**
         * This is the function that gets the number of years of a membership subscription
         */
        public function getTheNumberOfYearsOfThisMembershipSubscription($id){
            
            $model = new MembershipSubscription;
            
            return $model->getTheNumberOfYearsOfThisMembershipSubscription($id);
        }
        
        
        
         /**
         * This is the function that effects the deactivation of a member
         */
        public function isSubscriptionDeactivationSuccessful($id){
            
             $cmd =Yii::app()->db->createCommand();
             $result = $cmd->update('membership_subscription',
                                  array(
                                    'status'=>'inactive',
                                      'date_deactivated'=>new CDbExpression('NOW()'),
                                      'deactivated_by_id'=>Yii::app()->user->id
                                   
                               
		
                            ),
                     ("id=$id"));
            
           if($result>0){
               return true;
           }else{
               return false;
           }
            
        }
        
        
        
         /**
         * This is the function that effects the suspension of a subscription
         */
        public function isSubscriptionSuspensionSuccessful($id){
            
             $cmd =Yii::app()->db->createCommand();
             $result = $cmd->update('membership_subscription',
                                  array(
                                    'status'=>'suspended',
                                      'date_suspended'=>new CDbExpression('NOW()'),
                                      'suspended_by_id'=>Yii::app()->user->id
              
                            ),
                     ("id=$id"));
            
           if($result>0){
               return true;
           }else{
               return false;
           }
            
        }
        
        
        
          /**
         * This is the function that extends a subscription
         */
        public function isSubscriptionExtentionSuccessful($id,$new_end_date,$new_extended_month){
            
             $cmd =Yii::app()->db->createCommand();
             $result = $cmd->update('membership_subscription',
                                  array(
                                    'membership_end_date'=>$new_end_date,
                                      'number_of_months'=>$new_extended_month,
                                      'date_extended'=>new CDbExpression('NOW()'),
                                      'extended_by_id'=>Yii::app()->user->id
              
                            ),
                     ("id=$id"));
            
           if($result>0){
               return true;
           }else{
               return false;
           }
            
            
        }
        
        
        
        
           /**
         * This is the function that renews a subscription
         */
        public function isSubscriptionRenewalSuccessful($id,$new_start_date,$new_end_date,$number_of_months,$type){
            
             $cmd =Yii::app()->db->createCommand();
             $result = $cmd->update('membership_subscription',
                                  array(
                                    'membership_end_date'=>$new_end_date,
                                    'membership_start_date'=>$new_start_date,  
                                     'number_of_months'=>$number_of_months,
                                     'membership_type_id'=>$type, 
                                     'date_renewed'=>new CDbExpression('NOW()'),
                                     'renewed_by_id'=>Yii::app()->user->id
              
                            ),
                     ("id=$id"));
            
           if($result>0){
               return true;
           }else{
               return false;
           }
            
            
        }
        
        
        
         /**
         * This is the function that list all membership subscription
         */
        public function actionListMembershipSubscription(){
            
            $userid = Yii::app()->user->id;
          
            $subscription = MembershipSubscription::model()->findAll();
                if($subscription===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "subscription" => $subscription
                                        
                                
                            ));
                       
                }
            
            
            
            
        }
        
        
        /**
         * This is the function that retrieves extra information on subscription constituent
         */
        public function actionobtainSubscriptionExtraInformation(){
            
            $member_id = $_REQUEST['member_id'];
            $membership_type_id = $_REQUEST['membership_type_id'];
                       
            $member_name = $this->getTheNameOfThisMember($member_id);
            $memberbershiptype = $this->getThisSubscriptionType($membership_type_id);
            
            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                       "member"=>$member_name,
                                        "membership"=> $memberbershiptype
                                       
                                
                            ));
            
            
        }
        
        
        
         /**
         * This is the function that retrieves the name of the membership type
         */
        public function getThisSubscriptionType($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $type= Membershiptype::model()->find($criteria);
                
                return $type['name'];
            
        }
        
        
        
        /**
         * This is the function that gets the existing number of years
         */
        public function getTheCurrentNumberOfMonths($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $year= MembershipSubscription::model()->find($criteria);
                
                return $year['number_of_months'];
            
        }
      
        
}
