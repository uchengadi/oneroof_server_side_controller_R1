<?php

class ProductController extends Controller
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
				'actions'=>array('index','view','ListAllProducts','retrieveProductDetails','retrieveproductinformation',
                                    'confirmPriceValidity','obtainPrductExtraInformation','listProductsInAHamper','ListAllProductsForHampers','listAllProductsInAHamper',
                                    'retrievethedetailofproductinhamper','getExtraInformationAboutAHamper','listProductsInAHamper','retrieveAllProductsPurchasedByAUser',
                                    'ListAllProductsForAService','ListAllProductsForACategory','ListAllProductsForAType','ListAllProductsOnSales',
                                    'ListAllProductsLessThan1000','ListAllRentableProducts','ListAllProductsForSubscription','ListAllBookProductsType','retrieveTheMiddlePageAdvertProduct',
                                    'ListAllBasicBookProductsType','ListAllJssBookProductsType','ListAllSssBookProductsType','ListAllTertiaryBookProductsType','ListAllProfessionalBookProductsType',
                                    'ListAllOtherBookProductsType','ListAllLearningTools','ListAllProductsForAFaasCategory','ListAllProductsForAFaasType','modifyStockQuantity',
                                    'modifyProductMiddleAdvertPlacementStatus'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('obtainPrductExtraInformation','DeleteThisProduct','createNewProduct','updateProduct',
                                    'retrieveProductDetails','retrieveproductinformation','confirmPriceValidity',
                                    'ListMemberProductsInCart','removeThisProductFromCart','saveChangesToThisProductInTheCart',
                                    'ListMemberExistingOrders','ListMemberOrdersNotExceedingSixMonths','ListMemberOrdersBeyondSixMonths',
                                    'retrieveproducthistoryinformation','zerorisethisproductprice','requestToTradeOnProduct','requestForProductSubscription',
                                    'requestForPreProductSubscription','retrieveTheIdOfThisProduct','listAllProductsInAHamper','removeThisProductFromThisVendor',
                                    'unsubscribeFromThisProduct','ScheduleTheDeliveryOfThisProductForAMember','drawdownOnASubscribedProduct','escrowThisSubscribedProduct',
                                    'toppingUpThisSubscribedProduct','confirmIfThisProductIsSubscribable','retrieveproductfuturesinformation','listallProductsInThisCategory',
                                    'creatingOwnHamper','listAllCustomHampersByThisMember','editingOwnHamper','RemovingHamper',
                                    'NonConnectedMemberBeneficiariesForAHamper','connectedMemberBeneficiariesForAHamper','addingAConnectedMemberAsHamperBeneficiary',
                                    'addANonConnectedMemberAsHamperBeneficiary','updateAConnectedMemberAsHamperBeneficiary','updateANonConnectedMemberAsHamperBeneficiary',
                                    'removeThisHamperBeneficiary','listProductsInAHamper','addingAProductToHamper','removingAProductFromHamper',
                                    'getExtraInformationAboutAHamper','ListAllProductsForHampers','retrievethedetailofproductinhamper',
                                    'getInformationAboutAHamperForCart','listAllHampersThatAMemberIsABeneficiary','getInformationAboutAHamper',
                                    'sendingHamperToHamperManager','changingTheQuantityOfAHamperItem','ListAllProductsInTheStore',
                                    'confirmTheExistenceOfAHamperInTheCart','getTheProductCode','ListEveryProducts','listAllNoncustomHampers','getTheQuantityOfThisProductInThisHamper',
                                    'getTheNewTotalCostForTheHamper','DisplayThisHamperOnStore'),
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
         * This is the function that retrieves extra information on products
         */
        public function actionobtainPrductExtraInformation(){
            
            $category_id = $_REQUEST['category_id'];
            $service_id = $_REQUEST['service_id'];
            $product_type_id = $_REQUEST['product_type_id'];
            $product_id = $_REQUEST['product_id'];
            
            $category_name = $this->getThisCategoryName($category_id);
            $service_name = $this->getThisServiceName($service_id);
            $product_type_name = $this->getThisProductTypeName($product_type_id);
           // $measurement_symbol = $this->getThisMeasurementSymbolName($measurement_symbol_id);
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$product_id);
            $product= Product::model()->find($criteria);
            
            //get the keywords for this product
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='product_id=:id';
            $criteria->params = array(':id'=>$product_id);
            $keyword= Keywords::model()->findAll($criteria);
            
            //get the size of this array
            $keyword_size = sizeof($keyword);
                   
            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "category" => $category_name,
                                        "service"=>$service_name,
                                        "producttype"=>$product_type_name,
                                        "product"=> $product,
                                        "keyword"=>$keyword,
                                        "size"=>$keyword_size
                                
                            ));
            
            
        }
        
        
        /**
         * This is the function that retrieves the category name
         */
        public function getThisCategoryName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $name = Category::model()->find($criteria);
            
            return $name['name'];
            
            
        }
        
        
        
         /**
         * This is the function that retrieves the product type name
         */
        public function getThisProductTypeName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $name = ProductType::model()->find($criteria);
            
            return $name['name'];
            
            
        }
        
        
        
        /**
         * This is the function that retrieves the service name
         */
        public function getThisServiceName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $name = Service::model()->find($criteria);
            
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
         * This is the function that deletes a product
         */
        public function actionDeleteThisProduct(){
            
            $_id = $_POST['id'];
            $model=Product::model()->findByPk($_id);
            
            //get the currency name
            $product_name = $this->getThisProductName($_id);
            if($model === null){
                 $msg = 'No Such Record Exist'; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                          
                           
                           
                          
                       ));
                                      
            }else if($model->delete()){
                    $msg = "'$product_name' Product had been deleted successfully"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                           
                           
                           
                          
                       ));
                       
            } else {
                    $msg = "Validation Error: '$product_name' Product was not deleted"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            //"selected" => $selected,
                            "msg" => $msg
                           
                           
                           
                          
                       ));
                            
                }
            
        }
        
        
        /**
         * This is the function that adds a product
         */
        public function actioncreateNewProduct(){
            
            $model=new Product;
            
            
            //get the logged in user id
            $userid = Yii::app()->user->id;
                     
            $model->name = $_POST['name'];
            if(isset($_POST['description'])){
                $model->description = $_POST['description'];
            }
          /**  if(isset($_POST['description_1'])){
                $model->description_1 = $_POST['description_1'];
            }
             if(isset($_POST['description_2'])){
                $model->description_2 = $_POST['description_2'];
            }
             if(isset($_POST['description_3'])){
                $model->description_3 = $_POST['description_3'];
            }
             if(isset($_POST['description_4'])){
                $model->description_4 = $_POST['description_4'];
            }
           * 
           */
            
            if(is_numeric($_POST['service'])){
                $model->service_id = $_POST['service'];
            }else{
                $model->service_id = $_POST['service_id'];
            }
            if(is_numeric($_POST['category'])){
                $model->category_id = $_POST['category'];
            }else{
                $model->category_id = $_POST['category_id'];
            }
            if(is_numeric($_POST['product_type'])){
                $model->product_type_id = $_POST['product_type'];
            }else{
               $model->product_type_id = $_POST['product_type_id']; 
            }
            if(isset($_POST['condition'])){
                $model->condition = $_POST['condition'];
            }
           if(isset($_POST['is_quotable'])){
                $model->is_quotable = $_POST['is_quotable'];
                $model->prevailing_retail_selling_price = 0;
                $model->per_portion_price = 0;
            }else{
                $model->is_quotable = 0;
                if(isset($_POST['prevailing_retail_selling_price'])){
                    $model->prevailing_retail_selling_price = $_POST['prevailing_retail_selling_price'];
                }else{
                    $model->prevailing_retail_selling_price = 0;
                }
                if(isset($_POST['per_portion_price'])){
                    $model->per_portion_price = $_POST['per_portion_price'];
                }else{
                    $model->per_portion_price = 0;
                }
            }
            if(isset($_POST['is_available'])){
                $model->is_available = $_POST['is_available'];
            }else{
                $model->is_available = 0;
            }
             if(isset($_POST['is_escrowable'])){
                $model->is_escrowable = $_POST['is_escrowable'];
            }else{
                $model->is_escrowable = 0;
            }
             if(isset($_POST['is_future_tradable'])){
                $model->is_future_tradable = $_POST['is_future_tradable'];
            }else{
                $model->is_future_tradable = 0;
            }
           // $model->cumulative_selling_price = $this->generateTheCumulativeSellingPrice($model->per_portion_price, $model->maximum_portion);
           if(isset($_POST['feature'])){
               $model->feature = $_POST['feature'];
           } 
         if(isset($_POST['feature_1'])){
               $model->feature_1 = $_POST['feature_1'];
           } 
            if(isset($_POST['feature_2'])){
               $model->feature_2 = $_POST['feature_2'];
           } 
            if(isset($_POST['feature_3'])){
               $model->feature_3 = $_POST['feature_3'];
           } 
            if(isset($_POST['feature_4'])){
               $model->feature_4 = $_POST['feature_4'];
           } 
         
            if(isset($_POST['specifications'])){
               $model->specifications = $_POST['specifications'];
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
              $model->cumulative_quantity = $_POST['quantity']; 
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
          //  $model->discount_rate = $this->generateTheDiscountRate($model->prevailing_retail_selling_price,$model->per_portion_price);
        
            if(isset($_POST['displayable_on_store'])){
                $model->displayable_on_store = $_POST['displayable_on_store'];
            }else{
                $model->displayable_on_store = 0;
            }
            if(isset($_POST['is_a_hamper'])){
                $model->is_a_hamper = $_POST['is_a_hamper'];
                $model->hamper_cost_limit = $_POST['prevailing_retail_selling_price'];
            }else{
                $model->is_a_hamper = 0;
            }
            if(isset($_POST['is_payment_permitted_on_delivery'])){
                $model->is_payment_permitted_on_delivery = $_POST['is_payment_permitted_on_delivery'];
            }else{
                $model->is_payment_permitted_on_delivery = 0;
            }
             if(isset($_POST['is_with_video'])){
                $model->is_with_video = $_POST['is_with_video'];
                $model->video_for = strtolower($_POST['video_for']);
            }else{
                $model->is_with_video = 0;
            }
           $model->weight = $_POST['weight']; 
           $model->code = $model->generateAProductCode($model->service_id);
       /**  $model->leadscode = $_POST['leadscode']; 
           $model->is_leads_topmost = $_POST['is_leads_topmost']; 
           $model->leads_version = $_POST['leads_version']; 
           if(isset($_POST['auto'])){
               $model->leads_level = $model->getTheNextLevelNumberOfThisLeadTree($_POST['leadscode']);
           }else{
              $model->leads_level = $_POST['leads_level']; 
           }
        * 
        */
         
                    
            $model->is_member_price_valid = $this->isMemberPriceValid($_POST['start_price_validity_period'],$_POST['end_price_validity_period']);
            $model->create_user_id = $userid;
            $model->create_time = new CDbExpression('NOW()');
            
            if(isset($_POST['has_warranty'])){
              $model->has_warranty = $_POST['has_warranty']; 
           }else{
              $model->has_warranty = 0; 
           }
            if(isset($_POST['months_of_warranty'])){
              $model->months_of_warranty = $_POST['months_of_warranty']; 
           }else{
               $model->months_of_warranty = 0;
           }
            if(isset($_POST['has_son_certification'])){
              $model->has_son_certification = $_POST['has_son_certification']; 
           }else{
               $model->has_son_certification = 0; 
           }
             
            if(isset($_POST['has_nafdac_certification'])){
              $model->has_nafdac_certification = $_POST['has_nafdac_certification']; 
           }else{
               $model->has_nafdac_certification = 0; 
           }
            if(isset($_POST['other_certifications'])){
              $model->other_certifications = $_POST['other_certifications']; 
           }
            if(isset($_POST['is_rentable'])){
              $model->is_rentable = $_POST['is_rentable']; 
              $model->rent_cost_per_day =  $_POST['rent_cost_per_day']; 
              $model->maximum_rent_quantity_per_cycle =  $_POST['maximum_rent_quantity_per_cycle']; 
              $model->minimum_rent_duration =  $_POST['minimum_rent_duration']; 
              $model->maximum_rent_duration =  $_POST['maximum_rent_duration']; 
               $model->minimum_rent_quantity_per_cycle =  $_POST['minimum_rent_quantity_per_cycle']; 
                $model->minimum_rent_quantity_per_cycle =  $_POST['minimum_rent_quantity_per_cycle']; 
           }else{
              $model->is_rentable = 0; 
              $model->rent_cost_per_day =  0; 
              $model->maximum_rent_quantity_per_cycle =  0; 
              $model->minimum_rent_duration =  0; 
              $model->maximum_rent_duration =  0; 
              $model->minimum_rent_quantity_per_cycle =  0; 
              $model->minimum_rent_quantity_per_cycle =  0; 
           }
           
            if(isset($_POST['is_paas'])){
              $model->is_paas = $_POST['is_paas']; 
           }else{
               $model->is_paas = 0; 
           }
           
           
            if(isset($_POST['is_faas'])){
              $model->is_faas = $_POST['is_faas']; 
              $model->faas_stage =  strtolower($_POST['faas_stage']); 
              $model->faas_months_to_harvest =  $_POST['faas_months_to_harvest']; 
              $model->faas_months_from_seedling =  $_POST['faas_months_from_seedling']; 
              $model->faas_current_stage_to_harvest_position =  $_POST['faas_current_stage_to_harvest_position']; 
               $model->faas_maximum_number_of_stages_to_harvest =  $_POST['faas_maximum_number_of_stages_to_harvest']; 
                $model->faas_maximum_number_of_months_to_harvest =  $_POST['faas_maximum_number_of_months_to_harvest']; 
                 $model->faas_next_stage =  strtolower($_POST['faas_next_stage']); 
                 $model->faas_number_of_months_to_next_stage =  $_POST['faas_number_of_months_to_next_stage']; 
                  $model->faas_stage_activities =  $_POST['faas_stage_activities']; 
                   $model->faas_expected_total_produce =  $_POST['faas_expected_total_produce'];
                   if(isset($_POST['faas_must_be_held_to_maturity'])){
                        $model->faas_must_be_held_to_maturity = $_POST['faas_must_be_held_to_maturity']; 
                    }else{
                        $model->faas_must_be_held_to_maturity = 0; 
                    }
                    if(isset($_POST['is_faas_insured'])){
                        $model->is_faas_insured = $_POST['is_faas_insured']; 
                         $model->faas_total_insurance_value =  $_POST['faas_total_insurance_value'];
                         $model->faas_insurance_coverage =  $_POST['faas_insurance_coverage']; 
                         $model->faas_insurance_institution =  $_POST['faas_insurance_institution']; 
                    }else{
                        $model->is_faas_insured = 0; 
                    }
                     if(isset($_POST['is_faas_tradable'])){
                        $model->is_faas_tradable = $_POST['is_faas_tradable']; 
                    }else{
                        $model->is_faas_tradable = 0; 
                    }
                    
                     $model->faas_region =  $this->getTheRegionOfThisFaaS($model->category_id);
                    $model->maximum_faas_duration =  $_POST['maximum_faas_duration'];
                    $model->minimum_faas_duration =  $_POST['minimum_faas_duration']; 
                     $model->minimum_quantity_for_faas_subscription =  $_POST['minimum_quantity_for_faas_subscription']; 
                    $model->maximum_quantity_for_faas_subscription =  $_POST['maximum_quantity_for_faas_subscription']; 
                    $model->date_current_stage_started =  date("Y-m-d H:i:s", strtotime($_POST['date_current_stage_started']));
                    $model->faas_month_season_started =  strtolower($_POST['faas_month_season_started']);
                    $model->faas_year_season_started =  strtolower($_POST['faas_year_season_started']); 
                     $model->faas_stage_activities =  $_POST['faas_stage_activities']; 
                     $model->nature_of_product = strtolower('faas');
                   
           }else{
               $model->is_faas = 0; 
              $model->faas_months_to_harvest =  0; 
              $model->faas_months_from_seedling = 0; 
              $model->faas_maximum_number_of_stages_to_harvest =  0; 
                $model->faas_maximum_number_of_months_to_harvest = 0; 
                $model->faas_number_of_months_to_next_stage = 0; 
                 $model->is_faas_insured =  0; 
                 $model->faas_total_insurance_value =  0; 
                  $model->is_faas_tradable = 0; 
                   $model->faas_expected_total_produce =  0; 
                    $model->faas_must_be_held_to_maturity =  0; 
                    $model->faas_region =  0; 
                    $model->maximum_faas_duration =  0;
                    $model->minimum_faas_duration =  0; 
                     $model->minimum_quantity_for_faas_subscription =  0; 
                    $model->maximum_quantity_for_faas_subscription =  0; 
                    $model->date_current_stage_started = 0; 
                    
           }
           
           if($_POST['nature_of_product'] == strtolower('general')){
               $model->nature_of_product = $_POST['nature_of_product'];
           }else if($_POST['nature_of_product'] == strtolower('book')){
               $model->book_authors = $_POST['book_authors'];
               $model->book_edition = $_POST['book_edition'];
               $model->book_isbn = $_POST['book_isbn'];
               $model->book_total_page = $_POST['book_total_page'];
               $model->book_year_of_print = $_POST['book_year_of_print'];
               $model->book_format = $_POST['book_format'];
               $model->book_print_quality = $_POST['book_print_quality'];
               $model->book_target_demography = $_POST['book_target_demography'];
               $model->book_other_secondary_targets = $_POST['book_other_secondary_targets'];
               $model->book_writing_style = $_POST['book_writing_style'];
               $model->book_type = $_POST['book_type'];
               $model->book_number_of_chapters = $_POST['book_number_of_chapters'];
               $model->book_format_variation = $_POST['book_format_variation'];
               $model->book_edition_variation = $_POST['book_edition_variation'];
               $model->nature_of_product = $_POST['nature_of_product'];
               
           }else if($_POST['nature_of_product'] == strtolower('graphics')){
               $model->nature_of_product = $_POST['nature_of_product'];
               $model->image_dimension_width = $_POST['image_dimension_width'];
               $model->image_dimension_height = $_POST['image_dimension_height'];
               $model->image_dimension_variation = $_POST['image_dimension_variation'];
               $model->image_measurement_unit = $_POST['image_measurement_unit'];
                $model->image_format = $_POST['image_format'];
                $model->image_resolution = $_POST['image_resolution'];
                 $model->image_resolution_unit = $_POST['image_resolution_unit'];
                 $model->image_resolution_variation = $_POST['image_resolution_variation'];
                 if($_POST['image_measurement_unit'] ==strtolower('millimeters')){
                     $model->image_measurement_unit_symbol = "mm";
                 }else if($_POST['image_measurement_unit'] ==strtolower('centimeters')){
                      $model->image_measurement_unit_symbol = "cm";
                 }else if($_POST['image_measurement_unit'] ==strtolower('inches')){
                     $model->image_measurement_unit_symbol = "in";
                 }else if($_POST['image_measurement_unit'] ==strtolower('picas')){
                     $model->image_measurement_unit_symbol = "pi";
                 }else if($_POST['image_measurement_unit'] ==strtolower('pixels')){
                      $model->image_measurement_unit_symbol = "px";
                 }else if($_POST['image_measurement_unit'] ==strtolower('points')){
                     $model->image_measurement_unit_symbol = "pt";
                 }else{
                     $model->image_measurement_unit_symbol = "px";
                 }
               
           }else if($_POST['nature_of_product'] == strtolower('video')){
               $model->nature_of_product = $_POST['nature_of_product'];
               $model->footage_format = $_POST['footage_format'];
               $model->footage_production_type = $_POST['footage_production_type'];
               $model->footage_quality_type = $_POST['footage_quality_type'];
               $model->footage_scan_type = $_POST['footage_scan_type'];
               if($_POST['footage_scan_type'] ==strtolower('progressive')){
                     $model->footage_scan_type_unit = "p";
                 }else if($_POST['footage_scan_type'] ==strtolower('interlaced')){
                      $model->footage_scan_type_unit = "i";
                 }
                 $model->footage_dimension_width = $_POST['footage_dimension_width'];
                 $model->footage_dimension_height = $_POST['footage_dimension_height'];
                 $model->footage_resolution = $_POST['footage_resolution'];
                 $model->footage_aspect_ratio = $_POST['footage_aspect_ratio'];
                 $model->footage_clip_length = $_POST['footage_clip_length'];
                 $model->footage_frame_rate = $_POST['footage_frame_rate'];
                 $model->footage_quality_type_variation = $_POST['footage_quality_type_variation'];
                 $model->footage_aspect_ratio_variation = $_POST['footage_aspect_ratio_variation'];
                 $model->footage_resolution_variation = $_POST['footage_resolution_variation'];
                 $model->footage_resolution_variation = $_POST['footage_resolution_variation'];
           }else if($_POST['nature_of_product'] == strtolower('sound')){
               $model->nature_of_product = $_POST['nature_of_product'];
               $model->sound_format = $_POST['sound_format'];
               $model->sound_production_type = $_POST['sound_production_type'];
               $model->sound_clip_length = $_POST['sound_clip_length'];
               $model->sound_format_variation = $_POST['sound_format_variation'];
           }else if($_POST['nature_of_product'] == strtolower('shoes')){
               $model->nature_of_product = $_POST['nature_of_product'];
               $model->shoes_measurement_unit = $_POST['shoes_measurement_unit'];
               $model->shoe_is_designed_for = $_POST['shoe_is_designed_for'];
               if($_POST['shoe_is_designed_for'] ==strtolower('male')){
                     $model->shoe_men_type = $_POST['shoe_men_type'];
                 }else if($_POST['shoe_is_designed_for'] ==strtolower('female')){
                     $model->shoe_female_type = $_POST['shoe_female_type'];
                 }
               $model->shoe_colour = $_POST['shoe_colour'];
               $model->shoe_size = $_POST['shoe_size']; 
               $model->shoe_colour_variation = $_POST['shoe_colour_variation'];
               $model->shoe_size_variation = $_POST['shoe_size_variation'];
               $model->shoe_material = $_POST['shoe_material'];
               $model->shoe_material_variation = $_POST['shoe_material_variation'];
               $model->shoe_sole_material = $_POST['shoe_sole_material'];
               $model->shoe_sole_material_variation = $_POST['shoe_sole_material_variation'];
               $model->shoe_target_demography = $_POST['shoe_target_demography'];
               if($_POST['shoe_target_demography'] == 'preteens' || $_POST['shoe_target_demography'] == 'babies'){
                    if($_POST['baby_shoes_age_range'] !== ""){
                        $model->baby_shoes_age_range = $_POST['baby_shoes_age_range'];
                    }
                }
                }else if($_POST['nature_of_product'] == strtolower('clothes')){
               $model->nature_of_product = $_POST['nature_of_product'];
               $model->clothes_measurement_unit = $_POST['clothes_measurement_unit'];
               $model->clothes_type = $_POST['clothes_type'];
               $model->clothes_material = $_POST['clothes_material'];
               $model->clothes_colour = $_POST['clothes_colour'];
               $model->clothes_neck_size = $_POST['clothes_neck_size'];
               $model->clothes_hand_wrist_size = $_POST['clothes_hand_wrist_size'];
               $model->clothes_hand_length = $_POST['clothes_hand_length'];
               $model->clothes_stomach_size = $_POST['clothes_stomach_size'];
               $model->clothes_shoulder_size = $_POST['clothes_shoulder_size'];
               $model->clothes_body_length = $_POST['clothes_body_length'];
               $model->clothes_back_body_length = $_POST['clothes_back_body_length'];
               $model->clothes_chest_size = $_POST['clothes_chest_size'];
               $model->clothes_material_variation = $_POST['clothes_material_variation'];
               $model->clothes_neck_size_variation = $_POST['clothes_neck_size_variation'];
               $model->clothes_hand_wrist_variation = $_POST['clothes_hand_wrist_variation'];
               $model->clothes_hand_length_variation = $_POST['clothes_hand_length_variation'];
               $model->clothes_stomach_size_variation = $_POST['clothes_stomach_size_variation'];
               $model->clothes_shoulder_size_variation = $_POST['clothes_shoulder_size_variation'];
               $model->clothes_colour_variation = $_POST['clothes_colour_variation'];
               $model->clothes_body_length_variation = $_POST['clothes_body_length_variation'];
               $model->clothes_hand_length_type = $_POST['clothes_hand_length_type'];
               $model->clothes_dimension_label = $_POST['clothes_dimension_label'];
               $model->clothes_dimension_label_variation = $_POST['clothes_dimension_label_variation'];
               $model->clothes_trouser_length = $_POST['clothes_trouser_length'];
               $model->clothes_waist_size = $_POST['clothes_waist_size'];
               $model->clothes_thigh_size = $_POST['clothes_thigh_size'];
               $model->clothes_ankle_size = $_POST['clothes_ankle_size'];
               $model->clothes_trouser_length_variation = $_POST['clothes_trouser_length_variation'];
               $model->clothes_trouser_size_variation = $_POST['clothes_trouser_size_variation'];
               $model->clothes_ankle_size_variation = $_POST['clothes_ankle_size_variation'];
               $model->clothes_target_demography = $_POST['clothes_target_demography'];
               $model->clothes_baby_age_range = $_POST['clothes_baby_age_range'];
               $model->clothes_chest_length_variation = $_POST['clothes_chest_length_variation'];
               $model->clothes_back_body_length_variation = $_POST['clothes_back_body_length_variation'];
           }
           
           
           
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
               $video_error_counter = 0;
               $image_error_counter = 0;
               $footage_error_counter = 0;
               $sound_error_counter = 0;
               $book_softcopy_error_counter = 0;
               $book_previewcopy_error_counter = 0;
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
                
                
                //add the video content that is included with this product
                if(isset($_POST['is_with_video'])){
                    if($_FILES['video_filename']['name'] != ""){
                    if($this->isVideoFileTypeAndSizeLegal()){
                        
                       $video_filename = $_FILES['video_filename']['name'];
                       $video_size = $_FILES['video_filename']['size'];
                       $is_with_video = 1; 
                    }else{
                       
                        $video_error_counter = $video_error_counter + 1;
                         
                    }//end of the video size and type statement
                }else{
                   $video_filename = null;
                   $video_size = 0;
                   $video_error_counter = $video_error_counter + 1;
             
                }
                    
                }else{
                    $is_with_video = 0;
                }
                
                
                 // This is the script that uploads the image main file for sale 
                if($_FILES['image_file_for_download']['name'] != ""){
                    if($model->isTheImageFileIInTheRightFormat($_POST['image_format'])){
                        
                       $downloadable_image_filename = $_FILES['image_file_for_download']['name'];
                       $downloadable_image_size = $_FILES['image_file_for_download']['size'];
                      
                    }else{
                       
                        $image_error_counter = $image_error_counter + 1;
                         
                    }//end of the image  size and type statement
                }else{
                   $downloadable_image_filename = null;
                   $downloadable_image_size  = 0;
                  
             
                }
                
               // This is the script that uploads the footage file 
                if($_FILES['footage_file']['name'] != ""){
                    if($model->isTheFootageFileIInTheRightFormat($_POST['footage_format'])){
                        
                       $footage_filename = $_FILES['footage_file']['name'];
                       $footage_size = $_FILES['footage_file']['size'];
                      
                    }else{
                       
                        $footage_error_counter = $footage_error_counter + 1;
                         
                    }//end of the footage size and type statement
                }else{
                   $footage_filename = null;
                   $footage_size = 0;
                  
             
                }
                
                
                // This is the script that uploads the sound and sound effects file 
                if($_FILES['sound_file']['name'] != ""){
                    if($model->isTheSoundFileIInTheRightFormat($_POST['sound_format'])){
                        
                       $sound_filename = $_FILES['sound_file']['name'];
                       $sound_size = $_FILES['sound_file']['size'];
                      
                    }else{
                       
                        $sound_error_counter = $sound_error_counter + 1;
                         
                    }//end of the sound size and type statement
                }else{
                   $sound_filename = null;
                   $sound_size = 0;
                  
             
                }
                
                
                 // This is the script that uploads the book preview copy  file 
                if($_FILES['book_preview_file']['name'] != ""){
                    if($model->isTheBookReviewFileIInTheRightFormat()){
                        
                       $book_preview_filename = $_FILES['book_preview_file']['name'];
                       $book_preview_size = $_FILES['book_preview_file']['size'];
                      
                    }else{
                       
                        $book_previewcopy_error_counter = $book_previewcopy_error_counter + 1;
                         
                    }//end of the book preview copy size and type statement
                }else{
                   $book_preview_filename = null;
                  $book_preview_size = 0;
                  
             
                }
                
                 // This is the script that uploads the book softcopy file 
            if($_POST['book_format'] == 'softcopy'){
                  if($_FILES['book_softcopy_file']['name'] != ""){
                    if($model->isTheSoftcopyBookFileIInTheRightFormat()){
                        
                       $book_softcopy_filename = $_FILES['book_softcopy_file']['name'];
                       $book_softcopy_size = $_FILES['book_softcopy_file']['size'];
                      
                    }else{
                       
                        $book_softcopy_error_counter  = $book_softcopy_error_counter  + 1;
                         
                    }//end of the book preview copy size and type statement
                }else{
                  $book_softcopy_filename = null;
                  $book_softcopy_size = 0;
                  
             
                }
            }
               
            $keyword = [];
           if(isset($_POST['keyword1'])){
               $keyword[] = $_POST['keyword1']; 
           }
           if(isset($_POST['keyword2'])){
               $keyword[] = $_POST['keyword2']; 
           }
           if(isset($_POST['keyword3'])){
               $keyword[] = $_POST['keyword3']; 
           }
           if(isset($_POST['keyword4'])){
               $keyword[] = $_POST['keyword4']; 
           }
           if(isset($_POST['keyword5'])){
               $keyword[] = $_POST['keyword5']; 
           }
             
           
            //remove empty array list
           $keywords = array_filter($keyword);
                
                
                //end of the if icon is empty statement
                //Ensure that the files variables all validates
                if(($icon_error_counter ==0 && $poster_error_counter == 0 && $product_front_view_error_counter==0 && $product_right_side_view_error_counter==0 && $product_top_view_error_counter == 0 && $product_inside_view_error_counter==0 && $product_engine_view_error_counter==0 && $product_back_view_error_counter==0 && $product_left_side_view_error_counter == 0 && $product_bottom_view_error_counter == 0 && $product_dashboard_view_error_counter==0 && $product_contents_or_booth_view_error_counter==0 && $video_error_counter==0 && $image_error_counter == 0 && $footage_error_counter == 0 && $sound_error_counter ==0 && $book_softcopy_error_counter ==0 && $book_previewcopy_error_counter ==0)){
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
                        if($is_with_video == 1){
                            $model->video_filename = $this->moveTheProductVideoToItsPathAndReturnTheItsName($model,$video_filename);
                            $model->video_size = $video_size;
                        }
                     if($_POST['nature_of_product'] == strtolower('graphics')){
                            $model->image_file_for_download = $model->moveTheDownloadableImageFileToItsPathAndReturnTheItsName($model,$downloadable_image_filename);
                            $model->image_dimension_capacity_in_mb = ($downloadable_image_size/1000);
                        }
                       if($_POST['nature_of_product'] == strtolower('video')){
                            $model->footage_file = $model->moveTheDownloadableFootageFileToItsPathAndReturnTheItsName($model,$footage_filename);
                            $model->footage_size = $footage_size;
                            $model->footage_dimension_capacity_in_mb = ($footage_size/1000);
                       }
                      if($_POST['nature_of_product'] == strtolower('sound')){
                           $model->sound_file = $model->moveTheDownloadableSoundFileToItsPathAndReturnTheItsName($model,$sound_filename);
                           $model->sound_size = $sound_size;
                      }
                       if($_POST['nature_of_product'] == strtolower('book')){
                            $model->book_preview_file = $model->moveTheBookPreviewFileToItsPathAndReturnTheItsName($model,$book_preview_filename);
                            $model->book_preview_file_size = $book_preview_size;
                            if($_POST['book_format'] == 'softcopy'){
                                $model->book_softcopy_file = $model->moveTheBookSoftcopyToItsPathAndReturnTheItsName($model,$book_softcopy_filename);
                                $model->book_softcopy_file_size = $book_softcopy_size;
                        }
                       }
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
                       
                       if($_POST['is_a_variation'] == 1){
                           $model->is_a_variation = $_POST['is_a_variation'];
                           $model->product_variation_id = $_POST['product_variation_id'];
                       }else{
                           $model->is_a_variation = 0;
                           $model->product_variation_id = 0;
                       }
                        if($model->save()) {
                         $model->registerThisProductKeywords($model->id,$keywords);
                                $msg = "'$model->name' product was added successful";
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
                } else if($video_error_counter>0){
                    $video_size = $this->getTheMaximumVideoSizeForThisService();
                    $msg = "The product video provided is either not in the mp4 format or it exceeded the mamximum allowable size of '$video_size' MB";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );  
                }else if($image_error_counter>0){
                  $msg = "It is possible the image file is not in '$model->image_format' format. Please check the image file and try again";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );  
                }else if($footage_error_counter>0){
                    $msg = "It is possible the footage file is not in '$model->footage_format' format. Please check the footage file and try again";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );  
                }else if($sound_error_counter>0){
                    $msg = "It is possible the sound file is not in '$model->sound_format' format. Please check the sound file and try again";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       ); 
                }else if($book_previewcopy_error_counter>0){
                    $msg = "It is possible the book preview file is not in 'pdf' format. Please check the book preview file and try again";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       ); 
                }else if($book_softcopy_error_counter>0){
                     $msg = "It is possible the book softcopy file is not in 'pdf' format. Please check the book softcopy file and try again";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       ); 
                }
            
            
        }
        
        /**
         * This is the function that retrieves the region of a faas slot 
         */
        public function getTheRegionOfThisFaaS($category_id){
            $model = new Category;
            return $model->getTheRegionOfThisFaaS($category_id);
        }
        
        /**
         * This is the function that determines if member price is valid
         */
        public function isMemberPriceValid($start_price_validity_period,$end_price_validity_period){
            $model = new Order;
             $today = getdate(mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
             $start_date = getdate(strtotime($start_price_validity_period));
             $end_date = getdate(strtotime($end_price_validity_period));
            if($model->isTodayGreaterThanOrEqualToStartValidityDate($today, $start_date)){
                        if($model->isTodayLessThanOrEqualToEndValidityDate($today,$end_date)){
                            return 1;
                        }else{
                            return 0;
                        }
                        
                    }else{
                         return 0;
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
         * This is the function that updates a product
         */
        public function actionupdateProduct(){
            
            //get the logged in user id
            $userid = Yii::app()->user->id;
            
             $_id = $_POST['id'];
            $model=Product::model()->findByPk($_id);
            
            $model->name = $_POST['name'];
            if(isset($_POST['description'])){
                $model->description = $_POST['description'];
            }
                   
            if(is_numeric($_POST['service'])){
                $model->service_id = $_POST['service'];
            }else{
                $model->service_id = $_POST['service_id'];
            }
            if(is_numeric($_POST['category'])){
                $model->category_id = $_POST['category'];
            }else{
                $model->category_id = $_POST['category_id'];
            }
            if(is_numeric($_POST['product_type'])){
                $model->product_type_id = $_POST['product_type'];
            }else{
               $model->product_type_id = $_POST['product_type_id']; 
            }
            if(isset($_POST['condition'])){
                $model->condition = $_POST['condition'];
            }
             if(isset($_POST['is_quotable'])){
                $model->is_quotable = $_POST['is_quotable'];
                $model->prevailing_retail_selling_price = 0;
                $model->per_portion_price = 0;
            }else{
                $model->is_quotable = 0;
                if(isset($_POST['prevailing_retail_selling_price'])){
                    $model->prevailing_retail_selling_price = $_POST['prevailing_retail_selling_price'];
                }else{
                    $model->prevailing_retail_selling_price = 0;
                }
                if(isset($_POST['per_portion_price'])){
                    $model->per_portion_price = $_POST['per_portion_price'];
                }else{
                    $model->per_portion_price = 0;
                }
            }
            if(isset($_POST['is_available'])){
                $model->is_available = $_POST['is_available'];
            }else{
                $model->is_available = 0;
            }
            if(isset($_POST['is_escrowable'])){
                $model->is_escrowable = $_POST['is_escrowable'];
            }else{
                $model->is_escrowable = 0;
            }
             if(isset($_POST['is_future_tradable'])){
                $model->is_future_tradable = $_POST['is_future_tradable'];
            }else{
                $model->is_future_tradable = 0;
            }
           // $model->cumulative_selling_price = $this->generateTheCumulativeSellingPrice($model->per_portion_price, $model->maximum_portion);
           if(isset($_POST['feature'])){
               $model->feature = $_POST['feature'];
           } 
            if(isset($_POST['feature_1'])){
               $model->feature_1 = $_POST['feature_1'];
           } 
            if(isset($_POST['feature_2'])){
               $model->feature_2 = $_POST['feature_2'];
           } 
            if(isset($_POST['feature_3'])){
               $model->feature_3 = $_POST['feature_3'];
           } 
            if(isset($_POST['feature_4'])){
               $model->feature_4 = $_POST['feature_4'];
           } 
           
           if(isset($_POST['specifications'])){
               $model->specifications = $_POST['specifications'];
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
            if($_POST['target'] == 0){
                if(isset($_POST['quantity'])){
                    $model->quantity = $_POST['quantity']; 
                } 
                
            }else{
                 $model->quantity = $_POST['quantity']; 
                 $model->cumulative_quantity = $model->getTheProductCumulativeQuantity($_id) + $_POST['quantity']; 
            }
           
           // $model->discount_rate = $this->generateTheDiscountRate($model->prevailing_retail_selling_price,$model->per_portion_price);
        
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
           $model->is_member_price_valid = $this->isMemberPriceValid($_POST['start_price_validity_period'],$_POST['end_price_validity_period']);
            
           if(isset($_POST['is_a_hamper'])){
                $model->is_a_hamper = $_POST['is_a_hamper'];
                $model->hamper_cost_limit = $_POST['prevailing_retail_selling_price'];
                $model->displayable_on_store = 0;
            }else{
                $model->is_a_hamper = 0;
            }
            if(isset($_POST['is_payment_permitted_on_delivery'])){
                $model->is_payment_permitted_on_delivery = $_POST['is_payment_permitted_on_delivery'];
            }else{
                $model->is_payment_permitted_on_delivery = 0;
            }
             if(isset($_POST['is_with_video'])){
                $model->is_with_video = $_POST['is_with_video'];
                $model->video_for = strtolower($_POST['video_for']);
            }else{
                $model->is_with_video = 0;
            }
         $model->weight = $_POST['weight']; 
         $model->code = $model->retrieveTheCodeForThisProduct($_id);
          /** $model->is_leads_topmost = $_POST['is_leads_topmost']; 
           $model->leads_version = $_POST['leads_version']; 
           $model->leads_level = $_POST['leads_level']; 
           $model->leadscode = $_POST['leadscode']; 
            */
        
           $model->update_user_id = $userid;
           $model->update_time = new CDbExpression('NOW()');
           
           if(isset($_POST['has_warranty'])){
              $model->has_warranty = $_POST['has_warranty']; 
           }else{
              $model->has_warranty = 0; 
           }
            if(isset($_POST['months_of_warranty'])){
              $model->months_of_warranty = $_POST['months_of_warranty']; 
           }else{
               $model->months_of_warranty = 0;
           }
            if(isset($_POST['has_son_certification'])){
              $model->has_son_certification = $_POST['has_son_certification']; 
           }else{
               $model->has_son_certification = 0; 
           }
             
            if(isset($_POST['has_nafdac_certification'])){
              $model->has_nafdac_certification = $_POST['has_nafdac_certification']; 
           }else{
               $model->has_nafdac_certification = 0; 
           }
            if(isset($_POST['other_certifications'])){
              $model->other_certifications = $_POST['other_certifications']; 
           }
            if(isset($_POST['is_rentable'])){
              $model->is_rentable = $_POST['is_rentable']; 
              $model->rent_cost_per_day =  $_POST['rent_cost_per_day']; 
              $model->maximum_rent_quantity_per_cycle =  $_POST['maximum_rent_quantity_per_cycle']; 
              $model->minimum_rent_duration =  $_POST['minimum_rent_duration']; 
              $model->maximum_rent_duration =  $_POST['maximum_rent_duration']; 
               $model->minimum_rent_quantity_per_cycle =  $_POST['minimum_rent_quantity_per_cycle']; 
                $model->minimum_rent_quantity_per_cycle =  $_POST['minimum_rent_quantity_per_cycle']; 
           }else{
              $model->is_rentable = 0; 
              $model->rent_cost_per_day =  0; 
              $model->maximum_rent_quantity_per_cycle =  0; 
              $model->minimum_rent_duration =  0; 
              $model->maximum_rent_duration =  0; 
              $model->minimum_rent_quantity_per_cycle =  0; 
              $model->minimum_rent_quantity_per_cycle =  0; 
           }
           
            if(isset($_POST['is_paas'])){
              $model->is_paas = $_POST['is_paas']; 
           }else{
               $model->is_paas = 0; 
           }
           
           
            if(isset($_POST['is_faas'])){
              $model->is_faas = $_POST['is_faas']; 
              $model->faas_stage =  strtolower($_POST['faas_stage']); 
              $model->faas_months_to_harvest =  $_POST['faas_months_to_harvest']; 
              $model->faas_months_from_seedling =  $_POST['faas_months_from_seedling']; 
              $model->faas_current_stage_to_harvest_position =  $_POST['faas_current_stage_to_harvest_position']; 
               $model->faas_maximum_number_of_stages_to_harvest =  $_POST['faas_maximum_number_of_stages_to_harvest']; 
                $model->faas_maximum_number_of_months_to_harvest =  $_POST['faas_maximum_number_of_months_to_harvest']; 
                 $model->faas_next_stage =  strtolower($_POST['faas_next_stage']); 
                 $model->faas_number_of_months_to_next_stage =  $_POST['faas_number_of_months_to_next_stage']; 
                  $model->faas_expected_total_produce =  $_POST['faas_expected_total_produce']; 
                   if(isset($_POST['faas_must_be_held_to_maturity'])){
                        $model->faas_must_be_held_to_maturity = $_POST['faas_must_be_held_to_maturity']; 
                    }else{
                        $model->faas_must_be_held_to_maturity = 0; 
                    }
                    if(isset($_POST['is_faas_insured'])){
                        $model->is_faas_insured = $_POST['is_faas_insured']; 
                         $model->faas_total_insurance_value =  $_POST['faas_total_insurance_value'];
                         $model->faas_insurance_coverage =  $_POST['faas_insurance_coverage']; 
                         $model->faas_insurance_institution =  $_POST['faas_insurance_institution']; 
                    }else{
                        $model->is_faas_insured = 0; 
                    }
                     if(isset($_POST['is_faas_tradable'])){
                        $model->is_faas_tradable = $_POST['is_faas_tradable']; 
                    }else{
                        $model->is_faas_tradable = 0; 
                    }
                    $model->faas_region =  $this->getTheRegionOfThisFaaS($model->category_id);
                    $model->maximum_faas_duration =  $_POST['maximum_faas_duration'];
                    $model->minimum_faas_duration =  $_POST['minimum_faas_duration']; 
                     $model->minimum_quantity_for_faas_subscription =  $_POST['minimum_quantity_for_faas_subscription']; 
                    $model->maximum_quantity_for_faas_subscription =  $_POST['maximum_quantity_for_faas_subscription']; 
                    $model->date_current_stage_started =  date("Y-m-d H:i:s", strtotime($_POST['date_current_stage_started']));
                    $model->faas_month_season_started =  strtolower($_POST['faas_month_season_started']);
                    $model->faas_year_season_started =  strtolower($_POST['faas_year_season_started']); 
                     $model->faas_stage_activities =  $_POST['faas_stage_activities']; 
                      
           }else{
               $model->is_faas = 0; 
              $model->faas_months_to_harvest =  0; 
              $model->faas_months_from_seedling = 0; 
              $model->faas_maximum_number_of_stages_to_harvest =  0; 
                $model->faas_maximum_number_of_months_to_harvest = 0; 
                $model->faas_number_of_months_to_next_stage = 0; 
                 $model->is_faas_insured =  0; 
                 $model->faas_total_insurance_value =  0; 
                  $model->is_faas_tradable = 0; 
                   $model->faas_expected_total_produce =  0; 
                    $model->faas_must_be_held_to_maturity =  0; 
                    $model->faas_region =  0; 
                    $model->maximum_faas_duration =  0;
                    $model->minimum_faas_duration =  0; 
                     $model->minimum_quantity_for_faas_subscription =  0; 
                    $model->maximum_quantity_for_faas_subscription =  0; 
                    $model->date_current_stage_started = 0; 
                    
           }
           
           
           if($_POST['nature_of_product'] == strtolower('general')){
               $model->nature_of_product = $_POST['nature_of_product'];
           }else if($_POST['nature_of_product'] == strtolower('book')){
               $model->book_authors = $_POST['book_authors'];
               $model->book_edition = $_POST['book_edition'];
               $model->book_isbn = $_POST['book_isbn'];
               $model->book_total_page = $_POST['book_total_page'];
               $model->book_year_of_print = $_POST['book_year_of_print'];
               $model->book_format = $_POST['book_format'];
               $model->book_print_quality = $_POST['book_print_quality'];
               $model->book_target_demography = $_POST['book_target_demography'];
               $model->book_other_secondary_targets = $_POST['book_other_secondary_targets'];
               $model->book_writing_style = $_POST['book_writing_style'];
               $model->book_type = $_POST['book_type'];
               $model->book_number_of_chapters = $_POST['book_number_of_chapters'];
               $model->book_format_variation = $_POST['book_format_variation'];
               $model->book_edition_variation = $_POST['book_edition_variation'];
               $model->nature_of_product = $_POST['nature_of_product'];
               
           }else if($_POST['nature_of_product'] == strtolower('graphics')){
               $model->nature_of_product = $_POST['nature_of_product'];
               $model->image_dimension_width = $_POST['image_dimension_width'];
               $model->image_dimension_height = $_POST['image_dimension_height'];
               $model->image_dimension_variation = $_POST['image_dimension_variation'];
               $model->image_measurement_unit = $_POST['image_measurement_unit'];
                $model->image_format = $_POST['image_format'];
                $model->image_resolution = $_POST['image_resolution'];
                 $model->image_resolution_unit = $_POST['image_resolution_unit'];
                 $model->image_resolution_variation = $_POST['image_resolution_variation'];
                 if($_POST['image_measurement_unit'] ==strtolower('millimeters')){
                     $model->image_measurement_unit_symbol = "mm";
                 }else if($_POST['image_measurement_unit'] ==strtolower('centimeters')){
                      $model->image_measurement_unit_symbol = "cm";
                 }else if($_POST['image_measurement_unit'] ==strtolower('inches')){
                     $model->image_measurement_unit_symbol = "in";
                 }else if($_POST['image_measurement_unit'] ==strtolower('picas')){
                     $model->image_measurement_unit_symbol = "pi";
                 }else if($_POST['image_measurement_unit'] ==strtolower('pixels')){
                      $model->image_measurement_unit_symbol = "px";
                 }else if($_POST['image_measurement_unit'] ==strtolower('points')){
                     $model->image_measurement_unit_symbol = "pt";
                 }else{
                     $model->image_measurement_unit_symbol = "px";
                 }
               
           }else if($_POST['nature_of_product'] == strtolower('video')){
               $model->nature_of_product = $_POST['nature_of_product'];
               $model->footage_format = $_POST['footage_format'];
               $model->footage_production_type = $_POST['footage_production_type'];
               $model->footage_quality_type = $_POST['footage_quality_type'];
               $model->footage_scan_type = $_POST['footage_scan_type'];
               if($_POST['footage_scan_type'] ==strtolower('progressive')){
                     $model->footage_scan_type_unit = "p";
                 }else if($_POST['footage_scan_type'] ==strtolower('interlaced')){
                      $model->footage_scan_type_unit = "i";
                 }
                 $model->footage_dimension_width = $_POST['footage_dimension_width'];
                 $model->footage_dimension_height = $_POST['footage_dimension_height'];
                 $model->footage_resolution = $_POST['footage_resolution'];
                 $model->footage_aspect_ratio = $_POST['footage_aspect_ratio'];
                 $model->footage_clip_length = $_POST['footage_clip_length'];
                 $model->footage_frame_rate = $_POST['footage_frame_rate'];
                 $model->footage_quality_type_variation = $_POST['footage_quality_type_variation'];
                 $model->footage_aspect_ratio_variation = $_POST['footage_aspect_ratio_variation'];
                 $model->footage_resolution_variation = $_POST['footage_resolution_variation'];
                 $model->footage_resolution_variation = $_POST['footage_resolution_variation'];
           }else if($_POST['nature_of_product'] == strtolower('sound')){
               $model->nature_of_product = $_POST['nature_of_product'];
               $model->sound_format = $_POST['sound_format'];
               $model->sound_production_type = $_POST['sound_production_type'];
               $model->sound_clip_length = $_POST['sound_clip_length'];
               $model->sound_format_variation = $_POST['sound_format_variation'];
           }else if($_POST['nature_of_product'] == strtolower('shoes')){
               $model->nature_of_product = $_POST['nature_of_product'];
               $model->shoes_measurement_unit = $_POST['shoes_measurement_unit'];
               $model->shoe_is_designed_for = $_POST['shoe_is_designed_for'];
               if($_POST['shoe_is_designed_for'] ==strtolower('male')){
                     $model->shoe_men_type = $_POST['shoe_men_type'];
                 }else if($_POST['shoe_is_designed_for'] ==strtolower('female')){
                     $model->shoe_female_type = $_POST['shoe_female_type'];
                 }
               $model->shoe_colour = $_POST['shoe_colour'];
               $model->shoe_size = $_POST['shoe_size']; 
               $model->shoe_colour_variation = $_POST['shoe_colour_variation'];
               $model->shoe_size_variation = $_POST['shoe_size_variation'];
               $model->shoe_material = $_POST['shoe_material'];
               $model->shoe_material_variation = $_POST['shoe_material_variation'];
               $model->shoe_sole_material = $_POST['shoe_sole_material'];
               $model->shoe_sole_material_variation = $_POST['shoe_sole_material_variation'];
               $model->shoe_target_demography = $_POST['shoe_target_demography'];
           if($_POST['shoe_target_demography'] == 'preteens' || $_POST['shoe_target_demography'] == 'babies'){
               if($_POST['baby_shoes_age_range'] !== ""){
                   $model->baby_shoes_age_range = $_POST['baby_shoes_age_range'];
               }
           }
               
               
           }else if($_POST['nature_of_product'] == strtolower('clothes')){
               $model->nature_of_product = $_POST['nature_of_product'];
               $model->clothes_measurement_unit = $_POST['clothes_measurement_unit'];
               $model->clothes_type = $_POST['clothes_type'];
               $model->clothes_material = $_POST['clothes_material'];
               $model->clothes_colour = $_POST['clothes_colour'];
               $model->clothes_neck_size = $_POST['clothes_neck_size'];
               $model->clothes_hand_wrist_size = $_POST['clothes_hand_wrist_size'];
               $model->clothes_hand_length = $_POST['clothes_hand_length'];
               $model->clothes_stomach_size = $_POST['clothes_stomach_size'];
               $model->clothes_shoulder_size = $_POST['clothes_shoulder_size'];
               $model->clothes_body_length = $_POST['clothes_body_length'];
               $model->clothes_back_body_length = $_POST['clothes_back_body_length'];
               $model->clothes_chest_size = $_POST['clothes_chest_size'];
               $model->clothes_material_variation = $_POST['clothes_material_variation'];
               $model->clothes_neck_size_variation = $_POST['clothes_neck_size_variation'];
               $model->clothes_hand_wrist_variation = $_POST['clothes_hand_wrist_variation'];
               $model->clothes_hand_length_variation = $_POST['clothes_hand_length_variation'];
               //$model->clothes_stomach_size_variation = $_POST['clothes_stomach_size_variation'];
               $model->clothes_shoulder_size_variation = $_POST['clothes_shoulder_size_variation'];
               $model->clothes_colour_variation = $_POST['clothes_colour_variation'];
               $model->clothes_body_length_variation = $_POST['clothes_body_length_variation'];
               $model->clothes_hand_length_type = $_POST['clothes_hand_length_type'];
               $model->clothes_dimension_label = $_POST['clothes_dimension_label'];
               $model->clothes_dimension_label_variation = $_POST['clothes_dimension_label_variation'];
               $model->clothes_trouser_length = $_POST['clothes_trouser_length'];
               $model->clothes_waist_size = $_POST['clothes_waist_size'];
               $model->clothes_thigh_size = $_POST['clothes_thigh_size'];
               $model->clothes_ankle_size = $_POST['clothes_ankle_size'];
               $model->clothes_trouser_length_variation = $_POST['clothes_trouser_length_variation'];
               $model->clothes_trouser_size_variation = $_POST['clothes_trouser_size_variation'];
               $model->clothes_ankle_size_variation = $_POST['clothes_ankle_size_variation'];
               $model->clothes_target_demography = $_POST['clothes_target_demography'];
               $model->clothes_baby_age_range = $_POST['clothes_baby_age_range'];
               $model->clothes_chest_length_variation = $_POST['clothes_chest_length_variation'];
               $model->clothes_back_body_length_variation = $_POST['clothes_back_body_length_variation'];
           }
           
           $keyword = [];
           if($_POST['keyword1']!=""){
               $keyword[] = $_POST['keyword1']; 
           }
           if($_POST['keyword1']!=""){
               $keyword[] = $_POST['keyword2']; 
           }
           if($_POST['keyword1']!=""){
               $keyword[] = $_POST['keyword3']; 
           }
           if($_POST['keyword1']!=""){
               $keyword[] = $_POST['keyword4']; 
           }
           if($_POST['keyword1']!=""){
               $keyword[] = $_POST['keyword5']; 
           }
           //remove empty array list
           $keywords = array_filter($keyword);
                
            //get the container's name
            $product_name = $this->getThisProductName($_id);
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
               $video_error_counter = 0;
               $image_error_counter = 0;
               $footage_error_counter = 0;
               $sound_error_counter = 0;
               $book_softcopy_error_counter = 0;
               $book_previewcopy_error_counter = 0;
               
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
                
                
                 //add the video content that is included with this product
                if(isset($_POST['is_with_video'])){
                    if($_FILES['video_filename']['name'] != ""){
                    if($this->isVideoFileTypeAndSizeLegal()){
                        
                       $video_filename = $_FILES['video_filename']['name'];
                       $video_size = $_FILES['video_filename']['size'];
                        $is_with_video = 1;
                    }else{
                       
                        $video_error_counter = $video_error_counter + 1;
                         
                    }//end of the video size and type statement
                }else{
                   $video_filename = $this->retrieveThePreviosProductVideo($_id);
                   $video_size = $this->retrieveThePreviousProductVideoSize($_id);
                   $is_with_video = 1;
             
                }
                    
                }else{
                    $video_filename = null;
                    $is_with_video = 0;
                }
                
                
                 // This is the script that uploads the image main file for sale 
                if($_FILES['image_file_for_download']['name'] != ""){
                    if($model->isTheImageFileIInTheRightFormat($_POST['image_format'])){
                        
                       $downloadable_image_filename = $_FILES['image_file_for_download']['name'];
                       $downloadable_image_size = $_FILES['image_file_for_download']['size'];
                      
                    }else{
                       
                        $image_error_counter = $image_error_counter + 1;
                         
                    }//end of the image  size and type statement
                }else{
                   $downloadable_image_filename = null;
                   $downloadable_image_size  = 0;
                  
             
                }
           
                
               // This is the script that uploads the footage file 
                if($_FILES['footage_file']['name'] != ""){
                    if($model->isTheFootageFileIInTheRightFormat($_POST['footage_format'])){
                        
                       $footage_filename = $_FILES['footage_file']['name'];
                       $footage_size = $_FILES['footage_file']['size'];
                      
                    }else{
                       
                        $footage_error_counter = $footage_error_counter + 1;
                         
                    }//end of the footage size and type statement
                }else{
                   $footage_filename = null;
                   $footage_size = 0;
                  
             
                }
                
                
                // This is the script that uploads the sound and sound effects file 
                if($_FILES['sound_file']['name'] != ""){
                    if($model->isTheSoundFileIInTheRightFormat($_POST['sound_format'])){
                        
                       $sound_filename = $_FILES['sound_file']['name'];
                       $sound_size = $_FILES['sound_file']['size'];
                      
                    }else{
                       
                        $sound_error_counter = $sound_error_counter + 1;
                         
                    }//end of the sound size and type statement
                }else{
                   $sound_filename = null;
                   $sound_size = 0;
                  
             
                }
                
                
                 // This is the script that uploads the book preview copy  file 
                if($_FILES['book_preview_file']['name'] != ""){
                    if($model->isTheBookReviewFileIInTheRightFormat()){
                        
                       $book_preview_filename = $_FILES['book_preview_file']['name'];
                       $book_preview_size = $_FILES['book_preview_file']['size'];
                      
                    }else{
                       
                        $book_previewcopy_error_counter = $book_previewcopy_error_counter + 1;
                         
                    }//end of the book preview copy size and type statement
                }else{
                   $book_preview_filename = null;
                  $book_preview_size = 0;
                  
             
                }
                
                 // This is the script that uploads the book softcopy file 
            if($_POST['book_format'] == 'softcopy'){
                  if($_FILES['book_softcopy_file']['name'] != ""){
                    if($model->isTheSoftcopyBookFileIInTheRightFormat()){
                        
                       $book_softcopy_filename = $_FILES['book_softcopy_file']['name'];
                       $book_softcopy_size = $_FILES['book_softcopy_file']['size'];
                      
                    }else{
                       
                        $book_softcopy_error_counter  = $book_softcopy_error_counter  + 1;
                         
                    }//end of the book preview copy size and type statement
                }else{
                  $book_softcopy_filename = null;
                  $book_softcopy_size = 0;
                  
             
                }
            }
                
                //Ensure that the files variables all validates
                 if(($icon_error_counter ==0 && $poster_error_counter == 0 && $product_front_view_error_counter==0 && $product_right_side_view_error_counter==0 && $product_top_view_error_counter == 0 && $product_inside_view_error_counter==0 && $product_engine_view_error_counter==0 && $product_back_view_error_counter==0 && $product_left_side_view_error_counter == 0 && $product_bottom_view_error_counter == 0 && $product_dashboard_view_error_counter==0 && $product_contents_or_booth_view_error_counter==0 && $video_error_counter ==0 && $image_error_counter == 0 && $footage_error_counter == 0 && $sound_error_counter ==0 && $book_softcopy_error_counter ==0 && $book_previewcopy_error_counter ==0)){
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
                        if($is_with_video == 1){
                            $model->video_filename = $this->moveTheProductVideoToItsPathAndReturnTheItsName($model,$video_filename);
                            $model->video_size = $video_size;
                        }
                        if($_POST['nature_of_product'] == strtolower('graphics')){
                           $model->image_file_for_download = $model->moveTheDownloadableImageFileToItsPathAndReturnTheItsName($model,$downloadable_image_filename);
                           $model->image_dimension_capacity_in_mb = ($downloadable_image_size/1000);
                        }
                       if($_POST['nature_of_product'] == strtolower('video')){
                            $model->footage_file = $model->moveTheDownloadableFootageFileToItsPathAndReturnTheItsName($model,$footage_filename);
                            $model->footage_size = $footage_size;
                            $model->footage_dimension_capacity_in_mb = ($footage_size/1000);
                       }
                      if($_POST['nature_of_product'] == strtolower('sound')){
                           $model->sound_file = $model->moveTheDownloadableSoundFileToItsPathAndReturnTheItsName($model,$sound_filename);
                           $model->sound_size = $sound_size;
                      }
                       if($_POST['nature_of_product'] == strtolower('book')){
                            $model->book_preview_file = $model->moveTheBookPreviewFileToItsPathAndReturnTheItsName($model,$book_preview_filename);
                            $model->book_preview_file_size = $book_preview_size;
                            if($_POST['book_format'] == 'softcopy'){
                                $model->book_softcopy_file = $model->moveTheBookSoftcopyToItsPathAndReturnTheItsName($model,$book_softcopy_filename);
                                $model->book_softcopy_file_size = $book_softcopy_size;
                        }
                       }
                      
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
                       
                       if($_POST['is_a_variation'] == 1){
                           $model->is_a_variation = $_POST['is_a_variation'];
                           $model->product_variation_id = $_POST['product_variation_id'];
                       }else{
                           $model->is_a_variation = 0;
                           $model->product_variation_id = 0;
                       }
                        if($model->save()) {
                        //register this product keyword or kephrase
                            $model->modifyThisProductKeywords($_id,$keywords);
                                $msg = "'$model->name' product was updated successful";
                                 header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() == 0,
                                  "msg" => $msg,
                                    "keyword"=>$keywords,
                                    "sound_file"=>$model->isTheSoundFileIInTheRightFormat($_POST['sound_format']))
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
                }else if($video_error_counter>0){
                    $video_size = $this->getTheMaximumVideoSizeForThisService();
                    $msg = "The product video provided is either not in the mp4 format or it exceeded the mamximum allowable size of '$video_size' MB";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );  
                }else if($image_error_counter>0){
                  $msg = "It is possible the image file is not in '$model->image_format' format. Please check the image file and try again";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg,
                             "image_file"=>$model->isTheImageFileIInTheRightFormat($_POST['image_format'])
                                 )
                       );  
                }else if($footage_error_counter>0){
                    $msg = "It is possible the footage file is not in '$model->footage_format' format. Please check the footage file and try again";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       );  
                }else if($sound_error_counter>0){
                    $msg = "It is possible the sound file is not in '$model->sound_format' format. Please check the sound file and try again";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       ); 
                }else if($book_previewcopy_error_counter>0){
                    $msg = "It is possible the book preview file is not in 'pdf' format. Please check the book preview file and try again";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       ); 
                }else if($book_softcopy_error_counter>0){
                     $msg = "It is possible the book softcopy file is not in 'pdf' format. Please check the book softcopy file and try again";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                           "success" => mysql_errno() != 0,
                             "msg" => $msg)
                       ); 
                }
            
            
            
        }
        
        
        
        
      /**
         * This is the function that retrieves all product
             * 
             */
        
     public function actionListAllProducts(){
            
       $userid = Yii::app()->user->id;
             
       if(isset($_REQUEST['filter_para'])){
          
          $filter_para = strtolower($_REQUEST['filter_para']);
            //$hamper_id = 24;     
          $all_products = [];
          $criteria = new CDbCriteria();
          $criteria->select = '*';
          $criteria->condition='displayable_on_store=:displayable';
          $criteria->params = array(':displayable'=>1);
         //$criteria->order = "name";
          $products= Product::model()->findAll($criteria);
       $code = [];
       foreach($products as $product){
           if($this->isThisProductIncludable($filter_para,$product['category_id'])){
               $all_products[] = $product;
              // $code[]= $this->isThisProductIncludable($filter_para,$product['category_id']);
           }
       }
        header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                             "product" => $all_products
                           //"code"=>$code
               
                       ));
                 
     }else{
                  if(isset($_REQUEST['service']) ==""){
                 if(isset($_REQUEST['category']) ==""){
                     if(isset($_REQUEST['type']) ==""){
                         if(isset($_REQUEST['searchstring']) ==""){
                          $products = Product::model()->findAll(array('order'=>'name'));
                           $all_products = [];
                        foreach($products as $product){
                              if($product['is_custom_product']==0){
                                       if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                          }
                          
                          
                            header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                            ));

                          
                     }
                     }
                     
                    }elseif($_REQUEST['category'] == "All Categories" || $_REQUEST['category'] ==0){
                       if($_REQUEST['type'] =="All Types" || $_REQUEST['type'] ==0){
                         if($_REQUEST['searchstring'] ==""){
                            
                          $products = Product::model()->findAll(array('order'=>'name'));
                          $all_products = [];
                          foreach($products as $product){
                              if($product['is_custom_product']==0){
                                      if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                          }
                           header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                            ));
                        }else{
                            $searchstring = explode('+',$_REQUEST['searchstring']);
                            $searchWords = preg_replace('/\s/', '', $searchstring);
                            $searchable_items = [];
                            foreach($searchWords as $word){
                                 $q = "SELECT id FROM product where name REGEXP '$word' ordered by name" ;
                                 $cmd = Yii::app()->db->createCommand($q);
                                 $results = $cmd->query();
                                 foreach($results as $result){
                                     $criteria = new CDbCriteria();
                                     $criteria->select = '*';
                                     $criteria->condition='id=:id';   
                                     $criteria->params = array(':id'=>$result['id']);
                                     $criteria->order = "name";
                                     $product = Product::model()->find($criteria);
                                     if($product['is_custom_product']==0){
                                         if($product['displayable_on_store'] == 1){
                                             $searchable_items[] = $product;
                                         }
                                      }
                                 }
                            }
                              header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $searchable_items
                                        
                                
                                ));
                           
                        }
                        }else if(is_numeric($_REQUEST['type']) && $_REQUEST['type']!= 0 ){
                        if($_REQUEST['searchstring'] ==""){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='product_type_id=:typeid';   
                            $criteria->params = array(':typeid'=>$_REQUEST['type']);
                            $criteria->order = "name";
                            $products = Product::model()->findAll($criteria);
                            
                          $all_products = [];
                          foreach($products as $product){
                              if($product['is_custom_product']==0){
                                       if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                          }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                                ));
                        }else{
                          $searchstring = explode('+',$_REQUEST['searchstring']);
                            $searchWords = preg_replace('/\s/', '', $searchstring);
                            $searchable_items = [];
                            foreach($searchWords as $word){
                                 $q = "SELECT id FROM product where name REGEXP '$word' ordered by name" ;
                                 $cmd = Yii::app()->db->createCommand($q);
                                 $results = $cmd->query();
                                 foreach($results as $result){
                                     $criteria = new CDbCriteria();
                                     $criteria->select = '*';
                                     $criteria->condition='id=:id and product_type_id=:typeid';   
                                     $criteria->params = array(':id'=>$result['id'],':typeid'=>$_REQUEST['type']);
                                     $criteria->order = "name";
                                     $product = Product::model()->find($criteria);
                                     if($product['is_custom_product']==0){
                                         if($product['displayable_on_store'] == 1){
                                             $searchable_items[] = $product;
                                         }
                                      }
                                 }
                            }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $searchable_items
                                        
                                
                                ));
                        }
                        
                    }
                    }elseif(is_numeric($_REQUEST['category']) && $_REQUEST['category'] !=0){
                    if($_REQUEST['type'] =="All Types" || $_REQUEST['type'] ==0){
                        if($_REQUEST['searchstring'] ==""){
                            
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='category_id=:categoryid';   
                            $criteria->params = array(':categoryid'=>$_REQUEST['category']);
                            $criteria->order = "name";
                            $products = Product::model()->findAll($criteria);
                            
                           $all_products = [];
                          foreach($products as $product){
                              if($product['is_custom_product']==0){
                                       if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                          }
                            
                              header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                            ));
                        }else{
                            
                            $searchstring = explode('+',$_REQUEST['searchstring']);
                            $searchWords = preg_replace('/\s/', '', $searchstring);
                            $searchable_items = [];
                            foreach($searchWords as $word){
                                 $q = "SELECT id FROM product where name REGEXP '$word'" ;
                                 $cmd = Yii::app()->db->createCommand($q);
                                 $results = $cmd->query();
                                 foreach($results as $result){
                                     $criteria = new CDbCriteria();
                                     $criteria->select = '*';
                                     $criteria->condition='id=:id and category_id=:categoryid';   
                                     $criteria->params = array(':id'=>$result['id'],':categoryid'=>$_REQUEST['category']);
                                      $criteria->order = "name";
                                     $product = Product::model()->find($criteria);
                                     if($product['is_custom_product']==0){
                                          if($product['displayable_on_store'] == 1){
                                             $searchable_items[] = $product;
                                         }
                                      }
                                 }
                            }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $searchable_items
                                        
                                
                                ));
                        }
                        
                    }else if(is_numeric($_REQUEST['type']) && $_REQUEST['type']!= 0 ){
                        if($_REQUEST['searchstring'] ==""){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='category_id=:categoryid and product_type_id=:typeid';   
                            $criteria->params = array(':categoryid'=>$_REQUEST['category'],':typeid'=>$_REQUEST['type']);
                             $criteria->order = "name";
                            $products = Product::model()->findAll($criteria);
                            
                             $all_products = [];
                            foreach($products as $product){
                              if($product['is_custom_product']==0){
                                      if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                             }
                            
                            header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                                ));
                        }else{
                            $searchstring = explode('+',$_REQUEST['searchstring']);
                            $searchWords = preg_replace('/\s/', '', $searchstring);
                            $searchable_items = [];
                            foreach($searchWords as $word){
                                 $q = "SELECT id FROM product where name REGEXP '$word'" ;
                                 $cmd = Yii::app()->db->createCommand($q);
                                 $results = $cmd->query();
                                 foreach($results as $result){
                                     $criteria = new CDbCriteria();
                                     $criteria->select = '*';
                                     $criteria->condition='id=:id and (product_type_id=:typeid and category_id=:categoryid)';   
                                     $criteria->params = array(':id'=>$result['id'],':typeid'=>$_REQUEST['type'],':categoryid'=>$_REQUEST['category']);
                                     $criteria->order = "name";
                                     $product = Product::model()->find($criteria);
                                     if($product['is_custom_product']==0){
                                         if($product['displayable_on_store'] == 1){
                                             $searchable_items[] = $product;
                                         }
                                      }
                                 }
                            }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $searchable_items
                                        
                                
                                ));
                        }
                        
                    }
                    
                }
                }elseif($_REQUEST['service'] == "All" || $_REQUEST['service'] ==0){
                    if($_REQUEST['category'] =="All Categories" || $_REQUEST['category'] ==0){
                         if($_REQUEST['type'] =="All Types" || $_REQUEST['type'] ==0){
                          if($_REQUEST['searchstring'] ==""){
                            
                            $products = Product::model()->findAll(array('order'=>'name'));
                            $all_products = [];
                          foreach($products as $product){
                              if($product['is_custom_product']==0){
                                      if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                          }
                           header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                            ));
                        }else{
                            $searchstring = explode('+',$_REQUEST['searchstring']);
                            $searchWords = preg_replace('/\s/', '', $searchstring);
                            $searchable_items = [];
                            foreach($searchWords as $word){
                                 $q = "SELECT id FROM product where name REGEXP '$word'" ;
                                 $cmd = Yii::app()->db->createCommand($q);
                                 $results = $cmd->query();
                                 foreach($results as $result){
                                     $criteria = new CDbCriteria();
                                     $criteria->select = '*';
                                     $criteria->condition='id=:id';   
                                     $criteria->params = array(':id'=>$result['id']);
                                     $criteria->order = "name";
                                     $product = Product::model()->find($criteria);
                                     if($product['is_custom_product']==0){
                                         if($product['displayable_on_store'] == 1){
                                             $searchable_items[] = $product;
                                         }
                                      }
                                 }
                            }
                              header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $searchable_items
                                        
                                
                                ));
                           
                        }   
                             
                    }else if(is_numeric($_REQUEST['type']) && $_REQUEST['type']!= 0 ){
                        if($_REQUEST['searchstring'] ==""){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='product_type_id=:typeid';   
                            $criteria->params = array(':typeid'=>$_REQUEST['type']);
                            $criteria->order = "name";
                            $products = Product::model()->findAll($criteria);
                            
                          $all_products = [];
                          foreach($products as $product){
                              if($product['is_custom_product']==0){
                                       if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                          }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                                ));
                        }else{
                          $searchstring = explode('+',$_REQUEST['searchstring']);
                            $searchWords = preg_replace('/\s/', '', $searchstring);
                            $searchable_items = [];
                            foreach($searchWords as $word){
                                 $q = "SELECT id FROM product where name REGEXP '$word'" ;
                                 $cmd = Yii::app()->db->createCommand($q);
                                 $results = $cmd->query();
                                 foreach($results as $result){
                                     $criteria = new CDbCriteria();
                                     $criteria->select = '*';
                                     $criteria->condition='id=:id and product_type_id=:typeid';   
                                     $criteria->params = array(':id'=>$result['id'],':typeid'=>$_REQUEST['type']);
                                     $criteria->order = "name";
                                     $product = Product::model()->find($criteria);
                                     if($product['is_custom_product']==0){
                                         if($product['displayable_on_store'] == 1){
                                             $searchable_items[] = $product;
                                         }
                                      }
                                 }
                            }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $searchable_items
                                        
                                
                                ));
                        }
                        
                    }
                        
                        
                    }else if(is_numeric($_REQUEST['category']) && $_REQUEST['category']!= 0 ){
                        if($_REQUEST['type'] =="All Types" || $_REQUEST['type'] ==0){
                            if($_REQUEST['searchstring'] ==""){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='category_id=:categoryid';   
                            $criteria->params = array(':categoryid'=>$_REQUEST['category']);
                            $criteria->order = "name";
                            $products = Product::model()->findAll($criteria);
                            
                          $all_products = [];
                          foreach($products as $product){
                              if($product['is_custom_product']==0){
                                       if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                          }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                                ));
                        }else{
                          $searchstring = explode('+',$_REQUEST['searchstring']);
                            $searchWords = preg_replace('/\s/', '', $searchstring);
                            $searchable_items = [];
                            foreach($searchWords as $word){
                                 $q = "SELECT id FROM product where name REGEXP '$word'" ;
                                 $cmd = Yii::app()->db->createCommand($q);
                                 $results = $cmd->query();
                                 foreach($results as $result){
                                     $criteria = new CDbCriteria();
                                     $criteria->select = '*';
                                     $criteria->condition='id=:id and category_id=:categoryid';   
                                     $criteria->params = array(':id'=>$result['id'],':categoryid'=>$_REQUEST['category']);
                                     $criteria->order = "name";
                                     $product = Product::model()->find($criteria);
                                     if($product['is_custom_product']==0){
                                         if($product['displayable_on_store'] == 1){
                                             $searchable_items[] = $product;
                                         }
                                      }
                                 }
                            }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $searchable_items
                                        
                                
                                ));
                        }
                            
                        }else if(is_numeric($_REQUEST['type']) && $_REQUEST['type']!= 0 ){
                        if($_REQUEST['searchstring'] ==""){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='category_id=:categoryid and product_type_id=:typeid';   
                            $criteria->params = array(':categoryid'=>$_REQUEST['category'],':typeid'=>$_REQUEST['type']);
                            $criteria->order = "name";
                            $products = Product::model()->findAll($criteria);
                            
                             $all_products = [];
                            foreach($products as $product){
                              if($product['is_custom_product']==0){
                                      if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                             }
                            
                            header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                                ));
                        }else{
                            $searchstring = explode('+',$_REQUEST['searchstring']);
                            $searchWords = preg_replace('/\s/', '', $searchstring);
                            $searchable_items = [];
                            foreach($searchWords as $word){
                                 $q = "SELECT id FROM product where name REGEXP '$word'" ;
                                 $cmd = Yii::app()->db->createCommand($q);
                                 $results = $cmd->query();
                                 foreach($results as $result){
                                     $criteria = new CDbCriteria();
                                     $criteria->select = '*';
                                     $criteria->condition='id=:id and (product_type_id=:typeid and category_id=:categoryid)';   
                                     $criteria->params = array(':id'=>$result['id'],':typeid'=>$_REQUEST['type'],':categoryid'=>$_REQUEST['category']);
                                     $criteria->order = "name";
                                     $product = Product::model()->find($criteria);
                                     if($product['is_custom_product']==0){
                                         if($product['displayable_on_store'] == 1){
                                             $searchable_items[] = $product;
                                         }
                                      }
                                 }
                            }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $searchable_items
                                        
                                
                                ));
                        }
                        
                    }
                        
                        
                    }
                }elseif(is_numeric($_REQUEST['service']) && $_REQUEST['service'] !=0){
                    if($_REQUEST['category'] =="All Categories" || $_REQUEST['category'] ==0){
                        if($_REQUEST['type'] =="All Types" || $_REQUEST['type'] ==0){
                          if($_REQUEST['searchstring'] ==""){
                            
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='service_id=:serviceid';   
                            $criteria->params = array(':serviceid'=>$_REQUEST['service']);
                            $criteria->order = "name";
                            $products = Product::model()->findAll($criteria);
                            
                           $all_products = [];
                          foreach($products as $product){
                              if($product['is_custom_product']==0){
                                       if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                          }
                            
                              header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                            ));
                        }else{
                            
                            $searchstring = explode('+',$_REQUEST['searchstring']);
                            $searchWords = preg_replace('/\s/', '', $searchstring);
                            $searchable_items = [];
                            foreach($searchWords as $word){
                                 $q = "SELECT id FROM product where name REGEXP '$word'" ;
                                 $cmd = Yii::app()->db->createCommand($q);
                                 $results = $cmd->query();
                                 foreach($results as $result){
                                     $criteria = new CDbCriteria();
                                     $criteria->select = '*';
                                     $criteria->condition='id=:id and service_id=:serviceid';   
                                     $criteria->params = array(':id'=>$result['id'],':serviceid'=>$_REQUEST['service']);
                                     $criteria->order = "name";
                                     $product = Product::model()->find($criteria);
                                     if($product['is_custom_product']==0){
                                          if($product['displayable_on_store'] == 1){
                                             $searchable_items[] = $product;
                                         }
                                      }
                                 }
                            }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $searchable_items
                                        
                                
                                ));
                        }
                            
                        }else if(is_numeric($_REQUEST['type']) && $_REQUEST['type']!= 0 ){
                        if($_REQUEST['searchstring'] ==""){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='product_type_id=:typeid and service_id=:serviceid';   
                            $criteria->params = array(':typeid'=>$_REQUEST['type'],':serviceid'=>$_REQUEST['service']);
                            $criteria->order = "name";
                            $products = Product::model()->findAll($criteria);
                            
                             $all_products = [];
                            foreach($products as $product){
                              if($product['is_custom_product']==0){
                                      if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                             }
                            
                            header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                                ));
                        }else{
                            $searchstring = explode('+',$_REQUEST['searchstring']);
                            $searchWords = preg_replace('/\s/', '', $searchstring);
                            $searchable_items = [];
                            foreach($searchWords as $word){
                                 $q = "SELECT id FROM product where name REGEXP '$word'" ;
                                 $cmd = Yii::app()->db->createCommand($q);
                                 $results = $cmd->query();
                                 foreach($results as $result){
                                     $criteria = new CDbCriteria();
                                     $criteria->select = '*';
                                     $criteria->condition='id=:id and (service_id=:serviceid and product_type_id=:typeid)';   
                                     $criteria->params = array(':id'=>$result['id'],':serviceid'=>$_REQUEST['service'],':typeid'=>$_REQUEST['type']);
                                     $criteria->order = "name";
                                     $product = Product::model()->find($criteria);
                                     if($product['is_custom_product']==0){
                                         if($product['displayable_on_store'] == 1){
                                             $searchable_items[] = $product;
                                         }
                                      }
                                 }
                            }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $searchable_items
                                        
                                
                                ));
                        }
                        
                    }
                        
                        
                    }else if(is_numeric($_REQUEST['category']) && $_REQUEST['category']!= 0 ){
                        if($_REQUEST['type'] =="All Types" || $_REQUEST['type'] ==0){
                            if($_REQUEST['searchstring'] ==""){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='service_id=:serviceid and category_id=:categoryid';   
                            $criteria->params = array(':serviceid'=>$_REQUEST['service'],':categoryid'=>$_REQUEST['category']);
                            $criteria->order = "name";
                            $products = Product::model()->findAll($criteria);
                            
                          $all_products = [];
                          foreach($products as $product){
                              if($product['is_custom_product']==0){
                                       if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                          }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                                ));
                        }else{
                          $searchstring = explode('+',$_REQUEST['searchstring']);
                            $searchWords = preg_replace('/\s/', '', $searchstring);
                            $searchable_items = [];
                            foreach($searchWords as $word){
                                 $q = "SELECT id FROM product where name REGEXP '$word'" ;
                                 $cmd = Yii::app()->db->createCommand($q);
                                 $results = $cmd->query();
                                 foreach($results as $result){
                                     $criteria = new CDbCriteria();
                                     $criteria->select = '*';
                                     $criteria->condition='id=:id and (service_id = :serviceid and category_id=:categoryid)';   
                                     $criteria->params = array(':id'=>$result['id'],':serviceid'=>$_REQUEST['service'],':categoryid'=>$_REQUEST['category']);
                                     $criteria->order = "name";
                                     $product = Product::model()->find($criteria);
                                     if($product['is_custom_product']==0){
                                         if($product['displayable_on_store'] == 1){
                                             $searchable_items[] = $product;
                                         }
                                      }
                                 }
                            }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $searchable_items
                                        
                                
                                ));
                        }
                            
                        }else if(is_numeric($_REQUEST['type']) && $_REQUEST['type']!= 0 ){
                        if($_REQUEST['searchstring'] ==""){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='(service_id=:serviceid and category_id=:categoryid) and product_type_id=:typeid';   
                            $criteria->params = array(':serviceid'=>$_REQUEST['service'],':categoryid'=>$_REQUEST['category'],':typeid'=>$_REQUEST['type']);
                            $criteria->order = "name";
                            $products = Product::model()->findAll();
                            
                             $all_products = [];
                            foreach($products as $product){
                              if($product['is_custom_product']==0){
                                      if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                             }
                            
                            header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                                ));
                        }else{
                            $searchstring = explode('+',$_REQUEST['searchstring']);
                            $searchWords = preg_replace('/\s/', '', $searchstring);
                            $searchable_items = [];
                            foreach($searchWords as $word){
                                 $q = "SELECT id FROM product where name REGEXP '$word'" ;
                                 $cmd = Yii::app()->db->createCommand($q);
                                 $results = $cmd->query();
                                 foreach($results as $result){
                                     $criteria = new CDbCriteria();
                                     $criteria->select = '*';
                                     $criteria->condition='(id=:id and service_id=:serviceid) and (product_type_id=:typeid and category_id=:categoryid)';   
                                     $criteria->params = array(':id'=>$result['id'],':serviceid'=>$_REQUEST['service'],':typeid'=>$_REQUEST['type'],':categoryid'=>$_REQUEST['category']);
                                     $criteria->order = "name";
                                     $product = Product::model()->find($criteria);
                                     if($product['is_custom_product']==0){
                                         if($product['displayable_on_store'] == 1){
                                             $searchable_items[] = $product;
                                         }
                                      }
                                 }
                            }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $searchable_items
                                        
                                
                                ));
                        }
                        
                    }
                        
                        
                    }
                    
                }
             }
             
           
          
           
            
            
        } 
      
       
        
            
            
        
        /**
         * This is the function that list every product on the store
         */
      
        public function actionListEveryProducts(){
            
            $model = new Product;
            $userid = Yii::app()->user->id;
            
            $start = $_REQUEST['start'];
            $limit = $_REQUEST['limit'];
            
          
                       
            $criteria = new CDbCriteria();
            $criteria->select = '*';
           // $criteria->condition='is_rentable=:isrentable and displayable_on_store=:is_displayable';   
           // $criteria->params = array(':isrentable'=>1,':is_displayable'=>1);
            $criteria->order = "name";
            $criteria->offset = $start;
            $criteria->limit = $limit;     
            $products = Product::model()->findAll($criteria);
            
            $counts = $model->getTheToTalNumberOfProductsOnTheStore();
            
            if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "results"=>$counts,
                                     "product" => $products
                                        
                                
                            ));
                       
                }
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
         * This is the function that determines the type and size of video file
         */
        public function isVideoFileTypeAndSizeLegal(){
            
           if(isset($_FILES['video_filename']['name'])){
                $tmpName = $_FILES['video_filename']['tmp_name'];
                $videoFileName = $_FILES['video_filename']['name'];    
                $videoFileType = $_FILES['video_filename']['type'];
                $videoFileSize = $_FILES['video_filename']['size'];
            } 
         
            if($videoFileSize<=$this->getTheMaximumVideoSizeForThisService()){
                 if($videoFileType == 'video/mp4'){
                      return true;
                   }else{
                        return false;
            }
            }else{
                return false;
            }
      
        }

        
        /**
         * This is the function that obtains the maximum product video  size
         */
        public function getTheMaximumVideoSizeForThisService(){
            $model = new PlatformSettings;
            return $model->getTheMaximumVideoSizeForThisService();
          
        }


