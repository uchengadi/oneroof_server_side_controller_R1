<?php

class MembersController extends Controller
{
	private $_id;
         protected $passwordCompare;
          private $_userid;
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
				'actions'=>array('index','view','Login','confirmIfUserIsLoggedIn','ListAllUsersInThePlatform','RegisterThisNewMember','ListAllMembers',
                                    'RegisterThisNewBasicMember','RegisterThisNewBasicMemberQuickly'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update', 'ListAllUserRoles','GetUserPermissions', 'ListAllUsersInCity','ListAllUsersOfThisUsertype',
                                    'ListDevicesForUserForSingleScheduling', 'ChangePasswordByOwner', 'ListCityAndUsertypeForAUser', 'ChangePasswordForOtherUsers',
                                    'ListAllUsersTaskFavourites','RetrieveThisFavouriteTaskName','UpdateFavouriteTask','DeleteOneUserfavourite',
                                    'DetermineIfInUserFavourite','ListAllTasksAssignedToAUser','ListAllTaskRelatedToThisTask','DeleteOneRelatedTask',
                                    'RetrieveAllUserTaskForChaining','ChainSelectedSlaveTasksToMaster','determineToolboxIdGivenToolAndUserId','AddTaskToFavourites',
                                    'ListAllSimilarTasksAcrossUserTools','ListAllSimilarTasksAcrossUserToolboxes','ListAllUsersInThePlatform','listAllDomainUsers',
                                    'getThisUsername','getTheUsernameAndStorageRoom','addNewStaffMember','updateStaffMember','addNewNonStaffMember',
                                    'updateNonStaffMember','obtainMemberExtraInformation','DeleteThisMember','addNewStaffMember','updateStaffMember',
                                    'addNewNonStaffMember','updateNonStaffMember','ListAllNonStaffMembers','ListAllStaffMembers','ListAllMembers','ActivateThisMember',
                                    'SuspendThisMember','DeactivateThisMember','generateNewMemberNumber','retrieveMemberAccountInformation','updateMemberAccountInformation','updateAndRenewMemberhipInformation',
                                    'retrieveMemberCartDetails','updateAndExtendMemberhipInformation','ConnectMeToThisMember','listMemberConnectionToOtherMembers','listTradableProductsForThisMember','listAllProductsSubscribedToByThisMember',
                                    'listMembersConnectedToThisMember','retrieveThisMemberAccountInformation','acceptThisMemberInMyConnection','rejectThisMemberInMyConnection','disconnectThisMemberInMyConnection',
                                    'suspendThisMemberInMyConnection','unsuspendThisMemberInMyConnection','disconnectingFromThisMember','listallmembersconnectedtoamember','verifyThisMembershipNumber',
                                    'registerThisNewBasicMember','changeThisMemberMembershipSubscription','justTestingThisOut','retrieveMemberDetail','registerThisNewBasicMemberQuickly','updateNonMemberAccountInformation'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('listallusers','deleteoneuser','Logout', 'GetUserPermissions', 'ListAllUserNeeds','ListAllDomainNeeds','RetrieveThisUserNeedInfo'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

        
         /**
         * This is the function that retrieves extra information on product constituent
         */
        public function actionobtainMemberExtraInformation(){
            
            if(isset($_REQUEST['member_id'])){
                $member_id = $_REQUEST['member_id'];
            }
             if(isset($_REQUEST['member_id'])){
                $member_id = $_REQUEST['member_id'];
            }
            
            
           
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$member_id);
            $member= Members::model()->find($criteria);
            
            //get the membership type of this member
            $membershiptype = $this->getThisMemberMembershipType($member['id']);
            
             //get the membership type id of this member
            $membershiptype_id = $this->getThisMemberMembershipTypeId($member['id']);
            
            //get the date of membership renewal for this member
            $renewal_date = $this->getTheDateOfMembershipRenewalOfThisMember($member['id']);
            
             //get the membership status of this member
            $membership_status = $this->getTheMembershipStatusOfThisMember($member['id']);
            
            //get the name of the member city
            $member_city = $this->getTheNameofTheMemberCity($member['city_id']);
            
            //get the name of the member state
            $member_state = $this->getTheNameOfTheMemberState($member['state_id']);
            
            //get the name of the member country
            $member_country = $this->getTheNameOfTheMemberCountry($member['country_id']);
            
            //get the name of the member permanent delivery city
            $delivery_city = $this->getTheNameOfThePermamentDeliveryCity($member['delivery_city_id']);
            
            //get the name of the member permanent delivery state
            $delivery_state = $this->getTheOfTheNameOfThePermanentDeliveryState($member['delivery_state_id']);
            
            //get the name of the member permanent delivery country
            $delivery_country = $this->getTheNameOfThePermanentDeliveryCountry($member['delivery_country_id']);
            
            //get the name of the member corporate city
            $corporate_city = $this->getTheNameOfTheMemberCorporateCity($member['corporate_city_id']);
            
            //get the name of the member corporate state
            $corporate_state = $this->getTheNameOfTheMemberCorporateState($member['corporate_state_id']);
            
            //get the name of the member corporate country
            $corporate_country = $this->getTheNameOfTheMemberCorporateCountry($member['corporate_country_id']);
            
            //get the open order initiated by this member
            $order_id = $this->getTheOpenOrderInitiatedByMember($member['id']);
            
           
            
             header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                                //"selected" => $selected,
                                "member" => $member,
                                "membershiptype"=>$membershiptype,
                                "date_of_renewal"=>$renewal_date,
                                "member_city"=>$member_city,
                                "member_state"=>$member_state,
                                "member_country"=>$member_country,
                                "member_delivery_city"=>$delivery_city,
                                "member_delivery_state"=>$delivery_state,
                                "member_delivery_country"=>$delivery_country,
                                "member_corporate_city"=>$corporate_city,
                                "member_corporate_state"=>$corporate_state,
                                "member_corporate_country"=>$corporate_country,
                                "membership_status"=>$membership_status,
                                "membership_type_id"=>$membershiptype_id,
                                "order"=>$order_id
                                
                               
                             )); 
            
            
        }
        
        
        
        /**
         * This is the function that gets the city name
         */
        public function getThisCityName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $name = City::model()->find($criteria);
            
            return $name['name'];
        }
        
        
         /**
         * This is the function that gets the state name
         */
        public function getThisStateName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $name = State::model()->find($criteria);
            
            return $name['name'];
        }
        
        
        
          /**
         * This is the function that gets the country name
         */
        public function getThisCountryName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $name = Country::model()->find($criteria);
            
            return $name['name'];
        }
        
        
	
	/**
	 *Add a new staff member
	 * 
	 */
	public function actionaddNewStaffMember()
	{
		$model=new Members;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

                //get the logged i user
                $user_id = Yii::app()->user->id;
                
              		
                
                $model->usertype = strtolower('staff');
                $model->email = $_POST['email'];
                $model->username = $_POST['email'];
                if(is_numeric($_POST['city'])){
                     $model->city_id = $_POST['city'];
                }else{
                     $model->city_id = $_POST['city_id'];
                }
                if(is_numeric($_POST['state'])){
                     $model->state_id = $_POST['state'];
                }else{
                     $model->state_id = $_POST['state_id'];
                }
                if(is_numeric($_POST['country'])){
                     $model->country_id = $_POST['country'];
                }else{
                     $model->country_id = $_POST['country_id'];
                }
                $model->name = $_POST['name'];
               /** $model->lastname = $_POST['lastname'];
                if(isset($_POST['middlename'])){
                    $model->middlename = $_POST['middlename'];
                }
                $model->firstname = $_POST['firstname'];
                if(isset($_POST['gender'])){
                    $model->gender = strtolower($_POST['gender']);
                }
                if(isset($_POST['middlename'])){
                   $model->name = $model->lastname . " ". $model->middlename . " " . $model->firstname;
               }else{
                   $model->name = $model->lastname . " ". $model->firstname;
               }
                * 
                */
                 if(isset($_POST['religion'])){
                    $model->religion = strtolower($_POST['religion']);
                }
                 if(isset($_POST['maritalstatus'])){
                    $model->maritalstatus = strtolower($_POST['maritalstatus']);
                }
                if(isset($_POST['dateofbirth'])){
                    $model->dateofbirth = date("Y-m-d H:i:s", strtotime($_POST['dateofbirth'])); 
                }
                if(isset($_POST['address1'])){
                    $model->address1 = $_POST['address1']; 
                }
                if(isset($_POST['address2'])){
                    $model->address2 = $_POST['address2']; 
                }
                if(isset($_POST['delivery_address1'])){
                    $model->delivery_address1 = $_POST['delivery_address1']; 
                }
                 if(isset($_POST['delivery_address2'])){
                    $model->delivery_address2 = $_POST['delivery_address2']; 
                }  
                 if(is_numeric($_POST['delivery_city'])){
                     $model->delivery_city_id = $_POST['delivery_city'];
                }else{
                     $model->delivery_city_id = $_POST['delivery_city_id'];
                }
                if(is_numeric($_POST['delivery_state'])){
                     $model->delivery_state_id = $_POST['delivery_state'];
                }else{
                     $model->delivery_state_id = $_POST['delivery_state_id'];
                }
                if(is_numeric($_POST['delivery_country'])){
                     $model->delivery_country_id = $_POST['delivery_country'];
                }else{
                     $model->delivery_country_id = $_POST['delivery_country_id'];
                }
                 if(isset($_POST['name_of_organization'])){
                    $model->name_of_organization = $_POST['name_of_organization']; 
                } 
                 if(isset($_POST['unique_registration_number'])){
                    $model->unique_registration_number = $_POST['unique_registration_number']; 
                }
                  if(isset($_POST['business_category'])){
                    $model->business_category = $_POST['business_category']; 
                }
                if(isset($_POST['corporate_address1'])){
                    $model->corporate_address1 = $_POST['corporate_address1']; 
                }
                 if(isset($_POST['corporate_address2'])){
                    $model->corporate_address2 = $_POST['corporate_address2']; 
                }
                 if(is_numeric($_POST['corporate_city'])){
                     $model->corporate_city_id = $_POST['corporate_city'];
                }else{
                     $model->corporate_city_id = $_POST['corporate_city_id'];
                }
                if(is_numeric($_POST['corporate_state'])){
                     $model->corporate_state_id = $_POST['corporate_state'];
                }else{
                     $model->corporate_state_id = $_POST['corporate_state_id'];
                }
                if(is_numeric($_POST['corporate_country'])){
                     $model->corporate_country_id = $_POST['corporate_country'];
                }else{
                     $model->corporate_country_id = $_POST['corporate_country_id'];
                }
                 if(isset($_POST['landline'])){
                    $model->landline = $_POST['landline']; 
                }
                 if(isset($_POST['mobile_line'])){
                    $model->mobile_line = $_POST['mobile_line']; 
                }
                      
                $model->role = $_POST['role'];
                $model->category = $_POST['category'];
                $model->status = strtolower($_POST['status']);
                $model->create_time = new CDbExpression('NOW()');
                $model->create_user_id = Yii::app()->user->id;
                $password = $_POST['password'];
                $password_repeat = $_POST['passwordCompare'];
                
              //  $name = $model->firstname . ' ' . $model->middlename . ' ' . strtoupper($model->lastname);
                
                if($password === $password_repeat){
                    
                    $model->password = $password;
                    $model->password_repeat = $password_repeat;
                    
                    if($model->getPasswordMinLengthRule($password)){
                        
                        if($model->getPasswordMaxLengthRule($password )){
                            
                            if($model->getPasswordCharacterPatternRule($password)){
                                
                           
                  $icon_error_counter = 0;     
                 // $name = $model->firstname . ' ' . $model->middlename . ' ' . strtoupper($model->lastname);
                 if($_FILES['picture']['name'] != ""){
                    if($this->isIconTypeAndSizeLegal()){
                       
                       $icon_filename = $_FILES['picture']['name'];
                      $icon_size = $_FILES['picture']['size'];
                        
                    }else{
                       
                        $icon_error_counter = $icon_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    $icon_filename = $this->provideUserIconWhenUnavailable($model);
                   $icon_size = 0;
             
                }//end of the if icon is empty statement
                if($icon_error_counter ==0){
                   if($model->validate()){
                           $model->picture = $this->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                           $model->picture_size = $icon_size;
                           
                if($model->save()) {
                        
                    //$userid = Yii::app()->user->id;
                    if(isset($model->role)){
                            
                          if($this->assignRoleToAUser($model->role, $model->id)) {
                              // $result['success'] = 'true';
                                $msg = 'User Creation was successful';
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "msg" => $msg)
                                    );
                              
                              
                                 } else {
                                     $msg = 'User creation was not successful';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                                     
                                     
                                 } 
                        }else{
                            $msg = "Role is not assigned to this User";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                            
                            
                        }
                        
                   
                         
                     }else {
                         //$result['success'] = 'false';
                         $msg = 'User creation was not successful';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                     }
                        
                   }else{
                                
                                //delete all the moved files in the directory when validation error is encountered
                            $msg = "Validation Error: '$model->name'  was not created successful";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                            );
                          }
                        }elseif($icon_error_counter > 0){
                        //get the platform assigned icon width and height
                            $platform_width = $this->getThePlatformSetIconWidth();
                            $platform_height = $this->getThePlatformSeticonHeight();
                            $icon_types = $this->retrieveAllTheIconMimeTypes();
                            $icon_types = json_encode($icon_types);
                            $msg = "Please check your picture file type or size as picture must be of width '$platform_width'px and height '$platform_height'px. Picture is of types '$icon_types'";
                            header('Content-Type: application/json');
                                    echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                    "msg" => $msg)
                            );
                         
                        } 
                                
                                
                            }else{
                                
                                $msg = 'Password must contain at least one number(0-9), at least one lower case letter(a-z), at least one upper case letter(A-Z), and at least one special character(@,&,$ etc)';
                                 header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() != 0,
                                 "msg" => $msg,
                                )); 
                            }
                            
                        }else{
                                $msg = 'The maximum Password length allowed is sixty(60)';
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                     "success" => mysql_errno() != 0,
                                    "msg" => $msg,
                            )); 
                            
                        }
                        
                        
                    }else{
                        $msg = 'The minimum Password length allowed is eight(8)';
                             header('Content-Type: application/json');
                            echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg,
                       )); 
                        
                        
                    }
        
                
            }else{
               $msg = 'Repeat Password do not match the new password';
              header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                           "msg" => $msg,
                       )); 
                
                
            }

	
	}
        
        
        
        /**
	 *Add a new non staff member
	 * 
	 */
	public function actionaddNewNonStaffMember()
	{
		$model=new Members;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

                //get the logged i user
                $user_id = Yii::app()->user->id;
                
              		
                
                $model->usertype = strtolower('others');
                $model->email = $_POST['email'];
                $model->username = $_POST['email'];
                if(is_numeric($_POST['city'])){
                     $model->city_id = $_POST['city'];
                }else{
                     $model->city_id = $_POST['city_id'];
                }
                if(is_numeric($_POST['state'])){
                     $model->state_id = $_POST['state'];
                }else{
                     $model->state_id = $_POST['state_id'];
                }
                if(is_numeric($_POST['country'])){
                     $model->country_id = $_POST['country'];
                }else{
                     $model->country_id = $_POST['country_id'];
                }
                $model->lastname = $_POST['lastname'];
                if(isset($_POST['middlename'])){
                    $model->middlename = $_POST['middlename'];
                }
                $model->firstname = $_POST['firstname'];
                if(isset($_POST['gender'])){
                    $model->gender = strtolower($_POST['gender']);
                }
               if(isset($_POST['middlename'])){
                   $model->name = $model->lastname . " ". $model->middlename . " " . $model->firstname;
               }else{
                   $model->name = $model->lastname . " ". $model->firstname;
               }
                 if(isset($_POST['religion'])){
                    $model->religion = strtolower($_POST['religion']);
                }
                 if(isset($_POST['maritalstatus'])){
                    $model->maritalstatus = strtolower($_POST['maritalstatus']);
                }
                if(isset($_POST['dateofbirth'])){
                    $model->dateofbirth = date("Y-m-d H:i:s", strtotime($_POST['dateofbirth'])); 
                }
                if(isset($_POST['address1'])){
                    $model->address1 = $_POST['address1']; 
                }
                if(isset($_POST['address2'])){
                    $model->address2 = $_POST['address2']; 
                }
                if(isset($_POST['delivery_address1'])){
                    $model->delivery_address1 = $_POST['delivery_address1']; 
                }
                 if(isset($_POST['delivery_address2'])){
                    $model->delivery_address2 = $_POST['delivery_address2']; 
                }  
                 if(is_numeric($_POST['delivery_city'])){
                     $model->delivery_city_id = $_POST['delivery_city'];
                }else{
                     $model->delivery_city_id = $_POST['delivery_city_id'];
                }
                if(is_numeric($_POST['delivery_state'])){
                     $model->delivery_state_id = $_POST['delivery_state'];
                }else{
                     $model->delivery_state_id = $_POST['delivery_state_id'];
                }
                if(is_numeric($_POST['delivery_country'])){
                     $model->delivery_country_id = $_POST['delivery_country'];
                }else{
                     $model->delivery_country_id = $_POST['delivery_country_id'];
                }
                 if(isset($_POST['name_of_organization'])){
                    $model->name_of_organization = $_POST['name_of_organization']; 
                } 
                 if(isset($_POST['unique_registration_number'])){
                    $model->unique_registration_number = $_POST['unique_registration_number']; 
                }
                  if(isset($_POST['business_category'])){
                    $model->business_category = $_POST['business_category']; 
                }
                if(isset($_POST['corporate_address1'])){
                    $model->corporate_address1 = $_POST['corporate_address1']; 
                }
                 if(isset($_POST['corporate_address2'])){
                    $model->corporate_address2 = $_POST['corporate_address2']; 
                }
                 if(is_numeric($_POST['corporate_city'])){
                     $model->corporate_city_id = $_POST['corporate_city'];
                }else{
                     $model->corporate_city_id = $_POST['corporate_city_id'];
                }
                if(is_numeric($_POST['corporate_state'])){
                     $model->corporate_state_id = $_POST['corporate_state'];
                }else{
                     $model->corporate_state_id = $_POST['corporate_state_id'];
                }
                if(is_numeric($_POST['corporate_country'])){
                     $model->corporate_country_id = $_POST['corporate_country'];
                }else{
                     $model->corporate_country_id = $_POST['corporate_country_id'];
                }
                 if(isset($_POST['landline'])){
                    $model->landline = $_POST['landline']; 
                }
                 if(isset($_POST['mobile_line'])){
                    $model->mobile_line = $_POST['mobile_line']; 
                }
                if(isset($_POST['delivery_address1'])){
                    $model->delivery_address1 = $_POST['delivery_address1']; 
                }
                 if(isset($_POST['delivery_address2'])){
                    $model->delivery_address2 = $_POST['delivery_address2']; 
                }  
                 if(is_numeric($_POST['delivery_city'])){
                     $model->delivery_city_id = $_POST['delivery_city'];
                }else{
                     $model->delivery_city_id = $_POST['delivery_city_id'];
                }
                if(is_numeric($_POST['delivery_state'])){
                     $model->delivery_state_id = $_POST['delivery_state'];
                }else{
                     $model->delivery_state_id = $_POST['delivery_state_id'];
                }
                if(is_numeric($_POST['delivery_country'])){
                     $model->delivery_country_id = $_POST['delivery_country'];
                }else{
                     $model->delivery_country_id = $_POST['delivery_country_id'];
                }
                 if(isset($_POST['name_of_organization'])){
                    $model->name_of_organization = $_POST['name_of_organization']; 
                } 
                 if(isset($_POST['unique_registration_number'])){
                    $model->unique_registration_number = $_POST['unique_registration_number']; 
                }
                  if(isset($_POST['business_category'])){
                    $model->business_category = $_POST['business_category']; 
                }
                if(isset($_POST['corporate_address1'])){
                    $model->corporate_address1 = $_POST['corporate_address1']; 
                }
                 if(isset($_POST['corporate_address2'])){
                    $model->corporate_address2 = $_POST['corporate_address2']; 
                }
                 if(is_numeric($_POST['corporate_city'])){
                     $model->corporate_city_id = $_POST['corporate_city'];
                }else{
                     $model->corporate_city_id = $_POST['corporate_city_id'];
                }
                if(is_numeric($_POST['corporate_state'])){
                     $model->corporate_state_id = $_POST['corporate_state'];
                }else{
                     $model->corporate_state_id = $_POST['corporate_state_id'];
                }
                if(is_numeric($_POST['corporate_country'])){
                     $model->corporate_country_id = $_POST['corporate_country'];
                }else{
                     $model->corporate_country_id = $_POST['corporate_country_id'];
                }
                 if(isset($_POST['landline'])){
                    $model->landline = $_POST['landline']; 
                }
                 if(isset($_POST['mobile_line'])){
                    $model->mobile_line = $_POST['mobile_line']; 
                }
                      
                $model->role = $_POST['role'];
                $model->category = $_POST['category'];
                $model->status = strtolower($_POST['status']);
                $model->create_time = new CDbExpression('NOW()');
                $model->create_user_id = Yii::app()->user->id;
                $password = $_POST['password'];
                $password_repeat = $_POST['passwordCompare'];
                
                $name = $model->firstname . ' ' . $model->middlename . ' ' . strtoupper($model->lastname);
                
                if($password === $password_repeat){
                    
                    $model->password = $password;
                    $model->password_repeat = $password_repeat;
                    
                    if($model->getPasswordMinLengthRule($password)){
                        
                        if($model->getPasswordMaxLengthRule($password )){
                            
                            if($model->getPasswordCharacterPatternRule($password)){
                                
                        
                  $icon_error_counter = 0;     
                  $name = $model->firstname . ' ' . $model->middlename . ' ' . strtoupper($model->lastname);
                 if($_FILES['picture']['name'] != ""){
                    if($this->isIconTypeAndSizeLegal()){
                       
                       $icon_filename = $_FILES['picture']['name'];
                      $icon_size = $_FILES['picture']['size'];
                        
                    }else{
                       
                        $icon_error_counter = $icon_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    $icon_filename = $this->provideUserIconWhenUnavailable($model);
                   $icon_size = 0;
             
                }//end of the if icon is empty statement
                if($icon_error_counter ==0){
                   if($model->validate()){
                           $model->picture = $this->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                           $model->picture_size = $icon_size;
                           
                if($model->save()) {
                        
                    //$userid = Yii::app()->user->id;
                    if(isset($model->role)){
                            
                          if($this->assignRoleToAUser($model->role, $model->id)) {
                              // $result['success'] = 'true';
                                $msg = 'User Creation was successful';
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "msg" => $msg)
                                    );
                              
                              
                                 } else {
                                     $msg = 'User creation was not successful';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                                     
                                     
                                 } 
                        }else{
                            $msg = "Role is not assigned to this User";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                            
                            
                        }
                        
                   
                         
                     }else {
                         //$result['success'] = 'false';
                         $msg = 'User creation was not successful';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                     }
                        
                   }else{
                                
                                //delete all the moved files in the directory when validation error is encountered
                            $msg = "Validation Error: '$name'  was not created successful";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                            );
                          }
                        }elseif($icon_error_counter > 0){
                        //get the platform assigned icon width and height
                            $platform_width = $this->getThePlatformSetIconWidth();
                            $platform_height = $this->getThePlatformSeticonHeight();
                            $icon_types = $this->retrieveAllTheIconMimeTypes();
                            $icon_types = json_encode($icon_types);
                            $msg = "Please check your picture file type or size as picture must be of width '$platform_width'px and height '$platform_height'px. Picture is of types '$icon_types'";
                            header('Content-Type: application/json');
                                    echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                    "msg" => $msg)
                            );
                         
                        } 
                                
                                
                            }else{
                                
                                $msg = 'Password must contain at least one number(0-9), at least one lower case letter(a-z), at least one upper case letter(A-Z), and at least one special character(@,&,$ etc)';
                                 header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() != 0,
                                 "msg" => $msg,
                                )); 
                            }
                            
                        }else{
                                $msg = 'The maximum Password length allowed is sixty(60)';
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                     "success" => mysql_errno() != 0,
                                    "msg" => $msg,
                            )); 
                            
                        }
                        
                        
                    }else{
                        $msg = 'The minimum Password length allowed is eight(8)';
                             header('Content-Type: application/json');
                            echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg,
                       )); 
                        
                        
                    }
                
           
                
            }else{
               $msg = 'Repeat Password do not match the new password';
              header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                           "msg" => $msg,
                       )); 
                
                
            }

	
	}

	/**
	 * Update a staff member information
	
	 */
	public function actionUpdateStaffMember()
	{
		$_id = $_POST['id'];
               
                              
                                    
                $model=Members::model()->findByPk($_id);
               
                //obtain the current password
                $criteria3 = new CDbCriteria();
                $criteria3->select = 'id, password';
                $criteria3->condition='id=:id';
                $criteria3->params = array(':id'=>$_id);
                $current_password= Members::model()->find($criteria3);
                
                $model->current_pass = $current_password['password'];
                
                
                             
                if(is_numeric($_POST['city'])){
                     $model->city_id = $_POST['city'];
                }else{
                     $model->city_id = $_POST['city_id'];
                }
                if(is_numeric($_POST['state'])){
                     $model->state_id = $_POST['state'];
                }else{
                     $model->state_id = $_POST['state_id'];
                }
                if(is_numeric($_POST['country'])){
                     $model->country_id = $_POST['country'];
                }else{
                     $model->country_id = $_POST['country_id'];
                }
                $model->lastname = $_POST['lastname'];
                if(isset($_POST['middlename'])){
                    $model->middlename = $_POST['middlename'];
                }
                $model->firstname = $_POST['firstname'];
                if(isset($_POST['gender'])){
                    $model->gender = strtolower($_POST['gender']);
                }
                if(isset($_POST['middlename'])){
                   $model->name = $model->lastname . " ". $model->middlename . " " . $model->firstname;
               }else{
                   $model->name = $model->lastname . " ". $model->firstname;
               }
                 if(isset($_POST['religion'])){
                    $model->religion = strtolower($_POST['religion']);
                }
                 if(isset($_POST['maritalstatus'])){
                    $model->maritalstatus = strtolower($_POST['maritalstatus']);
                }
                if(isset($_POST['dateofbirth'])){
                    $model->dateofbirth = date("Y-m-d H:i:s", strtotime($_POST['dateofbirth'])); 
                }
                if(isset($_POST['address1'])){
                    $model->address1 = $_POST['address1']; 
                }
                if(isset($_POST['address2'])){
                    $model->address2 = $_POST['address2']; 
                }
                if(isset($_POST['delivery_address1'])){
                    $model->delivery_address1 = $_POST['delivery_address1']; 
                }
                 if(isset($_POST['delivery_address2'])){
                    $model->delivery_address2 = $_POST['delivery_address2']; 
                }  
               if(is_numeric($_POST['delivery_city'])){
                     $model->delivery_city_id = $_POST['delivery_city'];
                }else{
                     $model->delivery_city_id = $_POST['delivery_city_id'];
                }
                if(is_numeric($_POST['delivery_state'])){
                     $model->delivery_state_id = $_POST['delivery_state'];
                }else{
                     $model->delivery_state_id = $_POST['delivery_state_id'];
                }
                if(is_numeric($_POST['delivery_country'])){
                     $model->delivery_country_id = $_POST['delivery_country'];
                }else{
                     $model->delivery_country_id = $_POST['delivery_country_id'];
                }
                 if(isset($_POST['name_of_organization'])){
                    $model->name_of_organization = $_POST['name_of_organization']; 
                } 
                 if(isset($_POST['unique_registration_number'])){
                    $model->unique_registration_number = $_POST['unique_registration_number']; 
                }
                  if(isset($_POST['business_category'])){
                    $model->business_category = $_POST['business_category']; 
                }
                if(isset($_POST['corporate_address1'])){
                    $model->corporate_address1 = $_POST['corporate_address1']; 
                }
                 if(isset($_POST['corporate_address2'])){
                    $model->corporate_address2 = $_POST['corporate_address2']; 
                }
                 if(is_numeric($_POST['corporate_city'])){
                     $model->corporate_city_id = $_POST['corporate_city'];
                }else{
                     $model->corporate_city_id = $_POST['corporate_city_id'];
                }
                if(is_numeric($_POST['corporate_state'])){
                     $model->corporate_state_id = $_POST['corporate_state'];
                }else{
                     $model->corporate_state_id = $_POST['corporate_state_id'];
                }
                if(is_numeric($_POST['corporate_country'])){
                     $model->corporate_country_id = $_POST['corporate_country'];
                }else{
                     $model->corporate_country_id = $_POST['corporate_country_id'];
                }
                 if(isset($_POST['landline'])){
                    $model->landline = $_POST['landline']; 
                }
                 if(isset($_POST['mobile_line'])){
                    $model->mobile_line = $_POST['mobile_line']; 
                }
                if(isset($_POST['delivery_address1'])){
                    $model->delivery_address1 = $_POST['delivery_address1']; 
                }
                 if(isset($_POST['delivery_address2'])){
                    $model->delivery_address2 = $_POST['delivery_address2']; 
                }  
                if(is_numeric($_POST['delivery_city'])){
                     $model->delivery_city_id = $_POST['delivery_city'];
                }else{
                     $model->delivery_city_id = $_POST['delivery_city_id'];
                }
                if(is_numeric($_POST['delivery_state'])){
                     $model->delivery_state_id = $_POST['delivery_state'];
                }else{
                     $model->delivery_state_id = $_POST['delivery_state_id'];
                }
                if(is_numeric($_POST['delivery_country'])){
                     $model->delivery_country_id = $_POST['delivery_country'];
                }else{
                     $model->delivery_country_id = $_POST['delivery_country_id'];
                }
                 if(isset($_POST['name_of_organization'])){
                    $model->name_of_organization = $_POST['name_of_organization']; 
                } 
                 if(isset($_POST['unique_registration_number'])){
                    $model->unique_registration_number = $_POST['unique_registration_number']; 
                }
                  if(isset($_POST['business_category'])){
                    $model->business_category = $_POST['business_category']; 
                }
                if(isset($_POST['corporate_address1'])){
                    $model->corporate_address1 = $_POST['corporate_address1']; 
                }
                 if(isset($_POST['corporate_address2'])){
                    $model->corporate_address2 = $_POST['corporate_address2']; 
                }
                 if(is_numeric($_POST['corporate_city'])){
                     $model->corporate_city_id = $_POST['corporate_city'];
                }else{
                     $model->corporate_city_id = $_POST['corporate_city_id'];
                }
                if(is_numeric($_POST['corporate_state'])){
                     $model->corporate_state_id = $_POST['corporate_state'];
                }else{
                     $model->corporate_state_id = $_POST['corporate_state_id'];
                }
                if(is_numeric($_POST['corporate_country'])){
                     $model->corporate_country_id = $_POST['corporate_country'];
                }else{
                     $model->corporate_country_id = $_POST['corporate_country_id'];
                }
                 if(isset($_POST['landline'])){
                    $model->landline = $_POST['landline']; 
                }
                 if(isset($_POST['mobile_line'])){
                    $model->mobile_line = $_POST['mobile_line']; 
                }
            
                
                $model->role = $_POST['role'];
                $model->category = $_POST['category'];
                 $model->status = $_POST['status'];
                $model->update_time = new CDbExpression('NOW()');
                $model->update_user_id = Yii::app()->user->id;
                
               $model->password = '';
               $model->password_repeat = '';
                
              //get the domain name
                $user_name = $this->getTheUsername($_id);
                
                $icon_error_counter  = 0;
                
                if($_FILES['picture']['name'] != ""){
                    if($this->isIconTypeAndSizeLegal()){
                        
                       $icon_filename = $_FILES['picture']['name'];
                       $icon_size = $_FILES['picture']['size'];
                        
                    }else{
                       
                        $icon_error_counter = $icon_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    //$model->icon = $this->retrieveThePreviousIconName($_id);
                    $icon_filename = $this->retrieveThePreviousIconName($_id);
                    $icon_size = $this->retrieveThePrreviousIconSize($_id);
             
                }//end of the if icon is empty statement
                if($icon_error_counter ==0){
                   if($model->validate()){
                           $model->picture = $this->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                           $model->picture_size = $icon_size;
                           
                 if($model->save()) {
                        
                    //$userid = Yii::app()->user->id;
                    if(isset($model->role)){
                            
                          if($this->assignRoleToAUser($model->role, $model->id)) {
                              // $result['success'] = 'true';
                                $msg = 'User update was successful';
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "msg" => $msg)
                                    );
                              
                              
                                 } else {
                                     $msg = 'User update was not successful';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                                     
                                     
                                 } 
                        }else{
                            $msg = "Role is not assigned to this User";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                            
                            
                        }
                        
                   
                         
                     }else {
                         //$result['success'] = 'false';
                         $msg = 'User update was not successful';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                     }
                   }else{
                                
                                //delete all the moved files in the directory when validation error is encountered
                            $msg = "Validation Error: '$user_name' information update was not successful";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg,
                                        )
                            );
                          }
                        }elseif($icon_error_counter > 0){
                        //get the platform assigned icon width and height
                            $platform_width = $this->getThePlatformSetIconWidth();
                            $platform_height = $this->getThePlatformSeticonHeight();
                            $icon_types = $this->retrieveAllTheIconMimeTypes();
                            $icon_types = json_encode($icon_types);
                            $msg = "Please check your picture file type or size as picture must be of width '$platform_width'px and height '$platform_height'px. Picture is of types '$icon_types'";
                            header('Content-Type: application/json');
                                    echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                    "msg" => $msg)
                            );
                         
                        }  
	}
        
        
        
        
        /**
	 * Update a non staff member information
	
	 */
	public function actionUpdateNonStaffMember()
	{
		$_id = $_POST['id'];
                                                                 
                $model=Members::model()->findByPk($_id);
               
                //obtain the current password
                $criteria3 = new CDbCriteria();
                $criteria3->select = 'id, password';
                $criteria3->condition='id=:id';
                $criteria3->params = array(':id'=>$_id);
                $current_password= Members::model()->find($criteria3);
                
                $model->current_pass = $current_password['password'];
                
                
                             
                if(is_numeric($_POST['city'])){
                     $model->city_id = $_POST['city'];
                }else{
                     $model->city_id = $_POST['city_id'];
                }
                if(is_numeric($_POST['state'])){
                     $model->state_id = $_POST['state'];
                }else{
                     $model->state_id = $_POST['state_id'];
                }
                if(is_numeric($_POST['country'])){
                     $model->country_id = $_POST['country'];
                }else{
                     $model->country_id = $_POST['country_id'];
                }
                $model->lastname = $_POST['lastname'];
                if(isset($_POST['middlename'])){
                    $model->middlename = $_POST['middlename'];
                }
                $model->firstname = $_POST['firstname'];
                if(isset($_POST['gender'])){
                    $model->gender = strtolower($_POST['gender']);
                }
                if(isset($_POST['middlename'])){
                   $model->name = $model->lastname . " ". $model->middlename . " " . $model->firstname;
               }else{
                   $model->name = $model->lastname . " ". $model->firstname;
               }
                 if(isset($_POST['religion'])){
                    $model->religion = strtolower($_POST['religion']);
                }
                 if(isset($_POST['maritalstatus'])){
                    $model->maritalstatus = strtolower($_POST['maritalstatus']);
                }
                if(isset($_POST['dateofbirth'])){
                    $model->dateofbirth = date("Y-m-d H:i:s", strtotime($_POST['dateofbirth'])); 
                }
                if(isset($_POST['address1'])){
                    $model->address1 = $_POST['address1']; 
                }
                if(isset($_POST['address2'])){
                    $model->address2 = $_POST['address2']; 
                }
                if(isset($_POST['delivery_address1'])){
                    $model->delivery_address1 = $_POST['delivery_address1']; 
                }
                 if(isset($_POST['delivery_address2'])){
                    $model->delivery_address2 = $_POST['delivery_address2']; 
                }  
               if(is_numeric($_POST['delivery_city'])){
                     $model->delivery_city_id = $_POST['delivery_city'];
                }else{
                     $model->delivery_city_id = $_POST['delivery_city_id'];
                }
                if(is_numeric($_POST['delivery_state'])){
                     $model->delivery_state_id = $_POST['delivery_state'];
                }else{
                     $model->delivery_state_id = $_POST['delivery_state_id'];
                }
                if(is_numeric($_POST['delivery_country'])){
                     $model->delivery_country_id = $_POST['delivery_country'];
                }else{
                     $model->delivery_country_id = $_POST['delivery_country_id'];
                }
                 if(isset($_POST['name_of_organization'])){
                    $model->name_of_organization = $_POST['name_of_organization']; 
                } 
                 if(isset($_POST['unique_registration_number'])){
                    $model->unique_registration_number = $_POST['unique_registration_number']; 
                }
                  if(isset($_POST['business_category'])){
                    $model->business_category = $_POST['business_category']; 
                }
                if(isset($_POST['corporate_address1'])){
                    $model->corporate_address1 = $_POST['corporate_address1']; 
                }
                 if(isset($_POST['corporate_address2'])){
                    $model->corporate_address2 = $_POST['corporate_address2']; 
                }
                 if(is_numeric($_POST['corporate_city'])){
                     $model->corporate_city_id = $_POST['corporate_city'];
                }else{
                     $model->corporate_city_id = $_POST['corporate_city_id'];
                }
                if(is_numeric($_POST['corporate_state'])){
                     $model->corporate_state_id = $_POST['corporate_state'];
                }else{
                     $model->corporate_state_id = $_POST['corporate_state_id'];
                }
                if(is_numeric($_POST['corporate_country'])){
                     $model->corporate_country_id = $_POST['corporate_country'];
                }else{
                     $model->corporate_country_id = $_POST['corporate_country_id'];
                }
                 if(isset($_POST['landline'])){
                    $model->landline = $_POST['landline']; 
                }
                 if(isset($_POST['mobile_line'])){
                    $model->mobile_line = $_POST['mobile_line']; 
                }
                if(isset($_POST['delivery_address1'])){
                    $model->delivery_address1 = $_POST['delivery_address1']; 
                }
                 if(isset($_POST['delivery_address2'])){
                    $model->delivery_address2 = $_POST['delivery_address2']; 
                }  
                if(is_numeric($_POST['delivery_city'])){
                     $model->delivery_city_id = $_POST['delivery_city'];
                }else{
                     $model->delivery_city_id = $_POST['delivery_city_id'];
                }
                if(is_numeric($_POST['delivery_state'])){
                     $model->delivery_state_id = $_POST['delivery_state'];
                }else{
                     $model->delivery_state_id = $_POST['delivery_state_id'];
                }
                if(is_numeric($_POST['delivery_country'])){
                     $model->delivery_country_id = $_POST['delivery_country'];
                }else{
                     $model->delivery_country_id = $_POST['delivery_country_id'];
                }
                 if(isset($_POST['name_of_organization'])){
                    $model->name_of_organization = $_POST['name_of_organization']; 
                } 
                 if(isset($_POST['unique_registration_number'])){
                    $model->unique_registration_number = $_POST['unique_registration_number']; 
                }
                  if(isset($_POST['business_category'])){
                    $model->business_category = $_POST['business_category']; 
                }
                if(isset($_POST['corporate_address1'])){
                    $model->corporate_address1 = $_POST['corporate_address1']; 
                }
                 if(isset($_POST['corporate_address2'])){
                    $model->corporate_address2 = $_POST['corporate_address2']; 
                }
                 if(is_numeric($_POST['corporate_city'])){
                     $model->corporate_city_id = $_POST['corporate_city'];
                }else{
                     $model->corporate_city_id = $_POST['corporate_city_id'];
                }
                if(is_numeric($_POST['corporate_state'])){
                     $model->corporate_state_id = $_POST['corporate_state'];
                }else{
                     $model->corporate_state_id = $_POST['corporate_state_id'];
                }
                if(is_numeric($_POST['corporate_country'])){
                     $model->corporate_country_id = $_POST['corporate_country'];
                }else{
                     $model->corporate_country_id = $_POST['corporate_country_id'];
                }
                 if(isset($_POST['landline'])){
                    $model->landline = $_POST['landline']; 
                }
                 if(isset($_POST['mobile_line'])){
                    $model->mobile_line = $_POST['mobile_line']; 
                }
            
                      
                $model->role = $_POST['role'];
                $model->category = $_POST['category'];
                 $model->status = $_POST['status'];
                $model->update_time = new CDbExpression('NOW()');
                $model->update_user_id = Yii::app()->user->id;
                
                $model->password = '';
               $model->password_repeat = '';
               
              //get the domain name
                $user_name = $this->getTheUsername($_id);
                
                $icon_error_counter  = 0;
                
                if($_FILES['picture']['name'] != ""){
                    if($this->isIconTypeAndSizeLegal()){
                        
                       $icon_filename = $_FILES['picture']['name'];
                       $icon_size = $_FILES['picture']['size'];
                        
                    }else{
                       
                        $icon_error_counter = $icon_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    //$model->icon = $this->retrieveThePreviousIconName($_id);
                    $icon_filename = $this->retrieveThePreviousIconName($_id);
                    $icon_size = $this->retrieveThePrreviousIconSize($_id);
             
                }//end of the if icon is empty statement
                if($icon_error_counter ==0){
                   if($model->validate()){
                           $model->picture = $this->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                           $model->picture_size = $icon_size;
                           
                 if($model->save()) {
                        
                    //$userid = Yii::app()->user->id;
                    if(isset($model->role)){
                            
                          if($this->assignRoleToAUser($model->role, $model->id)) {
                              // $result['success'] = 'true';
                                $msg = 'User update was successful';
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "msg" => $msg)
                                    );
                              
                              
                                 } else {
                                     $msg = 'User update was not successful';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                                     
                                     
                                 } 
                        }else{
                            $msg = "Role is not assigned to this User";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                            
                            
                        }
                        
                   
                         
                     }else {
                         //$result['success'] = 'false';
                         $msg = 'User update was not successful';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                     }
                   }else{
                                
                                //delete all the moved files in the directory when validation error is encountered
                            $msg = "Validation Error: '$user_name' information update was not successful";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                            );
                          }
                        }elseif($icon_error_counter > 0){
                        //get the platform assigned icon width and height
                            $platform_width = $this->getThePlatformSetIconWidth();
                            $platform_height = $this->getThePlatformSeticonHeight();
                            $icon_types = $this->retrieveAllTheIconMimeTypes();
                            $icon_types = json_encode($icon_types);
                            $msg = "Please check your picture file type or size as picture must be of width '$platform_width'px and height '$platform_height'px. Picture is of types '$icon_types'";
                            header('Content-Type: application/json');
                                    echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                    "msg" => $msg)
                            );
                         
                        }  
	}
        
        
        /**
             * This is the function that gea user name
             */
            public  function getTheUsername($user_id){
                
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$user_id);
                $user= Members::model()->find($criteria);
                
                $name = $user['firstname']." ".$user['middlename']. " ". $user['lastname'];
                
                return $name;
                
            }

	/**
	 * Deletes a particular model.
	 * 
	 */
	public function actionDeleteThisMember()
	{
            //delete one user
            $_id = $_POST['id'];
            $model=Members::model()->findByPk($_id);
            if($model === null){
                $msg = "This model is null and there no data to delete";
                        header('Content-Type: application/json');
                        echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            
                            "msg" => $msg)
                       );
                                      
            }elseif($model->delete()){
                    
                    if($this->deleteRoleAssignedToAUser($model->role, $_id)){
                        $msg = "User was successfully deleted";
                        header('Content-Type: application/json');
                        echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            
                            "msg" => $msg)
                       );
                        
                        
                    }else {
                        $msg = "The user was deleted but his/her record(s) still exist in the role assignment table";
                        header('Content-Type: application/json');
                        echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            
                            "msg" => $msg)
                       );
                        
                        
                    }
                    
            } else {
                    $msg = "User record deletion was not successful";
                        header('Content-Type: application/json');
                        echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            
                            "msg" => $msg)
                       );
                            
                }
	}

	
	  /**
	 * Manages all models.
	 */
	public function actionListAllStaffMembers()
	{
		
            $userid = Yii::app()->user->id; 
            //get the domain of the logged in user
            
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='usertype=:type';
                $criteria->params = array(':type'=>'staff');
                $user= Members::model()->findAll($criteria);
                
                if($user===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                      header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "member" => $user
                                        
                                
                            ));
                       
                }
         
           
	}
        
        
        
          /**
	 * Manages all models.
	 */
	public function actionListAllMembers()
	{
		
            $userid = Yii::app()->user->id; 
            //get the domain of the logged in user
            
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
               // $criteria->condition='usertype=:type';
               // $criteria->params = array(':type'=>'staff');
                $user= Members::model()->findAll($criteria);
                
                if($user===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                      header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "member" => $user
                                        
                                
                            ));
                       
                }
         
           
	}
        
        
        
        
              
        /**
	 * Manages all models.
	 */
	public function actionListAllNonStaffMembers()
	{
		
            $userid = Yii::app()->user->id; 
            //get the domain of the logged in user
            
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='usertype=:type';
                $criteria->params = array(':type'=>'others');
                $user= Members::model()->findAll($criteria);
                
                if($user===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "member" => $user
                                        
                                
                            ));
                       
                }
         
           
	}
        
        /**
         * This is the function that will list all roles in the platform
         */

        public function actionListAllUserRoles()
	{
		
            
                $criteria = new CDbCriteria();
                $criteria->select = 'name';
                $criteria->condition='type=2';              
                $userrole= Authitem::model()->findAll($criteria);
               
                if($userrole===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            
                            "role" => $userrole)
                       );
                       
                }
	}
        
        /*
   * Obtain the users permissions
   */
  public function actionGetUserPermissions(){

    //ini_set('max_execution_time', 300);
    $_userid = Yii::app()->user->id;
    //retrieve all the authitem assigned to this user
    $criteria2 = new CDbCriteria();
    $criteria2->select = 'itemname';
    $criteria2->condition='userid=:id';
    $criteria2->params = array(':id'=>$_userid);
    $authitems= AuthAssignment::model()->findAll($criteria2);
	
    $iten = [];
    
    $itemtype = [];
    $permissions = [];
    $operations = [];
    $tasks = [];
    $roles = [];
    $i = 0;
    foreach($authitems as $item){ 
	//determine the type of the itemname	
	$iten[$i] = $item->itemname;
        $criteria3 = new CDbCriteria();
        $criteria3->select = 'name, type';
        $criteria3->condition='name=:name';
        $criteria3->params = array(':name'=>$iten[$i]);
        $authtype= Authitem::model()->findAll($criteria3);
            
       //$itemtype;
        $operations_children = [];
        $tasks_children = [];
        $roles_children = [];
        
        foreach($authtype as $item){
           // $type = $item->type;
            if($item->type == 0){
		//$itemname = $authtype->name;
                $operation_children = $this->getAllOperationChildren($item->name);
                //$operations_children = $operations_children + $operation_children;
               $operations = array_merge($operations, $operation_children);
            } elseif($item->type == 1) { //end of the authtype->type=0 statement
				
		$task_children = $this->getTaskOperationChildren($item->name);
                // $tasks_children = $tasks_children + $task;
                 $tasks = array_merge($tasks, $task_children);
			
            }elseif($item->type == 2){//end of the authtype->type=1 statement
		$role_children = $this->getRoleOperationChildren($item->name);
                //$roles_children = $roles_children + $role;
                 $roles = array_merge($roles, $role_children);
			
	    }//end of the authtype->type=2 statement
		
        }
		
           $i = $i + 1;
   } //end of the $authitems foreach loop
   header('Content-Type: application/json');
   echo CJSON::encode(array(
        "success" => mysql_errno() == 0,
       "operations"=>$operations,
       "tasks"=>$tasks,
       "roles"=>$roles
         ));
} //end of the function


