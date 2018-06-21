<?php

class OrderDeliveryController extends Controller
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
				'actions'=>array('ListAllPendingOrdersForDelivery','ListAllDeliveredOrders','ListAllOrdersOnTransit',
                                    'ListAllOrdersOndispute','ListAllFailedOrdersForDelivery','retrieveDeliveredOrderDetails',
                                    'commenceOrderDelivery','updateDeliverOnTransit'),
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
         * This is the function that list all pending deliverables
         */
        public function actionListAllPendingOrdersForDelivery(){
            
            $userid = Yii::app()->user->id; 
            //get the domain of the logged in user
            
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='status=:status';
                $criteria->params = array(':status'=>'pending');
                $order= AssigningOrderForDelivery::model()->findAll($criteria);
                
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
         * This is the function that list all delivered orders
         */
        public function actionListAllDeliveredOrders(){
            
            $userid = Yii::app()->user->id; 
            //get the domain of the logged in user
            
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='status=:status';
                $criteria->params = array(':status'=>'delivered');
                $order= AssigningOrderForDelivery::model()->findAll($criteria);
                
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
         * This is the function that list all orders on transit
         */
        public function actionListAllOrdersOnTransit(){
            
            $userid = Yii::app()->user->id; 
            //get the domain of the logged in user
            
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='status=:status';
                $criteria->params = array(':status'=>'ontransit');
                $order= AssigningOrderForDelivery::model()->findAll($criteria);
                
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
         * This is the function that list all orders on dispute
         */
        public function actionListAllOrdersOndispute(){
            
            $userid = Yii::app()->user->id; 
            //get the domain of the logged in user
            
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='status=:status';
                $criteria->params = array(':status'=>'ondispute');
                $order= AssigningOrderForDelivery::model()->findAll($criteria);
                
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
         * This is the function that list all failed delivery
         */
        public function actionListAllFailedOrdersForDelivery(){
            
            $userid = Yii::app()->user->id; 
            //get the domain of the logged in user
            
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='status=:status';
                $criteria->params = array(':status'=>'failed');
                $order= AssigningOrderForDelivery::model()->findAll($criteria);
                
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
         * This is the function that retrieves extra delivery information
         */
        public function actionretrieveDeliveredOrderDetails(){
            
            $order_number = $this->getThisOrderNumber($_POST['order_id']);
            $member_name = $this->getThisMemberName($_POST['member_id']);
            
            //retrieve the order information from the perspective of the member/client
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='order_id=:order';
            $criteria->params = array(':order'=>$_POST['order_id']);
            $order= OrderDelivery::model()->find($criteria);
            
            $order_assigned_by = $this->getThisMemberName($_POST['order_assigned_by']);
            $order_delivered_by = $this->getThisMemberName($order['order_delivered_by']);
            $delivery_confirmed_by = $this->getThisMemberName($order['delivery_confirmed_by']);
                      
            
             if($order===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                      header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "order_number" => $order_number,
                                     "member"=>$member_name,
                                     "delivery_status"=>$order['status'],
                                     "member_remark"=>$order['member_remark'],
                                     "order_assigned_by"=>$order_assigned_by,
                                     "order_delivered_by"=>$order_delivered_by,
                                     "delivery_confirmed_by"=>$delivery_confirmed_by,
                                     "date_of_delivery_confirmation"=>$order['date_of_delivery_confirmation'],
                                     "date_of_delivery"=>$order['date_of_delivery']
                                        
                                
                            ));
                       
                }
            
        }
        
        
                  /**
         * This is the function that retrieves the order number
         */
        public function getThisOrderNumber($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $order= Order::model()->find($criteria);
                
                return $order['order_number'];
            
        }
        
        /**
            * This is the function that retrieves the name of the member
         */
        public function getThisMemberName($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $user= Members::model()->find($criteria);
                
                $name = $user['lastname'] .  ' '. $user['middlename'] . ' ' . $user['firstname'] ;
                
                return $name;
            
        }

        
        
        
        /**
         * This is the function that commences order delivery
         */
        public function actioncommenceOrderDelivery(){
            
           $order_id = $_POST['order_id'];
           $member_id = $_POST['member_id'];
           $commencement_date = date("Y-m-d H:i:s", strtotime($_POST['ontransit_commencement_date'])); 
           $ontransit_remark = $_POST['ontransit_remark'];
           
           if($this->commencementDateIsNotLessThaToday($_POST['ontransit_commencement_date'])){
               $cmd =Yii::app()->db->createCommand();
                $result = $cmd->update('assigning_order_for_delivery',
                         array('status'=>'ontransit',
                             'ontransit_remark'=>$ontransit_remark,
                             'ontransit_commencement_date'=>date("Y-m-d H:i:s", strtotime($_POST['ontransit_commencement_date'])),
                             
                        ),
                        ("order_id=$order_id && member_id=$member_id")
                          
                     );
                
                if($result>0){
                     $msg = "Delivery of order has commenced sucessfully";
                    header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "msg" => $msg
                                        
                                
                            ));
                }else{
                     $msg = "You need to contact support services as this delivery could not be commenced";
                    header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                     "msg" => $msg
                                        
                                
                            ));
                }
               
           }else{
               $msg = "Commence date cannot be less than today";
               header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                     "msg" => $msg
                                        
                                
                            ));
               
               
           }
           
           
                   
        }
        
        
        /**
         * This is the function that confirms if commencement date is not less than today
         */
        public function commencementDateIsNotLessThaToday($ontransit_commencement_date){
            
            
            $today = getdate(mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
            $commencement_date = getdate(strtotime($ontransit_commencement_date));
            
            if(($commencement_date['year'] - $today['year'])<=0){
                if(($commencement_date['mon'] - $today['mon'])<=0){
                    if(($commencement_date['mday'] - ($today['mday']-1))<=0){
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
         * This is the function that updates the delivery records on transit
         */
public function actionupdateDeliverOnTransit(){
    
    $current_status = strtolower($_POST['current_status']);
    $order_id = $_POST['order_id'];
    $member_id = $_POST['member_id'];
    $remark = $_POST['ontransit_remark'];
    $date_of_delivery = new CDbExpression('NOW()');
    
    if($current_status == 'failed'){
        if($this->updateOfThisFailedOrderDeliveryIsSuccessful($order_id,$member_id,$current_status,$remark,$date_of_delivery)){
            
             $msg = "Update of this delivery to failed status is effected";
               header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "msg" => $msg
                                        
                                
                            ));
        }else{
             $msg = "Could not effect the update of this delivery to a failed status. Contact the service team";
               header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                     "msg" => $msg
                                        
                                
                            ));
            
        }
    }else if($current_status == 'ondispute'){
        
        if($this->updateOfThisOnDisputeOrderDeliveryIsSuccessful($order_id,$member_id,$current_status,$remark,$date_of_delivery)){
            
             $msg = "Update of this delivery to on dispute status is effected";
               header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "msg" => $msg
                                        
                                
                            ));
        }else{
             $msg = "Could not effect the update of this delivery to on dispute status. Contact the service team";
               header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                     "msg" => $msg
                                        
                                
                            ));
            
        }
        
        
    }else if($current_status == 'delivered'){
         if($this->updateOfThisOrderDeliveryIsSuccessful($order_id,$member_id,$current_status,$remark,$date_of_delivery)){
            
             $msg = "Update of this delivery to delivered status is effected";
               header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "msg" => $msg
                                        
                                
                            ));
        }else{
             $msg = "Could not effect the update of this delivery to a delivered status. Contact the service team";
               header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                     "msg" => $msg
                                        
                                
                            ));
            
        }
        
        
    }
    
    
}


