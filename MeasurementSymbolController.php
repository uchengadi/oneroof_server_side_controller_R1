<?php

class MeasurementSymbolController extends Controller
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
				'actions'=>array('DeleteThisMeasurementParameter','createnewmeasurementparameter','updatemeasurementparameter',
                                    'ListAllMeasurementSymbols'),
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
         * This is the function that deletes a measurement parameter
         */
        public function actionDeleteThisMeasurementParameter(){
             $_id = $_POST['id'];
            $model= MeasurementSymbol::model()->findByPk($_id);
            
            //get the measurement symbol name
            $symbol_name = $this->getThisMeasurementSymbolName($_id);
            if($model === null){
                 $msg = 'No Such Record Exist'; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                          
                           
                           
                          
                       ));
                                      
            }else if($model->delete()){
                    $msg = "'$symbol_name' Measurement Symbol had been deleted successfully"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                           
                           
                           
                          
                       ));
                       
            } else {
                    $msg = "Validation Error: '$symbol_name' Measurement Symbol was not deleted"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            //"selected" => $selected,
                            "msg" => $msg
                           
                           
                           
                          
                       ));
                            
                }
            
            
        }
        
        /**
         * This is the function that list all measurement parameter
         */
        public function actionListAllMeasurementSymbols(){
            
            $userid = Yii::app()->user->id;
          
            $symbol = MeasurementSymbol::model()->findAll();
                if($symbol===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "symbol" => $symbol
                                        
                                
                            ));
                       
                }
            
        }
        
        
        /**
         * This is the function that creates  a new measurement parameter
         */
        public function actioncreatenewmeasurementparameter(){
            
            $model=new MeasurementSymbol;
            
            $model->name = $_POST['name'];
            $model->description = $_POST['description'];
            $model->type = strtolower($_POST['type']);
            $model->create_time = new CDbExpression('NOW()');
            $model->create_user_id = Yii::app()->user->id;
                if($model->save()){
                         // $result['success'] = 'true';
                          $msg = 'New Measurement Parameter Successfully Added';
                          header('Content-Type: application/json');
                          echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                 }else {
                         //$result['success'] = 'false';
                         $msg = 'Addition of this Measurement Parameter was unsuccessful';
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
            
        }
        
        
        /**
         * This is the function that updates a measurement parameter
         */
        public function actionupdatemeasurementparameter(){
            
            $_id = $_POST['id'];
            $model=  MeasurementSymbol::model()->findByPk($_id);
            
            $model->name = $_POST['name'];
            $model->description = $_POST['description'];
            $model->type = strtolower($_POST['type']);
            $model->update_time = new CDbExpression('NOW()');
            $model->update_user_id = Yii::app()->user->id;
                if($model->save()){
                         // $result['success'] = 'true';
                          $msg = 'Update of this Measurement Parameter was successfully';
                          header('Content-Type: application/json');
                          echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                           );
                 }else {
                         //$result['success'] = 'false';
                         $msg = 'Ypdate of this Measurement Parameter was unsuccessful';
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
            
            
            
        }
        
        
        /**
         * This is the function that retrieves the product type name
         */
        public function getThisMeasurementSymbolName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $name = MeasurementSymbol::model()->find($criteria);
            
            return $name['name'];
            
            
        }
}
