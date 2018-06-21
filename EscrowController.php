<?php

class EscrowController extends Controller
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
				'actions'=>array('create','update','requestingEscrow','ListAllEscrows','listallescrowsreceivedbymember','listallescrowsinitiatedbymember',
                                    'cancelThisEscrow','modifyingThisEscrow','invokingThisEscrow','retrieveThisescrowFile','retrieveThisescrowFileForThisQuote'),
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
         * This is the function that requests for an escrow from the escrow manager
         */
        public function actionrequestingEscrow(){
            
            
         
            
         $model = new Escrow;
         
         $member_id = Yii::app()->user->id;
            
         if(isset($_POST['quoted_id'])){
             $model->quote_id = $_POST['quote_id'];
         }else{
             $model->quote_id = 0;
         }
       
         $model->product_id = $_POST['product_id'];
         $model->escrow_number = $model->generateTheEscrowNumberForThisTransaction($model->quote_id,$model->product_id);
        if(isset($_POST['is_quotable'])){
            if($_POST['is_quotable'] == true){
                $model->is_quoted =1;
            }else{
                $model->is_quoted =0;
            }
             
         }else{
             $model->is_quoted = 0;
         }
          if(isset($_POST['future_trading'])){
             $model->is_futuristic = $_POST['future_trading'];
           }else{
             $model->is_futuristic = 0;
            
         }
     
         
        $model->minimum_number_of_product_to_buy = $_POST['minimum_number_of_product_to_buy_for_computation'];
        $model->whats_product_per_item = $_POST['whats_represents_an_item'];
        $model->status = strtolower('live');
        $model->escrow_operation_for = $_POST['escrow_request_from'];
        if($_POST['subscription_type'] == strtolower('post')){
              $model->quantity = $_POST['subscription_quantity'];
         }else{
              $model->quantity = $_POST['quantity_of_purchase'];
         }
        
         $model->price_per_item = $_POST['prevailing_retail_selling_price'];
         $model->total_amount_purchased=$_POST['amount_to_be_paid'];
         $model->escrow_initiation_date = new CDbExpression('NOW()');
         $model->escrow_initiated_by = Yii::app()->user->id;
         $model->direction = strtolower('initiation');
       
         $is_escrowed = 1;
         $is_escrow_accepted = 0;
          $start_price_validity_period = date("Y-m-d H:i:s", strtotime($_POST['start_price_validity_period'])); 
          $end_price_validity_period = date("Y-m-d H:i:s", strtotime($_POST['end_price_validity_period'])); 
          $cobuy_member_selling_price = 0;
          $amount_saved_on_purchase = 0;
         if($this->isMemberWithOpenOrder($member_id)){
                $order_id = $this->getTheOpenOrderInitiatedByMember($member_id);
            }else{
                $order_id = $this->createNewOrderForThisMember($member_id);
            }
         
          $escrow_file_error = 0;
        
         if($_FILES['escrow_agreement_file']['name'] != ""){
              if($_FILES['escrow_agreement_file']['type'] == 'application/pdf'){
                   $escrow_filename = $_FILES['escrow_agreement_file']['name'];
                   $escrow_filesize = $_FILES['escrow_agreement_file']['size'];
              }else{
                  $escrow_file_error = $escrow_file_error + 1;
                  
              }
              
          if($escrow_file_error == 0){
             if($model->validate()) {
                
            //move the escrow agreement file to the escrow directory 
            $model->escrow_agreement_file = $model->moveTheEscrowAgreementToItsPathAndReturnItsFileName($escrow_filename);
            if($model->escrow_operation_for ==strtolower('noncart')){
                 if($model->save()) {
                       //  if($this->isProductSubscriptionUpdatedSuccessfully($member_id,$model->product_id)){
                              $msg = "product escrow  was initiated successful. Please Visit the Escrow module in 'My Oneroof' section if you ever need to modify or cancel the escrow";
                               header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                        "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                         "escrow_from"=>$model->escrow_operation_for)
                                 );
                      /**   }else{
                              $msg = "product escrow  was initiated successful. However, the product subscription information were not updated. Please Visit the Escrow module in 'My Oneroof' section to consummate this transaction";
                               header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                        "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                         "escrow_number"=>$model->escrow_number)
                                 );
                         }    
                       * 
                       */               
                       
                         
                         
              }else{
                            //delete all the moved files in the directory when validation error is encountered
                            $msg = "There was an issue initiating this escrow. Possibly field validation error";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                               );
                          }
                
            }else{
                if($model->hasTheEscrowOfThisSubscriptionAlreadyInitiated($member_id,$model->status,$model->product_id,$model->escrow_operation_for)==false){
                     if($model->save()) {
                         if($this->isProductSubscriptionUpdatedSuccessfully($model->id,$member_id,$model->product_id)){
                              $msg = "product escrow  was initiated successful. Please Visit the Escrow module in 'My Oneroof' section if you ever need to modify or cancel the escrow";
                               header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                        "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                         "escrow_from"=>$model->escrow_operation_for)
                                 );
                         
                       }else{
                           if($model->escrow_operation_for == 'pre'){
                               $msg = "This pre-subscribed product had been sent to the escrow administrator. If the escrow is accepted, the product will be added to your cart so as to effect payment. However, you may visit the Escrow module in 'My Oneroof' section if you ever need to modify or cancel the escrow";
                               header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                        "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                         "escrow_from"=>$model->escrow_operation_for)
                                 );
                           }else{
                               $msg = "product escrow  was initiated successful. However, the product subscription information were not updated. Please Visit the Escrow module in 'My Oneroof' section if you ever need to modify or cancel the escrow";
                               header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                        "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                         "escrow_from"=>$model->escrow_operation_for)
                                 );
                           }
                              
                         
                          }   
                    
                         
              }else{
                            
                            $msg = "There was an issue initiating this escrow. Possibly field validation error";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                               );
                          }
                    
                }else{
                     $msg = "You already have an escrow on this product subscription waiting for acceptance.You may need to wait for the escrow to be accepted before initiating a new one";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                               );
                }
                
            }
          
            
                }else{
                     //delete all the moved files in the directory when validation error is encountered
                            $msg = "Validaion Error: Check the forms fields for correctness";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg
                                       )
                               );
                          
                }
          }else{
              //delete all the moved files in the directory when validation error is encountered
                            $msg = "File Format Error: There was an error in the uploaded escrow agreement document. Please check the uploaded file and try again";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                               );
          }
         }else{
            
             if($model->escrow_operation_for ==strtolower('noncart')){
                 $msg = "The Escrow Agreement document was not uploaded. Please upload it and try again";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg,
                                         "escrow_number"=>$model->escrow_number)
                               );
             }else{
                 if($this->isProductEscrowable($model->product_id )){
                     $msg = "The Escrow Agreement document was not uploaded. Please upload it and try again";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg,
                                         "escrow_number"=>$model->escrow_number)
                               );
                 }else{
                     $msg = "This product is not escrowable. Please choose another product and try again";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg,
                                         )
                               );
                 }
             }
             
         }   
              
            
        }
        
        
        /**
         * This is the function that update product subscription information
         */
        public function isProductSubscriptionUpdatedSuccessfully($escrow_id,$member_id,$product_id){
            $model = new MemberSubscribedToProducts;
            return $model->isProductSubscriptionUpdatedSuccessfully($escrow_id,$member_id,$product_id);
        }
        
        
        /**
         * This is the function that determines if a product is escrowable
         */
        public function isProductEscrowable($product_id ){
            $model = new Product;
            return $model->isProductEscrowable($product_id);
        }
      
        
        
        /**
         * This is the function that determines if a member is with an open order
         */
        public function isMemberWithOpenOrder($member_id){
            $model = new Order;
            return $model->isMemberWithOpenOrder($member_id);
        }
        
        /**
         * This is the function that retrieves an open order id of a member
         */
        public function getTheOpenOrderInitiatedByMember($member_id){
            $model = new Order;
            return $model->getTheOpenOrderInitiatedByMember($member_id);
        }
        
        
        /**
         * This is the function that creates a new order for a member
         */
        public function createNewOrderForThisMember($member_id){
            $model = new Order;
            return $model->createNewOrderForThisMember($member_id);
            
        }
        
        
        /**
         * This is the function that determines if a product is already in the cart
         */
        public function isProductNotAlreadyInTheCart($order_id,$product_id){
            $model = new OrderHasProducts;
            return $model->isProductNotAlreadyInTheCart($order_id,$product_id);
        }
        
        
        /**
         * This is the function that adds an escrowed product to cart
         */
        public function isAddingThisProductToCartASuccess($order_id,$product_id,$quantity_of_purchase,$amount_saved_on_purchase,$prevailing_retail_selling_price,$cobuy_member_selling_price,$start_price_validity_period,$end_price_validity_period,$is_escrowed,$is_escrow_accepted){
            
            $model = new OrderHasProducts;
            return $model->isAddingThisProductToCartASuccess($order_id,$product_id,$quantity_of_purchase,$amount_saved_on_purchase,$prevailing_retail_selling_price,$cobuy_member_selling_price,$start_price_validity_period,$end_price_validity_period,$is_escrowed,$is_escrow_accepted);
        }
        
        
        /**
         * This is the  function that updates the escrow status of a product in cart
         */
        public function isTheUpdateOfTheEscrowStatusOfATransactionASuccess($order_id,$product_id,$is_escrowed,$is_escrow_accepted){
            
            $model = new OrderHasProducts;
            return $model->isTheUpdateOfTheEscrowStatusOfATransactionASuccess($order_id,$product_id,$is_escrowed,$is_escrow_accepted);
        }
        
        
        
        /**
         * This is the function that list all escrow on the store
         */
        public function actionListAllEscrows(){
            
             $escrow = Escrow::model()->findAll();
                if($escrow===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"assigned" => $assigned,
                            "escrow" => $escrow)
                       );
                       
                }
            
        }
        
        
        /**
         * This is the function that list all escrows initiated by a member
         */
        public function actionlistallescrowsinitiatedbymember(){
            
            $member_id = Yii::app()->user->id;
          
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='escrow_initiated_by=:id';
            $criteria->params = array(':id'=>$member_id);
            $escrow= Escrow::model()->findAll($criteria);
            
             if($escrow===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "escrow" => $escrow)
                       );
                       
                }
        }
        
        
        /**
         * This is the function that list all escrow received by a member
         */
        public function actionlistallescrowsreceivedbymember(){
            
            $model = new ProductHasVendor;
            
            $member_id = Yii::app()->user->id;
          
            //get all the product that a member is a merchant of
            $merchant_products = $model->getAllTheProductThisMemberIsAMerchantOf($member_id);
            
            $all_escrow_received = [];
            //retrieve all escrow
            $criteria = new CDbCriteria();
            $criteria->select = '*';
           // $criteria->condition='status=:status or (status=:accepted and quote_response_from=:responsefrom';
          //  $criteria->params = array(':status'=>'live',':accepted'=>'accepted',':responsefrom'=>$member_id);
            $escrows= Escrow::model()->findAll($criteria);
            
            foreach($escrows as $escrow){
                if(in_array($escrow['product_id'],$merchant_products)){
                    $all_escrow_received[] = $escrow;
                }
                
                
            }
            
             if($escrow===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "escrow" => $all_escrow_received)
                       );
                       
                }
        
            
        }
        
        /**
         * This is the function that modifies an existing escrow request
         */
        public function actionmodifyingThisEscrow(){
             
            $model = new Escrow;
            $member_id = Yii::app()->user->id;
            $escrow_id = $_POST['escrow_id'];
            $product_id = $_POST['product_id'];
            $quantity = $_POST['quantity_of_purchase'];
            $escrow_operation_for = $_POST['escrow_request_from'];
            $amount_to_be_paid = $_POST['amount_to_be_paid'];
            
          if($model->isThisEscrowModifiable($escrow_id)){
            $escrow_file_error = 0;
            if($_FILES['escrow_agreement_file']['name'] != ""){
                
                if($_FILES['escrow_agreement_file']['type'] == 'application/pdf'){
                   $escrow_filename = $_FILES['escrow_agreement_file']['name'];
                   $escrow_filesize = $_FILES['escrow_agreement_file']['size'];
               }else{
                  $escrow_file_error = $escrow_file_error + 1;
               }
               if($escrow_file_error == 0){
                   $new_escrow_agreement_file = $model->moveTheEscrowAgreementToItsPathAndReturnItsFileName($escrow_filename);
                   if($model->isModifyingThisEscrowAtEscrowASuccess($escrow_id,$quantity,$new_escrow_agreement_file,$amount_to_be_paid)){
                      // if($this->isModifyingEscrowFacilityAtSubscriptionASuccess($product_id,$member_id,$quantity)){
                        //escrow modified successfully
                         $msg = "Escrow modification is successful.";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                        "escrow_operation_for"=>$escrow_operation_for
                                        )
                               );  
                  /**  }else{
                        //escrow modified but subscription escrow feature not modified. 
                        $msg = "Modification of escrow is only successful at the escrow module but not at the product subscription module.";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                        "escrow_operation_for"=>$escrow_operation_for
                                        )
                               ); 
                    }
                   * 
                   */
                       
                   }else{
                       //modifying escrow is not a success
                       $msg = "No new data was provided hence there were no modification of this escrow or the escrow is no longer available.";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg,
                                        )
                               );
                   }
               }else{
                   //there is an error in the uploaded file. not in the rite format
                      $msg = "There is an error in the uploaded file. Please check the file and try again and ensure that you are using the .pdf format.";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg,
                                        )
                               ); 
               }
                
            }else{
               //escrow agreement file is empty
                if($model->isModifyingThisEscrowAtEscrowWithoutFileASuccess($escrow_id,$quantity,$amount_to_be_paid)){
                   // if($this->isModifyingEscrowFacilityAtSubscriptionASuccess($product_id,$member_id,$quantity)){
                        //escrow modified successfully
                        $msg = "Escrow modification is successful.";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                         "escrow_operation_for"=>$escrow_operation_for
                                        )
                               );  
                  /**  }else{
                        //escrow modified but subscription escrow feature not modified. 
                        $msg = "Modification of escrow is only successful at the escrow module but not at the product subscription module.";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                        "escrow_operation_for"=>$escrow_operation_for
                                        )
                               ); 
                    }
                   * 
                   */
                }else{
                    //escrow could not be modified
                    $msg = "No new data was provided hence there were no modification of this escrow or the escrow is no longer available.";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg,
                                        )
                               ); 
                }
            }
                
          }else{
                
                //cannot be modified
               $msg = "This escrow faciity cannot be modified. Its possible a decision had already been made on the escrow. Please contact customer care for some clarification.";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg,
                                        )
                               ); 
            } 
           
       
        }
        
        
        /**
         * This is the function that cancels an existing escrow request
         */
        public function actioncancelThisEscrow(){
            $model = new Escrow;
            $escrow_id = $_POST['escrow_id'];
            $product_id = $_POST['product_id'];
            $member_id = Yii::app()->user->id;
            $quantity = $_POST['quantity_of_purchase'];
            $escrow_operation_for = $_POST['escrow_request_from'];
            
            if($model->isThisEscrowCancellable($escrow_id)){
                if($model->isCancellingThisEscrowAtEscrowASuccess($escrow_id)){
                    if($escrow_operation_for == 'post'){
                        if($this->isRemovingEscrowFacilityAtSubscriptionASuccess($product_id,$member_id,$quantity)){
                        //escrow cancelled successfully
                        $msg = "The Escrow is cancelled successful.";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                        "escrow_operation_for"=>$escrow_operation_for
                                        )
                               );
                    }else{
                        //escrow cancelled but subscription escrow feature not modified. 
                        $msg = "The escrow is cancelled but an attempt to modify the escrow facility on the subscribed product failed.Please contact customer care for assistance ";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                        "escrow_operation_for"=>$escrow_operation_for
                                        )
                               );
                    }
                    }else{
                        $msg = "The Escrow is cancelled successful.";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                        "escrow_operation_for"=>$escrow_operation_for
                                        )
                               );
                    }
                    
                }else{
                    //escrow could not be cancelled
                    $msg = "This escrow could not be cancelled. It is possible the escrow is unavailable.Please contact customer care for assistance ";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg,
                                        )
                               );
                }
                
                
            }else{
                //escrow cannot be cancelled
                $msg = "This escrow could not be cancelled. It is possible a decision had already been made on the escrow. Please contact customer care for some clarification ";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg,
                                         "cancellable?"=>$model->isThisEscrowCancellable($escrow_id),
                                          "escrow_id"=>$escrow_id       
                                        )
                               );
            }
        }
        
        
        /**
         * This is the function that modifies escrow facility at subscription
         */
        public function isModifyingEscrowFacilityAtSubscriptionASuccess($product_id,$member_id,$quantity){
            $model = new MemberSubscribedToProducts;
            return $model->isModifyingEscrowFacilityAtSubscriptionASuccess($product_id,$member_id,$quantity);
        }
        
        
        /**
         * This is the function that tremoves the escrow faciity on products on subscription
         */
        public function isRemovingEscrowFacilityAtSubscriptionASuccess($product_id,$member_id,$quantity){
            $model = new MemberSubscribedToProducts;
            return $model->isRemovingEscrowFacilityAtSubscriptionASuccess($product_id,$member_id,$quantity);
        }
        
        
        /**
         * This is the function that invokes an escrow
         */
        public function actioninvokingThisEscrow(){
            $model = new Escrow;
            $escrow_id = $_POST['escrow_id'];
            $reason_for_invocation = $_POST['reason_for_invocation'];
            $accepted_escrow_invocation_terms= $_POST['terms_and_conditions'];
            $escrow_number = $_POST['escrow_number'];
            
            if($model->isThisEscrowInvokable($escrow_id )){
                
                if($model->isTheInvocationOfThisEscrowASuccess($escrow_id,$reason_for_invocation,$accepted_escrow_invocation_terms)){
                 $msg = "This is to ackonwledge your invocation of an escrow, number '$escrow_number'. We will be engaging with you subsequently until all disagreements are resolved. Sorry for the inconvenience";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                        )
                               );
            }else{
                $msg = "Your attempt to invoke this escrow, number '$escrow_number'was not successful. Its likely it had previously being invoked or the escrow cannot be invoked. Please contact customer care for assistance";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg,
                                        )
                               );
            }
            }else{
                $msg = "This escrow cannot be invoked as it appears the escrow is yet to be accepted. Please contact customer care for clarification";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg,
                                        )
                               );
            }
            
            
            
        }
        
        
        /**
         * This is the function that retrieves an escrow information
         */
        public function actionretrieveThisescrowFile(){
            
            $escrow_id = $_REQUEST['escrow_id'];
            
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$escrow_id);
            $escrow= Escrow::model()->find($criteria);
            
            if($escrow===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "escrow"=>$escrow,
                               )
                       );
                       
                }
            
            
        }
        
        
        /**
         * Tis is the function that retrieves the escrow file of a quotation
         */
        public function actionretrieveThisescrowFileForThisQuote(){
            
             $quote_id = $_REQUEST['quote_id'];
             $escrow_id = $_REQUEST['escrow_id'];
            
             if($quote_id >0){
                 $criteria = new CDbCriteria();
                 $criteria->select = '*';
                 $criteria->condition='quote_id=:id';
                 $criteria->params = array(':id'=>$quote_id);
                  $escrow= Escrow::model()->find($criteria);
            
            if($escrow===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "escrow"=>$escrow,
                               )
                       );
                       
                }
             }else{
                $criteria = new CDbCriteria();
                 $criteria->select = '*';
                 $criteria->condition='id=:id';
                 $criteria->params = array(':id'=>$escrow_id);
                  $escrow= Escrow::model()->find($criteria);
            
            if($escrow===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "escrow"=>$escrow,
                               )
                       );
                       
                }
             }
            
            
            
        }
}
