<?php

class FuturesController extends Controller
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
				'actions'=>array('create','update','listallfuturesinitiatedByMember','listallfuturesreceivedByMember','listallfutures'),
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
         * This is the function that list all futures in the system
         */
        public function actionlistallfutures(){
            
            $future = Futures::model()->findAll();
                if($future===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"assigned" => $assigned,
                            "future" => $future)
                       );
                       
                }
        }
        
        
        /**
         * This is the function that list all futures initiated by a member
         */
        public function actionlistallfuturesinitiatedByMember(){
            
            $member_id = Yii::app()->user->id;
          
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='futures_initiated_by=:id';
            $criteria->params = array(':id'=>$member_id);
            $future= Futures::model()->findAll($criteria);
            
             if($future===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "futures" => $future)
                       );
                       
                }
        }
        
        /**
         * This is the function that list all futures received by a member
         */
        public function actionlistallfuturesreceivedByMember(){
            
            $model = new ProductHasVendor;
            
            $member_id = Yii::app()->user->id;
          
            //get all the product that a member is a merchant of
            $merchant_products = $model->getAllTheProductThisMemberIsAMerchantOf($member_id);
            
            $all_future_received = [];
            //retrieve all escrow
            $criteria = new CDbCriteria();
            $criteria->select = '*';
          // $criteria->condition='status=:status or (status=:accepted and quote_response_from=:responsefrom';
          //  $criteria->params = array(':status'=>'live',':accepted'=>'accepted',':responsefrom'=>$member_id);
            $futures= Futures::model()->findAll($criteria);
            
            foreach($futures as $future){
                if(in_array($future['product_id'],$merchant_products)){
                    $all_future_received[] = $future;
                }
                
                
            }
            
             if($future===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "futures" => $all_future_received)
                       );
                       
                }
            
        }
}