/**
         * This is the function that retrieves the previous icon of the task in question
         */
        public function retrieveThePreviousIconName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = Product::model()->find($criteria);
            
            
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
            $icon = Product::model()->find($criteria);
            
            
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
                $icon= Product::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
              // $directoryPath = "c:\\xampp\htdocs\cobuy_images\\icons\\";
              $directoryPath ="/home/oneroof/public_html/admin.oneroof.com.ng/cobuy_images/icons/";
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
                $icon= Product::model()->find($criteria);
                
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
                $icon= Product::model()->find($criteria);
                
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
            $poster = Product::model()->find($criteria);
            
            
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
            $poster = Product::model()->find($criteria);
            
            
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
                $poster= Product::model()->find($criteria);
                
                if($poster['headline_image']==$poster_filename){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
         /**
         * This is the function that removes an existing headline image file
         */
        public function removeTheExistingPosterFile($id){
            
            //retreve the existing headline view image from the database
            if($this->isThePosterNotTheDefault($id)){
                
                 $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $poster= Product::model()->find($criteria);
                
               // $directoryPath =  dirname(Yii::app()->request->scriptFile);
                $directoryPath = "/home/oneroof/public_html/admin.oneroof.com.ng/cobuy_images/posters/";
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
                $icon= Product::model()->find($criteria);
                
                if($icon['headline_image'] == 'product_header_unavailable.png' || $icon['headline_image'] ===NULL){
                    return false;
                }else{
                    return true;
                }
        }
        
        
        /**
         * This is the function that retrieves a product details
         */
        public function actionretrieveProductDetails(){
            
                $model = new PlatformSettings;        
                $member_id = Yii::app()->user->id;
            
                $product_id = $_REQUEST['product_id'];
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$product_id);
                $product= Product::model()->find($criteria);
                
                
                if($product===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $product,
                                    
                            )); 
                       
                }
                
                         
                              
                
            
        }
        
        
        /**
         * This is the function that confirms a member's subscription to a product
         */
        public function isMemberSubscribedToThisProduct($member_id,$product_id){
            
            $model = new MemberSubscribedToProducts;
            return $model->isMemberSubscribedToThisProduct($member_id,$product_id);
        }
        
        /**
         * This is the function that retrieves the subscription quantity
         */
        public function getTheProductSubscriptionQuantity($member_id,$product_id){
            $model = new MemberSubscribedToProducts;
            return $model->getTheProductSubscriptionQuantity($member_id,$product_id);
        }
        
         /**
         * This is the function that retrieves the remaining subscription quantity
         */
        public function getTheRemainingProductSubscriptionQuantity($member_id,$product_id){
            $model = new MemberSubscribedToProducts;
            return $model->getTheRemainingProductSubscriptionQuantity($member_id,$product_id);
        }
        
        
         /**
         * This is the function that retrieves the per subscription quantity delivery
         */
        public function getThePerDeliveryProductQuantity($member_id,$product_id){
            $model = new MemberSubscribedToProducts;
            return $model->getThePerDeliveryProductQuantity($member_id,$product_id);
        }
        
        
        /**
         * This is the function that confirms if subscription is escrowed
         */
        public function isThisSubscriptionEscrowed($member_id,$product_id){
            $model = new MemberSubscribedToProducts;
            return $model->isThisSubscriptionEscrowed($member_id,$product_id);
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
         * This is the function that moves a product video file to its path and return its name
         */
        public function moveTheProductVideoToItsPathAndReturnTheItsName($model,$video_filename){
            
            if(isset($_FILES['video_filename']['name'])){
                        $tmpName = $_FILES['video_filename']['tmp_name'];
                        $videoName = $_FILES['video_filename']['name'];    
                        $videoType = $_FILES['video_filename']['type'];
                        $videoSize = $_FILES['video_filename']['size'];
                  
                   }
                    
                    if($videoName !== null) {
                        if($model->id === null){
                          
                          if($video_filename != null){
                                $videoFileName = time().'_'.$video_filename;  
                            }else{
                                $videoFileName = $video_filename;  
                            }
                          
                           // upload the video file
                        if($videoName !== null){
                            	$videoPath = Yii::app()->params['video'].$videoFileName;
				move_uploaded_file($tmpName,  $videoPath);
                                        
                        
                                return $videoFileName;
                        }else{
                            $videoFileName = $video_filename;
                            return $videoFileName;
                        } // validate to save file
                        }else{
                            if($this->noNewProductVideoFileProvided($model->id,$video_filename)){
                                $videoFileName = $video_filename; 
                                return $videoFileName;
                            }else{
                             if($video_filename != null){
                                 if($this->removeTheExistingProductVideoFile($model->id)){
                                 $videoFileName = time().'_'.$video_filename; 
                                 $videoPath = Yii::app()->params['video'].$videoFileName;
                                   move_uploaded_file($tmpName,$videoPath);
                                   return $videoFileName;
                                    
                                  
                                    
                             }
                             }
                                
                                
                            }
                          
                        }
                      
                     }else{
                         $videoFileName = $video_filename;
                         return $videoFileName;
                     }
            
        }
        
        
        
        
        /**
         * This is the function to ascertain if a new product video file was provided or not
         */
        public function noNewProductVideoFileProvided($id,$video_filename){
            
                $criteria = new CDbCriteria();
                $criteria->select = 'id, video_filename';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $video= Product::model()->find($criteria);
                
                if($video['video_filename']==$video_filename){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        
        	 /**
         * This is the function that removes an existing product video file file
         */
        public function removeTheExistingProductVideoFile($id){
            
            //retreve the existing product front view file from the database
            
            if($this->isTheProductVideoFileTheDefault($id)== false){
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $video= Product::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "/home/oneroof/public_html/admin.oneroof.com.ng/cobuy_images/icons/";
               // $iconpath = '..\appspace_assets\icons'.$icon['icon'];
                $filepath =$directoryPath.$video['video_filename'];
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
         * This is the function that determines if product video is not the default
         */
        public function isTheProductVideoFileTheDefault($id){
               $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $video= Product::model()->find($criteria);
                
                if($video['video_filename'] == null){
                    return true;
                }else{
                    return false;
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
                $icon= Product::model()->find($criteria);
                
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
                $icon= Product::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "/home/oneroof/public_html/admin.oneroof.com.ng/cobuy_images/icons/";
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
                $icon= Product::model()->find($criteria);
                
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
                $icon= Product::model()->find($criteria);
                
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
                $icon= Product::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "/home/oneroof/public_html/admin.oneroof.com.ng/cobuy_images/icons/";
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
                $icon= Product::model()->find($criteria);
                
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
                $icon= Product::model()->find($criteria);
                
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
                $icon= Product::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "/home/oneroof/public_html/admin.oneroof.com.ng/cobuy_images/icons/";
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
                $icon= Product::model()->find($criteria);
                
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
                $icon= Product::model()->find($criteria);
                
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
                $icon= Product::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "/home/oneroof/public_html/admin.oneroof.com.ng/cobuy_images/icons/";
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
                $icon= Product::model()->find($criteria);
                
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
                $icon= Product::model()->find($criteria);
                
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
                $icon= Product::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "/home/oneroof/public_html/admin.oneroof.com.ng/cobuy_images/icons/";
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
                $icon= Product::model()->find($criteria);
                
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
                $icon= Product::model()->find($criteria);
                
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
                $icon= Product::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "/home/oneroof/public_html/admin.oneroof.com.ng/cobuy_images/icons/";
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
                $icon= Product::model()->find($criteria);
                
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
                $icon= Product::model()->find($criteria);
                
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
                $icon= Product::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "/home/oneroof/public_html/admin.oneroof.com.ng/cobuy_images/icons/";
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
                $icon= Product::model()->find($criteria);
                
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
                $icon= Product::model()->find($criteria);
                
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
                $icon= Product::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "/home/oneroof/public_html/admin.oneroof.com.ng/cobuy_images/icons/";
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
                $icon= Product::model()->find($criteria);
                
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
                $icon= Product::model()->find($criteria);
                
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
                $icon= Product::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "/home/oneroof/public_html/admin.oneroof.com.ng/cobuy_images/icons/";
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
                $icon= Product::model()->find($criteria);
                
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
                $icon= Product::model()->find($criteria);
                
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
                $icon= Product::model()->find($criteria);
                
                //$directoryPath =  dirname(Yii::app()->request->scriptFile);
               $directoryPath = "/home/oneroof/public_html/admin.oneroof.com.ng/cobuy_images/icons/";
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
                $icon= Product::model()->find($criteria);
                
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
            $icon = Product::model()->find($criteria);
            
            
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
            $icon = Product::model()->find($criteria);
            
            
            return $icon['product_front_view_size'];
        }
        
        
        
         /* This is the function that retrieves the previous product right side image view name
         */
        public function retrieveThePreviousProductRightSizeViewImageName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = Product::model()->find($criteria);
            
            
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
            $icon = Product::model()->find($criteria);
            
            
            return $icon['product_right_side_view_size'];
        }
        
        
        
        /* This is the function that retrieves the previous product inside image view name
         */
        public function retrieveThePreviousProductInsideViewImageName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = Product::model()->find($criteria);
            
            
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
            $icon = Product::model()->find($criteria);
            
            
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
            $icon = Product::model()->find($criteria);
            
            
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
            $icon = Product::model()->find($criteria);
            
            
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
            $icon = Product::model()->find($criteria);
            
            
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
            $icon = Product::model()->find($criteria);
            
            
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
            $icon = Product::model()->find($criteria);
            
            
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
            $icon = Product::model()->find($criteria);
            
            
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
            $icon = Product::model()->find($criteria);
            
            
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
            $icon = Product::model()->find($criteria);
            
            
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
            $icon = Product::model()->find($criteria);
            
            
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
            $icon = Product::model()->find($criteria);
            
            
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
            $icon = Product::model()->find($criteria);
            
            
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
            $icon = Product::model()->find($criteria);
            
            
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
            $icon = Product::model()->find($criteria);
            
            
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
            $icon = Product::model()->find($criteria);
            
            
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
         * This is the function that retrieves a product information
         */
        public function actionretrieveproductinformation(){
            
            $model = new OrderHasProducts;
            
            $member_id = Yii::app()->user->id;
            
            $product_id = $_POST['product_id'];
            
            //get all the product details
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$product_id);
            $product= Product::model()->find($criteria);
            
            
            //get the product bucket details
             //get all the product details
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$product['product_type_id']);
            $bucket= ProductType::model()->find($criteria);
            
            
            //get the service code
            $service_code = $this->getTheServiceCode($product['service_id']);
            
               //get the remaining quantity of items on sale
            $remaining_quantity = $this->getTheRemainingQuantityOfItemsOnSale($product_id,$product['cumulative_quantity'] );
            
            if($this->isTheProductAvailableForAService($product['product_type_id'])){
                
                //get the service subscription cost
                $paas_subscription_cost = $this->getThePaasSubscriptionCost($product['product_type_id']);
                
                //get the minimum quantity required for subscription
                $min_quantity_for_paas = $this->getTheMinimumQuantityRequiredForPaasSubscription($product['product_type_id']);
                
                //get the maximum quantity required for paas subscription
                $max_quantity_for_paas = $this->getTheMaximumQuantityForThisPaasSubscription($product['product_type_id']);
                
                //get the minimum paas duration
                $min_paas_duration = $this->getTheMinimumNumberOfPaasDuration($product['product_type_id']);
                
                 //get the maximum paas duration
                $max_paas_duration = $this->getTheMaximumNumberOfPaasDuration($product['product_type_id']);
                
                $is_paas = 1;
            }else{
                $is_paas = 0;
                $paas_subscription_cost = 0;
                $min_quantity_for_paas = 0;
                $max_quantity_for_paas = 0;
                $min_paas_duration = 1;
                $max_paas_duration = 1;
            }
            
            
            if($member_id !== null){
                
                        
            //retrieve information about this product if it is in the cart
            $quantity_of_purchase = $model->getThisProductQuantityOfPurchaseByThisMember($member_id,$product['id']);
            
            //get the member open order
            
           $order_id = $this->getTheOpenOrderInitiatedByMember($member_id);
            
          if($model->isThisProductAHamperInCart($order_id,$product_id)){
              $prevailing_retail_selling_price = $model->getThePrevailingPriceOfThisProductInCart($order_id,$product_id);
          }else if($model->isThisProductInCartQuotedOrEscrowed($order_id,$product_id)){
              $prevailing_retail_selling_price = $model->getThePrevailingRetailingPriceForThisQuotedAndEscrowedTransactionInCart($order_id,$product_id);
          }else{
              $prevailing_retail_selling_price = $this->getThePrevailingRetailSellingPriceForThisOrder($order_id,$product['id'],$product['prevailing_retail_selling_price']);
              
          }
          //retrieve information from the cart about this order
          
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='order_id=:orderid and product_id=:productid';
            $criteria->params = array(':orderid'=>$order_id,':productid'=>$product_id);
            $cartinfo= OrderHasProducts::model()->find($criteria);
         
      
             $member_selling_price = $this->getTheMemberSellingPriceForThisOrder($order_id,$product['id'],$product['per_portion_price']);
            
            $validity = $this->isOrderPriceStillValid($order_id,$product['id']);
            
            //get the additional information of this product if available in the member's cart
            $is_product_escrowed_only_at_purchase = $this->getTheEscrowStatusOfThisProductInTheCart($order_id,$product_id);
            
            $is_product_quoted_only_at_purchase = $this->getTheQuotedStatusOfThisProductInTheCart($order_id,$product_id);
            
            $is_product_quoted_and_escrowed_at_purchase = $this->getTheQuotedAndEscrowStatusOfThisProductInTheCart($order_id,$product_id);
            
            $escrow_id = $this->getTheEscrowIdOfTheProductInTheCart($order_id,$product_id);
            
            $quote_id = $this->getTheQuoteIdOfTheProductInTheCart($order_id, $product_id);
            
            if($this->getTheQuoteIdOfTheProductInTheCart($order_id, $product_id) >0){
                $quote_id = $this->getTheQuoteIdOfTheProductInTheCart($order_id, $product_id);
            }else{
                $quote_id =0;
            }
            $is_quote_with_futures = $this->isQuoteAlreadyAssociatedWithFutures($quote_id);
            
          

            if($this->doesProductHaveConstituents($product_id)){
                if($this->isMemberSubscribedToThisProduct($member_id,$product_id)){
                    //get the subscription quantity
                    $subscription_quantity = $this->getTheProductSubscriptionQuantity($member_id,$product_id);
                    //get the remain subscription quantity
                    $remaining_subscription_quantity = $this->getTheRemainingProductSubscriptionQuantity($member_id,$product_id);
                    //get the per delivery quantity
                     $per_delivery_quantity = $this->getThePerDeliveryProductQuantity($member_id,$product_id);
                     
                     //confirm if subscription is escrowed
                     $is_escrowed = $this->isThisSubscriptionEscrowed($member_id,$product_id);
                     
                   
                      header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $product,
                                     "subscription_quantity"=>$subscription_quantity,
                                     "remaining_subscription_quantity"=>$remaining_subscription_quantity,
                                     "need_escrow_agreement"=>$is_escrowed,
                                     "per_delivery_quantity"=>$per_delivery_quantity,
                                     "constituents" => true,
                                        //"product"=>$product,
                                        "quantity_requested"=> $quantity_of_purchase,
                                        "retail_selling_price"=>$prevailing_retail_selling_price,
                                        "member_sellig_price"=>$member_selling_price,
                                        "validity"=>$validity,
                                        "member" => $member_id,
                                        "service_code"=>strtolower($service_code),
                                        "remaining_quantity"=> $remaining_quantity,
                                        "is_escrowed_only"=>$is_product_escrowed_only_at_purchase,
                                        "is_quoted_only"=>$is_product_quoted_only_at_purchase,
                                        "is_both_quoted_and_escrowed"=>$is_product_quoted_and_escrowed_at_purchase,
                                        "escrow_id"=>$escrow_id,
                                        "quote_id"=>$quote_id,
                                        "future_trading"=>$is_quote_with_futures,
                                        "is_a_hamper"=>$model->isThisProductAHamperInCart($order_id,$product_id),
                                        "paas_cost"=>$paas_subscription_cost,
                                        "paas_min_quantity"=>$min_quantity_for_paas,
                                        "paas_max_quantity_allowed"=>$max_quantity_for_paas,
                                        "min_paas_duration"=>$min_paas_duration,
                                        "max_paas_duration"=>$max_paas_duration,
                                        "is_paas"=>$is_paas,
                                        "cartinfo"=>$cartinfo,
                                        "bucket"=>$bucket
                                    
                            ));
                    
                }else{
                     header('Content-Type: application/json');
                           echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "constituents" => true,
                                        "product"=>$product,
                                        "quantity_requested"=> $quantity_of_purchase,
                                        "retail_selling_price"=>$prevailing_retail_selling_price,
                                        "member_sellig_price"=>$member_selling_price,
                                         "validity"=>$validity,
                                        "member" => $member_id,
                                        "service_code"=>strtolower($service_code),
                                        "remaining_quantity"=> $remaining_quantity,
                                        "is_escrowed_only"=>$is_product_escrowed_only_at_purchase,
                                        "is_quoted_only"=>$is_product_quoted_only_at_purchase,
                                        "is_both_quoted_and_escrowed"=>$is_product_quoted_and_escrowed_at_purchase,
                                         "escrow_id"=>$escrow_id,
                                        "quote_id"=>$quote_id,
                                        "future_trading"=>$is_quote_with_futures,
                                        "is_a_hamper"=>$model->isThisProductAHamperInCart($order_id,$product_id),
                                        "paas_cost"=>$paas_subscription_cost,
                                        "paas_min_quantity"=>$min_quantity_for_paas,
                                        "paas_max_quantity_allowed"=>$max_quantity_for_paas,
                                        "min_paas_duration"=>$min_paas_duration,
                                        "max_paas_duration"=>$max_paas_duration,
                                        "is_paas"=>$is_paas,
                                        "cartinfo"=>$cartinfo,
                                        "bucket"=>$bucket
                                       )
                           );
                }   
            
            }else {
                if($this->isMemberSubscribedToThisProduct($member_id,$product_id)){
                    //get the subscription quantity
                    $subscription_quantity = $this->getTheProductSubscriptionQuantity($member_id,$product_id);
                    //get the remain subscription quantity
                    $remaining_subscription_quantity = $this->getTheRemainingProductSubscriptionQuantity($member_id,$product_id);
                    //get the per delivery quantity
                     $per_delivery_quantity = $this->getThePerDeliveryProductQuantity($member_id,$product_id); 
                     //confirm if subscription is escrowed
                     $is_escrowed = $this->isThisSubscriptionEscrowed($member_id,$product_id);
                     
                                        
                     header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "constituents" => false,
                                         "product"=>$product,
                                         "quantity_requested"=> $quantity_of_purchase,
                                         "retail_selling_price"=>$prevailing_retail_selling_price,
                                        "member_sellig_price"=>$member_selling_price,
                                          "validity"=>$validity,
                                          "member" => $member_id,
                                          "service_code"=>strtolower($service_code),
                                         "remaining_quantity"=> $remaining_quantity,
                                         "subscription_quantity"=>$subscription_quantity,
                                        "remaining_subscription_quantity"=>$remaining_subscription_quantity,
                                        "per_delivery_quantity"=>$per_delivery_quantity,
                                        "need_escrow_agreement"=>$is_escrowed,
                                         "is_escrowed_only"=>$is_product_escrowed_only_at_purchase,
                                        "is_quoted_only"=>$is_product_quoted_only_at_purchase,
                                        "is_both_quoted_and_escrowed"=>$is_product_quoted_and_escrowed_at_purchase,
                                         "escrow_id"=>$escrow_id,
                                        "quote_id"=>$quote_id,
                                        "future_trading"=>$is_quote_with_futures,
                                        "is_a_hamper"=>$model->isThisProductAHamperInCart($order_id,$product_id),
                                        "paas_cost"=>$paas_subscription_cost,
                                        "paas_min_quantity"=>$min_quantity_for_paas,
                                        "paas_max_quantity_allowed"=>$max_quantity_for_paas,
                                        "min_paas_duration"=>$min_paas_duration,
                                        "max_paas_duration"=>$max_paas_duration,
                                        "is_paas"=>$is_paas,
                                        "cartinfo"=>$cartinfo,
                                        "bucket"=>$bucket
                                       )
                           );
                    
                }else{
                    header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "constituents" => false,
                                         "product"=>$product,
                                         "quantity_requested"=> $quantity_of_purchase,
                                         "retail_selling_price"=>$prevailing_retail_selling_price,
                                        "member_sellig_price"=>$member_selling_price,
                                        "validity"=>$validity,
                                          "member" => $member_id,
                                         "service_code"=>strtolower($service_code),
                                         "remaining_quantity"=> $remaining_quantity,
                                         "is_escrowed_only"=>$is_product_escrowed_only_at_purchase,
                                        "is_quoted_only"=>$is_product_quoted_only_at_purchase,
                                        "is_both_quoted_and_escrowed"=>$is_product_quoted_and_escrowed_at_purchase,
                                         "escrow_id"=>$escrow_id,
                                        "quote_id"=>$quote_id,
                                        "future_trading"=>$is_quote_with_futures,
                                        "is_a_hamper"=>$model->isThisProductAHamperInCart($order_id,$product_id),
                                        "paas_cost"=>$paas_subscription_cost,
                                        "paas_min_quantity"=>$min_quantity_for_paas,
                                        "paas_max_quantity_allowed"=>$max_quantity_for_paas,
                                        "min_paas_duration"=>$min_paas_duration,
                                        "max_paas_duration"=>$max_paas_duration,
                                        "is_paas"=>$is_paas,
                                        "cartinfo"=>$cartinfo,
                                        "bucket"=>$bucket
                                        
                                       )
                           );
                    
                }
                
            }
                
            }else if($this->doesProductHaveConstituents($product_id)){
                header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "member" => $member_id,
                                         "product"=>$product,
                                         "constituents" => true,
                                         "service_code"=>strtolower($service_code),
                                         "remaining_quantity"=> $remaining_quantity,
                                         "paas_cost"=>$paas_subscription_cost,
                                        "paas_min_quantity"=>$min_quantity_for_paas,
                                        "paas_max_quantity_allowed"=>$max_quantity_for_paas,
                                        "min_paas_duration"=>$min_paas_duration,
                                        "max_paas_duration"=>$max_paas_duration,
                                        "is_paas"=>$is_paas
                                        
                                       )
                           );
                
            }else{
                 header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "member" => $member_id,
                                         "product"=>$product,
                                         "constituents" => false,
                                        "service_code"=>strtolower($service_code),
                                         "remaining_quantity"=> $remaining_quantity,
                                         "paas_cost"=>$paas_subscription_cost,
                                        "paas_min_quantity"=>$min_quantity_for_paas,
                                        "paas_max_quantity_allowed"=>$max_quantity_for_paas,
                                        "min_paas_duration"=>$min_paas_duration,
                                        "max_paas_duration"=>$max_paas_duration,
                                        "is_paas"=>$is_paas
                                        
                                       )
                           );
                
            }
            
            
            
        }
        
        
       
        
        /**
         * This is the function that retrieves a product information
         */
        public function actionretrieveproductfuturesinformation(){
            
            $model = new OrderHasProducts;
            
            $member_id = Yii::app()->user->id;
            
            $quote_id = $_POST['quote_id'];
            
            $product_id = $this->getThisProductIdGivenTheQuoteId($quote_id); 
           
            //get all the product details
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$product_id);
            $product= Product::model()->find($criteria);
            
            //get the service code
            
            $service_code = $this->getTheServiceCode($product['service_id']);
            
            //get the remaining quantity of items on sale
            $remaining_quantity = $this->getTheRemainingQuantityOfItemsOnSale($product_id,$product['cumulative_quantity'] );
            
            
            if($member_id !== null){
                
                        
            //retrieve information about this product if it is in the cart
            $quantity_of_purchase = $model->getThisProductQuantityOfPurchaseByThisMember($member_id,$product['id']);
            
            //get the member open order
            
           $order_id = $this->getTheOpenOrderInitiatedByMember($member_id);
            
          
            $prevailing_retail_selling_price = $this->getThePrevailingRetailSellingPriceForThisOrder($order_id,$product['id'],$product['prevailing_retail_selling_price']);
            $member_selling_price = $this->getTheMemberSellingPriceForThisOrder($order_id,$product['id'],$product['per_portion_price']);
            
            $validity = $this->isOrderPriceStillValid($order_id,$product['id']);
            
            if($this->doesProductHaveConstituents($product_id)){
                if($this->isMemberSubscribedToThisProduct($member_id,$product_id)){
                    //get the subscription quantity
                    $subscription_quantity = $this->getTheProductSubscriptionQuantity($member_id,$product_id);
                    //get the remain subscription quantity
                    $remaining_subscription_quantity = $this->getTheRemainingProductSubscriptionQuantity($member_id,$product_id);
                    //get the per delivery quantity
                     $per_delivery_quantity = $this->getThePerDeliveryProductQuantity($member_id,$product_id);
                     
                     //confirm if subscription is escrowed
                     $is_escrowed = $this->isThisSubscriptionEscrowed($member_id,$product_id);
                     
                   
                      header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $product,
                                     "subscription_quantity"=>$subscription_quantity,
                                     "remaining_subscription_quantity"=>$remaining_subscription_quantity,
                                     "need_escrow_agreement"=>$is_escrowed,
                                     "per_delivery_quantity"=>$per_delivery_quantity,
                                     "constituents" => true,
                                        //"product"=>$product,
                                        "quantity_requested"=> $quantity_of_purchase,
                                        "retail_selling_price"=>$prevailing_retail_selling_price,
                                        "member_sellig_price"=>$member_selling_price,
                                        "validity"=>$validity,
                                        "member" => $member_id,
                                        "service_code"=>strtolower($service_code),
                                        "remaining_quantity"=> $remaining_quantity 
                                    
                            ));
                    
                }else{
                     header('Content-Type: application/json');
                           echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "constituents" => true,
                                        "product"=>$product,
                                        "quantity_requested"=> $quantity_of_purchase,
                                        "retail_selling_price"=>$prevailing_retail_selling_price,
                                        "member_sellig_price"=>$member_selling_price,
                                        "validity"=>$validity,
                                        "member" => $member_id,
                                        "service_code"=>strtolower($service_code),
                                        "remaining_quantity"=> $remaining_quantity 
                                       )
                           );
                }   
            
            }else {
                if($this->isMemberSubscribedToThisProduct($member_id,$product_id)){
                    //get the subscription quantity
                    $subscription_quantity = $this->getTheProductSubscriptionQuantity($member_id,$product_id);
                    //get the remain subscription quantity
                    $remaining_subscription_quantity = $this->getTheRemainingProductSubscriptionQuantity($member_id,$product_id);
                    //get the per delivery quantity
                     $per_delivery_quantity = $this->getThePerDeliveryProductQuantity($member_id,$product_id); 
                     //confirm if subscription is escrowed
                     $is_escrowed = $this->isThisSubscriptionEscrowed($member_id,$product_id);
                     
                     header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "constituents" => false,
                                         "product"=>$product,
                                         "quantity_requested"=> $quantity_of_purchase,
                                         "retail_selling_price"=>$prevailing_retail_selling_price,
                                        "member_sellig_price"=>$member_selling_price,
                                          "validity"=>$validity,
                                          "member" => $member_id,
                                         "service_code"=>strtolower($service_code),
                                         "remaining_quantity"=> $remaining_quantity,
                                         "subscription_quantity"=>$subscription_quantity,
                                        "remaining_subscription_quantity"=>$remaining_subscription_quantity,
                                        "per_delivery_quantity"=>$per_delivery_quantity,
                                        "need_escrow_agreement"=>$is_escrowed,
                                       )
                           );
                    
                }else{
                    header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "constituents" => false,
                                         "product"=>$product,
                                         "quantity_requested"=> $quantity_of_purchase,
                                         "retail_selling_price"=>$prevailing_retail_selling_price,
                                        "member_sellig_price"=>$member_selling_price,
                                          "validity"=>$validity,
                                          "member" => $member_id,
                                         "service_code"=>strtolower($service_code),
                                         "remaining_quantity"=> $remaining_quantity,
                                        
                                       )
                           );
                    
                }
                
            }
                
            }else if($this->doesProductHaveConstituents($product_id)){
                header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "member" => $member_id,
                                         "product"=>$product,
                                         "constituents" => true,
                                         "service_code"=>strtolower($service_code),
                                         "remaining_quantity"=> $remaining_quantity 
                                        
                                       )
                           );
                
            }else{
                 header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "member" => $member_id,
                                         "product"=>$product,
                                         "constituents" => false,
                                        "service_code"=>strtolower($service_code),
                                         "remaining_quantity"=> $remaining_quantity
                                        
                                       )
                           );
                
            }
            
            
            
        }
        
        /**
         * This is the function that confirms if a quote has a futures facility
         */
        public function isQuoteAlreadyAssociatedWithFutures($quote_id){
            $model = new Futures;
            return $model->isQuoteAlreadyAssociatedWithFutures($quote_id);
        }
        
         /**
         * This is the function that retrieves the quote id of a product in cart 
         */
        public function getTheQuoteIdOfTheProductInTheCart($order_id, $product_id){
            $model = new OrderHasProducts;
            return $model->getTheQuoteIdOfTheProductInTheCart($order_id, $product_id);
        }
        
        
        /**
         * This is the function that retrieves the escrow id of a product in cart 
         */
        public function getTheEscrowIdOfTheProductInTheCart($order_id,$product_id){
            $model = new OrderHasProducts;
            return $model->getTheEscrowIdOfTheProductInTheCart($order_id,$product_id);
        }
        
        /**
         * This is the function that confirms if a product in a cart is escrowed only
         */
        public function getTheEscrowStatusOfThisProductInTheCart($order_id,$product_id){
            $model =  new OrderHasProducts;
            return $model->getTheEscrowStatusOfThisProductInTheCart($order_id,$product_id);
        }
        
        
         /**
         * This is the function that confirms if a product in a cart is quoted only
         */
        public function getTheQuotedStatusOfThisProductInTheCart($order_id,$product_id){
            $model =  new OrderHasProducts;
            return $model->getTheQuotedStatusOfThisProductInTheCart($order_id,$product_id);
        }
        
        
          /**
         * This is the function that confirms if a product in a cart is both quoted and escrowed 
         */
        public function getTheQuotedAndEscrowStatusOfThisProductInTheCart($order_id,$product_id){
            $model =  new OrderHasProducts;
            return $model->getTheQuotedAndEscrowStatusOfThisProductInTheCart($order_id,$product_id);
        }
        
        /**
         * This is the function that gets the product is given the quote id
         */
        public function getThisProductIdGivenTheQuoteId($quote_id){
            $model = new Quote;
            return $model->getThisProductIdGivenTheQuoteId($quote_id);
        }
        
        
        
        
         /**
         * This is the function that retrieves a product information
         */
        public function actionretrieveproducthistoryinformation(){
            
            $model = new OrderHasProducts;
            
            $member_id = Yii::app()->user->id;
            
            $product_id = $_REQUEST['product_id'];
            
            $order_id = $_REQUEST['order_id'];
            
            //get all the product details
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$product_id);
            $product= Product::model()->find($criteria);
            
            //get the service code
            
          $service_code = $this->getTheServiceCode($product['service_id']);
            
            //get the remaining quantity of items on sale
            $remaining_quantity = $this->getTheRemainingQuantityOfItemsOnSale($product_id,$product['cumulative_quantity'] );
         
            
            if($member_id !== null){
                
                        
            //retrieve information about this product if it is in the cart
            $quantity_of_purchase = $model->getThisProductQuantityOfPurchaseByThisMember($member_id,$product['id']);
            
          
            //get the member open order
            
           //$order_id = $this->getTheOpenOrderInitiatedByMember($member_id);
            
          
            $prevailing_retail_selling_price = $this->getThePrevailingRetailSellingPriceForThisOrder($order_id,$product['id'],$product['prevailing_retail_selling_price']);
            $member_selling_price = $this->getTheMemberSellingPriceForThisOrder($order_id,$product['id'],$product['per_portion_price']);
            
            $validity = $this->isOrderPriceStillValid($order_id,$product['id']);
            
            if($this->doesProductHaveConstituents($product_id)){
                 header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "constituents" => true,
                                        "product"=>$product,
                                        "quantity_requested"=> $quantity_of_purchase,
                                        "retail_selling_price"=>$prevailing_retail_selling_price,
                                        "member_sellig_price"=>$member_selling_price,
                                        "validity"=>$validity,
                                        "member" => $member_id,
                                        "service_code"=>strtolower($service_code),
                                        "remaining_quantity"=> $remaining_quantity 
                                       )
                           );
            }else {
                header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "constituents" => false,
                                         "product"=>$product,
                                         "quantity_requested"=> $quantity_of_purchase,
                                         "retail_selling_price"=>$prevailing_retail_selling_price,
                                        "member_sellig_price"=>$member_selling_price,
                                          "validity"=>$validity,
                                          "member" => $member_id,
                                         "service_code"=>strtolower($service_code),
                                         "remaining_quantity"=> $remaining_quantity
                                       )
                           );
            }
                
            }else if($this->doesProductHaveConstituents($product_id)){
                header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "member" => $member_id,
                                         "product"=>$product,
                                         "constituents" => true,
                                         "service_code"=>strtolower($service_code),
                                         "remaining_quantity"=> $remaining_quantity 
                                        
                                       )
                           );
                
            }else{
                 header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "member" => $member_id,
                                         "product"=>$product,
                                         "constituents" => false,
                                        "service_code"=>strtolower($service_code),
                                         "remaining_quantity"=> $remaining_quantity
                                        
                                       )
                           );
                
            }
            
            
            
        }
        
        
        
        /**
         * This is the  function that gets a service code
         */
        public function getTheServiceCode($service_id){
            $model = new Service;
            
            return $model->getTheServiceCode($service_id);
        }
        
        
        /**
         * This is the function that gets the remaining quantity of items on sale
         */
        public function getTheRemainingQuantityOfItemsOnSale($product_id,$quantity ){
            $model = new OrderHasProducts;
            return $model->getTheRemainingQuantityOfItemsOnSale($product_id,$quantity);
            
        }
        
        
        /**
         * This is the function that determines if order price is still valid
         */
        public function isOrderPriceStillValid($order_id,$product_id){
            $model = new OrderHasProducts;
            
            return $model->isOrderPriceStillValid($order_id,$product_id);
        }
        
        
        
        /**
         * This is the function that gets the open order initiated by member
         */
        public function getTheOpenOrderInitiatedByMember($member_id){
            $model = new Order;
            
            return $model->getTheOpenOrderInitiatedByMember($member_id);
        }
        
        
        
        /**
         * This is the function that gets the prevailing selling price of a product
         */
        public function getThePrevailingRetailSellingPriceForThisOrder($order_id,$product_id,$prevailing_retail_selling_price){
            
            $model = new Order;
            $order_start_price_validity_date = $this->getThisOrderStartPriceValidityDate($order_id,$product_id); 
            
            $order_end_price_validity_date = $this->getThisOrderEndPriceValidityDate($order_id,$product_id); 
            
            $today = getdate(mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
                    
            
            if($this->doesProductHaveConstituents($product_id)){
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
                    
            
            if($this->doesProductHaveConstituents($product_id)){
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
                
            }else{
                return $member_retail_selling_price;
            }
        }
        
        
        /**
         * This is the function that gets the retail selling price for a product
         */
        public function getTheRetailSellingPriceFromThisOrder($order_id,$product_id){
            $model = new OrderHasProducts;
            
            return $model->getTheRetailSellingPriceFromThisOrder($order_id,$product_id);
        }
        
        
         /**
         * This is the function that gets the member selling price for a product
         */
        public function getTheMemberSellingPriceFromThisOrder($order_id,$product_id){
            $model = new OrderHasProducts;
            
            return $model->getTheMemberSellingPriceFromThisOrder($order_id,$product_id);
        }
        
        
        
        /**
         * This is the function that gets this order start price validity date
         */
        public function getThisOrderStartPriceValidityDate($order_id,$product_id){
            $model = new OrderHasProducts;
            
            return $model->getThisOrderStartPriceValidityDate($order_id,$product_id);
        }
        
        
          /**
        
        
      /**
       * This is the function that gets the prevailing retailing selling price from an order
       */
        public function getThisOrderEndPriceValidityDate($order_id,$product_id){
            $model = new OrderHasProducts;
            
            return $model->getThisOrderEndPriceValidityDate($order_id,$product_id);
        }
        
        
      
        
        /**
         * This is the function that determines if a product has constituents
         */
        public function doesProductHaveConstituents($product_id){
            $model = new Product;
            
            return $model->doesProductHaveConstituents($product_id);
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
            
            $today = getdate(mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
            
            
            //get all the product details
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$product_id);
            $product= Product::model()->find($criteria);
            
             //retrieve information about this product if it is in the cart
            //$quantity_of_purchase = $this->getThisProductQuantityOfPurchaseByThisMember($member_id,$product['id']);
            
            //get the member open order
            
            if(is_numeric($member_id)){
                $order_id = $model->getTheOpenOrderInitiatedByMember($member_id);
            
          if($_REQUEST['operation'] == 'noncart'){
              $prevailing_retail_selling_price = $this->getTheCurrentPrevailingRetailPriceOfThisPack($product_id);
              $member_selling_price = $this->getTheCurrentMemberOnlyPriceOfThisPack($product_id);
          }else{
           $prevailing_retail_selling_price = $this->getThePrevailingRetailSellingPriceForThisOrder($order_id,$product_id,$product['prevailing_retail_selling_price']);
            $member_selling_price = $this->getTheMemberSellingPriceForThisOrder($order_id,$product['id'],$product['per_portion_price']);
          }
           
            
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
                                        "member_selling_price"=>$member_selling_price,
                                        "operation"=>'this one'
                                       )
                           );
                }
            }else{
                header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "validity" => false,
                                        "prevailing_retail_selling_price"=>$prevailing_retail_selling_price,
                                        "member_selling_price"=>$member_selling_price,
                                        
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
         * This is the function that gets the current prevailing price of a product
         */
        public function getTheCurrentPrevailingRetailPriceOfThisPack($product_id){
            $model = new Product;
            return $model->getTheCurrentPrevailingRetailPriceOfThisPack($product_id);
        }
        
        
         /**
         * This is the function that gets the current member price price of a product
         */
        public function getTheCurrentMemberOnlyPriceOfThisPack($product_id){
            $model = new Product;
            return $model->getTheCurrentMemberOnlyPriceOfThisPack($product_id);
        }
        
        
        
        /**
         * This is the function that retrieves a members products in cart
         */
        public function actionListMemberProductsInCart(){
            
            $model = new Order;
            $member_id = Yii::app()->user->id;
            
            if($model->isMemberWithOpenOrder($member_id)){
                $order_id = $model->getTheOpenOrderInitiatedByMember($member_id);
                $products = $this->getAllProductsInThisOrder($order_id);
                
                $all_products = [];
                
                foreach($products as $product){
                     $criteria = new CDbCriteria();
                     $criteria->select = '*';
                     $criteria->condition='id=:id';
                     $criteria->params = array(':id'=>$product);
                     $incart= Product::model()->find($criteria);
                     
                     $all_products[] = $incart;
                }
                
                header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "product" => $all_products
                                       )
                           );
                
                
            }else{
                
                header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        //"validity" => true,
                                       )
                           );
            }
            
            
        }
	
        
        
        /**
         * This is the functin that retrieves all the products in an order
         */
        public function getAllProductsInThisOrder($order_id){
            
            $model = new OrderHasProducts;
            
            return $model->getAllProductsInThisOrder($order_id);
        }
        
        
        
        /**
         * This is the function that removes a product from the cart for a member
         */
        public function actionremoveThisProductFromCart(){
            
            $model = new Product;
            $product_id = $_REQUEST['product_id'];
            $delivery_type = $_REQUEST['delivery_type'];
            
            $member_id = Yii::app()->user->id;
            
            $product_name = $model->getThisProductName($product_id);
            
            $order_id = $this->getTheOpenOrderInitiatedByMember($member_id);
            
            if($this->isRemovalOfThisProductFromCartForThisMemberSuccessful($member_id,$product_id)){
                 //get the total amount of all items in the cart for this member
                $cart_amount = $this->getTheTotalGrossAmountOfProductsInTheCart($order_id);
                $delivery_charges = $this->getTheDeliveryChargeForThisTransaction($cart_amount,$delivery_type);
                $msg = "'$product_name' product is successfully removed";
                 header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                        "cart_amount" => $cart_amount,
                                        "delivery_charges"=>$delivery_charges
                                       )
                           );
                
            }else{
                $msg = "'$product_name' product could not be removed. Please contact the service desk for assistance";
                 header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg
                                       )
                           ); 
            }
            
            
            
        }
        
        
        /**
         * This is the function that ensures the removal of product from the cart
         */
        public function isRemovalOfThisProductFromCartForThisMemberSuccessful($member_id,$product_id){
            
            $model = new OrderHasProducts;
            
            return $model->isRemovalOfThisProductFromCartForThisMemberSuccessful($member_id,$product_id);
        }
        
        
        
        
        /**
         * This is the function that saves that changes to a product in the cart
         */
        public function actionsaveChangesToThisProductInTheCart(){
            
            $model = new Product;
            $product_id = $_REQUEST['product_id'];
             $member_id = Yii::app()->user->id;
            
            $product_name = $model->getThisProductName($product_id);
            
            $order_id = $this->getTheOpenOrderInitiatedByMember($member_id);
            $delivery_type = $_REQUEST['delivery_type'];
            if($_REQUEST['decision'] == 'buy'){
                 $quantity_of_purchase = $_REQUEST['quantity_of_purchase'];
                $prevailing_retail_selling_price = $_REQUEST['prevailing_retail_selling_price'];
                $cobuy_member_price = $_REQUEST['per_portion_price'];
                $start_price_validity_period = date("Y-m-d H:i:s", strtotime($_REQUEST['start_price_validity_period']));
                $end_price_validity_period = date("Y-m-d H:i:s", strtotime($_REQUEST['end_price_validity_period']));
                $amount_save_on_purchase = $_REQUEST['amount_save_on_purchase'];
               
                
              
            if($this->isSavingOfChangesInThisProductInCartSuccessful($member_id,$product_id,$quantity_of_purchase,$prevailing_retail_selling_price,$cobuy_member_price,$start_price_validity_period,$end_price_validity_period, $amount_save_on_purchase)){
              //  if($this->isProductConstituentsModificationSuccessful($product_id,$order_id)){
                     //get the total amount of all items in the cart for this member
                $cart_amount = $this->getTheTotalGrossAmountOfProductsInTheCart($order_id);
                $delivery_charges = $this->getTheDeliveryChargeForThisTransaction($cart_amount,$delivery_type);
                $msg = "Changes of '$product_name' product in cart is successfully effected";
                 header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                        "cart_amount" => $cart_amount,
                                        "delivery_charges"=>$delivery_charges
                                       )
                           );
            
                
            }else{
                $msg = "Changes to '$product_name' product in cart could not be effected. Please contact the service desk for assistance";
                 header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg
                                       )
                           ); 
            }
            
        }else if($_REQUEST['decision'] == 'rent'){
             $quantity_for_rent = $_REQUEST['quantity_for_rent'];
             $rent_duration = $_REQUEST['rent_duration'];
            if($this->isModifyingTheRentParametersASuccess($order_id,$product_id,$quantity_for_rent,$rent_duration)){
                 $cart_amount = $this->getTheTotalGrossAmountOfProductsInTheCart($order_id);
                 $delivery_charges = $this->getTheDeliveryChargeForThisTransaction($cart_amount,$delivery_type);
                 $msg = "Changes of '$product_name' product in cart is successfully effected";
                 header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                        "cart_amount" => $cart_amount,
                                        "delivery_charges"=>$delivery_charges
                                       )
                           );
            
                
            }else{
                $msg = "Changes to '$product_name' product in cart could not be effected. Please contact the service desk for assistance";
                 header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg
                                       )
                           ); 
            }
            
            
        }else if($_REQUEST['decision'] == 'paas'){
            
             $paas_product_quantity = $_REQUEST['paas_product_quantity'];
             $actual_paas_duration = $_REQUEST['actual_paas_duration'];
             
             if($this->ifModifyingThePaasParamerIsASuccess($order_id,$product_id,$paas_product_quantity,$actual_paas_duration)){
                 $cart_amount = $this->getTheTotalGrossAmountOfProductsInTheCart($order_id);
                 $delivery_charges = $this->getTheDeliveryChargeForThisTransaction($cart_amount,$delivery_type);
                 $msg = "Changes of '$product_name' product in cart is successfully effected";
                 header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                        "cart_amount" => $cart_amount,
                                        "delivery_charges"=>$delivery_charges
                                       )
                           );
            
                
            }else{
                $msg = "Changes to '$product_name' product in cart could not be effected. Please contact the service desk for assistance";
                 header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg
                                       )
                           ); 
            }
                 
             }else if($_REQUEST['decision'] == 'faas'){
                 
             $faas_product_quantity = $_REQUEST['faas_product_quantity'];
             $actual_faas_duration = $_REQUEST['actual_faas_duration'];
             
             if($this->ifModifyingTheFaasParamerIsASuccess($order_id,$product_id,$faas_product_quantity,$actual_faas_duration)){
                 $cart_amount = $this->getTheTotalGrossAmountOfProductsInTheCart($order_id);
                 $delivery_charges = $this->getTheDeliveryChargeForThisTransaction($cart_amount,$delivery_type);
                 $msg = "Changes of '$product_name' product in cart is successfully effected";
                 header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                        "cart_amount" => $cart_amount,
                                        "delivery_charges"=>$delivery_charges
                                       )
                           );
            
                
            }else{
                $msg = "Changes to '$product_name' product in cart could not be effected. Please contact the service desk for assistance";
                 header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg
                                       )
                           );
                 
                 
                 
             }
        
            
        }
    }
        
    
    
    /**
     * This is the function that modifies the faas parameter in cart
     */
    public function ifModifyingTheFaasParamerIsASuccess($order_id,$product_id,$faas_product_quantity,$actual_faas_duration){
        $model = new OrderHasProducts;
        return $model->ifModifyingTheFaasParamerIsASuccess($order_id,$product_id,$faas_product_quantity,$actual_faas_duration);
        
    }
        
        /**
         * This  is the function that modifies the paas information in cart
         */
        public function ifModifyingThePaasParamerIsASuccess($order_id,$product_id,$paas_product_quantity,$actual_paas_duration){
            $model = new OrderHasProducts;
            return $model->ifModifyingThePaasParamerIsASuccess($order_id,$product_id,$paas_product_quantity,$actual_paas_duration);
        }
        
        
        
        /**
         * This is the function that modifies rent information in the cart
         */
        public function isModifyingTheRentParametersASuccess($order_id,$product_id,$quantity_for_rent,$rent_duration){
            $model = new OrderHasProducts;
            return $model->isModifyingTheRentParametersASuccess($order_id,$product_id,$quantity_for_rent,$rent_duration);
        }
        
        /**
         * This is the function that gets the delivery charges of a transaction
         */
        public function getTheDeliveryChargeForThisTransaction($cart_amount,$delivery_type){
            $model = new PlatformSettings;
            return $model->getTheDeliveryChargeForThisTransaction($cart_amount,$delivery_type);
            
        }
        
        
        
        /**
         * This is the function that confirms the modification of product constituents
         */
        public function isProductConstituentsModificationSuccessful($product_id,$order_id){
            
            $model = new OrderHasConstituents;
            return $model->isProductConstituentsModificationSuccessful($product_id,$order_id);
            
            
        }
        
        
        /**
         * This is the function that gets all the amounts in the cart
         * 
         */
        public function getTheTotalGrossAmountOfProductsInTheCart($order_id){
            $model = new OrderHasProducts;
            return $model->getTheTotalGrossAmountOfProductsInTheCart($order_id);
        }
        
        
        
        /**
         * This is the function that effects product in the cart changes
         */
        public function isSavingOfChangesInThisProductInCartSuccessful($member_id,$product_id,$quantity_of_purchase,$prevailing_retail_selling_price,$cobuy_member_price,$start_price_validity_period,$end_price_validity_period, $amount_save_on_purchase){
            
            $model = new OrderHasProducts;
            
            return $model->isSavingOfChangesInThisProductInCartSuccessful($member_id,$product_id,$quantity_of_purchase,$prevailing_retail_selling_price,$cobuy_member_price,$start_price_validity_period,$end_price_validity_period, $amount_save_on_purchase);
        }

        
        
       
		
        /**
         * This is the function that retrieves  member product orders not exceeding six months
         */
        public function actionListMemberOrdersNotExceedingSixMonths(){
            $model = new Order;
            
            $member_id = Yii::app()->user->id;
            
            //get all the member orders not exceeding six months
            
            $orders = $model->getAllMemberOrdersNotExceedingSixMonths($member_id);
            
           //get all the products in each order
           
            if($orders != null){
                 $all_products = [];
             foreach($orders as $order){
                //get all the products in each order
                $products = $model->getAllTheProductsOnThisOrder($order);
                
               foreach($products as $product){
                    $criteria = new CDbCriteria();
                     $criteria->select = '*';
                     $criteria->condition='id=:id';
                     $criteria->params = array(':id'=>$product);
                     $prod= Product::model()->find($criteria);
                     
                     $all_products[] = $prod;
                }
            }
            
             if($orders===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $all_products,
                                     "orders"=>$orders
                                    
                                
                            ));
                       
                }
                
                
            }else{
                if($orders===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     
                                        
                                
                            ));
                       
                }
            }
            
            
            
        }
        
        
        
        /**
         * This is the function that retrieves member product orders beyond six months
         */
        public function actionListMemberOrdersBeyondSixMonths(){
            $model = new Order;
            
            $member_id = Yii::app()->user->id;
            
            //get all the member orders not exceeding six months
            
            $orders = $model->getAllMemberOrdersBeyondSixMonths($member_id);
            
           if($orders != null){
                 $all_products = [];
                foreach($orders as $order){
                //get all the products in each order
                $products = $model->getAllTheProductsOnThisOrder($order);
                
               
                foreach($products as $product){
                     $criteria = new CDbCriteria();
                     $criteria->select = '*';
                     $criteria->condition='id=:id';
                     $criteria->params = array(':id'=>$product);
                     $prod= Product::model()->find($criteria);
                     
                     $all_products[] = $prod;
                }
            }
            
             if($orders===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $all_products,
                                    "order" => $orders
                                        
                                
                            ));
                       
                }
                
                
            }else{
                $msg="No record found";    
                    header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    'msg'=>$msg
                                     
                                        
                                
                            ));
                       
               
            }
        }
        
        
        /**
         * This is the function that zerorises the prices of a product or pack
         */
        public function actionzerorisethisproductprice(){
            $product_id = $_POST['id'];
            $product_name = $this->getThisProductName($product_id);
            if($this->isThisProductPricesZerorisedSuccessfully($product_id)){
                $msg="The prices of '$product_name' product were successfully zerorised";    
                    header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    'msg'=>$msg
                                     
                                        
                                
                            ));
            }else{
                $msg="Could not zerorise the  prices of '$product_name' product. Please contact the support team for assistance";    
                    header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    'msg'=>$msg
                                     
                                        
                                
                            ));
            }
        }
        
       /**
        * This is the function that zerorised the prices of a product or pack
        */
        public function isThisProductPricesZerorisedSuccessfully($product_id){
            $model = new Product;
            return $model->isThisProductPricesZerorisedSuccessfully($product_id);
        }
        
        
        /**
         * This is the function that request to trade on a product
         */
        public function actionrequestToTradeOnProduct(){
            
            $model = new Product;
            
            $member_id = Yii::app()->user->id;
            
            $product_code = $_REQUEST['product_code'];
        if($this->canMemberTradeOnThisProduct($member_id)){  
            
            if($this->isMembershipSubscriptionActive($member_id)){
                if($product_code != ""){
              
               if($model->isProductCodeValid($product_code)){
                
                //get the id of this product
            $product_id = $model->getTheProductIdOfThisProductGivenItsProductCode($product_code);
            
            //get the product name given its id
            $product_name = $model->getThisProductName($product_id);
      
           if($model->isProductTradable($product_id)){
           if($this->isProductNotAlreadyAssignedToThisMember($member_id,$product_id)){
            if($this->isTheAssignmentOfProductToMemberASuccess($member_id,$product_id)){
                $msg = "You request to trade on the '$product_name' product have been received and it is undergoing the approval process.";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg,
                                            "member_id"=>$member_id)
                                        );
                
            }else{
                $msg = "The request to trade on '$product_name' product failed. Please contact customer care for detail";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
            }
            
        }else if($this->isAssignedProductToMemberNotYetActive($member_id,$product_id)){
            $msg = "Your previous request for the authority to trade on '$product_name' product is undergoing approval. Please be patient or contact customer care for detail";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
            
        }else{
            $msg = "You had already been authorised to trade on '$product_name' product. If you are still having challenges with this, please contact customer care";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
        }
           
           
       }else{
            $msg = "The '$product_name' product is not available for merchant trading. So your request is denied";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                
           
       }     
        
                
                
            }else{
                $msg = "The product code you provided is not valid. Plesae check the code and try again";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                
            }
              
              
          }else{
               $msg = "Please enter a valid product code and try again";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
          } 
                
                
            }else{
                $msg = "Your subscription had either expired or its inactive. To continue trading on this store you need to either renew your current subscription or change your subscription to a minimum of 'Oneroof Basic Prime' subscription type ";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                
            }
           
           
       }else{
           $msg = "You do not possess the right membership subscription type for trading on this store. Upgrade your membership to a minimum of 'Oneroof Basic Prime' and try again.";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
       }     
       
            
       
   }
   
   
   /**
    * This is the function that determines if a member's subscription is active
    */
   public function isMembershipSubscriptionActive($member_id){
       $model = new MembershipSubscription;
       return $model->isMembershipSubscriptionActive($member_id);
   }
   
   /**
    * This is the function that determines if a member can trade on a this store
    */
   public function canMemberTradeOnThisProduct($member_id){
       $model = new MembershipSubscription;
       return $model->canMemberTradeOnThisProduct($member_id);
   }
   
   
   /**
    * This is the function that confirms if a product is not already assigned to a member
    */
   public function isProductNotAlreadyAssignedToThisMember($member_id,$product_id){
       $model = new ProductHasVendor;
       return $model->isProductNotAlreadyAssignedToThisMember($member_id,$product_id);
   }
   
   /**
    * This is the function that determines the status of the assigned product to a vendor
    */
   public function isAssignedProductToMemberNotYetActive($member_id,$product_id){
       $model = new ProductHasVendor;
       return $model->isAssignedProductToMemberNotYetActive($member_id,$product_id);
   }
   
   /**
    * This is the function that determines if the assignment of product to a member is successful
    */
   public function isTheAssignmentOfProductToMemberASuccess($member_id,$product_id){
       $model = new ProductHasVendor;
       return $model->isTheAssignmentOfProductToMemberASuccess($member_id,$product_id);
   }
   
   
   
   
   /**
    * This is the function that requests for product subscription
    */
   public function actionrequestForProductSubscription(){
       
       $model = new Product;
            
       $member_id = Yii::app()->user->id;
            
       $product_code = $_POST['product_code'];
       
       $subscription_quantity = $_POST['subscription_quantity'];
       
       $subscription_type = strtolower($_POST['type']);
            
             
       if($model->isProductCodeValid($product_code)){
                
                //get the id of this product
       $product_id = $model->getTheProductIdOfThisProductGivenItsProductCode($product_code);
            
       //get the product name given its id
       $product_name = $model->getThisProductName($product_id);
       
       //get the minimum subscription qunatity for this product
       $min_subscription_quantity = $model->getTheMinimumQuantityOfPurchaseFoThisProduct($product_id);
       
       if($model->isThisAValidSubscriptionQuantity($min_subscription_quantity,$subscription_quantity)){
           if($model->isProductIdealforSubscription($product_id)){
           if($this->isProductNotAlreadySubscribedToByMember($member_id,$product_id)){
            if($this->isTheSuscriptionOfProductToMemberASuccess($member_id,$product_id,$subscription_type,$subscription_quantity)){
                $msg = "You have successfully subscribed to '$product_name' product. Please contact our customer care for any assistance";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg)
                                        );
                
            }else{
                $msg = "Subscription to this product was not successful. Please contact customer care for detail";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
            }
            
        }else if($this->isSubscribedProductToMemberNotYetActive($member_id,$product_id)){
            $msg = "Your previous request for '$product_name' product subscription is still pending. Please be patient or contact customer care for detail";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
            
        }else{
            $msg = "You had already subscribed to '$product_name' product. If you need further assistance, please contact customer care";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
        }
           
           
       }else{
           $msg = "The '$product_name' product is not available for subscription. Please choose another product";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
           
           
       }
           
       }else{
            $msg = "The subscription quantity cannot be lower than the minimum quantity of purchase of this product, which is '$min_subscription_quantity'";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
       }
       
       
       
            
        
                
                
            }else{
                $msg = "The product code you provided is not valid. Plesae check the code and try again";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                
            }
              
              
         
   }
   
   
   /**
    * This is the functoon that confirms if a product is already subscribed to by a member
    */
   public function isProductNotAlreadySubscribedToByMember($member_id,$product_id){
       $model = new MemberSubscribedToProducts;
       return $model->isProductNotAlreadySubscribedToByMember($member_id,$product_id);
   }
   
   
   /**
    * This is the function that determines if a subscribed product to a member is active
    */
   public function isSubscribedProductToMemberNotYetActive($member_id,$product_id){
       
       $model = new MemberSubscribedToProducts;
       return $model->isSubscribedProductToMemberNotYetActive($member_id,$product_id);
   }
   
   
   /**
    * This is the function that confirms if a product subscription was successful
    */
   public function isTheSuscriptionOfProductToMemberASuccess($member_id,$product_id,$subscription_type,$subscription_quantity){
       
       $model = new MemberSubscribedToProducts;
       return $model->isTheSuscriptionOfProductToMemberASuccess($member_id,$product_id,$subscription_type,$subscription_quantity);
   }
   
   
   
   /**
    * This is the function that provides pre subscription services of a product to a member
    */
   public function actionrequestForPreProductSubscription(){
       
       $model = new Product;
            
       $member_id = Yii::app()->user->id;
            
       $product_code = $_POST['product_code'];
       
       $subscription_quantity = $_POST['subscription_quantity'];
       
       $subscription_type = strtolower($_POST['type']);
            
             
       if($model->isProductCodeValid($product_code)){
                
                //get the id of this product
       $product_id = $model->getTheProductIdOfThisProductGivenItsProductCode($product_code);
            
       //get the product name given its id
       $product_name = $model->getThisProductName($product_id);
       
        //get the minimum subscription qunatity for this product
       $min_subscription_quantity = $model->getTheMinimumQuantityOfPurchaseFoThisProduct($product_id);
       
       if($model->isThisAValidSubscriptionQuantity($min_subscription_quantity,$subscription_quantity)){
           if($model->isProductIdealforSubscription($product_id)){
           if($this->isProductNotAlreadySubscribedToByMember($member_id,$product_id)){
            if($this->isTheSuscriptionOfProductToMemberASuccess($member_id,$product_id,$subscription_type,$subscription_quantity)){
                $msg = "You have successfully subscribed to '$product_name' product. Please contact our customer care for any assistance";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "product_id" => $product_id,
                                           "subscription_quantity"=>$subscription_quantity
                                             )
                                        );
                
            }else{
                $msg = "Subscription to this product was not successful. Please contact customer care for detail";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
            }
            
        }else if($this->isSubscribedProductToMemberNotYetActive($member_id,$product_id)){
            $msg = "Your previous request for '$product_name' product subscription is still pending. Please be patient or contact customer care for detail";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
            
        }else{
            $msg = "You had already subscribed to '$product_name' product. If you need further assistance, please contact customer care";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
        }
           
           
       }else{
           $msg = "The '$product_name' product is not available for subscription. Please choose another product";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
           
       }
            
           
           
       }else{
            $msg = "The subscription quantity cannot be lower than the minimum quantity of purchase of this product, which is '$min_subscription_quantity'";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
           
       }
       
       
        
                
                
            }else{
                $msg = "The product code you provided is not valid. Please check the code and try again";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
                
            }
       
   }
   
   
   /**
    * This is the function that retrieves the id of a product given its product code
    */
   public function actionretrieveTheIdOfThisProduct(){
       $model = new Product;
       
        if($_REQUEST['product_code'] != ""){
            if($model->isProductCodeValid($_REQUEST['product_code'])){
           $product_id = $model->getTheProductIdOfThisProductGivenItsProductCode($_REQUEST['product_code']);
            //$msg = "You have successfully subscribed to '$product_name' product. Please contact our customer care for any assistance";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "product_id" => $product_id,
                                            )
                                        );
       }else{
           $msg = "The product code you provided is not valid. Please check the code and try again";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
       }
            
        }else{
            $msg = "Please enter a valid product code and try again";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg)
                                        );
            
        }
       
       
       
       
       
   }
   
   
   /**
    * This is the function that list all products in a hamper
    */
   public function actionlistAllProductsInAHamper(){
       
       $model = new HamperHasProducts;
       
       $hamper_id = $_REQUEST['hamper_id'];
       
       $products = $model->getAllProductsInThisHamper($hamper_id);
       
       $all_hamper_products =[];
       
       foreach($products as $product){
           $criteria = new CDbCriteria();
           $criteria->select = '*';
           $criteria->condition='id=:id';
           $criteria->params = array(':id'=>$product);
           $criteria->order = "name";
           $product= Product::model()->find($criteria);
           
           $all_hamper_products[] = $product;
           
       }
        header('Content-Type: application/json');
         echo CJSON::encode(array(
              "success" => mysql_errno() == 0,
              "product" => $all_hamper_products)
            );
       
   }
   
   
   /**
    * This is the function that list all custom hampers for a member
    */
   public function actionlistAllCustomHampersByThisMember(){
       
       $member_id = Yii::app()->user->id;
       
        $criteria = new CDbCriteria();
        $criteria->select = '*';
        $criteria->condition='create_user_id=:memberid and is_custom_product=:custom';
        $criteria->params = array(':memberid'=>$member_id,':custom'=>1);
        $criteria->order = "name";
        $hampers= Product::model()->findAll($criteria);
        
        if($hampers===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "hamper" => $hampers)
                       );
                       
                }
       
   }
   
   
   /**
    * This is the function that list all beneficiaries of a hamper
    */
   public function actionlistAllBeneficiariesOfAHamper(){
       
       $hamper_id = $_REQUEST['hamper_id'];
       
        $criteria = new CDbCriteria();
        $criteria->select = '*';
        $criteria->condition='hamper_id=:id';
        $criteria->params = array(':id'=>$hamper_id);
        $beneficiaries= HamperHasBeneficiary::model()->findAll($criteria);
        
        if($beneficiaries===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "beneficiaries" => $beneficiaries)
                       );
                       
                }
       
       
   }
   
   
   /**
    * This is the product that removes a product from a vendor 
    */
   public function actionremoveThisProductFromThisVendor(){
       
       $model = new Product;
       $vendor_id = Yii::app()->user->id;
       
       $product_id = $_POST['product_id'];
       
        $product_name = $model->getThisProductName($product_id);
            
            if($this->isRemovalOfProductFromTheVendorSuccessful($vendor_id,$product_id)){
                    $msg = "You just remove yourself from trading on the '$product_name' product. You can always re-apply for it";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg,
                                            "vendor_id"=>$vendor_id)
                             );
               }else{
                   $msg = "We could not remove you from trading on '$member_name' product. Its possible such request was never available in the first place";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg,
                                           )
                             );
               }
   }
   
   
   /**
    * This is the function that effects the removel of a product from a vendor
    */
   public function isRemovalOfProductFromTheVendorSuccessful($vendor_id,$product_id){
       $model = new ProductHasVendor;
       return $model->isRemovalOfProductFromTheVendorSuccessful($vendor_id,$product_id);
   }
   
   
    public function actionunsubscribeFromThisProduct(){
            
       $model = new Product;
       $member_id = Yii::app()->user->id;
       
       $product_id = $_POST['product_id'];
       
        $product_name = $model->getThisProductName($product_id);
            
            if($this->isRemovalOfProductSubscriptionFromTheMemberSuccessful($member_id,$product_id)){
                    $msg = "You have successfully unsubscribed from '$product_name' product. You are always welcomed to re-subscribe";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg,
                                            "member_id"=>$member_id)
                             );
               }else{
                   $msg = "Unsubscribing from the '$product_name' product was unsuccessful. Please contact the customer care for assistance";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg,)
                              
                             );
               }
            
            
        }
        
        
        /**
         * This is the function that unsubscribes from a product
         */
        public function isRemovalOfProductSubscriptionFromTheMemberSuccessful($member_id,$product_id){
            $model = new MemberSubscribedToProducts;
            return $model->isRemovalOfProductSubscriptionFromTheMemberSuccessful($member_id,$product_id);
            
        }
        
        
        /**
         * This is the function that schedule the delivery  of a subscribed product for a member
         */
        public function actionScheduleTheDeliveryOfThisProductForAMember(){
            
            $model = new MemberSubscribedToProducts;
            
            $member_id = Yii::app()->user->id;
            
            $product_id= $_POST['product_id'];
            $day_of_delivery = strtolower($_POST['day_of_delivery']);
            $week_of_delivery = strtolower($_POST['week_of_delivery']);
            $delivery_frequency = strtolower($_POST['delivery_frequency']);
            $date_of_first_delivery = $_POST['date_of_first_delivery'];
            $per_delivery_quantity = $_POST['per_delivery_quantity'];
            $subscription_type = strtolower($_POST['subscription_type']);
            $today = getdate(mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
            
            $accepted_date_of_first_delivery = $model->getTheAcceptedDateOfDelivery($date_of_first_delivery);
            
            $product_name = $this->getThisProductName($product_id);
            
           if($model->isTodayGreaterThanTheDeliveryDate($today, getdate(strtotime($date_of_first_delivery)))==false){
                if($model->isProductSubscriptionSchedulingSuccessful($product_id,$member_id,$day_of_delivery,$week_of_delivery,$delivery_frequency,$accepted_date_of_first_delivery,$per_delivery_quantity,$subscription_type)){
                 $msg = "You have successfully scheduled the '$product_name' product subscription.";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg,
                                            )
                             );
                
            }else{
                $msg = "Scheduling of the '$product_name' product subscription was not successful. Please try again or contact customer care for assistance";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg,)
                              
                             );
            }
          }else{
                $msg = "You just selected a date that had already passed for the date of first delivery. Please change it and try again";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg,)
                              
                             );
            }
         
            
     
        }
        
        /**
         * This is the function that drawsdown on a pre-subscribed product
         */
        public function actiondrawdownOnASubscribedProduct(){
            
            $model = new MemberSubscribedToProducts;
            
            $member_id = Yii::app()->user->id;
            
            $product_id= $_POST['product_id'];
            
            $per_delivery_quantity = $_POST['per_delivery_quantity'];
            
            $date_of_next_delivery = $_POST['date_of_next_delivery'];
            
            $remaining_subscription_quantity = $_POST['remaining_subscription_quantity'];
            
            $is_presubscription_drawdown = $_POST['is_presubscription_drawdown'];
            
             $today = getdate(mktime(0, 0, 0, date("m")  , date("d"), date("Y")));
            
            if($remaining_subscription_quantity >=$_POST['per_delivery_quantity']){
                $per_delivery_quantity = $_POST['per_delivery_quantity'];
            }else{
                $per_delivery_quantity = $remaining_subscription_quantity;
            }
             $product_name = $this->getThisProductName($product_id);
            if($per_delivery_quantity != 0){
                $accepted_date_of_next_delivery = $model->getTheAcceptedDateOfDelivery($date_of_next_delivery);
           
            
            if($model->isTodayGreaterThanTheDeliveryDate($today, getdate(strtotime($date_of_next_delivery)))==false){
                if($model->isSubscriptionDrawdownSuccessful($member_id,$product_id,$per_delivery_quantity,$accepted_date_of_next_delivery,$remaining_subscription_quantity)){
                    
                    if($this->isTheAdditionOfThisPrescriptionDrawdownASuccess($member_id,$product_id,$per_delivery_quantity,$is_presubscription_drawdown)){
                        $msg = "You had successfully initiated a drawdown on the subscribed '$product_name' product. You can now visit your cart to consummate the transaction. Note that as a presubscribed product, only the delivery charge will be effected on this transaction";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg,
                                            )
                             );
                        
                    }else{
                        
                         $msg = "You had successfully initiated a drawdown on the subscribed '$product_name' product. However, the drawdown is not automatically registered on your cart. Please try manually sending this drawdown to cart or contact customer service for assistance";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg,
                                            )
                             );
                    }
               
                
            }else{
                $msg = "The attempted drawdown initiation on the subscribed '$product_name' product was not successful. Please contact customer care for assistance.";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg,)
                              
                             );
            }
                
            }else{
                 $msg = "You just selected a date that had already passed for the date of next delivery. Please change it and try again";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg,)
                              
                             );
                
            }
                
            }else{
                $msg = "You had already exhausted the number of the '$product_name' product you presubscribed to.You need to top-up the product.";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg,)
                              
                             );
            }
            
            
        }
        
        
        /**
         * This is the function that adds presubscribed drawdowns tpo cart
         */
        public function isTheAdditionOfThisPrescriptionDrawdownASuccess($member_id,$product_id,$per_delivery_quantity,$is_presubscription_drawdown){
            $model = new OrderHasProducts;
            return $model->isTheAdditionOfThisPrescriptionDrawdownASuccess($member_id,$product_id,$per_delivery_quantity,$is_presubscription_drawdown);
        }
        
        
        /**
         * This is the function that tops up the subscription quantity
         */
        public function actiontoppingUpThisSubscribedProduct(){
            
            $model = new MemberSubscribedToProducts;
            
            $member_id = Yii::app()->user->id;
            
            $product_id= $_POST['product_id'];
            
            $total_subscription_quantity= $_POST['total_subscription_quantity'];
            
           $remaining_subscription_quantity= $_POST['remaining_subscription_quantity'];
             
           $topup_quantity= $_POST['topup_quantity'];
             
             
           $product_name = $this->getThisProductName($product_id);
              
              if($model->isTopupOfTheSubscribedProductSuccessful($member_id,$product_id,$total_subscription_quantity,$remaining_subscription_quantity,$topup_quantity)){
                  $msg = "You had successfully topped up the subscribed '$product_name' product. The top up value is '$topup_quantity'";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg,
                                            "topup"=>$topup_quantity
                                            )
                             );
                  
              }else{
                  
                   $msg = "Could not top up the  subscribed '$product_name' product. Try again or contact customer care for assistance.";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg,)
                              
                             );
              }
             
             
            
        }
        
        
         /**
         * This is the function that that initiates an escrow on a subscribed product
         */
        public function actionescrowThisSubscribedProduct(){
            
            $model = new MemberSubscribedToProducts;
            
            $member_id = Yii::app()->user->id;
            
            $product_id= $_POST['product_id'];
            
            $need_escrow_agreement= $_POST['need_escrow_agreement'];
            
             $product_name = $this->getThisProductName($product_id);
        if($this->isProductEscrowable($product_id)){  
          if($_POST['status'] != strtolower('inactive') and $_POST['subscription_type'] ==strtolower('post')){
             if($model->isThisSubscriptionNotAlreadyInProcess($member_id,$product_id)){
                  
                //$need_escrow_agreement = $_POST['need_escrow_agreement'];
            
             $escrow_file_error = 0;
        
         if($_FILES['escrow_agreement_file']['name'] != ""){
              if($_FILES['escrow_agreement_file']['type'] == 'application/pdf'){
                   $escrow_filename = $_FILES['escrow_agreement_file']['name'];
                   $escrow_filesize = $_FILES['escrow_agreement_file']['size'];
              }else{
                  $escrow_file_error = $escrow_file_error + 1;
                  
              }
              
          if($escrow_file_error == 0){
           
                  //move the escrow agreement file to the escrow directory 
                $escrow_agreement_file = $this->moveTheEscrowAgreementToItsPathAndReturnItsFileName($escrow_filename);
                 if($model->isTheEscrowOfThisSubscriptionSuccessful($member_id,$product_id,$need_escrow_agreement,$escrow_agreement_file)){
                $msg = "You had successfully escrowed the subscribed '$product_name' product.";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "msg" => $msg,
                                            )
                             );
                
                
                
            }else{
                 $msg = "subscription on '$product_name' product could not be escrowed. Try again or contact customer care for assistance.";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "msg" => $msg,)
                              
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
              $msg = "The Escrow Agreement document was not uploaded. Please upload it and try again";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg,
                                         )
                               );
          }
                
              
                  
            }else{
                  if($model->isThisEscrowAlreadyAccepted($member_id,$product_id)){
                       $msg = "This subscription is already escrowed and that status can no longer be altered . You may contact customer care for assistance";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg,
                                         )
                               );
                  }else{
                       $msg = "This subscription is already in progress and therefore cannot be escrowed at this time. You may contact customer care for assistance";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg,
                                         )
                               );
                  }
                 
              }
            
       
                
            }else{
                $msg = "This '$product_name' product cannot be escrowed as the subscription is yet to be activated.";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg,
                                         )
                               );
                
            }
             }else{
                $msg = "The '$product_name' Product cannot be escrowed.";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg,
                                         )
                               );
            }
               
            
           
            
            
            
            
            
            
        }
        
        
        /**
         * This is the function that confirms if a product is escrowable or not
         */
        public function isProductEscrowable($product_id){
            $model = new Product;
            return $model->isProductEscrowable($product_id);
        }
        
        
        /**
         * This is the function that moves an escrow file to its path
         */
        public function moveTheEscrowAgreementToItsPathAndReturnItsFileName($escrow_filename){
            $model = new Escrow;
            return $model->moveTheEscrowAgreementToItsPathAndReturnItsFileName($escrow_filename);
        }
        
        /**
         * This is the product that confirms if a product is escrowable
         */
        public function actionconfirmIfThisProductIsSubscribable(){
            $model = new Product;
            
            $product_code = $_REQUEST['product_code'];
            
            if($_REQUEST['product_code'] != ""){
                if($model->isProductCodeValid($_REQUEST['product_code'])){
                 $product_id = $model->getTheProductIdOfThisProductGivenItsProductCode($_REQUEST['product_code']);
                 
                 if($model->isProductSubscribable($product_id)){
                      //$msg = "You have successfully subscribed to '$product_name' product. Please contact our customer care for any assistance";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "is_subscribable" => true,
                                            )
                                        );
                     
                 }else{
                      //$msg = "You have successfully subscribed to '$product_name' product. Please contact our customer care for any assistance";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() == 0,
                                            "is_subscribable" => false,
                                            )
                                        );
                 }
                   
            }else{
                    $msg = "The product code you provided is not valid. Please check the code and try again";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "is_subscribable" => false)
                                        );
                }
            
                }else{
                    $msg = "Please enter a valid product code and try again";
                                     header('Content-Type: application/json');
                                     echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                            "is_subscribable" => false)
                                        );
            
                    }
                }
                
                
                /**
                 * This is the function that list all products in a category
                 */
                public function actionlistallProductsInThisCategory(){
                    
                    $category_id = $_REQUEST['category_id'];
                    
                     $criteria = new CDbCriteria();
                     $criteria->select = '*';
                     $criteria->condition='category_id=:id';
                     $criteria->params = array(':id'=>$category_id);
                     $products= Product::model()->findAll($criteria);
                     
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
                 * This is the function that creates new hampers
                 */
                public function actioncreatingOwnHamper(){
                    
                    $model = new Product;
                    
                    $member_id = Yii::app()->user->id;
                    $model->name = $_POST['name'];
                    if(isset($_POST['description'])){
                        $model->description = $_POST['description'];
                    }
                    $model->hamper_cost_limit = $_POST['hamper_cost_limit'];
                    $model->service_id = $this->getTheHamperServiceId();
                    $model->category_id = $this->getTheHamperCategoryId();
                    $model->product_type_id = $this->getTheHamperProductTypeId();
                    $model->code = generateAProductCode($model->service_id);
                    $model->is_custom_product = 1;
                    $model->displayable_on_store = 0;
                    $model->whats_product_per_item = 'a pack';
                    $model->create_user_id = $member_id;
                    $model->create_time = new CDbExpression('NOW()');
                    
                    $icon_error_counter = 0;
                    
                    if($_FILES['icon']['name'] != ""){
                    if($this->isIconTypeAndSizeLegal()){
                        
                       $icon_filename = $_FILES['icon']['name'];
                       $icon_size = $_FILES['icon']['size'];
                        
                    }else{
                       
                        $icon_error_counter = $icon_error_counter + 1;
                         
                    }//end of the determine size and type statement
                    }else{
                        //$icon_filename = $this->provideHamperIconWhenUnavailable($model);
                        $icon_filename="";
                        $icon_size = 0;
             
                    }
                    
                    if(($icon_error_counter ==0 )){
                        if($model->validate()){
                            $model->icon = $this->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                            $model->headline_image = $model->icon;
                            $model->product_front_view = $model->icon;
                            $model->product_right_side_view = $model->icon;
                            $model->product_top_view = $model->icon;
                            $model->product_inside_view = $model->icon;
                            $model->product_engine_view = $model->icon;
                            $model->product_back_view = $model->icon;
                            $model->product_left_side_view = $model->icon;
                            $model->product_bottom_view = $model->icon;
                            $model->product_dashboard_view = $model->icon;
                            $model->product_contents_or_booth_view =$model->icon;
                            if($model->save()) {
                        
                                $msg = "'$model->name' hamper created successfully";
                                 header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() == 0,
                                  "msg" => $msg)
                                    );
                         
                        }else{
                            //delete all the moved files in the directory when validation error is encountered
                            $msg = 'Hamper Validaion Error: Check your file fields for correctness';
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                               );
                          }
                 }else{
                        //failed validation here
                            $msg = "Validation Error: '$model->name' hamper  was not created.There was a validation error. Check you form fields and try again";
                                    header('Content-Type: application/json');
                                    echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                             "msg" => $msg,
                                            "service_id"=>$model->service_id,
                                            "category_id"=>$model->category_id,
                                            "product_type_id"=>$model->product_type_id)
                                     );
                }
            }else{
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
            }
                     
                    
                }
                
                
