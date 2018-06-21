<?php

class ProductSpecificationController extends Controller
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
				'actions'=>array('create','update','ListAllProductSpecifications','updateProductSpecification',
                                    'createnewProductSpecification','DeleteThisProductSpecification','retrieveallspecifications',
                                    'AssignSpecificationsToProducttype','ListAllProductTypesWithSpecifications',
                                    'removeThisSpecificationFromThisProducttype','retrievespecificationsforproducttypes',
                                    'ReassignSpecificationsToProducttype','assignSpecificationValuesToProduct'),
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
         * This is the function that list all product specifications
         */
        public function actionListAllProductSpecifications(){
            
            $userid = Yii::app()->user->id;
          
            $specification = ProductSpecification::model()->findAll();
                if($specification ===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "specification" => $specification
                                        
                                
                            ));
                       
                }
            
            
            
        }
        
        /**
         * This is the function that will delete a product specfication
         */
        public function actionDeleteThisProductSpecification(){
            
            $_id = $_POST['id'];
            $model=  ProductSpecification::model()->findByPk($_id);
            
            //get the currency name
            $specification_name = $this->getThisProductSpecificationName($_id);
            if($model === null){
                 $msg = 'No Such Record Exist'; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                          
                           
                           
                          
                       ));
                                      
            }else if($model->delete()){
                    $msg = "' $specification_name' Product specification had been deleted successfully"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                           
                           
                           
                          
                       ));
                       
            } else {
                    $msg = "Validation Error: ' $specification_name' Product specification was not deleted"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            //"selected" => $selected,
                            "msg" => $msg
                           
                           
                           
                          
                       ));
                            
                }
        }
        
        
        /**
         * This is the function that gets a specification name
         */
        public function getThisProductSpecificationName($id){
            $model = new ProductSpecification;
            
            return $model->getThisProductSpecificationName($id);
        }
        
        
        /**
         * This is the function that adds a new product specification
         */
        public function actioncreatenewProductSpecification(){
            
            $model = new ProductSpecification;
            
            $model->name = $_POST['name'];
            $model->code = $_POST['code'];
            $model->description = $_POST['description'];
            $model->create_time = new CDbExpression('NOW()');
            $model->create_user_id = Yii::app()->user->id;
            
             if($model->save()){
                      
                            $msg = "'$model->name' product specification was successfully added";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                            );
                           
                       
                        
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Validation Error: '$model->name' product specification  was not added";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
            
        }
        
        
        
        /**
         * This is the function that updates the product specification information
         */
        public function actionupdateProductSpecification(){
            
            $_id = $_POST['id'];
            $model=  ProductSpecification::model()->findByPk($_id);
            
            $model->name = $_POST['name'];
            $model->code = $_POST['code'];
            $model->description = $_POST['description'];
            $model->update_time = new CDbExpression('NOW()');
            $model->update_user_id = Yii::app()->user->id;
            
             if($model->save()){
                      
                            $msg = "'$model->name' product specification was successfully updated";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                            );
                           
                       
                        
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Validation Error: '$model->name' product specification  was not updated";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  }
            
        }
        
        
        
        /**
         * This is the function that retrieves all products' specifications on the platform
         */
        public function actionretrieveallspecifications(){
            
            $userid = Yii::app()->user->id;
          
            $specification = ProductSpecification::model()->findAll();
                if($specification ===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "specification" => $specification
                                        
                                
                            ));
                       
                }
            
            
            
        }
        
        
        /**
         * This is the function that asigns specifications to product type
         */
        public function actionAssignSpecificationsToProducttype(){
            
            $producttype = $_POST['producttype'];
            
            //delete all specifications assigned to this product type
            $cmd =Yii::app()->db->createCommand();
            $cmd->delete('producttype_has_specifications', 'product_type_id=:id', array(':id'=>$producttype ));
            
            //reassign or assign some new set of specifications
            
            $counter = 0;
            foreach($_POST['specifications'] as $specification){
                
                if($this->isThisSpecificationAssignedToThisProducttype($producttype,$specification)){
                    $counter = $counter + 1;
                }
                
                
            }
            //get the name of this product type
            $producttype_name = $this->getTheNameOfThisProductType($producttype);
            $msg= " '$counter' specifications had been added to this '$producttype_name' product type";
             header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "msg" => $msg
                                                                       
                            ));
            
            
            
        }
        
        
        
        /**
         * This is the function that asigns specifications to product type
         */
        public function actionReassignSpecificationsToProducttype(){
            
            $producttype = $_POST['product_type_id'];
            
            //delete all specifications assigned to this product type
            $cmd =Yii::app()->db->createCommand();
            $cmd->delete('producttype_has_specifications', 'product_type_id=:id', array(':id'=>$producttype ));
            
            //reassign or assign some new set of specifications
            
            $counter = 0;
            foreach($_POST['specifications'] as $specification){
                
                if($this->isThisSpecificationAssignedToThisProducttype($producttype,$specification)){
                    $counter = $counter + 1;
                }
                
                
            }
            //get the name of this product type
            $producttype_name = $this->getTheNameOfThisProductType($producttype);
            $msg= " '$counter' specifications had been added to this '$producttype_name' product type";
             header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "msg" => $msg
                                                                       
                            ));
            
            
            
        }
        
        
        
        /**
         * This is a function that will list all product types with specification 
         */
        public function actionListAllProductTypesWithSpecifications(){
            
            $userid = Yii::app()->user->id;
          
            $specification = ProducttypeHasSpecifications::model()->findAll();
                if($specification ===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "producttype" => $specification
                                        
                                
                            ));
                       
                }
            
        }
        
        
        /**
         * This is the function that retrieves the name of a product type
         */
        public function getTheNameOfThisProductType($id){
            $model = new ProductType;
            
            return $model->getTheNameOfThisProductType($id);
            
        }
        
        
        /**
         * This is the function that inserts a new specification to a product type
         */
        public function isThisSpecificationAssignedToThisProducttype($producttype_id,$specification_id){
            $model = new ProducttypeHasSpecifications;
            
            return $model->isThisSpecificationAssignedToThisProducttype($producttype_id,$specification_id);
            
            
        }
        
        
        /**
         * This is the function that removes a specification from a product type
         */
        public function actionremoveThisSpecificationFromThisProducttype(){
            
            $product_type_id = $_REQUEST['type_id'];
            $specification_id = $_REQUEST['spec_id'];
            
            
            $producttype_name = $this->getTheNameOfThisProductType($product_type_id);
            $specification = $this->getThisProductSpecificationName($specification_id);
            //from the specification from the product type
            $cmd =Yii::app()->db->createCommand();
            $result = $cmd->delete('producttype_has_specifications', 'product_type_id=:id and specification_id=:specid', array(':id'=>$product_type_id,':specid'=>$specification_id ));
            if($result>0){
                $msg = "This '$specification' specification is successfully removed from the '$producttype_name' product type";
                 header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "msg" => $msg
                                        
                                
                            ));
            }else{
                $msg = "This '$specification' specification could not be removed from the '$producttype_name' product type";
                 header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                     "msg" => $msg
                                        
                                
                            ));
            }
            
            
            
        }
        
        
        /**
         * This is the function retrieves product types with its specifications
         */
        public function actionretrievespecificationsforproducttypes(){
            
            $product_type_id = $_POST['product_type_id'];
            
            $specification_id = $_POST['specification_id'];
            
            //retrieve all specfications
             $specification = ProductSpecification::model()->findAll();
             
             //get the product type name
             
           $producttype_name = $this->getTheNameOfThisProductType($product_type_id);
            //retrieve all specifications in this product type
           $criteria = new CDbCriteria();
           $criteria->select = '*';
           $criteria->condition='product_type_id=:id';
           $criteria->params = array(':id'=>$product_type_id);
           $selected= ProducttypeHasSpecifications::model()->findAll($criteria);
             header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "specification" => $specification,
                                     "selected"=>$selected,
                                     "producttype"=>$producttype_name
                                        
                                
                            ));
        }
        
        
        /**
         * This is the function that assigns specification values to product
         */
        public function actionassignSpecificationValuesToProduct(){
            
            
            $model = new ProductHasSpecifications;
            $product_type_id = $_POST['producttype'];
            $product_id = $_POST['product'];
            //get the product type code
            $producttype_code = $this->getTheProductTypeCode($product_type_id);
            
        $counter  = 0;    
        if($producttype_code == '001'){
                $model->released_date = $_POST['released_date'];
                $model->dimension = $_POST['dimension'];
                $model->weight = $_POST['weight'];
                $model->touchscreen = $_POST['touchscreen'];
                              
                //assign this specification values to this product
                if($this->isAssigningThisSpecificationValuesToThisProductSuccessful($model,$_POST['type_001_specifications'],$product_id)){
                    
                    $counter = $counter + 1;
                }
       
            }else if($producttype_code == '002'){
                $model->dimension = $_POST['dimension'];
                $model->weight = $_POST['weight'];
                $model->touchscreen = $_POST['touchscreen'];
                              
                //assign this specification values to this product
                if($this->isAssigningThisSpecificationValuesToThisProductSuccessful($model,$_POST['type_002_specifications'],$product_id)){
                    
                    $counter = $counter + 1;
                }
            }else if($producttype_code == '003'){
                $model->dimension = $_POST['dimension'];
                $model->weight = $_POST['weight'];
                $model->touchscreen = $_POST['touchscreen'];
                              
                //assign this specification values to this product
                if($this->isAssigningThisSpecificationValuesToThisProductSuccessful($model,$_POST['type_003_specifications'],$product_id)){
                    
                    $counter = $counter + 1;
                }
            }else if($producttype_code == '004'){
                $model->dimension = $_POST['dimension'];
                $model->weight = $_POST['weight'];
                $model->touchscreen = $_POST['touchscreen'];
                              
                //assign this specification values to this product
                if($this->isAssigningThisSpecificationValuesToThisProductSuccessful($model,$_POST['type_004_specifications'],$product_id)){
                    
                    $counter = $counter + 1;
                }
            }else if($producttype_code == '005'){
                $model->dimension = $_POST['dimension'];
                $model->weight = $_POST['weight'];
                $model->touchscreen = $_POST['touchscreen'];
                              
                //assign this specification values to this product
                if($this->isAssigningThisSpecificationValuesToThisProductSuccessful($model,$_POST['type_005_specifications'],$product_id)){
                    
                    $counter = $counter + 1;
                }
                
            }else if($producttype_code == '006'){
                
                $model->dimension = $_POST['dimension'];
                $model->weight = $_POST['weight'];
                $model->touchscreen = $_POST['touchscreen'];
                              
                //assign this specification values to this product
                if($this->isAssigningThisSpecificationValuesToThisProductSuccessful($model,$_POST['type_006_specifications'],$product_id)){
                    
                    $counter = $counter + 1;
                }
                
            }
         
         if($counter >0){
             $msg = "The Values of the related specifications had been added to '$product_name' product";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                       )
                           );
             
         }else{
              $msg = "The Values of the related specifications could not be added to '$product_name' product";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg,
                                       )
                           );
         }
            
            
            
        }
        
        
        /**
         * This is the function that will assign specification values to a product
         */
        public function isAssigningThisSpecificationValuesToThisProductSuccessful($productmodel,$type_specifications,$product_id){
            
            $model = new ProductHasSpecifications;
            
            return $model->isAssigningThisSpecificationValuesToThisProductSuccessful($productmodel,$type_specifications,$product_id);
        }
}