/**
 * This is the function that updates the failed delivery
 */
public function updateOfThisFailedOrderDeliveryIsSuccessful($order_id,$member_id,$current_status,$remark,$date_of_delivery){
    
    $cmd =Yii::app()->db->createCommand();
                $result = $cmd->update('assigning_order_for_delivery',
                         array('status'=>$current_status,
                             'failed_remark'=>$remark,
                             'delivery_return_date'=>$date_of_delivery,
                             
                        ),
                        ("order_id=$order_id && member_id=$member_id")
                          
                     );
                
                if($result>0){
                    $this->registerTheDateOfDelivery($order_id,$date_of_delivery);
                    return true;
                }else{
                    return false;
                }
    
}


/**
 * This is the function that updates the  delivery on dispute
 */
public function updateOfThisOnDisputeOrderDeliveryIsSuccessful($order_id,$member_id,$current_status,$remark,$date_of_delivery){
    
    $cmd =Yii::app()->db->createCommand();
                $result = $cmd->update('assigning_order_for_delivery',
                         array('status'=>$current_status,
                             'ondispute_remark'=>$remark,
                             'delivery_return_date'=>$date_of_delivery,
                             
                        ),
                        ("order_id=$order_id && member_id=$member_id")
                          
                     );
                
                if($result>0){
                    $this->registerTheDateOfDelivery($order_id,$date_of_delivery);
                    return true;
                }else{
                    return false;
                }
    
}



/**
 * This is the function that updates the  delivery on dispute
 */
public function updateOfThisOrderDeliveryIsSuccessful($order_id,$member_id,$current_status,$remark,$date_of_delivery){
    
    $cmd =Yii::app()->db->createCommand();
                $result = $cmd->update('assigning_order_for_delivery',
                         array('status'=>$current_status,
                             'delivered_remark'=>$remark,
                             'delivery_return_date'=>$date_of_delivery,
                             
                        ),
                        ("order_id=$order_id && member_id=$member_id")
                          
                     );
                
                if($result>0){
                    $this->registerTheDateOfDelivery($order_id,$date_of_delivery);
                    return true;
                }else{
                    return false;
                }
    
}




/**
 * This is the function that regiosters the date of delivery
 */
public function registerTheDateOfDelivery($order_id,$date_of_delivery){
    
     $cmd =Yii::app()->db->createCommand();
                $result = $cmd->update('order_delivery',
                         array('order_delivered_by'=>Yii::app()->user->id,
                             'date_of_delivery'=>$date_of_delivery
                           
                             
                        ),
                        ("order_id=$order_id")
                          
                     );
    
}


}