/*
 * Get all the operations permisions
 */
public function getAllOperationChildren($itemname){
    
    //determine the children of the task authitem
    $item = [];
    $i = 0;
    
    $permissions = [];
    //foreach($itemname as $operationitem ){
        //$item[$i] = $operationitem->name;
        $criteria8 = new CDbCriteria();
        $criteria8->select = 'child';
        $criteria8->condition='parent=:parent';
        $criteria8->params = array(':parent'=>$itemname);
        $operationchildren= AuthItemChild::model()->findAll($criteria8);
        
        //determine the type of the child
        $childtype = [];
        if($operationchildren !== []){
        foreach($operationchildren as $child){
            $criteria9 = new CDbCriteria();
            $criteria9->select = 'name, type';
            $criteria9->condition='name=:name';
            $criteria9->params = array(':name'=>$child->child);
            $childrentype= Authitem::model()->findAll($criteria9);
            
            //process the role  child depending on its type
            $operation_in_operation = [];
            foreach($childrentype as $type){
            if($type->type == 0){
                 $operation_in_operation = getAllOperationChildren($type->name);
                 $permissions = array_merge($permissions ,$operation_in_operation);
            }//end of the else $childtype if statement
            }
            
        }//end of the $operationchildren as $childtype foreach statement
        $i = $i + 1;
    }else {
        $permissions = array_merge($permissions, array($itemname));
        return array(
            $permissions
        );
    }
   // }//end of the $itemname as $operationitem foreach statement
    
    return array(
        
        $permissions
     ); 
    
    
}//end of the function
  
