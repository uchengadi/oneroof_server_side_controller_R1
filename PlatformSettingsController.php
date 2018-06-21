<?php

class PlatformSettingsController extends Controller
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
				'actions'=>array('create','update','ListAllPlatformsettingParamters','DeleteOnePlatformSettings',
                                    'AddNewPlatformSettings','UpdatePlatformSettings','retrieveSettingsInfo','retrieveTheNeighbourhoodsThatArePermittedToRequestForBoxes',
                                    'retrieveTheNeighbourhoodsThatArePermittedToRequestForBoxes','AddPermittedNeighbourhoodsForBoxesOnPlatform','AddPermittedClustersForBoxesOnPlatform',
                                    'retrieveTheClustersThatArePermittedToRequestForBoxes','AddPermittedClustersForDocumentCaputuringOnPlatform',
                                    'retrieveTheClustersThatArePermittedToCaptureDocuments','retrieveDocumentTpesThatDeterminesTheBatchToAcceptInABox',
                                    'retrieveDocumentCategoriesThatDeterminesTheBatchToAcceptInABox','AddDocumentTypesThatAreAcceptableToBatchesInBoxesOnPlatform',
                                    'AddDocumentCategoriesThatAreAcceptableToBatchesInBoxesOnPlatform','AddNeighbourhoodsWithPermissionToCollectBoxesOnPlatform',
                                    'retrieveNeighbourhoodsWithThePermissionToConsumeBoxes','retrieveClustersWithThePermissionToConsumeBoxes',
                                    'AddClustersWithPermissionToCollectBoxesOnPlatform','retrieveTheDocumentTypesThatAreAvailableForExchange',
                                    'retrieveTheNeighbourhoodsThatDocumentTypesAreRestrictedToOnExchange','retrieveTheClustersThatDocumentTypesAreRestrictedToOnExchange',
                                    'AddDocumentTypesThatAreAvailableForExchangeOnPlatform','AddDocumentTypesRestrictedToNeighbourhoodsAtExchangeOnPlatform',
                                    'AddDocumentTypesRestrictedToClustersAtExchangeOnPlatform','retrieveTheNeighbourhoodsThatDocumentTypesAreRestrictedToOnSharing',
                                    'AddDocumentTypeRestrictedToNeighbourhoodsAtSharingOnPlatform','AddDocumentTypeRestrictedToClustersAtSharingOnPlatform',
                                    'AddDocumentTypesThatAreAvailableForSharingOnPlatform','retrieveTheDocumentTypesThatAreAvailableForSharing','retrieveTheClustersThatDocumentTypesAreRestrictedToOnSharing',
                                    'retrieveTheNeighbourhoodsThatDocumentTypesAreRestrictedToOnTransfers','retrieveTheDocumentTypesThatAreAvailableOnTransfers','retrieveTheClustersThatDocumentTypesAreRestrictedToOnTransfers',
                                    'AddDocumentTypesThatAreRestrictedToNeighbourhoodsAtTransfersOnPlatform','AddDocumentTypesThatAreAvailableAtTransfersOnPlatform','AddDocumentTypesThatAreRestrictedToClustersAtTransfersOnPlatform',
                                    'retrieveTheDocumentTypesThatAreAvailableOnTrading','retrieveTheNeighbourhoodsThatDocumentTypesAreRestrictedToOnTrading','restrictTradingOfDocumenttypesOnStoresOwnByTheseNeighbourhoodsOnTrading',
                                    'allowTheseNeighbourhoodsToTradeOnOwnPrivateStoreOnTrading','allowTheseDocumentTypesFromSomeNeighbourhoodToTradeOnOwnStoreOnTrading','AddDocumentTypesThatAreAvailableAtTradingOnPlatform',
                                    'AddDocumentTypesThatAreRestrictedToNeighbourhoodsAtTradingOnPlatform','AddDocumentTypesThatAreRestrictedToClustersAtTradingOnPlatform','AddAllowTheseNeighbourhoodsToTradeOnOwnStoreAtTradingOnPlatform',
                                    'AddAllowTheseDocumenttypesFromSomeNeighbourhoodsToTradeOnOwnStoreAtTradingOnPlatform','AddDocumentTypesThatAreRestrictedToClustersAtTradingOnPlatform',
                                    'retrieveTheClustersThatDocumentTypesAreRestrictedToOnTrading','retrieveNeighbourhoodsThatDocumentMovementsHadBeenRestrictedToAtMovement','retrieveAllTheDocumentsThatCannotBeMovedAtMovement',
                                    'retrieveAllTheDocumentsThatCannotBeMovedOutsideOwnDomainAtMovement','retrieveAllProcessorsWhoseDocumentsCannotBeMovedAtMovement','retrieveAllProcessorsWhoseDocumentsCannotBeMovedOutsideOwnDomainAtMovement',
                                    'retrieveAllTheBatchesThatCannotBeMovedAtMovement','retrieveNeighbourhoodsThatBatchMovementsHadBeenRestrictedToAtMovement','retrieveAllTheBatchesThatCannotBeMovedOutsideOwnDomainAtMovement',
                                    'retrieveAllTheBoxesThatCannotBeMovedAtMovement','retrieveNeighbourhoodsThatBoxMovementsHadBeenRestrictedToAtMovement','retrieveAllTheBoxesThatCannotBeMovedOutsideOwnDomainAtMovement','retrieveAllTheDocumentsThatCannotBeReassignedAtMovement',
                                    'retrieveAllTheBatchesThatCannotBeReassignedAtMovement','retrieveAllTheBoxesThatCannotBeReassignedAtMovement','retrieveNeighbourhoodsThatBatchReassignmentIsRestrictedToAtMovement',
                                    'retrieveNeighbourhoodsThatDocumentReassignmentIsRestrictedToAtMovement','retrieveNeighbourhoodsThatBoxReassignmentIsRestrictedToAtMovement','retrieveClustersThatDocumentReassignmentIsRestrictedToAtMovement',
                                    'retrieveClustersThatBatchReassignmentIsRestrictedToAtMovement','retrieveClustersThatBoxReassignmentIsRestrictedToAtMovement','retrieveNeighbourhoodsWhoseDocumentsCannotBeDestroyedAtDestruction','retrieveClustersWhoseDocumentsCannotBeDestroyedAtDestruction',
                                    'retrieveProcessorsWhoseDocumentsCannotBeDestroyedAtDestruction','retrieveTypesWhoseDocumentsCannotBeDestroyedAtDestruction','retrieveCategoriesWhoseDocumentsCannotBeDestroyedAtDestruction',
                                    'retrieveTypesWhoseElectronicCopyBeRetainedAfterDestructionAtDestruction','retrieveCategoriesWhoseElectronicCopyBeRetainedAfterDestructionAtDestruction','retrieveTypesWhoseDestroyedTypesWillHaveNoGlobalRequestAtDestruction',
                                    'retrieveAllTheDestroyedDocumentsThatCannotBeAccessedByAllAtDestruction','retrieveClustersThatCouldAccessAllDestroyedDocumentAtDestruction','retrieveBatchesThatCanOnlyBeReassignedOrMovedAsAWholeAtMovement',
                                    'retrieveBoxesThatCanOnlyBeReassignedOrMovedAsAWholeAtMovement','retrieveRetainedDestroyedDocumentsAtDestruction','retrieveNeighbourhoodsThatCouldAccessAllDestroyedDocumentAtDestruction',
                                    'AddDocumentsThatCannotBeMovedOnPlatform','AddNeighbourhoodsForRestrictedDocumentMovementsOnPlatform','AddDocumentsThatCannotBeMovedOutsideOwnDomainOnPlatform','AddDocumentsByTheseProcessorsCannotBeMovedOnPlatform',
                                    'AddProcessorsWhoseDocumentsAreMoveableOnlyWithinOwnDomainOnPlatform','AddBatchesThatCannotBeMovedOnPlatform','AddNeighboursForRestrictedBatchMovementOnPlatform','AddBatchesThatCannotBeMovedOutsideOwnDomainOnPlatform',
                                    'AddBoxesThatCannotBeMovedOnPlatform','AddNeighbourhoodsForRestrictedDocumentMovementsOnPlatform','AddBoxesThatCannotBeMovedOutsideOwnDomainsOnPlatform','AddDocumentsThatAreNotReassignableOnPlatform',
                                    'AddBatchesThatAreNotReassignableOnPlatform','AddBoxesThatAreNotReassignableOnPlatform','AddNeighbourhoodsForRestrictedDocumentsReassignmentOnPlatform','AddNeighbourhoodsForRestrictedBatchReassignmentOnPlatform',
                                    'AddNeighbourhoodsForRestrictedBoxReassignmentOnPlatform','AddClustersForRestrictedDocumentsReassignmentOnPlatform','AddNeighboursForRestrictedBatchMovementOnPlatform',
                                    'AddClustersForRestrictedBatchReassignmentOnPlatform','AddClustersForRestrictedBoxReassignmentOnPlatform','AddNeighbourhoodsWhereDocumentsCannotBeDestroyedOnPlatform',
                                    'AddClustersWhereDocumentsCannotBeDestroyedOnPlatform','AddProcessorsWhoseDocumentsCannotBeDestroyedOnPlatform','AddDocumentTypesCannotBeDestroyedOnPlatform','AddDocumentCategoriesCannotBeDestroyedOnPlatform',
                                    'AddDocumentTypesOfElectronicCopyToRetainAfterDestructionOnPlatform','AddDocumentCategoriesOfElectronicCopyToRetainAfterDestructionOnPlatform','AddDocumentTypeWithNoGlobalRequestAfterDestructionOnPlatform','AddDestroyedDocumentsThatCannotBeAccessedOnPlatform',
                                    'AddNeighbourhoodsThatCouldAccessDestroyedDocumentsOnPlatform','AddClustersThatCouldAccessDestroyedDocumentsOnPlatform','AssignDocumentsForDestructionToImplementersOnPlatform','AddBatchesThatMustBeMovedAsAWholeOnPlatform','AddBoxesThatMustBeMovedAsAWholeOnPlatform',
                                    'AddDocumentsThatWillBeRetainedAfterDestructionOnPlatform','AddBatchesThatCannotBeMovedOnDomain','AddNeighbourhoodsForRestrictedBoxMovementsOnPlatform','AddApplicableBlacklistAtExchangeOnPlatform',
                                    'AddApplicableWhitelistAtExchangeOnPlatform','retrieveTheWhitelistsApplicableAtExchange','retrieveTheBlacklistsApplicableAtExchange','retrieveTheBlacklistsApplicableAtSharing','retrieveTheWhitelistsApplicableAtSharing',
                                    'retrieveTheBlacklistsApplicableAtTransfers','retrieveTheWhitelistsApplicableAtTransfers','retrieveTheBlacklistsApplicableAtTradings','retrieveTheWhitelistsApplicableAtTradings','retrieveTheBlacklistsApplicableAtRequest',
                                    'retrieveTheWhitelistsApplicableAtRequest','retrieveTheBlacklistsApplicableAtConsumption','retrieveTheWhitelistsApplicableAtConsumption','retrieveTheBlacklistsApplicableAtMovement','retrieveTheWhitelistsApplicableAtMovement',
                                    'retrieveTheBlacklistsApplicableAtDestruction','retrieveTheWhitelistsApplicableAtDestruction','AddApplicableBlacklistAtSharingOnPlatform','AddApplicableWhitelistAtSharingOnPlatform','AddApplicableBlacklistsAtTransfersOnPlatform',
                                    'AddApplicableWhitelistsAtTransfersOnPlatform','AddApplicableBlacklistAtTradingOnPlatform','AddApplicableWhitelistAtTradingOnPlatform','AddApplicableBlacklistAtRequestOnPlatform','AddApplicableWhitelistAtRequestOnPlatform','AddApplicableBlacklistAtConsumptionOnPlatform',
                                    'AddApplicableWhitelistAtConsumptionOnPlatform','AddApplicableBlacklistAtMovementOnPlatform','AddApplicableWhitelistAtMovementOnPlatform','AddApplicableBlacklistAtDestructionOnPlatform',
                                    'AddApplicableWhitelistAtDestructionOnPlatform','retrieveTheLifespanOfThisDocumentType','AssignThisLifespanToThisDocumentTypeOnPlatform','AssignThisLifespanToThisDocumentTypeOnPlatform','RemoveLifespanFromDocumentType',
                                    'AssignThisLifespanToThisDocumentCategoryOnPlatform','retrieveTheLifespanOfThisDocumentCategory','RemoveLifespanFromDocumentCategory','DeleteThisPlatformsetting','gettheapplicabledeliveryamount'),
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
         * This is the function that will add a platform settings to a database
         */
        public function actionAddNewPlatformSettings(){
            
            $model=new PlatformSettings;
            
            
            //get the logged in user id
            $userid = Yii::app()->user->id;
            
                              
            $model->icon_height = $_POST['icon_height'];
            $model->icon_width = $_POST['icon_width'];
            $model->poster_height = $_POST['poster_height'];
            $model->poster_width = $_POST['poster_width'];
            $model->platform_default_currency_id = $_POST['platform_default_currency'];
            $model->platform_default_time_zone_id = $_POST['platform_default_time_zone'];
            if(isset($_POST['include_management_fees'])){
                $model->include_management_fees = $_POST['include_management_fees'];
            }else{
                $model->include_management_fees = 0;
            }
            if(isset($_POST['include_handling_charges'])){
                $model->include_handling_charges = $_POST['include_handling_charges'];
            }else{
                $model->include_handling_charges = 0;
            }
            if(isset($_POST['include_shipping_charges'])){
                $model->include_shipping_charges = $_POST['include_shipping_charges'];
            }else{
                $model->include_shipping_charges = 0;
            }
             if(isset($_POST['effect_discount_for_subscription'])){
                $model->effect_discount_for_subscription = $_POST['effect_discount_for_subscription'];
            }else{
                $model->effect_discount_for_subscription = 0;
            }
            if(isset($_POST['discount_rate'])){
                $model->discount_rate = $_POST['discount_rate'];
            }
            if(isset($_POST['min_years_required_for_discount'])){
                $model->min_years_required_for_discount = $_POST['min_years_required_for_discount'];
            }
            if(isset($_POST['monthly_discount_rate'])){
                $model->monthly_discount_rate = $_POST['monthly_discount_rate'];
            }
            if(isset($_POST['min_months_required_for_discount'])){
                $model->min_months_required_for_discount = $_POST['min_months_required_for_discount'];
            }
             if(isset($_POST['top_priority_delivery_in_percentage'])){
                $model->top_priority_delivery_in_percentage = $_POST['top_priority_delivery_in_percentage'];
            }
            if(isset($_POST['priority_delivery_in_percentage'])){
                $model->priority_delivery_in_percentage = $_POST['priority_delivery_in_percentage'];
            }
             if(isset($_POST['standard_delivery_in_percentage'])){
                $model->standard_delivery_in_percentage = $_POST['standard_delivery_in_percentage'];
            }
             if(isset($_POST['minimum_top_priority_delivery_amount'])){
                $model->minimum_top_priority_delivery_amount = $_POST['minimum_top_priority_delivery_amount'];
            }
             if(isset($_POST['minimum_priority_delivery_amount'])){
                $model->minimum_priority_delivery_amount = $_POST['minimum_priority_delivery_amount'];
            }
              if(isset($_POST['minimum_standard_delivery_amount'])){
                $model->minimum_standard_delivery_amount = $_POST['minimum_standard_delivery_amount'];
            }
            $model->managemenr_fee_in_percetanges = $_POST['managemenr_fee_in_percetanges'];
            $model->escrow_minimum_amount = $_POST['escrow_minimum_amount'];
            $model->escrow_rate_in_percentages = $_POST['escrow_rate_in_percentages'];
            $model->maximum_allowable_cash_transaction = $_POST['maximum_allowable_cash_transaction'];
            $model->business_subscription_daily_quotation_limit = $_POST['business_subscription_daily_quotation_limit'];
            $model->business_prime_subscription_daily_quotation_limit = $_POST['business_prime_subscription_daily_quotation_limit'];
            $model->basic_prime_subscription_daily_quotation_limit = $_POST['basic_prime_subscription_daily_quotation_limit'];
            $model->business_subscription_monthly_quotation_limit = $_POST['business_subscription_monthly_quotation_limit'];
            $model->business_prime_subscription_monthly_quotation_limit = $_POST['business_prime_subscription_monthly_quotation_limit'];
            $model->basic_prime_subscription_monthly_quotation_limit = $_POST['basic_prime_subscription_monthly_quotation_limit'];
            $model->product_maximum_video_size = $_POST['product_maximum_video_size'];
            $model->product_code_pad_length = $_POST['product_code_pad_length'];
            $model->handling_charges_in_percetanges = $_POST['handling_charges_in_percetanges'];
            $model->shipping_charges_in_percetanges = $_POST['shipping_charges_in_percetanges'];
            $model->create_user_id = $userid;
            $model->create_time = new CDbExpression('NOW()');
                      
           
             if($model->save()){
                        if($this->createorUpdateTheVariousMimeTypes($model->id)){
                            $msg = "Platform Settings were successfully added";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                            );
                        }else{
                            $msg = "All except some of the Mime Type Platform Settings were successfully added";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                            ); 
                        }
                           
                           
                       
                        
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Validation Error: Platform Settings not added";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
            
        }
        
        
        /**
         * This is the function that updates platform settings information
         */
        public function actionUpdatePlatformSettings(){
            
            $_id = $_POST['id'];
            $model=PlatformSettings::model()->findByPk($_id);
            
             //get the logged in user id
            $userid = Yii::app()->user->id;
            
             $model->icon_height = $_POST['icon_height'];
            $model->icon_width = $_POST['icon_width'];
            $model->poster_height = $_POST['poster_height'];
            $model->poster_width = $_POST['poster_width'];
            if(is_numeric($_POST['platform_default_currency'])){
               $model->platform_default_currency_id = $_POST['platform_default_currency']; 
            }else{
                $model->platform_default_currency_id = $_POST['platform_default_currency_id'];
            }
            if(is_numeric($_POST['platform_default_time_zone'])){
                $model->platform_default_time_zone_id = $_POST['platform_default_time_zone']; 
            }else{
                 $model->platform_default_time_zone_id = $_POST['platform_default_time_zone_id']; 
            }
           
            if(isset($_POST['include_management_fees'])){
                $model->include_management_fees = $_POST['include_management_fees'];
            }else{
                $model->include_management_fees = 0;
            }
            if(isset($_POST['include_handling_charges'])){
                $model->include_handling_charges = $_POST['include_handling_charges'];
            }else{
                $model->include_handling_charges = 0;
            }
            if(isset($_POST['include_shipping_charges'])){
                $model->include_shipping_charges = $_POST['include_shipping_charges'];
            }else{
                $model->include_shipping_charges = 0;
            }
             if(isset($_POST['effect_discount_for_subscription'])){
                $model->effect_discount_for_subscription = $_POST['effect_discount_for_subscription'];
            }else{
                $model->effect_discount_for_subscription = 0;
            }
            if(isset($_POST['discount_rate'])){
                $model->discount_rate = $_POST['discount_rate'];
            }
            if(isset($_POST['min_years_required_for_discount'])){
                $model->min_years_required_for_discount = $_POST['min_years_required_for_discount'];
            }
            if(isset($_POST['monthly_discount_rate'])){
                $model->monthly_discount_rate = $_POST['monthly_discount_rate'];
            }
            if(isset($_POST['min_months_required_for_discount'])){
                $model->min_months_required_for_discount = $_POST['min_months_required_for_discount'];
            }
             if(isset($_POST['top_priority_delivery_in_percentage'])){
                $model->top_priority_delivery_in_percentage = $_POST['top_priority_delivery_in_percentage'];
            }
            if(isset($_POST['priority_delivery_in_percentage'])){
                $model->priority_delivery_in_percentage = $_POST['priority_delivery_in_percentage'];
            }
             if(isset($_POST['standard_delivery_in_percentage'])){
                $model->standard_delivery_in_percentage = $_POST['standard_delivery_in_percentage'];
            }
             if(isset($_POST['minimum_top_priority_delivery_amount'])){
                $model->minimum_top_priority_delivery_amount = $_POST['minimum_top_priority_delivery_amount'];
            }
             if(isset($_POST['minimum_priority_delivery_amount'])){
                $model->minimum_priority_delivery_amount = $_POST['minimum_priority_delivery_amount'];
            }
              if(isset($_POST['minimum_standard_delivery_amount'])){
                $model->minimum_standard_delivery_amount = $_POST['minimum_standard_delivery_amount'];
            }
            $model->managemenr_fee_in_percetanges = $_POST['managemenr_fee_in_percetanges'];
            $model->escrow_minimum_amount = $_POST['escrow_minimum_amount'];
            $model->escrow_rate_in_percentages = $_POST['escrow_rate_in_percentages'];
            $model->maximum_allowable_cash_transaction = $_POST['maximum_allowable_cash_transaction'];
            $model->business_subscription_daily_quotation_limit = $_POST['business_subscription_daily_quotation_limit'];
            $model->business_prime_subscription_daily_quotation_limit = $_POST['business_prime_subscription_daily_quotation_limit'];
            $model->basic_prime_subscription_daily_quotation_limit = $_POST['basic_prime_subscription_daily_quotation_limit'];
            $model->business_subscription_monthly_quotation_limit = $_POST['business_subscription_monthly_quotation_limit'];
            $model->business_prime_subscription_monthly_quotation_limit = $_POST['business_prime_subscription_monthly_quotation_limit'];
            $model->basic_prime_subscription_monthly_quotation_limit = $_POST['basic_prime_subscription_monthly_quotation_limit'];
            $model->product_maximum_video_size = $_POST['product_maximum_video_size'];
            $model->product_code_pad_length = $_POST['product_code_pad_length'];
            $model->handling_charges_in_percetanges = $_POST['handling_charges_in_percetanges'];
            $model->shipping_charges_in_percetanges = $_POST['shipping_charges_in_percetanges'];
            $model->update_user_id = $userid;
            $model->update_time = new CDbExpression('NOW()');
                      
           
             if($model->save()){
                            if($this->createorUpdateTheVariousMimeTypes($_id)){
                                $msg = "Platform Settings were successfully updated";
                                header('Content-Type: application/json');
                                echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                                ); 
                            }else{
                               $msg = "All except some of the Mime Type Platform Settings were successfully added";
                               header('Content-Type: application/json');
                               echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg)
                                ); 
                            }
                           
                           
                       
                        
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Validation Error: Platform Settings not updated";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg)
                           );
                  } 
            
        }
        
        /**
         * This is the function that creates the mimetypes during platform parameter createion or updates
         */
        public function createOrUpdateTheVariousMimeTypes($id){
            
            if(($this->createOrUpdateIconMimeTypes($id) && $this->createOrUpdatePosterMimeTypes($id)) ){
                return true;
            }else{
                return false;
            }
            
        }
        
        /**
         * This is the function that creates or updates an icon  mime type in the system
         */
        public function createOrUpdateIconMimeTypes($id){
            
           if(isset($_POST['icon_mime_type'])){
                 
                if(is_array($_POST['icon_mime_type'])) {
                  
                    $num = 0;
                    foreach($_POST['icon_mime_type'] as $value){
                        if($value == 'image/png' ) {
                            $num = $num + 1;
                            $this->saveThisIconMimeType($id,$num);
                        }else if($value == 'image/jpg'){
                            $num = $num + 2;
                            $this->saveThisIconMimeType($id,$num);
                        }elseif($value == 'image/jpeg'){
                            $num = $num + 4;
                            $this->saveThisIconMimeType($id,$num);
                        }elseif($value == 'image/gif'){
                            $num = $num + 8;
                            $this->saveThisIconMimeType($id,$num);
                        }elseif($value == 'application/x-shockwave-flash'){
                            $num = $num + 16;
                            $this->saveThisIconMimeType($id,$num);
                        }elseif($value == 'image/psd'){
                            $num = $num + 32;
                            $this->saveThisIconMimeType($id,$num);
                        }elseif($value == 'image/bmp'){
                            $num = $num + 64;
                            $this->saveThisIconMimeType($id,$num);
                        }elseif($value == 'image/tiff'){
                            $num = $num + 128;
                            $this->saveThisIconMimeType($id,$num);
                        }elseif($value == 'application/octet-stream'){
                            $num = $num + 256;
                            $this->saveThisIconMimeType($id,$num);
                        }elseif($value == 'image/jp2'){
                            $num = $num + 512;
                            $this->saveThisIconMimeType($id,$num);
                        }elseif($value == 'image/vnd.wap.wbmp'){
                            $num = $num + 1024;
                            $this->saveThisIconMimeType($id,$num);
                        }elseif($value == 'image/xbm'){
                            $num = $num + 2048;
                            $this->saveThisIconMimeType($id,$num);
                        }elseif($value == 'image/vnd.microsoft.icon'){
                            $num = $num + 4096;
                            $this->saveThisIconMimeType($id,$num);
                        }
                        
                    }
                    
                }//end of the if is_array statement
                 
             }//end of the icon isset if statement
            
            return true;
        }
        
        
        /**
         * This is the function that creates or updates a poster mime type in the system
         * 
         */
        public function createOrUpdatePosterMimeTypes($id){
            
            if(isset($_POST['poster_mime_type'])){
                 
                if(is_array($_POST['poster_mime_type'])) {
                  
                    $num = 0;
                    foreach($_POST['poster_mime_type'] as $value){
                        if($value == 'image/png' ) {
                            $num = $num + 1;
                            $this->saveThisPosterMimeType($id,$num);
                        }else if($value == 'image/jpg'){
                            $num = $num + 2;
                            $this->saveThisPosterMimeType($id,$num);
                        }elseif($value == 'image/jpeg'){
                            $num = $num + 4;
                            $this->saveThisPosterMimeType($id,$num);
                        }elseif($value == 'image/gif'){
                            $num = $num + 8;
                            $this->saveThisPosterMimeType($id,$num);
                        }elseif($value == 'application/x-shockwave-flash'){
                            $num = $num + 16;
                            $this->saveThisPosterMimeType($id,$num);
                        }elseif($value == 'image/psd'){
                            $num = $num + 32;
                            $this->saveThisPosterMimeType($id,$num);
                        }elseif($value == 'image/bmp'){
                            $num = $num + 64;
                            $this->saveThisPosterMimeType($id,$num);
                        }elseif($value == 'image/tiff'){
                            $num = $num + 128;
                            $this->saveThisPosterMimeType($id,$num);
                        }elseif($value == 'application/octet-stream'){
                            $num = $num + 256;
                            $this->saveThisPosterMimeType($id,$num);
                        }elseif($value == 'image/jp2'){
                            $num = $num + 512;
                            $this->saveThisPosterMimeType($id,$num);
                        }elseif($value == 'image/vnd.wap.wbmp'){
                            $num = $num + 1024;
                            $this->saveThisPosterMimeType($id,$num);
                        }elseif($value == 'image/xbm'){
                            $num = $num + 2048;
                            $this->saveThisPosterMimeType($id,$num);
                        }elseif($value == 'image/vnd.microsoft.icon'){
                            $num = $num + 4096;
                            $this->saveThisPosterMimeType($id,$num);
                        }
                        
                    }
                    
                }//end of the if is_array statement
                 
             }//end of the poster isset if statement
            
            return true;
            
            
        }
        
        
               
        /**
         * This is the function that creates or updates a zip mime type in the system
         */
        public function createOrUpdateZipMimeTypes($id){
            
            if(isset($_POST['zip_file_type'])){
                 
                if(is_array($_POST['zip_file_type'])) {
                  
                    $num = 0;
                    foreach($_POST['zip_file_type'] as $value){
                        if($value == 'application/zip' ) {
                            $num = $num + 1;
                            $this->saveThisZipMimeType($id,$num);
                        }else if($value == 'application/x-zip-compressed'){
                            $num = $num + 2;
                            $this->saveThisZipMimeType($id,$num);
                        }elseif($value == 'multipart/x-zip'){
                            $num = $num + 4;
                            $this->saveThisZipMimeType($id,$num);
                        }elseif($value == 'application/x-compressed'){
                            $num = $num + 8;
                            $this->saveThisZipMimeType($id,$num);
                        }
                    }
                    
                }//end of the if is_array statement
                 
             }//end of the zip isset if statement
            
            return true;
            
        }
        
        /**
         * This is the function to save an icon file type
         */
        public function saveThisIconMimeType($id,$num){
            $cmd =Yii::app()->db->createCommand();
            $result = $cmd->update('platform_settings',
                         array('icon_mime_type'=>'icon_mime_type'|$num),
                           "id = $id"
                     );
            
            if($result > 0){
                return true;
            }else{
                return false;
            }
            
            
        }
        
        /**
         * This is the function to save a poster file type
         */
        public function saveThisPosterMimeType($id,$num){
            
            $cmd =Yii::app()->db->createCommand();
            $result = $cmd->update('platform_settings',
                         array('poster_mime_type'=>'poster_mime_type'|$num),
                           "id = $id"
                     );
            
            if($result > 0){
                return true;
            }else{
                return false;
            }
              
        }
        
        
              
	/**
         * This is the function that will delete one platform settings from the database
         */
        public function actionDeleteThisPlatformsetting(){
            $_id = $_POST['id'];
            $model=PlatformSettings::model()->findByPk($_id);
            if($model === null){
                 $msg = 'No Such Record Exist'; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                            //"category" =>$category,
                           
                           
                          
                       ));
                                      
            }else if($model->delete()){
                    $msg = 'This Platform Settings had been deleted successfully'; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                            //"category" =>$category,
                           
                           
                          
                       ));
                       
            } else {
                    $msg = 'Validation Error: This Platform Settings were not deleted'; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() != 0,
                            //"selected" => $selected,
                            "msg" => $msg
                            //"category" =>$category,
                           
                           
                          
                       ));
                            
                }
            
        }
        
        /**
         * This is the function to list all platform settings
         */
        public function actionListAllPlatformsettingParamters(){
            
              //obtain the id of the logged in user
            $userid = Yii::app()->user->id;
            
            //determine the domain of the logged in user
           // $domainid = $this->determineAUserDomainIdGiven($userid);
            
           //spool the products/technologies for this domain
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            //$criteria->condition='domain_id=:id';
            //$criteria->params = array(':id'=>$domainid);
            $settings= PlatformSettings::model()->findAll($criteria);
            
                if($settings===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "settings" => $settings
                          
                           
                           
                          
                       ));
                       
                }
        }
        
        /**
         * This is the function to retrieve some info about the platform settings
         */
        public function actionRetrieveSettingsInfo(){
            
            $id = $_REQUEST['id'];
            $currency_id = $_REQUEST['platform_default_currency_id'];
           $timezone_id = $_REQUEST['platform_default_time_zone_id'];
            
           //$id = 2 ;
           //$currency_id = 1;
           //$timezone_id = 1;
            
            //get the currency name given the currency id
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$currency_id);
            $currency = Currencies::model()->find($criteria); 
            
            //get the timezone given the timezone id
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$timezone_id);
            $timezone = Timezones::model()->find($criteria); 
            
            //retrieve the icon mime types
            $icon_mimetype = [];
            $icon_types = [];
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$id);
            $icon_mime = PlatformSettings::model()->find($criteria); 
            
            $icon_mimetype = explode(',',$icon_mime['icon_mime_type']);
            foreach($icon_mimetype as $icon){
                $icon_types[] =$icon; 
                
            }
            
            //retrieve the poster mime types
            $poster_mimetype = [];
            $poster_types = [];
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$id);
            $poster_mime = PlatformSettings::model()->find($criteria); 
            
            $poster_mimetype = explode(',',$poster_mime['poster_mime_type']);
            foreach($poster_mimetype as $poster){
                $poster_types[] =$poster; 
                
            }
           
                     
             if($id===null) {
                    http_response_code(404);
                    $msg ='No record found';
                   header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg" =>$msg
                       ));
                       
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "currency" =>$currency['currency_name'],
                           "timezone" =>$timezone['timezone'],
                           "icon"=>$icon_types,
                           "poster"=>$poster_types,
                          
                       ));
                       
                } 
            
            
            
        }
        
        
        /**
         * This is the function that retrieves delivery charges
         */
        public function actiongettheapplicabledeliveryamount(){
            
            $model = new PlatformSettings;
            
            $member_id = Yii::app()->user->id;
            
                                 
            //get the open order of this member
            
            $order_id = $this->getTheOpenOrderInitiatedByMember($member_id);
            
            //confirm if the order contains only exclusive transactions like hampers and quoted transactions
            
            $is_exclusive = $this->isOrderWithOnlyExclusiveProducts($order_id);
            //get the city of delivery of this order
            //$city_of_delivery = $this->getThisOrderCityOfDelivery($order_id);
            $city_of_delivery = $_REQUEST['city_id'];
            
            $payment_method = $_REQUEST['payment_method'];
            
            $delivery_type = $_REQUEST['delivery_type'];
            if($city_of_delivery != 0){
                //get the top priority delivery charges for this order
            $top_priority_delivery_charge = $this->getTheCostOfOrderDeliveryToThisCityForTopPriority($order_id,$city_of_delivery);
            
             //get the priority delivery charges for this order
            $priority_delivery_charge = $this->getTheCostOfOrderDeliveryToThisCityForPriority($order_id,$city_of_delivery);
            
              //get the standard delivery charges for this order
            $standard_delivery_charge = $this->getTheCostOfOrderDeliveryToThisCityForStandard($order_id,$city_of_delivery);
            
            
            //get the delivery cost of products in the exclusive delivery cost list
            $delivery_cost_for_exclusive_products = $this->getTheCostOfDeliveryForExclusiveProductsInAnOrder($order_id);
            
            //get the escrow charges of an order
            $escrow_charges = $this->getTheEscrowChargesOfThisOrder($order_id);
            
            //get the cost of delivery to this city on payment on delivery service
           $payable_on_delivery_cost_of_service = $this->getTheOrderDeliveryCostToThisCityOnPaymentOnDeliveryService($order_id,$city_of_delivery,$payment_method,$delivery_type);
           
           //get the cost of delivery to this city on payment from wallet or online payment service
           $delivery_cost_on_wallet_or_online_payment = $this->getTheOrderDeliveryCostToThisCityOnPaymentByWalletOrOnlineService($order_id,$city_of_delivery,$payment_method,$delivery_type);
            
            
            
             header('Content-Type: application/json');
                       echo CJSON::encode(array(
                           "success" => mysql_errno() == 0,
                           "top_priority" =>$top_priority_delivery_charge,
                           "priority"=>$priority_delivery_charge,
                           "standard"=>$standard_delivery_charge,
                           "exclusives"=>$delivery_cost_for_exclusive_products,
                           "escrow"=>$escrow_charges,
                           "is_exclusive_only"=>$is_exclusive,
                           "payable_on_delivery_cost_of_service"=>$payable_on_delivery_cost_of_service,
                           "delivery_cost_on_wallet_or_online_payment"=>$delivery_cost_on_wallet_or_online_payment,
                  
                       ));
            
                
            }
            
        }
        
        
        /**
         * This is the function that will obtain the delivery cost from products payables from non-ondelivery settlements
         */
        public function getTheOrderDeliveryCostToThisCityOnPaymentByWalletOrOnlineService($order_id,$city_of_delivery,$payment_method,$delivery_type){
            $model = new OrderHasProducts;
            return $model->getTheOrderDeliveryCostToThisCityOnPaymentByWalletOrOnlineService($order_id,$city_of_delivery,$payment_method,$delivery_type);
        }
        
        
        
        /**
         * This is the function that will obtain the delivery cost on product payables on delivery
         */
        public function getTheOrderDeliveryCostToThisCityOnPaymentOnDeliveryService($order_id,$city_of_delivery,$payment_method,$delivery_type){
            $model = new OrderHasProducts;
            return $model->getTheOrderDeliveryCostToThisCityOnPaymentOnDeliveryService($order_id,$city_of_delivery,$payment_method,$delivery_type);
        }
        
        /**
         * This is the function that confirms if an order contains only exclusive transactions like hampers and quotes
         */
        public function isOrderWithOnlyExclusiveProducts($order_id){
            $model = new OrderHasProducts;
            return $model->isOrderWithOnlyExclusiveProducts($order_id);
        }
        
        
        /**
         * This is the function that gets the open order of a member
         */
        public function getTheOpenOrderInitiatedByMember($member_id){
            $model = new Order;
            return $model->getTheOpenOrderInitiatedByMember($member_id);
        }
        
        
        /**
         * This is the function that gets an order city of delivery
         */
        public function getThisOrderCityOfDelivery($order_id){
            $model = new Order;
            return $model->getThisOrderCityOfDelivery($order_id);
        }
        
        
        /**
         * This is the function that gets the top priority delivery charges for an order to a city
         */
        public function getTheCostOfOrderDeliveryToThisCityForTopPriority($order_id,$city_of_delivery){
            $model = new City;
            return $model->getTheCostOfOrderDeliveryToThisCityForTopPriority($order_id,$city_of_delivery);
        }
        
         /**
         * This is the function that gets the priority delivery charges for an order to a city
         */
        public function getTheCostOfOrderDeliveryToThisCityForPriority($order_id,$city_of_delivery){
            $model = new City;
            return $model->getTheCostOfOrderDeliveryToThisCityForPriority($order_id,$city_of_delivery);
        }
        
        
        /**
         * This is the function that gets the standard delivery charges for an order to a city
         */
        public function getTheCostOfOrderDeliveryToThisCityForStandard($order_id,$city_of_delivery){
            $model = new City;
            return $model->getTheCostOfOrderDeliveryToThisCityForStandard($order_id,$city_of_delivery);
        }
        
        
        /**
         * This is the function that gets the escrow charges of an order
         */
        public function getTheEscrowChargesOfThisOrder($order_id){
            $model = new OrderHasProducts;
            return $model->getTheEscrowChargesOfThisOrder($order_id);
        }
        
        
        /**
         * This is the function that gets the cost of a exclusive products in an order
         */
        public function getTheCostOfDeliveryForExclusiveProductsInAnOrder($order_id){
            $model = new OrderHasProducts;
            return $model->getTheCostOfDeliveryForExclusiveProductsInAnOrder($order_id);
        }
        
        
       
     
}
