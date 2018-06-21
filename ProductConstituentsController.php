<?php

class ProductConstituentsController extends Controller
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
				'actions'=>array('index','view','obtainConstituentsExtraInformation','ListAllProductConstituents',
                                    'retrieveProductConstituentDetails','confirmPriceValidity','ListThisProductConstituents'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('obtainConstituentsExtraInformation','DeleteThisProductConstituents','addNewProductConstituent',
                                    'updateProductConstituent','ListAllProductConstituents','ListThisProductConstituents',
                                    'retrieveProductConstituentDetails','effectTheChangesToThePack','removeThisConstituentFromThePack',
                                    'restoreConstituentProductToThePack','retrieveAllRemovedMemberProductConstituent','confirmPriceValidity'),
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
         * This is the function that retrieves extra information on product constituent
         */
        public function actionobtainConstituentsExtraInformation(){
            
            $product_id = $_REQUEST['product_id'];
            $id = $_REQUEST['id'];
           // $measurement_symbol_id = $_REQUEST['measurement_symbol_id'];
            
            $product_name = $this->getThisProductName($product_id);
           $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$id);
            $product= ProductConstituents::model()->find($criteria);
                   
            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                       "name"=>$product_name,
                                       "product"=>$product
                                        //"measurement_symbol"=> $measurement_symbol                                       
                                
                            ));
            
            
        }
        
        
        
        /**
         *  * This is the function that retrieves a product constituents' details
         */
        public function actionretrieveProductConstituentDetails(){
            
                $model = new MemberAmendedConstituents;
                $member_id = Yii::app()->user->id;
                $product_id = $_REQUEST['id'];
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$product_id);
                $constituent= ProductConstituents::model()->find($criteria);
                
                if($member_id != null){
                    
                 if(isset($_REQUEST['order_id'])){
                     if($_REQUEST['order_id'] == 0){
                     
                    //confirm if this constituent product was amended by this member
                 $result = $model->isConstituentQuantityActuallyAmendedByMember($constituent['id'],$member_id);
                 $member_quantity = $model->getThisMemberAmendedConstituentQuantity($constituent['id'],$member_id);
               
                 header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $constituent,
                                     "amended"=>$result,
                                     "member_quantity"=>$member_quantity
                                        
                                
                            ));
                     
                 }else{
                     //get the quantity of this historic transaction
                     $history_quantity = $this->getTheHistoryQuantityOfThisTransaction($_REQUEST['order_id'],$_REQUEST['id']);
                     
                     header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $constituent,
                                     "amended"=>"history",
                                     "history_quantity"=>$history_quantity
                                        
                                
                            ));
                 }
                     
                 }else{
                         //confirm if this constituent product was amended by this member
                 $result = $model->isConstituentQuantityActuallyAmendedByMember($constituent['id'],$member_id);
                 $member_quantity = $model->getThisMemberAmendedConstituentQuantity($constituent['id'],$member_id);
               
                 header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $constituent,
                                     "amended"=>$result,
                                     "member_quantity"=>$member_quantity
                                        
                                
                            ));
                     
                 }   
                 
                
                    
                }else{
                    header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $constituent,
                                    
                                        
                                
                            ));
                    
                }
                
                
            
        }
        
        
        
        /**
         * This is the quantity that gets the historic transaction value in an order
         */
        public function getTheHistoryQuantityOfThisTransaction($order_id,$constituent_id){
            
            $model = new OrderHasConstituents;
            
            return $model->getTheNewConstituentQuantityFromTheCart($order_id,$constituent_id);
        }
        
        
         /**
         * This is the function that retrieves the product name
         */
        public function getThisProductName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $name = Product::model()->find($criteria);
            
            return $name['name'];
            
            
        }
        
        
        /**
         * This is the function that retrieves the service name
         */
        public function getThisMeasurementSymbolName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $name = MeasurementSymbol::model()->find($criteria);
            
            return $name['name'];
            
            
        }
        
        
        
        /**
         * This is the function that retrieves the constituents name
         */
        public function getThisConstituentName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $name = ProductConstituents::model()->find($criteria);
            
            return $name['name'];
            
            
        }
        
        
        /**
         * This is the function that deletes a product
         */
        public function actionDeleteThisProductConstituents(){
            
            $_id = $_POST['id'];
            $model=  ProductConstituents::model()->findByPk($_id);
            
            //get the currency name
            $constituent_name = $this->getThisConstituentName($_id);
            if($model === null){
                 $msg = 'No Such Record Exist'; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                          
                           
                           
                          
                       ));
                                      
            }else if($model->delete()){
                    $msg = "'$constituent_name' Product Constituent had been deleted successfully"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                           
                           
                           
                          
                       ));
                       
            } else {
                    $msg = "Validation Error: '$constituent_name' Product Constituent was not deleted"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            //"selected" => $selected,
                            "msg" => $msg
                           
                           
                           
                          
                       ));
                            
                }
            
        }
        
        
        
        
             /**
         * This is the function that retrieves all product constituents
         */
        public function actionListAllProductConstituents(){
            
             $userid = Yii::app()->user->id;
          
            $constituents = ProductConstituents::model()->findAll();
                if($constituents===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $constituents
                                        
                                
                            ));
                       
                }
            
            
        }
        
        
         /**
         * This is the function that adds a product constituent
         */
        public function actionaddNewProductConstituent(){
            
            $model=new ProductConstituents;
            
            
            //get the logged in user id
            $userid = Yii::app()->user->id;
                     
            $model->name = $_POST['name'];
            if(isset($_POST['description'])){
                $model->description = $_POST['description'];
            }
            if(is_numeric($_POST['product'])){
                $model->product_id = $_POST['product'];
            }else{
                $model->product_id = $_POST['product_id'];
            }
            if(isset($_POST['condition'])){
                $model->condition = $_POST['condition'];
            }
             if(isset($_POST['specifications'])){
               $model->specifications = $_POST['specifications'];
           }
            if(isset($_POST['prevailing_retail_selling_price'])){
                 $model->prevailing_retail_selling_price = $_POST['prevailing_retail_selling_price'];
            }
            if(isset($_POST['per_portion_price'])){
               $model->per_portion_price = $_POST['per_portion_price'];
           }
           // $model->cumulative_selling_price = $this->generateTheCumulativeSellingPrice($model->per_portion_price, $model->maximum_portion);
           if(isset($_POST['feature'])){
               $model->feature = $_POST['feature'];
           } 
           if(isset($_POST['minimum_number_of_product_to_buy'])){
                $model->minimum_number_of_product_to_buy = $_POST['minimum_number_of_product_to_buy'];   
           }
           if(isset($_POST['whats_in_a_park'])){
              $model->whats_in_a_park = $_POST['whats_in_a_park']; 
           } 
            if(isset($_POST['whats_product_per_item'])){
                $model->whats_product_per_item = $_POST['whats_product_per_item'];
            }
             if(isset($_POST['price_validity_period'])){
                $model->price_validity_period = $_POST['price_validity_period'];
            }
           if(isset($_POST['quantity'])){
              $model->quantity = $_POST['quantity']; 
           } 
           if(isset($_POST['quantity_of_product_in_the_pack'])){
              $model->quantity_of_product_in_the_pack = $_POST['quantity_of_product_in_the_pack']; 
           } 
          //  $model->discount_rate = $this->generateTheDiscountRate($model->prevailing_retail_selling_price,$model->per_portion_price);
        
            if(isset($_POST['displayable_on_store'])){
                $model->displayable_on_store = $_POST['displayable_on_store'];
            }else{
                $model->displayable_on_store = 0;
            }
             if(isset($_POST['brand'])){
              $model->brand = $_POST['brand']; 
           } 
           if(isset($_POST['maker'])){
              $model->maker = $_POST['maker']; 
           }
           if(isset($_POST['origin'])){
              $model->origin = $_POST['origin']; 
           }
           if(isset($_POST['start_price_validity_period'])){
              $model->start_price_validity_period = date("Y-m-d H:i:s", strtotime($_POST['start_price_validity_period']));
           }
            if(isset($_POST['end_price_validity_period'])){
              $model->end_price_validity_period = date("Y-m-d H:i:s", strtotime($_POST['end_price_validity_period']));
           }
            
            $model->create_user_id = $userid;
            $model->create_time = new CDbExpression('NOW()');
                      
           
              //declare a universal error message variable
               $icon_error_counter = 0;
               $poster_error_counter =0;
               $product_front_view_error_counter =0;
               $product_right_side_view_error_counter =0;
               $product_top_view_error_counter =0;
               $product_inside_view_error_counter =0;
               $product_engine_view_error_counter =0;
               $product_back_view_error_counter =0;
               $product_left_side_view_error_counter =0;
               $product_bottom_view_error_counter =0;
               $product_dashboard_view_error_counter =0;
               $product_contents_or_booth_view_error_counter =0;
                //working with the icon file
                if($_FILES['icon']['name'] != ""){
                    if($this->isIconTypeAndSizeLegal()){
                        
                       $icon_filename = $_FILES['icon']['name'];
                       $icon_size = $_FILES['icon']['size'];
                        
                    }else{
                       
                        $icon_error_counter = $icon_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    $icon_filename = $this->provideProductIconWhenUnavailable($model);
                   $icon_size = 0;
             
                }//end of the if icon is empty statement
                
                
                //Working with the poster file
                 if($_FILES['headline_image']['name'] != ""){
                    if($this->isPosterTypeAndSizeLegal()){
                        
                       $poster_filename = $_FILES['headline_image']['name'];
                       $poster_size = $_FILES['headline_image']['size'];
                        
                    }else{
                        $poster_error_counter = $poster_error_counter + 1;
                        
                    }//end of the determine size and type statement
                }else{
                     $poster_filename = $this->provideProductImageWhenUnavailable($model);
                    $poster_size = 0;
                }
                
                
                //This is the segment to capture and verify the product front view image file
                if($_FILES['product_front_view']['name'] != ""){
                    if($this->isProductFronViewTypeAndSizeLegal()){
                        
                       $product_front_view_filename = $_FILES['product_front_view']['name'];
                       $product_front_view_size = $_FILES['product_front_view']['size'];
                        
                    }else{
                       
                        $product_front_view_error_counter = $product_front_view_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    $product_front_view_filename = $this->provideProductIconWhenUnavailable($model);
                   $product_front_view_size = 0;
             
                }
                
                
                //This is the segment to capture and verify the product right side view image file
                
                 if($_FILES['product_right_side_view']['name'] != ""){
                    if($this->isProductRightSizeViewTypeAndSizeLegal()){
                        
                       $product_right_side_view_filename = $_FILES['product_right_side_view']['name'];
                       $product_right_side_view_size = $_FILES['product_right_side_view']['size'];
                        
                    }else{
                       
                        $product_right_side_view_error_counter = $product_right_side_view_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    $product_right_side_view_filename = $this->provideProductIconWhenUnavailable($model);
                   $product_right_side_view_size = 0;
             
                }
                
                
                //This is the segment to capture and verify the product inside view image file
                
                 if($_FILES['product_inside_view']['name'] != ""){
                    if($this->isProductInsideViewTypeAndSizeLegal()){
                        
                       $product_inside_view_filename = $_FILES['product_inside_view']['name'];
                       $product_inside_view_size = $_FILES['product_inside_view']['size'];
                        
                    }else{
                       
                        $product_inside_view_error_counter = $product_inside_view_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    $product_inside_view_filename = $this->provideProductIconWhenUnavailable($model);
                   $product_inside_view_size = 0;
             
                }
                
                
                 //This is the segment to capture and verify the product top view image file
                
                 if($_FILES['product_top_view']['name'] != ""){
                    if($this->isProductTopViewTypeAndSizeLegal()){
                        
                       $product_top_view_filename = $_FILES['product_top_view']['name'];
                       $product_top_view_size = $_FILES['product_top_view']['size'];
                        
                    }else{
                       
                        $product_top_view_error_counter = $product_top_view_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    $product_top_view_filename = $this->provideProductIconWhenUnavailable($model);
                   $product_top_view_size = 0;
             
                }
                
                
                
                 //This is the segment to capture and verify the product engine view image file
                
                 if($_FILES['product_engine_view']['name'] != ""){
                    if($this->isProductEngineViewTypeAndSizeLegal()){
                        
                       $product_engine_view_filename = $_FILES['product_engine_view']['name'];
                       $product_engine_view_size = $_FILES['product_engine_view']['size'];
                        
                    }else{
                       
                        $product_engine_view_error_counter = $product_engine_view_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                   $product_engine_view_filename = $this->provideProductIconWhenUnavailable($model);
                   $product_engine_view_size = 0;
             
                }
                
                
                 //This is the segment to capture and verify the product back view image file
                
                 if($_FILES['product_back_view']['name'] != ""){
                    if($this->isProductBackViewTypeAndSizeLegal()){
                        
                       $product_back_view_filename = $_FILES['product_back_view']['name'];
                       $product_back_view_size = $_FILES['product_back_view']['size'];
                        
                    }else{
                       
                        $product_back_view_error_counter = $product_back_view_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                   $product_back_view_filename = $this->provideProductIconWhenUnavailable($model);
                   $product_back_view_size = 0;
             
                }
                
                
                //This is the segment to capture and verify the product left side view image file
                
                 if($_FILES['product_left_side_view']['name'] != ""){
                    if($this->isProductLeftSideViewTypeAndSizeLegal()){
                        
                       $product_left_side_view_filename = $_FILES['product_left_side_view']['name'];
                       $product_left_side_view_size = $_FILES['product_left_side_view']['size'];
                        
                    }else{
                       
                        $product_left_side_view_error_counter = $product_left_side_view_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                   $product_left_side_view_filename = $this->provideProductIconWhenUnavailable($model);
                   $product_left_side_view_size = 0;
             
                }
                
                
                //This is the segment to capture and verify the product bottom view image file
                
                 if($_FILES['product_bottom_view']['name'] != ""){
                    if($this->isProductBottomViewTypeAndSizeLegal()){
                        
                       $product_bottom_view_filename = $_FILES['product_bottom_view']['name'];
                       $product_bottom_view_size = $_FILES['product_bottom_view']['size'];
                        
                    }else{
                       
                        $product_bottom_view_error_counter = $product_bottom_view_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                   $product_bottom_view_filename = $this->provideProductIconWhenUnavailable($model);
                   $product_bottom_view_size = 0;
             
                }
                
                
                //This is the segment to capture and verify the product dashboard view image file
                
                 if($_FILES['product_dashboard_view']['name'] != ""){
                    if($this->isProductDashboardViewTypeAndSizeLegal()){
                        
                       $product_dashboard_view_filename = $_FILES['product_dashboard_view']['name'];
                       $product_dashboard_view_size = $_FILES['product_dashboard_view']['size'];
                        
                    }else{
                       
                        $product_dashboard_view_error_counter = $product_dashboard_view_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                   $product_dashboard_view_filename = $this->provideProductIconWhenUnavailable($model);
                   $product_dashboard_view_size = 0;
             
                }
                
                
                //This is the segment to capture and verify the product content and booth view image file
                
                 if($_FILES['product_contents_or_booth_view']['name'] != ""){
                    if($this->isProductContentsOrBoothsViewTypeAndSizeLegal()){
                        
                       $product_contents_or_booth_view_filename = $_FILES['product_contents_or_booth_view']['name'];
                       $product_contents_or_booth_view_size = $_FILES['product_contents_or_booth_view']['size'];
                        
                    }else{
                       
                        $product_contents_or_booth_view_error_counter = $product_contents_or_booth_view_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                   $product_contents_or_booth_view_filename = $this->provideProductIconWhenUnavailable($model);
                   $product_contents_or_booth_view_size = 0;
             
                }
                
                
                
                //end of the if icon is empty statement
                //Ensure that the files variables all validates
                if(($icon_error_counter ==0 && $poster_error_counter == 0 && $product_front_view_error_counter==0 && $product_right_side_view_error_counter==0 && $product_top_view_error_counter == 0 && $product_inside_view_error_counter==0 && $product_engine_view_error_counter==0 && $product_back_view_error_counter==0 && $product_left_side_view_error_counter == 0 && $product_bottom_view_error_counter == 0 && $product_dashboard_view_error_counter==0 && $product_contents_or_booth_view_error_counter==0)){
                    if($model->validate()){
                        $model->icon = $this->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                        $model->headline_image = $this->moveThePosterToItsPathAndReturnThePosterName($model,$poster_filename);
                        $model->product_front_view = $this->moveTheProductFrontViewImageToItsPathAndReturnTheItsName($model,$product_front_view_filename);
                        $model->product_right_side_view = $this->moveTheProductRightSideViewImageToItsPathAndReturnTheItsName($model,$product_right_side_view_filename);
                        $model->product_top_view = $this->moveTheProductTopViewImageToItsPathAndReturnTheItsName($model,$product_top_view_filename);
                        $model->product_inside_view = $this->moveTheProductInsideViewImageToItsPathAndReturnTheItsName($model,$product_inside_view_filename);
                        $model->product_engine_view = $this->moveTheProductEngineViewImageToItsPathAndReturnTheItsName($model,$product_engine_view_filename);
                        $model->product_back_view = $this->moveTheProductBackViewImageToItsPathAndReturnTheItsName($model,$product_back_view_filename);
                        $model->product_left_side_view = $this->moveTheProductLeftSideViewImageToItsPathAndReturnTheItsName($model,$product_left_side_view_filename);
                        $model->product_bottom_view = $this->moveTheProductBottomViewImageToItsPathAndReturnTheItsName($model,$product_bottom_view_filename);
                        $model->product_dashboard_view = $this->moveTheProductDashboardViewImageToItsPathAndReturnTheItsName($model,$product_dashboard_view_filename);
                        $model->product_contents_or_booth_view = $this->moveTheProductContentsAndBoothsViewImageToItsPathAndReturnTheItsName($model,$product_contents_or_booth_view_filename);
                       $model->icon_size = $icon_size;
                       $model->image_size = $poster_size; 
                       $model->product_front_view_size = $product_front_view_size; 
                       $model->product_right_side_view_size = $product_right_side_view_size; 
                       $model->product_top_view_size = $product_top_view_size;
                       $model->product_inside_view_size = $product_inside_view_size;
                       $model->product_engine_view_size = $product_engine_view_size;
                       $model->product_back_view_size = $product_back_view_size;
                       $model->product_left_side_view_size = $product_left_side_view_size;
                       $model->product_bottom_view_size = $product_bottom_view_size;
                       $model->product_dashboard_view_size = $product_dashboard_view_size;
                       $model->product_contents_or_booth_view_size = $product_contents_or_booth_view_size;
                        if($model->save()) {
                                //update the prices of the primary product(the pack)
                           $pack_prevailing_retail_price = $this->updateThePrevailingRetailPriceOfThePack($model->product_id,$_POST['quantity_of_product_in_the_pack'],$_POST['minimum_number_of_product_to_buy'],$_POST['prevailing_retail_selling_price']);
                           $member_selling_price =  $this->updateTheMemberOnlyPriceOfThePack($model->product_id,$_POST['quantity_of_product_in_the_pack'],$_POST['minimum_number_of_product_to_buy'],$_POST['per_portion_price']);
                                $msg = "'$model->name' product was added successful. The new pack prevailing retail price is =N=$pack_prevailing_retail_price and the new member only selling price is =N=$member_selling_price";
                                 header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() == 0,
                                  "msg" => $msg)
                            );
                         
                        }else{
                            //delete all the moved files in the directory when validation error is encountered
                            $msg = 'Validaion Error: Check your file fields for correctness';
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                               );
                          }
                            }else{
                                
                                //delete all the moved files in the directory when validation error is encountered
                            $msg = "Validation Error: '$model->name' Product  was not added successful";
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
                    $msg = "Please check your image file type or size as image must be at least of width '$platform_width'px and height '$platform_height'px. Images are of types '$icon_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );
                }else if($poster_error_counter >0){
                    //get the platform settings for this property
                    $platform_width = $this->getThePlatformSetPosterWidth();
                    $platform_height =$this->getThePlatformSetPosterHeight();
                    $poster_types = $this->retrieveAllThePosterMimeTypes();
                    $poster_types =  json_encode($poster_types);
                   $msg = "Please check your image file type or size as image must be of width '$platform_width'px and height '$platform_height'px.Images are  of types '$poster_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );
                }else if($product_front_view_error_counter>0){
                    
                     //get the platform assigned icon width and height
                  $platform_width = $this->getThePlatformSetIconWidth();
                  $platform_height = $this->getThePlatformSeticonHeight();
                  $icon_types = $this->retrieveAllTheIconMimeTypes();
                  $icon_types = json_encode($icon_types);
                    $msg = "Please check your images file type or size as images must be at least of width '$platform_width'px and height '$platform_height'px. Images are of types '$icon_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );
                }else if($product_right_side_view_error_counter>0){
                       //get the platform assigned icon width and height
                  $platform_width = $this->getThePlatformSetIconWidth();
                  $platform_height = $this->getThePlatformSeticonHeight();
                  $icon_types = $this->retrieveAllTheIconMimeTypes();
                  $icon_types = json_encode($icon_types);
                    $msg = "Please check your images file type or size as images must be at least of width '$platform_width'px and height '$platform_height'px. Images are of types '$icon_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );
                    
                }else if($product_top_view_error_counter>0){
                         //get the platform assigned icon width and height
                  $platform_width = $this->getThePlatformSetIconWidth();
                  $platform_height = $this->getThePlatformSeticonHeight();
                  $icon_types = $this->retrieveAllTheIconMimeTypes();
                  $icon_types = json_encode($icon_types);
                    $msg = "Please check your images file type or size as images must be at least of width '$platform_width'px and height '$platform_height'px. Images are of types '$icon_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );
                }else if($product_inside_view_error_counter>0){
                        //get the platform assigned icon width and height
                  $platform_width = $this->getThePlatformSetIconWidth();
                  $platform_height = $this->getThePlatformSeticonHeight();
                  $icon_types = $this->retrieveAllTheIconMimeTypes();
                  $icon_types = json_encode($icon_types);
                    $msg = "Please check your images file type or size as images must be at least of width '$platform_width'px and height '$platform_height'px. Images are of types '$icon_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );
                }else if($product_engine_view_error_counter>0){
                        //get the platform assigned icon width and height
                  $platform_width = $this->getThePlatformSetIconWidth();
                  $platform_height = $this->getThePlatformSeticonHeight();
                  $icon_types = $this->retrieveAllTheIconMimeTypes();
                  $icon_types = json_encode($icon_types);
                    $msg = "Please check your images file type or size as images must be at least of width '$platform_width'px and height '$platform_height'px. Images are of types '$icon_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );
                }else if($product_back_view_error_counter>0){
                      //get the platform assigned icon width and height
                  $platform_width = $this->getThePlatformSetIconWidth();
                  $platform_height = $this->getThePlatformSeticonHeight();
                  $icon_types = $this->retrieveAllTheIconMimeTypes();
                  $icon_types = json_encode($icon_types);
                    $msg = "Please check your images file type or size as images must be at least of width '$platform_width'px and height '$platform_height'px. Images are of types '$icon_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );
                }else if($product_left_side_view_error_counter>0){
                    //get the platform assigned icon width and height
                  $platform_width = $this->getThePlatformSetIconWidth();
                  $platform_height = $this->getThePlatformSeticonHeight();
                  $icon_types = $this->retrieveAllTheIconMimeTypes();
                  $icon_types = json_encode($icon_types);
                    $msg = "Please check your images file type or size as images must be at least of width '$platform_width'px and height '$platform_height'px. Images are of types '$icon_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );
                }else if($product_bottom_view_error_counter>0){
                   //get the platform assigned icon width and height
                  $platform_width = $this->getThePlatformSetIconWidth();
                  $platform_height = $this->getThePlatformSeticonHeight();
                  $icon_types = $this->retrieveAllTheIconMimeTypes();
                  $icon_types = json_encode($icon_types);
                    $msg = "Please check your images file type or size as images must be at least of width '$platform_width'px and height '$platform_height'px. Images are of types '$icon_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );  
                }else if($product_dashboard_view_error_counter>0){
                    //get the platform assigned icon width and height
                  $platform_width = $this->getThePlatformSetIconWidth();
                  $platform_height = $this->getThePlatformSeticonHeight();
                  $icon_types = $this->retrieveAllTheIconMimeTypes();
                  $icon_types = json_encode($icon_types);
                    $msg = "Please check your images file type or size as images must be at least of width '$platform_width'px and height '$platform_height'px. Images are of types '$icon_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );  
                }else if($product_contents_or_booth_view_error_counter>0){
                    //get the platform assigned icon width and height
                  $platform_width = $this->getThePlatformSetIconWidth();
                  $platform_height = $this->getThePlatformSeticonHeight();
                  $icon_types = $this->retrieveAllTheIconMimeTypes();
                  $icon_types = json_encode($icon_types);
                    $msg = "Please check your images file type or size as images must be at least of width '$platform_width'px and height '$platform_height'px. Images are of types '$icon_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );  
                }  
            
            
        }
        
        
        /**
         * This is the function that generates the cumulative selling pricr
         */
        public function generateTheCumulativeSellingPrice($per_portion_price, $maximum_portion){
            $total_selling_price = $per_portion_price * $maximum_portion;
            return $total_selling_price;
        }
        
        
        /**
         * This is the function that calculates the applicable discount rate
         */
        public function generateTheDiscountRate($prevailing_retail_selling_price,$per_portion_price){
            
            $rate = (($prevailing_retail_selling_price - $per_portion_price)/$prevailing_retail_selling_price)*100;
            return $rate;
        }
        
        
        
        
           /**
         * This is the function that updates a product constituent
         */
        public function actionupdateProductConstituent(){
            
            //get the logged in user id
            $userid = Yii::app()->user->id;
            
             $_id = $_POST['id'];
            $model=  ProductConstituents::model()->findByPk($_id);
            $model->name = $_POST['name'];
            if(isset($_POST['description'])){
                $model->description = $_POST['description'];
            }
            if(is_numeric($_POST['product'])){
                $model->product_id = $_POST['product'];
            }else{
                $model->product_id = $_POST['product_id'];
            }
            if(isset($_POST['condition'])){
                $model->condition = $_POST['condition'];
            }
             if(isset($_POST['specifications'])){
               $model->specifications = $_POST['specifications'];
           }
            if(isset($_POST['prevailing_retail_selling_price'])){
                 $model->prevailing_retail_selling_price = $_POST['prevailing_retail_selling_price'];
            }
            if(isset($_POST['per_portion_price'])){
               $model->per_portion_price = $_POST['per_portion_price'];
           }
           // $model->cumulative_selling_price = $this->generateTheCumulativeSellingPrice($model->per_portion_price, $model->maximum_portion);
           if(isset($_POST['feature'])){
               $model->feature = $_POST['feature'];
           } 
           if(isset($_POST['minimum_number_of_product_to_buy'])){
                $model->minimum_number_of_product_to_buy = $_POST['minimum_number_of_product_to_buy'];   
           }
           if(isset($_POST['whats_in_a_park'])){
              $model->whats_in_a_park = $_POST['whats_in_a_park']; 
           } 
            if(isset($_POST['whats_product_per_item'])){
                $model->whats_product_per_item = $_POST['whats_product_per_item'];
            }
             if(isset($_POST['price_validity_period'])){
                $model->price_validity_period = $_POST['price_validity_period'];
            }
           if(isset($_POST['quantity'])){
              $model->quantity = $_POST['quantity']; 
           } 
           if(isset($_POST['quantity_of_product_in_the_pack'])){
              $model->quantity_of_product_in_the_pack = $_POST['quantity_of_product_in_the_pack']; 
           } 
          //  $model->discount_rate = $this->generateTheDiscountRate($model->prevailing_retail_selling_price,$model->per_portion_price);
        
            if(isset($_POST['displayable_on_store'])){
                $model->displayable_on_store = $_POST['displayable_on_store'];
            }else{
                $model->displayable_on_store = 0;
            }
             if(isset($_POST['brand'])){
              $model->brand = $_POST['brand']; 
           } 
           if(isset($_POST['maker'])){
              $model->maker = $_POST['maker']; 
           }
           if(isset($_POST['origin'])){
              $model->origin = $_POST['origin']; 
           }
           if(isset($_POST['start_price_validity_period'])){
              $model->start_price_validity_period = date("Y-m-d H:i:s", strtotime($_POST['start_price_validity_period']));
           }
            if(isset($_POST['end_price_validity_period'])){
              $model->end_price_validity_period = date("Y-m-d H:i:s", strtotime($_POST['end_price_validity_period']));
           }
           $model->update_user_id = $userid;
            $model->update_time = new CDbExpression('NOW()');
                
            //get the constituent's name
            $product_name = $this->getThisConstituentName($_id);
                 //declare a universal error message variable
            //declare a universal error message variable
               $icon_error_counter = 0;
               $poster_error_counter =0;
               $product_front_view_error_counter =0;
               $product_right_side_view_error_counter =0;
               $product_top_view_error_counter =0;
               $product_inside_view_error_counter =0;
               $product_engine_view_error_counter =0;
               $product_back_view_error_counter =0;
               $product_left_side_view_error_counter =0;
               $product_bottom_view_error_counter =0;
               $product_dashboard_view_error_counter =0;
               $product_contents_or_booth_view_error_counter =0;
               
                //working with the icon file
                if($_FILES['icon']['name'] != ""){
                    if($this->isIconTypeAndSizeLegal()){
                        
                       $icon_filename = $_FILES['icon']['name'];
                       $icon_size = $_FILES['icon']['size'];
                        
                    }else{
                       
                        $icon_error_counter = $icon_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    $icon_filename = $this->retrieveThePreviousIconName($_id);
                    $icon_size = $this->retrieveThePreviousIconSize($_id);
             
                }//end of the if icon is empty statement
                
                //Working with the poster file
                 if($_FILES['headline_image']['name'] != ""){
                    if($this->isPosterTypeAndSizeLegal()){
                        
                       $image_filename = $_FILES['headline_image']['name'];
                       $image_size = $_FILES['headline_image']['size'];
                        
                    }else{
                        $poster_error_counter = $poster_error_counter + 1;
                        
                    }//end of the determine size and type statement
                }else{
                    $image_filename = $this->retrieveThePreviousPosterName($_id);
                    $image_size = $this->retrieveThePreviousPosterSize($_id);
                }//end of the if icon is empty statement
             
                
                //This is the segment to capture and verify the product front view image file
                if($_FILES['product_front_view']['name'] != ""){
                    if($this->isProductFronViewTypeAndSizeLegal()){
                        
                       $product_front_view_filename = $_FILES['product_front_view']['name'];
                       $product_front_view_size = $_FILES['product_front_view']['size'];
                        
                    }else{
                       
                        $product_front_view_error_counter = $product_front_view_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                   $product_front_view_filename = $this->retrieveThePreviousProductFrontViewImageName($_id);
                   $product_front_view_size = $this->retrieveThePreviousProductFrontViewImageSize($_id);
             
                }
                
                
                //This is the segment to capture and verify the product right side view image file
                
                 if($_FILES['product_right_side_view']['name'] != ""){
                    if($this->isProductRightSizeViewTypeAndSizeLegal()){
                        
                       $product_right_side_view_filename = $_FILES['product_right_side_view']['name'];
                       $product_right_side_view_size = $_FILES['product_right_side_view']['size'];
                        
                    }else{
                       
                        $product_right_side_view_error_counter = $product_right_side_view_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                   $product_right_side_view_filename = $this->retrieveThePreviousProductRightSizeViewImageName($_id);
                   $product_right_side_view_size = $this->retrieveThePreviousProductRightSizeViewImageSize($_id);
             
                }
                
                
                //This is the segment to capture and verify the product inside view image file
                
                 if($_FILES['product_inside_view']['name'] != ""){
                    if($this->isProductInsideViewTypeAndSizeLegal()){
                        
                       $product_inside_view_filename = $_FILES['product_inside_view']['name'];
                       $product_inside_view_size = $_FILES['product_inside_view']['size'];
                        
                    }else{
                       
                        $product_inside_view_error_counter = $product_inside_view_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    $product_inside_view_filename = $this->retrieveThePreviousProductInsideViewImageName($_id);
                    $product_inside_view_size = $this->retrieveThePreviousProductInsideViewImageSize($_id);
             
                }
                
                
                 //This is the segment to capture and verify the product top view image file
                
                 if($_FILES['product_top_view']['name'] != ""){
                    if($this->isProductTopViewTypeAndSizeLegal()){
                        
                       $product_top_view_filename = $_FILES['product_top_view']['name'];
                       $product_top_view_size = $_FILES['product_top_view']['size'];
                        
                    }else{
                       
                        $product_top_view_error_counter = $product_top_view_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    $product_top_view_filename = $this->retrieveThePreviousProductTopViewImageName($_id);
                    $product_top_view_size = $this->retrieveThePreviousProductTopViewImageSize($_id);
             
                }
                
                
                
                 //This is the segment to capture and verify the product engine view image file
                
                 if($_FILES['product_engine_view']['name'] != ""){
                    if($this->isProductEngineViewTypeAndSizeLegal()){
                        
                       $product_engine_view_filename = $_FILES['product_engine_view']['name'];
                       $product_engine_view_size = $_FILES['product_engine_view']['size'];
                        
                    }else{
                       
                        $product_engine_view_error_counter = $product_engine_view_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                   $product_engine_view_filename = $this->retrieveThePreviousProductEngineViewImageName($_id);
                    $product_engine_view_size = $this->retrieveThePreviousProductEngineViewImageSize($_id);
             
                }
                
                
                 //This is the segment to capture and verify the product back view image file
                
                 if($_FILES['product_back_view']['name'] != ""){
                    if($this->isProductBackViewTypeAndSizeLegal()){
                        
                       $product_back_view_filename = $_FILES['product_back_view']['name'];
                       $product_back_view_size = $_FILES['product_back_view']['size'];
                        
                    }else{
                       
                        $product_back_view_error_counter = $product_back_view_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    $product_back_view_filename = $this->retrieveThePreviousProductBackViewImageName($_id);
                    $product_back_view_size = $this->retrieveThePreviousProductBackViewImageSize($_id);
             
                }
                
                
                //This is the segment to capture and verify the product left side view image file
                
                 if($_FILES['product_left_side_view']['name'] != ""){
                    if($this->isProductLeftSideViewTypeAndSizeLegal()){
                        
                       $product_left_side_view_filename = $_FILES['product_left_side_view']['name'];
                       $product_left_side_view_size = $_FILES['product_left_side_view']['size'];
                        
                    }else{
                       
                        $product_left_side_view_error_counter = $product_left_side_view_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    $product_left_side_view_filename = $this->retrieveThePreviousProductLeftSideViewImageName($_id);
                    $product_left_side_view_size = $this->retrieveThePreviousProductLeftSideViewImageSize($_id);
             
                }
                
                
                //This is the segment to capture and verify the product bottom view image file
                
                 if($_FILES['product_bottom_view']['name'] != ""){
                    if($this->isProductBottomViewTypeAndSizeLegal()){
                        
                       $product_bottom_view_filename = $_FILES['product_bottom_view']['name'];
                       $product_bottom_view_size = $_FILES['product_bottom_view']['size'];
                        
                    }else{
                       
                        $product_bottom_view_error_counter = $product_bottom_view_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    $product_bottom_view_filename = $this->retrieveThePreviousProductBottomViewImageName($_id);
                    $product_bottom_view_size = $this->retrieveThePreviousProductBottomViewImageSize($_id);
             
                }
                
                
                //This is the segment to capture and verify the product dashboard view image file
                
                 if($_FILES['product_dashboard_view']['name'] != ""){
                    if($this->isProductDashboardViewTypeAndSizeLegal()){
                        
                       $product_dashboard_view_filename = $_FILES['product_dashboard_view']['name'];
                       $product_dashboard_view_size = $_FILES['product_dashboard_view']['size'];
                        
                    }else{
                       
                        $product_dashboard_view_error_counter = $product_dashboard_view_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                    $product_dashboard_view_filename = $this->retrieveThePreviousProductDashboardViewImageName($_id);
                    $product_dashboard_view_size = $this->retrieveThePreviousProductDashboardViewImageSize($_id);
             
                }
                
                
                //This is the segment to capture and verify the product content and booth view image file
                
                 if($_FILES['product_contents_or_booth_view']['name'] != ""){
                    if($this->isProductContentsOrBoothsViewTypeAndSizeLegal()){
                        
                       $product_contents_or_booth_view_filename = $_FILES['product_contents_or_booth_view']['name'];
                       $product_contents_or_booth_view_size = $_FILES['product_contents_or_booth_view']['size'];
                        
                    }else{
                       
                        $product_contents_or_booth_view_error_counter = $product_contents_or_booth_view_error_counter + 1;
                         
                    }//end of the determine size and type statement
                }else{
                     $product_contents_or_booth_view_filename = $this->retrieveThePreviousProductContentOrBoothViewImageName($_id);
                    $product_contents_or_booth_view_size = $this->retrieveThePreviousProductContentOrBoothViewImageSize($_id);
             
                }
                
                //Ensure that the files variables all validates
                 if(($icon_error_counter ==0 && $poster_error_counter == 0 && $product_front_view_error_counter==0 && $product_right_side_view_error_counter==0 && $product_top_view_error_counter == 0 && $product_inside_view_error_counter==0 && $product_engine_view_error_counter==0 && $product_back_view_error_counter==0 && $product_left_side_view_error_counter == 0 && $product_bottom_view_error_counter == 0 && $product_dashboard_view_error_counter==0 && $product_contents_or_booth_view_error_counter==0)){
                    if($model->validate()){
                        $model->icon = $this->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                        $model->headline_image = $this->moveThePosterToItsPathAndReturnThePosterName($model,$image_filename);
                        $model->product_front_view = $this->moveTheProductFrontViewImageToItsPathAndReturnTheItsName($model,$product_front_view_filename);
                        $model->product_right_side_view = $this->moveTheProductRightSideViewImageToItsPathAndReturnTheItsName($model,$product_right_side_view_filename);
                        $model->product_top_view = $this->moveTheProductTopViewImageToItsPathAndReturnTheItsName($model,$product_top_view_filename);
                        $model->product_inside_view = $this->moveTheProductInsideViewImageToItsPathAndReturnTheItsName($model,$product_inside_view_filename);
                        $model->product_engine_view = $this->moveTheProductEngineViewImageToItsPathAndReturnTheItsName($model,$product_engine_view_filename);
                        $model->product_back_view = $this->moveTheProductBackViewImageToItsPathAndReturnTheItsName($model,$product_back_view_filename);
                        $model->product_left_side_view = $this->moveTheProductLeftSideViewImageToItsPathAndReturnTheItsName($model,$product_left_side_view_filename);
                        $model->product_bottom_view = $this->moveTheProductBottomViewImageToItsPathAndReturnTheItsName($model,$product_bottom_view_filename);
                        $model->product_dashboard_view = $this->moveTheProductDashboardViewImageToItsPathAndReturnTheItsName($model,$product_dashboard_view_filename);
                        $model->product_contents_or_booth_view = $this->moveTheProductContentsAndBoothsViewImageToItsPathAndReturnTheItsName($model,$product_contents_or_booth_view_filename);
                       $model->icon_size = $icon_size;
                       $model->image_size = $image_size; 
                       $model->product_front_view_size = $product_front_view_size; 
                       $model->product_right_side_view_size = $product_right_side_view_size; 
                       $model->product_top_view_size = $product_top_view_size;
                       $model->product_inside_view_size = $product_inside_view_size;
                       $model->product_engine_view_size = $product_engine_view_size;
                       $model->product_back_view_size = $product_back_view_size;
                       $model->product_left_side_view_size = $product_left_side_view_size;
                       $model->product_bottom_view_size = $product_bottom_view_size;
                       $model->product_dashboard_view_size = $product_dashboard_view_size;
                       $model->product_contents_or_booth_view_size = $product_contents_or_booth_view_size;
                        if($model->save()) {
                           //update the prices of the primary product(the pack)
                           $pack_prevailing_retail_price = $this->updateThePrevailingRetailPriceOfThePack($model->product_id,$_POST['quantity_of_product_in_the_pack'],$_POST['minimum_number_of_product_to_buy'],$_POST['prevailing_retail_selling_price']);
                           $member_selling_price =  $this->updateTheMemberOnlyPriceOfThePack($model->product_id,$_POST['quantity_of_product_in_the_pack'],$_POST['minimum_number_of_product_to_buy'],$_POST['per_portion_price']);
                                $msg = "'$model->name' product was added successful. The current pack prevailing retail price is =N=$pack_prevailing_retail_price and the Member only price is =N=$member_selling_price";
                                 header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() == 0,
                                  "msg" => $msg,
                                    "retail_price"=>$pack_prevailing_retail_price,
                                    "member_price"=>$member_selling_price)
                            );
                         
                        }else{
                            //delete all the moved files in the directory when validation error is encountered
                            $msg = 'Validaion Error: Check your file fields for correctness';
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                               );
                          }
                            }else{
                                
                                //delete all the moved files in the directory when validation error is encountered
                            $msg = "Validation Error: '$model->name' Product  was not added successful";
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
                    $msg = "Please check your image file type or size as image must be at least of width '$platform_width'px and height '$platform_height'px. Images are of types '$icon_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );
                }else if($poster_error_counter >0){
                    //get the platform settings for this property
                    $platform_width = $this->getThePlatformSetPosterWidth();
                    $platform_height =$this->getThePlatformSetPosterHeight();
                    $poster_types = $this->retrieveAllThePosterMimeTypes();
                    $poster_types =  json_encode($poster_types);
                   $msg = "Please check your image file type or size as image must be of width '$platform_width'px and height '$platform_height'px.Images are  of types '$poster_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );
                }else if($product_front_view_error_counter>0){
                    
                     //get the platform assigned icon width and height
                  $platform_width = $this->getThePlatformSetIconWidth();
                  $platform_height = $this->getThePlatformSeticonHeight();
                  $icon_types = $this->retrieveAllTheIconMimeTypes();
                  $icon_types = json_encode($icon_types);
                    $msg = "Please check your images file type or size as images must be at least of width '$platform_width'px and height '$platform_height'px. Images are of types '$icon_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );
                }else if($product_right_side_view_error_counter>0){
                       //get the platform assigned icon width and height
                  $platform_width = $this->getThePlatformSetIconWidth();
                  $platform_height = $this->getThePlatformSeticonHeight();
                  $icon_types = $this->retrieveAllTheIconMimeTypes();
                  $icon_types = json_encode($icon_types);
                    $msg = "Please check your images file type or size as images must be at least of width '$platform_width'px and height '$platform_height'px. Images are of types '$icon_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );
                    
                }else if($product_top_view_error_counter>0){
                         //get the platform assigned icon width and height
                  $platform_width = $this->getThePlatformSetIconWidth();
                  $platform_height = $this->getThePlatformSeticonHeight();
                  $icon_types = $this->retrieveAllTheIconMimeTypes();
                  $icon_types = json_encode($icon_types);
                    $msg = "Please check your images file type or size as images must be at least of width '$platform_width'px and height '$platform_height'px. Images are of types '$icon_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );
                }else if($product_inside_view_error_counter>0){
                        //get the platform assigned icon width and height
                  $platform_width = $this->getThePlatformSetIconWidth();
                  $platform_height = $this->getThePlatformSeticonHeight();
                  $icon_types = $this->retrieveAllTheIconMimeTypes();
                  $icon_types = json_encode($icon_types);
                    $msg = "Please check your images file type or size as images must be at least of width '$platform_width'px and height '$platform_height'px. Images are of types '$icon_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );
                }else if($product_engine_view_error_counter>0){
                        //get the platform assigned icon width and height
                  $platform_width = $this->getThePlatformSetIconWidth();
                  $platform_height = $this->getThePlatformSeticonHeight();
                  $icon_types = $this->retrieveAllTheIconMimeTypes();
                  $icon_types = json_encode($icon_types);
                    $msg = "Please check your images file type or size as images must be at least of width '$platform_width'px and height '$platform_height'px. Images are of types '$icon_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );
                }else if($product_back_view_error_counter>0){
                      //get the platform assigned icon width and height
                  $platform_width = $this->getThePlatformSetIconWidth();
                  $platform_height = $this->getThePlatformSeticonHeight();
                  $icon_types = $this->retrieveAllTheIconMimeTypes();
                  $icon_types = json_encode($icon_types);
                    $msg = "Please check your images file type or size as images must be at least of width '$platform_width'px and height '$platform_height'px. Images are of types '$icon_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );
                }else if($product_left_side_view_error_counter>0){
                    //get the platform assigned icon width and height
                  $platform_width = $this->getThePlatformSetIconWidth();
                  $platform_height = $this->getThePlatformSeticonHeight();
                  $icon_types = $this->retrieveAllTheIconMimeTypes();
                  $icon_types = json_encode($icon_types);
                    $msg = "Please check your images file type or size as images must be at least of width '$platform_width'px and height '$platform_height'px. Images are of types '$icon_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );
                }else if($product_bottom_view_error_counter>0){
                   //get the platform assigned icon width and height
                  $platform_width = $this->getThePlatformSetIconWidth();
                  $platform_height = $this->getThePlatformSeticonHeight();
                  $icon_types = $this->retrieveAllTheIconMimeTypes();
                  $icon_types = json_encode($icon_types);
                    $msg = "Please check your images file type or size as images must be at least of width '$platform_width'px and height '$platform_height'px. Images are of types '$icon_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );  
                }else if($product_dashboard_view_error_counter>0){
                    //get the platform assigned icon width and height
                  $platform_width = $this->getThePlatformSetIconWidth();
                  $platform_height = $this->getThePlatformSeticonHeight();
                  $icon_types = $this->retrieveAllTheIconMimeTypes();
                  $icon_types = json_encode($icon_types);
                    $msg = "Please check your images file type or size as images must be at least of width '$platform_width'px and height '$platform_height'px. Images are of types '$icon_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );  
                }else if($product_contents_or_booth_view_error_counter>0){
                    //get the platform assigned icon width and height
                  $platform_width = $this->getThePlatformSetIconWidth();
                  $platform_height = $this->getThePlatformSeticonHeight();
                  $icon_types = $this->retrieveAllTheIconMimeTypes();
                  $icon_types = json_encode($icon_types);
                    $msg = "Please check your images file type or size as images must be at least of width '$platform_width'px and height '$platform_height'px. Images are of types '$icon_types'";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );  
                }
            
            
            
        }
        
        
        /**
         * This is the function that updates the prevailing retail price of the pack
         */
        public function updateThePrevailingRetailPriceOfThePack($product_id,$quantity_of_product_in_the_pack,$minimum_number_of_product_to_buy,$prevailing_retail_selling_price){
            $model = new Product;
            return $model->updateThePrevailingRetailPriceOfThePack($product_id,$quantity_of_product_in_the_pack,$minimum_number_of_product_to_buy,$prevailing_retail_selling_price);
        }
        
        
         /**
         * This is the function that updates the member only price of the pack
         */
        public function updateTheMemberOnlyPriceOfThePack($product_id,$quantity_of_product_in_the_pack,$minimum_number_of_product_to_buy,$per_portion_price){
            $model = new Product;
            return $model->updateTheMemberOnlyPriceOfThePack($product_id,$quantity_of_product_in_the_pack,$minimum_number_of_product_to_buy,$per_portion_price);
        }
        
        
         /**
         * This is the function that determines the type and size of icon file
         */
        public function isIconTypeAndSizeLegal(){
            
           $size = []; 
            if(isset($_FILES['icon']['name'])){
                $tmpName = $_FILES['icon']['tmp_name'];
                $iconFileName = $_FILES['icon']['name'];    
                $iconFileType = $_FILES['icon']['type'];
                $iconFileSize = $_FILES['icon']['size'];
            } 
           if (isset($_FILES['icon'])) {
             $filename = $_FILES['icon']['tmp_name'];
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
            $icon = ProductConstituents::model()->find($criteria);
            
            
            return $icon['icon'];
            
            
        }
        
        /**
         * This is the function that retrieves the previous icon size
         */
        public function retrieveThePreviousIconSize($id){
           
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = ProductConstituents::model()->find($criteria);
            
            
            return $icon['icon_size'];
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
            
            if(isset($_FILES['icon']['name'])){
                        $tmpName = $_FILES['icon']['tmp_name'];
                        $iconName = $_FILES['icon']['name'];    
                        $iconType = $_FILES['icon']['type'];
                        $iconSize = $_FILES['icon']['size'];
                  
                   }
                    
                    if($iconName !== null) {
                        if($model->id === null){
                          //$iconFileName = $icon_filename;  
                          if($icon_filename != 'product_unavailable.png'){
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
                             if($icon_filename != 'product_unavailable.png'){
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
                $icon= ProductConstituents::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "c:\\xampp\htdocs\cobuy_images\\icons\\";
               // $iconpath = '..\appspace_assets\icons'.$icon['icon'];
                $filepath =$directoryPath.$icon['icon'];
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
                $icon= ProductConstituents::model()->find($criteria);
                
                if($icon['icon'] == 'product_unavailable.png' || $icon['icon'] ===NULL){
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
                $criteria->select = 'id, icon';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                if($icon['icon']==$icon_filename){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        
        /**
	 * Provide icon when unavailable
	 */
	public function provideProductIconWhenUnavailable($model)
	{
		return 'product_unavailable.png';
	}
        
        
        
        /**
	 * Provide icon when unavailable
	 */
	public function provideProductImageWhenUnavailable($model)
	{
		return 'product_header_unavailable.png';
	}
        
    
         /**
         * This is the function that determines the type and size of poster file
         */
        public function isPosterTypeAndSizeLegal(){
          
            if(isset($_FILES['headline_image']['name'])){
                $tmpName = $_FILES['headline_image']['tmp_name'];
                $posterFileName = $_FILES['headline_image']['name'];    
                $posterFileType = $_FILES['headline_image']['type'];
                $posterFileSize = $_FILES['headline_image']['size'];
            } 
            //obtain the poster sizes for this domain
           if (isset($_FILES['headline_image'])) {
             $filename = $_FILES['headline_image']['tmp_name'];
             list($width, $height) = getimagesize($filename);
           }
            
            $platform_width = $this->getThePlatformSetPosterWidth();
            $platform_height = $this->getThePlatformSetPosterHeight();
            
            $width = $width;
            $height =  $height;
            
           // $max_poster_size = $this->retrieveTheMaxPosterSizeForThisDomain($domainid);
            $postertypes = $this->retrieveAllThePosterMimeTypes();
            
                       
           // if(($posterFileType === 'image/png'|| $posterFileType === 'image/jpg' || $posterFileType === 'image/jpeg') && ($posterFileSize <= 256 * 256 * 2)){
           if(in_array($posterFileType,$postertypes) && ($platform_width<=$width && $platform_height<=$height)){
                return true;
            }else{
                return false;
            }
            
        }
        
        
         /**
         * This is the function that retrieves the previous poster of the task in question
         */
        public function retrieveThePreviousPosterName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $poster = ProductConstituents::model()->find($criteria);
            
            
            return $poster['headline_image'];
            
            
            
        }
        
        /**
         * This is the function that returns the previous poster size
         */
        public function retrieveThePreviousPosterSize($id){
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $poster = ProductConstituents::model()->find($criteria);
            
            
            return $poster['image_size'];
            
        }
        
        
         /**
         * This is the function that gets the platform height setting
         */
        public function getThePlatformSetPosterHeight(){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
           // $criteria->condition='id=:id';
           // $criteria->params = array(':id'=>$id);
            $poster = PlatformSettings::model()->find($criteria); 
            
            return $poster['poster_height'];
        }
        
        
         /**
         * This is the function that gets the platform poster set width
         */
        public function getThePlatformSetPosterWidth(){
            
           $criteria = new CDbCriteria();
            $criteria->select = '*';
           // $criteria->condition='id=:id';
           // $criteria->params = array(':id'=>$id);
            $poster = PlatformSettings::model()->find($criteria); 
            
            return $poster['poster_width'];
        }
        
        
        /**
         * This is the function that retrieves all poster mime types in the platform
         */
        public function retrieveAllThePosterMimeTypes(){
           
             //retrieve the poster mime types
            $poster_mimetype = [];
            $poster_types = [];
            $criteria = new CDbCriteria();
            $criteria->select = '*';
           // $criteria->condition='id=:id';
           // $criteria->params = array(':id'=>$id);
            $poster_mime = PlatformSettings::model()->find($criteria); 
            
            $poster_mimetype = explode(',',$poster_mime['poster_mime_type']);
            foreach($poster_mimetype as $poster){
                $poster_types[] =$poster; 
                
            }
            
            return $poster_types;
            
        }
        
        
        
        /**
         * This is the function that moves poster files to its directory
         */
        public function moveThePosterToItsPathAndReturnThePosterName($model,$poster_filename){
            
            if(isset($_FILES['headline_image']['name'])){
                        $tmpName = $_FILES['headline_image']['tmp_name'];
                        $posterName = $_FILES['headline_image']['name'];    
                        $posterType = $_FILES['headline_image']['type'];
                        $posterSize = $_FILES['headline_image']['size'];
                  
                    }
                    
                    if( $posterName !== null) {
                      if($model->id === null){
                          //$posterFileName = $poster_filename;
                          if($poster_filename != 'product_header_unavailable.png'){
                                $posterFileName = time().'_'.$poster_filename;  
                            }else{
                                $posterFileName= $poster_filename;  
                            }
                          
                           // upload the poster file
                        if( $posterName !== null){
                            	$posterPath = Yii::app()->params['posters'].$posterFileName;
				move_uploaded_file($tmpName,  $posterPath);
                                        
                        
                                return $posterFileName;
                        }else{
                            
                            return $poster_filename;
                            
                            
                        } // validate to save file
                      }else{
                          if($this->noNewPosterFileProvided($model->id,$poster_filename)){
                                $posterFileName = $poster_filename;
                                return $posterFileName;
                            }else{
                              if($poster_filename != 'product_header_unavailable.png'){
                                   if($this->removeTheExistingPosterFile($model->id)){
                                 
                                     $posterFileName = time().'_'.$poster_filename;
                                     $posterPath = Yii::app()->params['posters'].$posterFileName;
                                     move_uploaded_file($tmpName,  $posterPath);
                                     return $posterFileName;
                               }
                                
                              }
                               
                                
                               //return $poster_filename;
                            }
                       
                      }
                        
                     }else{
                         $posterFileName = $poster_filename;
                          return $posterFileName;
                     }
					
                       
                               
        }
        
        
        /**
         * This is the function to ascertain if a new poster was provided or not
         */
        public function noNewPosterFileProvided($id,$poster_filename){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $poster= ProductConstituents::model()->find($criteria);
                
                if($poster['headline_image']==$poster_filename){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
         /**
         * This is the function that removes an existing video file
         */
        public function removeTheExistingPosterFile($id){
            
            //retreve the existing zip file from the database
            if($this->isThePosterNotTheDefault($id)){
                
                 $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $poster= ProductConstituents::model()->find($criteria);
                
               // $directoryPath =  dirname(Yii::app()->request->scriptFile);
                $directoryPath = "c:\\xampp\htdocs\cobuy_images\\posters\\";
                //$posterpath = '..\appspace_assets\posters'.$poster['poster'];
               $filepath =  $directoryPath.$poster['headline_image'];
               // $filepath = $directoryPath.$posterpath;
                
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
        public function isThePosterNotTheDefault($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                if($icon['headline_image'] == 'product_header_unavailable.png' || $icon['headline_image'] ===NULL){
                    return false;
                }else{
                    return true;
                }
        }
        
        
        /**
         * This is the section that reads and moves product front view images
         */
        	
        /**
         * This is the function that read and relocate the product front view file to its directory
         */
        public function moveTheProductFrontViewImageToItsPathAndReturnTheItsName($model,$icon_filename){
            
            if(isset($_FILES['product_front_view']['name'])){
                        $tmpName = $_FILES['product_front_view']['tmp_name'];
                        $iconName = $_FILES['product_front_view']['name'];    
                        $iconType = $_FILES['product_front_view']['type'];
                        $iconSize = $_FILES['product_front_view']['size'];
                  
                   }
                    
                    if($iconName !== null) {
                        if($model->id === null){
                          //$iconFileName = $icon_filename;  
                          if($icon_filename != 'product_unavailable.png'){
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
                            if($this->noNewProductFrontViewFileProvided($model->id,$icon_filename)){
                                $iconFileName = $icon_filename; 
                                return $iconFileName;
                            }else{
                             if($icon_filename != 'product_unavailable.png'){
                                 if($this->removeTheExistingProductFrontViewFile($model->id)){
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
         * This is the function to ascertain if a new product front view image was provided or not
         */
        public function noNewProductFrontViewFileProvided($id,$icon_filename){
            
                $criteria = new CDbCriteria();
                $criteria->select = 'id, product_front_view';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                if($icon['product_front_view']==$icon_filename){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        	 /**
         * This is the function that removes an existing product front view file
         */
        public function removeTheExistingProductFrontViewFile($id){
            
            //retreve the existing product front view file from the database
            
            if($this->isTheProductFrontViewNotTheDefault($id)){
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "c:\\xampp\htdocs\cobuy_images\\icons\\";
               // $iconpath = '..\appspace_assets\icons'.$icon['icon'];
                $filepath =$directoryPath.$icon['product_front_view'];
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
         * This is the function that determines if  a product front view image  is the default
         */
        public function isTheProductFrontViewNotTheDefault($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                if($icon['product_front_view'] == 'product_unavailable.png' || $icon['product_front_view'] ===NULL){
                    return false;
                }else{
                    return true;
                }
        }
        
        
        
        
         /**
         * This is the section that reads and moves product right side view images
         */
        	
        /**
         * This is the function that read and relocate the product right side  view file to its directory
         */
        public function moveTheProductRightSideViewImageToItsPathAndReturnTheItsName($model,$icon_filename){
            
            if(isset($_FILES['product_right_side_view']['name'])){
                        $tmpName = $_FILES['product_right_side_view']['tmp_name'];
                        $iconName = $_FILES['product_right_side_view']['name'];    
                        $iconType = $_FILES['product_right_side_view']['type'];
                        $iconSize = $_FILES['product_right_side_view']['size'];
                  
                   }
                    
                    if($iconName !== null) {
                        if($model->id === null){
                          //$iconFileName = $icon_filename;  
                          if($icon_filename != 'product_unavailable.png'){
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
                            if($this->noNewProductRightSideViewFileProvided($model->id,$icon_filename)){
                                $iconFileName = $icon_filename; 
                                return $iconFileName;
                            }else{
                             if($icon_filename != 'product_unavailable.png'){
                                 if($this->removeTheExistingProductRightSideViewFile($model->id)){
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
         * This is the function to ascertain if a new product right side view image was provided or not
         */
        public function noNewProductRightSideViewFileProvided($id,$icon_filename){
            
                $criteria = new CDbCriteria();
                $criteria->select = 'id, product_right_side_view';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                if($icon['product_right_side_view']==$icon_filename){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        	 /**
         * This is the function that removes an existing product right side view file
         */
        public function removeTheExistingProductRightSideViewFile($id){
            
            //retreve the existing product right side view file from the database
            
            if($this->isTheProductRightSideViewNotTheDefault($id)){
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "c:\\xampp\htdocs\cobuy_images\\icons\\";
               // $iconpath = '..\appspace_assets\icons'.$icon['icon'];
                $filepath =$directoryPath.$icon['product_right_side_view'];
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
         * This is the function that determines if  a product right side view image  is the default
         */
        public function isTheProductRightSideViewNotTheDefault($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                if($icon['product_right_side_view'] == 'product_unavailable.png' || $icon['product_right_side_view'] ===NULL){
                    return false;
                }else{
                    return true;
                }
        }
        
        
        
        
        /**
         * This is the section that reads and moves product top view images
         */
        	
        /**
         * This is the function that read and relocate the product top view file to its directory
         */
        public function moveTheProductTopViewImageToItsPathAndReturnTheItsName($model,$icon_filename){
            
            if(isset($_FILES['product_top_view']['name'])){
                        $tmpName = $_FILES['product_top_view']['tmp_name'];
                        $iconName = $_FILES['product_top_view']['name'];    
                        $iconType = $_FILES['product_top_view']['type'];
                        $iconSize = $_FILES['product_top_view']['size'];
                  
                   }
                    
                    if($iconName !== null) {
                        if($model->id === null){
                          //$iconFileName = $icon_filename;  
                          if($icon_filename != 'product_unavailable.png'){
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
                            if($this->noNewProductTopViewFileProvided($model->id,$icon_filename)){
                                $iconFileName = $icon_filename; 
                                return $iconFileName;
                            }else{
                             if($icon_filename != 'product_unavailable.png'){
                                 if($this->removeTheExistingProductTopViewFile($model->id)){
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
         * This is the function to ascertain if a new product top view image was provided or not
         */
        public function noNewProductTopViewFileProvided($id,$icon_filename){
            
                $criteria = new CDbCriteria();
                $criteria->select = 'id, product_top_view';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                if($icon['product_top_view']==$icon_filename){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        	 /**
         * This is the function that removes an existing product top view file
         */
        public function removeTheExistingProductTopViewFile($id){
            
            //retreve the existing product top view file from the database
            
            if($this->isTheProductTopViewNotTheDefault($id)){
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "c:\\xampp\htdocs\cobuy_images\\icons\\";
               // $iconpath = '..\appspace_assets\icons'.$icon['icon'];
                $filepath =$directoryPath.$icon['product_top_view'];
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
         * This is the function that determines if  a product top view image  is the default
         */
        public function isTheProductTopViewNotTheDefault($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                if($icon['product_top_view'] == 'product_unavailable.png' || $icon['product_top_view'] ===NULL){
                    return false;
                }else{
                    return true;
                }
        }
        
        
        
        
        /**
         * This is the section that reads and moves product inside view images
         */
        	
        /**
         * This is the function that read and relocate the product inside view file to its directory
         */
        public function moveTheProductInsideViewImageToItsPathAndReturnTheItsName($model,$icon_filename){
            
            if(isset($_FILES['product_inside_view']['name'])){
                        $tmpName = $_FILES['product_inside_view']['tmp_name'];
                        $iconName = $_FILES['product_inside_view']['name'];    
                        $iconType = $_FILES['product_inside_view']['type'];
                        $iconSize = $_FILES['product_inside_view']['size'];
                  
                   }
                    
                    if($iconName !== null) {
                        if($model->id === null){
                          //$iconFileName = $icon_filename;  
                          if($icon_filename != 'product_unavailable.png'){
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
                            if($this->noNewProductInsideViewFileProvided($model->id,$icon_filename)){
                                $iconFileName = $icon_filename; 
                                return $iconFileName;
                            }else{
                             if($icon_filename != 'product_unavailable.png'){
                                 if($this->removeTheExistingProductInsideViewFile($model->id)){
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
         * This is the function to ascertain if a new product inside view image was provided or not
         */
        public function noNewProductInsideViewFileProvided($id,$icon_filename){
            
                $criteria = new CDbCriteria();
                $criteria->select = 'id, product_inside_view';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                if($icon['product_inside_view']==$icon_filename){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        	 /**
         * This is the function that removes an existing product inside view file
         */
        public function removeTheExistingProductInsideViewFile($id){
            
            //retreve the existing product inside view file from the database
            
            if($this->isTheProductInsideViewNotTheDefault($id)){
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "c:\\xampp\htdocs\cobuy_images\\icons\\";
               // $iconpath = '..\appspace_assets\icons'.$icon['icon'];
                $filepath =$directoryPath.$icon['product_inside_view'];
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
         * This is the function that determines if  a product inside image  is the default
         */
        public function isTheProductInsideViewNotTheDefault($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                if($icon['product_inside_view'] == 'product_unavailable.png' || $icon['product_inside_view'] ===NULL){
                    return false;
                }else{
                    return true;
                }
        }
        
        
        
        
        /**
         * This is the section that reads and moves product engine view images
         */
        	
        /**
         * This is the function that read and relocate the product engine view file to its directory
         */
        public function moveTheProductEngineViewImageToItsPathAndReturnTheItsName($model,$icon_filename){
            
            if(isset($_FILES['product_engine_view']['name'])){
                        $tmpName = $_FILES['product_engine_view']['tmp_name'];
                        $iconName = $_FILES['product_engine_view']['name'];    
                        $iconType = $_FILES['product_engine_view']['type'];
                        $iconSize = $_FILES['product_engine_view']['size'];
                  
                   }
                    
                    if($iconName !== null) {
                        if($model->id === null){
                          //$iconFileName = $icon_filename;  
                          if($icon_filename != 'product_unavailable.png'){
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
                            if($this->noNewProductEngineViewFileProvided($model->id,$icon_filename)){
                                $iconFileName = $icon_filename; 
                                return $iconFileName;
                            }else{
                             if($icon_filename != 'product_unavailable.png'){
                                 if($this->removeTheExistingProductEngineViewFile($model->id)){
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
         * This is the function to ascertain if a new product engine view image was provided or not
         */
        public function noNewProductEngineViewFileProvided($id,$icon_filename){
            
                $criteria = new CDbCriteria();
                $criteria->select = 'id, product_engine_view';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                if($icon['product_engine_view']==$icon_filename){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        	 /**
         * This is the function that removes an existing product engine view file
         */
        public function removeTheExistingProductEngineViewFile($id){
            
            //retreve the existing product engine view file from the database
            
            if($this->isTheProductEngineViewNotTheDefault($id)){
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "c:\\xampp\htdocs\cobuy_images\\icons\\";
               // $iconpath = '..\appspace_assets\icons'.$icon['icon'];
                $filepath =$directoryPath.$icon['product_engine_view'];
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
         * This is the function that determines if  a product engine image  is the default
         */
        public function isTheProductEngineViewNotTheDefault($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                if($icon['product_engine_view'] == 'product_unavailable.png' || $icon['product_engine_view'] ===NULL){
                    return false;
                }else{
                    return true;
                }
        }
        
        
        
        
        /**
         * This is the section that reads and moves product back view images
         */
        	
        /**
         * This is the function that read and relocate the product back view file to its directory
         */
        public function moveTheProductBackViewImageToItsPathAndReturnTheItsName($model,$icon_filename){
            
            if(isset($_FILES['product_back_view']['name'])){
                        $tmpName = $_FILES['product_back_view']['tmp_name'];
                        $iconName = $_FILES['product_back_view']['name'];    
                        $iconType = $_FILES['product_back_view']['type'];
                        $iconSize = $_FILES['product_back_view']['size'];
                  
                   }
                    
                    if($iconName !== null) {
                        if($model->id === null){
                          //$iconFileName = $icon_filename;  
                          if($icon_filename != 'product_unavailable.png'){
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
                            if($this->noNewProductBackViewFileProvided($model->id,$icon_filename)){
                                $iconFileName = $icon_filename; 
                                return $iconFileName;
                            }else{
                             if($icon_filename != 'product_unavailable.png'){
                                 if($this->removeTheExistingProductBackViewFile($model->id)){
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
         * This is the function to ascertain if a new product back view image was provided or not
         */
        public function noNewProductBackViewFileProvided($id,$icon_filename){
            
                $criteria = new CDbCriteria();
                $criteria->select = 'id, product_back_view';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                if($icon['product_back_view']==$icon_filename){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        	 /**
         * This is the function that removes an existing product back view file
         */
        public function removeTheExistingProductBackViewFile($id){
            
            //retreve the existing product back view file from the database
            
            if($this->isTheProductBackViewNotTheDefault($id)){
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "c:\\xampp\htdocs\cobuy_images\\icons\\";
               // $iconpath = '..\appspace_assets\icons'.$icon['icon'];
                $filepath =$directoryPath.$icon['product_back_view'];
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
         * This is the function that determines if  a product back image  is the default
         */
        public function isTheProductBackViewNotTheDefault($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                if($icon['product_back_view'] == 'product_unavailable.png' || $icon['product_back_view'] ===NULL){
                    return false;
                }else{
                    return true;
                }
        }
        
        
        
        /**
         * This is the section that reads and moves product left side view images
         */
        	
        /**
         * This is the function that read and relocate the product left side view file to its directory
         */
        public function moveTheProductLeftSideViewImageToItsPathAndReturnTheItsName($model,$icon_filename){
            
            if(isset($_FILES['product_left_side_view']['name'])){
                        $tmpName = $_FILES['product_left_side_view']['tmp_name'];
                        $iconName = $_FILES['product_left_side_view']['name'];    
                        $iconType = $_FILES['product_left_side_view']['type'];
                        $iconSize = $_FILES['product_left_side_view']['size'];
                  
                   }
                    
                    if($iconName !== null) {
                        if($model->id === null){
                          //$iconFileName = $icon_filename;  
                          if($icon_filename != 'product_unavailable.png'){
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
                            if($this->noNewProductLeftSideViewFileProvided($model->id,$icon_filename)){
                                $iconFileName = $icon_filename; 
                                return $iconFileName;
                            }else{
                             if($icon_filename != 'product_unavailable.png'){
                                 if($this->removeTheExistingProductLeftSideViewFile($model->id)){
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
         * This is the function to ascertain if a new product left side view image was provided or not
         */
        public function noNewProductLeftSideViewFileProvided($id,$icon_filename){
            
                $criteria = new CDbCriteria();
                $criteria->select = 'id, product_left_side_view';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                if($icon['product_left_side_view']==$icon_filename){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        	 /**
         * This is the function that removes an existing product left side view file
         */
        public function removeTheExistingProductLeftSideViewFile($id){
            
            //retreve the existing product left side view file from the database
            
            if($this->isTheProductLeftSideViewNotTheDefault($id)){
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "c:\\xampp\htdocs\cobuy_images\\icons\\";
               // $iconpath = '..\appspace_assets\icons'.$icon['icon'];
                $filepath =$directoryPath.$icon['product_left_side_view'];
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
         * This is the function that determines if  a product left side image  is the default
         */
        public function isTheProductLeftSideViewNotTheDefault($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                if($icon['product_left_side_view'] == 'product_unavailable.png' || $icon['product_left_side_view'] ===NULL){
                    return false;
                }else{
                    return true;
                }
        }
        
        
        
        
        /**
         * This is the section that reads and moves product bottom view images
         */
        	
        /**
         * This is the function that read and relocate the product bottom view file to its directory
         */
        public function moveTheProductBottomViewImageToItsPathAndReturnTheItsName($model,$icon_filename){
            
            if(isset($_FILES['product_bottom_view']['name'])){
                        $tmpName = $_FILES['product_bottom_view']['tmp_name'];
                        $iconName = $_FILES['product_bottom_view']['name'];    
                        $iconType = $_FILES['product_bottom_view']['type'];
                        $iconSize = $_FILES['product_bottom_view']['size'];
                  
                   }
                    
                    if($iconName !== null) {
                        if($model->id === null){
                          //$iconFileName = $icon_filename;  
                          if($icon_filename != 'product_unavailable.png'){
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
                            if($this->noNewProductBottomViewFileProvided($model->id,$icon_filename)){
                                $iconFileName = $icon_filename; 
                                return $iconFileName;
                            }else{
                             if($icon_filename != 'product_unavailable.png'){
                                 if($this->removeTheExistingProductBottomViewFile($model->id)){
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
         * This is the function to ascertain if a new product bottom view image was provided or not
         */
        public function noNewProductBottomViewFileProvided($id,$icon_filename){
            
                $criteria = new CDbCriteria();
                $criteria->select = 'id, product_bottom_view';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                if($icon['product_bottom_view']==$icon_filename){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        	 /**
         * This is the function that removes an existing product bottom view file
         */
        public function removeTheExistingProductBottomViewFile($id){
            
            //retreve the existing product bottom view file from the database
            
            if($this->isTheProductBottomViewNotTheDefault($id)){
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "c:\\xampp\htdocs\cobuy_images\\icons\\";
               // $iconpath = '..\appspace_assets\icons'.$icon['icon'];
                $filepath =$directoryPath.$icon['product_bottom_view'];
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
         * This is the function that determines if  a product bottom image is the default
         */
        public function isTheProductBottomViewNotTheDefault($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                if($icon['product_bottom_view'] == 'product_unavailable.png' || $icon['product_bottom_view'] ===NULL){
                    return false;
                }else{
                    return true;
                }
        }
        
        
        
         /**
         * This is the section that reads and moves product dashboard view images
         */
        	
        /**
         * This is the function that read and relocate the product dashboard view file to its directory
         */
        public function moveTheProductDashboardViewImageToItsPathAndReturnTheItsName($model,$icon_filename){
            
            if(isset($_FILES['product_dashboard_view']['name'])){
                        $tmpName = $_FILES['product_dashboard_view']['tmp_name'];
                        $iconName = $_FILES['product_dashboard_view']['name'];    
                        $iconType = $_FILES['product_dashboard_view']['type'];
                        $iconSize = $_FILES['product_dashboard_view']['size'];
                  
                   }
                    
                    if($iconName !== null) {
                        if($model->id === null){
                          //$iconFileName = $icon_filename;  
                          if($icon_filename != 'product_unavailable.png'){
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
                            if($this->noNewProductDashboardViewFileProvided($model->id,$icon_filename)){
                                $iconFileName = $icon_filename; 
                                return $iconFileName;
                            }else{
                             if($icon_filename != 'product_unavailable.png'){
                                 if($this->removeTheExistingProductDashboardViewFile($model->id)){
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
         * This is the function to ascertain if a new product dashboard view image was provided or not
         */
        public function noNewProductDashboardViewFileProvided($id,$icon_filename){
            
                $criteria = new CDbCriteria();
                $criteria->select = 'id, product_dashboard_view';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                if($icon['product_dashboard_view']==$icon_filename){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        	 /**
         * This is the function that removes an existing product dashboard view file
         */
        public function removeTheExistingProductDashboardViewFile($id){
            
            //retreve the existing product dashboard view file from the database
            
            if($this->isTheProductDashboardViewNotTheDefault($id)){
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "c:\\xampp\htdocs\cobuy_images\\icons\\";
               // $iconpath = '..\appspace_assets\icons'.$icon['icon'];
                $filepath =$directoryPath.$icon['product_dashboard_view'];
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
         * This is the function that determines if  product dashboard imageis the default
         */
        public function isTheProductDashboardViewNotTheDefault($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                if($icon['product_dashboard_view'] == 'product_unavailable.png' || $icon['product_dashboard_view'] ===NULL){
                    return false;
                }else{
                    return true;
                }
        }
        
        
        
        
         /**
         * This is the section that reads and moves product contents or booth view images
         */
        	
        /**
         * This is the function that read and relocate the product contents or booth view file to its directory
         */
        public function moveTheProductContentsAndBoothsViewImageToItsPathAndReturnTheItsName($model,$icon_filename){
            
            if(isset($_FILES['product_contents_or_booth_view']['name'])){
                        $tmpName = $_FILES['product_contents_or_booth_view']['tmp_name'];
                        $iconName = $_FILES['product_contents_or_booth_view']['name'];    
                        $iconType = $_FILES['product_contents_or_booth_view']['type'];
                        $iconSize = $_FILES['product_contents_or_booth_view']['size'];
                  
                   }
                    
                    if($iconName !== null) {
                        if($model->id === null){
                          //$iconFileName = $icon_filename;  
                          if($icon_filename != 'product_unavailable.png'){
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
                            if($this->noNewProductContentsAndBoothsViewFileProvided($model->id,$icon_filename)){
                                $iconFileName = $icon_filename; 
                                return $iconFileName;
                            }else{
                             if($icon_filename != 'product_unavailable.png'){
                                 if($this->removeTheExistingProductContentsAndBoothsViewFile($model->id)){
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
         * This is the function to ascertain if a new product contents and booths view image was provided or not
         */
        public function noNewProductContentsAndBoothsViewFileProvided($id,$icon_filename){
            
                $criteria = new CDbCriteria();
                $criteria->select = 'id, product_contents_or_booth_view';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                if($icon['product_contents_or_booth_view']==$icon_filename){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        	 /**
         * This is the function that removes an existing product content or booth view file
         */
        public function removeTheExistingProductContentsAndBoothsViewFile($id){
            
            //retreve the existing product content or booth view file from the database
            
            if($this->isTheProductContentsAndBoothsViewNotTheDefault($id)){
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "c:\\xampp\htdocs\cobuy_images\\icons\\";
               // $iconpath = '..\appspace_assets\icons'.$icon['icon'];
                $filepath =$directoryPath.$icon['product_contents_or_booth_view'];
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
         * This is the function that determines if  a product content or booth image is the default
         */
        public function isTheProductContentsAndBoothsViewNotTheDefault($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $icon= ProductConstituents::model()->find($criteria);
                
                if($icon['product_contents_or_booth_view'] == 'product_unavailable.png' || $icon['product_contents_or_booth_view'] ===NULL){
                    return false;
                }else{
                    return true;
                }
        }
        
        
        
        /**
         * This segment retrieves previous image names and sizes
         */
        /* This is the function that retrieves the previous product front view image name
         */
        public function retrieveThePreviousProductFrontViewImageName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = ProductConstituents::model()->find($criteria);
            
            
            return $icon['product_front_view'];
            
            
        }
        
        /**
         * This is the function that retrieves the previous product front view image size
         */
        public function retrieveThePreviousProductFrontViewImageSize($id){
           
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = ProductConstituents::model()->find($criteria);
            
            
            return $icon['product_front_view_size'];
        }
        
        
        
         /* This is the function that retrieves the previous product right side image view name
         */
        public function retrieveThePreviousProductRightSizeViewImageName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = ProductConstituents::model()->find($criteria);
            
            
            return $icon['product_right_side_view'];
            
            
        }
        
        /**
         * This is the function that retrieves the previous product right side image view size
         */
        public function retrieveThePreviousProductRightSizeViewImageSize($id){
           
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = ProductConstituents::model()->find($criteria);
            
            
            return $icon['product_right_side_view_size'];
        }
        
        
        
        /* This is the function that retrieves the previous product inside image view name
         */
        public function retrieveThePreviousProductInsideViewImageName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = ProductConstituents::model()->find($criteria);
            
            
            return $icon['product_inside_view'];
            
            
        }
        
        /**
         * This is the function that retrieves the previous product inside image view size
         */
        public function retrieveThePreviousProductInsideViewImageSize($id){
           
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = ProductConstituents::model()->find($criteria);
            
            
            return $icon['product_inside_view_size'];
        }
        
        
        
        /**
         /* This is the function that retrieves the previous product top image view name
         */
        public function retrieveThePreviousProductTopViewImageName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = ProductConstituents::model()->find($criteria);
            
            
            return $icon['product_top_view'];
            
            
        }
        
        /**
         * This is the function that retrieves the previous product top image view size
         */
        public function retrieveThePreviousProductTopViewImageSize($id){
           
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = ProductConstituents::model()->find($criteria);
            
            
            return $icon['product_top_view_size'];
        }
        
        
        
        
        /**
         /* This is the function that retrieves the previous product engine image view name
         */
        public function retrieveThePreviousProductEngineViewImageName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = ProductConstituents::model()->find($criteria);
            
            
            return $icon['product_engine_view'];
            
            
        }
        
        /**
         * This is the function that retrieves the previous product engine image view size
         */
        public function retrieveThePreviousProductEngineViewImageSize($id){
           
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = ProductConstituents::model()->find($criteria);
            
            
            return $icon['product_engine_view_size'];
        }
        
        
        
         /**
         /* This is the function that retrieves the previous product back image view name
         */
        public function retrieveThePreviousProductBackViewImageName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = ProductConstituents::model()->find($criteria);
            
            
            return $icon['product_back_view'];
            
            
        }
        
        /**
         * This is the function that retrieves the previous product back image view size
         */
        public function retrieveThePreviousProductBackViewImageSize($id){
           
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = ProductConstituents::model()->find($criteria);
            
            
            return $icon['product_back_view_size'];
        }
        
        
        
        /**
         /* This is the function that retrieves the previous product left side image view name
         */
        public function retrieveThePreviousProductLeftSideViewImageName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = ProductConstituents::model()->find($criteria);
            
            
            return $icon['product_left_side_view'];
            
            
        }
        
        /**
         * This is the function that retrieves the previous product left side image view size
         */
        public function retrieveThePreviousProductLeftSideViewImageSize($id){
           
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = ProductConstituents::model()->find($criteria);
            
            
            return $icon['product_left_side_view_size'];
        }
        
        
        
         /**
         /* This is the function that retrieves the previous product bottom image view name
         */
        public function retrieveThePreviousProductBottomViewImageName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = ProductConstituents::model()->find($criteria);
            
            
            return $icon['product_bottom_view'];
            
            
        }
        
        /**
         * This is the function that retrieves the previous product bottom image view size
         */
        public function retrieveThePreviousProductBottomViewImageSize($id){
           
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = ProductConstituents::model()->find($criteria);
            
            
            return $icon['product_bottom_view_size'];
        }
        
        
        
        /**
         /* This is the function that retrieves the previous product dashboard image view name
         */
        public function retrieveThePreviousProductDashboardViewImageName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = ProductConstituents::model()->find($criteria);
            
            
            return $icon['product_dashboard_view'];
            
            
        }
        
        /**
         * This is the function that retrieves the previous product dashboard image view size
         */
        public function retrieveThePreviousProductDashboardViewImageSize($id){
           
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = ProductConstituents::model()->find($criteria);
            
            
            return $icon['product_dashboard_view_size'];
        }
        
        
        /**
         /* This is the function that retrieves the previous product content or booth image view name
         */
        public function retrieveThePreviousProductContentOrBoothViewImageName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = ProductConstituents::model()->find($criteria);
            
            
            return $icon['product_contents_or_booth_view'];
            
            
        }
        
        /**
         * This is the function that retrieves the previous product content or booth  image view size
         */
        public function retrieveThePreviousProductContentOrBoothViewImageSize($id){
           
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = ProductConstituents::model()->find($criteria);
            
            
            return $icon['product_contents_or_booth_view_size'];
        }
        
        
        /**
         * This is the function that checks the type and size of product front view image
         */
        public function isProductFronViewTypeAndSizeLegal(){
            
            $size = []; 
            if(isset($_FILES['product_front_view']['name'])){
                $tmpName = $_FILES['product_front_view']['tmp_name'];
                $iconFileName = $_FILES['product_front_view']['name'];    
                $iconFileType = $_FILES['product_front_view']['type'];
                $iconFileSize = $_FILES['product_front_view']['size'];
            } 
           if (isset($_FILES['product_front_view'])) {
             $filename = $_FILES['product_front_view']['tmp_name'];
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
         * This is the function that checks the type and size of product right side view image
         */
        public function isProductRightSizeViewTypeAndSizeLegal(){
            
            $size = []; 
            if(isset($_FILES['product_right_side_view']['name'])){
                $tmpName = $_FILES['product_right_side_view']['tmp_name'];
                $iconFileName = $_FILES['product_right_side_view']['name'];    
                $iconFileType = $_FILES['product_right_side_view']['type'];
                $iconFileSize = $_FILES['product_right_side_view']['size'];
            } 
           if (isset($_FILES['product_right_side_view'])) {
             $filename = $_FILES['product_right_side_view']['tmp_name'];
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
         * This is the function that checks the type and size of product inside view image
         */
        public function isProductInsideViewTypeAndSizeLegal(){
            
            $size = []; 
            if(isset($_FILES['product_inside_view']['name'])){
                $tmpName = $_FILES['product_inside_view']['tmp_name'];
                $iconFileName = $_FILES['product_inside_view']['name'];    
                $iconFileType = $_FILES['product_inside_view']['type'];
                $iconFileSize = $_FILES['product_inside_view']['size'];
            } 
           if (isset($_FILES['product_inside_view'])) {
             $filename = $_FILES['product_inside_view']['tmp_name'];
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
         * This is the function that checks the type and size of product top view image
         */
        public function isProductTopViewTypeAndSizeLegal(){
            
            $size = []; 
            if(isset($_FILES['product_top_view']['name'])){
                $tmpName = $_FILES['product_top_view']['tmp_name'];
                $iconFileName = $_FILES['product_top_view']['name'];    
                $iconFileType = $_FILES['product_top_view']['type'];
                $iconFileSize = $_FILES['product_top_view']['size'];
            } 
           if (isset($_FILES['product_top_view'])) {
             $filename = $_FILES['product_top_view']['tmp_name'];
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
         * This is the function that checks the type and size of product engine view image
         */
        public function isProductEngineViewTypeAndSizeLegal(){
            
            $size = []; 
            if(isset($_FILES['product_engine_view']['name'])){
                $tmpName = $_FILES['product_engine_view']['tmp_name'];
                $iconFileName = $_FILES['product_engine_view']['name'];    
                $iconFileType = $_FILES['product_engine_view']['type'];
                $iconFileSize = $_FILES['product_engine_view']['size'];
            } 
           if (isset($_FILES['product_engine_view'])) {
             $filename = $_FILES['product_engine_view']['tmp_name'];
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
         * This is the function that checks the type and size of product back view image
         */
        public function isProductBackViewTypeAndSizeLegal(){
            
            $size = []; 
            if(isset($_FILES['product_back_view']['name'])){
                $tmpName = $_FILES['product_back_view']['tmp_name'];
                $iconFileName = $_FILES['product_back_view']['name'];    
                $iconFileType = $_FILES['product_back_view']['type'];
                $iconFileSize = $_FILES['product_back_view']['size'];
            } 
           if (isset($_FILES['product_back_view'])) {
             $filename = $_FILES['product_back_view']['tmp_name'];
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
         * This is the function that checks the type and size of product left side view image
         */
        public function isProductLeftSideViewTypeAndSizeLegal(){
            
            $size = []; 
            if(isset($_FILES['product_left_side_view']['name'])){
                $tmpName = $_FILES['product_left_side_view']['tmp_name'];
                $iconFileName = $_FILES['product_left_side_view']['name'];    
                $iconFileType = $_FILES['product_left_side_view']['type'];
                $iconFileSize = $_FILES['product_left_side_view']['size'];
            } 
           if (isset($_FILES['product_left_side_view'])) {
             $filename = $_FILES['product_left_side_view']['tmp_name'];
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
         * This is the function that checks the type and size of product bottom view image
         */
        public function isProductBottomViewTypeAndSizeLegal(){
            
            $size = []; 
            if(isset($_FILES['product_bottom_view']['name'])){
                $tmpName = $_FILES['product_bottom_view']['tmp_name'];
                $iconFileName = $_FILES['product_bottom_view']['name'];    
                $iconFileType = $_FILES['product_bottom_view']['type'];
                $iconFileSize = $_FILES['product_bottom_view']['size'];
            } 
           if (isset($_FILES['product_bottom_view'])) {
             $filename = $_FILES['product_bottom_view']['tmp_name'];
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
         * This is the function that checks the type and size of product dashboard view image
         */
        public function isProductDashboardViewTypeAndSizeLegal(){
            
            $size = []; 
            if(isset($_FILES['product_dashboard_view']['name'])){
                $tmpName = $_FILES['product_dashboard_view']['tmp_name'];
                $iconFileName = $_FILES['product_dashboard_view']['name'];    
                $iconFileType = $_FILES['product_dashboard_view']['type'];
                $iconFileSize = $_FILES['product_dashboard_view']['size'];
            } 
           if (isset($_FILES['product_dashboard_view'])) {
             $filename = $_FILES['product_dashboard_view']['tmp_name'];
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
         * This is the function that checks the type and size of product contents or booth view image
         */
        public function isProductContentsOrBoothsViewTypeAndSizeLegal(){
            
            $size = []; 
            if(isset($_FILES['product_contents_or_booth_view']['name'])){
                $tmpName = $_FILES['product_contents_or_booth_view']['tmp_name'];
                $iconFileName = $_FILES['product_contents_or_booth_view']['name'];    
                $iconFileType = $_FILES['product_contents_or_booth_view']['type'];
                $iconFileSize = $_FILES['product_contents_or_booth_view']['size'];
            } 
           if (isset($_FILES['product_contents_or_booth_view'])) {
             $filename = $_FILES['product_contents_or_booth_view']['tmp_name'];
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
         * This is the function that retrieves the constituents of a product
         */
        public function actionListThisProductConstituents(){
            
            $user_id = Yii::app()->user->id;
            
            $product_id = $_REQUEST['product_id'];
            //$product_id =6;
            
            //get all the constituents in the part
            
            $constituents = $this->getAllProductConstituents($product_id);
            
            $accepted_constituents = [];
            
            $accepted = [];
            
            if($user_id != null){
                if($_REQUEST['operation'] != 'noncart'){
                    
                    foreach($constituents as $constituent){
                if($this->isConstituentAcceptedbyMember($constituent,$user_id)){
                    $accepted_constituents[] =$constituent;
                }
            }
            
            
            foreach($accepted_constituents as $accept){
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';   
                $criteria->params = array(':id'=>$accept);
                $consts = ProductConstituents::model()->find($criteria);
                
                $accepted[]= $consts;
            }
            
            
            header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "product" => $accepted,
                                        "member"=>$user_id
                                       )
                           );
                    
                }else{
                    
                    foreach($constituents as $constituent){
                    $criteria = new CDbCriteria();
                    $criteria->select = '*';
                    $criteria->condition='id=:id';   
                    $criteria->params = array(':id'=>$constituent);
                    $consts = ProductConstituents::model()->find($criteria);
                
                    $accepted[]= $consts;
                 }
                 
                 header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "product" => $accepted,
                                        "member"=>$user_id
                                       )
                           );
                    
                }
                            
            
                
                
            }else{
                foreach($constituents as $constituent){
                    $criteria = new CDbCriteria();
                    $criteria->select = '*';
                    $criteria->condition='id=:id';   
                    $criteria->params = array(':id'=>$constituent);
                    $consts = ProductConstituents::model()->find($criteria);
                
                    $accepted[]= $consts;
                 }
                 
                 header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "product" => $accepted,
                                        "member"=>$user_id
                                       )
                           );
                
            }
            
            
            
            
        }
        
        
        
        /**
         * This is the function that gets the constitues of this product
         */
        public function getAllProductConstituents($product_id){
            
            $model = new ProductConstituents;
            
            return $model->getAllProductConstituents($product_id);
        }
        
        
        /**
         * This is the function that confirms if consittuent is accepted by member
         */
        public function isConstituentAcceptedbyMember($constituent,$user_id){
            $model =  new MemberAmendedConstituents;
            
            return $model->isConstituentAcceptedbyMember($constituent,$user_id);
        }
        
        /**
         * This is the function that removes an item from a pack
         */
        public function actionremoveThisConstituentFromThePack(){
            
            $model = new OrderHasConstituents;
            $user_id = Yii::app()->user->id;
            
            $id = $_REQUEST['id'];
            
            $quantity_of_purchase = $_REQUEST['quantity_of_product_in_the_pack'];
            $prevailing_retail_selling_price = $_REQUEST['prevailing_retail_selling_price'];
            $cobuy_member_price = $_REQUEST['per_portion_price'];
            $start_price_validity_period = date("Y-m-d H:i:s", strtotime($_REQUEST['start_price_validity_period']));
            $end_price_validity_period = date("Y-m-d H:i:s", strtotime($_REQUEST['end_price_validity_period']));
            $amount_save_on_purchase = $_REQUEST['amount_save_on_purchase'];
            $primary_product_id = $_REQUEST['product_id'];
           
            //get the open order for this member
            $order_id = $this->getTheOpenOrderInitiatedByMember($user_id); 
            $constituent_name = $this->getTheNameOfThisConstituent($id);
            
            if($this->isThisConstituentSuccessfullyRemovedFromThePack($id,$user_id)){
                $pack_prevailing_retail_selling_price = $model->getThePackNewPrevailingRetailSellingPrice($order_id,$primary_product_id);
                $pack_member_selling_price = $model->getTheMemberNewPackSellingPrice($order_id,$primary_product_id);
                if($this->isPackPricesUpdatedSuccessfully($order_id,$primary_product_id,$pack_prevailing_retail_selling_price,$pack_member_selling_price)){
                    $msg = "'$constituent_name' product is successfully removed from the pack for this customer";
                     header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                         "pack_prevailing_retail_selling_price"=>$pack_prevailing_retail_selling_price,
                                        "pack_member_selling_price"=>$pack_member_selling_price
                                       )
                           );
                    
                }else{
                    $msg = "The '$constituent_name' product is successfully removed from the pack but not the relevant pack prices. You need to reload/reopen this page";
                        header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg,
                                        "pack_prevailing_retail_selling_price"=>$pack_prevailing_retail_selling_price,
                                        "pack_member_selling_price"=>$pack_member_selling_price
                                       )
                           );
                }
         
            }else{
                $msg = "could not remove '$constituent_name' product from the pack for this customer. Please contact the help desk ";
                    header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg,
                                         "pack_prevailing_retail_selling_price"=>$pack_prevailing_retail_selling_price,
                                        "pack_member_selling_price"=>$pack_member_selling_price
                                       )
                           );
                    
            }
            
        }
        
        
        /**
         * This is the function that gets the name of a product constituent
         */
        public function getTheNameOfThisConstituent($id){
            $model = new ProductConstituents;
            
            return $model->getTheNameOfThisConstituent($id);
        }
        
        
        /**
         * This is the function that removes a constituent from the park for a member
         */
        public function isThisConstituentSuccessfullyRemovedFromThePack($id,$user_id){
            $model = new MemberAmendedConstituents;
            
            return $model->isThisConstituentSuccessfullyRemovedFromThePack($id,$user_id);
        }
        
        
        /**
         * This is the function that effects some changes to the pack 
         */
        public function actioneffectTheChangesToThePack(){
            
            $model = new OrderHasConstituents;
            $user_id = Yii::app()->user->id;
            
            $id = $_REQUEST['id'];
            $primary_product_id = $_POST['product_id'];
            $quantity_of_product_in_the_pack = $_POST['quantity_of_product_in_the_pack'];
            $prevailing_retail_selling_price_per_item = $_POST['prevailing_retail_selling_price'];
            $cobuy_member_price_per_item = $_POST['per_portion_price'];
            $start_price_validity_period = date("Y-m-d H:i:s", strtotime($_POST['start_price_validity_period']));
            $end_price_validity_period = date("Y-m-d H:i:s", strtotime($_POST['end_price_validity_period']));
            
            $constituent_name = $this->getTheNameOfThisConstituent($id);
            
            $order_id = $this->getTheOpenOrderInitiatedByMember($user_id);
                      
            if($this->isThisConstituentSuccessfullyModifiedInThePack($id,$primary_product_id,$user_id,$quantity_of_product_in_the_pack,$prevailing_retail_selling_price_per_item,$cobuy_member_price_per_item,$start_price_validity_period,$end_price_validity_period)){
                $pack_prevailing_retail_selling_price = $model->getThePackNewPrevailingRetailSellingPrice($order_id,$primary_product_id);
                $pack_member_selling_price = $model->getTheMemberNewPackSellingPrice($order_id,$primary_product_id);
                 if($this->isPackPricesUpdatedSuccessfully($order_id,$primary_product_id,$pack_prevailing_retail_selling_price,$pack_member_selling_price)){
                      $msg = "The quantity of '$constituent_name' product is successfully modified as well is the relevant pack prices ";
                        header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                        "pack_prevailing_retail_selling_price"=>$pack_prevailing_retail_selling_price,
                                        "pack_member_selling_price"=>$pack_member_selling_price
                                       )
                           );
                
                 }else{
                     $msg = "The quantity of '$constituent_name' product is successfully modified but not the relevant pack prices. You need to reload/reopen this page";
                        header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg,
                                        "pack_prevailing_retail_selling_price"=>$pack_prevailing_retail_selling_price,
                                        "pack_member_selling_price"=>$pack_member_selling_price
                                       )
                           );
                 } 
               
                
            }else{
                $msg = "Could not modify '$constituent_name' product  quantity in the pack for this customer. It is possible you did not make any change to this product. Please contact the help desk for further assistance ";
                header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg
                                       )
                           );
            }
            
            
        }
        
        
        
        /**
         * This is the function that confirms if there is an open order for this member
         */
        public function isMemberWithOpenOrder($member_id){
            $model = new Order;
            return $model->isMemberWithOpenOrder($member_id);
        }
        
        
        /**
         * This is the function that creates a new open order for a member 
         */
        public function createNewOrderForThisMember($member_id){
            $model = new Order;
            return $model->createNewOrderForThisMember($member_id);
        }
        
            
        /**
         * This is the function that updates the pack product information
         */
        public function isPackPricesUpdatedSuccessfully($order_id,$product_id,$pack_prevailing_retail_selling_price,$pack_member_selling_price){
            $model = new OrderHasProducts;
            
            return $model->isPackPricesUpdatedSuccessfully($order_id,$product_id,$pack_prevailing_retail_selling_price,$pack_member_selling_price);
            
        }
        
        
        
        /**
         * This is the function that retrieves a member open order
         */
        public function getTheOpenOrderInitiatedByMember($member_id){
            $model = new Order;
            return $model->getTheOpenOrderInitiatedByMember($member_id);
        }
        
        /**
         * This is the function that effects some changes to quantities in the pack
         */
        public function isThisConstituentSuccessfullyModifiedInThePack($constituent_id,$primary_product_id,$member_id,$quantity_of_product_in_the_pack,$prevailing_retail_selling_price_per_item,$cobuy_member_price_per_item,$start_price_validity_period,$end_price_validity_period){
            
             $model = new MemberAmendedConstituents;
            
            return $model->isThisConstituentSuccessfullyModifiedInThePack($constituent_id,$primary_product_id,$member_id,$quantity_of_product_in_the_pack,$prevailing_retail_selling_price_per_item,$cobuy_member_price_per_item,$start_price_validity_period,$end_price_validity_period);
            
        }
        
        
        /**
         * This is the function that restores constituents product to the pack
         */
        public function actionrestoreConstituentProductToThePack(){
            
            $model = new OrderHasConstituents;
            
            $member_id = Yii::app()->user->id;
            
            $constituent_id = $_POST['constituent'];
            
            $product_id = $_POST['product_id'];
            
            //get the minimum constituent items that is required for each purchase 
            $min_item_quantity_for_purchase = $this->getTheMiniumItemRequiredForPurchase($constituent_id);
            
            $constituent_name = $this->getTheNameOfThisConstituent($constituent_id);
            
            //get the open order for this member
            $order_id = $this->getTheOpenOrderInitiatedByMember($member_id); 
            
             if($this->isThisConstituentSuccessfullyRestoredToThePack($constituent_id,$member_id,$min_item_quantity_for_purchase)){
                 $pack_prevailing_retail_selling_price = $model->getThePackNewPrevailingRetailSellingPrice($order_id,$product_id);
                $pack_member_selling_price = $model->getTheMemberNewPackSellingPrice($order_id,$product_id);
                 if($this->isPackPricesUpdatedSuccessfully($order_id,$product_id,$pack_prevailing_retail_selling_price,$pack_member_selling_price)){
                      $msg = "The '$constituent_name' product is successfully restored to the pack for this customer";
                        header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                        "pack_prevailing_retail_selling_price"=>$pack_prevailing_retail_selling_price,
                                        "pack_member_selling_price"=>$pack_member_selling_price
                                       )
                           );
                 }else{
                     $msg = "The '$constituent_name' product is successfully restored to the pack for this customer but the pack price is not amended. Please reload this page";
                        header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg,
                                        "pack_prevailing_retail_selling_price"=>$pack_prevailing_retail_selling_price,
                                        "pack_member_selling_price"=>$pack_member_selling_price
                                       )
                           );
                 }
               
                
                
            }else{
                $msg = "Could not restore the  '$constituent_name' product to the pack for this customer. Please contact the help desk ";
                header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg,
                                        //"pack_prevailing_retail_selling_price"=>$pack_prevailing_retail_selling_price,
                                       // "pack_member_selling_price"=>$pack_member_selling_price
                                       )
                           );
            }
            
            
        }
        
        
        
        /**
         * This is the function that retrieves a constituents minimum number required for purchase
         */
        public function getTheMiniumItemRequiredForPurchase($constituent_id){
            $model = new ProductConstituents;
            
            return $model->getTheMiniumItemRequiredForPurchase($constituent_id);
        }
        
        /**
         * This is the function that retrieves all removed constituents from a back
         */
        public function actionretrieveAllRemovedMemberProductConstituent(){
            
            $member_id = Yii::app()->user->id;
            
            $model = new MemberAmendedConstituents;
            
            $product_id = $_REQUEST['product_id'];
            
            //$product_id = 6;
            
            $removed_constituents = $model->getAllRemovedConstituentsByThisMember($member_id);
            
            $all_removed_constituents = [];
            
            foreach($removed_constituents as $removed){
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';   
                $criteria->params = array(':id'=>$removed);
                $consts = ProductConstituents::model()->find($criteria);
                
                if($this->isThisConstituentInThisProductPack($removed,$product_id)){
                    $all_removed_constituents[]= $consts;
                }
                
            }
            
            
            header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "product" => $all_removed_constituents
                                       )
                           );
        }
        
        
        /**
         * This is the function that confirms if constituent is in a product pack
         */
        public function isThisConstituentInThisProductPack($removed,$product_id){
            $model = new ProductConstituents;
            return $model->isThisConstituentInThisProductPack($removed,$product_id);
        }
        
        
        /**
         * This is the function that effects the restoration of item to the pack
         */
        public function isThisConstituentSuccessfullyRestoredToThePack($constituent_id,$member_id,$min_item_quantity_for_purchase){
            
            $model = new MemberAmendedConstituents;
            
            return $model->isThisConstituentSuccessfullyRestoredToThePack($constituent_id,$member_id,$min_item_quantity_for_purchase);
        }
        
        
        
        /**
         * This is the function that confirms the status of a product price validity
         */
        public function actionconfirmPriceValidity(){
            
            $model = new Order;
            
            $member_id = Yii::app()->user->id;
            
            $start_date = getdate(strtotime($_REQUEST['start_date']));
            
            $end_date = getdate(strtotime($_REQUEST['end_date']));
            
            $product_id = $_REQUEST['product_id'];
            
            $id = $_REQUEST['id'];
            
            $today = getdate(mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
            
            
            //get all the product constituent details
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$id);
            $product= ProductConstituents::model()->find($criteria);
            
             //retrieve information about this product if it is in the cart
            //$quantity_of_purchase = $this->getThisProductQuantityOfPurchaseByThisMember($member_id,$product['id']);
            
            //get the member open order
            
            if(is_numeric($member_id)){
                $order_id = $model->getTheOpenOrderInitiatedByMember($member_id);
            
          
            $prevailing_retail_selling_price = $this->getThePrevailingRetailSellingPriceForThisOrder($order_id,$product_id,$product['prevailing_retail_selling_price']);
            $member_selling_price = $this->getTheMemberSellingPriceForThisOrder($order_id,$product['id'],$product['per_portion_price']);
            
          if($_REQUEST['end_date'] != ""){
              
              if($model->isTodayGreaterThanOrEqualToStartValidityDate($today, $start_date)){
                if($model->isTodayLessThanOrEqualToEndValidityDate($today,$end_date)){
                    header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "validity" => true,
                                        "prevailing_retail_selling_price"=>$prevailing_retail_selling_price,
                                        "member_selling_price"=>$member_selling_price
                                       )
                           );
                }else{
                     header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "validity" => false,
                                        "prevailing_retail_selling_price"=>$prevailing_retail_selling_price,
                                        "member_selling_price"=>$member_selling_price
                                       )
                           );
                }
            }else{
                header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "validity" => false,
                                        "prevailing_retail_selling_price"=>$prevailing_retail_selling_price,
                                        "member_selling_price"=>$member_selling_price
                                       )
                           );
            }
              
          }else{
              header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "validity" => true,
                                        "prevailing_retail_selling_price"=>$prevailing_retail_selling_price,
                                        "member_selling_price"=>$member_selling_price
                                       )
                           );
          }
            
                
                
            }else{
                header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "validity" => true,
                                       "prevailing_retail_selling_price"=>$product['prevailing_retail_selling_price'],
                                       "member_selling_price"=>$product['prevailing_retail_selling_price'],
                                       )
                           );
                
            }
            
           
         
                    
            
        }
        
        
        
         /**
         * This is the function that gets the prevailing selling price of a product
         */
        public function getThePrevailingRetailSellingPriceForThisOrder($order_id,$product_id,$prevailing_retail_selling_price){
            
            $model = new Order;
            $order_start_price_validity_date = $this->getThisOrderStartPriceValidityDate($order_id,$product_id); 
            
            $order_end_price_validity_date = $this->getThisOrderEndPriceValidityDate($order_id,$product_id); 
            
            $today = getdate(mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
                    
            
              //confrim if order is still open
                if($model->isOrderOpen($order_id)){
                    if($model->isTodayGreaterThanOrEqualToStartValidityDate($today, $order_start_price_validity_date)){
                        if($model->isTodayLessThanOrEqualToEndValidityDate($today,$order_end_price_validity_date)){
                            return $this->getTheRetailSellingPriceFromThisOrder($order_id,$product_id);
                        }else{
                            return $prevailing_retail_selling_price;
                        }
                        
                    }else{
                         return $prevailing_retail_selling_price;
                    }
            
                }else{
                    return $prevailing_retail_selling_price;
                }
                
            }
        
        
        
        
         /**
         * This is the function that gets the prevailing selling price of a product
         */
        public function getTheMemberSellingPriceForThisOrder($order_id,$product_id,$member_retail_selling_price){
            
            $model = new Order;
            $order_start_price_validity_date = $this->getThisOrderStartPriceValidityDate($order_id,$product_id); 
            
            $order_end_price_validity_date = $this->getThisOrderEndPriceValidityDate($order_id,$product_id); 
            
            $today = getdate(mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
                    
            
                //confrim if order is still open
                if($model->isOrderOpen($order_id)){
                    if($model->isTodayGreaterThanOrEqualToStartValidityDate($today, $order_start_price_validity_date)){
                        if($model->isTodayLessThanOrEqualToEndValidityDate($today,$order_end_price_validity_date)){
                            return $this->getTheMemberSellingPriceFromThisOrder($order_id,$product_id);
                        }else{
                            return $member_retail_selling_price;
                        }
                        
                    }else{
                         return $member_retail_selling_price;
                    }
            
                }else{
                    return $member_retail_selling_price;
                }
                
          
        }
        
        
        
         /**
         * This is the function that gets the retail selling price for a product
         */
        public function getTheRetailSellingPriceFromThisOrder($order_id,$product_id){
            $model = new OrderHasConstituents;
            
            return $model->getTheRetailSellingPriceFromThisOrder($order_id,$product_id);
        }
        
        
         /**
         * This is the function that gets the member selling price for a product
         */
        public function getTheMemberSellingPriceFromThisOrder($order_id,$product_id){
            $model = new OrderHasConstituents;
            
            return $model->getTheMemberSellingPriceFromThisOrder($order_id,$product_id);
        }
        
        
        
        /**
         * This is the function that gets this order start price validity date
         */
        public function getThisOrderStartPriceValidityDate($order_id,$product_id){
            $model = new OrderHasConstituents;
            
            return $model->getThisOrderStartPriceValidityDate($order_id,$product_id);
        }
        
        
          /**
        
        
      /**
       * This is the function that gets the prevailing retailing selling price from an order
       */
        public function getThisOrderEndPriceValidityDate($order_id,$product_id){
            $model = new OrderHasConstituents;
            
            return $model->getThisOrderEndPriceValidityDate($order_id,$product_id);
        }
}