/*
 * Get all the operations that makes up the task
 */
public function getTaskOperationChildren($itemname){
    
    //determine the children of the task authitem
    $item = [];
    $i = 0;
    
    $permissions = [];
    
   // foreach($itemname as $taskitem ){
       // $item[$i] = $taskitem->name;
        $criteria6 = new CDbCriteria();
        $criteria6->select = 'child';
        $criteria6->condition='parent=:parent';
        $criteria6->params = array(':parent'=>$itemname);
        $taskchildren= AuthItemChild::model()->findAll($criteria6);
        
        //determine the type of the child
        if($taskchildren !== []){
        $childtype = [];
        foreach($taskchildren as $child){
            $criteria7 = new CDbCriteria();
            $criteria7->select = 'name, type';
            $criteria7->condition='name=:name';
            $criteria7->params = array(':name'=>$child->child);
            $childrentype= Authitem::model()->findAll($criteria7);
            
            //process the role  child depending on its type
            $task_in_task = [];
            $operation_in_task = [];
            foreach($childrentype as $type){
            if($type->type == 0){
                 $operation_in_task = $this->getAllOperationChildren($type->name);
                 $permissions = array_merge($permissions, $operation_in_task);
            }elseif($type->type == 1){
                $task_in_task = $this->getTaskOperationChildren($type->name);
                $permissions = array_merge($permissions ,$task_in_task);
                
            }//end of the else $childtype if statement
        }
            
        }//end of the $taskchiledren as $childtype foreach statement
        $i = $i + 1;
    } else {
        
        $permissions = array_merge($permissions, array($itemname));
        return array(
            $permissions
        );
    } 
  //  }//end of the $itemname as $taskitem foreach statement
    
    return array(
        
        $permissions
      );
        
    
    
}//end of the function



/*
 * Get all the operations that makes up the role
 */
public function getRoleOperationChildren($itemname){
    
    //determine the children of the role authitem
    $item = [];
    $i = 0;
    
    $permissions = [];
    //foreach($itemname as $roleitem ){
       // $item[$i] = $roleitem['name'];
        $criteria4 = new CDbCriteria();
        $criteria4->select = 'child';
        $criteria4->condition='parent=:parent';
        $criteria4->params = array(':parent'=>$itemname);
        $rolechildren= AuthItemChild::model()->findAll($criteria4);
        
        //determine the type of the child
        if($rolechildren !== []){
        $childtype = [];
        $operation_in_role = [];
        $task_in_role = [];
        $role_in_role = [];
        foreach($rolechildren as $childname){
            $criteria5 = new CDbCriteria();
            $criteria5->select = 'name, type';
            $criteria5->condition='name=:name';
            $criteria5->params = array(':name'=>$childname->child);
            $childrentype= Authitem::model()->findAll($criteria5);
            
            //process the role  child depending on its type
            foreach($childrentype as $type){
            if($type->type == 0){
                 $operation_in_role = $this->getAllOperationChildren($type->name);
                  $permissions = array_merge($permissions, $operation_in_role);
            }elseif($type->type == 1){
                $task_in_role = $this->getTaskOperationChildren($type->name);
                $permissions = array_merge($permissions ,$task_in_role);
            }elseif($type->type == 2){
                $role_in_role = $this->getRoleOperationChildren($type->name);
                $permissions = array_merge($permissions, $role_in_role);
                
            }//end of the else $childtype if statement
          } 
            
        }//end of the $rolechiledren as $childtype foreach statement
       
        $i = $i + 1;
    } else {
        $permissions = array_merge($permissions, array($itemname));
        return array(
            $permissions
        );
    }
   // }//end of the $itemname as $roleitem foreach statement
    
    return array(
        $permissions
        );
       
    
    
}//end of the function
        
        /**
	 * Displays the login page
         * 
         */
	
         
          
	public function actionLogin()
	{
		
             $model = new Members('login');
            
                                             
               
                              
                $model->username = $_POST['username'];
                $model->password = $_POST['password'];
                
                //determine the users id
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='username=:name and status!=:status';
                $criteria->params = array(':name'=>$_POST['username'],':status'=>'inactive');
                $id = Members::model()->find($criteria);   
                
               $firstname = $id['firstname'] . " ". $id['lastname'];
               $name = $id['name'];
              //validate the users logon credentials
               
          if($this->validatePassword($model, $id->id,$model->password) && $model->login()) {
           //if($this->validatePassword($model, $id->id,$model->password)) {    
           // if($model->login()) {
                      header('Content-Type: application/json');
                      $msg = "$name";
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg" => $msg,
                           "firstname"=>$id['firstname'],
                           "subscription_active_status"=>$this->isMembershipSubscriptionActive($id['id']),
                           "userid"=>$id['id'],
                                                         
                            )
                           
                       );
          // }  

        }else {
                     header('Content-Type: application/json');
                      $msg= 'Incorrect username or password.';
                     echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg,
                         "firstname"=>$id['firstname'],
                        
                             )
                       );
                       
                }
             
         
 
	}
        
        
        
        /**
         * This is the function that determines if this member's subscription is active
         */
        public function isMembershipSubscriptionActive($member_id){
            $model = new MembershipSubscription;
            
            return $model->isMembershipSubscriptionActive($member_id);
        }
        
        
         /**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		//$this->redirect(Yii::app()->homeUrl);
                header('Content-Type: application/json');
                      $msg= "You just logged out. Please come again";
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg" => $msg
                            )
                           
                       );
	}
        
        
         
        
               
                 
        /**
	 * perform own password change
	 */
	public function actionChangePasswordByOwner()
	{
		
            //obtain the current password from the database
            $userid = Yii::app()->user->id;
            
            $model = Members::model()->findByPk($userid); 
                        
            $current_password = $_POST['password'];
            $password = $_POST['new_password'];
            $password_repeat = $_POST['password_repeat'];
                 
            //determine the value of the stored passwoord
            
            $criteria2 = new CDbCriteria();
            $criteria2->select = 'id, password';
            $criteria2->condition='id=:id';
            $criteria2->params = array(':id'=>$userid);
            $user = Members::model()->find($criteria2);
            
            //ascertain that the new password is the same with the stored password
            if($model->hashPassword($current_password) === $user->password){
                               
                if($password === $password_repeat){
                    
                    $model->password = $password;
                    $model->password_repeat = $password_repeat;
                    
                    if($model->getPasswordMinLengthRule($password)){
                        
                        if($model->getPasswordMaxLengthRule($password )){
                            
                            if($model->getPasswordCharacterPatternRule($password)){
                                
                                    //$model->password = $model->hashPassword($newpassword);
                            if($model->save()){
                                $msg = 'Password was successfully changed';
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                        "success" => mysql_errno() == 0,
                                         "msg" => $msg,
                                ));
                 
                            }else{
                            $msg = 'Password change was not successful';
                             header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                     "msg" => $msg,
                             ));  
                 
                         }
                                
                                
                                
                                
                            }else{
                                
                                $msg = 'Password must contain at least one number(0-9), at least one lower case letter(a-z), at least one upper case letter(A-Z), and at least one special character(@,&,$ etc)';
                                 header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() != 0,
                                 "msg" => $msg,
                                )); 
                            }
                            
                        }else{
                                $msg = 'The maximum Password length allowed is sixty(60)';
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                     "success" => mysql_errno() != 0,
                                    "msg" => $msg,
                            )); 
                            
                        }
                        
                        
                    }else{
                        $msg = 'The minimum Password length allowed is eight(8)';
                             header('Content-Type: application/json');
                            echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg,
                       )); 
                        
                        
                    }
                
                           
            }else{
               $msg = 'Repeat Password do not match the new password';
              header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                           "msg" => $msg,
                       )); 
                
                
            }
                
                
            }else{
                 $msg = 'Invalid current password. Try again';
                 header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                           "msg" => $msg,
                       )); 
                
                
            }
            
 
               
	}
        
        
        /**
	 * perform password change for other users
	 */
	public function actionChangePasswordForOtherUsers()
	{
		
            //obtain the current password from the database
            $_id = $_POST['id'];
            
            $model = Members::model()->findByPk($_id); 
           // $model->scenario = 'password_reset';
                        
            $password_repeat = $_POST['password_repeat'];
            $password = $_POST['new_password'];
            
            $model->password = $password;
            $model->password_repeat = $password_repeat;
            
            //determine if the password meet the specified length
              if($model->getPasswordMinLengthRule($password)){
                    if($model->getPasswordMaxLengthRule($password )){
              
                                     
                        //determine if the password matches the specified patterns
                      if($model->getPasswordCharacterPatternRule($password)){
              
              
                       
                               //ascertain that the new password is the same with the stored password
                
                
            
                             if($password === $password_repeat){
                                 
                                 //$model->password = $password;                
                               if($model->validate()){ 
                               // $model->password = md5($password);
                               //$model->password = $model->hashPassword($password);
                           
                                 if($model->save()){
                                        $msg = 'Password was successfully changed';
                                         header('Content-Type: application/json');
                                          echo CJSON::encode(array(
                                          "success" => mysql_errno() == 0,
                                          "msg" => $msg,
                                        ));
                 
                                }else{
                                     $msg = 'Password change was not successful';
                                    header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                     "success" => mysql_errno() != 0,
                                    "msg" => $msg,
                                   )); 
                                 
                             }   
                                  
                 
                        }else{
                              $msg = 'Validation error detected';
                                    header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                     "success" => mysql_errno() != 0,
                                    "msg" => $msg,
                                   )); 
                             
                         }
                      
                     
              
                
                
                        }else{
                            $msg = 'Confirm Password do not match the new password';
                             header('Content-Type: application/json');
                            echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg,
                       )); 
                
                
            }
                            
                            
                       
       }else{
             $msg = 'Password must contain at least one number(0-9), at least one lower case letter(a-z), at least one upper case letter(A-Z), and at least one special character(@,&,$ etc)';
                             header('Content-Type: application/json');
                            echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg,
                       )); 
            
            
            
        }
       
      
                        
                        
                        
                        
        }else{
            $msg = 'The maximum Password length allowed is sixty(60)';
                             header('Content-Type: application/json');
                            echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg,
                       )); 
            
            
        }
                   
                   
                   
                   
               }else{
                   $msg = 'The minimum Password length allowed is eight(8)';
                             header('Content-Type: application/json');
                            echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg,
                       )); 
                   
                   
               }
        
        
               
               
	}
        
             
        
        
        /**
         * 
         * @param type $model
         * Save user information during creation and  update without the user picture
         */
        
        public function saveUserInfoWithoutPicture($model, $userid=null){
            if($_FILES['picture']['name'] == null AND $model->id === null) {
                $model->picture = $this->provideUserIconWhenUnavailable($model);
                if($model->save()) {
                        
                    //$userid = Yii::app()->user->id;
                    if(isset($model->role)){
                            
                          if($this->assignRoleToAUser($model->role, $model->id)) {
                              // $result['success'] = 'true';
                                $msg = 'User Creation/Update was successful';
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "msg" => $msg)
                                    );
                              
                              
                                 } else {
                                     $msg = 'User creation/Update was not successful';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                                     
                                     
                                 } 
                        }else{
                            $msg = "Role is not assigned to this User";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                            
                            
                        }
                        
                   
                         
                     }else {
                         //$result['success'] = 'false';
                         $msg = 'User creation/Update was not successful';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                     }
            
                
                
            }elseif($_FILES['picture']['name'] == null AND $model->id !== null) {
                
                if($model->validate()){
                     if($model->save()) {
                        
                    //$userid = Yii::app()->user->id;
                    if(isset($model->role)){
                            
                          if($this->assignRoleToAUser($model->role, $model->id)) {
                              // $result['success'] = 'true';
                                $msg = 'User Creation/Update was successful';
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "msg" => $msg)
                                    );
                              
                              
                                 } else {
                                     $msg = 'User creation/Update was not successful';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                                     
                                     
                                 } 
                        }else{
                            $msg = "Role is not assigned to this User";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                            
                            
                        }
                        
                   
                         
                     }else {
                         //$result['success'] = 'false';
                         $msg = 'User creation/Update was not successful';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                     }
                    
                }else{
                    
                    $msg = 'There is a validation issue';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                }
               
                
            }
                
            
            
        }
        
        /**
         * 
         * @param type $model
         * @param type $picture
         * save user information with the picture during creation and update
         */
        
        public function saveUserInfoWithPictureInclusive($model, $picture, $userid=null){
            
           // $userid = Yii::app()->user->id;
            if(isset($_FILES['picture']['tmp_name'])){
                    $tmpName = $_FILES['picture']['tmp_name'];
                    $fileName = $_FILES['picture']['name'];
                    $type = $_FILES['picture']['type'];
                    $size = $_FILES['picture']['size'];
                  
                }
               if($fileName !== null) {
                      $iconFileName = time().'_'.$fileName;
                      $model->picture = $iconFileName;
                }
                //Testing for the file extension and size
                if($type === 'image/png'|| $type === 'image/jpg' || $type === 'image/jpeg'){
                   if($size <= 256 * 256 * 2){
                    if($model->validate()){
                        if($model->save())
                     {
                     // upload the file
                     if($fileName !== null) // validate to save file
                          $filepath = Yii::app()->params['users'].$iconFileName;
                            move_uploaded_file($tmpName,  $filepath);
                           
                            //assign primary role to the user in the authassignment table
                             $cmd1 =Yii::app()->db->createCommand();
                          if(isset($model->role)){
                              //assign the role to the user
                             if($this->assignRoleToAUser($model->role, $model->id)){
                                 $msg = 'User Creation/Update was successful';
                                 header('Content-Type: application/json');
                                 echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "msg" => $msg)
                                    );
                                 
                                 
                             }else{
                                 $msg = 'User creation/Update was not successful';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                                 
                             } 
                             
                          }else{
                              $msg = "Role is not assigned to this User";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                              
                              
                          }
                           
                          
                    }else {
                            $msg = 'User creation/Update was not successful';
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  }
                        
                    }else {
                        $msg = 'There is a validation issue';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                        
                    }
                     
                   }else {
                       $msg = 'Icon/Image file is too large: Maximum file size allowed is 131kb';
				header('Content-Type: application/json');
				echo CJSON::encode(array(
                                        "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                                    );                  
                   }
                   
                }else {
                    $msg = 'Wrong file Type. Only jpeg or jpg or png file is allowed ';
                         header('Content-Type: application/json');
			   echo CJSON::encode(array(
                                "success" => mysql_errno() != 0,
                                "msg" => $msg)
                          );
                    
                    
                }
            
        }
        
        
               
        
        /**
	 * Assign new role to a user 
	 */
	public function assignRoleToAUser($role, $userid)
	{
		
             //confirm that the role exist in the authitem table
             $cmd =Yii::app()->db->createCommand();
                $cmd->select('COUNT(*)')
                    ->from('authitem')
                    ->where('name' == "$role");
                $result = $cmd->queryScalar();
                
                if($result > 0){
                    //confirm if the user aleady had been assigned a role before
                    $cmd =Yii::app()->db->createCommand();
                        $cmd->select('COUNT(*)')
                        ->from('authassignment')
                         ->where('userid' == $userid);
                    $result1 = $cmd->queryScalar();
                    
                     if($result1 > 0){
                         //delete the previous role(s) of the user and insert the new role
                          $cmd->delete('authassignment', 'userid=:id', array(':id'=>$userid ));
                         
                         
                     }
                     $cmd->insert('authassignment',
                                        array(
                                           'userid'=>$userid,
                                           'itemname'=>$role,
                                          
                     ));
                  
                    return true;
                    
                    
                }else{
                  
                    return false;
                }
         
               
	}
        
        
        /**
	 * delete a role assigned to a user 
	 */
	public function deleteRoleAssignedToAUser($role, $userid)
	{
            //determine if the role actually was assigned to the user
            $cmd =Yii::app()->db->createCommand();
                $cmd->select('COUNT(*)')
                    ->from('authassignment')
                    ->where('itemname' == $role and 'userid'==$userid);
                $result = $cmd->queryScalar();
                
                if($result > 0){
                    //delete the role and assignment to the user
                     $cmd->delete('authassignment', 'userid=:id and itemname=:item', array(':id'=>$userid, ':item'=>$role ));
                      
                    return true;
                }else{
                  
                    return false;
                    
                }
                
		
             
               
	}
        
          /**
        * Provide icon when unavailable
	 */
	public function provideUserIconWhenUnavailable($model)
	{
		return 'user_unavailable.png';
	}
        
        
         /*
         * validate the password during authorisation
         */
        public function validatePassword($model, $id, $password){
         
            //determine the existing password
            
           $criteria = new CDbCriteria();
           $criteria->select = 'id, password';
           $criteria->condition='id=:id';
           $criteria->params = array(':id'=>$id);
           $existing_password = Members::model()->find($criteria);   
        
           return $model->hashPassword($password)=== $existing_password->password;
           
            
        }
        
        
        //get the hashed password
        
        public function getThisHashedPassword($model,$password){
             return $model->hashPassword($password);
        }
        
        //get the requesting username
        public function getTheRequestingUsername($model, $id){
            
           $criteria = new CDbCriteria();
           $criteria->select = 'id, username';
           $criteria->condition='id=:id';
           $criteria->params = array(':id'=>$id);
           $existing_username = Members::model()->find($criteria); 
           
           return $existing_username['username'];
           
        }
        
        /**
         * Confirm if the requesting username is actually in the database
         */
        public function isUsernameRegistered($model,$id,$username){
            if($this->getTheRequestingUsername($model, $id) == $username){
                return true;
            }else{
                return false;
            }
        }
        
      
        /**
         * This is a function that determines if a user has a particular privilege assigned to him
         */
        public function determineIfAUserHasThisPrivilegeAssigned($userid, $privilegename){
            
             $allprivileges = [];
            //spool all the privileges assigned to a user
                $criteria7 = new CDbCriteria();
                $criteria7->select = 'itemname, userid';
                $criteria7->condition='userid=:userid';
                $criteria7->params = array(':userid'=>$userid);
                $priv= AuthAssignment::model()->find($criteria7);
                
                //retrieve all the children of the role
                
                $criteria = new CDbCriteria();
                $criteria->select = 'child';
                $criteria->condition='parent=:parent';
                $criteria->params = array(':parent'=>$priv['itemname']);
                $allprivs= AuthItemChild::model()->findAll($criteria);
                 
                //check to see if this privilege exist for this user
                foreach($allprivs as $pris){
                    if($this->privilegeType($pris['child'])== 0){
                        $allprivileges[] = $pris['child'];
                        
                    }elseif($this->privilegeType($pris['child'])== 1){
                        
                       $allprivileges[] = $this->retrieveAllTaskPrivileges($pris['child']); 
                    }elseif($this->privilegeType($pris['child'])== 2){
                        
                        $allprivileges[] = $this->retrieveAllRolePrivileges($pris['child']);
                    }
                    
                    
                    
                    
                }
               
                
                if(in_array($privilegename, $allprivileges)){
                    
                    return true;
                     
                }else{
                    
                    return false;
                     
                }
                
                
                
                
                
           
           
            
           
        }
        
        
        /**
         * This is the function that returns all member privileges of a task
         */
        public function retrieveAllTaskPrivileges($task){
            
            $member = [];
            
                $criteria = new CDbCriteria();
                $criteria->select = 'child';
                $criteria->condition='parent=:parent';
                $criteria->params = array(':parent'=>$task);
                $allprivs= AuthItemChild::model()->findAll($criteria);
                
                foreach($allprivs as $privs){
                    if($this->privilegeType($privs['child'])== 0){
                         $member[] = $privs['child'];
                        
                    }elseif($this->privilegeType($privs['child'])== 1){
                        
                        $member[] = $this->retrieveAllTaskPrivileges($privs['child']); 
                    }
                   
                    
                }
              return $member;
               
            
        }
        
        /**
         * This is the function that returns all members in a role
         */
        public function retrieveAllRolePrivileges($role){
            
            $member = [];
            
                $criteria = new CDbCriteria();
                $criteria->select = 'child';
                $criteria->condition='parent=:parent';
                $criteria->params = array(':parent'=>$role);
                $allprivs= AuthItemChild::model()->findAll($criteria);
                
                foreach($allprivs as $privs){
                    if($this->privilegeType($privs['child'])== 0){
                         $member[] = $privs['child'];
                        
                    }elseif($this->privilegeType($privs['child'])== 1){
                        
                        $member[] = $this->retrieveAllTaskPrivileges($privs['child']); 
                    }elseif($this->privilegeType($privs['child'])== 2){
                        
                        $member[] = $this->retrieveAllRolePrivileges($privs['child']); 
                    }
                   
                    
                }
              return $member;
                
            
        }
        
        
       
        
        /**
         * This is the function that determines a privilege type
         */
        public function privilegeType($privname){
            
            $criteria7 = new CDbCriteria();
                $criteria7->select = 'name, type';
                $criteria7->condition='name=:name';
                $criteria7->params = array(':name'=>$privname);
                $privs= Authitem::model()->find($criteria7);
                
                return $privs['type'];
                
                
        }
         
        
        
                    
        
            /**
         * This is the function that determines the type and size of icon file
         */
        public function isIconTypeAndSizeLegal(){
            
           $size = []; 
            if(isset($_FILES['picture']['name'])){
                $tmpName = $_FILES['picture']['tmp_name'];
                $iconFileName = $_FILES['picture']['name'];    
                $iconFileType = $_FILES['picture']['type'];
                $iconFileSize = $_FILES['picture']['size'];
            } 
           if (isset($_FILES['picture'])) {
             $filename = $_FILES['picture']['tmp_name'];
             list($width, $height) = getimagesize($filename);
           }
      

           
            $platform_width = $this->getThePlatformSetIconWidth();
            $platform_height = $this->getThePlatformSeticonHeight();
            
            $width = $width;
            $height = $height;
           
            //$size = $width * $height;
           
            $icontypes = $this->retrieveAllTheIconMimeTypes();
            
          
           
            //if(($iconFileType === 'image/png'|| $iconFileType === 'image/jpg' || $iconFileType === 'image/jpeg') && ($iconFileSize = 256 * 256)){
            if((in_array($iconFileType,$icontypes)) && ($platform_width <= $width && $platform_height <= $height)){
                return true;
               
            }else{
                return false;
            }
            
        }



