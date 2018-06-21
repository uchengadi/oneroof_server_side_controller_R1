<?php

class HamperHasNonMemberBeneficiaryController extends Controller
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
				'actions'=>array('create','update','listallnonMemberBeneficiariesForAHamper','addANonMemberAsHamperBeneficiary',
                                    'updateANonMemberAsHamperBeneficiary','removeThisHamperBeneficiary'),
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
         * This is the function that list all non members that are beneficiaries of a hamper
         */
        public function actionlistallnonMemberBeneficiariesForAHamper(){
               $hamper_id = $_REQUEST['hamper_id'];
            
               $criteria = new CDbCriteria();
               $criteria->select = '*';
               $criteria->condition='hamper_id=:hamperid';
               $criteria->params = array(':hamperid'=>$hamper_id);
               $beneficiary= HamperHasNonMemberBeneficiary::model()->findAll($criteria);
                
                if($beneficiary===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                      header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "beneficiary" => $beneficiary
                                        
                                
                            ));
                       
                }
        
            
        }
        
        
        /**
         * This is the function that adds new non member hamper beneficiary
         */
        public function actionaddANonMemberAsHamperBeneficiary(){
            
            $model = new HamperHasNonMemberBeneficiary;
            
            $model->hamper_id = $_POST['hamper_id'];
            $model->hamper_label = $_POST['hamper_label'];
            $model->beneficiary = $_POST['non_member_beneficiary'];
            $model->status = strtolower('pending');
            $model->delivery_type = strtolower($_POST['delivery_type']);
            $model->number_of_hampers_delivered = $_POST['number_of_hampers_delivered'];
            $model->city_id = $_POST['city'];
            $model->state_id = $_POST['state'];
            $model->country_id = $_POST['country'];
            $model->place_of_delivery = $_POST['address'];
            $model->hamper_container_id = $_POST['hamper_container_id'];
            
            if($model->save()) {
                           $msg = "$model->beneficiary had been added as a beneficiary of the $model->hamper_label hamper";
                               header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                        "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                         )
                                 );
                      
                         
              }else{
                            //delete all the moved files in the directory when validation error is encountered
                            $msg = "There was an issue adding $model->beneficiary as a beneficiary to the  $model->hamper_label hamper. Possibly field validation error";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                               );
                          }
            
            
        }
        
        
        
        /**
         * This is the function that adds new non member hamper beneficiary
         */
        public function actionupdateANonMemberAsHamperBeneficiary(){
            
            $_id = $_POST['id'];
            $model=HamperHasNonMemberBeneficiary::model()->findByPk($_id);
            
            $model->hamper_id = $_POST['hamper_id'];
            $model->hamper_label = $_POST['hamper_label'];
            $model->beneficiary = $_POST['non_member_beneficiary'];
            $model->status = strtolower('pending');
            $model->delivery_type = strtolower($_POST['delivery_type']);
            $model->number_of_hampers_delivered = $_POST['number_of_hampers_delivered'];
            $model->city_id = $_POST['city'];
            $model->state_id = $_POST['state'];
            $model->country_id = $_POST['country'];
            $model->place_of_delivery = $_POST['address'];
            $model->hamper_container_id = $_POST['hamper_container_id'];
            
            if($model->save()) {
                           $msg = "$model->beneficiary had been added as a beneficiary of the $model->hamper_label hamper";
                               header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                        "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                         )
                                 );
                      
                         
              }else{
                            //delete all the moved files in the directory when validation error is encountered
                            $msg = "There was an issue adding $model->beneficiary as a beneficiary to the  $model->hamper_label hamper. Possibly field validation error";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                               );
                          }
            
            
        }
        
    /**
    * this is the function that removes a hamper beneficiary
    */
   public function actionremoveThisHamperBeneficiary(){
       $model = new HamperHasNonMemberBeneficiary;
       $hamper_id = $_REQUEST['hamper_id'];
       $beneficiary = $_REQUEST['beneficiary'];
       $hamper_label = $_REQUEST['hamper_label'];
       $id = $_REQUEST['id'];
       
           
       if($model->isThisMemberAlreadyABeneficiaryOfTheHamper($id)){
           if($model->isTheRemovalOfBeneficiaryFromHamperListASuccess($id)){
               $msg = " $beneficiary had successfully been removed from  the $hamper_label hamper beneficiary list"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg" => $msg
               
                       ));
               
           }else{
               //could not remove hamper beneficiary
                $msg = " $beneficiary could not be removed from  the $hamper_label hamper beneficiary list. Please contact customer cate for assistance"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg
               
                       ));
           }
           
       }else{
           //record does not exist
            $msg = " $beneficiary is not currently a beneficiary to the $hamper_label hamper beneficiary list."; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg
               
                       ));
       }
   }
   
   
   /**
    * This is the function that gets a hamper beneficiary name
    */
   public function getTheNameOfThisMember($beneficiary_id){
       $model = new Members;
       return $model->getTheNameOfThisMember($beneficiary_id);
   }
}
