<?php

class QuoteController extends Controller
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
				'actions'=>array('index','view','requestingForAQuote'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','listallquotes','listallquotesinitiatedByMember','listallquotesreceivedByMember',
                                    'retrievethisquotefuturesinformation','effectTheModificationOfAQuote','cancelThisQuote','sendingQuotationToThisQuoteRequest',
                                    'listAllResponsesToAQuote','retrievequoteinformation','rejectingThisMemberQuotation','acceptingThisMemberQuotation',
                                    'retrieveThisQuotationFile'),
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
         * This is the function that requests for a quote for an item
         */
        public function actionrequestingForAQuote(){
            
         $model = new Quote;
         
         $service_id = $_POST['service_id'];
         $category_id = $_POST['category_id'];
         $type_id = $_POST['type_id'];
         
        $model->product_id = $_POST['id'];
         $model->quote_number = $model->generateThisQuoteNumber($service_id,$category_id,$type_id,$model->product_id);
     if((isset($_POST['need_escrow_agreement']))){
             $model->is_escrowed = $_POST['need_escrow_agreement'];
         }else{
             $model->is_escrowed = 0;
         }
         
        
         if(isset($_POST['future_trading'])){
             $model->is_for_future = $_POST['future_trading'];
           }else{
             $model->is_for_future = 0;
            
         }
    
          
         
        $model->minimum_number_of_product_to_buy = $_POST['minimum_number_of_product_to_buy_for_computation'];
        $model->whats_product_per_item = strtolower($_POST['whats_represents_an_item']);
        //$model->whats_product_per_item = '50kg';
        $model->status = strtolower('live');
        $model->quantity = $_POST['quantity_of_purchase'];
        $model->direction = strtolower('initiation');
        $model->quote_initiation_date = new CDbExpression('NOW()');
        $model->quote_initiated_by = Yii::app()->user->id;
        $model->quote_submission_date_of_expiry = $model->getThisQuoteSubmissionExpiryDueDate();
        
        if(isset($_POST['month_of_delivery'])){
            $month_of_delivery = strtolower($_POST['month_of_delivery']);
        }else{
            $month_of_delivery=0;
        }
        if(isset($_POST['year_of_delivery'])){
            $year_of_delivery = strtolower($_POST['year_of_delivery']);
        }else{
            $year_of_delivery=0;
        }
        if(isset($_POST['payment_type'])){
            $payment_type = strtolower($_POST['payment_type']);
        }else{
           $payment_type = 0;
        } 
        if(isset($_POST['payment_frequency'])){
            $payment_frequency = strtolower($_POST['payment_frequency']);
        }else{
            $payment_frequency =0;
        }
         $escrow_file_error = 0;
         
  if($model->isThereAPendingQuoteOfThisProductOfSameQuantity($model->product_id,$model->status,$model->quantity,$model->is_for_future,$month_of_delivery,$year_of_delivery,$payment_type,$payment_frequency)==false){
     
    //  $this_quote_idd = $model->getThisQuoteId($model->product_id,$model->status,$model->quantity,$model->is_for_future,strtolower($_POST['month_of_delivery']),strtolower($_POST['year_of_delivery']),strtolower($_POST['payment_type']),strtolower($_POST['payment_frequency']));
      
      if($model->is_escrowed == 1){
     if($_FILES['escrow_agreement_file']['name'] != ""){
          if($_FILES['escrow_agreement_file']['type'] == 'application/pdf'){
                   $escrow_filename = $_FILES['escrow_agreement_file']['name'];
                   $escrow_filesize = $_FILES['escrow_agreement_file']['size'];
              }else{
                  $escrow_file_error = $escrow_file_error + 1;
                  
              }   
     }else{
        $escrow_file_error = $escrow_file_error + 1;
     }
             
           
  if($escrow_file_error == 0){
        if($model->save()) {
                
            //move the escrow agreement file to the escrow directory 
            $escrow_agreement_filename = $this->moveTheEscrowAgreementToItsPathAndReturnItsFileName($escrow_filename);
            //write the escrow information to the escrow table
            if($this->isNewEscrowSuccessfullyInitiated($model->product_id,$model->id,$escrow_agreement_filename,$_POST['requesting_for_a_quote'],$model->is_for_future,$model->status,$model->minimum_number_of_product_to_buy,$model->whats_product_per_item,$model->quantity,$_POST['escrow_request_from'])){
                    if($model->is_for_future == 1 || $model->is_for_future == true){
                        if($this->isNewFuturesSuccessfullyInitiated($model->id,$_POST['requesting_for_a_quote'],$model->is_escrowed,$model->status,$model->minimum_number_of_product_to_buy,$model->whats_product_per_item,$model->quantity,strtolower($_POST['month_of_delivery']),strtolower($_POST['year_of_delivery']),strtolower($_POST['payment_type']),strtolower($_POST['payment_frequency']))){
                                            $msg = "The Request for a quote and the initiation of both trade assurance and futures trading were successful.Visit the 'My Oneroof' section to consummate this transaction ";
                                                 header('Content-Type: application/json');
                                                 echo CJSON::encode(array(
                                                 "success" => mysql_errno() == 0,
                                                 "msg" => $msg,
                                                    // "tis quote id"=>$this_quote_idd
                                                         )
                                                );
                                }else{
                                        $msg = "The Request for a quote and the initiation of trade assurance were successful. However,Futures initiation was not successful.You need to contact customer care for assistance for that. Buy you need to Visit the 'My Oneroof' section to consummate this transaction";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                                } 
                    }else{
                        $msg = "The Request for a quote and the initiation of trade assurance were successful.Visit the 'My Oneroof' section to consummate this transaction";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                    }
            }else{
                   if($model->is_for_future == 1 || $model->is_for_future == true){
                        if($this->isNewFuturesSuccessfullyInitiated($model->id,$_POST['requesting_for_a_quote'],$model->is_escrowed,$model->status,$model->minimum_number_of_product_to_buy,$model->whats_product_per_item,$model->quantity,strtolower($_POST['month_of_delivery']),strtolower($_POST['year_of_delivery']),strtolower($_POST['payment_type']),strtolower($_POST['payment_frequency']))){
                                            $msg = "The Request for a quote and the initiation of futures trading were successful. However, there was an error initiating the escrow request. Please contact customer care for assistance. You also need to Visit the 'My Oneroof' section to consummate this transaction";
                                                 header('Content-Type: application/json');
                                                 echo CJSON::encode(array(
                                                 "success" => mysql_errno() == 0,
                                                 "msg" => $msg)
                                                );
                                }else{
                                        $msg = "The Request for a quote was successful. However, there was an error initiating both Futures trading and the escrow facility .You need to contact customer care for assistance as well as Visit the 'My Oneroof' section to consummate this transaction";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                                } 
                    }else{
                        $msg = "The Request for a quote was successful.However, the initiation of the escrow facility was not successful. Please Visit the 'My Oneroof' section to consummate this transaction";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                    }
                
            }
                                    
                                    
       }else{
                 $msg = "The initiation of this quote was not successful. Please try again";
                                 header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() != 0,
                                  "msg" => $msg)
                                    ); 
                
       } 
             
             
         }else{
              $msg = "There was an error in the escrow agreement file uploaded. Please ensure you are uploading a .pdf file";
                                 header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() != 0,
                                  "msg" => $msg)
                                    ); 
         }     
  
    }else if($model->is_escrowed == 0){
             if($model->save()) {
                     if($model->is_for_future == 1 || $model->is_for_future == true){
                          if($this->isNewFuturesSuccessfullyInitiated($model->id,$_POST['requesting_for_a_quote'],$model->is_escrowed,$model->status,$model->minimum_number_of_product_to_buy,$model->whats_product_per_item,$model->quantity,strtolower($_POST['month_of_delivery']),strtolower($_POST['year_of_delivery']),strtolower($_POST['payment_type']),strtolower($_POST['payment_frequency']))){
                              $msg = "The Request for a quote and the initiation of futures trading on this product were successful. Please Visit the 'My Oneroof' section to consummate this transaction";
                                 header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() == 0,
                                  "msg" => $msg)
                                    );
                         }else{
                             $msg = "The Request for a quote was successful. However, Futures initiation request was not successful.You need to contact customer care for assistance as well as Visit the 'My Oneroof' section to consummate this transaction";
                                 header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() == 0,
                                  "msg" => $msg)
                                    ); 
                         }
                          
                      }else{
                          $msg = "The request for a quote was successul.We will provide you with the requested quotes in not more than two days. Please Visit the 'My Oneroof' section to consummate this transaction";
                                 header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() == 0,
                                  "msg" => $msg)
                                    ); 
                      } 
                 
             }else{
                 $msg = "There was an error initiating this quote.Please try again";
                                 header('Content-Type: application/json');
                                    echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                    "msg" => $msg,
                                     "quote_number"=>$model->quote_number   )
                                 ); 
             }
         
             
         }
      
  }else{
      $msg = "It appears you are trying to duplicate this quote request as there is already an existing quote of same product and quantity waiting for acceptance";
                                 header('Content-Type: application/json');
                                    echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                    "msg" => $msg,
                                      )
                                 ); 
  }          
         
 
       
            
            
         
            
        }
        
        
        /**
         * This is the function that moves an escrow agreement form to its path
         */
        public function moveTheEscrowAgreementToItsPathAndReturnItsFileName($escrow_filename){
            
         
            if(isset($_FILES['escrow_agreement_file']['name'])){
                        $tmpName = $_FILES['escrow_agreement_file']['tmp_name'];
                        $escrowName = $_FILES['escrow_agreement_file']['name'];    
                        $escrowType = $_FILES['escrow_agreement_file']['type'];
                        $escrowSize = $_FILES['escrow_agreement_file']['size'];
                  
                   }
                    
                    if($escrowName !== null) {
                             if($escrow_filename != null || $escrow_filename != ""){
                                $escrowFileName = time().'_'.$escrow_filename;  
                            }else{
                                $escrowFileName = $escrow_filename;  
                            }
                          
                           // upload the icon file
                        if($escrowName !== null){
                            	$escrowPath = Yii::app()->params['escrow'].$escrowFileName;
				move_uploaded_file($tmpName,  $escrowPath);
                                        
                        
                                return $escrowFileName;
                        }else{
                            $escrowFileName = $escrow_filename;
                            return $escrowFileName;
                        } // validate to save file
                        
                      
                     }else{
                         $escrowFileName = $escrow_filename;
                         return $escrowFileName;
                     }
        }
        
        /**
         * This is the function that initiates esrow for a transaction
         */
        public function isNewEscrowSuccessfullyInitiated($product_id,$id,$escrow_agreement_filename,$is_quoted,$is_for_future,$status,$minimum_number_of_product_to_buy,$whats_product_per_item,$quantity,$escrow_request_from){
            
            $model = new Escrow;
            return $model->isNewEscrowSuccessfullyInitiated($product_id,$id,$escrow_agreement_filename,$is_quoted,$is_for_future,$status,$minimum_number_of_product_to_buy,$whats_product_per_item,$quantity,$escrow_request_from);
            
        }
        
        /**
         * This is the function that initiates transaction futures
         */
        public function isNewFuturesSuccessfullyInitiated($quote_id,$is_quoted,$is_escrowed,$status,$minimum_number_of_product_to_buy,$whats_product_per_item,$quantity,$delivery_month,$delivery_year,$payment_method,$staggered_payment_frequency){
            $model = new Futures;
            return $model->isNewFuturesSuccessfullyInitiated($quote_id,$is_quoted,$is_escrowed,$status,$minimum_number_of_product_to_buy,$whats_product_per_item,$quantity,$delivery_month,$delivery_year,$payment_method,$staggered_payment_frequency);
            
        }
        
        /**
         * This is the function that list all quotes in the platform
         */
        public function actionlistallquotes(){
            
           $quote = Quote::model()->findAll();
                if($quote===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"assigned" => $assigned,
                            "quote" => $quote)
                       );
                       
                }
        }
        
        
        /**
         * This is the function that retrieves all quotes initiated by a member
         */
        public function actionlistallquotesinitiatedByMember(){
            
            $member_id = Yii::app()->user->id;
          
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='quote_initiated_by=:id';
            $criteria->params = array(':id'=>$member_id);
            $quote= Quote::model()->findAll($criteria);
            
             if($quote===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "quote" => $quote)
                       );
                       
                }
        }
        
        
        
         /**
         * This is the function that retrieves all quotes recieved by a member
         */
        public function actionlistallquotesreceivedByMember(){
            
            $model = new ProductHasVendor;
            
            $member_id = Yii::app()->user->id;
          
            //get all the product that a member is a merchant of
            $merchant_products = $model->getAllTheProductThisMemberIsAMerchantOf($member_id);
            
            $all_quote_received = [];
            //retrieve all quotes
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='(status=:status or status=:accepted)';
            $criteria->params = array(':status'=>'live',':accepted'=>'accepted');
            $quotes= Quote::model()->findAll($criteria);
            
            foreach($quotes as $quote){
                if(in_array($quote['product_id'],$merchant_products)){
                    if($quote['status'] =='accepted'){
                        if($this->isThisQuoteResponseFromMember($quote['id'],$member_id)){
                            $all_quote_received[] = $quote;
                        }
                    }else{
                        $all_quote_received[] = $quote;
                    }
                    
                }
                
                
            }
            
             if($quote===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "quote" => $all_quote_received)
                       );
                       
                }
        }
        
        
        /**
         * This is the function that determines if a quotes response is from a member
         */
        public function isThisQuoteResponseFromMember($id,$member_id){
            $model = new Quote;
            return $model->isThisQuoteResponseFromMember($id,$member_id);
        }
        
        
        /**
         * This is the function that retrieves the futures associated to a quote
         */
        public function actionretrievethisquotefuturesinformation(){
            
            $quote_id = $_REQUEST['quote_id'];
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='(quote_id=:id)';
            $criteria->params = array(':id'=>$quote_id);
            $futures= Futures::model()->find($criteria);
            
             if($futures===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "futures" => $futures)
                       );
                       
                }
            
        }
        
        
        /**
         * This is the function that effects the modification of a quote
         * 
         */
        public function actioneffectTheModificationOfAQuote(){
            
           
            $_id = $_POST['quote_id'];
            $model=Quote::model()->findByPk($_id);

            //get the logged in user id
            $member_id = Yii::app()->user->id;
            
           
            
            
         if((isset($_POST['need_escrow_agreement']))){
             $model->is_escrowed = $_POST['need_escrow_agreement'];
         }else{
             $model->is_escrowed = 0;
         }
         
        
         if(isset($_POST['future_trading'])){
             $model->is_for_future = $_POST['future_trading'];
           }else{
             $model->is_for_future = 0;
            
         }
    
        $model->minimum_number_of_product_to_buy = $_POST['minimum_number_of_product_to_buy_for_computation'];
        $model->whats_product_per_item = strtolower($_POST['whats_represents_an_item']);
        $model->status = strtolower('live');
        $model->product_id = $_POST['product_id'];
        $model->direction = strtolower('initiation');
        $model->quantity = $_POST['quantity_of_purchase'];
        $model->quote_modification_date = new CDbExpression('NOW()');
        $model->quote_modified_by = Yii::app()->user->id;
       
            
        $escrow_file_error = 0;
         
 if($model->is_escrowed == 1){
     if($_FILES['escrow_agreement_file']['name'] != ""){
          if($_FILES['escrow_agreement_file']['type'] == 'application/pdf'){
                   $escrow_filename = $_FILES['escrow_agreement_file']['name'];
                   $escrow_filesize = $_FILES['escrow_agreement_file']['size'];
              }else{
                  $escrow_file_error = $escrow_file_error + 1;
                  
              }   
     }else{
         $escrow_filename = "";
     }
             
              
  if($escrow_file_error == 0){
        if($model->save()) {
                
           if($escrow_filename !=""){
               //move the escrow agreement file to the escrow directory 
                $escrow_agreement_filename = $this->moveTheEscrowAgreementToItsPathAndReturnItsFileName($escrow_filename);
           }else{
               $escrow_agreement_filename=$escrow_filename;
           }
            
            //write the escrow information to the escrow table
            if($this->isEscrowSuccessfullyUpdated($model->product_id,$_id,$escrow_agreement_filename,$_POST['requesting_for_a_quote'],$model->is_for_future,$model->status,$model->minimum_number_of_product_to_buy,$model->whats_product_per_item,$model->quantity)){
                    if($model->is_for_future == 1 || $model->is_for_future == true){
                        if($this->isFuturesSuccessfullyUpdated($_id,$_POST['requesting_for_a_quote'],$model->is_escrowed,$model->status,$model->minimum_number_of_product_to_buy,$model->whats_product_per_item,$model->quantity,strtolower($_POST['month_of_delivery']),strtolower($_POST['year_of_delivery']),strtolower($_POST['payment_type']),strtolower($_POST['payment_frequency']))){
                                            $msg = "The modification of this quote and that of both trade assurance and futures trading were successful.You may visit the escrow and futures tabs in the 'My Oneroof' section for details ";
                                                 header('Content-Type: application/json');
                                                 echo CJSON::encode(array(
                                                 "success" => mysql_errno() == 0,
                                                 "msg" => $msg)
                                                );
                                }else{
                                        $msg = "The modification of this  quote and that of escrow facilities were successful. However,Futures facility modification was not successful.You need to contact customer care for assistance or visit the futures tab in the 'My Oneroof' section to manually effect the futures facility modification";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                                } 
                    }else{
                       if($this->isQuoteAlreadyAssociatedWithFutures($_id)){
                            if($this->isTheRemovalOfAssociatedFuturesSuccessful($_id)){
                                 $msg = "The modification of this quote was done successfully .";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                            }else{
                                $msg = "The removal of associated futures to this quote was not successful. However, both the quote and escrow information were updated successfully. Please Visit the futures tab in the 'My Oneroof' section to manually remove the associated futures or call customer care for assistance";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                            }
                            
                        }else{
                             $msg = "The modification of both the quote and the associated escrow were successful.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                        }
                    }
            }else{
                   if($model->is_for_future == 1 || $model->is_for_future == true){
                        if($this->isFuturesSuccessfullyUpdated($_id,$_POST['requesting_for_a_quote'],$model->is_escrowed,$model->status,$model->minimum_number_of_product_to_buy,$model->whats_product_per_item,$model->quantity,strtolower($_POST['month_of_delivery']),strtolower($_POST['year_of_delivery']),strtolower($_POST['payment_type']),strtolower($_POST['payment_frequency']))){
                                            $msg = "The modification of the quote and its associated futures facility were successful. However, there was an error while trying to effect the modification of the escrow facility. You may need to visit the the escrow tab in the 'My Oneroof' section to manually effect the escrow modification or contact customer care for assistance";
                                                 header('Content-Type: application/json');
                                                 echo CJSON::encode(array(
                                                 "success" => mysql_errno() == 0,
                                                 "msg" => $msg)
                                                );
                                }else{
                                        $msg = "The modification of this quote was successful. However, there were errors in the modification of both the quote's associated escrow and futures facilities .Please visit both the escrow and futures tab in the 'My Oneroof' section to manually effect theie modifications or contact customer care for assistance";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                                } 
                    }else{
                        if($this->isQuoteAlreadyAssociatedWithFutures($_id)){
                            if($this->isTheRemovalOfAssociatedFuturesSuccessful($_id)){
                                 $msg = "The modification of this quote was done successfully .However, the escrow facility modification was not successful. Please Visit the Escrow tab in the  'My Oneroof' section to manually effect the escrow modification";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                            }else{
                                $msg = "The removal of associated futures to this quote was not successful. However, the quote update was successfully carried out.Please Visit the futures tab in the 'My Oneroof' section to manually remove the associated futures or call customer care for assistance";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                            }
                            
                        }else{
                             $msg = "The modification of this quote was successful.However, the escrow facility could not be modified as intended. Please Visit the Escrow tab in the 'My Oneroof' section to manually effect the modification or call customer care for assistance";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                        }
                       
                    }
                
            }
                                    
                                    
       }else{
                 $msg = "The modification of this quote was not successful. Please try again";
                                 header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() != 0,
                                  "msg" => $msg)
                                    ); 
                
       } 
             
             
         }else{
              $msg = "There was an error in the escrow agreement file uploaded. Please ensure you are uploading a .pdf file";
                                 header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() != 0,
                                  "msg" => $msg)
                                    ); 
         }     
  
    }else if($model->is_escrowed == 0){
             if($model->save()) {
                 if($this->isThereAnAssociatedEscrowToThisQuote($_id)){
                     if($this->isTheRemovalOfThisAssociatedEscrowSuccessful($_id)){
                         if($model->is_for_future == 1 || $model->is_for_future == true){
                          if($this->isFuturesSuccessfullyUpdated($_id,$_POST['requesting_for_a_quote'],$model->is_escrowed,$model->status,$model->minimum_number_of_product_to_buy,$model->whats_product_per_item,$model->quantity,strtolower($_POST['month_of_delivery']),strtolower($_POST['year_of_delivery']),strtolower($_POST['payment_type']),strtolower($_POST['payment_frequency']))){
                              $msg = "The modification of quote and futures trading information were successful.";
                                 header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() == 0,
                                  "msg" => $msg)
                                    );
                         }else{
                           $msg = "The modification of this quote was successful. However, the futures information were not updated. Please visit the Futures tab in the 'My Oneroof' section to manually effect an update on this quote associated futures";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                             
                            
                         }
                          
                      }else{
                          if($this->isQuoteAlreadyAssociatedWithFutures($_id)){
                            if($this->isTheRemovalOfAssociatedFuturesSuccessful($_id)){
                                 $msg = "The modification of this quote was done successfully .";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                            }else{
                                $msg = "The removal of associated futures to this quote was not successful. However, the quote update was successfully carried out.Please visit the futures tab in the 'My Oneroof' section to manually remove the associated futures or call customer care for assistance";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                            }
                            
                        }else{
                             $msg = "The modification of this quote was successful.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                        }
                      }
                         
                     }else{
                         if($model->is_for_future == 1 || $model->is_for_future == true){
                          if($this->isFuturesSuccessfullyUpdated($_id,$_POST['requesting_for_a_quote'],$model->is_escrowed,$model->status,$model->minimum_number_of_product_to_buy,$model->whats_product_per_item,$model->quantity,strtolower($_POST['month_of_delivery']),strtolower($_POST['year_of_delivery']),strtolower($_POST['payment_type']),strtolower($_POST['payment_frequency']))){
                              $msg = "The modification of quote and futures trading information were successful.";
                                 header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() == 0,
                                  "msg" => $msg)
                                    );
                         }else{
                           $msg = "The modification of this quote was successful. However, the futures information were not updated. Please visit the Future tab in the 'My Oneroof' section to manually effect an update on this quote associated futures";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                             
                            
                         }
                          
                      }else{
                          if($this->isQuoteAlreadyAssociatedWithFutures($_id)){
                            if($this->isTheRemovalOfAssociatedFuturesSuccessful($_id)){
                                 $msg = "The modification of this quote was done successfully .However, the futures trading facility modification was not successful. Please Visit the Futures tab in the  'My Oneroof' section to manually effect the modification on the associated futures";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                            }else{
                                $msg = "The removal of associated futures to this quote was not successful. However, the quote update was successfully carried out.Please visit the futures tab in the 'My Oneroof' section to manually remove the associated futures or call customer care for assistance";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                            }
                            
                        }else{
                             $msg = "The modification of this quote was successful.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                        }
                      }
                     }
                 }else{
                     if($model->is_for_future == 1 || $model->is_for_future == true){
                          if($this->isFuturesSuccessfullyUpdated($_id,$_POST['requesting_for_a_quote'],$model->is_escrowed,$model->status,$model->minimum_number_of_product_to_buy,$model->whats_product_per_item,$model->quantity,strtolower($_POST['month_of_delivery']),strtolower($_POST['year_of_delivery']),strtolower($_POST['payment_type']),strtolower($_POST['payment_frequency']))){
                              $msg = "The modification of quote and futures trading information were successful.";
                                 header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() == 0,
                                  "msg" => $msg)
                                    );
                         }else{
                           $msg = "The modification of this quote was successful. However, the futures information were not updated. Please visit the Future tab in the 'My Oneroof' section to manually effect an update on this quote associated futures";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                             
                            
                         }
                          
                      }else{
                          if($this->isQuoteAlreadyAssociatedWithFutures($_id)){
                            if($this->isTheRemovalOfAssociatedFuturesSuccessful($_id)){
                                 $msg = "The modification of this quote was done successfully .However, the futures trading facility modification was not successful. Please Visit the Futures tab in the  'My Oneroof' section to manually effect the modification on the associated futures";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                            }else{
                                $msg = "The removal of associated futures to this quote was not successful. However, the quote update was successfully carried out.Please visit the futures tab in the 'My Oneroof' section to manually remove the associated futures or call customer care for assistance";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                            }
                            
                        }else{
                             $msg = "The modification of this quote was successful.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                        }
                      }
                 }
                      
                 
             }else{
                 $msg = "There was an error modifying this quote.Please try again";
                                 header('Content-Type: application/json');
                                    echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                    "msg" => $msg,
                                        )
                                 ); 
             }
         
             
         }
            
            
        }
        
        
        /**
         * This is the function that confirms if a futures facility is associated with  a quote
         */
        public function isQuoteAlreadyAssociatedWithFutures($quote_id){
            $model = new Futures;
            return $model->isQuoteAlreadyAssociatedWithFutures($quote_id);
            
        }
        
        /**
         * This is the function that confirms if an escrow facility is associated with a quote
         */
        public function isThereAnAssociatedEscrowToThisQuote($quote_id){
            $model = new Escrow;
            return $model->isThereAnAssociatedEscrowToThisQuote($quote_id);
        }
        
        /**
         *This is the function that confirms the success of the removal of a futures facility of a quote
         */
        public function isTheRemovalOfAssociatedFuturesSuccessful($quote_id){
            $model = new Futures;
            return $model->isTheRemovalOfAssociatedFuturesSuccessful($quote_id);
        }
        
        /**
         * This is the function that confirms the success of the removal of an escrow facility of a quote 
         */
        public function isTheRemovalOfThisAssociatedEscrowSuccessful($quote_id){
            $model = new Escrow;
            return $model->isTheRemovalOfThisAssociatedEscrowSuccessful($quote_id);
        }
        
        
        /**
         * This is the function that effects the modification of an escrow that is associated to a quote
         */
        public function isEscrowSuccessfullyUpdated($product_id,$quote_id,$escrow_agreement_filename,$requesting_for_a_quote,$is_for_future,$status,$minimum_number_of_product_to_buy,$whats_product_per_item,$quantity){
            
            $model = new Escrow;
            return $model->isEscrowSuccessfullyUpdated($product_id,$quote_id,$escrow_agreement_filename,$requesting_for_a_quote,$is_for_future,$status,$minimum_number_of_product_to_buy,$whats_product_per_item,$quantity);
        }
        
        
        /**
         * This is the function that effects the modification of a futures facility that is associated to a quote
         */
        public function isFuturesSuccessfullyUpdated($quote_id,$requesting_for_a_quote,$is_escrowed,$status,$minimum_number_of_product_to_buy,$whats_product_per_item,$quantity,$month_of_delivery,$year_of_delivery,$payment_type,$payment_frequency){
            $model = new Futures;
            return $model->isFuturesSuccessfullyUpdated($quote_id,$requesting_for_a_quote,$is_escrowed,$status,$minimum_number_of_product_to_buy,$whats_product_per_item,$quantity,$month_of_delivery,$year_of_delivery,$payment_type,$payment_frequency);
            
        }
        
        
        /**
         * This is the function that cancels a quote request
         */
        public function actioncancelThisQuote(){
            
            $quote_id = $_POST['quote_id'];
            
            if($this->isThereAnAssociatedEscrowToThisQuote($quote_id)){
                if($this->isTheRemovalOfThisAssociatedEscrowSuccessful($quote_id)){
                    if($this->isQuoteAlreadyAssociatedWithFutures($quote_id)){
                        if($this->isTheRemovalOfAssociatedFuturesSuccessful($quote_id)){
                            if($this->isTheCancellationOfThisQuoteSuccessful($quote_id)){
                                //all of escrow, futures and quotes cancelled
                                $msg = "This quote and the associated escrow and futures facilities were successfully cancelled.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    ); 
                            }else{
                                //escrow and futures cancelled but not quote
                                 $msg = "This quote was not cancelled but its associated escrow qnd futures facilities were successfully cancelled.Please contact customer care for assistance";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    );
                            }
                            
                        }else{
                             if($this->isTheCancellationOfThisQuoteSuccessful($quote_id)){
                                //escrow and quote cancel. futures cancellation failed
                                 $msg = "This quote and its associated escrow were cancelled successfully. However, the future facility was not cancelled.You may need to contact the customer care for assistance or visit the futures tab in 'My Oneroof' to manually cancel the associated futures facility";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    );
                            }else{
                                //escrow  cancelled but not quote and futures
                                 $msg = "The cancellation of escrow was successful. However, the cancellation of both quote and its futures facility were not successful.You may need to contact the customer care for assistance.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    );
                            }
                        }
                    }else{
                        if($this->isTheCancellationOfThisQuoteSuccessful($quote_id)){
                                //both escrow and quote cancelled
                            $msg = "The cancellation of this quote and it associated escrow was successful.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    );
                            
                            }else{
                                //escrow  cancelled but not quote as there are no futures
                                $msg = "The cancellation of this quote was not successful. However its associated escrow was cancelled. You may need to contact customer care for assistance";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    );
                            }
                    }
                }else{
                     if($this->isQuoteAlreadyAssociatedWithFutures($quote_id)){
                        if($this->isTheRemovalOfAssociatedFuturesSuccessful($quote_id)){
                            if($this->isTheCancellationOfThisQuoteSuccessful($quote_id)){
                                //futures and quotes cancelled but not escrow
                                $msg = "The cancellation of this quote and its associated futures facility was successful.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    );
                            }else{
                                //futures cancelled but not quote and escrow
                                $msg = "The cancellation of this quote was not successful. However, the associated futures facility was duly cancelled. Contact customer care for assistance.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    );
                            }
                            
                        }else{
                             if($this->isTheCancellationOfThisQuoteSuccessful($quote_id)){
                                //quote cancel. futures and escrow cancellation failed
                                  $msg = "The cancellation of this quote was successful. However, the associated futures facility was not cancelled. Contact customer care for assistance or visit the futures tab in 'My Oneroof' section to manually effect the cancellation.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    );
                            }else{
                                //all cancellation failed
                                    $msg = "The cancellation of this quote failed. Please contact customer care for assistance.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    );
                            }
                        }
                    }else{
                        if($this->isTheCancellationOfThisQuoteSuccessful($quote_id)){
                                //both futures and escrow cancellation failed.quote cancellation was a success
                            $msg = "The cancellation of this quote was success. However. that cancellation of the associated escrow and futures facility was not succesful.Please contact customer care for assistance or visit the escrow and futures tab in the 'My Oneroof' section to manually effect the cancellation.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    );
                                
                            }else{
                                //all cancellation failed
                                $msg = "The cancellation of this quote failed. Please contact customer care for assistance.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    );
                            }
                    }
                }
            }else{
                 if($this->isQuoteAlreadyAssociatedWithFutures($quote_id)){
                        if($this->isTheRemovalOfAssociatedFuturesSuccessful($quote_id)){
                            if($this->isTheCancellationOfThisQuoteSuccessful($quote_id)){
                                //futures and quotes cancelled but not escrow
                                $msg = "The cancellation of this quote and its associated futures facility was successful.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    );
                            }else{
                                //futures cancelled but not quote 
                                $msg = "The cancellation of this quote was not successful. However its associated futures facility was successfully cancelled.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    );
                            }
                            
                        }else{
                             if($this->isTheCancellationOfThisQuoteSuccessful($quote_id)){
                                //quote cancel. futures cancellation failed
                                  $msg = "The cancellation of this quote was successful but not the cancellation of its associated futures facility. You may need to visit the furures tab in the 'My Oneroof' section to manually cancel the futures facility.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    );
                            }else{
                                //all cancellation failed
                               $msg = "The cancellation of this quote failed. Please contact customer care for assistance.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    );
                            }
                        }
                    }else{
                        if($this->isTheCancellationOfThisQuoteSuccessful($quote_id)){
                                //both futures and escrow cancellation failed.quote cancellation was a success
                                $msg = "The cancellation of this quote was successful";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    );
                                
                            }else{
                                //all cancellation failed
                                $msg = "The cancellation of this quote failed. Please contact customer care for assistance.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    );
                            }
                    }
            }
            
           
            
        }
        
         /**
             * This is the function that effects the cancellation of a quote
             */
            public function isTheCancellationOfThisQuoteSuccessful($quote_id){
                
                $model = new Quote;
                return $model->isTheCancellationOfThisQuoteSuccessful($quote_id);
            }
            
            
            /**
             * This is the function that sends a quotation request to a quote respect
             */
            public function actionsendingQuotationToThisQuoteRequest(){
                $model = new QuoteHasResponse;
                
                $member_id = Yii::app()->user->id;
                
                $quote_id = $_POST['quote_id'];
                if($_POST['need_escrow_agreement'] == 'true'){
                    $is_quote_escrowed =$_POST['accept_escrow'];
                }else{
                    $is_quote_escrowed = 0;
                }
                if($_POST['future_trading'] == 'true'){
                    $is_quote_for_future = $_POST['accept_futures'];
                }else{
                    $is_quote_for_future = 0;
                }
                $total_amount_quoted = $_POST['total_amount_quoted'];
                $is_platform_quotation_terms_accepted = $_POST['terms_and_conditions'];
                $status = strtolower('pending');
                
                $quotation_file_error = 0;
                $video_error_counter = 0;
                
                
               
        
             if($_FILES['quotation_file']['name'] != ""){
                 
                 if($_FILES['quotation_file']['type'] == 'application/pdf'){
                   $quotation_filename = $_FILES['quotation_file']['name'];
                   $quotation_filesize = $_FILES['quotation_file']['size'];
              }else{
                  $quotation_file_error = $quotation_file_error + 1;
                  
              }
              
              //upload the video file
              if(isset($_POST['is_with_video'])){
                  $is_with_video = 1;
                    if($_FILES['video_filename']['name'] != ""){
                    if($model->isVideoFileTypeAndSizeLegal()){
                        
                       $video_filename = $_FILES['video_filename']['name'];
                       $video_size = $_FILES['video_filename']['size'];
                        
                    }else{
                       
                        $video_error_counter = $video_error_counter + 1;
                         
                    }//end of the video size and type statement
                }else{
                   $video_filename = null;
                   $video_size = 0;
             
                }
                    
                }else{
                    $is_with_video = 0;
                }
              
              
              
                
               if($quotation_file_error == 0 and $video_error_counter == 0){
                   
                    if($this->isMonthlyLimitOfMemberNotReached($member_id,$total_amount_quoted)){
                    if($this->isMemberWithinDailyTransactionLimited($member_id,$total_amount_quoted)){
                        
                       if($model->hasMemberNotAlreadyRespondedToThisQuote($member_id,$quote_id) == false){
                           $quotation_file = $model->moveTheQuotationAgreementToItsPathAndReturnItsFileName($quotation_filename);
                           $video_filename = $model->moveTheQuotationVideoToItsPathAndReturnItsName($video_filename);
                           if($model->isTheSendingOfThisQuotationASuccess($quote_id,$is_quote_escrowed,$is_quote_for_future,$total_amount_quoted,$is_platform_quotation_terms_accepted,$status,$quotation_file,$video_filename,$is_with_video,$member_id)){
                                //sending quotation was successful
                                $msg = "Your quotation had been received. You will be contacted if your quotation is accepted.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    );
                            }else{
                                //sending quotation was not successful
                                $msg = "Your quotation had not been received. Its possible there are some validation issues in your form.Please try again";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                    );
                            }
                           
                       }else{
                           
                           //member had already responded to this quote
                            $msg = "You had already responded to this request before and cannot send another. However, you can always modify your quotation and resend provided the requester had not made a choice yet.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                    );
                       }
                       
                        
                    }else{
                        //exceeding member daily transaction limit
                        $msg = "This quotation could not be received as you had reached your daily limit. You can upgrade your membership and resend.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                    );
                    }
                }else{
                    //member monthly limit had reached
                     $msg = "This quotation could not be received as you had reached your monthly limit. You can upgrade your membership and resend.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                    );
                }
                   
                   
               }else if($quotation_file_error>0){
                  //non pdf file was uploaded 
                   $msg = "Your quotation file could not be uploaded because it is not in the required .pdf format. Please, reformat your quotation file to the required format and try again.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                    );
               }else if($video_error_counter>0){
                    $msg = "Your sales pitch video file could not be uploaded because it could be the size is too large or you are uploading a non-mp4 video file";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                    );
               }
                 
                 
             }else{
                 
                  //quotation file cannot be empty
                 $msg = "Your quotation file field is required and cannot be empty. Please attach the required quotation file in .pdf format and try again.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                    );
             }
             
            }
            
            
            /**
             * This is the function that determines if a members monthly limit has not been exceeded
             */
            public function isMonthlyLimitOfMemberNotReached($member_id,$total_amount_quoted){
                $model = new PlatformSettings;
                //return $model->isMonthlyLimitOfMemberNotReached($member_id,$total_amount_quoted);
                return true;
            }
            
            
            /**
             * This is the function that determines if a member is within his daily transaction limit 
             */
            public function isMemberWithinDailyTransactionLimited($member_id,$total_amount_quoted){
                $model = new PlatformSettings;
                //return $model->isMemberWithinDailyTransactionLimited($member_id,$total_amount_quoted);
                return true;
            }
            
            
            /**
             * This is the function that list all responses to a quote
             */
            public function actionlistAllResponsesToAQuote(){
                
                $quote_id = $_REQUEST['quote_id'];
                
                  $criteria = new CDbCriteria();
                  $criteria->select = '*';
                  $criteria->condition='quote_id=:id';
                  $criteria->params = array(':id'=>$quote_id);
                  $responses= QuoteHasResponse::model()->findAll($criteria);
                  
                  if($responses===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "quote" => $responses)
                       );
                       
                }
                  
            }
            
            /**
             * This is the function that retrieves the codes information
             */
            public function actionretrievequoteinformation(){
                $quote_id = $_REQUEST['quote_id'];
                $member_id = Yii::app()->user->id;
                  $criteria = new CDbCriteria();
                  $criteria->select = '*';
                  $criteria->condition='id=:id';
                  $criteria->params = array(':id'=>$quote_id);
                  $quote= Quote::model()->find($criteria);
                  
                  
                  //get the quote futures information too
                  
                  $criteria1 = new CDbCriteria();
                  $criteria1->select = '*';
                  $criteria1->condition='(quote_id=:id)';
                  $criteria1->params = array(':id'=>$quote_id);
                  $futures= Futures::model()->find($criteria1);
                  
                  //get the quote accepted response
                  $criteria2 = new CDbCriteria();
                  $criteria2->select = '*';
                  $criteria2->condition='(quote_id=:quoteid and status=:status)';
                  $criteria2->params = array(':quoteid'=>$quote_id,':status'=>'accepted');
                  $response= QuoteHasResponse::model()->find($criteria2);
                  
                  //retrieve the escrow id for this quote
                  $criteria1 = new CDbCriteria();
                  $criteria1->select = '*';
                  $criteria1->condition='(quote_id=:id)';
                  $criteria1->params = array(':id'=>$quote_id);
                  $escrow= Escrow::model()->find($criteria1);
                  
                  
                   if($quote===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "quote" => $quote,
                           "futures"=>$futures,
                           "response"=>$response,
                           "escrow"=>$escrow,
                               )
                       );
                       
                }
                  
                
            }
            
            
            /**
             * This is the function that accepts a quotation
             */
            public function actionacceptingThisMemberQuotation(){
                
               $model = new Quote;
                
                $quote_id = $_POST['quote_id'];
                
                $member_id = $_POST['quote_responder'];
                
                $product_name = $this->getThisProductName($_POST['product_id']);
                
                if($model->hasDecisionBeenMadeOnThisQuoteRequest($quote_id)== false){
                    if($this->isAcceptingThisQuoteResponseASuccess($quote_id,$member_id)){
                        if($model->isTakingAcceptanceDecisionOnThisQuoteASuccess($quote_id)){
                            //acceptance decision has successfully been taken on this quote
                            if($this->isTheAdditionOfThisQuoteToCartSuccessful($quote_id)){
                                $msg = "Your acceptance of this quotation had been recognized. The quote is now in your cart for payment";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    );
                            }else{
                                $msg = "Your acceptance of this quotation had been recognized but the quote could not be added to the cart automatically. You may manually add this quote to the cart and subsequently effect payment";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    );
                            }
                            
                        }else{
                            //could not make the acceptance decision on this quote
                             $msg = "Your acceptance of this quotation could not be recognized.Its possible that the quote resquest no longer exist. Please contact customer care for detail";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                    );
                        }
                    }else{
                        //accepting quote response was not successful
                        $msg = "Your acceptance of this quotation could not be recognized zzzz.It is possible that this quote reponse no longer exist. Please contact customer care for detail";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                    );
                    }
                    
                }else{
                    //get the status of the quote
                    $quote_current_status = $model->getThisQuoteCurrentStatus($quote_id);
                    //decision had already been made on this quote
                    $msg = "Your acceptance of this quotation could not be recognized as decision had already been made on this quote request. Please contact customer care for detail";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                    );
                }
            }
            
            
            /**
             * This is the function that automatically adds the accepted quotation to cart
             */
            public function isTheAdditionOfThisQuoteToCartSuccessful($quote_id){
                $model = new QuoteHasResponse;
                return $model->isTheAdditionOfThisQuoteToCartSuccessful($quote_id);
            }
            
            /**
             * This is the function that accepts a quote response
             */
            public function isAcceptingThisQuoteResponseASuccess($quote_id,$member_id){
                $model = new QuoteHasResponse;
                return $model->isAcceptingThisQuoteResponseASuccess($quote_id,$member_id);
            }
            
            
            /**
             * This is the function that rejects a quote response
             */
            public function isRejectingThisQuoteResponseASuccess($quote_id,$member_id){
                $model = new QuoteHasResponse;
                return $model->isRejectingThisQuoteResponseASuccess($quote_id,$member_id);
            }
            
            
            /**
             * This is the function that retrieves a product name
             */
            public function getThisProductName($product_id){
                $model = new Product;
                return $model->getThisProductName($product_id);
            }
            
            
            /**
             * This is the function that rejects a quotation
             */
            public function actionrejectingThisMemberQuotation(){
                
                $model = new Quote;
                
                $quote_id = $_POST['quote_id'];
                
                 $member_id = $_POST['quote_responder'];
                
                $product_name = $this->getThisProductName($_POST['product_id']);
                
                if($model->hasDecisionBeenMadeOnThisQuoteRequest($quote_id)== false){
                    if($this->isRejectingThisQuoteResponseASuccess($quote_id,$member_id)){
                        $msg = "Your rejection of this quotation had been recognized.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                    );
             
                    }else{
                        $msg = "The attempt to reject this quotation had not been successful.It possible the quotation no longer exist.";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                    );
                    }
             
                }else{
                    $quote_current_status = $model->getThisQuoteCurrentStatus($quote_id);
                    //decision had already been made on this quote
                    $msg = "Your rejection of this quotation could not be recognized as decision had already been made on this quote request. Please contact customer care for detail";
                                            header('Content-Type: application/json');
                                            echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                    );
                }
            }
            
            
            /**
             * This is the function that retreives a quote response
             */
            public function actionretrieveThisQuotationFile(){
                
                 $quote_id = $_REQUEST['quote_id'];
                 $member_id = $_REQUEST['member_id'];
                
                  $criteria = new CDbCriteria();
                  $criteria->select = '*';
                  $criteria->condition='quote_id=:quoteid and member_id=:memberid';
                  $criteria->params = array(':quoteid'=>$quote_id,':memberid'=>$member_id) ;
                  $quote= QuoteHasResponse::model()->find($criteria);
                  
                 if($quote===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "quote" => $quote,
                          
                               )
                       );
                       
                }
                  
                  
            }
}