/**
 * This is the function that gets the service id of a hamper
 */                
 public function getTheHamperServiceId(){
     $model = new Service;
     return $model->getTheHamperServiceId();
 }    
 
 
 /**
 * This is the function that gets the category id of a hamper
 */                
 public function getTheHamperCategoryId(){
     $model = new Category;
     return $model->getTheHamperCategoryId();
 }    
 
 
 /**
 * This is the function that gets the product type id of a hamper
 */                
 public function getTheHamperProductTypeId(){
     $model = new ProductType;
     return $model->getTheHamperProductTypeId();
 } 
 
 
 /**
  * This is the function that edits a hamper information
  */
 public function actioneditingOwnHamper(){
     
      $_id = $_POST['id'];
      $model=Product::model()->findByPk($_id);
      
      $member_id = Yii::app()->user->id;
      $model->name = $_POST['name'];
      if(isset($_POST['description'])){
          $model->description = $_POST['description'];
      }
      $model->hamper_cost_limit = $_POST['hamper_cost_limit'];
      $model->service_id = $this->getTheHamperServiceId();
      $model->category_id = $this->getTheHamperCategoryId();
      $model->product_type_id = $this->getTheHamperProductTypeId();
      $model->is_custom_product = 1;
      $model->displayable_on_store = 0;
      $model->whats_product_per_item = 'a pack';
      $model->update_user_id = $member_id;
      $model->update_time = new CDbExpression('NOW()');
                    
      $icon_error_counter = 0;
                    
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
             
                }
                    
                    if(($icon_error_counter ==0 )){
                        if($model->validate()){
                            $model->icon = $this->moveTheIconToItsPathAndReturnTheIconName($model,$icon_filename);
                            $model->headline_image = $model->icon;
                            $model->product_front_view = $model->icon;
                            $model->product_right_side_view = $model->icon;
                            $model->product_top_view = $model->icon;
                            $model->product_inside_view = $model->icon;
                            $model->product_engine_view = $model->icon;
                            $model->product_back_view = $model->icon;
                            $model->product_left_side_view = $model->icon;
                            $model->product_bottom_view = $model->icon;
                            $model->product_dashboard_view = $model->icon;
                            $model->product_contents_or_booth_view =$model->icon;
                            if($model->save()) {
                        
                                $msg = "'$model->name' hamper information updated successfully";
                                 header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() == 0,
                                  "msg" => $msg)
                                    );
                         
                        }else{
                            //delete all the moved files in the directory when validation error is encountered
                            $msg = 'Hamper Validaion Error: Check your file fields for correctness';
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                               );
                          }
                 }else{
                        //failed validation here
                            $msg = "Validation Error: '$model->name' hamper information was not updated.There was a validation error. Check you form fields and try again";
                                    header('Content-Type: application/json');
                                    echo CJSON::encode(array(
                                            "success" => mysql_errno() != 0,
                                             "msg" => $msg,
                                            "service_id"=>$model->service_id,
                                            "category_id"=>$model->category_id,
                                            "product_type_id"=>$model->product_type_id)
                                     );
                }
            }else{
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
            }
     
 }
 
 
 /**
         * This is the function that deletes a hamper
         */
        public function actionRemovingHamper(){
                   
            $_id = $_POST['hamper_id'];
            $model=Product::model()->findByPk($_id);
            
            //remove all the products in this hamper
           
            if($this->isTheRemovalOfAllContentsInThisHamperASuccess($_id)){
                //get the currency name
            $product_name = $this->getThisProductName($_id);
            if($model === null){
                 $msg = 'No Such Record Exist'; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                          
                           
                           
                          
                       ));
                                      
            }else if($model->delete()){
                    $msg = "'$product_name' hamper had been deleted successfully"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                           
                           
                           
                          
                       ));
                       
            } else {
                    $msg = "Validation Error: '$product_name' hamper was not deleted"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            //"selected" => $selected,
                            "msg" => $msg
               
                       ));
                            
                }
                
            }else{
                
                $msg = "could not remove all the contents in tje '$product_name' hamper. Please contact customer service for assistance"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            //"selected" => $selected,
                            "msg" => $msg
               
                       ));
                            
                
            }
            
            
            
        }
        
        
        /**
         * This is the function that confirms if the removal of hamper contents is successful
         */
        public function isTheRemovalOfAllContentsInThisHamperASuccess($hamper_id){
            $model = new HamperHasProducts;
            return $model->isTheRemovalOfAllContentsInThisHamperASuccess($hamper_id);
        }
        
        
        /**
         * This is the function that retrieves all connected members that atre beneficiaries of a hamper
         */
        public function actionconnectedMemberBeneficiariesForAHamper(){
            
            $model = new MemberHasMembers;
            
            $member_id = Yii::app()->user->id;
            
            $hamper_id = $_REQUEST['hamper_id'];
                       
            $all_beneficiaries = [];
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='hamper_id=:hamperid';
            $criteria->params = array(':hamperid'=>$hamper_id);
            $beneficiaries= HamperHasBeneficiary::model()->findAll($criteria);
            
            foreach($beneficiaries as $beneficiary){
                if($model->isThisBeneficiaryConnectedToThisMember($member_id,$beneficiary['beneficiary_id'])){
                    $all_beneficiaries[] = $beneficiary;
                }
                
            }
            header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "beneficiary" => $all_beneficiaries
                           
                           
                           
                          
                       ));
            
        }
        
        
        
        /**
         * This is the function that retrieves all non connected members that are beneficiaries of a hamper
         */
        public function actionNonConnectedMemberBeneficiariesForAHamper(){
            $model = new MemberHasMembers;
            
            $member_id = Yii::app()->user->id;
            
            $hamper_id = $_REQUEST['hamper_id'];
                       
            $all_beneficiaries = [];
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='hamper_id=:hamperid';
            $criteria->params = array(':hamperid'=>$hamper_id);
            $beneficiaries= HamperHasBeneficiary::model()->findAll($criteria);
            
            foreach($beneficiaries as $beneficiary){
                if($model->isThisBeneficiaryConnectedToThisMember($member_id,$beneficiary['beneficiary_id'])==false){
                    $all_beneficiaries[] = $beneficiary;
                }
                
            }
            header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "beneficiary" => $all_beneficiaries
                           
                           
                           
                          
                       ));
            
        }
 
 /**
  * This is the function that adds a connected member to a hamper beneficiary list
  */
   public function actionaddingAConnectedMemberAsHamperBeneficiary(){
       
       $model = new HamperHasBeneficiary;
       $member_id = Yii::app()->user->id;
       $hamper_id = $_POST['hamper_id'];
       $hamper_label = $_POST['hamper_label'];
       $beneficiary_id = $_POST['connected_member_beneficiary'];
       $delivery_type = $_POST['delivery_type'];
       $number_of_hampers_delivered = $_POST['number_of_hampers_delivered'];
       $delivery_address_option= $_POST['delivery_address_option'];
       $hamper_container_id= $_POST['hamper_container_id'];
        if(isset($_POST['delivery_is_redirectable'])){
           $delivery_is_redirectable= $_POST['delivery_is_redirectable'];
       }else{
          $delivery_is_redirectable = 0; 
       }
       if($delivery_address_option == strtolower('others')){
           $city_id = $_POST['city'];
           $state_id = $_POST['state'];
           $country_id = $_POST['country'];
           $place_of_delivery = $_POST['address'];
       }else{
           $city_id = $this->getThisMemberPrimaryCityId($beneficiary_id);
           $state_id = $this->getThisMemberPrimaryStateId($beneficiary_id);
           $country_id = $this->getThisMemberPrimaryCountryId($beneficiary_id);
           $place_of_delivery = $this->getThisMemberPrimaryAddess($beneficiary_id);
       }
      
       $member_name = $this->getTheNameOfThisMember($beneficiary_id);
       if($model->isThisMemberAlreadyABeneficiaryOfTheHamper($beneficiary_id,$hamper_id)== false){
           if($model->isTheAdditionOfThisMemberAsHamperBeneficiaryASuccess($hamper_id,$beneficiary_id,$number_of_hampers_delivered,$city_id,$state_id,$country_id,$place_of_delivery,$delivery_address_option,$delivery_is_redirectable,$delivery_type,$hamper_container_id)){
                $msg = " $member_name had been added as a beneficiary of this $hamper_label hamper"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg" => $msg
               
                       ));
           }else{
               $msg = " $member_name could not be added as a beneficiary of this $hamper_label hamper. Please contact customer care for assistance"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno()!= 0,
                            "msg" => $msg
               
                       ));
           }
           
       }else{
           $msg = " $member_name is already a beneficiary of this $hamper_label hamper."; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg
               
                       ));
           
       }
       
     
   }  
   
   
   /**
  * This is the function that updates the information of  a connected member in a hamper beneficiary list
  */
   public function actionupdateAConnectedMemberAsHamperBeneficiary(){
       
       $model = new HamperHasBeneficiary;
       $member_id = Yii::app()->user->id;
       $hamper_id = $_POST['hamper_id'];
       $hamper_label = $_POST['hamper_label'];
       $beneficiary_id = $_POST['connected_member_beneficiary'];
       $delivery_type = $_POST['delivery_type'];
       $number_of_hampers_delivered = $_POST['number_of_hampers_delivered'];
       $delivery_address_option= $_POST['delivery_address_option'];
       $hamper_container_id= $_POST['hamper_container_id'];
       if(isset($_POST['delivery_is_redirectable'])){
           $delivery_is_redirectable= $_POST['delivery_is_redirectable'];
       }else{
          $delivery_is_redirectable = 0; 
       }
       if($delivery_address_option == strtolower('others')){
           if(is_numeric($_POST['city'])){
               $city_id = $_POST['city'];
           }else{
               $city_id = $_POST['city_id'];
           }
           if(is_numeric($_POST['state'])){
               $state_id = $_POST['state'];
           }else{
               $state_id = $_POST['state_id'];
           }
           if(is_numeric($_POST['country'])){
               $country_id = $_POST['country'];
           }else{
               $country_id = $_POST['country_id'];
           }
           $place_of_delivery = $_POST['address'];
       }else{
           $city_id = $this->getThisMemberPrimaryCityId($beneficiary_id);
           $state_id = $this->getThisMemberPrimaryStateId($beneficiary_id);
           $country_id = $this->getThisMemberPrimaryCountryId($beneficiary_id);
           $place_of_delivery = $this->getThisMemberPrimaryAddess($beneficiary_id);
       }
      
       $member_name = $this->getTheNameOfThisMember($beneficiary_id);
       if($model->isThisMemberAlreadyABeneficiaryOfTheHamper($beneficiary_id,$hamper_id)){
           
            if($model->isTheUpdateOfThisMemberAsHamperBeneficiaryASuccess($hamper_id,$beneficiary_id,$number_of_hampers_delivered,$city_id,$state_id,$country_id,$place_of_delivery,$delivery_address_option,$delivery_is_redirectable,$delivery_type,$hamper_container_id)){
                $msg = " $member_name information had been updated successfully as a beneficiary of the $hamper_label hamper"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg" => $msg
               
                       ));
           }else{
               $msg = " $member_name information could not be updated as a beneficiary of this $hamper_label hamper. Please contact customer care for assistance"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg
               
                       ));
           }
           
       }else{
           
           $msg = " $member_name is not already a beneficiary of this $hamper_label hamper and his/her information cannot be updated."; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg
               
                       ));
       }
      
     
   }  
   
   
   
   /**
  * This is the function that adds a connected member to a hamper beneficiary list
  */
   public function actionaddANonConnectedMemberAsHamperBeneficiary(){
       
       $model = new HamperHasBeneficiary;
       $member_id = Yii::app()->user->id;
       $hamper_id = $_POST['hamper_id'];
       $hamper_label = $_POST['hamper_label'];
       $delivery_type = $_POST['delivery_type'];
       $beneficiary_id = $this->getTheMemberIdGivenItsMembershipNumber($_POST['non_connected_beneficiary']);
       $number_of_hampers_delivered = $_POST['number_of_hampers_delivered'];
       $hamper_container_id= $_POST['hamper_container_id'];
       if(isset($_POST['delivery_is_redirectable'])){
           $delivery_is_redirectable= $_POST['delivery_is_redirectable'];
       }else{
          $delivery_is_redirectable = 0; 
       }
       $delivery_address_option= $_POST['delivery_address_option'];
       if($delivery_address_option == strtolower('others')){
           $city_id = $_POST['city'];
           $state_id = $_POST['state'];
           $country_id = $_POST['country'];
           $place_of_delivery = $_POST['address'];
       }else{
           $city_id = $this->getThisMemberPrimaryCityId($beneficiary_id);
           $state_id = $this->getThisMemberPrimaryStateId($beneficiary_id);
           $country_id = $this->getThisMemberPrimaryCountryId($beneficiary_id);
           $place_of_delivery = $this->getThisMemberPrimaryAddess($beneficiary_id);
       }
      
       $member_name = $this->getTheNameOfThisMember($beneficiary_id);
       if($model->isThisMemberAlreadyABeneficiaryOfTheHamper($beneficiary_id,$hamper_id) == false){
           if($model->isTheAdditionOfThisMemberAsHamperBeneficiaryASuccess($hamper_id,$beneficiary_id,$number_of_hampers_delivered,$city_id,$state_id,$country_id,$place_of_delivery,$delivery_address_option,$delivery_is_redirectable,$delivery_type,$hamper_container_id)){
                $msg = " $member_name had been added as a beneficiary of this $hamper_label hamper"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg" => $msg
               
                       ));
           }else{
               $msg = " $member_name could not be added as a beneficiary of this $hamper_label hamper. Please contact customer care for assistance"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg" => $msg
               
                       ));
           }
           
           
       }else{
           $msg = " $member_name is already a beneficiary of this $hamper_label hamper."; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg
               
                       ));
           
       }
       
     
   }  
   
   
   
   /**
  * This is the function that updates the information of  a non connected member in a hamper beneficiary list
  */
   public function actionupdateANonConnectedMemberAsHamperBeneficiary(){
       
       $model = new HamperHasBeneficiary;
       $member_id = Yii::app()->user->id;
       $hamper_id = $_POST['hamper_id'];
       $hamper_label = $_POST['hamper_label'];
       $beneficiary_id = $_POST['beneficiary_id'];
       $delivery_type = $_POST['delivery_type'];
       $number_of_hampers_delivered = $_POST['number_of_hampers_delivered'];
       $hamper_container_id= $_POST['hamper_container_id'];
       if(isset($_POST['delivery_is_redirectable'])){
           $delivery_is_redirectable= $_POST['delivery_is_redirectable'];
       }else{
          $delivery_is_redirectable = 0; 
       }
       $delivery_address_option= $_POST['delivery_address_option'];
       if($delivery_address_option == strtolower('others')){
           if(is_numeric($_POST['city'])){
               $city_id = $_POST['city'];
           }else{
               $city_id = $_POST['city_id'];
           }
           if(is_numeric($_POST['state'])){
               $state_id = $_POST['state'];
           }else{
               $state_id = $_POST['state_id'];
           }
           if(is_numeric($_POST['country'])){
               $country_id = $_POST['country'];
           }else{
               $country_id = $_POST['country_id'];
           }
           $place_of_delivery = $_POST['address'];
       }else{
           $city_id = $this->getThisMemberPrimaryCityId($beneficiary_id);
           $state_id = $this->getThisMemberPrimaryStateId($beneficiary_id);
           $country_id = $this->getThisMemberPrimaryCountryId($beneficiary_id);
           $place_of_delivery = $this->getThisMemberPrimaryAddess($beneficiary_id);
       }
      
       $member_name = $this->getTheNameOfThisMember($beneficiary_id);
       if($model->isThisMemberAlreadyABeneficiaryOfTheHamper($beneficiary_id,$hamper_id)){
           if($model->isTheUpdateOfThisMemberAsHamperBeneficiaryASuccess($hamper_id,$beneficiary_id,$number_of_hampers_delivered,$city_id,$state_id,$country_id,$place_of_delivery,$delivery_address_option,$delivery_is_redirectable,$delivery_type,$hamper_container_id)){
                $msg = " $member_name information had been updated successfully as a beneficiary of the $hamper_label hamper"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg" => $msg
               
                       ));
           }else{
               $msg = " $member_name information could not be updated as a beneficiary of this $hamper_label hamper. Please contact customer care for assistance"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg" => $msg
               
                       ));
           }
           
       }else{
            $msg = " $member_name is not already a beneficiary of this $hamper_label hamper and his/her information cannot be updated."; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg
               
                       ));
           
       }
       
     
   }  
   
   
   /**
    * This is the function that gets a member id from the membership number
    */
   public function getTheMemberIdGivenItsMembershipNumber($membership_number){
       $model = new Members;
       return $model->getTheMemberIdGivenItsMembershipNumber($membership_number);
       
   }
   
    /**
    * This is the function that gets the primary address of a hamper beneficiary
    */
   public function getThisMemberPrimaryAddess($beneficiary_id){
       $model = new Members;
       return $model->getThisMemberPrimaryAddess($beneficiary_id);
   }
   
  
   
   /**
    * This is the function that gets the primary country id of a hamper beneficiary
    */
   public function getThisMemberPrimaryCountryId($beneficiary_id){
       $model = new Members;
       return $model->getThisMemberPrimaryCountryId($beneficiary_id);
   }
   
   /**
    * This is the function that gets the primary state 
    *  id of a hamper beneficiary
    */
   public function getThisMemberPrimaryStateId($beneficiary_id){
       $model = new Members;
       return $model->getThisMemberPrimaryStateId($beneficiary_id);
   }
   
   /**
    * This is the function that gets the primary city id of a hamper beneficiary
    */
   public function getThisMemberPrimaryCityId($beneficiary_id){
       $model = new Members;
       return $model->getThisMemberPrimaryCityId($beneficiary_id);
   }
        
      /**
       * this is th function that gets the name of a member
       */          
   public function getTheNameOfThisMember($member_id){
       $model= new Members;
       return $model->getTheNameOfThisMember($member_id);
   }
   
   
   /**
    * this is the function that removes a hamper beneficiary
    */
   public function actionremoveThisHamperBeneficiary(){
       $model = new HamperHasBeneficiary;
       $hamper_id = $_REQUEST['hamper_id'];
       $beneficiary_id = $_REQUEST['beneficiary_id'];
       $hamper_label = $_REQUEST['hamper_label'];
       
       $beneficiary_name = $this->getTheNameOfThisMember($beneficiary_id);
       
       if($model->isThisMemberAlreadyABeneficiaryOfTheHamper($beneficiary_id,$hamper_id)){
           if($model->isTheRemovalOfBeneficiaryFromHamperListASuccess($hamper_id,$beneficiary_id)){
               $msg = " $beneficiary_name had successfully been removed from  the $hamper_label hamper beneficiary list"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg" => $msg
               
                       ));
               
           }else{
               //could not remove hamper beneficiary
                $msg = " $beneficiary_name could not be removed from  the $hamper_label hamper beneficiary list. Please contact customer cate for assistance"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg
               
                       ));
           }
           
       }else{
           //record does not exist
            $msg = " $beneficiary_name is not currently a beneficiary to the $hamper_label hamper beneficiary list."; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg
               
                       ));
       }
   }
        
   
   /**
    * This is the function that list all products in a hamper
    */
   public function actionlistProductsInAHamper(){
       $model = new HamperHasProducts;
       $hamper_id = $_REQUEST['hamper_id'];
        //$hamper_id = 24;     
       $all_products = [];
       $criteria = new CDbCriteria();
       $criteria->select = '*';
      // $criteria->condition='id=:id';
      // $criteria->params = array(':id'=>$this_product);
       $criteria->order = "name";
       $products= Product::model()->findAll($criteria);
       
       foreach($products as $product){
           if($model->isThisProductInThisHamper($hamper_id,$product['id'])){
               $all_products[] = $product;
           }
       }
        header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "product" => $all_products
               
                       ));
       
   }
         
   
   /**
    * This is the function that adds a product to a hamper
    */
   public function actionaddingAProductToHamper(){
       $model = new HamperHasProducts();
       
       $product_id = $_REQUEST['product_id'];
       $hamper_id = $_REQUEST['hamper_id'];
       $quantity = $_REQUEST['quantity'];
       
       //get the price limit of this hamper
       $hamper_price_limit = $this->getThePriceLimitOfThisHamper($hamper_id);
       //get the current price of this product
       $product_current_price =$this->getTheCurrentPrevailingRetailPriceOfThisPack($product_id);
       $product_name = $this->getThisProductName($product_id);
       if($model->isThisProductInThisHamper($hamper_id,$product_id) == false){
           if($model->isTheAdditionOfThisProductToHamperASuccess($hamper_id,$product_id,$quantity,$hamper_price_limit,$product_current_price)){
                $total_hamper_cost = $this->getTheTotalCostOfAnHamper($hamper_id);
                $msg = " The addition of $product_name product to this hamper is successful."; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg" => $msg,
                           "price"=>$total_hamper_cost
               
                       ));
               
           }else{
                $msg = " The request to add $product_name product to this hamper was not successful.It is very likely you are exceeding  the set limit of this hamper which is =N=$hamper_price_limit. Extend the hamper limit and try again"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg
               
                       ));
           }
           
           
       }else{
           $msg = " $product_name product is already in this Hamper.If you are attempting to increase the quantity of the product, first remove it from the hamper and add it again with the new quantity"; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg
               
                       ));
       }
       
   }
   
   /**
    * This is the function that gets the price limit of an hamper
    */
   public function getThePriceLimitOfThisHamper($hamper_id){
       $model = new Product;
       return $model->getThePriceLimitOfThisHamper($hamper_id);
   }
  
   
   /**
    * This is the function that removes a product from a hamper
    */
   public function actionremovingAProductFromHamper(){
       
       $model = new HamperHasProducts();
       $product_id = $_REQUEST['product_id'];
       
       $hamper_id = $_REQUEST['hamper_id'];
       
        $product_name = $this->getThisProductName($product_id);
        
        
       
       if($model->isThisProductInThisHamper($hamper_id,$product_id)){
           if($model->isTheRemovalOfThisProductFromThisHamperSuccessful($hamper_id,$product_id)){
               $total_hamper_cost = $this->getTheTotalCostOfAnHamper($hamper_id);
               $msg = " $product_name product had successfully been removed from this hamper."; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg" => $msg,
                            "price"=>$total_hamper_cost
               
                       ));
               
           }else{
               $msg = " $product_name product could not be removed from this hamper. Is possible it is not currently in the hamper. Please contact customer care for further assistance."; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg
               
                       ));
           }
           
       }else{
            $msg = " $product_name product is not currently in this Hamper."; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg
               
                       ));
       }
   }
   
   
   /**
    * This is the function that gets an extra information about a hamper
    */
   public function actiongetExtraInformationAboutAHamper(){
      $model = new Product;
       $hamper_id = $_REQUEST['hamper_id'];
       $total_hamper_cost = $this->getTheTotalCostOfAnHamper($hamper_id);
       $hamper_container_id = $model->getTheHamperContainerIdOfThisHamper($hamper_id);
       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "total_hamper_cost" => $total_hamper_cost,
                           "hamper_container_id"=>$hamper_container_id
               
                       ));
       
   }
   
   /**
    * This is the function that gets the new total cost of a hamper
    */
   public function actiongetTheNewTotalCostForTheHamper(){
       
       $model = new Product;
       $hamper_id = $_REQUEST['hamper_id'];
       $hamper_container_id = $_REQUEST['container_id'];
       
       //update the hamper container of this hamper
       if($model->isThisHamperContainerUpdatedSuccessfully($hamper_id,$hamper_container_id)){
           $total_hamper_cost = $this->getTheTotalCostOfAnHamper($hamper_id);
            header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "total_hamper_cost" => $total_hamper_cost
                           
               
                       ));
           
       }else{
           $total_hamper_cost = $this->getTheTotalCostOfAnHamper($hamper_id);
            header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "total_hamper_cost" => $total_hamper_cost
                           
               
                       ));
       }
       
       
       
   }
        
   
   /**
    * This is the function that gets the total price of items in a hamper
    */
   public function getTheTotalPriceOfItemsInAHamper($hamper_id){
       $model = new HamperHasProducts;
       return $model->getTheTotalPriceOfItemsInAHamper($hamper_id);
   }
   
   /**
    * get the cost of hamper content container and its processing charges
    */
   public function getTheTotalCostOfAnHamper($hamper_id){
       $model = new HamperContainer;
       return $model->getTheTotalCostOfAnHamper($hamper_id,$this->getTheTotalPriceOfItemsInAHamper($hamper_id));
   }
   
   
       /**
         * This is the function that retrieves all product that could be included in a hamper
         */
        public function actionListAllProductsForHampers(){
            $model = new Product;
              $userid = Yii::app()->user->id;
             
            if(isset($_REQUEST['service']) ==""){
                 if(isset($_REQUEST['category']) ==""){
                     if(isset($_REQUEST['type']) ==""){
                         if(isset($_REQUEST['searchstring']) ==""){
                          $products = Product::model()->findAll(array('order'=>'name'));
                           $all_products = [];
                           foreach($products as $product){
                               if($model->canThisProductBeAddedToHamper($product['id'])){
                                    if($product['is_custom_product']==0){
                                       if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                               }
                             
                          }
                            header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                            ));
                     }
                     }
                     
                    }elseif($_REQUEST['category'] == "All Categories" || $_REQUEST['category'] ==0){
                       if($_REQUEST['type'] =="All Types" || $_REQUEST['type'] ==0){
                         if($_REQUEST['searchstring'] ==""){
                            
                          $products = Product::model()->findAll(array('order'=>'name'));
                          $all_products = [];
                          foreach($products as $product){
                              if($model->canThisProductBeAddedToHamper($product['id'])){
                                    if($product['is_custom_product']==0){
                                       if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                               }
                          }
                           header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                            ));
                        }else{
                            $searchstring = explode('+',$_REQUEST['searchstring']);
                            $searchWords = preg_replace('/\s/', '', $searchstring);
                            $searchable_items = [];
                            foreach($searchWords as $word){
                                 $q = "SELECT id FROM product where name REGEXP '$word'" ;
                                 $cmd = Yii::app()->db->createCommand($q);
                                 $results = $cmd->query();
                                 foreach($results as $result){
                                     $criteria = new CDbCriteria();
                                     $criteria->select = '*';
                                     $criteria->condition='id=:id';   
                                     $criteria->params = array(':id'=>$result['id']);
                                     $criteria->order = "name";
                                     $product = Product::model()->find($criteria);
                                    if($model->canThisProductBeAddedToHamper($product['id'])){
                                         if($product['is_custom_product']==0){
                                         if($product['displayable_on_store'] == 1){
                                             $searchable_items[] = $product;
                                         }
                                      }
                                     }
                                     
                                 }
                            }
                              header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $searchable_items
                                        
                                
                                ));
                           
                        }
                        }else if(is_numeric($_REQUEST['type']) && $_REQUEST['type']!= 0 ){
                        if($_REQUEST['searchstring'] ==""){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='product_type_id=:typeid';   
                            $criteria->params = array(':typeid'=>$_REQUEST['type']);
                            $criteria->order = "name";
                            $products = Product::model()->findAll($criteria);
                            
                          $all_products = [];
                          foreach($products as $product){
                              if($model->canThisProductBeAddedToHamper($product['id'])){
                                    if($product['is_custom_product']==0){
                                       if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                               }
                          }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                                ));
                        }else{
                          $searchstring = explode('+',$_REQUEST['searchstring']);
                            $searchWords = preg_replace('/\s/', '', $searchstring);
                            $searchable_items = [];
                            foreach($searchWords as $word){
                                 $q = "SELECT id FROM product where name REGEXP '$word'" ;
                                 $cmd = Yii::app()->db->createCommand($q);
                                 $results = $cmd->query();
                                 foreach($results as $result){
                                     $criteria = new CDbCriteria();
                                     $criteria->select = '*';
                                     $criteria->condition='id=:id and product_type_id=:typeid';   
                                     $criteria->params = array(':id'=>$result['id'],':typeid'=>$_REQUEST['type']);
                                     $criteria->order = "name";
                                     $product = Product::model()->find($criteria);
                                    if($model->canThisProductBeAddedToHamper($product['id'])){
                                       if($product['is_custom_product']==0){
                                         if($product['displayable_on_store'] == 1){
                                             $searchable_items[] = $product;
                                         }
                                      }
                                     }
                                 }
                            }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $searchable_items
                                        
                                
                                ));
                        }
                        
                    }
                    }elseif(is_numeric($_REQUEST['category']) && $_REQUEST['category'] !=0){
                    if($_REQUEST['type'] =="All Types" || $_REQUEST['type'] ==0){
                        if($_REQUEST['searchstring'] ==""){
                            
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='category_id=:categoryid';   
                            $criteria->params = array(':categoryid'=>$_REQUEST['category']);
                            $criteria->order = "name";
                            $products = Product::model()->findAll($criteria);
                            
                           $all_products = [];
                          foreach($products as $product){
                              if($model->canThisProductBeAddedToHamper($product['id'])){
                                    if($product['is_custom_product']==0){
                                       if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                               }
                          }
                            
                              header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                            ));
                        }else{
                            
                            $searchstring = explode('+',$_REQUEST['searchstring']);
                            $searchWords = preg_replace('/\s/', '', $searchstring);
                            $searchable_items = [];
                            foreach($searchWords as $word){
                                 $q = "SELECT id FROM product where name REGEXP '$word'" ;
                                 $cmd = Yii::app()->db->createCommand($q);
                                 $results = $cmd->query();
                                 foreach($results as $result){
                                     $criteria = new CDbCriteria();
                                     $criteria->select = '*';
                                     $criteria->condition='id=:id and category_id=:categoryid';   
                                     $criteria->params = array(':id'=>$result['id'],':categoryid'=>$_REQUEST['category']);
                                     $criteria->order = "name";
                                     $product = Product::model()->find($criteria);
                                     if($model->canThisProductBeAddedToHamper($product['id'])){
                                       if($product['is_custom_product']==0){
                                         if($product['displayable_on_store'] == 1){
                                             $searchable_items[] = $product;
                                         }
                                      }
                                     }
                                 }
                            }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $searchable_items
                                        
                                
                                ));
                        }
                        
                    }else if(is_numeric($_REQUEST['type']) && $_REQUEST['type']!= 0 ){
                        if($_REQUEST['searchstring'] ==""){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='category_id=:categoryid and product_type_id=:typeid';   
                            $criteria->params = array(':categoryid'=>$_REQUEST['category'],':typeid'=>$_REQUEST['type']);
                            $criteria->order = "name";
                            $products = Product::model()->findAll($criteria);
                            
                             $all_products = [];
                            foreach($products as $product){
                              if($model->canThisProductBeAddedToHamper($product['id'])){
                                    if($product['is_custom_product']==0){
                                       if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                               }
                             }
                            
                            header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                                ));
                        }else{
                            $searchstring = explode('+',$_REQUEST['searchstring']);
                            $searchWords = preg_replace('/\s/', '', $searchstring);
                            $searchable_items = [];
                            foreach($searchWords as $word){
                                 $q = "SELECT id FROM product where name REGEXP '$word'" ;
                                 $cmd = Yii::app()->db->createCommand($q);
                                 $results = $cmd->query();
                                 foreach($results as $result){
                                     $criteria = new CDbCriteria();
                                     $criteria->select = '*';
                                     $criteria->condition='id=:id and (product_type_id=:typeid and category_id=:categoryid)';   
                                     $criteria->params = array(':id'=>$result['id'],':typeid'=>$_REQUEST['type'],':categoryid'=>$_REQUEST['category']);
                                     $criteria->order = "name";
                                     $product = Product::model()->find($criteria);
                                     if($model->canThisProductBeAddedToHamper($product['id'])){
                                       if($product['is_custom_product']==0){
                                         if($product['displayable_on_store'] == 1){
                                             $searchable_items[] = $product;
                                         }
                                      }
                                     }
                                 }
                            }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $searchable_items
                                        
                                
                                ));
                        }
                        
                    }
                    
                }
                }elseif($_REQUEST['service'] == "All Services" || $_REQUEST['service'] ==0){
                    if($_REQUEST['category'] =="All Categories" || $_REQUEST['category'] ==0){
                         if($_REQUEST['type'] =="All Types" || $_REQUEST['type'] ==0){
                          if($_REQUEST['searchstring'] ==""){
                            
                            $products = Product::model()->findAll(array('order'=>'name'));
                            $all_products = [];
                          foreach($products as $product){
                              if($model->canThisProductBeAddedToHamper($product['id'])){
                                    if($product['is_custom_product']==0){
                                       if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                               }
                          }
                           header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                            ));
                        }else{
                            $searchstring = explode('+',$_REQUEST['searchstring']);
                            $searchWords = preg_replace('/\s/', '', $searchstring);
                            $searchable_items = [];
                            foreach($searchWords as $word){
                                 $q = "SELECT id FROM product where name REGEXP '$word'" ;
                                 $cmd = Yii::app()->db->createCommand($q);
                                 $results = $cmd->query();
                                 foreach($results as $result){
                                     $criteria = new CDbCriteria();
                                     $criteria->select = '*';
                                     $criteria->condition='id=:id';   
                                     $criteria->params = array(':id'=>$result['id']);
                                     $criteria->order = "name";
                                     $product = Product::model()->find($criteria);
                                     if($model->canThisProductBeAddedToHamper($product['id'])){
                                       if($product['is_custom_product']==0){
                                         if($product['displayable_on_store'] == 1){
                                             $searchable_items[] = $product;
                                         }
                                      }
                                     }
                                 }
                            }
                              header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $searchable_items
                                        
                                
                                ));
                           
                        }   
                             
                    }else if(is_numeric($_REQUEST['type']) && $_REQUEST['type']!= 0 ){
                        if($_REQUEST['searchstring'] ==""){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='product_type_id=:typeid';   
                            $criteria->params = array(':typeid'=>$_REQUEST['type']);
                            $criteria->order = "name";
                            $products = Product::model()->findAll($criteria);
                            
                          $all_products = [];
                          foreach($products as $product){
                              if($model->canThisProductBeAddedToHamper($product['id'])){
                                    if($product['is_custom_product']==0){
                                       if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                               }
                          }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                                ));
                        }else{
                          $searchstring = explode('+',$_REQUEST['searchstring']);
                            $searchWords = preg_replace('/\s/', '', $searchstring);
                            $searchable_items = [];
                            foreach($searchWords as $word){
                                 $q = "SELECT id FROM product where name REGEXP '$word'" ;
                                 $cmd = Yii::app()->db->createCommand($q);
                                 $results = $cmd->query();
                                 foreach($results as $result){
                                     $criteria = new CDbCriteria();
                                     $criteria->select = '*';
                                     $criteria->condition='id=:id and product_type_id=:typeid';   
                                     $criteria->params = array(':id'=>$result['id'],':typeid'=>$_REQUEST['type']);
                                     $criteria->order = "name";
                                     $product = Product::model()->find($criteria);
                                    if($model->canThisProductBeAddedToHamper($product['id'])){
                                       if($product['is_custom_product']==0){
                                         if($product['displayable_on_store'] == 1){
                                             $searchable_items[] = $product;
                                         }
                                      }
                                     }
                                 }
                            }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $searchable_items
                                        
                                
                                ));
                        }
                        
                    }
                        
                        
                    }else if(is_numeric($_REQUEST['category']) && $_REQUEST['category']!= 0 ){
                        if($_REQUEST['type'] =="All Types" || $_REQUEST['type'] ==0){
                            if($_REQUEST['searchstring'] ==""){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='category_id=:categoryid';   
                            $criteria->params = array(':categoryid'=>$_REQUEST['category']);
                            $criteria->order = "name";
                            $products = Product::model()->findAll($criteria);
                            
                          $all_products = [];
                          foreach($products as $product){
                              if($model->canThisProductBeAddedToHamper($product['id'])){
                                    if($product['is_custom_product']==0){
                                       if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                               }
                          }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                                ));
                        }else{
                          $searchstring = explode('+',$_REQUEST['searchstring']);
                            $searchWords = preg_replace('/\s/', '', $searchstring);
                            $searchable_items = [];
                            foreach($searchWords as $word){
                                 $q = "SELECT id FROM product where name REGEXP '$word'" ;
                                 $cmd = Yii::app()->db->createCommand($q);
                                 $results = $cmd->query();
                                 foreach($results as $result){
                                     $criteria = new CDbCriteria();
                                     $criteria->select = '*';
                                     $criteria->condition='id=:id and category_id=:categoryid';   
                                     $criteria->params = array(':id'=>$result['id'],':categoryid'=>$_REQUEST['category']);
                                     $criteria->order = "name";
                                     $product = Product::model()->find($criteria);
                                     if($model->canThisProductBeAddedToHamper($product['id'])){
                                       if($product['is_custom_product']==0){
                                         if($product['displayable_on_store'] == 1){
                                             $searchable_items[] = $product;
                                         }
                                      }
                                     }
                                 }
                            }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $searchable_items
                                        
                                
                                ));
                        }
                            
                        }else if(is_numeric($_REQUEST['type']) && $_REQUEST['type']!= 0 ){
                        if($_REQUEST['searchstring'] ==""){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='category_id=:categoryid and product_type_id=:typeid';   
                            $criteria->params = array(':categoryid'=>$_REQUEST['category'],':typeid'=>$_REQUEST['type']);
                            $criteria->order = "name";
                            $products = Product::model()->findAll($criteria);
                            
                             $all_products = [];
                            foreach($products as $product){
                              if($model->canThisProductBeAddedToHamper($product['id'])){
                                    if($product['is_custom_product']==0){
                                       if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                               }
                             }
                            
                            header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                                ));
                        }else{
                            $searchstring = explode('+',$_REQUEST['searchstring']);
                            $searchWords = preg_replace('/\s/', '', $searchstring);
                            $searchable_items = [];
                            foreach($searchWords as $word){
                                 $q = "SELECT id FROM product where name REGEXP '$word'" ;
                                 $cmd = Yii::app()->db->createCommand($q);
                                 $results = $cmd->query();
                                 foreach($results as $result){
                                     $criteria = new CDbCriteria();
                                     $criteria->select = '*';
                                     $criteria->condition='id=:id and (product_type_id=:typeid and category_id=:categoryid)';   
                                     $criteria->params = array(':id'=>$result['id'],':typeid'=>$_REQUEST['type'],':categoryid'=>$_REQUEST['category']);
                                     $criteria->order = "name";
                                     $product = Product::model()->find($criteria);
                                     if($model->canThisProductBeAddedToHamper($product['id'])){
                                       if($product['is_custom_product']==0){
                                         if($product['displayable_on_store'] == 1){
                                             $searchable_items[] = $product;
                                         }
                                      }
                                     }
                                 }
                            }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $searchable_items
                                        
                                
                                ));
                        }
                        
                    }
                        
                        
                    }
                }elseif(is_numeric($_REQUEST['service']) && $_REQUEST['service'] !=0){
                    if($_REQUEST['category'] =="All Categories" || $_REQUEST['category'] ==0){
                        if($_REQUEST['type'] =="All Types" || $_REQUEST['type'] ==0){
                          if($_REQUEST['searchstring'] ==""){
                            
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='service_id=:serviceid';   
                            $criteria->params = array(':serviceid'=>$_REQUEST['service']);
                            $criteria->order = "name";
                            $products = Product::model()->findAll($criteria);
                            
                           $all_products = [];
                          foreach($products as $product){
                              if($model->canThisProductBeAddedToHamper($product['id'])){
                                    if($product['is_custom_product']==0){
                                       if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                               }
                          }
                            
                              header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                            ));
                        }else{
                            
                            $searchstring = explode('+',$_REQUEST['searchstring']);
                            $searchWords = preg_replace('/\s/', '', $searchstring);
                            $searchable_items = [];
                            foreach($searchWords as $word){
                                 $q = "SELECT id FROM product where name REGEXP '$word'" ;
                                 $cmd = Yii::app()->db->createCommand($q);
                                 $results = $cmd->query();
                                 foreach($results as $result){
                                     $criteria = new CDbCriteria();
                                     $criteria->select = '*';
                                     $criteria->condition='id=:id and service_id=:serviceid';   
                                     $criteria->params = array(':id'=>$result['id'],':serviceid'=>$_REQUEST['service']);
                                     $criteria->order = "name";
                                     $product = Product::model()->find($criteria);
                                     if($model->canThisProductBeAddedToHamper($product['id'])){
                                       if($product['is_custom_product']==0){
                                         if($product['displayable_on_store'] == 1){
                                             $searchable_items[] = $product;
                                         }
                                      }
                                     }
                                 }
                            }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $searchable_items
                                        
                                
                                ));
                        }
                            
                        }else if(is_numeric($_REQUEST['type']) && $_REQUEST['type']!= 0 ){
                        if($_REQUEST['searchstring'] ==""){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='product_type_id=:typeid and service_id=:serviceid';   
                            $criteria->params = array(':typeid'=>$_REQUEST['type'],':serviceid'=>$_REQUEST['service']);
                            $criteria->order = "name";
                            $products = Product::model()->findAll($criteria);
                            
                             $all_products = [];
                            foreach($products as $product){
                              if($model->canThisProductBeAddedToHamper($product['id'])){
                                    if($product['is_custom_product']==0){
                                       if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                               }
                             }
                            
                            header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                                ));
                        }else{
                            $searchstring = explode('+',$_REQUEST['searchstring']);
                            $searchWords = preg_replace('/\s/', '', $searchstring);
                            $searchable_items = [];
                            foreach($searchWords as $word){
                                 $q = "SELECT id FROM product where name REGEXP '$word'" ;
                                 $cmd = Yii::app()->db->createCommand($q);
                                 $results = $cmd->query();
                                 foreach($results as $result){
                                     $criteria = new CDbCriteria();
                                     $criteria->select = '*';
                                     $criteria->condition='id=:id and (service_id=:serviceid and product_type_id=:typeid)';   
                                     $criteria->params = array(':id'=>$result['id'],':serviceid'=>$_REQUEST['service'],':typeid'=>$_REQUEST['type']);
                                     $criteria->order = "name";
                                     $product = Product::model()->find($criteria);
                                     if($model->canThisProductBeAddedToHamper($product['id'])){
                                       if($product['is_custom_product']==0){
                                         if($product['displayable_on_store'] == 1){
                                             $searchable_items[] = $product;
                                         }
                                      }
                                     }
                                 }
                            }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $searchable_items
                                        
                                
                                ));
                        }
                        
                    }
                        
                        
                    }else if(is_numeric($_REQUEST['category']) && $_REQUEST['category']!= 0 ){
                        if($_REQUEST['type'] =="All Types" || $_REQUEST['type'] ==0){
                            if($_REQUEST['searchstring'] ==""){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='service_id=:serviceid and category_id=:categoryid';   
                            $criteria->params = array(':serviceid'=>$_REQUEST['service'],':categoryid'=>$_REQUEST['category']);
                            $criteria->order = "name";
                            $products = Product::model()->findAll($criteria);
                            
                          $all_products = [];
                          foreach($products as $product){
                              if($model->canThisProductBeAddedToHamper($product['id'])){
                                    if($product['is_custom_product']==0){
                                       if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                               }
                          }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                                ));
                        }else{
                          $searchstring = explode('+',$_REQUEST['searchstring']);
                            $searchWords = preg_replace('/\s/', '', $searchstring);
                            $searchable_items = [];
                            foreach($searchWords as $word){
                                 $q = "SELECT id FROM product where name REGEXP '$word'" ;
                                 $cmd = Yii::app()->db->createCommand($q);
                                 $results = $cmd->query();
                                 foreach($results as $result){
                                     $criteria = new CDbCriteria();
                                     $criteria->select = '*';
                                     $criteria->condition='id=:id and (service_id = :serviceid and category_id=:categoryid)';   
                                     $criteria->params = array(':id'=>$result['id'],':serviceid'=>$_REQUEST['service'],':categoryid'=>$_REQUEST['category']);
                                     $criteria->order = "name";
                                     $product = Product::model()->find($criteria);
                                    if($model->canThisProductBeAddedToHamper($product['id'])){
                                       if($product['is_custom_product']==0){
                                         if($product['displayable_on_store'] == 1){
                                             $searchable_items[] = $product;
                                         }
                                      }
                                     }
                                 }
                            }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $searchable_items
                                        
                                
                                ));
                        }
                            
                        }else if(is_numeric($_REQUEST['type']) && $_REQUEST['type']!= 0 ){
                        if($_REQUEST['searchstring'] ==""){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='(service_id=:serviceid and category_id=:categoryid) and product_type_id=:typeid';   
                            $criteria->params = array(':serviceid'=>$_REQUEST['service'],':categoryid'=>$_REQUEST['category'],':typeid'=>$_REQUEST['type']);
                            $criteria->order = "name";
                            $products = Product::model()->findAll($criteria);
                            
                             $all_products = [];
                            foreach($products as $product){
                              if($model->canThisProductBeAddedToHamper($product['id'])){
                                    if($product['is_custom_product']==0){
                                       if($product['displayable_on_store'] == 1){
                                            $all_products[] = $product;
                                        }
                                  }
                               }
                             }
                            
                            header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $all_products
                                        
                                
                                ));
                        }else{
                            $searchstring = explode('+',$_REQUEST['searchstring']);
                            $searchWords = preg_replace('/\s/', '', $searchstring);
                            $searchable_items = [];
                            foreach($searchWords as $word){
                                 $q = "SELECT id FROM product where name REGEXP '$word'" ;
                                 $cmd = Yii::app()->db->createCommand($q);
                                 $results = $cmd->query();
                                 foreach($results as $result){
                                     $criteria = new CDbCriteria();
                                     $criteria->select = '*';
                                     $criteria->condition='(id=:id and service_id=:serviceid) and (product_type_id=:typeid and category_id=:categoryid)';   
                                     $criteria->params = array(':id'=>$result['id'],':serviceid'=>$_REQUEST['service'],':typeid'=>$_REQUEST['type'],':categoryid'=>$_REQUEST['category']);
                                     $criteria->order = "name";
                                     $product = Product::model()->find($criteria);
                                     if($model->canThisProductBeAddedToHamper($product['id'])){
                                       if($product['is_custom_product']==0){
                                         if($product['displayable_on_store'] == 1){
                                             $searchable_items[] = $product;
                                         }
                                      }
                                     }
                                 }
                            }
                            
                             header('Content-Type: application/json');
                                        echo CJSON::encode(array(
                                         "success" => mysql_errno() == 0,
                                            "product" => $searchable_items
                                        
                                
                                ));
                        }
                        
                    }
                        
                        
                    }
                    
                }
  
        }
        
        
         /**
         * This is the function that retrieves a product details
         */
        public function actionretrievethedetailofproductinhamper(){
            
                $model = new PlatformSettings;        
                $member_id = Yii::app()->user->id;
            
                $product_id = $_REQUEST['product_id'];
                $hamper_id = $_REQUEST['hamper_id'];
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$product_id);
                $product= Product::model()->find($criteria);
                
                //get the quantity of the product in the hamper
                $product_quantity_in_hamper = $this->getTheQuantityOfThisProductInTheHamper($hamper_id,$product_id);
                if($this->doesProductHaveConstituents($product_id)){
                    
                    header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $product,
                                     "constituents" => true,
                                     "quantity_in_hamper"=>$product_quantity_in_hamper
                                    
                            ));          
                              
                }else{
                    header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $product,
                                     "constituents" => false,
                                     "quantity_in_hamper"=>$product_quantity_in_hamper
                                    
                            ));    
                }
                 
                
            
        }
        
        
         /**
         * This is the function that gets the quantity of a product in a hamper
         */
        public function getTheQuantityOfThisProductInTheHamper($hamper_id,$product_id){
            $model = new HamperHasProducts;
            return $model->getTheQuantityOfThisProductInTheHamper($hamper_id,$product_id);
        }
        
        
        /**
         * This is the function that gets hampers information for the cart
         */
        public function actiongetInformationAboutAHamperForCart(){
            
           
            $model = new HamperHasBeneficiary;
            
            $hamper_id = $_REQUEST['hamper_id'];
            
            //get the number of total member beneficiaries for this hamper
            $total_Member_beneficiary_number = $model->getTheNumberOfTotalMemberBeneficiariesForThisHamper($hamper_id);
            
            //get the number of total non member beneficiaries for this hamper
            $total_Non_Member_beneficiary_number = $this->getTheNumberOfTotalNonMemberBeneficiariesForThisHamper($hamper_id);
            
            //get the total beneficiaries of this hamper
            
            $total_beneficiary_number = $total_Member_beneficiary_number + $total_Non_Member_beneficiary_number;
            
            //get the total number of hampers for delivery
            $total_number_of_hampers_for_delivery = $model->getTheTotalNumberOfThisHamperForDelivery($hamper_id);
            
            //get the cost per hamper
            $cost_per_hamper = $this->getTheTotalPriceOfItemsInAHamper($hamper_id) + $model->getTheAverageMembersContainerPrice($hamper_id,$this->getTheTotalPriceOfItemsInAHamper($hamper_id));
            
            //get the average cost of hamper delivery
            $average_cost_of_hamper_delivery = $model->getTheAverageCostOfHamperDelivery($hamper_id);
            
            //get the total cost of this hamper for delivery
            $total_cost_of_hamper = $model->getTheTotalCostOfThisHamper($hamper_id);
            
            //get the total cost of delivery
            $total_cost_of_delivery = $model->getTheTotalCostOfDeliveryOfThisHamper($hamper_id);
            
            //get the terms and condition
            $terms_and_conditions = $this->getSendToCartTermsAndConditions($hamper_id);
                    
            
           
            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "total_beneficiary_number" => $total_beneficiary_number,
                                    "total_number_of_items_for_delivery" => $total_number_of_hampers_for_delivery,
                                    "cost_per_hamper"=>$cost_per_hamper,
                                    "average_cost_of_hamper_delivery"=>$average_cost_of_hamper_delivery,
                                     "total_cost_of_hamper"=>$total_cost_of_hamper,
                                     "total_cost_of_delivery"=>$total_cost_of_delivery,
                                     "terms_and_conditions"=>$terms_and_conditions
                                    
                            ));  
        }
        
        
        /**
         * This is the function that confirms if the terms were accepted for sending a hamper to cart
         */
        public function getSendToCartTermsAndConditions($hamper_id){
            $model = new OrderHasProducts;
            return false;
        }
        
        
        /**
         * This is the function that gets the total number of non member beneficiaries of a hamper
         */
        public function getTheNumberOfTotalNonMemberBeneficiariesForThisHamper($hamper_id){
            $model = new HamperHasNonMemberBeneficiary;
            return $model->getTheNumberOfTotalNonMemberBeneficiariesForThisHamper($hamper_id);
        }
        
        
        /**
         * This is the function that list all hampers a member is one of the beneficiaries
         */
        public function actionlistAllHampersThatAMemberIsABeneficiary(){
            
            $model = new HamperHasBeneficiary;
            
            $member_id = Yii::app()->user->id;
            
           // $member_id=2;
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='beneficiary_id=:beneid';
            $criteria->params = array(':beneid'=>$member_id);
            $beneficiaries= HamperHasBeneficiary::model()->findAll($criteria);
        
            if($beneficiaries===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "beneficiaries" => $beneficiaries)
                       );
                       
                }
        }
        
        /**
         * This is the function that gets some information about a hamper
         */
        public function actiongetInformationAboutAHamper(){
            
            $hamper_id = $_REQUEST['hamper_id'];
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$hamper_id);
            $hamper= Product::model()->find($criteria);
            
            if($hamper===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "hamper_label" => $hamper['name'])
                       );
                       
                }
            
            
        }
        
        
        /**
         * This is the hamper that sends hamper to the hamper manager from the store
         */
        public function actionsendingHamperToHamperManager(){
                    $model = new Product;
                    
                    $member_id = Yii::app()->user->id;
                    $product_id = $_POST['product_id'];
                    $member_id = Yii::app()->user->id;
                    $model->name = $_POST['hamper_label'];
                    $model->description = $model->getTheDescriptionOfThisProduct($product_id);
                    $model->hamper_cost_limit = $_POST['prevailing_retail_selling_price'];
                    $model->service_id = $model->getTheServiceIdOfThisProduct($product_id);
                    $model->category_id = $model->getTheCategoryIdOfThisProduct($product_id);
                    $model->product_type_id = $model->getTheProductTypeIdOfThisProduct($product_id);
                    $model->code = $model->generateAProductCode($model->service_id);
                    $model->hamper_container_id = $model->getTheHamperContainerIdOfThisHamper($product_id);
                    //$model->code = 'AABBCC003322189HAMP';
                    $model->is_custom_product = 1;
                    $model->brand = strtolower('custom');
                    $model->maker = strtolower('custom');
                    $model->origin = strtolower('custom');
                    $model->icon = $model->getTheIconOfThisProduct($product_id);
                    $model->headline_image = $model->getThisProductHeadlineImage($product_id);
                    $model->product_front_view = $model->getTheProductFrontViewImage($product_id);
                    $model->product_right_side_view = $model->getTheProductRightSideViewImage($product_id);
                    $model->product_top_view = $model->getTheProductTopViewImage($product_id);
                    $model->product_inside_view = $model->getTheProductInsideViewImage($product_id);
                    $model->product_engine_view = $model->getTheProductEngineViewImage($product_id);
                    $model->product_back_view = $model->getTheProductBackViewImage($product_id);
                    $model->product_left_side_view = $model->getTheProductLeftSideViewImage($product_id);
                    $model->product_bottom_view = $model->getTheProductBottomViewImage($product_id);
                    $model->product_dashboard_view = $model->getTheProductDashboardViewImage($product_id);
                    $model->product_contents_or_booth_view =$model->getTheProductContentsOrBoothViewImage($product_id);
                    $model->weight = $model->getTheWeightOfThisProduct($product_id);
                    $model->feature = $model->getTheFeatureOfThisProduct($product_id);
                    $model->condition = $model->getTheConditionOfThisProduct($product_id);
                    $model->specifications = $model->getTheSpecificationOfThisProduct($product_id);
                    if($model->isVideoRequitredForThisProduct($product_id)){
                        $model->is_with_video = 1;
                        $model->video_for = $model->getWhatAVideoIsFor($product_id);
                        $model->video_filename = $model->getTheVideoFilenameOfThisProduct($product_id);
                    }else{
                        $model->is_with_video = 0;
                    }
                     
                    $model->displayable_on_store = 0;
                    $model->whats_product_per_item = $_POST['whats_represents_an_item'];
                    $model->create_user_id = $member_id;
                    $model->create_time = new CDbExpression('NOW()');
                    
                  if($model->save()) {
                    if($this->isTheContentOfTheOriginalHamperAddedToThisNewHamperSuccessfully($model->id,$product_id,$model->hamper_cost_limit)){
                         $msg = "'$model->name' hamper is sent to the Hamper Manager where you can consummate every hamper related transaction. The Hamper Manager is in your 'My Oneroof' module";
                                 header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() == 0,
                                  "msg" => $msg)
                                    );
                        
                    }else{
                        $msg = "'$model->name' hamper is sent to the Hamper Manager but you have to add your own content as the original contents were not added to the your hamper automatically";
                              header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                 "success" => mysql_errno() == 0,
                                  "msg" => $msg)
                                    );
                    }
                               
                         
                        }else{
                            //delete all the moved files in the directory when validation error is encountered
                            $msg = 'Hamper Validaion Error: Check your file fields for correctness';
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                               );
                          }
            
        }
        
        /**
         * This is the function that adds original hamper content to a new hamper
         */
        public function isTheContentOfTheOriginalHamperAddedToThisNewHamperSuccessfully($new_hamper_id,$original_hamper_id,$hamper_cost_limit){
            $model = new HamperHasProducts;
            return $model->isTheContentOfTheOriginalHamperAddedToThisNewHamperSuccessfully($new_hamper_id,$original_hamper_id,$hamper_cost_limit);
        }
        
        
        /**
         * This is the function that changes the quantity of a product in a hamper
         */
        public function actionchangingTheQuantityOfAHamperItem(){
            
           $model = new HamperHasProducts;
            $hamper_id = $_POST['hamper_id'];
            $product_id = $_POST['product_id'];
            $product_quantity_in_the_hamper = $_POST['product_quantity_in_the_hamper'];
            
            $product_name = $this->getThisProductName($product_id);
            
            //get the current quantity of this product in this hamper
            $current_product_quantity = $model->getTheQuantityOfThisProductInTheHamper($hamper_id,$product_id);
            
            //get this hamper maximum limit
            $hamper_max_limit = $this->getThisHamperMaximumLimit($hamper_id);
            
            if($model->isTheChangingInThisHamperItemASuccess($hamper_id,$product_id,$product_quantity_in_the_hamper)){
                 $total_hamper_cost = $this->getTheTotalCostOfAnHamper($hamper_id);
                 if($hamper_max_limit >= $total_hamper_cost){
                     $msg = "The quantity of '$product_name' items in this hamper is now $product_quantity_in_the_hamper ";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg,
                                        "price"=>$total_hamper_cost)
                               );
                     
                 }else{
                     if($model->isTheChangingInThisHamperItemASuccess($hamper_id,$product_id,$current_product_quantity)){
                         $total_hamper_cost = $this->getTheTotalCostOfAnHamper($hamper_id);
                          
                            $msg = "Changing the quantity of '$product_name' items in this hamper to $product_quantity_in_the_hamper will cause the hamper to exceed its limit, hence the change was not effected ";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg,
                                        "price"=>$total_hamper_cost)
                               );
                     }else{
                         $msg = "Could not change the  quantity of '$product_name' items in this hamper. Please contact customer care for assistance ";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                               );
                     }
                     
                 }
                
                          
            }else{
                $msg = "Could not change the  quantity of '$product_name' items in this hamper. Please contact customer care for assistance ";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                               );
            }
            
            
            
        }
        
        
        /**
         * This is the function that gets the maximun limkit of a hamper
         */
        public function getThisHamperMaximumLimit($hamper_id){
            $model = new Product;
            return $model->getThisHamperMaximumLimit($hamper_id);
        }
        
        
        
        /**
         * This is the function that list all futures in the system
         */
        public function actionListAllProductsInTheStore(){
            
            $product = Product::model()->findAll(array('order'=>'name'));
                if($product===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"assigned" => $assigned,
                            "product" => $product)
                       );
                       
                }
        }
        
        
        /**
         * This the function that retrieves the previous product video filename
         */
        public function retrieveThePreviosProductVideo($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $video = Product::model()->find($criteria);
            
            
            return $video['video_filename'];
        }
        
        
        
        /**
         * This the function that retrieves the previous product video file size
         */
        public function retrieveThePreviousProductVideoSize($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $video = Product::model()->find($criteria);
            
            
            return $video['video_size'];
        }
        
        /**
         * This is the function that confirms if a hamper exist in a cart
         */
        public function actionconfirmTheExistenceOfAHamperInTheCart(){
            
            $model = new OrderHasProducts;
            $member_id = Yii::app()->user->id;
            
            //get the open cart of this member
            
            $order_id = $this->getTheOpenOrderInitiatedByMember($member_id);
            //confirm if the city of delivery accepts payment on delivery
            if($this->isOrderCityOfDeliveryAcceptPaymentOnDelivery($order_id)){
                 $non_ondelivery_item_exist = $model->isThereNonOnDeliveryItemInThisOrder($order_id);
            
                header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"assigned" => $assigned,
                            "non_ondelivery_exist" => $non_ondelivery_item_exist,
                             "order_iddd"=> $order_id 
                               )
                       );
            }else{
                 header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"assigned" => $assigned,
                            "non_ondelivery_exist" => false,
                           "order_iddd"=> $order_id 
                               )
                       );
            }
           
        }
        
        
        /**
         * This is the function that retrieves a product code
         */
        public function actiongetTheProductCode(){
            $model = new Product;
            $product_id = $_REQUEST['product_id'];
            
            $code = $model->getThisProductCode($product_id);
            
            header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"assigned" => $assigned,
                            "code" => $code
                               )
                       );
            
        }
        
        
        /**
         * This is the function that confirms if payment on delivery is acceptable in a city
         */
        public function isOrderCityOfDeliveryAcceptPaymentOnDelivery($order_id){
            $model = new City;
            return $model->isOrderCityOfDeliveryAcceptPaymentOnDelivery($order_id);
        }
       
        
        
         /**
    * This is the function that list all custom hampers for a member
    */
   public function actionlistAllNonCustomHampers(){
       
       $member_id = Yii::app()->user->id;
       
        $criteria = new CDbCriteria();
        $criteria->select = '*';
        $criteria->condition='is_a_hamper=:ishamper and is_custom_product=:custom';
        $criteria->params = array(':ishamper'=>1,':custom'=>0);
        $criteria->order = "name";
        $hampers= Product::model()->findAll($criteria);
        
        if($hampers===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "hamper" => $hampers)
                       );
                       
                }
       
   }
   
   
   /**
    * This is the function that retrieves the quantity of an item in a hamper
    */
   public function actiongetTheQuantityOfThisProductInThisHamper(){
       $model = new HamperHasProducts;
       
       $hamper_id = $_REQUEST['hamper_id'];
       
       $product_id = $_REQUEST['product_id'];
       
       $quantity = $model->getTheQuantityOfThisProductInTheHamper($hamper_id, $product_id);
       
        if($quantity===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "quantity" => $quantity)
                       );
                       
                }
       
   }
   
   
   /**
    * This is the function that effect a display of a hamper on the store
    */
   public function actionDisplayThisHamperOnStore(){
       
       $model = new Product;
       $hamper_container_id = $_POST['hamper_container'];
       $hamper_id = $_POST['hamper_id'];
       $price_of_hamper = $_POST['total_cost_for_computation'];
           
       
       if($model->isHamperReadyToBeDisplayedOnTheStore($hamper_id,$hamper_container_id,$price_of_hamper)){
           $msg = "This hamper is now displayed on the store"; 
           header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg" => $msg)
                       );
       }else{
           $msg = "Could not display this hamper on the store"; 
           header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            "msg" => $msg)
                       );
       }
   }
   
   
   
   
   /**
    * this is the function that retrieves all the leads purchased by a 
    * user
    */
   public function actionretrieveAllProductsPurchasedByAUser(){
       
       $model = new Order;
       
       $user_id = Yii::app()->user->id;
              
       //get all the closed orders by this user
       $closed_orders = $model->getAllClosedOrdersByThisUser($user_id);
       
       //retrieve all the products in these closed orders
       
       $purchased = [];
       foreach($closed_orders as $closed){
           //get all the products in an order
           $order_products = $this->getAllTheProductsInThisOrder($closed);
           foreach($order_products as $prod){
               if(in_array($prod,$purchased) == false){
                   $purchased[] = $prod;
               }
           }
       }
       //retrive all the purchased products
       $retrievable = [];
       foreach($purchased as $purchase){
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$purchase);
            $thisprod= Product::model()->find($criteria);
            $retrievable[] = $thisprod;
       }
       
        if($retrievable===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $retrievable
                                        
                                
                            ));
                       
                }
       
   }
   
   /**
    * This is the function that retrieves all the products in an order
    */
   public function getAllTheProductsInThisOrder($order){
       $model = new OrderHasProducts;
       return $model->getAllTheProductsInThisOrder($order);
   }
   
   
   /**
    * This is the function that determines if a product could be included in a filter
    */
   public function isThisProductIncludable($filter_para,$category_id){
       $model = new Category;
       return $model->isThisProductIncludable($filter_para,$category_id);
   }
   
   
   
   /**
         * This is the function that read and relocate the leads file to its directory
         */
        public function moveTheZippedLeadFileToItsPathAndReturnTheItsName($model,$leads_filename){
            
            if(isset($_FILES['lead_filename']['name'])){
                        $tmpName = $_FILES['lead_filename']['tmp_name'];
                        $leadName = $_FILES['lead_filename']['name'];    
                        $leadType = $_FILES['lead_filename']['type'];
                        $leadSize = $_FILES['lead_filename']['size'];
                  
                   }
                    
                    if($leadName !== null) {
                        if($model->id === null){
                              if($leads_filename != 'product_unavailable.png'){
                                $leadFileName = time().'_'.$leads_filename;  
                            }else{
                                $leadFileName = $leads_filename;  
                            }
                          
                           // upload the lead file
                        if($leadName !== null){
                            	$leadPath = Yii::app()->params['leads'].$leadFileName;
				move_uploaded_file($tmpName,  $leadPath);
                                        
                        
                                return $leadFileName;
                        }else{
                            $leadFileName = $leads_filename;
                            return $leadFileName;
                        } // validate to save file
                        }else{
                            if($this->noNewLeadFileProvided($model->id,$leads_filename)){
                                $leadFileName = $leads_filename; 
                                return $leadFileName;
                            }else{
                             if($leads_filename != 'product_unavailable.png'){
                                 if($this->removeTheExistingLeadFileFile($model->id)){
                                 $leadFileName = time().'_'.$leads_filename; 
                                 //$iconFileName = time().$icon_filename;  
                                   $leadPath = Yii::app()->params['leads'].$leadFileName;
                                   move_uploaded_file($tmpName,$leadPath);
                                   return $leadFileName;
                                    
                                                                
                             }
                             }
                                
                                
                            }
                            
                         
                                              
                            
                        }
                      
                     }else{
                         $leadFileName = $leads_filename;
                         return $leadFileName;
                     }
					
                       
                               
        }
        
        
        	/**
         * This is the function to ascertain if a new lead file was provided or not
         */
        public function noNewLeadFileProvided($id,$lead_filename){
            
                $criteria = new CDbCriteria();
                $criteria->select = 'id, lead_filename';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $lead= Product::model()->find($criteria);
                
                if($lead['lead_filename']==$lead_filename){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        	 /**
         * This is the function that removes an existing leads file
         */
        public function removeTheExistingLeadFileFile($id){
            
            //retreve the existing product engine view file from the database
            
            if($this->isTheLeadFileNotTheDefault($id)){
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $lead= Product::model()->find($criteria);
                
               $directoryPath = "/home/leadsdome/public_html/admin.leadsdome.com/leads/";
               $filepath =$directoryPath.$lead['lead_filename'];
                             
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
         * This is the function that determines if  the leads file  is the default
         */
        public function isTheLeadFileNotTheDefault($id){
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='id=:id';
                $criteria->params = array(':id'=>$id);
                $lead= Product::model()->find($criteria);
                
                if($lead['lead_filename'] == 'product_unavailable.png' || $icon['lead_filename'] ===NULL){
                    return false;
                }else{
                    return true;
                }
        }
        
        
        
         /**
         * This is the function that determines the type and size of the leads file
         */
        public function isLeadsFileTypeAndSizeLegal(){
            
           $size = []; 
            if(isset($_FILES['lead_filename']['name'])){
                $tmpName = $_FILES['lead_filename']['tmp_name'];
                $leadFileName = $_FILES['lead_filename']['name'];    
                $leadFileType = $_FILES['lead_filename']['type'];
                $leadFileSize = $_FILES['lead_filename']['size'];
            } 
                     
        if($leadFileType == 'application/zip' || $leadFileType == 'application/x-rar-compressed' || $leadFileType == 'application/octet-stream'){
            //if((in_array($iconFileType,$icontypes)) && ($platform_width <= $width && $platform_height <= $height)){
                return true;
               
            }else{
                return false;
            }
            
        }
        
        
        /**
         * This is the function that retrieves the previous lead filename
         * 
         */
         
        public function retrieveThePreviousLeadFileName($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $icon = Product::model()->find($criteria);
            
            
            return $icon['lead_filename'];
            
            
        }
        
        /**
         * This is the function that returns the previous leads file size
         */
        public function retrieveThePreviousLeadFileSize($id){
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';   
            $criteria->params = array(':id'=>$id);
            $poster = Product::model()->find($criteria);
            
            
            return $poster['lead_filesize'];
            
        }
        
        
        
        /**
         * This is the function that list all products belonging to a particular service
         */
        public function actionListAllProductsForAService(){
             
            $model = new Product;
            $start = $_REQUEST['start'];
            $limit = $_REQUEST['limit']; 
            $service_id = $_REQUEST['service_id'];
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='service_id=:id and displayable_on_store=1';   
                $criteria->params = array(':id'=>$service_id);
                $criteria->order = "name";
                $criteria->offset = $start;
                $criteria->limit = $limit;     
                $products = Product::model()->findAll($criteria);
                
                //get the total number of products for this service
                $counts = $model->getTheTotalNumberOfProductsForThisService($service_id);
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "results"=>$counts,
                                     "product" => $products
                                        
                                
                            ));
                       
                }
            
                
                
          
            
        }
        
        
        
         /**
         * This is the function that list all products belonging to a particular category
         */
        public function actionListAllProductsForACategory(){
                      
                $model = new Product;
                $start = $_REQUEST['start'];
                $limit = $_REQUEST['limit'];
            
                $category_id = $_REQUEST['category_id'];
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='category_id=:id and displayable_on_store=1';   
                $criteria->params = array(':id'=>$category_id);
                $criteria->order = "name";
                $criteria->offset = $start;
                $criteria->limit = $limit;     
                $products = Product::model()->findAll($criteria);
                
                //get the total number of products under this category
                $counts = $model->getTheTotalNumberOfProductsForThisCategory($category_id);
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "results"=>$counts,
                                     "product" => $products
                                        
                                
                            ));
                       
                }
            
                
                
          
            
        }
        
        
        
          /**
         * This is the function that list all products belonging to a particular type
         */
        public function actionListAllProductsForAType(){
                      
                $model = new Product;
                $start = $_REQUEST['start'];
                $limit = $_REQUEST['limit'];
                $type_id = $_REQUEST['type_id'];
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='product_type_id=:id and displayable_on_store=1';   
                $criteria->params = array(':id'=>$type_id);
                $criteria->order = "name";
                $criteria->offset = $start;
                $criteria->limit = $limit;     
                $products = Product::model()->findAll($criteria);
                
                //get the total number if products for this type
                $counts = $model->getTheTotalNumberOfProductsForThisType($type_id);
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "results"=>$counts,
                                     "product" => $products
                                        
                                
                            ));
                       
                }
            
                
                
          
            
        }
        
        
        /**
         * This is the function that list all products on sales depending on processing scope
        
        public function actionListAllProductsOnSales(){
            
            $model = new Product;
            
             $scope = $_REQUEST['scope'];
            
           // $id = $_REQUEST['id'];
            
            if($scope == strtolower('service')){
                
                $target = [];
                
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='displayable_on_store=:is_displayable';   
                $criteria->params = array(':is_displayable'=>1);
                $products = Product::model()->findAll($criteria);
                
                foreach($products as $product){
                    if($model->isThisProductOnPromotion($product['id'])){
                        $target[] = $product;
                    }
                }
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $target
                                        
                                
                            ));
                       
                }
                
            }else if($scope == strtolower('category')){
               // $service_id = $_REQUEST['id'];
                $target = [];
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='service_id=:id and displayable_on_store=:is_displayable';   
                $criteria->params = array(':id'=>$service_id,'is_displayable'=>1);
                $products = Product::model()->findAll($criteria);
                
                 foreach($products as $product){
                    if($model->isThisProductOnPromotion($product['id'])){
                        $target[] = $product;
                    }
                }
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $target
                                        
                                
                            ));
                       
                }
                
            }else if($scope == strtolower('type')){
               //  $category_id = $_REQUEST['id'];
                $target = [];
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='category_id=:id and displayable_on_store=:is_displayable';   
                $criteria->params = array(':id'=>$category_id,':is_displayable'=>1);
                $products = Product::model()->findAll($criteria);
                
                 foreach($products as $product){
                    if($model->isThisProductOnPromotion($product['id'])){
                        $target[] = $product;
                    }
                }
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $target
                                        
                                
                            ));
                       
                }
                
            }else{
                $target = [];
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='displayable_on_store=:is_displayable';   
                $criteria->params = array(':is_displayable'=>1);
                $products = Product::model()->findAll($criteria);
                
                 foreach($products as $product){
                    if($model->isThisProductOnPromotion($product['id'])){
                        $target[] = $product;
                    }
                }
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $target
                                        
                                
                            ));
                       
                }
                
            }
        }
         * 
         */
        
        /**
         * This is the function that retrieves all the products on sales
         */
        public function actionListAllProductsOnSales(){
                $model = new Product;
                $start = $_REQUEST['start'];
                $limit = $_REQUEST['limit'];
            
                $target = [];
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='(displayable_on_store=:is_displayable and (is_faas=0 and is_quotable=0)) and(has_free_shipping_promotion=1 or has_give_away_promotion=1 or has_percentage_off_promotion=1 or has_buy_one_get_one_promotion=1)';   
                $criteria->params = array(':is_displayable'=>1);
                $criteria->order = "name";
                $criteria->offset = $start;
                $criteria->limit = $limit;     
                $products = Product::model()->findAll($criteria);
                
                /** foreach($products as $product){
                    if($model->isThisProductOnPromotion($product['id'])){
                        $target[] = $product;
                    }
                }
                 * 
                 */
                
                $count = $model->getTheTotalProductsOnSales();
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "results"=>$count,
                                     "product" => $products
                                        
                                
                            ));
                       
                }
            
            
            
        }
        
        
        
        /**
         * This is the function that list all products less than N1000 depending on scope
         
        public function actionListAllProductsLessThan1000(){
            
            
            $model = new Product;
            
            $scope = $_REQUEST['scope'];
            $start = $_REQUEST['start'];
            $limit = $_REQUEST['limit'];
            
            //$id = $_REQUEST['id'];
            
            if($scope == strtolower('service')){
                
                $target = [];
                
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='service_id=:id and displayable_on_store=:is_displayable';   
                $criteria->limit = "$start,$limit";
                $criteria->params = array(':id'=>$id,':is_displayable'=>1);
                $products = Product::model()->findAll($criteria);
                
                foreach($products as $product){
                    if($model->isThePriceOfThisProductLessThan1000Naira($product['id'])){
                        $target[] = $product;
                    }
                }
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $target
                                        
                                
                            ));
                       
                }
                
            }else if($scope == strtolower('category')){
                
                $target = [];
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='category_id=:id and displayable_on_store=:is_displayable';  
                $criteria->limit = 100;
                $criteria->params = array(':id'=>$id,'is_displayable'=>1);
                $products = Product::model()->findAll($criteria);
                
                 foreach($products as $product){
                    if($model->isThePriceOfThisProductLessThan1000Naira($product['id'])){
                        $target[] = $product;
                    }
                }
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $target
                                        
                                
                            ));
                       
                }
                
            }else if($scope == strtolower('type')){
                
                $target = [];
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='product_type_id=:id and displayable_on_store=:is_displayable'; 
                $criteria->limit = $limit;
                $criteria->params = array(':id'=>$id,':is_displayable'=>1);
                $products = Product::model()->findAll($criteria);
                
                 foreach($products as $product){
                    if($model->isThePriceOfThisProductLessThan1000Naira($product['id'])){
                        $target[] = $product;
                    }
                }
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $target
                                        
                                
                            ));
                       
                }
                
            }else{
                $target = [];
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='displayable_on_store=:is_displayable'; 
                $criteria->offset = $start;
                $criteria->limit = 100;
                $criteria->params = array(':is_displayable'=>1);
                $products = Product::model()->findAll($criteria);
                
                 foreach($products as $product){
                    if($model->isThePriceOfThisProductLessThan1000Naira($product['id'])){
                        $target[] = $product;
                    }
                }
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $target
                                        
                                
                            ));
                       
                }
                
            }
            
        }
         * 
         */
        
         /**
         * This is the function that list all products less than N1000 depending on scope
         */
        public function actionListAllProductsLessThan1000(){
                $model = new Product;
                $start = $_REQUEST['start'];
                $limit = $_REQUEST['limit'];
                
                $target = [];
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='(prevailing_retail_selling_price<=1000 and is_quotable=0) and (displayable_on_store=:is_displayable and is_faas=0)'; 
                $criteria->params = array(':is_displayable'=>1);
                $criteria->order = "name";
                $criteria->offset = $start;
                $criteria->limit = $limit;                          
                $products = Product::model()->findAll($criteria);
                
                
                
                /** foreach($products as $product){
                    if($model->isThePriceOfThisProductLessThan1000Naira($product['id'])){
                        $target[] = $product;
                    }
                }
                 * 
                 */
                $count = $model->getTheTotalNumberOfProductsWithLessThanOrEqualTo1000NairaPriceTag();
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "results"=>$count,
                                     "product" => $products
                                                                            
                                
                            ));
                       
                }
                
            }
        
        
        
        /**
         * This is the function that list all products for rent depending on scope
         
        public function actionListAllRentableProducts(){
            
            $model = new Product;
            
            $scope = $_REQUEST['scope'];
            
            //$id = $_REQUEST['id'];
            
            if($scope == strtolower('service')){
                
                $target = [];
                
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='service_id=:id and displayable_on_store=:is_displayable';   
                $criteria->params = array(':id'=>$id,':is_displayable'=>1);
                $products = Product::model()->findAll($criteria);
                
                foreach($products as $product){
                    if($model->isTheProductRentable($product['id'])){
                        $target[] = $product;
                    }
                }
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $target
                                        
                                
                            ));
                       
                }
                
            }else if($scope == strtolower('category')){
                
                $target = [];
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='category_id=:id and displayable_on_store=:is_displayable';   
                $criteria->params = array(':id'=>$id,'is_displayable'=>1);
                $products = Product::model()->findAll($criteria);
                
                 foreach($products as $product){
                    if($model->isTheProductRentable($product['id'])){
                        $target[] = $product;
                    }
                }
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $target
                                        
                                
                            ));
                       
                }
                
            }else if($scope == strtolower('type')){
                
                $target = [];
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='product_type_id=:id and displayable_on_store=:is_displayable';   
                $criteria->params = array(':id'=>$id,':is_displayable'=>1);
                $products = Product::model()->findAll($criteria);
                
                 foreach($products as $product){
                    if($model->isTheProductRentable($product['id'])){
                        $target[] = $product;
                    }
                }
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $target
                                        
                                
                            ));
                       
                }
                
            }else{
                $target = [];
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='displayable_on_store=:is_displayable';   
                $criteria->params = array(':is_displayable'=>1);
                $products = Product::model()->findAll($criteria);
                
                 foreach($products as $product){
                    if($model->isTheProductRentable($product['id'])){
                        $target[] = $product;
                    }
                }
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $target
                                        
                                
                            ));
                       
                }
                
            }
        }
         * 
         */
            
         /**
          * This is the function that retrieves all rentable products on the store
          */   
           public function actionListAllRentableProducts(){
                $model = new Product;
                $start = $_REQUEST['start'];
                $limit = $_REQUEST['limit'];
                
                $target = [];
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='(is_rentable=:isrentable and is_quotable=0) and (displayable_on_store=:is_displayable and is_faas=0)';   
                $criteria->params = array(':isrentable'=>1,':is_displayable'=>1);
                $criteria->order = "name";
                $criteria->offset = $start;
                $criteria->limit = $limit;     
                $products = Product::model()->findAll($criteria);
                
                /** foreach($products as $product){
                    if($model->isTheProductRentable($product['id'])){
                        $target[] = $product;
                    }
                }
                 * 
                 */
            
                $count = $model->getTheTotalNumberOfProductsThatAreRentable();
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                      "results"=>$count,
                                      "product" => $products
                                        
                                
                            ));
                       
                }
               
               
               
           }  
        
      
        /**
         * This is the function that list all products for subscription
         */
        public function actionListAllProductsForSubscription(){
            
            $model = new ProductType;
            
            $scope = $_REQUEST['scope'];
            
           // $id = $_REQUEST['id'];
            
            if($scope == strtolower('service')){
                
                $target = [];
                
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='(service_id=:id and is_paas=1) and (displayable_on_store=:is_displayable and is_faas=0)';   
                $criteria->params = array(':id'=>$id,':is_displayable'=>1);
                $products = Product::model()->findAll($criteria);
                
                foreach($products as $product){
                    if($model->isTheProductAvailableForAService($product['product_type_id'])){
                        $target[] = $product;
                    }
                }
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $target
                                        
                                
                            ));
                       
                }
                
            }else if($scope == strtolower('category')){
                
                $target = [];
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='(category_id=:id and is_paas=1) and (displayable_on_store=:is_displayable is_faas=0)';   
                $criteria->params = array(':id'=>$id,'is_displayable'=>1);
                $products = Product::model()->findAll($criteria);
                
                 foreach($products as $product){
                    if($model->isTheProductAvailableForAService($product['product_type_id'])){
                        $target[] = $product;
                    }
                }
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $target
                                        
                                
                            ));
                       
                }
                
            }else if($scope == strtolower('type')){
                
                $target = [];
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='(product_type_id=:id and is_paas=1) and (displayable_on_store=:is_displayable and is_faas=0)';   
                $criteria->params = array(':id'=>$id,':is_displayable'=>1);
                $products = Product::model()->findAll($criteria);
                
                 foreach($products as $product){
                    if($model->isTheProductAvailableForAService($product['product_type_id'])){
                        $target[] = $product;
                    }
                }
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $target
                                        
                                
                            ));
                       
                }
                
            }else{
                $target = [];
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='(displayable_on_store=:is_displayable and is_paas=1) and is_faas=0';   
                $criteria->params = array(':is_displayable'=>1);
                $products = Product::model()->findAll($criteria);
                
                 foreach($products as $product){
                    if($model->isTheProductAvailableForAService($product['product_type_id'])){
                        $target[] = $product;
                    }
                }
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $target
                                        
                                
                            ));
                       
                }
                
            }
            
        }
        
        
        /**
         * This is the function that list all books based on their product type code 
         */
        public function actionListAllBookProductsType(){
            
            $model = new ProductType;
            
            $scope = $_REQUEST['code'];
            
           // $id = $_REQUEST['id'];
            
            if($scope == 'BOOKBASIC'){
                
                              
                $type_id = $model->getThisTypeIdGivenItsCode($scope);
                
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='displayable_on_store=:is_displayable';   
                $criteria->params = array(':is_displayable'=>1);
                $products = Product::model()->findAll($criteria);
                $target = [];
                  foreach($products as $product){
                    if($model->isThisBookForNurseryOrPrimarySchool($product['product_type_id'],$scope)){
                        $target[] = $product;
                    }
                }
               
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $target
                                        
                                
                            ));
                       
                }
                
            }else if($scope == 'BOOKJSSSSS'){
                
                $target = [];
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='displayable_on_store=:is_displayable';   
                $criteria->params = array(':is_displayable'=>1);
                $products = Product::model()->findAll($criteria);
                
                 foreach($products as $product){
                    if($model->isThisBookForSecondarySchool($product['product_type_id'],$scope)){
                        $target[] = $product;
                    }
                }
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $target
                                        
                                
                            ));
                       
                }
                
            }else if($scope == 'BOOKTERTIARY'){
                
                $target = [];
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='displayable_on_store=:is_displayable';   
                $criteria->params = array(':is_displayable'=>1);
                $products = Product::model()->findAll($criteria);
                
                 foreach($products as $product){
                    if($model->isThisBookForTertiaryInstitutions($product['product_type_id'],$scope)){
                        $target[] = $product;
                    }
                }
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $target
                                        
                                
                            ));
                       
                }
                
            }else if($scope == 'BOOKPROFESSIONAL'){
                $target = [];
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='displayable_on_store=:is_displayable';   
                $criteria->params = array(':is_displayable'=>1);
                $products = Product::model()->findAll($criteria);
                
                 foreach($products as $product){
                    if($model->isThisBookForProfessionals($product['product_type_id'],$scope)){
                        $target[] = $product;
                    }
                }
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $target
                                        
                                
                            ));
                       
                }
                
            }else{
                $target = [];
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='displayable_on_store=:is_displayable';   
                $criteria->params = array(':is_displayable'=>1);
                $products = Product::model()->findAll($criteria);
                
                 foreach($products as $product){
                    if($model->isThisBookInOthersSections($product['product_type_id'],'BOOKOTHERS')){
                        $target[] = $product;
                    }
                }
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                     "product" => $target
                                        
                                
                            ));
                       
                }
                
            }
            
        }
        
        
        
        /**
         * This is the function that retrieves the product on the home page middle advert
         */
        public function actionretrieveTheMiddlePageAdvertProduct(){
            $model = new Product;
             $criteria = new CDbCriteria();
             $criteria->select = '*';
             $criteria->condition='displayable_on_store=:is_displayable and is_the_middle_page_advert =:advert';   
             $criteria->params = array(':is_displayable'=>1,':advert'=>1);
             $products = Product::model()->findAll($criteria);
                
             
             //get to the total number of products displayable by middle adverts
             $counts = $model->getTheTotalNumberOfProductsDisplayableByMiddleAdverts();
              if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "results"=>$counts,
                                     "product" => $products
                                        
                                
                            ));
                       
                }
                
            }
            
            /**
             * This is the function that retrieves a paas subscriotion cost
             */
            public function getThePaasSubscriptionCost($product_type_id){
                $model = new ProductType;
                return $model->getThePaasSubscriptionCost($product_type_id);
            }
        
            
              /**
             * This is the function that retrieves a paas subscription minimum required quantity
             */
            public function getTheMinimumQuantityRequiredForPaasSubscription($product_type_id){
                $model = new ProductType;
                return $model->getTheMinimumQuantityRequiredForPaasSubscription($product_type_id);
            }
        
            
            /**
             * This is the function that retrieves a paas subscription maximmum allowable quantity
             */
            public function getTheMaximumQuantityForThisPaasSubscription($product_type_id){
                $model = new ProductType;
                return $model->getTheMaximumQuantityForThisPaasSubscription($product_type_id);
            }
            
            
            /**
             * This is the function that determines if a producr is paas enabled
             */
            public function isTheProductAvailableForAService($product_type_id){
                $model = new ProductType;
                return $model->isTheProductAvailableForAService($product_type_id);
            }
            
            /**
             * This is the function that gets the minimum paas duration in the system 
             */
            public function getTheMinimumNumberOfPaasDuration($product_type_id){
                $model = new ProductType;
                return $model->getTheMinimumNumberOfPaasDuration($product_type_id);
                
            }
            
            
            /**
             * This is the function that gets the maximum paas duration in the system 
             */
            public function getTheMaximumNumberOfPaasDuration($product_type_id){
                $model = new ProductType;
                return $model->getTheMaximumNumberOfPaasDuration($product_type_id);
                
            }
            
            
            /**
             * This is the function that list all nursery and primary school books
             */
            public function actionListAllBasicBookProductsType(){
                
                $model = new ProductType;
                $start = $_REQUEST['start'];
                $limit = $_REQUEST['limit'];
                
                //get the category id of the primary school books
                $category_id = $model->getTheCategoryIdOfNurserySchoolBooks();
                
               // $primary_book_type_id = $model->getTheProductTypeIdOfPrimarySchoolBooks();
                
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='displayable_on_store=:is_displayable and category_id=:primaryid';   
                $criteria->params = array(':is_displayable'=>1,':primaryid'=>$category_id);
                $criteria->order = "name";
                $criteria->offset = $start;
                $criteria->limit = $limit;     
                $products = Product::model()->findAll($criteria);
               
                $count = $model->getTheTotalNumberOfBothNurseryAndPrimarySchoolDisplaybleBooks();
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "results"=>$count,
                                     "product" => $products
                                        
                                
                            ));
                       
                }
            }
            
            
            
            /**
             * This is the function that list all junior secondary school and senior secondary school books
             */
            public function actionListAllSssBookProductsType(){
                
                $model = new ProductType;
                $start = $_REQUEST['start'];
                $limit = $_REQUEST['limit'];
                
                //get the product id of both nursery and primary school books
                $category_id = $model->getTheSecondarySchoolCategoryId();
               
                
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='displayable_on_store=:is_displayable and category_id=:categoryid';   
                $criteria->params = array(':is_displayable'=>1,':categoryid'=>$category_id);
                $criteria->order = "name";
                $criteria->offset = $start;
                $criteria->limit = $limit;     
                $products = Product::model()->findAll($criteria);
               
                $count = $model->getTheTotalNumberOfBothJssAndSssSchoolDisplaybleBooks();
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "results"=>$count,
                                     "product" => $products
                                        
                                
                            ));
                       
                }
            }
            
            
            /**
             * This is the function that list all tertiary institutions books
             */
            public function actionListAllTertiaryBookProductsType(){
                
                $model = new ProductType;
                $start = $_REQUEST['start'];
                $limit = $_REQUEST['limit'];
                
                //get the category id of tertiary instutions book
                $category_id = $model->getTheCategoryIdOfTertiarySchoolBooks();
                                
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='displayable_on_store=:is_displayable and category_id=:tertiaryid';   
                $criteria->params = array(':is_displayable'=>1,':tertiaryid'=>$category_id);
                $criteria->order = "name";
                $criteria->offset = $start;
                $criteria->limit = $limit;     
                $products = Product::model()->findAll($criteria);
               
                $count = $model->getTheTotalNumberOfTertiarySchoolDisplaybleBooks();
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "results"=>$count,
                                     "product" => $products
                                        
                                
                            ));
                       
                }
            }
            
            
            
            /**
             * This is the function that list all professional books
             */
            public function actionListAllProfessionalBookProductsType(){
                
                $model = new ProductType;
                $start = $_REQUEST['start'];
                $limit = $_REQUEST['limit'];
                
                //get the category id of professional book
                $category_id = $model->getTheCategoryIdOfProfessionalSchoolBooks();
                                
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='displayable_on_store=:is_displayable and category_id=:proid';   
                $criteria->params = array(':is_displayable'=>1,':proid'=>$category_id);
                $criteria->order = "name";
                $criteria->offset = $start;
                $criteria->limit = $limit;     
                $products = Product::model()->findAll($criteria);
               
                $count = $model->getTheTotalNumberOfProfessionalDisplaybleBooks();
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "results"=>$count,
                                     "product" => $products
                                        
                                
                            ));
                       
                }
            }
            
            
            
            /**
             * This is the function that list all others books
             */
            public function actionListAllOtherBookProductsType(){
                
                $model = new ProductType;
                $start = $_REQUEST['start'];
                $limit = $_REQUEST['limit'];
                
                //get the product id of other books
                $category_id = $model->getTheCategoryIdOfOtherBooks();
                                
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='displayable_on_store=:is_displayable and category_id=:othersid';   
                $criteria->params = array(':is_displayable'=>1,':othersid'=>$category_id);
                $criteria->order = "name";
                $criteria->offset = $start;
                $criteria->limit = $limit;     
                $products = Product::model()->findAll($criteria);
               
                $count = $model->getTheTotalNumberOfOtherDisplaybleBooks();
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "results"=>$count,
                                     "product" => $products
                                        
                                
                            ));
                       
                }
            }
            
            
            /**
             * This is the function that list all stationary products on the store
             */
            public function actionListAllLearningTools(){
                
                $model = new Service;
                $start = $_REQUEST['start'];
                $limit = $_REQUEST['limit'];
                
                //get the product id of other books
                $stationary_id = $model->getTheStationaryServiceIdOnTheStore();
                                
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='displayable_on_store=:is_displayable and service_id=:serviceid';   
                $criteria->params = array(':is_displayable'=>1,':serviceid'=>$stationary_id);
                $criteria->order = "name";
                $criteria->offset = $start;
                $criteria->limit = $limit;     
                $products = Product::model()->findAll($criteria);
               
                $count = $model->getTheTotalNumberOfStationaryDisplayableProducts();
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "results"=>$count,
                                     "product" => $products
                                        
                                
                            ));
                       
                }
                
            }
            
            
            /**
             * This is the function that list all faas products of a tupe
             */
            public function actionListAllProductsForAFaasCategory(){
                
                $model = new Product;
                $start = $_REQUEST['start'];
                $limit = $_REQUEST['limit'];
            
                $category_id = $_REQUEST['category_id'];
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='category_id=:id and (displayable_on_store=1 and is_faas=1)';   
                $criteria->params = array(':id'=>$category_id);
                $criteria->order = "name";
                $criteria->offset = $start;
                $criteria->limit = $limit;     
                $products = Product::model()->findAll($criteria);
                
                //get the total number of faas products under this category
                $counts = $model->getTheTotalNumberOfProductsForThisFaasCategory($category_id);
            
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "results"=>$counts,
                                     "product" => $products
                                        
                                
                            ));
                       
                }
            }
            
            
            /**
             * This is the function that list all products for a faas type
             */
            public function actionListAllProductsForAFaasType(){
                
                $model = new Product;
                $start = $_REQUEST['start'];
                $limit = $_REQUEST['limit'];
                $type_id = $_REQUEST['type_id'];
            
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='product_type_id=:id and (displayable_on_store=1 and is_faas=1)';   
                $criteria->params = array(':id'=>$type_id);
                $criteria->order = "name";
                $criteria->offset = $start;
                $criteria->limit = $limit;     
                $products = Product::model()->findAll($criteria);
                
                //get the total number if products for this faas type
                $counts = $model->getTheTotalNumberOfProductsForThisFaasType($type_id);
            
                
                if($products===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                    "results"=>$counts,
                                     "product" => $products
                                        
                                
                            ));
                       
                }
            }
            
            /**
             * This is the function that modifies the product atock quantity
             */
            public function actionmodifyStockQuantity(){
                
                $model = new Product;
                
                $id = $_POST['id'];
                
                $name = $_POST['name'];
                $code = $_POST['code'];
                $service = $_POST['service_id'];
                $category = $_POST['category_id'];
                $type = $_POST['product_type_id'];
                $whats_product_per_item = $_POST['whats_product_per_item'];
                $operation = strtolower($_POST['operation']);
                $cumulative_quantity = $_POST['cumulative_quantity'];
                $current_quanitity_in_stock = $_POST['quantity'];
                
                if($operation =='add'){
                    $cumulative_quantity = $_POST['cumulative_quantity'] + $_POST['quantity_to_add'];
                    $current_quanitity_in_stock = $_POST['quantity'] + $_POST['quantity_to_add'];
                    
                }else if($operation =='remove'){
                    if($_POST['quantity_to_remove'] <$_POST['cumulative_quantity']){
                         $cumulative_quantity = $_POST['cumulative_quantity'] - $_POST['quantity_to_remove'];
                    }else{
                      $cumulative_quantity = 0;  
                    }
                    if($_POST['quantity_to_remove'] <$_POST['quantity']){
                         $current_quanitity_in_stock = $_POST['quantity'] - $_POST['quantity_to_remove'];
                    }else{
                        $current_quanitity_in_stock =0;
                    }
                   
                    
                }
                
                if($model->isModificationOfTheProductStockSuccessful($id,$cumulative_quantity,$current_quanitity_in_stock)){
                    $msg = "This Product Stock Quantity was successfully modified";
                    header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg
                                       )
                           );
                    
                    
                }else{
                    $msg = "Modification of this product quantity was not successful";
                    header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg
                                       )
                           );
                    
                }
                
                
                
                
            }
   
        
            /**
             * This is the function t6hat places or removes a product from middple page advert
             */
            public function actionmodifyProductMiddleAdvertPlacementStatus(){
                
                $model = new Product;
                
                $id = $_POST['id'];
                
                if(strtolower($_POST['operation']) == 'add'){
                    
                    $is_the_middle_page_advert = 1;
                    
                }else if(strtolower($_POST['operation']) == 'remove'){
                    $is_the_middle_page_advert = 0;
                }
                
                if($model->isTheModificationOfMiddlePageAdvertPlacementASuccess($id,$is_the_middle_page_advert)){
                    
                     $msg = "This Product is successfully included or excluded from the Middle Page Advert Placement";
                    header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg
                                       )
                           );
                }else{
                     $msg = "This Product is successfully excluded from the Middle Page Advert Placemen";
                    header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg
                                       )
                           );
                }
            }
            
            
}









 