/**
         * This is the function that retrieves the previous icon of the task in question
         */
        public function retrieveThePreviousIconName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = Members::model()->find($criteria);
            
            
            return $icon['picture'];
            
            
        }
        
        /**
         * This is the function that retrieves the previous icon size
         */
        public function retrieveThePrreviousIconSize($id){
           
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = Members::model()->find($criteria);
            
            
            return $icon['picture_size'];
        }
		
		
		
		 /**
         * This is the function that gets the platform height setting
         */
        public function getThePlatformSeticonHeight(){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
           // $criteria->condition='id=:id';
           // $criteria->params = array(':id'=>$id);
            $icon = PlatformSettings::model()->find($criteria); 
            
            return $icon['icon_height'];
        }
		
		
		
		 /**
         * This is the function that gets the platform icon set width
         */
        public function getThePlatformSetIconWidth(){
            
           $criteria = new CDbCriteria();
            $criteria->select = '*';
           // $criteria->condition='id=:id';
           // $criteria->params = array(':id'=>$id);
            $icon = PlatformSettings::model()->find($criteria); 
            
            return $icon['icon_width'];
        }
		
		
		
		/**
         * This is the function that retrieves all icon mime types in the platform
         */
        public function retrieveAllTheIconMimeTypes(){
            
            $icon_mimetype = [];
            $icon_types = [];
            $criteria = new CDbCriteria();
            $criteria->select = '*';
           // $criteria->condition='id=:id';
           // $criteria->params = array(':id'=>$id);
            $icon_mime = PlatformSettings::model()->find($criteria); 
            
            $icon_mimetype = explode(',',$icon_mime['icon_mime_type']);
            foreach($icon_mimetype as $icon){
                $icon_types[] =$icon; 
                
            }
            
            return $icon_types;
            
        }
		
		
		
		/**
         * This is the function that moves icons to its directory
         */
        public function moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename){
            
            if(isset($_FILES['picture']['name'])){
                        $tmpName = $_FILES['picture']['tmp_name'];
                        $iconName = $_FILES['picture']['name'];    
                        $iconType = $_FILES['picture']['type'];
                        $iconSize = $_FILES['picture']['size'];
                  
                   }
                    
                    if($iconName !== null) {
                        if($model->id === null){
                          //$iconFileName = $icon_filename; 
                          if($icon_filename != 'user_unavailable.png'){
                                $iconFileName = time().'_'.$icon_filename;  
                            }else{
                                $iconFileName = $icon_filename;  
                            }    
                          
                           // upload the icon file
                        if($iconName !== null){
                            	$iconPath = Yii::app()->params['icons'].$iconFileName;
				move_uploaded_file($tmpName,  $iconPath);
                                        
                        
                                return $iconFileName;
                        }else{
                            $iconFileName = $icon_filename;
                            return $iconFileName;
                        } // validate to save file
                        }else{
                            if($this->noNewIconFileProvided($model->id,$icon_filename)){
                                $iconFileName = $icon_filename; 
                                return $iconFileName;
                            }else{
                            if($icon_filename != 'user_unavailable.png'){
                                
                                if($this->removeTheExistingIconFile($model->id)){
                                 $iconFileName = time().'_'.$icon_filename; 
                                 //$iconFileName = time().$icon_filename;  
                                   $iconPath = Yii::app()->params['icons'].$iconFileName;
                                   move_uploaded_file($tmpName,$iconPath);
                                   return $iconFileName;
                                    
                                   // $iconFileName = time().'_'.$icon_filename;  
                                    
                             }
                            }
                                
                                
                            }
                            
                            //$iconFileName = $icon_filename; 
                                              
                            
                        }
                      
                     }else{
                         $iconFileName = $icon_filename;
                         return $iconFileName;
                     }
					
                       
                               
        }
        
		
		
		 /**
         * This is the function that removes an existing video file
         */
        public function removeTheExistingIconFile($id){
            
            //retreve the existing zip file from the database
            
            if($this->isTheIconNotTheDefault($id)){
                
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= Members::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "c:\\xampp\htdocs\cobuy_images\\icons\\";
               // $iconpath = '..\appspace_assets\icons'.$icon['icon'];
                $filepath =$directoryPath.$icon['picture'];
                //$filepath = $directoryPath.$iconpath;
                
                if(unlink($filepath)){
                    return true;
                }else{
                    return false;
                }
                
            }else{
                return true;
            }
                
            
            
        }
        
	
        /**
         * This is the function that determines if  a tooltype icon is the default
         */
        public function isTheIconNotTheDefault($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= members::model()->find($criteria);
                
                if($icon['picture'] == 'user_unavailable.png' || $icon['picture'] ===NULL){
                    return false;
                }else{
                    return true;
                }
        }
		
		
		
		/**
         * This is the function to ascertain if a new icon was provided or not
         */
        public function noNewIconFileProvided($id,$icon_filename){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= Members::model()->find($criteria);
                
                if($icon['picture']==$icon_filename){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        /**
         * This is the function to confirm if a user is logged in
         */
        public function actionconfirmIfUserIsLoggedIn(){
            $model = new Members;
            
            $userid = Yii::app()->user->id;
            
           //retreive the firstname of the logged in user
            $firstname = $model->getTheFirstNameOfTheLoggedInUser($userid);
            
            //confirm if user has item in the cart
           // $with_open_order = $this->isMemberWithOpenOrder($userid);
            
            //get the membership number of this user
            
            $membership_number = $model->getTheMembershipNumberOfThisMember($userid);
            
            //get the registered email of this member
            $member_email = $model->getTheRegisteredEmailOfThisMember($userid);
            
            
            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                                //"selected" => $selected,
                                "userid" => $userid,
                                "firstname"=>$firstname,
                                "length" =>strlen($firstname),
                               // "with_open_order"=>$with_open_order,
                                "membership_number"=>$membership_number,
                                "member_email"=>$member_email
                             ));  
            
        }
        
        
        /**
         * This is the function that determines if a user has an open order
         */
        public function isMemberWithOpenOrder($member_id){
            $model = new Order;
            return $model->isMemberWithOpenOrder($member_id);
        }

   
        
       
        
        /**
         * This is the functijon that gets a user's name
         */
        public function actiongetThisUsername(){
            
            $id = $_POST['user_id'];
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$id);
            $user= Members::model()->find($criteria);
            
            $name = $user['firstname'] . ' ' . $user['lastname'];
            
            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                               "name"=>$name
            ));  
            
   
        }
        
        
        
        /**
         * This is the function that activates a member
         */
        public function actionActivateThisMember(){
            
            $id = $_POST['id'];
            $status = strtolower($_POST['status']);
            $user_name = $this->getThisMemberName($id);
            
            if($status == 'active'){
                $msg = "This user is already active on the platform" ;
                header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() != 0,
                               "msg"=>$msg
                                  
                ));
                
            }else{
                if($this->isUserActivationSuccessful($id)){
                    
                     $msg = "'$user_name' is activated successfully" ;
                     header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                               "msg"=>$msg
                                  
                        ));
                    
                }else{
                     $msg = "The attempt to activate '$user_name' was  not successful" ;
                     header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() != 0,
                               "msg"=>$msg
                                  
                ));
                }
                
                
            }
            
        }
        
        
        /**
         * This is the function that deactivates a member
         */
        public function actionDeactivateThisMember(){
            
            $id = $_POST['id'];
            $status = strtolower($_POST['status']);
            $user_name = $this->getThisMemberName($id);
            
            if($status == 'inactive'){
                $msg = "This user is already deactivated on the platform" ;
                header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() != 0,
                               "msg"=>$msg
                                  
                ));
                
            }else{
                if($this->isUserDeactivationSuccessful($id)){
                    
                     $msg = "'$user_name' is deactivated successfully" ;
                     header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                               "msg"=>$msg
                                  
                        ));
                    
                }else{
                     $msg = "The attempt to deactivate '$user_name' was  not successful" ;
                     header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() != 0,
                               "msg"=>$msg
                                  
                ));
                }
                
                
            }
            
        }
        
        
        
        /**
         * This is the function that suspend a member
         */
        public function actionSuspendThisMember(){
            
            $id = $_POST['id'];
            $status = strtolower($_POST['status']);
            $user_name = $this->getThisMemberName($id);
            
            if($status == 'suspended'){
                $msg = "This user is already suspended on the platform" ;
                header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() != 0,
                               "msg"=>$msg
                                  
                ));
                
            }else{
                if($this->isUserSuspensionSuccessful($id)){
                    
                     $msg = "'$user_name' is suspended successfully" ;
                     header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                               "msg"=>$msg
                                  
                        ));
                    
                }else{
                     $msg = "The attempt to suspend '$user_name' was  not successful" ;
                     header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() != 0,
                               "msg"=>$msg
                                  
                ));
                }
                
                
            }
            
        }
        
        
        /**
         * This is the function that retreieves the name of a member
         */
        public function getThisMemberName($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $user= Members::model()->find($criteria);
                
                $name = $user['lastname'] .  ''. $user['firstname'];
                
                return $name;
            
        }
        
        
        /**
         * This is the function that effects the activation of a member
         */
        public function isUserActivationSuccessful($id){
            
             $cmd =Yii::app()->db->createCommand();
             $result = $cmd->update('members',
                                  array(
                                    'status'=>'active',
                                      'activated_time'=>new CDbExpression('NOW()'),
                                      'activated_by'=>Yii::app()->user->id
                                   
                               
		
                            ),
                     ("id=$id"));
            
           if($result>0){
               return true;
           }else{
               return false;
           }
            
        }
        
        
        
         /**
         * This is the function that effects the activation of a member
         */
        public function isUserDeactivationSuccessful($id){
            
             $cmd =Yii::app()->db->createCommand();
             $result = $cmd->update('members',
                                  array(
                                    'status'=>'inactive',
                                      'deactivated_time'=>new CDbExpression('NOW()'),
                                      'deactivated_by'=>Yii::app()->user->id
                                   
                               
		
                            ),
                     ("id=$id"));
            
           if($result>0){
               return true;
           }else{
               return false;
           }
            
        }
        
        
        
         /**
         * This is the function that effects the suspension of a member
         */
        public function isUserSuspensionSuccessful($id){
            
             $cmd =Yii::app()->db->createCommand();
             $result = $cmd->update('members',
                                  array(
                                    'status'=>'suspended',
                                      'suspended_time'=>new CDbExpression('NOW()'),
                                      'suspended_by'=>Yii::app()->user->id
                                   
                               
		
                            ),
                     ("id=$id"));
            
           if($result>0){
               return true;
           }else{
               return false;
           }
            
        }
        
        
        
        /**
         * This is the function that generates a members number
         */
        public function actiongenerateNewMemberNumber(){
            
            $member_id = $_POST['id'];
            $city_id = $_POST['city_id'];
            $state_id = $_POST['state_id'];
            $country_id = $_POST['country_id'];
            
            //get the city number code
            $city_code = $this->getThisCityNumberCode($city_id);
            
            //get the state code
            $state_code = $this->getThisStateNumberCode($state_id);
            
            //get the two letters of a country code
            $country_code = $this->getThisCountryFirstTwoLettetCode($country_id);
            
            //get the member number sequence on that city
            $member_year_of_registration = $this->getThisMemberYearOfRegistration($member_id);
            
            //get the current sequence number for this city
            $city_sequence_number = $this->getThisCityNewPaddedSequenceNumber($city_id);
                       
            
            //get the new membership code for this member
            $membership_code = $state_code.$city_code.$city_sequence_number.$member_year_of_registration.$country_code;
            
            //get this member's name 
            $member_name = $this->getTheUsername($member_id);
            if($this->membershipNumberNotAlreadyInExistence($membership_code)){
                $this->assignMembershipNumberToMember($member_id,$membership_code);
                $msg = "'$member_name' membership number is: $membership_code " ;
                     header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                               "msg"=>$msg
                                  
                ));
            }else{
                $membership_code = $membership_code.$this->uniqueNumberDifferentiator();
                $this->assignMembershipNumberToMember($member_id,$membership_code);
                $msg = "'$member_name' membership number is: $membership_code " ;
                     header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                               "msg"=>$msg
                                  
                ));
            }
            
        }
        
        /**
         * This is the function that assigns membership number
         */
        public function assignMembershipNumberToMember($member_id,$membership_code){
            $model = new Members;
            
            return $model->assignMembershipNumberToMember($member_id,$membership_code);
            
        }
        
        /**
         * This is the function that confirms that a given member number is not already in existence
         */
        public function membershipNumberNotAlreadyInExistence($membership_code){
            
            $model = new Members;
            
            return $model->isMembershipNumberNotAlreadyExisting($membership_code);
        }
        
        
        
        /**
         * This is the function that get a city padded sequence number
         */
        public function getThisCityNewPaddedSequenceNumber($city_id){
            
            $model = new City;
            return $model->getThisCityNewPaddedSequenceNumber($city_id);
        }
        
        
        /**
         * This is the function that gets this member ywar of registration
         */
        public function getThisMemberYearOfRegistration($member_id){
            $model = new Members;
            return $model->getThisMemberYearOfRegistration($member_id);
        }
        
        
        /**
         * This is the function that gets the  first two letters of a country code
         */
        public function getThisCountryFirstTwoLettetCode($country_id){
            $model = new Country;
            
            return $model->getThisCountryFirstTwoLettetCode($country_id);
        }
        
        /**
         * This is the function that gets the state number code
         */
        public function getThisStateNumberCode($state_id){
             $model = new State;
            
            return $model->getThisStateNumberCode($state_id);
        }
        
        
        /**
         * This is the function that gets a city code
         */
        public function getThisCityNumberCode($city_id){
            
            $model = new City;
            
            return $model->getThisCityNumberCode($city_id);
        }
        
        
         /**
         * This is the function that gets the new unique number differentiator
         */
        public function uniqueNumberDifferentiator(){
                
            $model = new PlatformSettings;
                             
            return $model->uniqueNumberDifferentiator();
                
        }
            
        
        
        /**
         * This is the function that registers a new non-staff member from the client
         * 
         */
        public function actionRegisterThisNewMember(){
            
                $model = new Members;
            
                
                if($_POST['subscription_type'] == strtolower('monthly')){
                    $number_of_months = $_POST['number_of_months'];
                }else{
                    $number_of_months = $_POST['number_of_years'] * 12;
                }
                $model->usertype = strtolower('others');
                $model->email = $_POST['email'];
                $model->username = $_POST['email'];
                $model->name = $_POST['name'];
             /**   if(is_numeric($_POST['city'])){
                     $model->city_id = $_POST['city'];
                }else{
                     $model->city_id = $_POST['city_id'];
                }
                if(is_numeric($_POST['state'])){
                     $model->state_id = $_POST['state'];
                }else{
                     $model->state_id = $_POST['state_id'];
                }
                if(is_numeric($_POST['country'])){
                     $model->country_id = $_POST['country'];
                }else{
                     $model->country_id = $_POST['country_id'];
                }
                $model->lastname = $_POST['lastname'];
                if(isset($_POST['middlename'])){
                    $model->middlename = $_POST['middlename'];
                }
                $model->firstname = $_POST['firstname'];
                if(isset($_POST['gender'])){
                    $model->gender = strtolower($_POST['gender']);
                }
                 if(isset($_POST['religion'])){
                    $model->religion = strtolower($_POST['religion']);
                }
                 if(isset($_POST['maritalstatus'])){
                    $model->maritalstatus = strtolower($_POST['maritalstatus']);
                }
                if(isset($_POST['dateofbirth'])){
                    $model->dateofbirth = date("Y-m-d H:i:s", strtotime($_POST['dateofbirth'])); 
                }
                if(isset($_POST['address1'])){
                    $model->address1 = $_POST['address1']; 
                }
                if(isset($_POST['address2'])){
                    $model->address2 = $_POST['address2']; 
                }
                if(isset($_POST['delivery_address1'])){
                    $model->delivery_address1 = $_POST['delivery_address1']; 
                }
                 if(isset($_POST['delivery_address2'])){
                    $model->delivery_address2 = $_POST['delivery_address2']; 
                }  
                 if(is_numeric($_POST['delivery_city'])){
                     $model->delivery_city_id = $_POST['delivery_city'];
                }else{
                     $model->delivery_city_id = $_POST['delivery_city_id'];
                }
                if(is_numeric($_POST['delivery_state'])){
                     $model->delivery_state_id = $_POST['delivery_state'];
                }else{
                     $model->delivery_state_id = $_POST['delivery_state_id'];
                }
                if(is_numeric($_POST['delivery_country'])){
                     $model->delivery_country_id = $_POST['delivery_country'];
                }else{
                     $model->delivery_country_id = $_POST['delivery_country_id'];
                }
                 if(isset($_POST['name_of_organization'])){
                    $model->name_of_organization = $_POST['name_of_organization']; 
                } 
                 if(isset($_POST['unique_registration_number'])){
                    $model->unique_registration_number = $_POST['unique_registration_number']; 
                }
                if(isset($_POST['business_category'])){
                    $model->business_category = $_POST['business_category']; 
                }
                if(isset($_POST['corporate_address1'])){
                    $model->corporate_address1 = $_POST['corporate_address1']; 
                }
                 if(isset($_POST['corporate_address2'])){
                    $model->corporate_address2 = $_POST['corporate_address2']; 
                }
                 if(is_numeric($_POST['corporate_city'])){
                     $model->corporate_city_id = $_POST['corporate_city'];
                }else{
                     $model->corporate_city_id = $_POST['corporate_city_id'];
                }
                if(is_numeric($_POST['corporate_state'])){
                     $model->corporate_state_id = $_POST['corporate_state'];
                }else{
                     $model->corporate_state_id = $_POST['corporate_state_id'];
                }
                if(is_numeric($_POST['corporate_country'])){
                     $model->corporate_country_id = $_POST['corporate_country'];
                }else{
                     $model->corporate_country_id = $_POST['corporate_country_id'];
                }
                 if(isset($_POST['landline'])){
                    $model->landline = $_POST['landline']; 
                }
                 if(isset($_POST['mobile_line'])){
                    $model->mobile_line = $_POST['mobile_line']; 
                }
              * 
              */
                
                $model->role =strtolower($_POST['role']);
                $model->category = $_POST['category'];
                $model->accounttype = $_POST['accounttype'];
                $model->status = strtolower("active");
                $model->create_time = new CDbExpression('NOW()');
                $model->create_user_id = Yii::app()->user->id;
                $password = $_POST['password'];
                $password_repeat = $_POST['passwordCompare'];
                
               // $name = $model->firstname . ' ' . $model->middlename . ' ' . strtoupper($model->lastname);
                if($this->isUsernameAndEmailUnique($model->username,$model->email)){
                    if($password === $password_repeat){
                    
                    $model->password = $password;
                    $model->password_repeat = $password_repeat;
                    
                    if($model->getPasswordMinLengthRule($password)){
                        
                        if($model->getPasswordMaxLengthRule($password )){
                            
                            if($model->getPasswordCharacterPatternRule($password)){
                                
                        
                  $icon_error_counter = 0;     
                 // $name = $model->firstname . ' ' . $model->middlename . ' ' . strtoupper($model->lastname);
                 if($_FILES['picture']['name'] != ""){
                    if($this->isIconTypeAndSizeLegal()){
                       
                       $icon_filename = $_FILES['picture']['name'];
                      $icon_size = $_FILES['picture']['size'];
                        
                    }else{
                       
                        $icon_error_counter = $icon_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    $icon_filename = $this->provideUserIconWhenUnavailable($model);
                   $icon_size = 0;
             
                }//end of the if icon is empty statement
                if($icon_error_counter ==0){
                   if($model->validate()){
                           $model->picture = $this->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                           $model->picture_size = $icon_size;
                           
                if($model->save()) {
                        
                    //$userid = Yii::app()->user->id;
                    if(isset($model->role)){
                            
                          if($this->assignRoleToAUser($model->role, $model->id)) {
                              if($this->isThisAssignmentOfMembershipTypeSuccessful($model->id,$_POST['membership_type'],$number_of_months,$_POST['gross'],$_POST['discount'],$_POST['net'],$_POST['is_term_acceptable'],$_POST['status'])){
                                //get the invoice number for this subscription
                                 $invoice_number = $this->getTheInvoiceNumberOfThisPayment($model->id,$_POST['membership_type']); 
                                  
                                // $result['success'] = 'true';
                                //$msg = "To complete this subscription, kindly user '$invoice_number' as the payment invoice number to effect the actual online payment on the redirected payment platform.";
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "invoice" => $invoice_number,
                                    "email"=>$model->getTheRegisteredEmailOfThisMember($model->id),
                                    "amount"=>$_POST['net'])
                                    );
                              
                              
                                 } else {
                                     $msg = 'You had successfully being registered to start using the Oneroof online store. However, if you need  to do much more than just buying, please contact customer care to process your membership type ';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                                     
                                     
                                 } 
                                  
                              }else{
                                  $msg = 'User creation was successful but assignment of role to this user was not successful. Please contact customer care for assistance';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                                  
                              }
                              
                        }else{
                            $msg = "Role is not assigned to this User. Please assign the role and continue with the registration";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                            
                            
                        }
                        
                   
                         
                     }else {
                         //$result['success'] = 'false';
                         $msg = 'Your registration was not sucessful. Please contact customer care for asistance';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                     }
                        
                   }else{
                                
                                //delete all the moved files in the directory when validation error is encountered
                            $msg = "Validation Error: There seem to be a field validation issue with your form. Try again or contact customer care for assistance ";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                            );
                          }
                        }elseif($icon_error_counter > 0){
                        //get the platform assigned icon width and height
                            $platform_width = $this->getThePlatformSetIconWidth();
                            $platform_height = $this->getThePlatformSeticonHeight();
                            $icon_types = $this->retrieveAllTheIconMimeTypes();
                            $icon_types = json_encode($icon_types);
                            $msg = "Please check your picture file type or size as picture must be of width '$platform_width'px and height '$platform_height'px. Picture is of types '$icon_types'";
                            header('Content-Type: application/json');
                                    echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                    "msg" => $msg)
                            );
                         
                        } 
                                
                                
                            }else{
                                
                                $msg = 'Password must contain at least one number(0-9), at least one lower case letter(a-z), at least one upper case letter(A-Z), and at least one special character(@,&,$ etc)';
                                 header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() != 0,
                                 "msg" => $msg,
                                )); 
                            }
                            
                        }else{
                                $msg = 'The maximum Password length allowed is sixty(60)';
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                     "success" => mysql_errno() != 0,
                                    "msg" => $msg,
                            )); 
                            
                        }
                        
                        
                    }else{
                        $msg = 'The minimum Password length allowed is eight(8)';
                             header('Content-Type: application/json');
                            echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg,
                       )); 
                        
                        
                    }
                
           
                
            }else{
               $msg = 'Repeat Password do not match the new password';
              header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                           "msg" => $msg,
                       )); 
                
                
            }
                    
                }else{
                     $msg = 'Your username and Email must be unique';
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                           "msg" => $msg,
                       )); 
                    
                }
                
            
        }
        
        
        /**
         * This is the function that determines if username and password is unique
         */
        public function isUsernameAndEmailUnique($username,$email){
            
            $model = new Members;
            $counter = 0;
            
            if($model->isUsernameNotUnique($username)){
                $counter = $counter + 1;
            }
            if($model->isEmailNotUnique($email)){
                $counter = $counter + 1;
            }
            
            if($counter == 0){
                return true;
            }else{
                return false;
            }
        }
        
        
        /**
         * This is the function that determines if membership assignemnt to a new member was successful
         */
        public function isThisAssignmentOfMembershipTypeSuccessful($id,$membership_type,$number_of_years,$gross,$discount,$net_amount,$is_term_acceptable,$status){
            
            $model = new MembershipSubscription;
            
            return $model->isThisAssignmentOfMembershipTypeSuccessful($id,$membership_type,$number_of_years,$gross,$discount,$net_amount,$is_term_acceptable,$status);
            
        }
        
        
        /**
         * This is the information that retreieves relevant account information for a member 
         */
        public function actionretrieveMemberAccountInformation(){
           $model = new MembershipSubscription; 
            $user_id = Yii::app()->user->id;
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$user_id);
            $member= Members::model()->find($criteria);
            
           if($model->isThisMemberWithActiveSubscription($member['id'])){
                
                //get this member active subscription id
                $subscription_id = $model->getMemberActiveSubscriptionId($member['id']);
                //get the membership type id of this member
                $membershiptype_id = $model->getThisMemberActiveMembershipTypeId($subscription_id);

                //get the membership type of this member
                $membershiptype_name = $this->getTheNameOfThisMembershipType($membershiptype_id);;
                
                              
                 //get the date of membership renewal for this member
                $renewal_date = $model->getTheDateOfMembershipRenewalOfThisActiveMembershipSubscription($subscription_id);
           
                //get the membership status of this member
                $membership_status = $model->getTheMembershipStatusOfThisMember($subscription_id);
            }else{
                 //get the membership type id of the most subscription with the farthest future end of date
                $membershiptype_id = $model->getThisMemberMembershipTypeIdOfTheLastToEndSubscription($user_id);
                
                //get the subscription id of this subscription
                $sub_id = $model->getTheSubscriptionIdWithFarthestDateToExpiration($member['id']);
                
                //get the name of this last to end subscription
                $membershiptype_name = $this->getTheNameOfThisMembershipType($membershiptype_id);
                
                 //get the date of membership renewal for this last to end subscription
                $renewal_date = $model->getTheDateOfMembershipRenewalOfThisLastToEndSubscription($sub_id);
                
                 //get the membership status of this last to end subscription
                $membership_status = $model->getTheMembershipStatusOfThisLastToEndSubscription($sub_id);
            }
            //get the name of the member city
            $member_city = $this->getTheNameofTheMemberCity($member['city_id']);
            
            //get the name of the member state
            $member_state = $this->getTheNameOfTheMemberState($member['state_id']);
            
            //get the name of the member country
            $member_country = $this->getTheNameOfTheMemberCountry($member['country_id']);
            
            //get the name of the member permanent delivery city
            $delivery_city = $this->getTheNameOfThePermamentDeliveryCity($member['delivery_city_id']);
            
            //get the name of the member permanent delivery state
            $delivery_state = $this->getTheOfTheNameOfThePermanentDeliveryState($member['delivery_state_id']);
            
            //get the name of the member permanent delivery country
            $delivery_country = $this->getTheNameOfThePermanentDeliveryCountry($member['delivery_country_id']);
            
            //get the name of the member corporate city
            $corporate_city = $this->getTheNameOfTheMemberCorporateCity($member['corporate_city_id']);
            
            //get the name of the member corporate state
            $corporate_state = $this->getTheNameOfTheMemberCorporateState($member['corporate_state_id']);
            
            //get the name of the member corporate country
            $corporate_country = $this->getTheNameOfTheMemberCorporateCountry($member['corporate_country_id']);
            
            //get the open order initiated by this member
            $order_id = $this->getTheOpenOrderInitiatedByMember($member['id']);
            
           
            
             header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                                //"selected" => $selected,
                                "member" => $member,
                                "membershiptype"=>$membershiptype_name,
                                "date_of_renewal"=>$renewal_date,
                                "member_city"=>$member_city,
                                "member_state"=>$member_state,
                                "member_country"=>$member_country,
                                "member_delivery_city"=>$delivery_city,
                                "member_delivery_state"=>$delivery_state,
                                "member_delivery_country"=>$delivery_country,
                                "member_corporate_city"=>$corporate_city,
                                "member_corporate_state"=>$corporate_state,
                                "member_corporate_country"=>$corporate_country,
                                "membership_status"=>$membership_status,
                                "membership_type_id"=>$membershiptype_id,
                                "order"=>$order_id,
                               
                                
                               
                             ));  
            
        }
        
        /**
         * This is the function that retrieves  another  members account information
         */
        public function actionretrieveThisMemberAccountInformation(){
            
            $model = new MembershipSubscription; 
            
            $user_id = $_REQUEST['other_member_id'];
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$user_id);
            $member= Members::model()->find($criteria);
            
            
            if($model->isThisMemberWithActiveSubscription($member['id'])){
                
                //get this member active subscription id
                $subscription_id = $model->getMemberActiveSubscriptionId($member['id']);
                //get the membership type id of this member
                $membershiptype_id = $model->getThisMemberActiveMembershipTypeId($subscription_id);

                //get the membership type of this member
                $membershiptype_name = $this->getTheNameOfThisMembershipType($membershiptype_id);;
                
                              
                 //get the date of membership renewal for this member
                $renewal_date = $model->getTheDateOfMembershipRenewalOfThisActiveMembershipSubscription($subscription_id);
           
                //get the membership status of this member
                $membership_status = $model->getTheMembershipStatusOfThisMember($subscription_id);
            }else{
                 //get the membership type id of the most subscription with the farthest future end of date
                $membershiptype_id = $model->getThisMemberMembershipTypeIdOfTheLastToEndSubscription($member['id']);
                
                //get tge subscription id
                $sub_id = $model->getTheSubscriptionIdWithFarthestDateToExpiration($member['id']);
                
                //get the name of this last to end subscription
                $membershiptype_name = $this->getTheNameOfThisMembershipType($sub_id);
                
                 //get the date of membership renewal for this last to end subscription
                $renewal_date = $model->getTheDateOfMembershipRenewalOfThisLastToEndSubscription($sub_id);
                
                 //get the membership status of this last to end subscription
                $membership_status = $model->getTheMembershipStatusOfThisLastToEndSubscription($sub_id);
            }
           
        
            //get the name of the member city
            $member_city = $this->getTheNameofTheMemberCity($member['city_id']);
            
            //get the name of the member state
            $member_state = $this->getTheNameOfTheMemberState($member['state_id']);
            
            //get the name of the member country
            $member_country = $this->getTheNameOfTheMemberCountry($member['country_id']);
            
            //get the name of the member permanent delivery city
            $delivery_city = $this->getTheNameOfThePermamentDeliveryCity($member['delivery_city_id']);
            
            //get the name of the member permanent delivery state
            $delivery_state = $this->getTheOfTheNameOfThePermanentDeliveryState($member['delivery_state_id']);
            
            //get the name of the member permanent delivery country
            $delivery_country = $this->getTheNameOfThePermanentDeliveryCountry($member['delivery_country_id']);
            
            //get the name of the member corporate city
            $corporate_city = $this->getTheNameOfTheMemberCorporateCity($member['corporate_city_id']);
            
            //get the name of the member corporate state
            $corporate_state = $this->getTheNameOfTheMemberCorporateState($member['corporate_state_id']);
            
            //get the name of the member corporate country
            $corporate_country = $this->getTheNameOfTheMemberCorporateCountry($member['corporate_country_id']);
            
            //get the open order initiated by this member
            $order_id = $this->getTheOpenOrderInitiatedByMember($member['id']);
            
           
            
             header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                                //"selected" => $selected,
                                "member" => $member,
                                "membershiptype"=>$membershiptype_name,
                                "date_of_renewal"=>$renewal_date,
                                "member_city"=>$member_city,
                                "member_state"=>$member_state,
                                "member_country"=>$member_country,
                                "member_delivery_city"=>$delivery_city,
                                "member_delivery_state"=>$delivery_state,
                                "member_delivery_country"=>$delivery_country,
                                "member_corporate_city"=>$corporate_city,
                                "member_corporate_state"=>$corporate_state,
                                "member_corporate_country"=>$corporate_country,
                                "membership_status"=>$membership_status,
                                "membership_type_id"=>$membershiptype_id,
                                "decision"=>$model->isThisMemberWithActiveSubscription($member['id']),
                                "order"=>$order_id,
                               
                                
                               
                             ));  
            
        }
        
        
        /**
         * just testing stuff
         */
        public function actionjustTestingThisOut(){
            
            $model = new MembershipSubscription; 
            $member_id = Yii::app()->user->id;
            $date = $model->getTheSubscriptionEndDateForExtensions("05/05/2018",$number_of_months=2);
              header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                           "date" => $date,
                       )); 
            
        }
        
        
        /**
         * This is the function that gets a members active subscription id
         */
        public function getMemberActiveSubscriptionId($member_id){
            $model = new MembershipSubscription;
            return $model->getMemberActiveSubscriptionId($member_id);
        }
        
        /**
         * This is the function that retrieves the name of a membership type
         
         */
        public function getTheNameOfThisMembershipType($membershiptype_id){
            $model = new Membershiptype;
            return $model->getTheNameOfThisMembershipType($membershiptype_id);
        }
        
        /**
         * This is the function that retrieves the membership type of a member
         */
        public function getThisMemberMembershipType($member_id){
            
            $model = new MembershipSubscription;
            
            return $model->getThisMemberMembershipType($member_id);
        }
        
        /**
         * This is the function that gets the membership type id of this member
         */
        public function getThisMemberMembershipTypeId($member_id){
            
            $model = new MembershipSubscription;
            
            return $model->getThisMemberMembershipTypeId($member_id);
            
        }
        
        
        /**
         * This is the function that gets the name of this city
         */
        public function getTheNameofTheMemberCity($city_id){
            $model= new City;
            
            return $model->getThisCityName($city_id);
        }
        
        
        /**
         * This is the function that gets the name of a state
         */
        public function getTheNameOfTheMemberState($state_id){
    
             $model = new State;
             
             return $model->getThisStateName($state_id);
    
        }
        
        /**
         * This is the function that gets the name of a country
         */
        public function getTheNameOfTheMemberCountry($country_id){
            $model = new Country;
            
            return $model->getThisCountryName($country_id);
            
        }
        
        
        /**
         * This is the function that gets the name of the permanent delivery city
         */
        public function getTheNameOfThePermamentDeliveryCity($delivery_city_id){
            
            $model= new City;
            
            return $model->getThisCityName($delivery_city_id);
        }
        
        
        /**
         * This is the function that gets the name of the permanent delivery state
         */
        public function getTheOfTheNameOfThePermanentDeliveryState($delivery_state_id){
            
             $model = new State;
             
             return $model->getThisStateName($delivery_state_id);
            
        }
        
        
        /**
         * This is the function that gets the name of the permanent delivery country
         */
        public function getTheNameOfThePermanentDeliveryCountry($delivery_country_id){
            
             $model = new Country;
            
            return $model->getThisCountryName($delivery_country_id);
        }
        
        /**
         * This is the function that gets the name of the city where the organization is located
         */
        public function getTheNameOfTheMemberCorporateCity($corporate_city_id){
            
            $model= new City;
            
            return $model->getThisCityName($corporate_city_id);
        }
        
        
        /**
         * This is the function that gets the name of the state the organization is located
         */
        public function getTheNameOfTheMemberCorporateState($corporate_state_id){
            
            $model = new State;
             
            return $model->getThisStateName($corporate_state_id);
        }
        
        /**
         * This is the function that gets the name of the country the organization is located
         */
        public function getTheNameOfTheMemberCorporateCountry($corporate_country_id){
           
             $model = new Country;
            
            return $model->getThisCountryName($corporate_country_id);
            
        }
        
        
        /**
         * This is the function that gets the membership status of this member
         */
        public function getTheMembershipStatusOfThisMember($subscription_id){
            $model = new MembershipSubscription;
            
            return $model->getTheMembershipStatusOfThisMember($subscription_id);
            
        }
        
        
        /**
         * This is the function that retrieves the renewal date of a members subscription  
         */
        public function getTheDateOfMembershipRenewalOfThisMember($member_id){
            $model = new MembershipSubscription;
            
            return $model->getTheDateOfMembershipRenewalOfThisMember($member_id);
            
            
        }
        
        
        /**
         * This is the function that retrieves the open order initiated by this member
         */
        public function getTheOpenOrderInitiatedByMember($member_id){
            $model = new Order;
            return $model->getTheOpenOrderInitiatedByMember($member_id);
            
        }
        
        
        /**
         * This is the functin that only updates a member account
         */
        public function actionupdateMemberAccountInformation(){
            
            $_id = $_POST['id'];
            $model=Members::model()->findByPk($_id);
            
            //obtain the current password
                $criteria3 = new CDbCriteria();
                $criteria3->select = 'id, password';
                $criteria3->condition='id=:id';
                $criteria3->params = array(':id'=>$_id);
                $current_password= Members::model()->find($criteria3);
                
                $model->current_pass = $current_password['password'];
            
                $model->usertype = $model->getThisMemberUsertype($_id);
                if(is_numeric($_POST['city'])){
                     $model->city_id = $_POST['city'];
                }else{
                     $model->city_id = $_POST['city_id'];
                }
                if(is_numeric($_POST['state'])){
                     $model->state_id = $_POST['state'];
                }else{
                     $model->state_id = $_POST['state_id'];
                }
                if(is_numeric($_POST['country'])){
                     $model->country_id = $_POST['country'];
                }else{
                     $model->country_id = $_POST['country_id'];
                }
                if(isset($_POST['gender'])){
                    $model->gender = strtolower($_POST['gender']);
                }
                 if(isset($_POST['religion'])){
                    $model->religion = strtolower($_POST['religion']);
                }
                 if(isset($_POST['maritalstatus'])){
                    $model->maritalstatus = strtolower($_POST['maritalstatus']);
                }
                if(isset($_POST['address1'])){
                    $model->address1 = $_POST['address1']; 
                }
                if(isset($_POST['address2'])){
                    $model->address2 = $_POST['address2']; 
                }
                if(isset($_POST['delivery_address1'])){
                    $model->delivery_address1 = $_POST['delivery_address1']; 
                }
                 if(isset($_POST['delivery_address2'])){
                    $model->delivery_address2 = $_POST['delivery_address2']; 
                }  
                 if(is_numeric($_POST['delivery_city'])){
                     $model->delivery_city_id = $_POST['delivery_city'];
                }else{
                     $model->delivery_city_id = $_POST['delivery_city_id'];
                }
                if(is_numeric($_POST['delivery_state'])){
                     $model->delivery_state_id = $_POST['delivery_state'];
                }else{
                     $model->delivery_state_id = $_POST['delivery_state_id'];
                }
                if(is_numeric($_POST['delivery_country'])){
                     $model->delivery_country_id = $_POST['delivery_country'];
                }else{
                     $model->delivery_country_id = $_POST['delivery_country_id'];
                }
                 if(isset($_POST['name_of_organization'])){
                    $model->name_of_organization = $_POST['name_of_organization']; 
                } 
                 if(isset($_POST['unique_registration_number'])){
                    $model->unique_registration_number = $_POST['unique_registration_number']; 
                }
                if(isset($_POST['business_category'])){
                    $model->business_category = $_POST['business_category']; 
                }
                if(isset($_POST['corporate_address1'])){
                    $model->corporate_address1 = $_POST['corporate_address1']; 
                }
                 if(isset($_POST['corporate_address2'])){
                    $model->corporate_address2 = $_POST['corporate_address2']; 
                }
                 if(is_numeric($_POST['corporate_city'])){
                     $model->corporate_city_id = $_POST['corporate_city'];
                }else{
                     $model->corporate_city_id = $_POST['corporate_city_id'];
                }
                if(is_numeric($_POST['corporate_state'])){
                     $model->corporate_state_id = $_POST['corporate_state'];
                }else{
                     $model->corporate_state_id = $_POST['corporate_state_id'];
                }
                if(is_numeric($_POST['corporate_country'])){
                     $model->corporate_country_id = $_POST['corporate_country'];
                }else{
                     $model->corporate_country_id = $_POST['corporate_country_id'];
                }
                 if(isset($_POST['landline'])){
                    $model->landline = $_POST['landline']; 
                }
                 if(isset($_POST['mobile_line'])){
                    $model->mobile_line = $_POST['mobile_line']; 
                }
                $model->role = strtolower($_POST['role']);
                $model->status = strtolower($_POST['status']);
                $model->category = strtolower($_POST['category']);
                $model->update_time = new CDbExpression('NOW()');
                $model->update_user_id = Yii::app()->user->id;
                
                $model->password = '';
                $model->password_repeat = '';
                             
                $icon_error_counter = 0;     
                  $name = $model->firstname . ' ' . $model->middlename . ' ' . strtoupper($model->lastname);
                 if($_FILES['picture']['name'] != ""){
                    if($this->isIconTypeAndSizeLegal()){
                       
                       $icon_filename = $_FILES['picture']['name'];
                      $icon_size = $_FILES['picture']['size'];
                        
                    }else{
                       
                        $icon_error_counter = $icon_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    //$model->icon = $this->retrieveThePreviousIconName($_id);
                    $icon_filename = $this->retrieveThePreviousIconName($_id);
                    $icon_size = $this->retrieveThePrreviousIconSize($_id);
             
                }//end of the if icon is empty statement
                if($icon_error_counter ==0){
                   if($model->validate()){
                           $model->picture = $this->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                           $model->picture_size = $icon_size;
                           
                if($model->save()) {
                        
                            // $result['success'] = 'true';
                                $msg = 'Update of this member information was successful';
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "msg" => $msg)
                                    );
                              
                              
                                    
                     }else {
                         //$result['success'] = 'false';
                         $msg = 'User creation was not successful';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                     }
                        
                   }else{
                                
                                //delete all the moved files in the directory when validation error is encountered
                            $msg = "Validation Error: '$name'  was not updated successful";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                            );
                          }
                        }elseif($icon_error_counter > 0){
                        //get the platform assigned icon width and height
                            $platform_width = $this->getThePlatformSetIconWidth();
                            $platform_height = $this->getThePlatformSeticonHeight();
                            $icon_types = $this->retrieveAllTheIconMimeTypes();
                            $icon_types = json_encode($icon_types);
                            $msg = "Please check your picture file type or size as picture must be of width '$platform_width'px and height '$platform_height'px. Picture is of types '$icon_types'";
                            header('Content-Type: application/json');
                                    echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                    "msg" => $msg)
                            );
                         
                        } 
                                
                     
                 
        }
        
        
        
        
        
        /**
         * This is the functin that only updates a member account
         */
        public function actionupdateNonMemberAccountInformation(){
            
            $_id = $_POST['id'];
            $model=Members::model()->findByPk($_id);
            
            //obtain the current password
                $criteria3 = new CDbCriteria();
                $criteria3->select = 'id, password';
                $criteria3->condition='id=:id';
                $criteria3->params = array(':id'=>$_id);
                $current_password= Members::model()->find($criteria3);
                
                $model->current_pass = $current_password['password'];
                $model->usertype =$model->usertype = $model->getThisMemberUsertype($_id);
                if(is_numeric($_POST['city'])){
                     $model->city_id = $_POST['city'];
                }else{
                     $model->city_id = $_POST['city_id'];
                }
                if(is_numeric($_POST['state'])){
                     $model->state_id = $_POST['state'];
                }else{
                     $model->state_id = $_POST['state_id'];
                }
                if(is_numeric($_POST['country'])){
                     $model->country_id = $_POST['country'];
                }else{
                     $model->country_id = $_POST['country_id'];
                }
                if(isset($_POST['gender'])){
                    $model->gender = strtolower($_POST['gender']);
                }
                 if(isset($_POST['religion'])){
                    $model->religion = strtolower($_POST['religion']);
                }
                 if(isset($_POST['maritalstatus'])){
                    $model->maritalstatus = strtolower($_POST['maritalstatus']);
                }
                if(isset($_POST['address1'])){
                    $model->address1 = $_POST['address1']; 
                }
                if(isset($_POST['address2'])){
                    $model->address2 = $_POST['address2']; 
                }
                if(isset($_POST['delivery_address1'])){
                    $model->delivery_address1 = $_POST['delivery_address1']; 
                }
                 if(isset($_POST['delivery_address2'])){
                    $model->delivery_address2 = $_POST['delivery_address2']; 
                }  
                 if(is_numeric($_POST['delivery_city'])){
                     $model->delivery_city_id = $_POST['delivery_city'];
                }else{
                     $model->delivery_city_id = $_POST['delivery_city_id'];
                }
                if(is_numeric($_POST['delivery_state'])){
                     $model->delivery_state_id = $_POST['delivery_state'];
                }else{
                     $model->delivery_state_id = $_POST['delivery_state_id'];
                }
                if(is_numeric($_POST['delivery_country'])){
                     $model->delivery_country_id = $_POST['delivery_country'];
                }else{
                     $model->delivery_country_id = $_POST['delivery_country_id'];
                }
                 if(isset($_POST['name_of_organization'])){
                    $model->name_of_organization = $_POST['name_of_organization']; 
                } 
                 if(isset($_POST['unique_registration_number'])){
                    $model->unique_registration_number = $_POST['unique_registration_number']; 
                }
                if(isset($_POST['business_category'])){
                    $model->business_category = $_POST['business_category']; 
                }
                if(isset($_POST['corporate_address1'])){
                    $model->corporate_address1 = $_POST['corporate_address1']; 
                }
                 if(isset($_POST['corporate_address2'])){
                    $model->corporate_address2 = $_POST['corporate_address2']; 
                }
                 if(is_numeric($_POST['corporate_city'])){
                     $model->corporate_city_id = $_POST['corporate_city'];
                }else{
                     $model->corporate_city_id = $_POST['corporate_city_id'];
                }
                if(is_numeric($_POST['corporate_state'])){
                     $model->corporate_state_id = $_POST['corporate_state'];
                }else{
                     $model->corporate_state_id = $_POST['corporate_state_id'];
                }
                if(is_numeric($_POST['corporate_country'])){
                     $model->corporate_country_id = $_POST['corporate_country'];
                }else{
                     $model->corporate_country_id = $_POST['corporate_country_id'];
                }
                 if(isset($_POST['landline'])){
                    $model->landline = $_POST['landline']; 
                }
                 if(isset($_POST['mobile_line'])){
                    $model->mobile_line = $_POST['mobile_line']; 
                }
                $model->role = strtolower($_POST['role']);
                $model->status = strtolower($_POST['status']);
                $model->category = strtolower($_POST['category']);
                $model->update_time = new CDbExpression('NOW()');
                $model->update_user_id = Yii::app()->user->id;
                
                $model->password = '';
                $model->password_repeat = '';
                             
                $icon_error_counter = 0;     
                  $name = $model->firstname . ' ' . $model->middlename . ' ' . strtoupper($model->lastname);
                 if($_FILES['picture']['name'] != ""){
                    if($this->isIconTypeAndSizeLegal()){
                       
                       $icon_filename = $_FILES['picture']['name'];
                      $icon_size = $_FILES['picture']['size'];
                        
                    }else{
                       
                        $icon_error_counter = $icon_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    //$model->icon = $this->retrieveThePreviousIconName($_id);
                    $icon_filename = $this->retrieveThePreviousIconName($_id);
                    $icon_size = $this->retrieveThePrreviousIconSize($_id);
             
                }//end of the if icon is empty statement
                if($icon_error_counter ==0){
                   if($model->validate()){
                           $model->picture = $this->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                           $model->picture_size = $icon_size;
                           
                if($model->save()) {
                        
                            // $result['success'] = 'true';
                                $msg = 'User Creation was successful';
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "msg" => $msg)
                                    );
                              
                              
                                    
                     }else {
                         //$result['success'] = 'false';
                         $msg = 'User creation was not successful';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                     }
                        
                   }else{
                                
                                //delete all the moved files in the directory when validation error is encountered
                            $msg = "Validation Error: '$name'  was not updated successful";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                            );
                          }
                        }elseif($icon_error_counter > 0){
                        //get the platform assigned icon width and height
                            $platform_width = $this->getThePlatformSetIconWidth();
                            $platform_height = $this->getThePlatformSeticonHeight();
                            $icon_types = $this->retrieveAllTheIconMimeTypes();
                            $icon_types = json_encode($icon_types);
                            $msg = "Please check your picture file type or size as picture must be of width '$platform_width'px and height '$platform_height'px. Picture is of types '$icon_types'";
                            header('Content-Type: application/json');
                                    echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                    "msg" => $msg)
                            );
                         
                        } 
                                
                     
                 
        }
        
        
        
        
            /**
         * This is the function that updates a member's account and renews memberships
         */
        public function actionupdateAndRenewMemberhipInformation(){
            
            $_id = $_POST['id'];
            $model=Members::model()->findByPk($_id);
            
            if($_POST['subscription_type'] == strtolower('monthly')){
                    $number_of_months = $_POST['number_of_months'];
                }else{
                    $number_of_months = $_POST['number_of_years'] * 12;
                }
            
             //obtain the current password
                $criteria3 = new CDbCriteria();
                $criteria3->select = 'id, password';
                $criteria3->condition='id=:id';
                $criteria3->params = array(':id'=>$_id);
                $current_password= Members::model()->find($criteria3);
                
                $model->current_pass = $current_password['password'];
            
                $model->usertype = strtolower($_POST['usertype']);
                if(is_numeric($_POST['city'])){
                     $model->city_id = $_POST['city'];
                }else{
                     $model->city_id = $_POST['city_id'];
                }
                if(is_numeric($_POST['state'])){
                     $model->state_id = $_POST['state'];
                }else{
                     $model->state_id = $_POST['state_id'];
                }
                if(is_numeric($_POST['country'])){
                     $model->country_id = $_POST['country'];
                }else{
                     $model->country_id = $_POST['country_id'];
                }
                if(isset($_POST['gender'])){
                    $model->gender = strtolower($_POST['gender']);
                }
                 if(isset($_POST['religion'])){
                    $model->religion = strtolower($_POST['religion']);
                }
                 if(isset($_POST['maritalstatus'])){
                    $model->maritalstatus = strtolower($_POST['maritalstatus']);
                }
                if(isset($_POST['address1'])){
                    $model->address1 = $_POST['address1']; 
                }
                if(isset($_POST['address2'])){
                    $model->address2 = $_POST['address2']; 
                }
                if(isset($_POST['delivery_address1'])){
                    $model->delivery_address1 = $_POST['delivery_address1']; 
                }
                 if(isset($_POST['delivery_address2'])){
                    $model->delivery_address2 = $_POST['delivery_address2']; 
                }  
                 if(is_numeric($_POST['delivery_city'])){
                     $model->delivery_city_id = $_POST['delivery_city'];
                }else{
                     $model->delivery_city_id = $_POST['delivery_city_id'];
                }
                if(is_numeric($_POST['delivery_state'])){
                     $model->delivery_state_id = $_POST['delivery_state'];
                }else{
                     $model->delivery_state_id = $_POST['delivery_state_id'];
                }
                if(is_numeric($_POST['delivery_country'])){
                     $model->delivery_country_id = $_POST['delivery_country'];
                }else{
                     $model->delivery_country_id = $_POST['delivery_country_id'];
                }
                 if(isset($_POST['name_of_organization'])){
                    $model->name_of_organization = $_POST['name_of_organization']; 
                } 
                 if(isset($_POST['unique_registration_number'])){
                    $model->unique_registration_number = $_POST['unique_registration_number']; 
                }
                if(isset($_POST['business_category'])){
                    $model->business_category = $_POST['business_category']; 
                }
                if(isset($_POST['corporate_address1'])){
                    $model->corporate_address1 = $_POST['corporate_address1']; 
                }
                 if(isset($_POST['corporate_address2'])){
                    $model->corporate_address2 = $_POST['corporate_address2']; 
                }
                 if(is_numeric($_POST['corporate_city'])){
                     $model->corporate_city_id = $_POST['corporate_city'];
                }else{
                     $model->corporate_city_id = $_POST['corporate_city_id'];
                }
                if(is_numeric($_POST['corporate_state'])){
                     $model->corporate_state_id = $_POST['corporate_state'];
                }else{
                     $model->corporate_state_id = $_POST['corporate_state_id'];
                }
                if(is_numeric($_POST['corporate_country'])){
                     $model->corporate_country_id = $_POST['corporate_country'];
                }else{
                     $model->corporate_country_id = $_POST['corporate_country_id'];
                }
                 if(isset($_POST['landline'])){
                    $model->landline = $_POST['landline']; 
                }
                 if(isset($_POST['mobile_line'])){
                    $model->mobile_line = $_POST['mobile_line']; 
                }
                $model->role = strtolower($_POST['role']);
                $model->status = strtolower($_POST['status']);
                $model->category = strtolower($_POST['category']);
                $model->update_time = new CDbExpression('NOW()');
                $model->update_user_id = Yii::app()->user->id;
                
               $model->password = '';
               $model->password_repeat = '';
                                
                $icon_error_counter = 0;     
                  $name = $model->firstname . ' ' . $model->middlename . ' ' . strtoupper($model->lastname);
                 if($_FILES['picture']['name'] != ""){
                    if($this->isIconTypeAndSizeLegal()){
                       
                       $icon_filename = $_FILES['picture']['name'];
                      $icon_size = $_FILES['picture']['size'];
                        
                    }else{
                       
                        $icon_error_counter = $icon_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    //$model->icon = $this->retrieveThePreviousIconName($_id);
                    $icon_filename = $this->retrieveThePreviousIconName($_id);
                    $icon_size = $this->retrieveThePrreviousIconSize($_id);
             
                }//end of the if icon is empty statement
                if($icon_error_counter ==0){
                   if($model->validate()){
                           $model->picture = $this->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                           $model->picture_size = $icon_size;
                 
                   //get this member prevailing membership type id
                        $prevailing_membershiptype_id = $this->getThisMemberPrevailingMembershipTypeId($_id);
                        //get the prevailing subscription id
                        $subscription_id = $this->getThePrevailingSubscriptionIdOfThisMember($_id);
                        
                     if($prevailing_membershiptype_id == $_POST['new_membership_type']){
                         if($model->save()) {
                        
                          if($this->isThisRenewalOfMembershipTypeSuccessful($model->id,$prevailing_membershiptype_id,$subscription_id,$number_of_months,$_POST['gross'],$_POST['discount'],$_POST['net'],$_POST['is_term_acceptable'])){
                              //get the invoice number of this payment    
                              $invoice_number = $this->getTheInvoiceNumberOfThisPayment($model->id,$prevailing_membershiptype_id);
                              
                             // $result['success'] = 'true';
                               // $msg = "Using your '$model->membership_number' membership number and '$invoice_number' invoice number, effect the online payment on the redirected payment platform";
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "invoice" => $invoice_number,
                                    "amount"=>$_POST['net'])
                                    );
                              
                              
                                 } else {
                                     $msg = 'Renewal of membership subscription  was not successful but update information was successful';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                                     
                                     
                                 } 
                                  
                                          
                     }else {
                         //$result['success'] = 'false';
                         $msg = 'Both the renewal of membership subscription and the update of membership information were unsuccessful';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                     }
                         
                     }else{
                          $msg = 'You can only renew the prevailing membership type or in the alternative change your subscription type. Please contact customer care for further assistance';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                     }                 
                      
                      
               
                   }else{
                                
                                //delete all the moved files in the directory when validation error is encountered
                            $msg = "Validation Error: Update or renewal of '$name' membership subscription was not  successful";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                            );
                          }
                        }elseif($icon_error_counter > 0){
                        //get the platform assigned icon width and height
                            $platform_width = $this->getThePlatformSetIconWidth();
                            $platform_height = $this->getThePlatformSeticonHeight();
                            $icon_types = $this->retrieveAllTheIconMimeTypes();
                            $icon_types = json_encode($icon_types);
                            $msg = "Please check your picture file type or size as picture must be of width '$platform_width'px and height '$platform_height'px. Picture is of types '$icon_types'";
                            header('Content-Type: application/json');
                                    echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                    "msg" => $msg)
                            );
                         
                        } 
        
        }
        
        
        
        
          /**
         * This is the function that updates a member's account and extends memberships subscription
         */
        public function actionupdateAndExtendMemberhipInformation(){
            
            $_id = $_POST['id'];
            $model=Members::model()->findByPk($_id);
            
             if($_POST['subscription_type'] == strtolower('monthly')){
                    $number_of_months = $_POST['number_of_months'];
                }else{
                    $number_of_months = $_POST['number_of_years'] * 12;
                }
            
             //obtain the current password
                $criteria3 = new CDbCriteria();
                $criteria3->select = 'id, password';
                $criteria3->condition='id=:id';
                $criteria3->params = array(':id'=>$_id);
                $current_password= Members::model()->find($criteria3);
                
                $model->current_pass = $current_password['password'];
            
                $model->usertype = strtolower($_POST['usertype']);
                if(is_numeric($_POST['city'])){
                     $model->city_id = $_POST['city'];
                }else{
                     $model->city_id = $_POST['city_id'];
                }
                if(is_numeric($_POST['state'])){
                     $model->state_id = $_POST['state'];
                }else{
                     $model->state_id = $_POST['state_id'];
                }
                if(is_numeric($_POST['country'])){
                     $model->country_id = $_POST['country'];
                }else{
                     $model->country_id = $_POST['country_id'];
                }
                if(isset($_POST['gender'])){
                    $model->gender = strtolower($_POST['gender']);
                }
                 if(isset($_POST['religion'])){
                    $model->religion = strtolower($_POST['religion']);
                }
                 if(isset($_POST['maritalstatus'])){
                    $model->maritalstatus = strtolower($_POST['maritalstatus']);
                }
                if(isset($_POST['address1'])){
                    $model->address1 = $_POST['address1']; 
                }
                if(isset($_POST['address2'])){
                    $model->address2 = $_POST['address2']; 
                }
                if(isset($_POST['delivery_address1'])){
                    $model->delivery_address1 = $_POST['delivery_address1']; 
                }
                 if(isset($_POST['delivery_address2'])){
                    $model->delivery_address2 = $_POST['delivery_address2']; 
                }  
                 if(is_numeric($_POST['delivery_city'])){
                     $model->delivery_city_id = $_POST['delivery_city'];
                }else{
                     $model->delivery_city_id = $_POST['delivery_city_id'];
                }
                if(is_numeric($_POST['delivery_state'])){
                     $model->delivery_state_id = $_POST['delivery_state'];
                }else{
                     $model->delivery_state_id = $_POST['delivery_state_id'];
                }
                if(is_numeric($_POST['delivery_country'])){
                     $model->delivery_country_id = $_POST['delivery_country'];
                }else{
                     $model->delivery_country_id = $_POST['delivery_country_id'];
                }
                 if(isset($_POST['name_of_organization'])){
                    $model->name_of_organization = $_POST['name_of_organization']; 
                } 
                 if(isset($_POST['unique_registration_number'])){
                    $model->unique_registration_number = $_POST['unique_registration_number']; 
                }
                if(isset($_POST['business_category'])){
                    $model->business_category = $_POST['business_category']; 
                }
                if(isset($_POST['corporate_address1'])){
                    $model->corporate_address1 = $_POST['corporate_address1']; 
                }
                 if(isset($_POST['corporate_address2'])){
                    $model->corporate_address2 = $_POST['corporate_address2']; 
                }
                 if(is_numeric($_POST['corporate_city'])){
                     $model->corporate_city_id = $_POST['corporate_city'];
                }else{
                     $model->corporate_city_id = $_POST['corporate_city_id'];
                }
                if(is_numeric($_POST['corporate_state'])){
                     $model->corporate_state_id = $_POST['corporate_state'];
                }else{
                     $model->corporate_state_id = $_POST['corporate_state_id'];
                }
                if(is_numeric($_POST['corporate_country'])){
                     $model->corporate_country_id = $_POST['corporate_country'];
                }else{
                     $model->corporate_country_id = $_POST['corporate_country_id'];
                }
                 if(isset($_POST['landline'])){
                    $model->landline = $_POST['landline']; 
                }
                 if(isset($_POST['mobile_line'])){
                    $model->mobile_line = $_POST['mobile_line']; 
                }
                $model->role = strtolower($_POST['role']);
                $model->status = strtolower($_POST['status']);
                $model->category = strtolower($_POST['category']);
                $model->update_time = new CDbExpression('NOW()');
                $model->update_user_id = Yii::app()->user->id;
                
               $model->password = '';
               $model->password_repeat = '';
                                
                $icon_error_counter = 0;     
                  $name = $model->firstname . ' ' . $model->middlename . ' ' . strtoupper($model->lastname);
                 if($_FILES['picture']['name'] != ""){
                    if($this->isIconTypeAndSizeLegal()){
                       
                       $icon_filename = $_FILES['picture']['name'];
                      $icon_size = $_FILES['picture']['size'];
                        
                    }else{
                       
                        $icon_error_counter = $icon_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    //$model->icon = $this->retrieveThePreviousIconName($_id);
                    $icon_filename = $this->retrieveThePreviousIconName($_id);
                    $icon_size = $this->retrieveThePrreviousIconSize($_id);
             
                }//end of the if icon is empty statement
                if($icon_error_counter ==0){
                   if($model->validate()){
                           $model->picture = $this->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                           $model->picture_size = $icon_size;
                           
                           //get this member prevailing membership type id
                        $prevailing_membershiptype_id = $this->getThisMemberPrevailingMembershipTypeId($_id);
                        //get the prevailing subscription id
                        $subscription_id = $this->getThePrevailingSubscriptionIdOfThisMember($_id);
                        
                     if($prevailing_membershiptype_id == $_POST['new_membership_type']){
                          if($model->save()) {
                        
                          if($this->isThisExtensionOfMembershipSubscriptionSuccessful($model->id,$prevailing_membershiptype_id,$subscription_id,$number_of_months,$_POST['gross'],$_POST['discount'],$_POST['net'],$_POST['is_term_acceptable'])){
                              //get the invoice number of this payment    
                              $invoice_number = $this->getTheInvoiceNumberOfThisPayment($model->id,$prevailing_membershiptype_id);
                              
                             // $result['success'] = 'true';
                                //$msg = "Using your '$model->membership_number' membership number and '$invoice_number' invoice number, effect the online payment on the redirected payment platform";
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "invoice" => $invoice_number,
                                    "amount"=>$_POST['net'])
                                    );
                              
                              
                                 } else {
                                     $msg = 'extension of membership subscription  was not successful but update of information was successful';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                                     
                                     
                                 } 
                                  
                                          
                     }else {
                         //$result['success'] = 'false';
                         $msg = 'Both the extension of membership subscription and the update of membership information were unsuccessful';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                     }
                         
                     }else{
                         $msg = 'You can only extend the prevailing membership type or in the alternative change your subscription type. Please contact customer care for further assistance';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                     }    
                 
               
                        
                   }else{
                                
                                //delete all the moved files in the directory when validation error is encountered
                            $msg = "Validation Error: Update or renewal of '$name' membership subscription was not  successful";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                            );
                          }
                        }elseif($icon_error_counter > 0){
                        //get the platform assigned icon width and height
                            $platform_width = $this->getThePlatformSetIconWidth();
                            $platform_height = $this->getThePlatformSeticonHeight();
                            $icon_types = $this->retrieveAllTheIconMimeTypes();
                            $icon_types = json_encode($icon_types);
                            $msg = "Please check your picture file type or size as picture must be of width '$platform_width'px and height '$platform_height'px. Picture is of types '$icon_types'";
                            header('Content-Type: application/json');
                                    echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                    "msg" => $msg)
                            );
                         
                        } 
        
        }
        
        
        /**
         * This is the function that gets the prevailing subscription id of a member
         */
        public function getThePrevailingSubscriptionIdOfThisMember($member_id){
            $model = new MembershipSubscription;
            return $model->getThePrevailingSubscriptionIdOfThisMember($member_id);
        }
        
        /**
         * This is the function that gets the prevailing subscription type id
         */
        public function getThisMemberPrevailingMembershipTypeId($member_id){
            $model = new MembershipSubscription;
            return $model->getThisMemberPrevailingMembershipTypeId($member_id);
        }
        
        /**
         * This is the function that determinnes if renewal of membership types was successful
         */
        public function isThisExtensionOfMembershipSubscriptionSuccessful($member_id,$membership_type_id,$subscription_id,$number_of_months,$gross,$discount,$net,$is_term_acceptable){
            
            $model = new MembershipSubscription;
            
            return $model->isThisExtensionOfMembershipSubscriptionSuccessful($member_id,$membership_type_id,$subscription_id,$number_of_months,$gross,$discount,$net,$is_term_acceptable);
            
            
        }
        
        
        
        /**
         * This is the function that determinnes if renewal of membership types was successful
         */
        public function isThisRenewalOfMembershipTypeSuccessful($member_id,$membership_type_id,$subscription_id,$number_of_months,$gross,$discount,$net,$is_term_acceptable){
            
            $model = new MembershipSubscription;
            
            return $model->isThisRenewalOfMembershipTypeSuccessful($member_id,$membership_type_id,$subscription_id,$number_of_months,$gross,$discount,$net,$is_term_acceptable);
            
            
        }
        
        /**
         * This is the function that gets the invoice number of a payment
         */
        public function getTheInvoiceNumberOfThisPayment($member_id,$membership_type_id){
            $model = new SubscriptionPayment;
            
            return $model->getTheInvoiceNumberOfThisPayment($member_id,$membership_type_id);
            
        }
        
        
        
        /**
         *This is the function that retrieves member cart details
         */
        public function actionretrieveMemberCartDetails(){
            
            $model = new OrderHasProducts;;
            
             $user_id = Yii::app()->user->id;
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$user_id);
            $member= Members::model()->find($criteria);
            
            //get the membership type of this member
            $membershiptype = $this->getThisMemberMembershipType($member['id']);
            
             //get the membership type id of this member
            $membershiptype_id = $this->getThisMemberMembershipTypeId($member['id']);
            
            //get the date of membership renewal for this member
            $renewal_date = $this->getTheDateOfMembershipRenewalOfThisMember($member['id']);
            
             //get the membership status of this member
            $membership_status = $this->getTheMembershipStatusOfThisMember($member['id']);
            
            //get the name of the member city
            $member_city = $this->getTheNameofTheMemberCity($member['city_id']);
            
            //get the name of the member state
            $member_state = $this->getTheNameOfTheMemberState($member['state_id']);
            
            //get the name of the member country
            $member_country = $this->getTheNameOfTheMemberCountry($member['country_id']);
            
            //get the name of the member permanent delivery city
            $delivery_city = $this->getTheNameOfThePermamentDeliveryCity($member['delivery_city_id']);
            
            //get the name of the member permanent delivery state
            $delivery_state = $this->getTheOfTheNameOfThePermanentDeliveryState($member['delivery_state_id']);
            
            //get the name of the member permanent delivery country
            $delivery_country = $this->getTheNameOfThePermanentDeliveryCountry($member['delivery_country_id']);
            
            //get the name of the member corporate city
            $corporate_city = $this->getTheNameOfTheMemberCorporateCity($member['corporate_city_id']);
            
            //get the name of the member corporate state
            $corporate_state = $this->getTheNameOfTheMemberCorporateState($member['corporate_state_id']);
            
            //get the name of the member corporate country
            $corporate_country = $this->getTheNameOfTheMemberCorporateCountry($member['corporate_country_id']);
            
            //get the open order initiated by this member
            $order_id = $this->getTheOpenOrderInitiatedByMember($member['id']);
            
            //get the total gross amount of the products in the cart
            
            $cart_gross_amount = $this->getTheTotalGrossAmountOfProductsInTheCart($order_id);
            
            //get the total discount amount of products in the order
            $cart_discount_amount = $this->getTheTotalDiscountAmountOfProductsInTheCart($order_id);
            
            //get the total net amount of all products in the cart
            $cart_net_amount = $cart_gross_amount - $cart_discount_amount;
            
            
           //get the total amount of products that can be paid for on delivery
            $payable_ondelivery_product_cost = $model->getTheAmountOfPayableOnDeliveryProducts($order_id);
            
            //get the total cost of delivery of payable on delivery products
           //$payable_ondelivery_delivery_cost = $model->getTheDeliveryCostOfPayableOnDeliveryProducts($order_id);
           
           //get the total amounht of products that must be settled before delivery
           $payable_before_delivery_product_cost = $model->getTheTotalCostOfPayableBeforeDeliveryProducts($order_id);
           
           //get the escrow charges in an order
           
           $escrow_charges = $model->getTheEscrowChargesOfThisOrder($order_id);
            
             header('C$escrow_chargesontent-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                                //"selected" => $selected,
                                "member" => $member,
                                "membershiptype"=>$membershiptype,
                                "date_of_renewal"=>$renewal_date,
                                "member_city"=>$member['city_id'],
                                "member_state"=>$member['state_id'],
                                "member_country"=>$member['country_id'],
                                "member_delivery_city"=>$member['delivery_city_id'],
                                "member_delivery_state"=>$member['delivery_state_id'],
                                "member_delivery_country"=>$member['delivery_country_id'],
                                "member_corporate_city"=>$member['corporate_city_id'],
                                "member_corporate_state"=>$member['corporate_state_id'],
                                "member_corporate_country"=>$member['corporate_country_id'],
                                "membership_status"=>$membership_status,
                                "membership_type_id"=>$membershiptype_id,
                                "order"=>$order_id,
                                "cart_gross_amount"=>$cart_gross_amount,
                                "cart_discount_amount"=>$cart_discount_amount,
                                "cart_net_amount"=>$cart_net_amount,
                                "payable_ondelivery_product_cost"=>$payable_ondelivery_product_cost,
                                "payable_before_delivery_product_cost"=>$payable_before_delivery_product_cost,
                                "escrow_charges"=>$escrow_charges,
                              
                               
                             )); 
            
            
            
        }
        
        
       
        
        /**
         * This is the function that gets the total gross amount of products in the cart
         */
        public function getTheTotalGrossAmountOfProductsInTheCart($order_id){
            $model = new OrderHasProducts;
            return $model->getTheTotalGrossAmountOfProductsInTheCart($order_id);
        }
        
        
        /**
         * This is the function that gets the total discount amount of products in the cart
         */
        public function getTheTotalDiscountAmountOfProductsInTheCart($order_id){
            $model = new OrderHasProducts;
            return $model->getTheTotalDiscountAmountOfProductsInTheCart($order_id);
            
        }
        
        
        /**
         * This is the function that connects this member to another member
         */
        public function actionConnectMeToThisMember(){
            $model = new Members;
            
            $member_id = Yii::app()->user->id;
            
           
            $relationship = strtolower($_POST['relationship']);
            
            
            
            
            if($model->isMembershipNumberValid($_POST['membership_number'])){
                 //get the member id of the other member
            $other_member_id = $model->getTheIdOfThisMemberGivenTheMembershipNumber($_POST['membership_number']);
             //get the name of the other member
                $member_name = $model->getTheNameOfThisMember($other_member_id);
            if($this->notInMembersConnectionAlready($member_id,$other_member_id)){
                if($member_id != $other_member_id){
               
                //make the connection
            if($this->isConnectingMeToThisMemberASuccess($other_member_id,$member_id,$relationship)){
                $msg = "You have initiated a connection to '$member_name'. Please do await his/her acceptance";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg,
                                            "member_id"=>$member_id)
                                        );
            }else{
               if($this->areYouAlreadyConnectedToThisMember($member_id,$other_member_id)){
                    $msg = "You are already connected to '$member_name'";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
               }else{
                   if($model->doesThisMemberAcceptConnections($other_member_id)){
                        $msg = "Your connection request to '$member_name' was not successful.Please contact customer care for assistance";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                   }else{
                        $msg = "'$member_name' does not accept connections as at this time .You may contact the member directly for permission";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                   }
               }
            }
                
            }else{
                $msg = "Connection to self is not allowed. Please Try again with another's membership number";
                           header('Content-Type: application/json');
                           echo CJSON::encode(array(
                                 "success" => mysql_errno() != 0,
                                 "msg" => $msg)
                            );
            }
                
            }else{
                $msg = "You are already in '$member_name' connection.Very possible your request is still pending with him/her";
                           header('Content-Type: application/json');
                           echo CJSON::encode(array(
                                 "success" => mysql_errno() != 0,
                                 "msg" => $msg)
                            );
                
            }
                        
            
                
            }else{
                $msg = "The Membership Number you provided is not valid. Please correct that and try again";
                           header('Content-Type: application/json');
                           echo CJSON::encode(array(
                                 "success" => mysql_errno() != 0,
                                 "msg" => $msg)
                            );
            }
           
            
            
        }
        
        
        /**
         * This is the function that confirms if i am not in a members connection
         */
        public function notInMembersConnectionAlready($member_id,$other_member_id){
            $model = new MemberHasMembers;
            return $model->notInMembersConnectionAlready($member_id,$other_member_id);
        }
        
        
        /**
         * This is the function that connects this member to another member
         */
        public function isConnectingMeToThisMemberASuccess($member_id,$other_member_id,$relationship){
            
            $model = new MemberHasMembers;
            return $model->isConnectingMeToThisMemberASuccess($member_id,$other_member_id,$relationship);
            
        }
        
        
        /**
         * This is the function that confirms if you are already connected to a member
         */
        public function areYouAlreadyConnectedToThisMember($member_id,$other_member_id){
            
            $model = new MemberHasMembers;
            return $model->areYouAlreadyConnectedToThisMember($member_id,$other_member_id);
        }
        
        
        /**
         * This is the function that list all members connected to this member
         */
        public function actionlistMembersConnectedToThisMember(){
            
            $member_id = Yii::app()->user->id;
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='member_id=:id';
            $criteria->params = array(':id'=>$member_id);
            $relationship= MemberHasMembers::model()->findAll($criteria);
            
            if($relationship===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "member" => $relationship)
                       );
                       
                }
        }
        
       
        /**
         * This is the function that list member connections to other members
         */
        public function actionlistMemberConnectionToOtherMembers(){
            
            $member_id = Yii::app()->user->id;
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='other_member_id=:id';
            $criteria->params = array(':id'=>$member_id);
            $relationship= MemberHasMembers::model()->findAll($criteria);
            
            if($relationship===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "member" => $relationship)
                       );
                       
                }
        }
        
        
        /**
         * This is the function that list all tradable product for a member
         */
        public function actionlistTradableProductsForThisMember(){
            
            $member_id = Yii::app()->user->id;
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='vendor_id=:id and status=:status';
            $criteria->params = array(':id'=>$member_id,':status'=>'inactive');
            $products= ProductHasVendor::model()->findAll($criteria);
            
            if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "product" => $products)
                       );
                       
                }
        }
        
        
        
        /**
         * This is the function that list all products subscribed to by a member
         */
        public function actionlistAllProductsSubscribedToByThisMember(){
            
            $member_id = Yii::app()->user->id;
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='member_id=:id ';
            $criteria->params = array(':id'=>$member_id);
            $products= MemberSubscribedToProducts::model()->findAll($criteria);
            
            if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "product" => $products)
                       );
                       
                }
        }
        
        
        /**
         * This is the function that accepts a member to another members connection
         */
        public function actionacceptThisMemberInMyConnection(){
            
            $model = new MemberHasMembers;
            $member_id = Yii::app()->user->id;
            
            $other_member_id = $_REQUEST['other_member_id'];
            
            $member_name = $this->getTheNameOfThisMember($other_member_id);
            
            if($model->isThisMemberNotAlreadyAcceptedInMyConnection($member_id,$other_member_id)){
                 if($model->isAcceptanceOfThisMemberToConnectionSuccessful($member_id,$other_member_id)){
                 $msg = "'$member_name' is now connected to you";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg,
                                            "member_id"=>$member_id)
                                        );
            }else{
                $msg = "This request to accept '$member_name' to your connection was not successful. Please contact customer care for assistance";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
            }
                
            }else{
                $msg = "This request to accept '$member_name' to your connection was not successful. The member is already accepted in your connection";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
            }
           
            
            
            
        }
        
        
        /**
         * This is the function that rejects a member from a connection
         */
        public function actionrejectThisMemberInMyConnection(){
            
            $model = new MemberHasMembers;
            $member_id = Yii::app()->user->id;
            
            $other_member_id = $_REQUEST['other_member_id'];
            
            $member_name = $this->getTheNameOfThisMember($other_member_id);
            
           if($model->isRejectionOfThisMemberToConnectionSuccessful($member_id,$other_member_id)){
               if($model->isRemovalOfAMemberConnectionRequestSuccessful($member_id,$other_member_id)){
                    $msg = "'$member_name' connection request to you is successfully rejected";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg,
                                            "member_id"=>$member_id)
                             );
               }else{
                   $msg = "'$member_name' connection request to you could not be rejected. Its possible such connection request was never registered";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg,
                                           )
                             );
               }
                
            }else{
                $msg = "This request to reject '$member_name' connection request to you was not successful. Its possible such connection request was never registered";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
            }
                
            
            
        }
        
        
        
        /**
         * This is the function that disconnect a members connection
         */
        public function actiondisconnectThisMemberInMyConnection(){
            
            $model = new MemberHasMembers;
            $member_id = Yii::app()->user->id;
            
            $other_member_id = $_REQUEST['other_member_id'];
            
            $member_name = $this->getTheNameOfThisMember($other_member_id);
            
            if($model->isRemovalOfAMemberConnectionRequestSuccessful($member_id,$other_member_id)){
                    $msg = "'$member_name' connection request to you is successfully disconnected";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg,
                                            "member_id"=>$member_id)
                             );
               }else{
                   $msg = "'$member_name' connection request to you could not be disconnected. Its possible such connection request was never registered";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg,
                                           )
                             );
               }
            
        }
        
        
        
        /**
         * This is the function that disconnect a members connection
         */
        public function actiondisconnectingFromThisMember(){
            
            $model = new MemberHasMembers;
            $member_id = Yii::app()->user->id;
            
            $other_member_id = $_REQUEST['member_id'];
            
            $member_name = $this->getTheNameOfThisMember($other_member_id);
            
            if($model->isRemovalOfAMemberConnectionRequestSuccessful($other_member_id,$member_id)){
                    $msg = "Your connection request to '$member_name' is successfully disconnected";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg,
                                            "member_id"=>$member_id)
                             );
               }else{
                   $msg = "Your connection request to '$member_name' could not be disconnected. Its possible such connection request was never registered";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg,
                                           )
                             );
               }
            
        }
        
        
        
        /**
         * This is the function that suspends a member's connection
         */
        public function actionsuspendThisMemberInMyConnection(){
            
            $model = new MemberHasMembers;
            $member_id = Yii::app()->user->id;
            
            $other_member_id = $_REQUEST['other_member_id'];
            
            $member_name = $this->getTheNameOfThisMember($other_member_id);
            
            if($model->isThisMemberNotAlreadyAcceptedInMyConnection($member_id,$other_member_id) == false){
                
                 if($model->isSuspensionOfAMemberConnectionRequestSuccessful($member_id,$other_member_id)){
                    $msg = "'$member_name' connection request to you is successfully suspended";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg,
                                            "member_id"=>$member_id)
                             );
               }else{
                   $msg = "'$member_name' connection request to you could not be suspended. Its possible such connection request was never accepted in the first place";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg,
                                           )
                             );
               }
                
            }else{
                
                $msg = "'$member_name' connection request had not been accepted or it is already suspended. So it is not suspendable";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg,
                                           )
                             );
            }
            
           
            
        }
        
        
        /**
         * This is the function that unsuspends a member connection
         */
        public function actionunsuspendThisMemberInMyConnection(){
            
            $model = new MemberHasMembers;
            $member_id = Yii::app()->user->id;
            
            $other_member_id = $_REQUEST['other_member_id'];
            
            $member_name = $this->getTheNameOfThisMember($other_member_id);
            
            if($model->isThisMemberConnectionSuspended($member_id,$other_member_id)){
                
                 if($model->isUnSuspensionOfAMemberConnectionRequestSuccessful($member_id,$other_member_id)){
                    $msg = "The suspension of '$member_name' connection request to you is successfully lifted";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg,
                                            "member_id"=>$member_id)
                             );
               }else{
                   $msg = "The suspension of '$member_name' connection request to you could not be lifted. Its possible that the connection was never suspended";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg,
                                           )
                             );
               }
                
            }else{
                
                $msg = "'$member_name' connection request was never suspended.";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg,
                                           )
                             );
            }
            
        }
        
        /**
         * This is the function that gets a member name
         */
        public function getTheNameOfThisMember($other_member_id){
            $model = new Members;
            return $model->getTheNameOfThisMember($other_member_id);
        }
        
        
        /**
         * This is the function that retrieves all members that connected to a member
         */
        public function actionlistallmembersconnectedtoamember(){
            $model = new MemberHasMembers;
            $member_id = Yii::app()->user->id;
            
            $connected_members = $model->getAllMembersConnectedToMember($member_id);
            
            $all_members = [];
            foreach($connected_members as $connected){
                 $criteria = new CDbCriteria();
                 $criteria->select = '*';
                 $criteria->condition='id=:id ';
                 $criteria->params = array(':id'=>$connected);
                 $member= Members::model()->find($criteria);
                 
                 $all_members[] = $member;
            }
            
            if($all_members===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "member" => $all_members)
                       );
                       
                }
            
        }
        
        
        /**
         * This is the function that verifys a membership number
         */
        public function actionverifyThisMembershipNumber(){
            $model = new Members;
            $membership_number = $_POST['membership_number'];
            
                       
            if($model->isMembershipNumberValid($membership_number)){
                //get the member name 
                $member_name = $model->getTheNameOfThisMemberGivenTheMembershipNumber($membership_number);
                 $msg = "'$member_name' is a member of this platform with membership number '$membership_number'.";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg,
                                           )
                             ); 
                
            }else{
               $msg = "'$membership_number' membership number does not exist. Please try again";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg,
                                           )
                             ); 
            }
            
            
        }
        
        
        /**
         * This is the function that registers a new basic member
         */
        public function actionRegisterThisNewBasicMember(){
            
            $model = new Members;
            
             
                $number_of_months = 144;
                $model->usertype = strtolower('others');
                $model->email = $_POST['email'];
                $model->username = $_POST['email'];
                $model->name = $_POST['name'];
            /**    if(is_numeric($_POST['city'])){
                     $model->city_id = $_POST['city'];
                }else{
                     $model->city_id = $_POST['city_id'];
                }
                if(is_numeric($_POST['state'])){
                     $model->state_id = $_POST['state'];
                }else{
                     $model->state_id = $_POST['state_id'];
                }
                if(is_numeric($_POST['country'])){
                     $model->country_id = $_POST['country'];
                }else{
                     $model->country_id = $_POST['country_id'];
                }
             * 
             */
               
            /**    $model->lastname = $_POST['lastname'];
                if(isset($_POST['middlename'])){
                    $model->middlename = $_POST['middlename'];
                }
                $model->firstname = $_POST['firstname'];
                if(isset($_POST['gender'])){
                    $model->gender = strtolower($_POST['gender']);
                }
                 if(isset($_POST['religion'])){
                    $model->religion = strtolower($_POST['religion']);
                }
                 if(isset($_POST['maritalstatus'])){
                    $model->maritalstatus = strtolower($_POST['maritalstatus']);
                }
                if(isset($_POST['dateofbirth'])){
                    $model->dateofbirth = date("Y-m-d H:i:s", strtotime($_POST['dateofbirth'])); 
                }
                if(isset($_POST['address1'])){
                    $model->address1 = $_POST['address1']; 
                }
                if(isset($_POST['address2'])){
                    $model->address2 = $_POST['address2']; 
                }
                if(isset($_POST['delivery_address1'])){
                    $model->delivery_address1 = $_POST['delivery_address1']; 
                }
                 if(isset($_POST['delivery_address2'])){
                    $model->delivery_address2 = $_POST['delivery_address2']; 
                }  
                 if(is_numeric($_POST['delivery_city'])){
                     $model->delivery_city_id = $_POST['delivery_city'];
                }else{
                     $model->delivery_city_id = $_POST['delivery_city_id'];
                }
                if(is_numeric($_POST['delivery_state'])){
                     $model->delivery_state_id = $_POST['delivery_state'];
                }else{
                     $model->delivery_state_id = $_POST['delivery_state_id'];
                }
                if(is_numeric($_POST['delivery_country'])){
                     $model->delivery_country_id = $_POST['delivery_country'];
                }else{
                     $model->delivery_country_id = $_POST['delivery_country_id'];
                }
                 if(isset($_POST['name_of_organization'])){
                    $model->name_of_organization = $_POST['name_of_organization']; 
                } 
                 if(isset($_POST['unique_registration_number'])){
                    $model->unique_registration_number = $_POST['unique_registration_number']; 
                }
                if(isset($_POST['business_category'])){
                    $model->business_category = $_POST['business_category']; 
                }
                if(isset($_POST['corporate_address1'])){
                    $model->corporate_address1 = $_POST['corporate_address1']; 
                }
                 if(isset($_POST['corporate_address2'])){
                    $model->corporate_address2 = $_POST['corporate_address2']; 
                }
                 if(is_numeric($_POST['corporate_city'])){
                     $model->corporate_city_id = $_POST['corporate_city'];
                }else{
                     $model->corporate_city_id = $_POST['corporate_city_id'];
                }
                if(is_numeric($_POST['corporate_state'])){
                     $model->corporate_state_id = $_POST['corporate_state'];
                }else{
                     $model->corporate_state_id = $_POST['corporate_state_id'];
                }
                if(is_numeric($_POST['corporate_country'])){
                     $model->corporate_country_id = $_POST['corporate_country'];
                }else{
                     $model->corporate_country_id = $_POST['corporate_country_id'];
                }
                 if(isset($_POST['landline'])){
                    $model->landline = $_POST['landline']; 
                }
                 if(isset($_POST['mobile_line'])){
                    $model->mobile_line = $_POST['mobile_line']; 
                }
             * 
             */
                               
                $model->role =strtolower($_POST['role']);
                $model->category = $_POST['category'];
                $model->accounttype = $_POST['accounttype'];
                $model->status = strtolower("active");
                $model->create_time = new CDbExpression('NOW()');
                $model->create_user_id = Yii::app()->user->id;
                $password = $_POST['password'];
                $password_repeat = $_POST['passwordCompare'];
                
               // $name = $model->firstname . ' ' . $model->middlename . ' ' . strtoupper($model->lastname);
                if($this->isUsernameAndEmailUnique($model->username,$model->email)){
                    if($password === $password_repeat){
                    
                    $model->password = $password;
                    $model->password_repeat = $password_repeat;
                    
                    if($model->getPasswordMinLengthRule($password)){
                        
                        if($model->getPasswordMaxLengthRule($password )){
                            
                            if($model->getPasswordCharacterPatternRule($password)){
                                
                        
                  $icon_error_counter = 0;     
                 // $name = $model->firstname . ' ' . $model->middlename . ' ' . strtoupper($model->lastname);
                 if($_FILES['picture']['name'] != ""){
                    if($this->isIconTypeAndSizeLegal()){
                       
                       $icon_filename = $_FILES['picture']['name'];
                      $icon_size = $_FILES['picture']['size'];
                        
                    }else{
                       
                        $icon_error_counter = $icon_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    $icon_filename = $this->provideUserIconWhenUnavailable($model);
                   $icon_size = 0;
             
                }//end of the if icon is empty statement
                if($icon_error_counter ==0){
                   if($model->validate()){
                           $model->picture = $this->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                           $model->picture_size = $icon_size;
                           
                if($model->save()) {
                        
                    //$userid = Yii::app()->user->id;
                    if(isset($model->role)){
                            
                         if($this->assignRoleToAUser($model->role, $model->id)) {
                              if($this->isThisAssignmentOfMembershipTypeSuccessful($model->id,$_POST['membership_type'],$number_of_months,0,0,0,$_POST['is_term_acceptable'],$_POST['status'])){
                                //get the invoice number for this subscription
                              $invoice_number = $this->getTheInvoiceNumberOfThisPayment($model->id,$_POST['membership_type']); 
                                  
                                // $result['success'] = 'true';
                                $msg = "You are welcome to Leadsdome, the online Leads marketplace. However, if you hope to become a merchant on Leads, it will be advisable to upgrade your membership type";
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    //"invoice" => $invoice_number,
                                    "msg"=>$msg)
                                    );
                              
                              
                              } else {
                                     $msg = 'You had successfully being registered to start using the Oneroof online store. However, if you need  to do much more than just buying, please contact customer care to process your membership type ';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                                     
                                     
                                 } 
                              
                              }else{
                                  $msg = 'User creation was successful but assignment of role to this user was not successful. Please contact customer care for assistance';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                                  
                              }
                              
                        }else{
                            $msg = "Role is not assigned to this User. Please assign the role and continue with the registration";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                            
                            
                        }
                        
                   
                         
                     }else {
                         //$result['success'] = 'false';
                         $msg = 'Your registration was not sucessful. Please contact customer care for asistance';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                     }
                        
                   }else{
                                
                                //delete all the moved files in the directory when validation error is encountered
                            $msg = "Validation Error: There seem to be a field validation issue with your form. Try again or contact customer care for assistance ";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                            );
                          }
                        }elseif($icon_error_counter > 0){
                        //get the platform assigned icon width and height
                            $platform_width = $this->getThePlatformSetIconWidth();
                            $platform_height = $this->getThePlatformSeticonHeight();
                            $icon_types = $this->retrieveAllTheIconMimeTypes();
                            $icon_types = json_encode($icon_types);
                            $msg = "Please check your picture file type or size as picture must be of width '$platform_width'px and height '$platform_height'px. Picture is of types '$icon_types'";
                            header('Content-Type: application/json');
                                    echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                    "msg" => $msg)
                            );
                         
                        } 
                                
                                
                            }else{
                                
                                $msg = 'Password must contain at least one number(0-9), at least one lower case letter(a-z), at least one upper case letter(A-Z), and at least one special character(@,&,$ etc)';
                                 header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() != 0,
                                 "msg" => $msg,
                                )); 
                            }
                            
                        }else{
                                $msg = 'The maximum Password length allowed is sixty(60)';
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                     "success" => mysql_errno() != 0,
                                    "msg" => $msg,
                            )); 
                            
                        }
                        
                        
                    }else{
                        $msg = 'The minimum Password length allowed is eight(8)';
                             header('Content-Type: application/json');
                            echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg,
                       )); 
                        
                        
                    }
                
           
                
            }else{
               $msg = 'Repeat Password do not match the new password';
              header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                           "msg" => $msg,
                       )); 
                
                
            }
                    
                }else{
                     $msg = 'Your username and Email must be unique';
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                           "msg" => $msg,
                       )); 
                    
                }
            
            
        }
        
        
        /**
         * This is the function that changes a member's membership subscription
         */
        public function actionchangeThisMemberMembershipSubscription(){
            
        $model = new MembershipSubscription;
        
        if(isset($_POST['subscription_type'])){
            if($_POST['subscription_type'] == strtolower('monthly')){
                    $number_of_months = $_POST['number_of_months'];
                }else{
                    $number_of_months = $_POST['number_of_years'] * 12;
                }
                
           $member_id = $_POST['id'];
                     
           $new_member_type_id = $_POST['new_membership_type'];
           //retrieve the existing membership type for this member
           
           $existing_membership_type_id = $model->getThisMemberPrevailingMembershipTypeId($member_id);
           
          if($this->getThisMembershipTypeCode($new_member_type_id)  != strtolower('freebee')){
             if($new_member_type_id != $existing_membership_type_id){
                 if($model->isTheMembershipTypeChangeSuccessful($member_id,$_POST['new_membership_type'],$existing_membership_type_id,$number_of_months,$_POST['gross'],$_POST['discount'],$_POST['net'],$_POST['is_term_acceptable'])){
                  $invoice_number = $this->getTheInvoiceNumberOfThisPayment($member_id,$_POST['new_membership_type']);
                  $amount = $_POST['net'];
                  header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                           "invoice" => $invoice_number,
                           "amount"=>$amount
                       ));
               }else{
                   $msg = 'Could not change your membership. Please try again or contact customer care for assistance';
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                           "quest"=>$quest,
                           "msg" => $msg,
                       )); 
               }
               
               
            }else{
                $msg = 'You cannot change to the same  prevailing membership type. Select any other membership type and try again';
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                           "msg" => $msg,
                       )); 
            }
           }else{
              $msg = 'Changing to the Oneroof Basic membership type is not necessary as all other membership types automatically defaults to it whenever they are out of subscription.';
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                           "msg" => $msg,
                           
                       ));  
           }
            
        }else{
            $msg = 'Changing to the Oneroof Basic membership type is not necessary as all other membership types automatically defaults to it whenever they are out of subscription.';
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                           "msg" => $msg,
                           
                       ));  
        }
        
           
            
            
        }
        
        
        /**
         * This is the function that retrieves a membership type code
         */
        public function getThisMembershipTypeCode($new_member_type_id){
            $model = new Membershiptype;
            return $model->getThisMembershipTypeCode($new_member_type_id);
        }
       
        
        /**
         * This is the function that retrieves a single member information
         */
        public function actionretrieveMemberDetail(){
            $member_id = $_REQUEST['id'];
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id ';
            $criteria->params = array(':id'=>$member_id);
            $member= Members::model()->find($criteria);
            
             header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                           "member" => $member,
                           
                       ));  
        }
        
        
        
        /**
         * This is the function that registers a new basic member
         */
        public function actionRegisterThisNewBasicMemberQuickly(){
            
            $model = new Members;
            
             
                $number_of_months = 144;
                $model->usertype = strtolower('others');
                $model->email = $_POST['email'];
                $model->username = $_POST['email'];
                $model->name = $_POST['name'];
             /**   if(is_numeric($_POST['city'])){
                     $model->city_id = $_POST['city'];
                }else{
                     $model->city_id = $_POST['city_id'];
                }
                if(is_numeric($_POST['state'])){
                     $model->state_id = $_POST['state'];
                }else{
                     $model->state_id = $_POST['state_id'];
                }
                if(is_numeric($_POST['country'])){
                     $model->country_id = $_POST['country'];
                }else{
                     $model->country_id = $_POST['country_id'];
                }
              * 
              */
               
                
             /**   $model->lastname = $_POST['lastname'];
                if(isset($_POST['middlename'])){
                    $model->middlename = $_POST['middlename'];
                }
                $model->firstname = $_POST['firstname'];
                if(isset($_POST['gender'])){
                    $model->gender = strtolower($_POST['gender']);
                }
                 if(isset($_POST['religion'])){
                    $model->religion = strtolower($_POST['religion']);
                }
                 if(isset($_POST['maritalstatus'])){
                    $model->maritalstatus = strtolower($_POST['maritalstatus']);
                }
                if(isset($_POST['dateofbirth'])){
                    $model->dateofbirth = date("Y-m-d H:i:s", strtotime($_POST['dateofbirth'])); 
                }
                if(isset($_POST['address1'])){
                    $model->address1 = $_POST['address1']; 
                }
                if(isset($_POST['address2'])){
                    $model->address2 = $_POST['address2']; 
                }
                if(isset($_POST['delivery_address1'])){
                    $model->delivery_address1 = $_POST['delivery_address1']; 
                }
                 if(isset($_POST['delivery_address2'])){
                    $model->delivery_address2 = $_POST['delivery_address2']; 
                }  
                 if(is_numeric($_POST['delivery_city'])){
                     $model->delivery_city_id = $_POST['delivery_city'];
                }else{
                     $model->delivery_city_id = $_POST['delivery_city_id'];
                }
                if(is_numeric($_POST['delivery_state'])){
                     $model->delivery_state_id = $_POST['delivery_state'];
                }else{
                     $model->delivery_state_id = $_POST['delivery_state_id'];
                }
                if(is_numeric($_POST['delivery_country'])){
                     $model->delivery_country_id = $_POST['delivery_country'];
                }else{
                     $model->delivery_country_id = $_POST['delivery_country_id'];
                }
                 if(isset($_POST['name_of_organization'])){
                    $model->name_of_organization = $_POST['name_of_organization']; 
                } 
                 if(isset($_POST['unique_registration_number'])){
                    $model->unique_registration_number = $_POST['unique_registration_number']; 
                }
                if(isset($_POST['business_category'])){
                    $model->business_category = $_POST['business_category']; 
                }
                if(isset($_POST['corporate_address1'])){
                    $model->corporate_address1 = $_POST['corporate_address1']; 
                }
                 if(isset($_POST['corporate_address2'])){
                    $model->corporate_address2 = $_POST['corporate_address2']; 
                }
                 if(is_numeric($_POST['corporate_city'])){
                     $model->corporate_city_id = $_POST['corporate_city'];
                }else{
                     $model->corporate_city_id = $_POST['corporate_city_id'];
                }
                if(is_numeric($_POST['corporate_state'])){
                     $model->corporate_state_id = $_POST['corporate_state'];
                }else{
                     $model->corporate_state_id = $_POST['corporate_state_id'];
                }
                if(is_numeric($_POST['corporate_country'])){
                     $model->corporate_country_id = $_POST['corporate_country'];
                }else{
                     $model->corporate_country_id = $_POST['corporate_country_id'];
                }
                 if(isset($_POST['landline'])){
                    $model->landline = $_POST['landline']; 
                }
                 if(isset($_POST['mobile_line'])){
                    $model->mobile_line = $_POST['mobile_line']; 
                }
              * 
              */
                  
                $accepted_terms = 0;
                
                $basic_membership_type = $this->getTheBasicMembershiptypeId(); 
                
                $model->role =strtolower($_POST['role']);
                $model->category = $_POST['category'];
                $model->accounttype = $_POST['accounttype'];
                 $model->status = strtolower("active");
                $model->create_time = new CDbExpression('NOW()');
                $model->create_user_id = Yii::app()->user->id;
                $password = $_POST['password'];
                $password_repeat = $_POST['passwordCompare'];
                
               // $name = $model->firstname . ' ' . $model->middlename . ' ' . strtoupper($model->lastname);
                if($this->isUsernameAndEmailUnique($model->username,$model->email)){
                    if($password === $password_repeat){
                    
                    $model->password = $password;
                    $model->password_repeat = $password_repeat;
                    
                    if($model->getPasswordMinLengthRule($password)){
                        
                        if($model->getPasswordMaxLengthRule($password )){
                            
                            if($model->getPasswordCharacterPatternRule($password)){
                                
                        
                  $icon_error_counter = 0;     
                 // $name = $model->firstname . ' ' . $model->middlename . ' ' . strtoupper($model->lastname);
             /**   if($_FILES['picture']['name'] != ""){
                    if($this->isIconTypeAndSizeLegal()){
                       
                       $icon_filename = $_FILES['picture']['name'];
                      $icon_size = $_FILES['picture']['size'];
                        
                    }else{
                       
                        $icon_error_counter = $icon_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    $icon_filename = $this->provideUserIconWhenUnavailable($model);
                   $icon_size = 0;
             
                }//end of the if icon is empty statement
              * 
              */
                if($icon_error_counter ==0){
                   if($model->validate()){
                          // $model->picture = $this->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                          // $model->picture_size = $icon_size;
              
                           
                if($model->save()) {
                        
                    //$userid = Yii::app()->user->id;
                    if(isset($model->role)){
                            
                          if($this->assignRoleToAUser($model->role, $model->id)) {
                              if($this->isThisAssignmentOfMembershipTypeSuccessful($model->id,$basic_membership_type,$number_of_months,0,0,0,$accepted_terms,$_POST['status'])){
                                //get the invoice number for this subscription
                                 $invoice_number = $this->getTheInvoiceNumberOfThisPayment($model->id,$_POST['membership_type']); 
                                  
                                // $result['success'] = 'true';
                                $msg = "You are welcome to LeadsDome, the Online Marketplace for Sale's and Business Leads. You can now begin shopping. However, if you hope to become a merchant on LeadsDome, it will be advisable to upgrade your membership type";
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    //"invoice" => $invoice_number,
                                    "msg"=>$msg)
                                    );
                              
                              
                                 } else {
                                     $msg = 'You had successfully being registered to start using the LeadsDome online store. However, if you need  to do much more than just buying, please contact customer care to process your membership type ';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                                     
                                     
                                 } 
                                  
                              }else{
                                  $msg = 'User creation was successful but assignment of role to this user was not successful. Please contact customer care for assistance';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                                  
                              }
                              
                        }else{
                            $msg = "Role is not assigned to this User. Please assign the role and continue with the registration";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                            
                            
                        }
                        
                   
                         
                     }else {
                         //$result['success'] = 'false';
                         $msg = 'Your registration was not sucessful. Please contact customer care for asistance';
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                         
                     }
                        
                   }else{
                                
                                //delete all the moved files in the directory when validation error is encountered
                            $msg = "Validation Error: There seem to be a field validation issue with your form. Try again or contact customer care for assistance ";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                            );
                          }
                        }elseif($icon_error_counter > 0){
                        //get the platform assigned icon width and height
                            $platform_width = $this->getThePlatformSetIconWidth();
                            $platform_height = $this->getThePlatformSeticonHeight();
                            $icon_types = $this->retrieveAllTheIconMimeTypes();
                            $icon_types = json_encode($icon_types);
                            $msg = "Please check your picture file type or size as picture must be of width '$platform_width'px and height '$platform_height'px. Picture is of types '$icon_types'";
                            header('Content-Type: application/json');
                                    echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                    "msg" => $msg)
                            );
                         
                        } 
                                
                                
                            }else{
                                
                                $msg = 'Password must contain at least one number(0-9), at least one lower case letter(a-z), at least one upper case letter(A-Z), and at least one special character(@,&,$ etc)';
                                 header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() != 0,
                                 "msg" => $msg,
                                )); 
                            }
                            
                        }else{
                                $msg = 'The maximum Password length allowed is sixty(60)';
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                     "success" => mysql_errno() != 0,
                                    "msg" => $msg,
                            )); 
                            
                        }
                        
                        
                    }else{
                        $msg = 'The minimum Password length allowed is eight(8)';
                             header('Content-Type: application/json');
                            echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg,
                       )); 
                        
                        
                    }
                
           
                
            }else{
               $msg = 'Repeat Password do not match the new password';
              header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                           "msg" => $msg,
                       )); 
                
                
            }
                    
                }else{
                     $msg = 'Your username and Email must be unique';
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                           "msg" => $msg,
                       )); 
                    
                }
            
            
        }
        
        
        /**
         * This is the function that retrieves the basic membership type id
         */
        public function getTheBasicMembershiptypeId(){
            $model = new Membershiptype;
            return $model->getTheBasicMembershiptypeId();
        }
        
}
