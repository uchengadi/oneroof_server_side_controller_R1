<?php

class StoresController extends Controller
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
				'actions'=>array('index','view','ListAllToolboxesInThisCountryStore','retrieveTheExchangeRateForThisStore'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','AddNewStoreParameters','UpdateStoreParameters',
                                    'ListAllStoresInfo','DeleteOneStore','RetrieveNeededStoreParameters','ListAllToolboxesInThisDomainNetworkStore',
                                    'ListAllToolboxesInThisDomainStore','getNetworkToolboxes','isToolboxInComplianceWithDomainPolicy',
                                    'determineDomainStorePolicyOnDuplicateRestrictedPublicToolboxes','ListAllToolboxesInThisCountryStore',
                                    'getTheCurrencyExchangeRate','getStoreAndCurrencyParameters','ListThisPartnerOrNetworkToolboxes',
                                    'ListAllPossibleDomainLegitimateToolboxesForPurchase'),
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
         * This is the function to create new store and its parameter settings
         */
        public function actionAddNewStoreParameters(){
            
             $model=new Stores;
            
            //get the logged in user id
            $userid = Yii::app()->user->id;
            
            //determine the domain of the logged in user
            $domainid = $this->determineAUserDomainIdGiven($userid);
                    
            $model->timezone_id = $_POST['timezone'];
            $model->country_id = $_POST['country'];
            $model->currency_id = $_POST['currency'];
            $model->type = strtolower($_POST['type']);
            if(isset($_POST['default_store'])){
                $model->default_store = $_POST['default_store'];
            }else{
                $model->default_store = 0;
            }
            $model->domain_id = $domainid;
            if(isset($_POST['make_restricted_public_toolboxes_available'])){
                $model->make_restricted_public_toolboxes_available = $_POST['make_restricted_public_toolboxes_available'];
            }else{
                $model->make_restricted_public_toolboxes_available = 0;
            }
            if(isset($_POST['make_public_toolboxes_available'])){
                $model->make_public_toolboxes_available = $_POST['make_public_toolboxes_available'];
            }else{
                $model->make_public_toolboxes_available = 0;
            }
            if(isset($_POST['make_private_toolboxes_available'])){
                $model->make_private_toolboxes_available = $_POST['make_private_toolboxes_available'];
            }else{
                $model->make_private_toolboxes_available = 0;
            }
            $model->create_user_id = $userid;
            $model->create_time = new CDbExpression('NOW()');
                      
           
             if($model->save()){
                      
                            $msg = "Store was Successfully added";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg
                                        
                                
                            ));
                           
                       
                        
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Validation Error: Store not Added";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg
                                        
                           ));
                  } 
            
            
        }
        
        
        /**
         * This is the function to update store info
         */
        public function actionUpdateStoreParameters(){
            
             $_id = $_POST['id'];
            $model=Stores::model()->findByPk($_id);
            
             //get the logged in user id
            $userid = Yii::app()->user->id;
            
             //determine the domain of the logged in user
            //$domainid = $this->determineAUserDomainIdGiven($userid);
            
            //get the domain_id of this store
            
            $domainid = $this->getTheDomainIdOfThisStore($_id);
            
            if(is_numeric($_POST['timezone'])){
                $model->timezone_id = $_POST['timezone'];
            }else{
                $model->timezone_id = $_POST['timezone_id'];
            }
            if(is_numeric($_POST['country'])){
                $model->country_id = $_POST['country'];
            }else{
                $model->country_id = $_POST['country_id'];
            }
            if(is_numeric($_POST['currency'])){
                $model->currency_id = $_POST['currency'];
            }else{
                $model->currency_id = $_POST['currency_id'];
            }
             if(isset($_POST['default_store'])){
                $model->default_store = $_POST['default_store'];
            }else{
                $model->default_store = 0;
            }
            $model->type = strtolower($_POST['type']);
            $model->domain_id = $domainid;
            if(isset($_POST['make_restricted_public_toolboxes_available'])){
                $model->make_restricted_public_toolboxes_available = $_POST['make_restricted_public_toolboxes_available'];
            }else{
                $model->make_restricted_public_toolboxes_available = 0;
            }
            if(isset($_POST['make_public_toolboxes_available'])){
                $model->make_public_toolboxes_available = $_POST['make_public_toolboxes_available'];
            }else{
                $model->make_public_toolboxes_available = 0;
            }
            if(isset($_POST['make_private_toolboxes_available'])){
                $model->make_private_toolboxes_available = $_POST['make_private_toolboxes_available'];
            }else{
                $model->make_private_toolboxes_available = 0;
            }
            $model->update_user_id = $userid;
            $model->update_time = new CDbExpression('NOW()');
                      
           
             if($model->save()){
                      
                            $msg = "Store was Successfully updated";
                            header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                    "success" => mysql_errno() == 0,
                                        "msg" => $msg
                                        
                                
                            ));
                           
                       
                        
                 }else {
                         //$result['success'] = 'false';
                         $msg = "Validation Error: Store not Updated";
                         header('Content-Type: application/json');
                         echo CJSON::encode(array(
                                    "success" => mysql_errno() != 0,
                                        "msg" => $msg
                                        
                           ));
                  } 
            
            
            
        }
        
        
        /**
         * This is the function that retrieves the domain id of a store
         */
        public function getTheDomainIdOfThisStore($id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$id);
            $store = Stores::model()->find($criteria); 
            
            return $store['domain_id'];
            
            
        }
        
      
        
	/**
         * This is the function to list stores
         */
        public function actionListAllStoresInfo(){
            
              //obtain the id of the logged in user
            $userid = Yii::app()->user->id;
            
            //determine the domain of the logged in user
           $domainid = $this->determineAUserDomainIdGiven($userid);
            if($this->determineIfAUserHasThisPrivilegeAssigned($userid, "platformAdmin") || $this->determineIfAUserHasThisPrivilegeAssigned($userid, "platformStoreSupport")){
               //spool the products/technologies for this domain
                $criteria1 = new CDbCriteria();
                $criteria1->select = '*';
                //$criteria->condition='domain_id=:id';
                //$criteria->params = array(':id'=>$domainid);
                $store= Stores::model()->findAll($criteria1);
            
                if($store===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "store" => $store
                          
                           
                           
                          
                       ));
                       
                } 
                
            }else{
                //spool the products/technologies for this domain
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='domain_id=:id';
                $criteria->params = array(':id'=>$domainid);
                $store= Stores::model()->findAll($criteria);
            
                if($store===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "store" => $store
                          
                           
                           
                          
                       ));
                       
                }
            } 
           
            
        }
        
        /**
         * This is the function to delete one store parameters
         */
        public function actionDeleteOneStore(){
            
            $_id = $_POST['id'];
            $model=Stores::model()->findByPk($_id);
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
                    $msg = 'This Store parameters had been deleted successfully'; 
                    header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"selected" => $selected,
                            "msg" => $msg
                            //"category" =>$category,
                           
                           
                          
                       ));
                       
            } else {
                    $msg = 'Validation Error: This Store was not deleted'; 
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
         * This is the function that will retrieve some needed parameters for store
         */
        public function actionRetrieveNeededStoreParameters(){
            
           $country_id = $_REQUEST['country_id'];
           $currency_id = $_REQUEST['currency_id'];
           $timezone_id = $_REQUEST['timezone_id'];
            
           //$id = 7 ;
           // $currency_id = 3;
           // $timezone_id = 1;
            
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
            
            
            //get the country given the country id
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$country_id);
            $country = Country::model()->find($criteria); 
            
           
            
             if($country_id===null) {
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
                           "country"=>$country['name']
                           
                       ));
                       
                } 
            
            
        }
        
        /**
         * Determine a domain id of a user given his user id 
         */
        public function determineAUserDomainIdGiven_old($userid){
            
            //determine the usertype id of the user
            $typeid = $this->determineAUserUsertypeId($userid);
            //determine the usertype name of this usertypeid
            $typename = $this->determineUserTypeName($typeid);
            
            //determine the domain id given usertype name
            $domainid = $this->determineDomainIdGiveUsertypeName($typename);
            
            //determine the domain name given its id
            $name = $this->determineDomainNameGivenItId($domainid);
            //determine the domain id given its name
            $domainname = $this->determineDomainIdGivenItsName($name);
            
            return $domainname;
            
            
        }
        
        
         /**
         * Determine a domain id of a user given his user id 
         */
        public function determineAUserDomainIdGiven($userid){
            
            $criteria1 = new CDbCriteria();
            $criteria1->select = '*';
            $criteria1->condition='id=:id';
            $criteria1->params = array(':id'=>$userid);
            $user= User::model()->find($criteria1);
            
            return $user['domain_id'];
        }
        
        
              
        /**
         * this is the function that retrieves the grouptype id given domain name
         */
        public function determineGrouptypeIdGivenDomainName($domainname){
            
            $criteria = new CDbCriteria();
            $criteria->select = 'id, name';
            $criteria->condition='name=:name';
            $criteria->params = array(':name'=>$domainname);
            $grouptypeid= GroupType::model()->find($criteria);
            
            return $grouptypeid['id'];
            
            
        }
           
        /**
         * Determine a users usertype_id
         */
        public function determineAUserUsertypeId($userid){
            
            $criteria = new CDbCriteria();
            $criteria->select = 'id, usertype_id';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$userid);
            $usertype= User::model()->find($criteria);
            
            return $usertype['usertype_id'];
            
            
        }
        
        /*
         * Determine a usertype name given its id
         */
        public function determineUserTypeName($usertypeid){
            $criteria1 = new CDbCriteria();
            $criteria1->select = 'id, name';
            $criteria1->condition='id=:id';
            $criteria1->params = array(':id'=>$usertypeid);
            $name= UserType::model()->find($criteria1);
            
            return $name['name'];
            
        }
        
        /*
         *  determine a usertype id given its name
         */
        public function determineUsertypeNameGiveId($usertypename){
            $criteria1 = new CDbCriteria();
            $criteria1->select = 'id, name';
            $criteria1->condition='name=:name';
            $criteria1->params = array(':name'=>$usertypename);
            $id= UserType::model()->find($criteria1);
            
            return $id['id'];
        }
        
        /*
         * Determine a domain name given a usetypername
         */
        public function determineDomainNameGiveUsertypeName($usertypename){
            
            $criteria1 = new CDbCriteria();
            $criteria1->select = 'id, name';
            $criteria1->condition='name=:name';
            $criteria1->params = array(':name'=>$usertypename);
            $name= ResourcegroupCategory::model()->find($criteria1);
            
            return $name['name'];
            
        }
        
        /*
         * Determine a domain id given a usetypername
         */
        public function determineDomainIdGiveUsertypeName($usertypename){
            
            $criteria1 = new CDbCriteria();
            $criteria1->select = 'id, name';
            $criteria1->condition='name=:name';
            $criteria1->params = array(':name'=>$usertypename);
            $id= ResourcegroupCategory::model()->find($criteria1);
            
            return $id['id'];
            
        }
        
        
        /**
         * Determine a domain id given its name
         */
        public function determineDomainIdGivenItsName($name){
            $criteria1 = new CDbCriteria();
            $criteria1->select = 'id, name';
            $criteria1->condition='name=:name';
            $criteria1->params = array(':name'=>$name);
            $id= ResourcegroupCategory::model()->find($criteria1);
            
            return $id['id'];
            
        }
        
        /**
         * Determine a domain name given its id
         */
        public function determineDomainNameGivenItId($domainid){
            $criteria1 = new CDbCriteria();
            $criteria1->select = 'id, name';
            $criteria1->condition='id=:id';
            $criteria1->params = array(':id'=>$domainid);
            $name= ResourcegroupCategory::model()->find($criteria1);
            
            return $name['name'];
            
            
        }
        
        /**
         * This is the function that retrieves a resource/tool id given its name
         */
        public function determineResourceOrToolId($toolname){
            
            $criteria1 = new CDbCriteria();
            $criteria1->select = 'id, name';
            $criteria1->condition='name=:name';
            $criteria1->params = array(':name'=>$toolname);
            $id= Resources::model()->find($criteria1);
            
            return $id['id'];
            
        }
        
        
        /**
         * This is the function that retrieves a resource/tool name given its id
         */
        public function determineResourceOrToolName($toolid){
            
            $criteria1 = new CDbCriteria();
            $criteria1->select = 'id, name';
            $criteria1->condition='id=:id';
            $criteria1->params = array(':id'=>$toolid);
            $name= Resources::model()->find($criteria1);
            
            return $name['name'];
            
        }
        
        /**
         * This is the function that retrieves a resource/tool name given its id
         */
        public function determineGrouptypeGivenDomainId($domainid){
            
            $criteria1 = new CDbCriteria();
            $criteria1->select = 'id, name';
            $criteria1->condition='id=:id';
            $criteria1->params = array(':id'=>$domainid);
            $name= GroupType::model()->find($criteria1);
            
            return $name['name'];
            
        }
        
        /**
         * This is the function the retrieves a group id given the group name
         */
        public function determineGroupIdGivenGroupName($groupname,$domainid){
            
            //obtain the grouptype id given a domain id
            $grouptype_id = $this->determineGrouptypeIdGivenDomainId($domainid);
            $criteria = new CDbCriteria();
            $criteria->select = 'id, name';
            $criteria->condition='name=:name and grouptype_id=:id';
            $criteria->params = array(':name'=>$groupname, ':id'=>$grouptype_id);
            $id= Group::model()->find($criteria);
            
            return $id['id'];
            
            
        }
        
        /**
         * This is the function to retrieve subgroup id given subgroup name
         */
        public function determineSubgroupIdGivenSubgroupName($subgroupname, $domainid){
            //determine the group for this subgroup            
            $criteria = new CDbCriteria();
            $criteria->select = 'id, name, group_id';
            $criteria->condition='name=:name';
            $criteria->params = array(':name'=>$subgroupname);
            $groups= SubGroup::model()->findAll($criteria);
            
            foreach($groups as $group){
                $groupdomainid = $this->determineDomainIdGivenGroupId($group['group_id']);
                if($groupdomainid == $domainid){
                    $criteria1 = new CDbCriteria();
                    $criteria1->select = 'id, name';
                    $criteria1->condition='name=:name';
                    $criteria1->params = array(':name'=>$subgroupname);
                    $id= SubGroup::model()->find($criteria1);
                    
                     return $id['id'];
                    
                }
                
                
            }
            
           
            
        }
        
        /**
         * This is the function that determines grouptype is given domain id
         */
        public function determineGrouptypeIdGivenDomainId($domainid){
            
            //determine domain name
            $domainname = $this->determineDomainNameGivenItId($domainid);
            //Determine grouptype id given domain name
            $grouptypeid = $this->determineGrouptypeIdGivenDomainName($domainname);
            
            return $grouptypeid;
            
        }
        
        
        /**
         * This is the function that determines domain id given group id
         */
        public function determineDomainIdGivenGroupId($groupid){
            //determine grouptype id given group id
            $grouptypeid = $this->determineGrouptypeIdGivenGroupId($groupid);
            //determine domain id given grouptype id
            $domainid = $this->determineDomainIdGivenGrouptypeId($grouptypeid);
            
            return $domainid;
            
            
        }
        
        /**
         * This is the function that determines the grouptypeid given group id
         */
        public function determineGrouptypeIdGivenGroupId($groupid){
            
            $criteria = new CDbCriteria();
            $criteria->select = 'id, name, grouptype_id';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$groupid);
            $type= Group::model()->find($criteria);
            
            return $type['grouptype_id'];
            
        }
        
        /**
         * This is the function that returns domain id given grouptype id
         */
        public function determineDomainIdGivenGrouptypeId($grouptypeid){
            
            //determine the grouptype name
            $typename = $this->determineGrouptypeNameGivenGrouptypeId($grouptypeid);
            
            $domainname = $this->determineDomainNameGivenGrouptypeName($typename);
           
            //determine domain id given its id
            $domainid = $this->determineDomainIdGivenItsName($domainname);
            
            return $domainid;
            
            
        }
        
        /**
         * This is the function that determines grouptype name given its id
         **/
        public function determineGrouptypeNameGivenGrouptypeId($typeid){
            
            $criteria = new CDbCriteria();
            $criteria->select = 'id, name';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$typeid);
            $type= GroupType::model()->find($criteria);
            
            return $type['name'];
            
        }
        
        /**
         * This is the function that determines domain name given grouptype name
         */
        public function determineDomainNameGivenGrouptypeName($typename){
            
            $criteria = new CDbCriteria();
            $criteria->select = 'id, name';
            $criteria->condition='name=:name';
            $criteria->params = array(':name'=>$typename);
            $domain= ResourcegroupCategory::model()->find($criteria);
            
            return $domain['name'];
            
        }
        
        /**
         * This is the function that obtains a toolbox name given its id 
         */
        public function determineToolboxNameGivenItsId($toolboxid){
            
            $criteria = new CDbCriteria();
            $criteria->select = 'id, name';
            $criteria->condition='id=:id';
            $criteria->params = array(':id'=>$toolboxid);
            $toolbox= Resourcegroup::model()->find($criteria);
            
            return $toolbox['name'];
            
        }
        
        
        /**
         * This is the function that obtains a toolbox id given its name
         */
        public function determineToolboxIdGivenItsName($toolboxname){
            
            $criteria = new CDbCriteria();
            $criteria->select = 'id, name';
            $criteria->condition='name=:name';
            $criteria->params = array(':name'=>$toolboxname);
            $toolbox= Resourcegroup::model()->find($criteria);
            
            return $toolbox['id'];
            
        }

        
        /**
         * This is the function that retrieves all the toolboxes in a domain's store
         */
        public function actionListAllToolboxesInThisDomainStore(){
            
            $user_id = Yii::app()->user->id;
            if(isset($_REQUEST['product_id'])){
                $product_id=$_REQUEST['product_id'];
            }else{
                $product_id = "";
            }
            if(isset($_REQUEST['search_string'])){
                $search_string = $_REQUEST['search_string'];
                //$string = preg_replace('/\s/', '', $search_string);
                $searchWords = explode('+',$search_string);
            }else{
                $search_string = "";
            }
            if(isset($_REQUEST['id'])){
                $domain_id = $_REQUEST['id'];
            }else{
                $domain_id="";
            }
            if(isset($_REQUEST['network_id'])){
                $network_id = $_REQUEST['network_id'];
            }else{
                $network_id="";
            }
            
            if(isset($_REQUEST['keyword'])){
                $keyword = $_REQUEST['keyword'];
            }else{
               $keyword="false"; 
            }
            if(isset($_REQUEST['actor'])){
                $actor = $_REQUEST['actor'];
            }else{
                $actor="false";
            }
            if(isset($_REQUEST['designation'])){
                $designation = $_REQUEST['designation'];
            }else{
               $designation="false"; 
            }
            if(isset($_REQUEST['context'])){
                $context = $_REQUEST['context'];
                
                
            }else{
                $context="false";
            }
            if(isset($_REQUEST['dcontext'])){
                    $dcontext = $_REQUEST['dcontext'];
             }else{
                 $dcontext = "";
             }
            if($domain_id !== ""){
                //$domain_id = 1;
                $domain_id = $domain_id;
                //$domain_id = $_REQUEST['id'];
                //list all the toolboxes in this domain
                //$network_id = $_REQUEST['network_id'];
           
            if(($product_id === "" || $product_id == 0) &&  $search_string === ""){
                $criteria9 = new CDbCriteria();
                $criteria9->select = '*';
                $criteria9->condition='domain_id=:domainid and visibility!=:visibility';
                $criteria9->params = array(':domainid'=>$domain_id,':visibility'=>'reserved');
                $toolboxes= Resourcegroup::model()->findAll($criteria9);
                
                //determine if the toolboxes are in compliance with the domain's store policy
                $alltoolboxes = [];
              
                //first update all toolboxes with deriviable prices with the most current and actual price
                foreach($toolboxes as $derived){
                     if($this->isToolboxPricingDerivable($derived['id'])){
                            $derived_price = $this->getThisToolboxAmount($derived['id']);
                            //update the corresponding  toolbox
                            $this->updateThisToolboxPricing($derived['id'],$derived_price);
                        }
                }
               
                
                foreach($toolboxes as $toolbox){
                    if($this->isToolboxInComplianceWithDomainPolicy($toolbox['id'], $domain_id)){
                        $criteria10 = new CDbCriteria();
                        $criteria10->select = '*';
                        $criteria10->condition='id=:id';
                        $criteria10->params = array(':id'=>$toolbox['id']);
                        $dtoolboxes= Resourcegroup::model()->find($criteria10); 
                        
                        $alltoolboxes[] = $dtoolboxes;
                        
                        
                    }
                    
                }
               
                if($alltoolboxes===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "toolbox" => $alltoolboxes,
                            "product_id"=>$product_id,
                           "string"=>$search_string,
                           "network_id"=>$network_id,
                           "words"=>$searchWords
                          
                       ));
                       
                }
            }else if(($product_id !== "" || $product_id != 0 ) &&  $search_string === "" ){
                //get the toolbox that has tools with that product type
                 $toolboxes = $this->getAllToolboxesBelongingToThisDomain($domain_id);
                 $product_toolboxes = [];
                 foreach($toolboxes as $toolbox){
                     if($this->toolboxHasAToolBelongingToThisProduct($toolbox,$product_id)){
                       if($this->isToolboxInComplianceWithDomainPolicy($toolbox, $domain_id)){
                              $product_toolboxes[] = $toolbox;
                          }
                         
                     }
                 }
                 
                 
                //first update all toolboxes with deriviable prices with the most current and actual price
                foreach($product_toolboxes as $derived){
                     if($this->isToolboxPricingDerivable($derived)){
                            $derived_price = $this->getThisToolboxAmount($derived);
                            //update the corresponding  toolbox
                            $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                }
                //spool the detail of these toolboxes
                 $selected_toolboxes = [];
                 
                 foreach($product_toolboxes as $product){
                     $criteria8 = new CDbCriteria();
                     $criteria8->select = '*';
                     $criteria8->condition='id=:id';
                     $criteria8->params = array(':id'=>$product);
                     $dtoolboxes= Resourcegroup::model()->find($criteria8);
                     $selected_toolboxes[] = $dtoolboxes;
                    
                 }
                
                 if($selected_toolboxes===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "toolbox" => $selected_toolboxes,
                           "product_id"=>$product_id,
                           "string"=>$search_string,
                           "words"=>$searchWords
                           
                        
                       ));
                       
                }
            }else if(($product_id === "" || $product_id == 0) &&  $this->searchStringParameterIsTheOnlyInfluencer($search_string,$keyword,$actor,$designation,$context)){
                
                $searchable_items = [];
                foreach($searchWords as $word){
                    $word = preg_replace('/\s/', '', $word);
                   $q = "SELECT id FROM resourcegroup where domain_id=$domain_id and name REGEXP '$word'" ;
                    $cmd = Yii::app()->db->createCommand($q);
                    $results = $cmd->query();
                    foreach($results as $result){
                        if($this->isToolboxInComplianceWithDomainPolicy($result['id'], $domain_id)){
                            $searchable_items[] = $result['id'];
                        }
                        
                    } 
                    
                }
                
                //first update all toolboxes with deriviable prices with the most current and actual price
                foreach($searchable_items as $derived){
                     if($this->isToolboxPricingDerivable($derived)){
                            $derived_price = $this->getThisToolboxAmount($derived);
                            //update the corresponding  toolbox
                            $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                }
                //spool the toolboxes 
                $searched_out_results = [];
               
                foreach($searchable_items as $item){
                    $criteria3 = new CDbCriteria();
                    $criteria3->select = '*';
                    $criteria3->condition='id=:id and visibility!=:visibility';
                    $criteria3->params = array(':id'=>$item,':visibility'=>'reserved');
                    $toolboxes= Resourcegroup::model()->find($criteria3);
                    $searched_out_results[] = $toolboxes;
                   
                }
                
            
                if($searched_out_results===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "toolbox" => $searched_out_results,
                           "product_id"=>$product_id,
                           "string"=>$search_string,
                           "words"=>$searchWords,
                           "word"=>$word
                           
                          
              
                       ));
                       
                }
            }else if(($product_id !== "" || $product_id != 0) &&  $this->searchStringParameterIsTheOnlyInfluencer($search_string,$keyword,$actor,$designation,$context) ){
                $searchable_items = [];
                $toolboxes_with_derived_prices = [];
                foreach($searchWords as $word){
                    $word = preg_replace('/\s/', '', $word);
                   $q = "SELECT id FROM resourcegroup where domain_id=$domain_id and name REGEXP '$word'" ;
                    $cmd = Yii::app()->db->createCommand($q);
                    $results = $cmd->query();
                    foreach($results as $result){
                     $searchable_items[] = $result['id'];
                    } 
                    
                }
                
                //confirm if these toolboxes has a tool from the given product id
                $product_toolboxes = [];
                 foreach($searchable_items as $item){
                     if($this->toolboxHasAToolBelongingToThisProduct($item,$product_id)){
                         if($this->isToolboxInComplianceWithDomainPolicy($item, $domain_id)){
                             $product_toolboxes[] = $item;
                         }
                         
                     }
                 }
               
                  //first update all toolboxes with deriviable prices with the most current and actual price
                foreach($product_toolboxes as $derived){
                     if($this->isToolboxPricingDerivable($derived)){
                            $derived_price = $this->getThisToolboxAmount($derived);
                            //update the corresponding  toolbox
                            $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                }

                  //spool the detail of these toolboxes
                 $selected_toolboxes = [];
                 
                 foreach($product_toolboxes as $product){
                     $criteria4 = new CDbCriteria();
                     $criteria4->select = '*';
                     $criteria4->condition='id=:id';
                     $criteria4->params = array(':id'=>$product);
                     $dtoolboxes= Resourcegroup::model()->find($criteria4);
                     $selected_toolboxes[] = $dtoolboxes;
                     
                 }
                
                 if($selected_toolboxes===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "toolbox" => $selected_toolboxes,
                           "product_id"=>$product_id,
                           "string"=>$search_string,
                           "words"=>$searchWords,
                           "word"=>$word
                          
                          
                           
                           
                          
                       ));
                       
                }
                
                 
            }else if($this->searchStringParameterIsTheOnlyInfluencer($search_string,$keyword,$actor,$designation,$context) === false){
                
                $usable_media = [];
                if($this->isSeachStringParameterAnInfluencer($search_string)){
                    //obtain all the toolboxes in this domain that matches the seacjh parameters
                $searchable_items = [];
               // $results = [];
                if(($product_id !== "")){
                    
                    foreach($searchWords as $word){
                        $word = preg_replace('/\s/', '', $word);
                        $q = "SELECT id FROM resourcegroup where domain_id=$domain_id" ;
                        $cmd = Yii::app()->db->createCommand($q);
                        $results = $cmd->query();
                        foreach($results as $result){
                           if($this->toolboxHasAToolBelongingToThisProduct($result['id'],$product_id)){
                               if($this->isToolboxInComplianceWithDomainPolicy($result['id'], $domain_id)){
                                    $searchable_items[] = $result['id'];
                               }
                           }
                            
                    } 
                    
                } 
                }else{
                    foreach($searchWords as $word){
                        $word = preg_replace('/\s/', '', $word);
                        $q = "SELECT id FROM resourcegroup where domain_id=$domain_id" ;
                        $cmd = Yii::app()->db->createCommand($q);
                        $results = $cmd->query();
                        foreach($results as $result){
                             if($this->isToolboxInComplianceWithDomainPolicy($result['id'], $domain_id)){
                                 $searchable_items[] = $result['id'];
                             }
                        
                    } 
                    
                } 
                }
                
                    
                  foreach($searchWords as $word){
                      $word = preg_replace('/\s/', '', $word);
                      if($this->isKeywordParameterAnInfluencer($keyword)){
                          //on each of the toolboxes/media Program service confirm if there is a slot with such keyword
                          foreach($searchable_items as $item){
                              if($this->isProgramWithMediaSlotWithSuchKeyword($item,$word)){
                                  $usable_media[]= $item;
                              }
                          }
                      }
                      if($this->isActorParameterAnInfluencer($actor)){
                          
                          foreach($searchable_items as $item){
                              if($this->isProgramWithMediaSlotWithSuchAnActor($item,$word)){
                                  $usable_media[]= $item;
                              }
                          }
                      }
                      if($this->isDesignationParameterAnInfluencer($designation)){
                          foreach($searchable_items as $item){
                              if($this->isProgramWithMediaSlotWithSuchADesignation($item,$word)){
                                  $usable_media[]= $item;
                              }
                          }
                      }
                   
                   
                }// end of the foreach statement
                 if($this->isContextParameterAnInfluencer($context)){
                          $context_filtered_media =  [];
                           //find the unique toolboxes for this search
                         $search_unique = array_unique($usable_media);
                         $target_media = [];
                        $selected_media = [];
                         
                         if($dcontext !="" || $dcontext != " "){
                             
                             foreach($search_unique as $media_program){
                            // if($media != "" || $media != " "){
                              if($this->isProgramWithMediaServiceWithinThisContext($media_program,$dcontext)){
                                  $context_filtered_media[]= $media_program;
                              }
                            // }
                          }
                           //load the data for this toolboxes/media
                        
                        foreach($context_filtered_media as $searching){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='id=:id';
                            $criteria->params = array(':id'=>$searching);
                            $active_media = Resourcegroup::model()->find($criteria); 
                            $target_media[] = $active_media;
                            $selected_media[]=$active_media['id'];
                            
                          //set the latest pricing for this media/toolbox resources
                         foreach($selected_media as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                        }
                         }else{
                              //load the data for this toolboxes/media
                            
                            foreach($search_unique as $searching){
                                $criteria = new CDbCriteria();
                                $criteria->select = '*';
                                $criteria->condition='id=:id';
                                $criteria->params = array(':id'=>$searching);
                                $active_media = Resourcegroup::model()->find($criteria); 
                                $target_media[] = $active_media; 
                                $selected_media[]=$active_media['id'];
                            } 
                         }
                         
                        //set the latest pricing for this media/toolbox resources
                    foreach($selected_media as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                        
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                                "toolbox" =>$target_media,
                                "tester"=>"just testing this control",
                                "searchables"=>$searchable_items,
                                "domain"=>$domain_id,
                                "product"=>$product_id,
                               // "result"=>$result['id'],
                                "usables"=>$usable_media,
                                "search_unique"=>$search_unique,
                                "string"=>$search_string,
                                "words"=>$searchWords,
                                "word"=>$word
                                
                              
                               
                               
                            ));
                        
                          
                          
                      }else{
                                       
                            //find the unique toolboxes for this search
                         $search_unique = array_unique($usable_media);
                  
                         //load the data for this toolboxes/media
                        $target_media = [];
                        $selected_media = [];
                        foreach($search_unique as $searching){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='id=:id';
                            $criteria->params = array(':id'=>$searching);
                            $active_media = Resourcegroup::model()->find($criteria); 
                            $target_media[] = $active_media;
                            $selected_media[] = $active_media['id'];
                        }
                        
                    //set the latest pricing for this media/toolbox resources
                    foreach($selected_media as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                                "toolbox" =>$target_media,
                                "searching"=>$search_unique,
                                "usable"=>$usable_media,
                                "context"=>"no set",
                                "string"=>$search_string,
                                "words"=>$searchWords,
                                "word"=>$word
                            ));
                        }
                        
                    
                }else{
                     if($this->isContextParameterAnInfluencer($context)){
                            //get all the media within the given context
                        
                        $target_media = [];
                        $selected_media = [];
                        
                        //get the resources that are in that context
                        if(($product_id !== "")){
                            if($product_id !== 0){
                                $alltoolboxes = $this->getAllProgramAndEventMediaBelongingToThisDomainInThisProduct($product_id, $domain_id); 
                            }else{
                                $alltoolboxes = $this->getAllProgramAndEventsBelongingToThisDomain($domain_id);
                            }
                           
                        }else{
                            $alltoolboxes = $this->getAllProgramAndEventsBelongingToThisDomain($domain_id);
                        }
                        foreach($alltoolboxes as $program){
                                    if($this->isProgramWithMediaServiceWithinThisContext($program,$dcontext)){
                                        $criteria = new CDbCriteria();
                                        $criteria->select = '*';
                                        $criteria->condition='id=:id';
                                        $criteria->params = array(':id'=>$program);
                                        $active_toolboxes = Resourcegroup::model()->find($criteria); 
                                        $target_media[] = $active_toolboxes;
                                        $selected_media[] = $active_toolboxes['id'];
                            }
                                
                        }
                    //set the latest pricing for this media/toolbox resources
                    foreach($selected_media as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                        
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                                "toolbox" =>$target_media,
                                "string"=>$search_string,
                                "words"=>$searchWords
                               
                            ));
                        
                            
             }
                    
          }
                
            }//end of the use of search influencers here
                
            }else if($network_id !== ""){
               
               //get the network domain
                $network_domain = $this->getTheNetworkDomain($network_id);
                //list all the toolboxes in this domain
            
            if(($product_id === "" || $product_id == 0) &&  $search_string === ""){
                //retreive all toolboxes on that network
                $network_toolboxes = $this->getNetworkToolboxes($network_id);
                
                 //first update all toolboxes with deriviable prices with the most current and actual price
                foreach($network_toolboxes as $derived){
                     if($this->isToolboxPricingDerivable($derived)){
                            $derived_price = $this->getThisToolboxAmount($derived);
                            //update the corresponding  toolbox
                            $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                }
                $alltoolboxes = [];
                
                foreach($network_toolboxes as $ntoolbox){
                    $criteria5 = new CDbCriteria();
                    $criteria5->select = '*';
                    $criteria5->condition='id=:id and visibility=:visibility';
                    $criteria5->params = array(':id'=>$ntoolbox,':visibility'=>'reserved');
                    $toolbox= Resourcegroup::model()->find($criteria5);
                    
                    $alltoolboxes[] = $toolbox;
                   
                }
                
                if($alltoolboxes===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "toolbox" => $alltoolboxes,
                            "product_id"=>$product_id,
                           "string"=>$search_string,
                           "words"=>$searchWords
                    
                       ));
                       
                }
            }else if(($product_id !== "" || $product_id != 0 ) &&  $search_string === "" ){
                //get the toolbox that has tools with that product type
                 $toolboxes = $this->getAllToolboxesBelongingToThisDomain($network_domain);
                 $product_toolboxes = [];
                
                 foreach($toolboxes as $toolbox){
                     if($this->toolboxHasAToolBelongingToThisProduct($toolbox,$product_id)){
                         if($this->isToolboxInNetwork($network_id,$toolbox)){
                             $product_toolboxes[] = $toolbox;
                         }
                         
                     }
                 }
                 
                  //first update all toolboxes with deriviable prices with the most current and actual price
                foreach($product_toolboxes as $derived){
                     if($this->isToolboxPricingDerivable($derived)){
                            $derived_price = $this->getThisToolboxAmount($derived);
                            //update the corresponding  toolbox
                            $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                }
                 //spool the detail of these toolboxes
                 $selected_toolboxes = [];
               
                 foreach($product_toolboxes as $product){
                     $criteria6 = new CDbCriteria();
                     $criteria6->select = '*';
                     $criteria6->condition='id=:id';
                     $criteria6->params = array(':id'=>$product);
                     $dtoolboxes= Resourcegroup::model()->find($criteria6);
                     $selected_toolboxes[] = $dtoolboxes;
                     
                 }
                 
                 if($selected_toolboxes===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "toolbox" => $selected_toolboxes,
                           "product_id"=>$product_id,
                           "string"=>$search_string,
                           "words"=>$searchWords
                          
                          
                           
                           
                          
                       ));
                       
                }
            }else if(($product_id === "" || $product_id == 0) &&  $this->searchStringParameterIsTheOnlyInfluencer($search_string,$keyword,$actor,$designation,$context) ){
               
                $searchable_items = [];
                foreach($searchWords as $word){
                   $word = preg_replace('/\s/', '', $word);
                   $q = "SELECT id FROM resourcegroup where domain_id=$network_domain and name REGEXP '$word'" ;
                    $cmd = Yii::app()->db->createCommand($q);
                    $results = $cmd->query();
                    foreach($results as $result){
                     if($this->isToolboxInNetwork($network_id,$result['id'])){
                          $searchable_items[] = $result['id'];
                     }
                       
                    } 
                    
                }
                foreach($searchable_items as $derived){
                     if($this->isToolboxPricingDerivable($derived)){
                            $derived_price = $this->getThisToolboxAmount($derived);
                            //update the corresponding  toolbox
                            $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                }
                $searched_out_results = [];
                
                foreach($searchable_items as $item){
                    $criteria7 = new CDbCriteria();
                    $criteria7->select = '*';
                    $criteria7->condition='id=:id and visibility=:visibility';
                    $criteria7->params = array(':id'=>$item,':visibility'=>'reserved');
                    $toolboxes= Resourcegroup::model()->find($criteria7);
                    $searched_out_results[] = $toolboxes;
                    
                }
                
                
                if($searched_out_results===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "toolbox" => $searched_out_results,
                           "product_id"=>$product_id,
                           "string"=>$search_string,
                           "words"=>$searchWords
                           
                          
                           
                           
                          
                       ));
                       
                }
            }else if(($product_id !== "" || $product_id != 0) &&  $this->searchStringParameterIsTheOnlyInfluencer($search_string,$keyword,$actor,$designation,$context) ){
                $searchable_items = [];
                foreach($searchWords as $word){
                   $word = preg_replace('/\s/', '', $word);
                   $q = "SELECT id FROM resourcegroup where domain_id=$network_domain and name REGEXP '$word'" ;
                    $cmd = Yii::app()->db->createCommand($q);
                    $results = $cmd->query();
                    foreach($results as $result){
                      if($this->isToolboxInNetwork($network_id,$result['id'])){
                        $searchable_items[] = $result['id'];
                      }  
                    } 
                    
                }
                
                //confirm if these toolboxes has a tool from the given product id
                $product_toolboxes = [];
                 foreach($searchable_items as $item){
                     if($this->toolboxHasAToolBelongingToThisProduct($item,$product_id)){
                          $product_toolboxes[] = $item;
                        
                     }
                 }
               
                  foreach($product_toolboxes as $derived){
                     if($this->isToolboxPricingDerivable($derived)){
                            $derived_price = $this->getThisToolboxAmount($derived);
                            //update the corresponding  toolbox
                            $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                }
                 //spool the detail of these toolboxes
                 $selected_toolboxes = [];
                
                 foreach($product_toolboxes as $product){
                     $criteria8 = new CDbCriteria();
                     $criteria8->select = '*';
                     $criteria8->condition='id=:id';
                     $criteria8->params = array(':id'=>$product);
                     $dtoolboxes= Resourcegroup::model()->find($criteria8);
                     $selected_toolboxes[] = $dtoolboxes;
                    
                 }
               
                 if($selected_toolboxes===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "toolbox" => $selected_toolboxes,
                           "product_id"=>$product_id,
                           "string"=>$search_string,
                           "words"=>$searchWords
                           
                          
                           
                           
                          
                       ));
                       
                }
                
                 
            }else if($this->searchStringParameterIsTheOnlyInfluencer($search_string,$keyword,$actor,$designation,$context) === false){
                $usable_media = [];
                if($this->isSeachStringParameterAnInfluencer($search_string)){
                    //obtain all the toolboxes in this domain that matches the seacjh parameters
                $searchable_items = [];
               // $results = [];
                if(($product_id !== "")){
                    
                    foreach($searchWords as $word){
                       $word = preg_replace('/\s/', '', $word);
                       $q = "SELECT id FROM resourcegroup where domain_id=$network_domain and visibility='reserved'" ;
                        $cmd = Yii::app()->db->createCommand($q);
                        $results = $cmd->query();
                        foreach($results as $result){
                           if($this->toolboxHasAToolBelongingToThisProduct($result['id'],$product_id)){
                               //if($this->isToolboxInComplianceWithDomainPolicy($result['id'], $domain_id)){
                                    $searchable_items[] = $result['id'];
                              // }
                           }
                            
                    } 
                    
                } 
                }else{
                    foreach($searchWords as $word){
                        $word = preg_replace('/\s/', '', $word);
                        $q = "SELECT id FROM resourcegroup where domain_id=$network_domain and visibility='reserved'" ;
                        $cmd = Yii::app()->db->createCommand($q);
                        $results = $cmd->query();
                        foreach($results as $result){
                            // if($this->isToolboxInComplianceWithDomainPolicy($result['id'], $domain_id)){
                                 $searchable_items[] = $result['id'];
                             //}
                        
                    } 
                    
                } 
                }
                
                    
                  foreach($searchWords as $word){
                      $word = preg_replace('/\s/', '', $word);
                      if($this->isKeywordParameterAnInfluencer($keyword)){
                          //on each of the toolboxes/media Program service confirm if there is a slot with such keyword
                          foreach($searchable_items as $item){
                              if($this->isProgramWithMediaSlotWithSuchKeyword($item,$word)){
                                  $usable_media[]= $item;
                              }
                          }
                      }
                      if($this->isActorParameterAnInfluencer($actor)){
                          
                          foreach($searchable_items as $item){
                              if($this->isProgramWithMediaSlotWithSuchAnActor($item,$word)){
                                  $usable_media[]= $item;
                              }
                          }
                      }
                      if($this->isDesignationParameterAnInfluencer($designation)){
                          foreach($searchable_items as $item){
                              if($this->isProgramWithMediaSlotWithSuchADesignation($item,$word)){
                                  $usable_media[]= $item;
                              }
                          }
                      }
                   
                   
                }// end of the foreach statement
                 if($this->isContextParameterAnInfluencer($context)){
                          $context_filtered_media =  [];
                           //find the unique toolboxes for this search
                         $search_unique = array_unique($usable_media);
                         $target_media = [];
                        $selected_media = [];
                         
                         if($dcontext !="" || $dcontext != " "){
                             
                             foreach($search_unique as $media_program){
                            // if($media != "" || $media != " "){
                              if($this->isProgramWithMediaServiceWithinThisContext($media_program,$dcontext)){
                                  $context_filtered_media[]= $media_program;
                              }
                            // }
                          }
                           //load the data for this toolboxes/media
                        
                        foreach($context_filtered_media as $searching){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='id=:id';
                            $criteria->params = array(':id'=>$searching);
                            $active_media = Resourcegroup::model()->find($criteria); 
                            $target_media[] = $active_media;
                            $selected_media[]=$active_media['id'];
                            
                          //set the latest pricing for this media/toolbox resources
                         foreach($selected_media as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                        }
                         }else{
                              //load the data for this toolboxes/media
                            
                            foreach($search_unique as $searching){
                                $criteria = new CDbCriteria();
                                $criteria->select = '*';
                                $criteria->condition='id=:id';
                                $criteria->params = array(':id'=>$searching);
                                $active_media = Resourcegroup::model()->find($criteria); 
                                $target_media[] = $active_media; 
                                $selected_media[]=$active_media['id'];
                            } 
                         }
                         
                        //set the latest pricing for this media/toolbox resources
                    foreach($selected_media as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                        
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                                "toolbox" =>$target_media,
                                "tester"=>"just testing this control stuff",
                                "searchables"=>$searchable_items,
                                "network_domain"=>$network_domain,
                                "product"=>$product_id,
                               //"domain"=>$network_domain,
                                "usables"=>$usable_media,
                                "search_unique"=>$search_unique,
                                "string"=>$search_string,
                                "words"=>$searchWords,
                                "word"=>$word
                                
                              
                               
                               
                            ));
                        
                          
                          
                      }else{
                                       
                            //find the unique toolboxes for this search
                         $search_unique = array_unique($usable_media);
                  
                         //load the data for this toolboxes/media
                        $target_media = [];
                        $selected_media = [];
                        foreach($search_unique as $searching){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='id=:id';
                            $criteria->params = array(':id'=>$searching);
                            $active_media = Resourcegroup::model()->find($criteria); 
                            $target_media[] = $active_media;
                            $selected_media[] = $active_media['id'];
                        }
                        
                    //set the latest pricing for this media/toolbox resources
                    foreach($selected_media as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                                "toolbox" =>$target_media,
                                "searching"=>$search_unique,
                                "usable"=>$usable_media,
                                "context"=>"no setttt",
                                "string"=>$search_string,
                                "words"=>$searchWords,
                                "word"=>$word
                            ));
                        }
                        
                    
                }else{
                     if($this->isContextParameterAnInfluencer($context)){
                            //get all the media within the given context
                        
                        $target_media = [];
                        $selected_media = [];
                        
                        //get the resources that are in that context
                        if(($product_id !== "")){
                            if($product_id !== 0){
                                $alltoolboxes = $this->getAllReservedProgramAndEventMediaBelongingToThisDomainInThisProduct($product_id, $domain_id); 
                            }else{
                                $alltoolboxes = $this->getAllReservedProgramAndEventsBelongingToThisDomain($domain_id);
                            }
                           
                        }else{
                            $alltoolboxes = $this->getAllREservedProgramAndEventsBelongingToThisDomain($domain_id);
                        }
                        foreach($alltoolboxes as $program){
                                    if($this->isProgramWithMediaServiceWithinThisContext($program,$dcontext)){
                                        $criteria = new CDbCriteria();
                                        $criteria->select = '*';
                                        $criteria->condition='id=:id';
                                        $criteria->params = array(':id'=>$program);
                                        $active_toolboxes = Resourcegroup::model()->find($criteria); 
                                        $target_media[] = $active_toolboxes;
                                        $selected_media[] = $active_toolboxes['id'];
                            }
                                
                        }
                    //set the latest pricing for this media/toolbox resources
                    foreach($selected_media as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                        
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                                "toolbox" =>$target_media,
                                "string"=>$search_string,
                                "words"=>$searchWords
                               
                            ));
                        
                            
             }
                    
          } 
                
                
            }
                
            }
            
            
   
            
        }
        
        
        
        
        /**
         * This is the function that retrieves all program & events resources belonging to a domain by products
         */
        public function getAllProgramAndEventMediaBelongingToThisDomainInThisProduct($product_id, $domain_id){
            
                     $criteria8 = new CDbCriteria();
                     $criteria8->select = '*';
                     $criteria8->condition='domain_id=:id';
                     $criteria8->params = array(':id'=>$domain_id);
                     $resources= Resourcegroup::model()->findAll($criteria8);
                                          
                     $selected_media = [];
                     foreach($resources as $media){
                         if($this->toolboxHasAToolBelongingToThisProduct($media['id'],$product_id)){
                             if($this->isToolboxInComplianceWithDomainPolicy($media['id'], $domain_id)){
                                  $selected_media[] = $media['id'];
                             }
                            
                         }
                     }
                     
                     return $selected_media;
                             
        }
        
        
        /**
         * This is the function that retrieves all reserved program & event within a product for a domain
         */
        public function getAllReservedProgramAndEventMediaBelongingToThisDomainInThisProduct($product_id, $domain_id){
            
            $criteria8 = new CDbCriteria();
                     $criteria8->select = '*';
                     $criteria8->condition='domain_id=:id and visibility=:visibility';
                     $criteria8->params = array(':id'=>$domain_id,':visibility'=>'reserved');
                     $resources= Resourcegroup::model()->findAll($criteria8);
                                          
                     $selected_media = [];
                     foreach($resources as $media){
                         if($this->toolboxHasAToolBelongingToThisProduct($media['id'],$product_id)){
                            // if($this->isToolboxInComplianceWithDomainPolicy($media['id'], $domain_id)){
                                  $selected_media[] = $media['id'];
                             //}
                            
                         }
                     }
                     
                     return $selected_media;
            
        }
        
        /**
         * This is the function that get all the program & events for a domain
         */
        public function getAllReservedProgramAndEventsBelongingToThisDomain($domain_id){
            
                     $criteria8 = new CDbCriteria();
                     $criteria8->select = '*';
                     $criteria8->condition='domain_id=:id and visibility=:visibility';
                     $criteria8->params = array(':id'=>$domain_id,':visibility'=>'reserved');
                     $resources= Resourcegroup::model()->findAll($criteria8);
                                          
                     $selected_media = [];
                     foreach($resources as $media){
                     // if($this->isToolboxInComplianceWithDomainPolicy($media['id'], $domain_id)){
                                  $selected_media[] = $media['id'];
                           //  }
                            
                         
                     }
                     
                     return $selected_media;
            
        }
        
        /**
         * This is the function that gets all the reserved program & events for a domain
         */
        public function getAllProgramAndEventsBelongingToThisDomain($domain_id){
            
            $criteria8 = new CDbCriteria();
                     $criteria8->select = '*';
                     $criteria8->condition='domain_id=:id';
                     $criteria8->params = array(':id'=>$domain_id);
                     $resources= Resourcegroup::model()->findAll($criteria8);
                                          
                     $selected_media = [];
                     foreach($resources as $media){
                      if($this->isToolboxInComplianceWithDomainPolicy($media['id'], $domain_id)){
                                  $selected_media[] = $media['id'];
                             }
                            
                         
                     }
                     
                     return $selected_media;
            
        }
        
         /**
         * This is the function retrieves all tools in a toolbox
         */
        public function retrieveAllToolsInThisToolbox($toolbox_id){
            
               $criteria = new CDbCriteria();
               $criteria->select = '*';
               $criteria->condition='resourcegroup_id=:id';
               $criteria->params = array(':id'=>$toolbox_id);
               $tools= ResourceHasResourcegroups::model()->findAll($criteria);
               
               $alltools = [];
               foreach($tools as $tool){
                   $alltools[] = $tool['resource_id'];
               }
            
               return $alltools;
        }
        
        
         /**
         * This is the function that determines if search string is a search influencer
         */
        public function isSeachStringParameterAnInfluencer($searchstring){
            if($searchstring != ""){
                return true;
            }else{
                return false;
            }
        }
        
        
        /**
         * This is the function that determines if keyword is a search influencer
         */
        public function isKeywordParameterAnInfluencer($keyword){
            if($keyword ==="true" || $keyword === true){
                return true;
            }else{
                return false;
            }
        }
        
        /**
         * This is the function that determines if actor is a search influencer
         */
        public function isActorParameterAnInfluencer($actor){
            if($actor ==="true" || $actor === true){
                return true;
            }else{
                return false;
            }
        }
        
        /**
         * This is the function that determines if destination is a search influencer 
         */
        public function isDesignationParameterAnInfluencer($designation){
            if($designation === "true" || $designation === true){
                return true;
            }else{
                return false;
            }
        }
        
        
        /**
         * This is the function that determines if a context is a search influencer
         */
        public function isContextParameterAnInfluencer($context){
            if($context === "true" || $context === true){
                return true;
            }else{
                return false;
            }
        }
        
        
        /**
         * This is the function that confirms that only the search string is  available for the search
         * 
         */
        public function searchStringParameterIsTheOnlyInfluencer($searchstring,$keyword,$actor,$designation,$context){
            if($this->isKeywordParameterAnInfluencer($keyword)===false && $this->isActorParameterAnInfluencer($actor) ===false && $this->isDesignationParameterAnInfluencer($designation)=== false){
                if($this->isSeachStringParameterAnInfluencer($searchstring) === true){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
            
        }
        
        
        /**
         * This is the function that confirms that only the context is  available for the search
         * 
         */
        public function contextParameterIsTheOnlyInfluencer($searchstring,$keyword,$actor,$designation,$context){
            if($this->isKeywordParameterAnInfluencer($keyword)===false && $this->isActorParameterAnInfluencer($actor) ===false && $this->isDesignationParameterAnInfluencer($designation)=== false&& $this->isSeachStringParameterAnInfluencer($searchstring) === false){
                if($this->isContextParameterAnInfluencer($context) === true){
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
            
        }
        
        
        /**
         * This is the function that searchs through the program/event to  confirm the existence of a keyword
         */
        public function isProgramWithMediaSlotWithSuchKeyword($toolbox_id,$word){
            
            //get all the slots in this program
            //$all_slots = [];
            $all_sessions = $this->retrieveAllToolsInThisToolbox($toolbox_id);
            $keyword_counter = 0;
            //for each of the tools/session retrieve their task/slot and confirm the existence of that keyword
            foreach($all_sessions as $session){
                if($this->isThisKeywordInAnySlotInThisSession($session,$word)){
                    $keyword_counter = $keyword_counter + 1;
                }
            }
           if($keyword_counter >0){
               return true;
           }else{
               return false;
           }
        }
        
        /**
         * This is the function that searches through a session/tool for a keyword
         */
        public function isThisKeywordInAnySlotInThisSession($session_id,$word){
            
            //retrieve all the slot/task in this session/tool
            $all_slots = $this->retrieveAllTasksForThisTool($session_id);
            
            $keyword_counter = 0;
            foreach($all_slots as $slot){
                if($this->isThisKeywordInThisSlot($slot,$word)){
                    $keyword_counter = $keyword_counter + 1;
                }
            }
            if($keyword_counter >0){
                return true;
            }else{
                return false;
            }
        }
        
        
        /**
         * This is the function that confirms the existence of a keyword in amedia slot
         */
        public function isThisKeywordInThisSlot($slot_id,$word){
            
            //get all the keywords with this media slot 
            $slot_keywords = $this->retrieveAllKeywordsInThisMediaSlot($slot_id);
            
            //examine the word in each of these keywords
            $counter = 0;
            foreach($slot_keywords as $keyword){
                if($this->isSearchWordPartOfASlotKeyword($keyword, $word)){
                    $counter = $counter + 1;
                }
            }
            
            if($counter > 0){
                return  true;
            }else{
                return false;
            }
        }
        
        
        /**
         * This is the function that confirms if a searched word is in a slot keyword
         */
        public function isSearchWordPartOfASlotKeyword($keyword_id, $word){
             $cmd =Yii::app()->db->createCommand();
               $cmd->select('COUNT(*)')
                    ->from('keywords')
                    ->where("id = $keyword_id and keyword REGEXP '$word'");
                $result = $cmd->queryScalar();
                
                if($result > 0){
                    return true;
                }else{
                    return false;
                }
        }
        
        
        /**
         * This is the function that retrieves the entire slot keywords
         */
        public function retrieveAllKeywordsInThisMediaSlot($slot_id){
            
               $criteria = new CDbCriteria();
               $criteria->select = '*';
               $criteria->condition='media_id=:id';
               $criteria->params = array(':id'=>$slot_id);
               $keywords= MediaHasKeywords::model()->findAll($criteria);
               
               $allkeywords = [];
               foreach($keywords as $keyword){
                   
                   $allkeywords[] = $keyword['keyword_id'];
               }
            
               return $allkeywords;
            
        }
        
        
        /**
         * This is the function that searches through a program/event to confirm the existence of an actor in a search
         */
        public function isProgramWithMediaSlotWithSuchAnActor($toolbox_id,$word){
            
            //get all the slots in this program
            //$all_slots = [];
            $all_sessions = $this->retrieveAllToolsInThisToolbox($toolbox_id);
            $actor_counter = 0;
            //for each of the tools/session retrieve their task/slot and confirm the existence of that keyword
            foreach($all_sessions as $session){
                if($this->isThisActorInAnySlotInThisSession($session,$word)){
                    $actor_counter = $actor_counter + 1;
                }
            }
           if($actor_counter >0){
               return true;
           }else{
               return false;
           }
            
        }
        
        /**
         * This is the function that confirms if the search actor is in any slot in a session
         */
        public function isThisActorInAnySlotInThisSession($session_id,$word){
            
            //retrieve all the slot/task in this session/tool
            $all_slots = $this->retrieveAllTasksForThisTool($session_id);
            
            $actor_counter = 0;
            foreach($all_slots as $slot){
                if($this->isThisActorInThisSlot($slot,$word)){
                    $actor_counter = $actor_counter + 1;
                }
            }
            if($actor_counter >0){
                return true;
            }else{
                return false;
            }
            
            
        }
        
         /**
         * This is the function that list all task in a toolbox
         */
        public function retrieveAllTasksForThisTool($tool){
            
               $criteria = new CDbCriteria();
               $criteria->select = '*';
               $criteria->condition='parent_id=:id';
               $criteria->params = array(':id'=>$tool);
               $tasks= Resources::model()->findAll($criteria);
               
               $alltasks = [];
               foreach($tasks as $task){
                   
                   $alltasks[] = $task['id'];
               }
            
               return $alltasks;
        }
        
        /**
         * This is the function that verifies the existence of the searched actor in a slot
         */
        public function isThisActorInThisSlot($slot_id,$word){
            
             //get all the keywords with this media slot 
            $slot_actors = $this->retrieveAllActorsInThisMediaSlot($slot_id);
            
            //examine the word in each of these keywords
            $counter = 0;
            foreach($slot_actors as $actor){
                if($this->isSearchWordPartOfASlotActor($actor, $word)){
                    $counter = $counter + 1;
                }
            }
            
            if($counter > 0){
                return  true;
            }else{
                return false;
            }
        }
        
        
        /**
         * This is the function that confirms if a searched word is part of slot actor
         */
        public function isSearchWordPartOfASlotActor($actor_id, $word){
            
            $cmd =Yii::app()->db->createCommand();
               $cmd->select('COUNT(*)')
                    ->from('actors')
                    ->where("id = $actor_id and name REGEXP '$word'");
                $result = $cmd->queryScalar();
                
                if($result > 0){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        /**
         * This is the function that retrieves all actors in a media slot
         */
        public function retrieveAllActorsInThisMediaSlot($slot_id){
            
               $criteria = new CDbCriteria();
               $criteria->select = '*';
               $criteria->condition='media_id=:id';
               $criteria->params = array(':id'=>$slot_id);
               $actors= MediaHasKeywords::model()->findAll($criteria);
               
               $allactors = [];
               foreach($actors as $actor){
                   
                   $allactors[] = $actor['actor_id'];
               }
            
               return $allactors;
            
        }
        
        
        /**
         * This is the function that searches through a program to determine if a slot has actor with this searched designation
         */
        public function isProgramWithMediaSlotWithSuchADesignation($toolbox_id,$word){
            
            //get all the slots in this program
            //$all_slots = [];
            $all_sessions = $this->retrieveAllToolsInThisToolbox($toolbox_id);
            $actor_counter = 0;
            //for each of the tools/session retrieve their task/slot and confirm the existence of that keyword
            foreach($all_sessions as $session){
                if($this->isThisDesignationInAnySlotInThisSession($session,$word)){
                    $actor_counter = $actor_counter + 1;
                }
            }
           if($actor_counter >0){
               return true;
           }else{
               return false;
           }
            
            
            
        }
        
        
        /**
         * This is the function  that confirms if a designation in the search is in a slot
         */
        public function isThisDesignationInAnySlotInThisSession($session_id,$word){
            
            //retrieve all the slot/task in this session/tool
            $all_slots = $this->retrieveAllTasksForThisTool($session_id);
            
            $designation_counter = 0;
            foreach($all_slots as $slot){
                if($this->isThisDesignationInThisSlot($slot,$word)){
                    $designation_counter = $designation_counter + 1;
                }
            }
            if($designation_counter >0){
                return true;
            }else{
                return false;
            }
            
        }
        
        /**
         * This is the function that verifies if a designation is in a slot
         */
        public function isThisDesignationInThisSlot($slot_id,$word){
            
              //get all the keywords with this media slot 
            $slot_designation = $this->retrieveAllDesignationsInThisMediaSlot($slot_id);
            
            //examine the word in each of these keywords
            $counter = 0;
            foreach($slot_designation as $designation){
                if($this->isSearchWordPartOfASlotDesignation($designation, $word)){
                    $counter = $counter + 1;
                }
            }
            
            if($counter > 0){
                return  true;
            }else{
                return false;
            }
            
        }
        
        
        /**
         * This is the function that confirms if a search word is part of an actor designation in a media
         */
        public function isSearchWordPartOfASlotDesignation($designation_id, $word){
            
             $cmd =Yii::app()->db->createCommand();
               $cmd->select('COUNT(*)')
                    ->from('designations')
                    ->where("id = $designation_id and designation REGEXP '$word'");
                $result = $cmd->queryScalar();
                
                if($result > 0){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        /**
         * This is the function that retrieves all designations in a slot
         */
        public function retrieveAllDesignationsInThisMediaSlot($slot_id){
            
               $criteria = new CDbCriteria();
               $criteria->select = '*';
               $criteria->condition='media_id=:id';
               $criteria->params = array(':id'=>$slot_id);
               $designations= MediaHasKeywords::model()->findAll($criteria);
               
               $alldesignations = [];
               foreach($designations as $designation){
                   
                   $alldesignations[] = $designation['designation_id'];
               }
            
               return $alldesignations;
            
        }
        
        /**
         * This is the function  that verifies if a media is within a given context
         */
        public function isProgramWithMediaServiceWithinThisContext($program_id,$dcontext_id){
            
            //get all the slots in this program
            //$all_slots = [];
            $all_sessions = $this->retrieveAllToolsInThisToolbox($program_id);
            $context_counter = 0;
            //for each of the tools/session retrieve their task/slot and confirm the existence of that that context
            foreach($all_sessions as $session){
                if($this->isThisContextInAnySlotInThisSession($session,$dcontext_id)){
                    $context_counter = $context_counter + 1;
                }
            }
           if($context_counter >0){
              return true;
              
           }else{
             return false;
           }
                
  
        }
        
        
         /**
         * This is the function that determines the existence of a context attributable to a media session
         */
        public function  isThisContextInAnySlotInThisSession($session_id, $context_id){
            
            //retrieve all the slot/task in this session/tool
            $all_slots = $this->retrieveAllTasksForThisTool($session_id);
            
            $context_counter = 0;
            foreach($all_slots as $slot){
                if($this->isContextInThisSlot($slot,$context_id )){
                    $context_counter = $context_counter + 1;
                }
            }
            if($context_counter >0){
               return true;
              }else{
               return false;
               
            }
        }
        
        
        
        /**
         * This is the function that determines if this context is in this slot
         */
        public function isContextInThisSlot($slot_id, $context_id){
            
             //get all the context with this media slot 
            $slot_context = $this->retrieveAllThisMediaContext($slot_id);
            
            if(in_array($context_id,$slot_context)){
                return true;
                
            }else{
                return false;
               
            }
            
        
        }
        
        
        
        /**
         * This is the function that retrieves all context in a media slot
         */
        public function retrieveAllThisMediaContext($slot_id){
            
               $criteria = new CDbCriteria();
               $criteria->select = '*';
               $criteria->condition='media_id=:id';
               $criteria->params = array(':id'=>$slot_id);
               $contexts= MediaHasKeywords::model()->findAll($criteria);
               
               $allcontexts = [];
               foreach($contexts as $context){
                   
                   $allcontexts[] = $context['context_id'];
               }
            
               return $allcontexts;
            
            
        }
        
        
        /**
         * This is the function that determines if derived pricing is applicable to a toolbox
         */
        public function isDerivedPricingApplicableToThisToolbox($toolbox_id){
            
                     $criteria = new CDbCriteria();
                     $criteria->select = '*';
                     $criteria->condition='id=:id';
                     $criteria->params = array(':id'=>$toolbox_id);
                     $toolbox= Resourcegroup::model()->find($criteria);
                     
                     if($toolbox['cumulative_component_price'] == 1){
                         return true;
                     }else{
                         return false;
                     }
            
        }
        
        
        
        /**
         * This is the function that will list all toolboxes  for a country store 
         * 
         * 
         */
        public function actionListAllToolboxesInThisCountryStore(){
            
            
           // $user_id = Yii::app()->user->id;
             $store_id = $_REQUEST['store_id'];
             
             if(isset($_REQUEST['product_id'])){
                $product_id=$_REQUEST['product_id'];
            }else{
                $product_id = "";
            }
            if(isset($_REQUEST['search_string'])){
                $search_string = $_REQUEST['search_string'];
                //$string = preg_replace('/\s/', '', $search_string);
                $searchWords = explode('+',$search_string);
            }else{
                $search_string = "";
            }
            if(isset($_REQUEST['id'])){
                $domain_id = $_REQUEST['id'];
            }else{
                $domain_id="";
            }
            if(isset($_REQUEST['network_id'])){
                $network_id = $_REQUEST['network_id'];
            }else{
                $network_id="";
            }
            if(isset($_REQUEST['keyword'])){
                $keyword = $_REQUEST['keyword'];
            }else{
               $keyword="false"; 
            }
            if(isset($_REQUEST['actor'])){
                $actor = $_REQUEST['actor'];
            }else{
                $actor="false";
            }
            if(isset($_REQUEST['designation'])){
                $designation = $_REQUEST['designation'];
            }else{
               $designation="false"; 
            }
            if(isset($_REQUEST['context'])){
                $context = $_REQUEST['context'];
                
                
            }else{
                $context="false";
            }
            if(isset($_REQUEST['dcontext'])){
                    $dcontext = $_REQUEST['dcontext'];
             }else{
                 $dcontext = "";
             }
             
             //get the store type
             $type = $this->getThisStoreType($store_id);
            if( $type == 'public'){
                
                   
            if(($product_id === "" || $product_id == 0) &&  ($this->isSeachStringParameterAnInfluencer($search_string) === false && $this->isContextParameterAnInfluencer($context) ===false)){
               
                //list all resourcegroup/program in the platform
                
                $all_programs = $this->getAllProgramAndEventsBelongingToThisPlatform($store_id);
                
                $programs = [];
                $selected = [];
                foreach($all_programs as $program){
                     if($this->isMediaInComplianceWithThisStorePolicy($program, $store_id)){
                        $criteria9 = new CDbCriteria();
                        $criteria9->select = '*';
                        $criteria9->condition='id=:id';
                        $criteria9->params = array(':id'=>$program);
                        $media= Resourcegroup::model()->find($criteria9);
                        $programs[] = $media;
                        $selected[] = $media['id']; 
                  } 
                    
                }
               foreach($selected as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
             
                if($programs===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "toolbox" => $programs,
                            "product_id"=>$product_id,
                           "string"=>$search_string,
                           "store_id"=>$store_id,
                          "type"=>$type
                               
                          
                           
                           
                          
                       ));
                       
                }
            }else if(($product_id !== "" || $product_id != 0 ) &&  ($this->isSeachStringParameterAnInfluencer($search_string) === false && $this->isContextParameterAnInfluencer($context) ===false)){
                //get the toolbox that has tools with that product type
                 //$toolboxes = $this->getAllToolboxesBelongingToThisDomain($domain_id);
                $toolboxes = $this->getAllProgramAndEventsBelongingToThisPlatform($store_id);
                 $product_toolboxes = [];
                foreach($toolboxes as $toolbox){
                     if($this->toolboxHasAToolBelongingToThisProduct($toolbox,$product_id)){
                       if($this->isMediaInComplianceWithThisStorePolicy($toolbox, $store_id)){
                              $product_toolboxes[] = $toolbox;
                              
                          }
                         
                     }
                 }
                 
                 //spool the detail of these toolboxes
                 $selected_toolboxes = [];
                 $selected = [];
                 foreach($product_toolboxes as $product){
                     $criteria8 = new CDbCriteria();
                     $criteria8->select = '*';
                     $criteria8->condition='id=:id';
                     $criteria8->params = array(':id'=>$product);
                     $dtoolboxes= Resourcegroup::model()->find($criteria8);
                     $selected_toolboxes[] = $dtoolboxes;
                     $selected[] = $dtoolboxes['id'];
                 }
                 
                 foreach($selected as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                 
                 if($selected_toolboxes===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "toolbox" => $selected_toolboxes,
                           "product_id"=>$product_id,
                           "string"=>$search_string
                          
                           
                           
                          
                       ));
                       
                }
            }else if(($product_id === "" || $product_id == 0) &&  $this->searchStringParameterIsTheOnlyInfluencer($search_string,$keyword,$actor,$designation,$context) ){
                
                $searchable_items = [];
                foreach($searchWords as $word){
                    $word = preg_replace('/\s/', '', $word);
                   $q = "SELECT id FROM resourcegroup where name REGEXP '$word'" ;
                    $cmd = Yii::app()->db->createCommand($q);
                    $results = $cmd->query();
                    foreach($results as $result){
                        if($this->isMediaInComplianceWithThisStorePolicy($result['id'], $store_id)){
                            $searchable_items[] = $result['id'];
                        }
                        
                    } 
                    
                }
                
                $searched_out_results = [];
                $selected = [];
                foreach($searchable_items as $item){
                    $criteria3 = new CDbCriteria();
                    $criteria3->select = '*';
                    $criteria3->condition='id=:id and visibility!=:visibility';
                    $criteria3->params = array(':id'=>$item,':visibility'=>'reserved');
                    $toolboxes= Resourcegroup::model()->find($criteria3);
                    $searched_out_results[] = $toolboxes;
                    $selected[] = $toolboxes['id'];
                }
                
            foreach($selected as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                if($searched_out_results===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "toolbox" => $searched_out_results,
                           "product_id"=>$product_id,
                           "string"=>$search_string
                          
                           
                           
                          
                       ));
                       
                }
            }else if(($product_id !== "" || $product_id != 0) &&  $this->searchStringParameterIsTheOnlyInfluencer($search_string,$keyword,$actor,$designation,$context)){
                $searchable_items = [];
                foreach($searchWords as $word){
                    $word = preg_replace('/\s/', '', $word);
                   $q = "SELECT id FROM resourcegroup where name REGEXP '$word'" ;
                    $cmd = Yii::app()->db->createCommand($q);
                    $results = $cmd->query();
                    foreach($results as $result){
                     $searchable_items[] = $result['id'];
                    } 
                    
                }
                
                //confirm if these toolboxes has a tool from the given product id
                $product_toolboxes = [];
                 foreach($searchable_items as $item){
                     if($this->toolboxHasAToolBelongingToThisProduct($item,$product_id)){
                         if($this->isMediaInComplianceWithThisStorePolicy($item, $store_id)){
                             $product_toolboxes[] = $item;
                         }
                         
                     }
                 }
               
                 //spool the detail of these toolboxes
                 $selected_toolboxes = [];
                 $selected = [];
                 foreach($product_toolboxes as $product){
                     $criteria4 = new CDbCriteria();
                     $criteria4->select = '*';
                     $criteria4->condition='id=:id';
                     $criteria4->params = array(':id'=>$product);
                     $dtoolboxes= Resourcegroup::model()->find($criteria4);
                     $selected_toolboxes[] = $dtoolboxes;
                     $selected[] = $dtoolboxes['id'];
                 }
                 
                 foreach($selected as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                 
                 if($selected_toolboxes===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "toolbox" => $selected_toolboxes,
                           "product_id"=>$product_id,
                           "string"=>$search_string
                          
                           
                           
                          
                       ));
                       
                }
                
                 
            }else if($this->searchStringParameterIsTheOnlyInfluencer($search_string,$keyword,$actor,$designation,$context) == false){
                
                $usable_media = [];
                if($this->isSeachStringParameterAnInfluencer($search_string)){
                    //obtain all the toolboxes in this domain that matches the seacjh parameters
                $searchable_items = [];
               // $results = [];
                if(($product_id !== "")){
                    
                    foreach($searchWords as $word){
                        $word = preg_replace('/\s/', '', $word);
                        $q = "SELECT id FROM resourcegroup" ;
                        $cmd = Yii::app()->db->createCommand($q);
                        $results = $cmd->query();
                        foreach($results as $result){
                           if($this->toolboxHasAToolBelongingToThisProduct($result['id'],$product_id)){
                               if($this->isMediaInComplianceWithThisStorePolicy($result['id'], $store_id)){
                                    $searchable_items[] = $result['id'];
                               }
                           }
                            
                    } 
                    
                } 
                }else{
                    foreach($searchWords as $word){
                        $word = preg_replace('/\s/', '', $word);
                        $q = "SELECT id FROM resourcegroup" ;
                        $cmd = Yii::app()->db->createCommand($q);
                        $results = $cmd->query();
                        foreach($results as $result){
                             if($this->isMediaInComplianceWithThisStorePolicy($result['id'], $store_id)){
                                 $searchable_items[] = $result['id'];
                             }
                        
                    } 
                    
                } 
                }
                
                    
                  foreach($searchWords as $word){
                      $word = preg_replace('/\s/', '', $word);
                      if($this->isKeywordParameterAnInfluencer($keyword)){
                          //on each of the toolboxes/media Program service confirm if there is a slot with such keyword
                          foreach($searchable_items as $item){
                              if($this->isProgramWithMediaSlotWithSuchKeyword($item,$word)){
                                  $usable_media[]= $item;
                              }
                          }
                      }
                      if($this->isActorParameterAnInfluencer($actor)){
                          
                          foreach($searchable_items as $item){
                              if($this->isProgramWithMediaSlotWithSuchAnActor($item,$word)){
                                  $usable_media[]= $item;
                              }
                          }
                      }
                      if($this->isDesignationParameterAnInfluencer($designation)){
                          foreach($searchable_items as $item){
                              if($this->isProgramWithMediaSlotWithSuchADesignation($item,$word)){
                                  $usable_media[]= $item;
                              }
                          }
                      }
                   
                   
                }// end of the foreach statement
                 if($this->isContextParameterAnInfluencer($context)){
                          $context_filtered_media =  [];
                           //find the unique toolboxes for this search
                         $search_unique = array_unique($usable_media);
                         $target_media = [];
                        $selected_media = [];
                         
                         if($dcontext !="" || $dcontext != " "){
                             
                             foreach($search_unique as $media_program){
                            // if($media != "" || $media != " "){
                              if($this->isProgramWithMediaServiceWithinThisContext($media_program,$dcontext)){
                                  $context_filtered_media[]= $media_program;
                              }
                            // }
                          }
                           //load the data for this toolboxes/media
                        
                        foreach($context_filtered_media as $searching){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='id=:id';
                            $criteria->params = array(':id'=>$searching);
                            $active_media = Resourcegroup::model()->find($criteria); 
                            $target_media[] = $active_media;
                            $selected_media[]=$active_media['id'];
                            
                          //set the latest pricing for this media/toolbox resources
                         foreach($selected_media as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                        }
                         }else{
                              //load the data for this toolboxes/media
                            
                            foreach($search_unique as $searching){
                                $criteria = new CDbCriteria();
                                $criteria->select = '*';
                                $criteria->condition='id=:id';
                                $criteria->params = array(':id'=>$searching);
                                $active_media = Resourcegroup::model()->find($criteria); 
                                $target_media[] = $active_media; 
                                $selected_media[]=$active_media['id'];
                            } 
                         }
                         
                        //set the latest pricing for this media/toolbox resources
                    foreach($selected_media as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                        
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                                "toolbox" =>$target_media,
                                "tester"=>"just testing this control",
                                "searchables"=>$searchable_items,
                                "domain"=>$domain_id,
                                "product"=>$product_id,
                               // "result"=>$result['id'],
                                "usables"=>$usable_media,
                                "search_unique"=>$search_unique,
                                "string"=>$search_string,
                                "words"=>$searchWords,
                                "word"=>$word
                                
                              
                               
                               
                            ));
                        
                          
                          
                      }else{
                                       
                            //find the unique toolboxes for this search
                         $search_unique = array_unique($usable_media);
                  
                         //load the data for this toolboxes/media
                        $target_media = [];
                        $selected_media = [];
                        foreach($search_unique as $searching){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='id=:id';
                            $criteria->params = array(':id'=>$searching);
                            $active_media = Resourcegroup::model()->find($criteria); 
                            $target_media[] = $active_media;
                            $selected_media[] = $active_media['id'];
                        }
                        
                    //set the latest pricing for this media/toolbox resources
                    foreach($selected_media as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                                "toolbox" =>$target_media,
                                "searching"=>$search_unique,
                                "usable"=>$usable_media,
                                "context"=>"no set",
                                "string"=>$search_string,
                                "words"=>$searchWords,
                                "word"=>$word
                            ));
                        }
                        
                    
                }else{
                     if($this->isContextParameterAnInfluencer($context)){
                            //get all the media within the given context
                        
                        $target_media = [];
                        $selected_media = [];
                        
                        //get the resources that are in that context
                        if(($product_id !== "")){
                            if($product_id !== 0){
                                $alltoolboxes = $this->getAllProgramAndEventMediaBelongingToThisProductOnThisPlatform($product_id,$store_id); 
                            }else{
                                $alltoolboxes = $this->getAllProgramAndEventsBelongingToThisPlatform($store_id);
                            }
                           
                        }else{
                            $alltoolboxes = $this->getAllProgramAndEventsBelongingToThisPlatform($store_id);
                        }
                        foreach($alltoolboxes as $program){
                                    if($this->isProgramWithMediaServiceWithinThisContext($program,$dcontext)){
                                        $criteria = new CDbCriteria();
                                        $criteria->select = '*';
                                        $criteria->condition='id=:id';
                                        $criteria->params = array(':id'=>$program);
                                        $active_toolboxes = Resourcegroup::model()->find($criteria); 
                                        $target_media[] = $active_toolboxes;
                                        $selected_media[] = $active_toolboxes['id'];
                            }
                                
                        }
                    //set the latest pricing for this media/toolbox resources
                    foreach($selected_media as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                        
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                                "toolbox" =>$target_media,
                                "string"=>$search_string,
                                "words"=>$searchWords
                               
                            ));
                        
                            
             }
                    
          }
                
            }else if($this->contextParameterIsTheOnlyInfluencer($search_string,$keyword,$actor,$designation,$context)){
                
                if(($product_id !== "")){
                    
                    foreach($searchWords as $word){
                        $word = preg_replace('/\s/', '', $word);
                        $q = "SELECT id FROM resourcegroup" ;
                        $cmd = Yii::app()->db->createCommand($q);
                        $results = $cmd->query();
                        foreach($results as $result){
                          // if($this->isProgramWithMediaServiceWithinThisContext($result['id'],$dcontext)){
                                if($this->toolboxHasAToolBelongingToThisProduct($result['id'],$product_id)){
                                    if($this->isMediaInComplianceWithThisStorePolicy($result['id'], $store_id)){
                                        $searchable_items[] = $result['id'];
                                  }
                           // }
                           }
                           
                            
                    } 
                    
                } 
                }else{
                    foreach($searchWords as $word){
                        $word = preg_replace('/\s/', '', $word);
                        $q = "SELECT id FROM resourcegroup" ;
                        $cmd = Yii::app()->db->createCommand($q);
                        $results = $cmd->query();
                        foreach($results as $result){
                           // if($this->isProgramWithMediaServiceWithinThisContext($result['id'],$dcontext)){
                                if($this->isMediaInComplianceWithThisStorePolicy($result['id'], $store_id)){
                                    $searchable_items[] = $result['id'];
                             }
                           // } 
                            
                        
                    } 
                    
                }
                //get the media within this context
                if($this->isContextParameterAnInfluencer($context)){
                          $context_filtered_media =  [];
                           //find the unique toolboxes for this search
                         $search_unique = array_unique($usable_media);
                         $target_media = [];
                        $selected_media = [];
                         
                         if($dcontext !="" || $dcontext != " "){
                             
                             foreach($search_unique as $media_program){
                            // if($media != "" || $media != " "){
                              if($this->isProgramWithMediaServiceWithinThisContext($media_program,$dcontext)){
                                  $context_filtered_media[]= $media_program;
                              }
                            // }
                          }
                           //load the data for this toolboxes/media
                        
                        foreach($context_filtered_media as $searching){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='id=:id';
                            $criteria->params = array(':id'=>$searching);
                            $active_media = Resourcegroup::model()->find($criteria); 
                            $target_media[] = $active_media;
                            $selected_media[]=$active_media['id'];
                            
                          //set the latest pricing for this media/toolbox resources
                         foreach($selected_media as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                        }
                         }else{
                              //load the data for this toolboxes/media
                            
                            foreach($search_unique as $searching){
                                $criteria = new CDbCriteria();
                                $criteria->select = '*';
                                $criteria->condition='id=:id';
                                $criteria->params = array(':id'=>$searching);
                                $active_media = Resourcegroup::model()->find($criteria); 
                                $target_media[] = $active_media; 
                                $selected_media[]=$active_media['id'];
                            } 
                         }
                         
                        //set the latest pricing for this media/toolbox resources
                    foreach($selected_media as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                        
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                                "toolbox" =>$target_media,
                                "tester"=>"just testing this control",
                                "searchables"=>$searchable_items,
                                "domain"=>$domain_id,
                                "product"=>$product_id,
                               // "result"=>$result['id'],
                                "usables"=>$usable_media,
                                "search_unique"=>$search_unique,
                                "string"=>$search_string,
                                "words"=>$searchWords,
                                "word"=>$word
                                
                              
                               
                               
                            ));
                        
                          
                          
                      }
                }
            }
                
            }else if($type == 'private'){
                
                //get the domain of this store
                
                $domain_id = $this->getTheDomainOwnerOfThisStore($store_id);
                
                 if(($product_id === "" || $product_id == 0) &&  ($this->isSeachStringParameterAnInfluencer($search_string) === false && $this->isContextParameterAnInfluencer($context) ===false)){
               
                //list all resourcegroup/program in the platform
                
                $all_programs = $this->getAllToolboxesBelongingToThisDomain($domain_id);
                
                $programs = [];
                $selected = [];
                foreach($all_programs as $program){
                     if($this->isMediaInComplianceWithThisStorePolicy($program, $store_id)){
                        $criteria9 = new CDbCriteria();
                        $criteria9->select = '*';
                        $criteria9->condition='id=:id';
                        $criteria9->params = array(':id'=>$program);
                        $media= Resourcegroup::model()->find($criteria9);
                        $programs[] = $media;
                        $selected[] = $media['id'];
                  } 
                    
                }
                foreach($selected as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
              
             
                if($programs===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "toolbox" => $programs,
                            "product_id"=>$product_id,
                           "string"=>$search_string,
                           "store_id"=>$store_id,
                           "type"=>$type
                           
                           
                               
                          
                           
                           
                          
                       ));
                       
                }
            }else if(($product_id !== "" || $product_id != 0 ) &&  ($this->isSeachStringParameterAnInfluencer($search_string) === false && $this->isContextParameterAnInfluencer($context) ===false)){
                //get the toolbox that has tools with that product type
                 $toolboxes = $this->getAllToolboxesBelongingToThisDomain($domain_id);
                 $product_toolboxes = [];
                 foreach($toolboxes as $toolbox){
                     if($this->toolboxHasAToolBelongingToThisProduct($toolbox,$product_id)){
                       if($this->isMediaInComplianceWithThisStorePolicy($toolbox, $store_id)){
                              $product_toolboxes[] = $toolbox;
                          }
                         
                     }
                 }
                 
                 //spool the detail of these toolboxes
                 $selected_toolboxes = [];
                 $selected = [];
                 foreach($product_toolboxes as $product){
                     $criteria8 = new CDbCriteria();
                     $criteria8->select = '*';
                     $criteria8->condition='id=:id';
                     $criteria8->params = array(':id'=>$product);
                     $dtoolboxes= Resourcegroup::model()->find($criteria8);
                     $selected_toolboxes[] = $dtoolboxes;
                     $selected[] = $dtoolboxes['id'];
                 }
                 
                 foreach($selected as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                 
                 if($selected_toolboxes===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "toolbox" => $selected_toolboxes,
                           "product_id"=>$product_id,
                           "string"=>$search_string
                          
                           
                           
                          
                       ));
                       
                }
            }else if(($product_id === "" || $product_id == 0) &&  $this->searchStringParameterIsTheOnlyInfluencer($search_string,$keyword,$actor,$designation,$context) ){
                
                $searchable_items = [];
                foreach($searchWords as $word){
                    $word = preg_replace('/\s/', '', $word);
                   $q = "SELECT id FROM resourcegroup where domain_id=$domain_id and name REGEXP '$word'" ;
                    $cmd = Yii::app()->db->createCommand($q);
                    $results = $cmd->query();
                    foreach($results as $result){
                        if($this->isMediaInComplianceWithThisStorePolicy($result['id'], $store_id)){
                            $searchable_items[] = $result['id'];
                        }
                        
                    } 
                    
                }
                
                $searched_out_results = [];
                $selected = [];
                foreach($searchable_items as $item){
                    $criteria3 = new CDbCriteria();
                    $criteria3->select = '*';
                    $criteria3->condition='id=:id and visibility!=:visibility';
                    $criteria3->params = array(':id'=>$item,':visibility'=>'reserved');
                    $toolboxes= Resourcegroup::model()->find($criteria3);
                    $searched_out_results[] = $toolboxes;
                    $selected[] = $toolboxes['id']; 
                }
                
                foreach($selected as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
            
                if($searched_out_results===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "toolbox" => $searched_out_results,
                           "product_id"=>$product_id,
                           "string"=>$search_string
                          
                           
                           
                          
                       ));
                       
                }
            }else if(($product_id !== "" || $product_id != 0) &&  $this->searchStringParameterIsTheOnlyInfluencer($search_string,$keyword,$actor,$designation,$context)){
                $searchable_items = [];
                foreach($searchWords as $word){
                    $word = preg_replace('/\s/', '', $word);
                   $q = "SELECT id FROM resourcegroup where domain_id=$domain_id and name REGEXP '$word'" ;
                    $cmd = Yii::app()->db->createCommand($q);
                    $results = $cmd->query();
                    foreach($results as $result){
                     $searchable_items[] = $result['id'];
                    } 
                    
                }
                
                //confirm if these toolboxes has a tool from the given product id
                $product_toolboxes = [];
                 foreach($searchable_items as $item){
                     if($this->toolboxHasAToolBelongingToThisProduct($item,$product_id)){
                         if($this->isMediaInComplianceWithThisStorePolicy($item, $store_id)){
                             $product_toolboxes[] = $item;
                         }
                         
                     }
                 }
               
                 //spool the detail of these toolboxes
                 $selected_toolboxes = [];
                 $selected = [];
                 foreach($product_toolboxes as $product){
                     $criteria4 = new CDbCriteria();
                     $criteria4->select = '*';
                     $criteria4->condition='id=:id';
                     $criteria4->params = array(':id'=>$product);
                     $dtoolboxes= Resourcegroup::model()->find($criteria4);
                     $selected_toolboxes[] = $dtoolboxes;
                     $selected[] = $dtoolboxes['id'];
                 }
                 
                 foreach($selected as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                 if($selected_toolboxes===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "toolbox" => $selected_toolboxes,
                           "product_id"=>$product_id,
                           "string"=>$search_string
                          
                           
                           
                          
                       ));
                       
                }
                
                 
            }else if($this->searchStringParameterIsTheOnlyInfluencer($search_string,$keyword,$actor,$designation,$context) == false){
                
                $usable_media = [];
                if($this->isSeachStringParameterAnInfluencer($search_string)){
                    //obtain all the toolboxes in this domain that matches the seacjh parameters
                $searchable_items = [];
               // $results = [];
                if(($product_id !== "")){
                    
                    foreach($searchWords as $word){
                        $word = preg_replace('/\s/', '', $word);
                        $q = "SELECT id FROM resourcegroup where domain_id=$domain_id" ;
                        $cmd = Yii::app()->db->createCommand($q);
                        $results = $cmd->query();
                        foreach($results as $result){
                           if($this->toolboxHasAToolBelongingToThisProduct($result['id'],$product_id)){
                               if($this->isMediaInComplianceWithThisStorePolicy($result['id'], $store_id)){
                                    $searchable_items[] = $result['id'];
                               }
                           }
                            
                    } 
                    
                } 
                }else{
                    foreach($searchWords as $word){
                        $word = preg_replace('/\s/', '', $word);
                        $q = "SELECT id FROM resourcegroup where domain_id=$domain_id" ;
                        $cmd = Yii::app()->db->createCommand($q);
                        $results = $cmd->query();
                        foreach($results as $result){
                             if($this->isMediaInComplianceWithThisStorePolicy($result['id'], $store_id)){
                                 $searchable_items[] = $result['id'];
                             }
                        
                    } 
                    
                } 
                }
                
                    
                  foreach($searchWords as $word){
                      $word = preg_replace('/\s/', '', $word);
                      if($this->isKeywordParameterAnInfluencer($keyword)){
                          //on each of the toolboxes/media Program service confirm if there is a slot with such keyword
                          foreach($searchable_items as $item){
                              if($this->isProgramWithMediaSlotWithSuchKeyword($item,$word)){
                                  $usable_media[]= $item;
                              }
                          }
                      }
                      if($this->isActorParameterAnInfluencer($actor)){
                          
                          foreach($searchable_items as $item){
                              if($this->isProgramWithMediaSlotWithSuchAnActor($item,$word)){
                                  $usable_media[]= $item;
                              }
                          }
                      }
                      if($this->isDesignationParameterAnInfluencer($designation)){
                          foreach($searchable_items as $item){
                              if($this->isProgramWithMediaSlotWithSuchADesignation($item,$word)){
                                  $usable_media[]= $item;
                              }
                          }
                      }
                   
                   
                }// end of the foreach statement
                 if($this->isContextParameterAnInfluencer($context)){
                          $context_filtered_media =  [];
                           //find the unique toolboxes for this search
                         $search_unique = array_unique($usable_media);
                         $target_media = [];
                        $selected_media = [];
                         
                         if($dcontext !="" || $dcontext != " "){
                             
                             foreach($search_unique as $media_program){
                            // if($media != "" || $media != " "){
                              if($this->isProgramWithMediaServiceWithinThisContext($media_program,$dcontext)){
                                  $context_filtered_media[]= $media_program;
                              }
                            // }
                          }
                           //load the data for this toolboxes/media
                        
                        foreach($context_filtered_media as $searching){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='id=:id';
                            $criteria->params = array(':id'=>$searching);
                            $active_media = Resourcegroup::model()->find($criteria); 
                            $target_media[] = $active_media;
                            $selected_media[]=$active_media['id'];
                            
                          //set the latest pricing for this media/toolbox resources
                         foreach($selected_media as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                        }
                         }else{
                              //load the data for this toolboxes/media
                            
                            foreach($search_unique as $searching){
                                $criteria = new CDbCriteria();
                                $criteria->select = '*';
                                $criteria->condition='id=:id';
                                $criteria->params = array(':id'=>$searching);
                                $active_media = Resourcegroup::model()->find($criteria); 
                                $target_media[] = $active_media; 
                                $selected_media[]=$active_media['id'];
                            } 
                         }
                         
                        //set the latest pricing for this media/toolbox resources
                    foreach($selected_media as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                        
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                                "toolbox" =>$target_media,
                                "tester"=>"just testing this control",
                                "searchables"=>$searchable_items,
                                "domain"=>$domain_id,
                                "product"=>$product_id,
                               // "result"=>$result['id'],
                                "usables"=>$usable_media,
                                "search_unique"=>$search_unique,
                                "string"=>$search_string,
                                "words"=>$searchWords,
                                "word"=>$word
                                
                              
                               
                               
                            ));
                        
                          
                          
                      }else{
                                       
                            //find the unique toolboxes for this search
                         $search_unique = array_unique($usable_media);
                  
                         //load the data for this toolboxes/media
                        $target_media = [];
                        $selected_media = [];
                        foreach($search_unique as $searching){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='id=:id';
                            $criteria->params = array(':id'=>$searching);
                            $active_media = Resourcegroup::model()->find($criteria); 
                            $target_media[] = $active_media;
                            $selected_media[] = $active_media['id'];
                        }
                        
                    //set the latest pricing for this media/toolbox resources
                    foreach($selected_media as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                                "toolbox" =>$target_media,
                                "searching"=>$search_unique,
                                "usable"=>$usable_media,
                                "context"=>"no set",
                                "string"=>$search_string,
                                "words"=>$searchWords,
                                "word"=>$word
                            ));
                        }
                        
                    
                }else{
                     if($this->isContextParameterAnInfluencer($context)){
                            //get all the media within the given context
                        
                        $target_media = [];
                        $selected_media = [];
                        
                        //get the resources that are in that context
                        if(($product_id !== "")){
                            if($product_id !== 0){
                                $alltoolboxes = $this->getAllProgramAndEventMediaBelongingToThisDomainInThisProduct($product_id, $domain_id); 
                            }else{
                                $alltoolboxes = $this->getAllProgramAndEventsBelongingToThisDomain($domain_id);
                            }
                           
                        }else{
                            $alltoolboxes = $this->getAllProgramAndEventsBelongingToThisDomain($domain_id);
                        }
                        foreach($alltoolboxes as $program){
                                    if($this->isProgramWithMediaServiceWithinThisContext($program,$dcontext)){
                                        $criteria = new CDbCriteria();
                                        $criteria->select = '*';
                                        $criteria->condition='id=:id';
                                        $criteria->params = array(':id'=>$program);
                                        $active_toolboxes = Resourcegroup::model()->find($criteria); 
                                        $target_media[] = $active_toolboxes;
                                        $selected_media[] = $active_toolboxes['id'];
                            }
                                
                        }
                    //set the latest pricing for this media/toolbox resources
                    foreach($selected_media as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                        
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                                "toolbox" =>$target_media,
                                "string"=>$search_string,
                                "words"=>$searchWords
                               
                            ));
                        
                            
             }
                    
          }
                
            }else if($this->contextParameterIsTheOnlyInfluencer($search_string,$keyword,$actor,$designation,$context)){
                
                if(($product_id !== "")){
                    
                    foreach($searchWords as $word){
                        $word = preg_replace('/\s/', '', $word);
                        $q = "SELECT id FROM resourcegroup" ;
                        $cmd = Yii::app()->db->createCommand($q);
                        $results = $cmd->query();
                        foreach($results as $result){
                          // if($this->isProgramWithMediaServiceWithinThisContext($result['id'],$dcontext)){
                                if($this->toolboxHasAToolBelongingToThisProduct($result['id'],$product_id)){
                                    if($this->isMediaInComplianceWithThisStorePolicy($result['id'], $store_id)){
                                        $searchable_items[] = $result['id'];
                                  }
                           // }
                           }
                           
                            
                    } 
                    
                } 
                }else{
                    foreach($searchWords as $word){
                        $word = preg_replace('/\s/', '', $word);
                        $q = "SELECT id FROM resourcegroup" ;
                        $cmd = Yii::app()->db->createCommand($q);
                        $results = $cmd->query();
                        foreach($results as $result){
                           // if($this->isProgramWithMediaServiceWithinThisContext($result['id'],$dcontext)){
                                if($this->isMediaInComplianceWithThisStorePolicy($result['id'], $store_id)){
                                    $searchable_items[] = $result['id'];
                             }
                           // } 
                            
                        
                    } 
                    
                }
                //get the media within this context
                if($this->isContextParameterAnInfluencer($context)){
                          $context_filtered_media =  [];
                           //find the unique toolboxes for this search
                         $search_unique = array_unique($usable_media);
                         $target_media = [];
                        $selected_media = [];
                         
                         if($dcontext !="" || $dcontext != " "){
                             
                             foreach($search_unique as $media_program){
                            // if($media != "" || $media != " "){
                              if($this->isProgramWithMediaServiceWithinThisContext($media_program,$dcontext)){
                                  $context_filtered_media[]= $media_program;
                              }
                            // }
                          }
                           //load the data for this toolboxes/media
                        
                        foreach($context_filtered_media as $searching){
                            $criteria = new CDbCriteria();
                            $criteria->select = '*';
                            $criteria->condition='id=:id';
                            $criteria->params = array(':id'=>$searching);
                            $active_media = Resourcegroup::model()->find($criteria); 
                            $target_media[] = $active_media;
                            $selected_media[]=$active_media['id'];
                            
                          //set the latest pricing for this media/toolbox resources
                         foreach($selected_media as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                        }
                         }else{
                              //load the data for this toolboxes/media
                            
                            foreach($search_unique as $searching){
                                $criteria = new CDbCriteria();
                                $criteria->select = '*';
                                $criteria->condition='id=:id';
                                $criteria->params = array(':id'=>$searching);
                                $active_media = Resourcegroup::model()->find($criteria); 
                                $target_media[] = $active_media; 
                                $selected_media[]=$active_media['id'];
                            } 
                         }
                         
                        //set the latest pricing for this media/toolbox resources
                    foreach($selected_media as $derived){
                        if($this->isToolboxPricingDerivable($derived)){
                                $derived_price = $this->getThisToolboxAmount($derived);
                                //update the corresponding  toolbox
                                $this->updateThisToolboxPricing($derived,$derived_price);
                        }
                    }
                        
                        header('Content-Type: application/json');
                            echo CJSON::encode(array(
                                "success" => mysql_errno() == 0,
                                "toolbox" =>$target_media,
                                "tester"=>"just testing this control",
                                "searchables"=>$searchable_items,
                                "domain"=>$domain_id,
                                "product"=>$product_id,
                               // "result"=>$result['id'],
                                "usables"=>$usable_media,
                                "search_unique"=>$search_unique,
                                "string"=>$search_string,
                                "words"=>$searchWords,
                                "word"=>$word
                                
                              
                               
                               
                            ));
                        
                          
                          
                      }
                }
            }
                
            }
            
            
        }
        
        
        /**
         * This is the function that retrieves a store type
         */
        public function getThisStoreType($store_id){
            
                $criteria9 = new CDbCriteria();
                $criteria9->select = '*';
                $criteria9->condition='id=:id';
                $criteria9->params = array(':id'=>$store_id);
                $type= Stores::model()->find($criteria9);
                
                return $type['type'];
            
        }
        
        
        /**
         * This is the function that gets the domain owner of a store
         */
        public function getTheDomainOwnerOfThisStore($store_id){
            
                $criteria9 = new CDbCriteria();
                $criteria9->select = '*';
                $criteria9->condition='id=:id';
                $criteria9->params = array(':id'=>$store_id);
                $domain= Stores::model()->find($criteria9);
                
                return $domain['domain_id'];
        }
        
        /**
         * This is the function that retrieves all programs & events on the platform
         */
        public function getAllProgramAndEventMediaBelongingToThisProductOnThisPlatform($product_id, $store_id){
            
                     $criteria8 = new CDbCriteria();
                     $criteria8->select = '*';
                    // $criteria8->condition='domain_id=:id';
                     //$criteria8->params = array(':id'=>$domain_id);
                     $resources= Resourcegroup::model()->findAll($criteria8);
                                          
                     $selected_media = [];
                     foreach($resources as $media){
                         if($this->toolboxHasAToolBelongingToThisProduct($media['id'],$product_id)){
                             if($this->isMediaInComplianceWithThisStorePolicy($media['id'], $store_id)){
                                  $selected_media[] = $media['id'];
                             }
                            
                         }
                     }
                     
                     return $selected_media;
            
        }
        
        /**
         * This is the function that gets all store compliant media programs on the platform
         */
        public function getAllProgramAndEventsBelongingToThisPlatform($store_id){
            
                     $criteria8 = new CDbCriteria();
                     $criteria8->select = '*';
                     //$criteria8->condition='domain_id=:id and visibility=:visibility';
                     //$criteria8->params = array(':id'=>$domain_id,':visibility'=>'reserved');
                     $resources= Resourcegroup::model()->findAll($criteria8);
                                          
                     $selected_media = [];
                     foreach($resources as $media){
                     if($this->isMediaInComplianceWithThisStorePolicy($media['id'], $store_id)){
                                  $selected_media[] = $media['id'];
                           }
                            
                         
                     }
                   
                     
                     return $selected_media;
            
        }
        
        
        /**
         * This is the function that gets all programe & events on the platform
         */
        public function getAllProgramsAndEventsInThePlatform(){
            
                $programs = [];
            
                $criteria9 = new CDbCriteria();
                $criteria9->select = '*';
                $criteria9->condition='visibility!=:visibility';
                 $criteria9->params = array(':visibility'=>'reserved');
                $media_programs= Resourcegroup::model()->findAll($criteria9);
                
                foreach($media_programs as $media){
                    $programs[] = $media['id'];
                    
                }
            
                return $programs;
            
        }
        
        
        
        /**
         * This is the function that determines if a resource/media/toolbox meets a stores requirements
         */
        public function isMediaInComplianceWithThisStorePolicy($program_id, $store_id){
            
            //get the vissbility of this program
            $program_visibility = $this->getThisProgramVisibility($program_id);
            
            if($program_visibility == 'restricted_public' && $this->isPermitRestrictedPublicVisibilityResources($store_id)){
                return true;
            }else if($program_visibility == 'public' && $this->isPermitPublicVisibilityResources($store_id)){
                return true;
            }else if($program_visibility == 'private' && $this->isPermitPrivateVisibilityResources($store_id)){
                return true;
            }else if($program_visibility == 'private & restricted_public' && $this->isPermitPrivateAndRestrictedPublicVisibilityResources($store_id)){
                return true;
            }else if($program_visibility == 'private & public' && $this->isPermitPrivateAndPublicVisibilityResources($store_id)){
                return true;
            }else{
                return false;
            }
            
        }
        
        /**
         * This is the function that determines the visibility of a resourcegroup/program/toolbox
         */
        public function getThisProgramVisibility($program_id){
            
                $criteria9 = new CDbCriteria();
                $criteria9->select = '*';
                $criteria9->condition='id=:id';
                $criteria9->params = array(':id'=>$program_id);
                $media= Resourcegroup::model()->find($criteria9);
                
                return $media['visibility'];
            
        }
        
        /**
         * This is the function that confirms if retricted public visibility resources are allowed in a store
         */
        public function isPermitRestrictedPublicVisibilityResources($store_id){
            
            $criteria9 = new CDbCriteria();
                $criteria9->select = '*';
                $criteria9->condition='id=:id';
                $criteria9->params = array(':id'=>$store_id);
                $media= Stores::model()->find($criteria9);
                
                if($media['make_restricted_public_toolboxes_available'] == 1){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        /**
         * This is the function that confirms if public visibility resources are allowed in a store
         */
        public function isPermitPublicVisibilityResources($store_id){
            
                $criteria9 = new CDbCriteria();
                $criteria9->select = '*';
                $criteria9->condition='id=:id';
                $criteria9->params = array(':id'=>$store_id);
                $media= Stores::model()->find($criteria9);
                
                if($media['make_public_toolboxes_available'] == 1){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
         /**
         * This is the function that confirms if private visibility resources are allowed in a store
         */
        public function isPermitPrivateVisibilityResources($store_id){
            
                $criteria9 = new CDbCriteria();
                $criteria9->select = '*';
                $criteria9->condition='id=:id';
                $criteria9->params = array(':id'=>$store_id);
                $media= Stores::model()->find($criteria9);
                
                if($media['make_private_toolboxes_available'] == 1){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        /**
         * This is the function that confirms if private & restricted visibility resources are allowed in a store
         */
        public function isPermitPrivateAndRestrictedPublicVisibilityResources($store_id){
            
                $criteria9 = new CDbCriteria();
                $criteria9->select = '*';
                $criteria9->condition='id=:id';
                $criteria9->params = array(':id'=>$store_id);
                $media= Stores::model()->find($criteria9);
                
                if($media['make_private_toolboxes_available'] == 1 && $media['make_restricted_public_toolboxes_available']== 1){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        /**
         * This is the function that confirms if private & restricted visibility resources are allowed in a store
         */
        public function isPermitPrivateAndPublicVisibilityResources($store_id){
            
                $criteria9 = new CDbCriteria();
                $criteria9->select = '*';
                $criteria9->condition='id=:id';
                $criteria9->params = array(':id'=>$store_id);
                $media= Stores::model()->find($criteria9);
                
                if($media['make_private_toolboxes_available'] == 1 && $media['make_public_toolboxes_available']==1){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        /**
         * This is the policy that determines a  duplicate toolbox with restricted public visibility
         */
        public function isThisToolboxADuplicateWithRestrictedPublic($toolbox_id,$domain_id){
            
              $cmd =Yii::app()->db->createCommand();
                $cmd->select('COUNT(*)')
                    ->from('resourcegroup')
                    ->where("id = $toolbox_id && (is_duplicate =1 && visibility='restricted_public')");
                $result = $cmd->queryScalar();
                
                if($result > 0){
                    if($this->determineDomainStorePolicyOnDuplicateRestrictedPublicToolboxes($domain_id)){
                        return true;
                    }else{
                        return false;
                    }
                    
                }else{
                    return false;
                }
            
            
        }
        
        
        /**
         * This is the policy that determines a  duplicate toolbox with restricted public visibility
         */
        public function isThisToolboxADuplicateWithPrivateAndRestrictedPublic($toolbox_id,$domain_id){
            
             $cmd =Yii::app()->db->createCommand();
                $cmd->select('COUNT(*)')
                    ->from('resourcegroup')
                    ->where("id = $toolbox_id && (is_duplicate =1 && visibility='private & restricted_public')");
                $result = $cmd->queryScalar();
                
                if($result > 0){
                    if($this->determineDomainStorePolicyOnDuplicatePrivateAndRestrictedPublicToolboxes($domain_id)){
                        return true;
                    }else{
                        return false;
                    }
                    
                }else{
                    return false;
                }
            
            
        }
        
        /**
         * This is the policy that determines a  toolbox with public visibility
         */
        public function isThisToolboxWithPublicVisibility($toolbox_id,$domain_id){
            
             $cmd =Yii::app()->db->createCommand();
                $cmd->select('COUNT(*)')
                    ->from('resourcegroup')
                    ->where("(id = $toolbox_id && visibility='public') || (id = $toolbox_id && visibility='private & public')");
                $result = $cmd->queryScalar();
                
                if($result > 0){
                    if($this->determineDomainStorePolicyOnPublicToolboxes($domain_id)){
                        return true;
                    }else{
                        return false;
                    }
                    
                }else{
                    return false;
                }
            
            
        }
        
        
        /**
         * This is the policy that determines  a  toolbox with restricted public visibility
         */
        public function isThisToolboxWithRestictedPublicVisibility($toolbox_id,$domain_id){
            
             $cmd =Yii::app()->db->createCommand();
                $cmd->select('COUNT(*)')
                    ->from('resourcegroup')
                    ->where("(id = $toolbox_id && visibility='restricted_public') || (id = $toolbox_id && visibility='private & restricted_public')");
                $result = $cmd->queryScalar();
                
                if($result > 0){
                    if($this->determineDomainStorePolicyOnRestrictedPublicToolboxes($domain_id)){
                        return true;
                    }else{
                        return false;
                    }
                    
                }else{
                    return false;
                }
            
            
        }
        
        /**
         * This is the policy that determines  a  toolbox with private visibility
         */
        public function isThisToolboxWithPrivateVisibility($toolbox_id,$domain_id){
            
            $cmd =Yii::app()->db->createCommand();
                $cmd->select('COUNT(*)')
                    ->from('resourcegroup')
                    ->where("(id = $toolbox_id && visibility='private') || (id = $toolbox_id && visibility='private & restricted_public') || (id = $toolbox_id && visibility='private & public') ");
                $result = $cmd->queryScalar();
                
                if($result > 0){
                    if($this->determineDomainStorePolicyOnPrivateToolboxes($domain_id)){
                        return true;
                    }else{
                        return false;
                    }
                    
                }else{
                    return false;
                }
            
            
        }
        
        
        /**
         * This is the function that determines if a toolbox is in compliance with domain policy
         */
        public function isToolboxInComplianceWithDomainPolicy($toolbox_id, $domain_id){
            
           $duplicate_and_restricted =  $this->isThisToolboxADuplicateWithRestrictedPublic($toolbox_id,$domain_id);
           $duplicate_private_and_restricted = $this->isThisToolboxADuplicateWithPrivateAndRestrictedPublic($toolbox_id,$domain_id);
           $public = $this->isThisToolboxWithPublicVisibility($toolbox_id,$domain_id); 
           $restricted_public = $this->isThisToolboxWithRestictedPublicVisibility($toolbox_id,$domain_id);
           $private = $this->isThisToolboxWithPrivateVisibility($toolbox_id,$domain_id);
           
           if(($duplicate_and_restricted === true ||$duplicate_private_and_restricted ===true) || ($public === true ||$restricted_public == true || $private ===true) ){
               
              return true; 
              
           }else{
               return false;
             
           }
        }
        
        /**
         * This is the function that determines a domain store policy on duplicate  restricted public toolboxes on Partner store
         */
        public function determineDomainStorePolicyOnDuplicateRestrictedPublicToolboxes($domain_id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='domain_id=:id and status=:status';
            $criteria->params = array(':id'=>$domain_id,':status'=>'active');
            $policy = DomainPolicy::model()->find($criteria); 
            
            if($policy['allow_duplicate_toolboxes_with_restricted_public_as_partners'] == 1){
                return true;
                
            }else{
               return false;
               
            }
        }
        
        
        /**
         * This is the function that determines a domain store policy on public toolboxes on Partner store
         */
        public function determineDomainStorePolicyOnPublicToolboxes($domain_id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='domain_id=:id and status=:status';
            $criteria->params = array(':id'=>$domain_id,':status'=>'active');
            $policy = DomainPolicy::model()->find($criteria); 
            
            if($policy['make_public_toolboxes_visible_in_partnership'] == 1){
                return true;
            }else{
                return false;
            }
        }
        
        /**
         * This is the function that determines a domain store policy on restricted public toolboxes on Partner store
         */
        public function determineDomainStorePolicyOnRestrictedPublicToolboxes($domain_id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='domain_id=:id and status=:status';
            $criteria->params = array(':id'=>$domain_id,':status'=>'active');
            $policy = DomainPolicy::model()->find($criteria); 
            
            if($policy['make_restricted_public_toolboxes_visible_in_partnership'] == 1){
                return true;
            }else{
                return false;
            }
        }
        
        /**
         * This is the function that determines a domain store policy on private toolboxes on Partner store
         */
        public function determineDomainStorePolicyOnPrivateToolboxes($domain_id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='domain_id=:id and status=:status';
            $criteria->params = array(':id'=>$domain_id,':status'=>'active');
            $policy = DomainPolicy::model()->find($criteria); 
            
            if($policy['make_private_toolboxes_visible_in_partnership'] == 1){
                return true;
            }else{
                return false;
            }
        }
        
        
        
        /**
         * This is the function that determines a domain store policy on private and restricted public toolboxes on Partner store
         */
        public function determineDomainStorePolicyOnDuplicatePrivateAndRestrictedPublicToolboxes($domain_id){
            
            $criteria = new CDbCriteria();
            $criteria->select = '*';
            $criteria->condition='domain_id=:id and status=:status';
            $criteria->params = array(':id'=>$domain_id,':status'=>'active');
            $policy = DomainPolicy::model()->find($criteria); 
            
            if($policy['allow_duplicate_toolboxes_restricted_private_as_partner_private'] == 1){
                return true;
            }else{
                return false;
            }
        }
        
      
        
        /**
         * This is the function that determines a toolbox is in a network
         */
        public function isToolboxInNetwork($network_id,$toolbox_id){
            
             $cmd =Yii::app()->db->createCommand();
                $cmd->select('COUNT(*)')
                    ->from('network_has_toolboxes')
                    ->where("network_id = $network_id && toolbox_id =$toolbox_id");
                $result = $cmd->queryScalar();
                
                if($result > 0){
                    return true;
                }else{
                    return false;
                }
            
            
        }
        
        /**
         * This is the function that determines the domain of a network
         */
        public function getTheNetworkDomain($network_id){
                     $criteria = new CDbCriteria();
                     $criteria->select = '*';
                     $criteria->condition='id=:id';
                     $criteria->params = array(':id'=>$network_id);
                     $domain= Network::model()->find($criteria);
                     
                     return $domain['domain_id'];
            
            
        }
        
        /**
         * This is the function that gets the toolboxes in a network
         */
        public function getNetworkToolboxes($network_id){
            
                    $criteria = new CDbCriteria();
                     $criteria->select = '*';
                     $criteria->condition='network_id=:id';
                     $criteria->params = array(':id'=>$network_id);
                     $toolboxes= NetworkHasToolboxes::model()->findAll($criteria);
                     
                     $alltoolboxes = [];
                     foreach($toolboxes as $toolbox){
                         $alltoolboxes[] = $toolbox['toolbox_id'];
                     }
                     
                     return $alltoolboxes;
                     
        }
        
        /**
         * This is the function that retrieves all domain toolboxes that do not have reserved visibility
         */
        public function getAllToolboxesBelongingToThisDomain($domain_id){
                    $criteria = new CDbCriteria();
                     $criteria->select = '*';
                     $criteria->condition='domain_id=:domainid and visibility!=:visibility';
                     $criteria->params = array(':domainid'=>$domain_id,':visibility'=>'reserved');
                     $toolboxes= Resourcegroup::model()->findAll($criteria);
                     
                     $alltoolboxes = [];
                     foreach($toolboxes as $toolbox){
                         $alltoolboxes[] = $toolbox['id'];
                     }
                     
                     return $alltoolboxes;
            
        }
        
        /**
         * This is the function that determines if a toolbox has at least a tool with a particular produict
         */
        public function toolboxHasAToolBelongingToThisProduct($toolbox_id,$product_id){
            //spool all the tools in this toolbox
            $tools = $this->getAllToolsInThisToolbox($toolbox_id);
            
            $truth_count = 0;
            
            foreach($tools as $tool){
                if($this->isThisToolForThisProduct($tool,$product_id)){
                    $truth_count = $truth_count + 1;
                }
            }
            if($truth_count >0){
                return true;
            }else{
                return false;
            }
            
            
        }
        
        /**
         * This is the function that gets all tools in a toolbox
         */
        public function getAllToolsInThisToolbox($toolbox_id){
            
                    $criteria = new CDbCriteria();
                     $criteria->select = '*';
                     $criteria->condition='resourcegroup_id=:groupid';
                     $criteria->params = array(':groupid'=>$toolbox_id);
                     $tools= ResourceHasResourcegroups::model()->findAll($criteria);
                     
                     $alltools = [];
                     foreach($tools as $tool){
                         $alltools[] = $tool['resource_id']; 
                     }
                     return $alltools;
            
        }
        
        /**
         * This is the function that determines if a tool belonge to a product
         */
        public function isThisToolForThisProduct($tool_id,$product_id){
            
            if($product_id == 0){
                
                $cmd =Yii::app()->db->createCommand();
                $cmd->select('COUNT(*)')
                    ->from('resources')
                    ->where("id = $tool_id && product_id !=$product_id");
                $result = $cmd->queryScalar();
                
                if($result > 0){
                    return true;
                }else{
                    return false;
                }
            }else{
                
                $cmd =Yii::app()->db->createCommand();
                $cmd->select('COUNT(*)')
                    ->from('resources')
                    ->where("id = $tool_id && product_id =$product_id");
                $result = $cmd->queryScalar();
                
                if($result > 0){
                    return true;
                }else{
                    return false;
                }
            }
            
            
            
        }
        
        /*
         * This is the function that determines if a cart is empty
         */
        public function isUserCartEmpty(){
            $user_id = Yii::app()->user->id;
            //retrieve the user's open order id
            $order_id = $this->getTheUsersOpenOrder($user_id);
            
            //verify if user cart is empty
            if($this->isCartEmpty($order_id)){
                return false;
                
            }else{
                return true;
            }
        }
        
        /**
         * This is the function that gets a users open order or return 0 if there are no open order
         */
        public function getTheUsersOpenOrder($user_id){
                $cmd =Yii::app()->db->createCommand();
                $cmd->select('COUNT(*)')
                    ->from('order')
                    ->where("order_initiated_by = $user_id && status ='open'");
                $result = $cmd->queryScalar();
                
                if($result > 0){
                    //fetch the order id
                    $order_id = $this->getTheOpenOrderId($user_id);
                    return $order_id;
                }else{
                    return 0;
                }
                     
        }
        
        /**
         * This is the function that verifies if an order cart is empty or not
         * 
         */
        public function isCartEmpty($order_id){
            
            $cmd =Yii::app()->db->createCommand();
            $cmd->select('COUNT(*)')
                    ->from('order_has_toolboxes')
                    ->where("order_id = $order_id");
                $result = $cmd->queryScalar();
                
                if($result <= 0){
                    return true;
                }else{
                    return false;
                }
        }
        
        /**
         * This is the function that returns the a user's open order
         */
        public function getTheOpenOrderId($user_id){
            
                     $criteria = new CDbCriteria();
                     $criteria->select = '*';
                     $criteria->condition='order_initiated_by=:userid';
                     $criteria->params = array(':userid'=>$user_id);
                     $order= Order::model()->find($criteria);
                     
                     return $order['id'];
            
            
        }
        
        /**
         * This is the function that retrieves the exchange rate of the currency of a store
         */
        public function actionretrieveTheExchangeRateForThisStore(){
            
             $store_id = $_REQUEST['store_id'];
            //$store_id = 3;
            
            //get the country of this store
            $country_id = $this->getTheStoreCountryId($store_id);
            
            //get the currency id of this country
            $currency_id = $this->getTheCurrencyIdOfCountry($country_id);
            
            //get the exchange rate of this currency
            $exchange_rate = $this->getTheCurrencyExchangeRate($currency_id);
            
            //get the currency code of this currency
            
            $currency_code = $this->getThisCurrencyCode($currency_id);
            
             header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "exchange_rate" => $exchange_rate,
                           "store"=>$store_id,
                           "currency_code"=>$currency_code
                          
                           
                           
                          
                       ));
            
            
        }
        
        /**
         * This is the function that gets the country of a store
         */
        public function getTheStoreCountryId($store_id){
            
                     $criteria = new CDbCriteria();
                     $criteria->select = '*';
                     $criteria->condition='id=:id';
                     $criteria->params = array(':id'=>$store_id);
                     $store= Stores::model()->find($criteria);
                     
                     return $store['country_id'];
        }
        
        
        /**
         * This is the function that gets the currency id of a country
         */
        public function getTheCurrencyIdOfCountry($country_id){
            
                    $criteria = new CDbCriteria();
                     $criteria->select = '*';
                     $criteria->condition='country_id=:id';
                     $criteria->params = array(':id'=>$country_id);
                     $currency= Currencies::model()->find($criteria);
                     
                     return $currency['id'];
            
            
        }
        
        /**
         * This is the function that gets the currency exchange rate
         */
        public function getTheCurrencyExchangeRate($currency_id){
            
            //$exchange_rate = 125.25;
            //get the platform base currency id
            $base_currency = $this->getThePlatformBaseCurrency();
            $exchange_rate = (double)$this->retrieveTheExchangeRateForThisCurrency($base_currency,$currency_id);
            
            return $exchange_rate;
            
            
        }
        
        /**
         * This is the function that retrieves the platform's base currency
         */
        public function getThePlatformBaseCurrency(){
                     $criteria = new CDbCriteria();
                     $criteria->select = '*';
                    // $criteria->condition='country_id=:id';
                   //  $criteria->params = array(':id'=>$country_id);
                     $platform= PlatformSettings::model()->find($criteria);
                     
                     return $platform['platform_default_currency_id'];
            
        }
        
        /**
         * This is the function that retrieves the exchange rate for a currency against the base currency
         */
        public function retrieveTheExchangeRateForThisCurrency($base_currency,$currency_id){
            
            if( $base_currency !==$currency_id ){
                   $criteria = new CDbCriteria();
                   $criteria->select = '*';
                   $criteria->condition='base_currency_id=:baseid and currency_id=:currencyid';
                   $criteria->params = array(':baseid'=>$base_currency,':currencyid'=>$currency_id);
                   $exchange_rate= CurrencyExchange::model()->find($criteria);
                     
                    return $exchange_rate['exchange_rate'];
                  
            }else{
                $exchange_rate = 1.00;
                return $exchange_rate;
            }       
            
            
        }
        
        /**
         * This is the function that gets the default store id during initialization
         */
        public function actiongetStoreAndCurrencyParameters(){
                                
              //get the logged in user
              $user_id = Yii::app()->user->id;
              
              //get the domain of the logged in user
              $domain_id = $this->determineAUserDomainIdGiven($user_id);
              
              //get the platform base currency
              $base_currency = $this->getThePlatformBaseCurrency();
              
              //get he base currency code
              $base_currency_code = $this->getThisCurrencyCode($base_currency);
              
              //get the operating country of this domain
              $country_id = $this->getTheCountryOfThisDomain($domain_id);
              
              if($this->isStoreAvailableForThisCountry($country_id)){
                  $store_id = $this->getTheStoreIdOfThisCountry($country_id);
                  
                  $currency_id = $this->getThePreferredCurrencyIdOfThisDomain($domain_id);
                  $currency_code = $this->getThisCurrencyCode($currency_id);
                  
                  $exchange_rate = $this->getTheCurrencyExchangeRate($currency_id);
              }else{
                  $store_id = $this->getThePlatformDefaultStore();
                  
                  $currency_code = $this->getThisCurrencyCode($base_currency);
                  $exchange_rate = $this->getTheCurrencyExchangeRate($base_currency);
              }
              
              
                     
                   header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "store_id" =>$store_id,
                           "currency_code"=>$currency_code,
                           "exchange_rate"=>(double)$exchange_rate,
                           "base_currency"=>$base_currency,
                           "currency_id"=>$currency_id,
                           "base_currency_code"=>$base_currency_code
                          
                       ));
            
        }
        
        /**
         * This is the function that gets the preferred currency id of country
         */
        public function getThePreferredCurrencyIdOfThisDomain($domain_id){
                    $criteria = new CDbCriteria();
                     $criteria->select = '*';
                     $criteria->condition='domain_id=:id and status="active"';
                     $criteria->params = array(':id'=>$domain_id);
                     $domain= DomainPolicy::model()->find($criteria);
                     
                     return $domain['domain_currency_preference'];
            
        }
        
        /**
         * This is the function that determines the country of this domain
         */
        public function getTheCountryOfThisDomain($domain_id){
            
           
                    $criteria = new CDbCriteria();
                     $criteria->select = '*';
                     $criteria->condition='id=:id';
                     $criteria->params = array(':id'=>$domain_id);
                     $domain= Resourcegroupcategory::model()->find($criteria);
                     
                     return $domain['country_id'];
            
        }
        
        
        /**
         * This is the function that verifies if store is available for a country
         */
        public function isStoreAvailableForThisCountry($country_id){
            
            $cmd =Yii::app()->db->createCommand();
            $cmd->select('COUNT(*)')
                    ->from('stores')
                    ->where("country_id = $country_id");
                $result = $cmd->queryScalar();
                
                if($result > 0){
                    return true;
                }else{
                    return false;
                }
            
        }
        
        
        /**
         * This is the function that retrieves the store id of a country
         */
        public function getTheStoreIdOfThisCountry($country_id){
                    $criteria = new CDbCriteria();
                     $criteria->select = '*';
                     $criteria->condition='country_id=:id';
                     $criteria->params = array(':id'=>$country_id);
                     $store= Stores::model()->find($criteria);
                     
                     return $store['id'];
            
        }
        
        /**
         * This is the function that gets a currency code
         */
        public function getThisCurrencyCode($currency_id){
            
                    $criteria = new CDbCriteria();
                     $criteria->select = '*';
                     $criteria->condition='id=:id';
                     $criteria->params = array(':id'=>$currency_id);
                     $currency= Currencies::model()->find($criteria);
                     
                     return $currency['currency_code'];
            
        }
        
        
        /**
         * This is a function that determines if a user has a particular privilege assigned to him
         */
        public function determineIfAUserHasThisPrivilegeAssigned($userid, $privilegename){
            
             $allprivileges = [];
            //spool all the privileges assigned to a user
                $criteria7 = new CDbCriteria();
                $criteria7->select = 'itemname, userid';
                $criteria7->condition='userid=:userid';
                $criteria7->params = array(':userid'=>$userid);
                $priv= AuthAssignment::model()->find($criteria7);
                
                //retrieve all the children of the role
                
                $criteria = new CDbCriteria();
                $criteria->select = 'child';
                $criteria->condition='parent=:parent';
                $criteria->params = array(':parent'=>$priv['itemname']);
                $allprivs= Authitemchild::model()->findAll($criteria);
                 
                //check to see if this privilege exist for this user
                foreach($allprivs as $pris){
                    if($this->privilegeType($pris['child'])== 0){
                        $allprivileges[] = $pris['child'];
                        
                    }elseif($this->privilegeType($pris['child'])== 1){
                        
                       $allprivileges[] = $this->retrieveAllTaskPrivileges($pris['child']); 
                    }elseif($this->privilegeType($pris['child'])== 2){
                        
                        $allprivileges[] = $this->retrieveAllRolePrivileges($pris['child']);
                    }
                    
                    
                    
                    
                }
               
                
                if(in_array($privilegename, $allprivileges)){
                    
                    return true;
                     
                }else{
                    
                    return false;
                     
                }
      
        }
        
        
          /**
         * This is the function that determines a privilege type
         */
        public function privilegeType($privname){
            
            $criteria7 = new CDbCriteria();
                $criteria7->select = 'name, type';
                $criteria7->condition='name=:name';
                $criteria7->params = array(':name'=>$privname);
                $privs= Authitem::model()->find($criteria7);
                
                return $privs['type'];
                
                
        }
        
        
         /**
         * This is the function that returns all member privileges of a task
         */
        public function retrieveAllTaskPrivileges($task){
            
            $member = [];
            
                $criteria = new CDbCriteria();
                $criteria->select = 'child';
                $criteria->condition='parent=:parent';
                $criteria->params = array(':parent'=>$task);
                $allprivs= Authitemchild::model()->findAll($criteria);
                
                foreach($allprivs as $privs){
                    if($this->privilegeType($privs['child'])== 0){
                         $member[] = $privs['child'];
                        
                    }elseif($this->privilegeType($privs['child'])== 1){
                        
                        $member[] = $this->retrieveAllTaskPrivileges($privs['child']); 
                    }
                   
                    
                }
              return $member;
               
            
        }
        
        /**
         * This is the function that returns all members in a role
         */
        public function retrieveAllRolePrivileges($role){
            
            $member = [];
            
                $criteria = new CDbCriteria();
                $criteria->select = 'child';
                $criteria->condition='parent=:parent';
                $criteria->params = array(':parent'=>$role);
                $allprivs= Authitemchild::model()->findAll($criteria);
                
                foreach($allprivs as $privs){
                    if($this->privilegeType($privs['child'])== 0){
                         $member[] = $privs['child'];
                        
                    }elseif($this->privilegeType($privs['child'])== 1){
                        
                        $member[] = $this->retrieveAllTaskPrivileges($privs['child']); 
                    }elseif($this->privilegeType($privs['child'])== 2){
                        
                        $member[] = $this->retrieveAllRolePrivileges($privs['child']); 
                    }
                   
                    
                }
              return $member;
                
            
        }
        
        /**
         * This is the function that list a partners or networks relevant toolboxes
         */
        public function actionListThisPartnerOrNetworkToolboxes(){
            
          $partner_id = $_REQUEST['partner_id'];
          
          $network_id = $_REQUEST['network_id'];
            
            $all_required_toolboxes = [];
            
          if($partner_id > 0){
              
                
                $partner_id = $_REQUEST['partner_id'];
                //retrieve all toolboxes for this partner
                $partner_toolboxes = $this->retrieveAllToolboxesForThisPartner($partner_id);
                
                               
                //retrieve only toolboxes that is in compliance with partner domain policy
                foreach($partner_toolboxes as $toolbox_id){
                    if($this->isToolboxInComplianceWithDomainPolicy($toolbox_id, $partner_id)){
                        $all_required_toolboxes[] = $toolbox_id;
                    }
                }
                
                //get th detail of the toolboxes
            $legitimate_toolboxes = [];
            
            foreach($all_required_toolboxes as $required){
                $criteria1 = new CDbCriteria();
                $criteria1->select = '*';
                $criteria1->condition='id=:id';
                $criteria1->params = array(':id'=>$required);
                $toolbox = Resourcegroup::model()->find($criteria1);
                
                $legitimate_toolboxes[] = $toolbox;
            }
            
            if($all_required_toolboxes===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "toolbox" => $legitimate_toolboxes
                          
                           
                           
                          
                       ));
                       
                }
       
           }else if($network_id > 0){
            
                $network_id = $_REQUEST['network_id'];
                //retrieve all toolbox for this network
                $all_required_toolboxes = $this->retrieveAllToolboxesForThisNetwork($network_id);
                
                //get th detail of the toolboxes
            $legitimate_toolboxes = [];
            
            foreach($all_required_toolboxes as $required){
                $criteria1 = new CDbCriteria();
                $criteria1->select = '*';
                $criteria1->condition='id=:id';
                $criteria1->params = array(':id'=>$required);
                $toolbox = Resourcegroup::model()->find($criteria1);
                
                $legitimate_toolboxes[] = $toolbox;
            }
            
            if($all_required_toolboxes===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "toolbox" => $legitimate_toolboxes
                          
                           
                           
                          
                       ));
                       
                }
            }
            
            
            
            
        }
        
        
        /**
         * This is the function that retrieves all toolboxes for a domain
         */
        public function retrieveAllToolboxesForThisPartner($domain_id){
            
                
                $all_toolboxes = [];
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='domain_id=:id';
                $criteria->params = array(':id'=>$domain_id);
                $toolboxes = Resourcegroup::model()->findAll($criteria);
                
                foreach($toolboxes as $toolbox){
                    $all_toolboxes[] = $toolbox['id'];
                }
            
                return $all_toolboxes;
            
        }
        
        
        /**
         * This is the function that retrieves all the toolboxes in a network
         */
        public function retrieveAllToolboxesForThisNetwork($network_id){
            
                $all_toolboxes = [];
                $criteria = new CDbCriteria();
                $criteria->select = '*';
                $criteria->condition='network_id=:id';
                $criteria->params = array(':id'=>$network_id);
                $toolboxes = NetworkHasToolboxes::model()->findAll($criteria);
                
                foreach($toolboxes as $toolbox){
                    $all_toolboxes[] = $toolbox['toolbox_id'];
                }
            
                return $all_toolboxes;
        }
        
        
        /**
         * This is the function that list all the legitimate toolboxes for purchase
         */
        public function actionListAllPossibleDomainLegitimateToolboxesForPurchase(){
            
            //retrieve all domain toolboxes on the platform
            $all_toolboxes = $this->retrieveAllDomainToolboxes();
            
            $tradable_toolboxes = [];
            
            foreach($all_toolboxes as $toolbox){
                //get the domain owner of this toolbox
                $toolbox_domain_owner_id = $this->getTheDomainOwnerOfThisToolbox($toolbox);
                if($this->isToolboxInComplianceWithDomainPolicy($toolbox, $toolbox_domain_owner_id)){
                    $tradable_toolboxes[] = $toolbox;
                }
                
            }
            
            $toolbox_details = [];
            foreach($tradable_toolboxes as $tradable){
                $criteria1 = new CDbCriteria();
                $criteria1->select = '*';
                $criteria1->condition='id=:id';
                $criteria1->params = array(':id'=>$tradable);
                $legitimate_toolbox = Resourcegroup::model()->find($criteria1);
                
                $toolbox_details[] = $legitimate_toolbox;
                
            }
                    
            if($tradable_toolboxes===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "toolbox" => $toolbox_details
                          
                           
                           
                          
                       ));
                       
                }
            
            
        }
        
        
        /**
         * This is the function that retrieves all toolboxes on the platform
         */
        public function retrieveAllDomainToolboxes(){
            
                //declare an array that will hold the toolboxes
                $all_toolboxes = [];
                
                //spool all the toolboxes from that database
                $criteria1 = new CDbCriteria();
                $criteria1->select = '*';
                //$criteria1->condition='id=:id';
               // $criteria1->params = array(':id'=>$id);
                $toolboxes = Resourcegroup::model()->findAll($criteria1);
                
                foreach($toolboxes as $toolbox){
                    $all_toolboxes[] = $toolbox['id'];
                }
                
                return $all_toolboxes;
                
                
            
        }
        
        /**
         * This is the function that gets the domain id of a toolbox
         */
        public function getTheDomainOwnerOfThisToolbox($id){
            
                $criteria1 = new CDbCriteria();
                $criteria1->select = '*';
                $criteria1->condition='id=:id';
                $criteria1->params = array(':id'=>$id);
                $toolbox = Resourcegroup::model()->find($criteria1);
                
                return $toolbox['domain_id'];
            
            
        }
        
        
         /**
         * This is the function that gets the toolbox actual amount
         */
        public function getThisToolboxAmount($toolbox_id){
            
            if($this->isPricePreferenceChecked($toolbox_id)){
                if($this->isStatedPriceHigherThanDerivedPrice($toolbox_id)){
                    $price = $this->getTheToolboxStatedPrice($toolbox_id);
                    return $price;
                }else{
                    if($this->isToolboxPricingDerivable($toolbox_id)){
                        $price = $this->getTheDerivePriceForThisToolbox($toolbox_id);
                        return $price;
                    }else{
                       $price = $this->getTheToolboxStatedPrice($toolbox_id);
                       return $price; 
                    }
                    
                    
                }
            }else{
              if($this->isToolboxPricingDerivable($toolbox_id)){
                        $price = $this->getTheDerivePriceForThisToolbox($toolbox_id);
                        return $price;
                    }else{
                       $price = $this->getTheToolboxStatedPrice($toolbox_id);
                       return $price; 
                    }
            }
            
        }
        
        
        /**
         * This is the function that determines if toolbox pring should be derived from its component
         */
        public function isToolboxPricingDerivable($toolbox_id){
            
                    $criteria = new CDbCriteria();
                    $criteria->select = '*';
                    $criteria->condition='id=:id';
                    $criteria->params = array(':id'=>$toolbox_id);
                    $toolbox= Resourcegroup::model()->find($criteria);
                    
                    if($toolbox['cumulative_component_price']== 1){
                        return true;
                    }else{
                        return false;
                    }
            
        }
        
        /**
         * This is the function that verifies if a toolbox price preference is checked or not
         */
        public function isPricePreferenceChecked($toolbox_id){
                    $criteria = new CDbCriteria();
                    $criteria->select = '*';
                    $criteria->condition='id=:id';
                    $criteria->params = array(':id'=>$toolbox_id);
                    $toolbox= ResourceGroup::model()->find($criteria);
                    
                    if($toolbox['price_preference']== 1){
                        return true;
                    }else{
                        return false;
                    }
            
        }
        
        /**
         * This is the function that retrieve the stated price of a toolbox
         */
        public function getTheToolboxStatedPrice($toolbox_id){
                    $criteria = new CDbCriteria();
                    $criteria->select = '*';
                    $criteria->condition='id=:id';
                    $criteria->params = array(':id'=>$toolbox_id);
                    $toolbox= ResourceGroup::model()->find($criteria);
                    
                    return $toolbox['price'];
            
        }
        
        /**
         * This is the function that determines if a toolbox stated price is higher than the derived price
         */
        public function isStatedPriceHigherThanDerivedPrice($toolbox_id){
            
            if($this->getTheToolboxStatedPrice($toolbox_id) >= $this->getTheDerivePriceForThisToolbox($toolbox_id)){
                return true;
            }else{
                return false;
            }
            
        }
        
        /**
         * This is the function that gets the derived price of a toolbox
         */
        public function getTheDerivePriceForThisToolbox($toolbox_id){
            
            //get all the tools in this toolbox
            $tools = $this->getAllTheToolsInThisToolbox($toolbox_id);
            $sum = 0;
            foreach($tools as $tool){
                $sum = $sum + $this->getThePriceAssignedToThisTool($tool);
                
            }
            return $sum;
        }
        
        
        /**
         * This is the function that obtains the price assigned to this tool
         */
        public function getThePriceAssignedToThisTool($tool_id){
            
            //confirm if tool price preference is checked
            if($this->isToolPricePreferenceChecked($tool_id)){
                if($this->isToolStatedPriceHigherThanDerivedPrice($tool_id)){
                    $price = $this->getTheToolStatedPrice($tool_id);
                    return $price;
                }else{
                    if($this->isToolPricingDerivable($tool_id)){
                        $price = $this->getTheDerivePriceForThisTool($tool_id);
                        return $price;
                }else{
                    $price = $this->getTheToolStatedPrice($tool_id);
                    return $price;
                }
                    
                }
            }else{
                
                 if($this->isToolPricingDerivable($tool_id)){
                        $price = $this->getTheDerivePriceForThisTool($tool_id);
                        return $price;
                }else{
                    $price = $this->getTheToolStatedPrice($tool_id);
                    return $price;
                }
            }
     
        }
        
        
        /**
         * This is the function that determines if a tool pricing is derivable from its components 
         */
        public function isToolPricingDerivable($tool_id){
            
                    $criteria = new CDbCriteria();
                    $criteria->select = '*';
                    $criteria->condition='id=:id';
                    $criteria->params = array(':id'=>$tool_id);
                    $tool= Resources::model()->find($criteria);
                    
                    if($tool['cumulative_component_price']== 1){
                        return true;
                    }else{
                        return false;
                    }
            
        }
  /**
   * This is the function that confirms if tools price preference is checked or not
   */      
        public function isToolPricePreferenceChecked($tool_id){
            
                    $criteria = new CDbCriteria();
                    $criteria->select = '*';
                    $criteria->condition='id=:id';
                    $criteria->params = array(':id'=>$tool_id);
                    $tool= Resources::model()->find($criteria);
                    
                    if($tool['price_preference']== 1){
                        return true;
                    }else{
                        return false;
                    }
        }
        
        /**
         * This is the function that determines if the stated price is higher than the derived price
         */
        public function isToolStatedPriceHigherThanDerivedPrice($tool_id){
            
            if($this->getTheToolStatedPrice($tool_id) > $this->getTheDerivePriceForThisTool($tool_id)){
                return true;
            }else{
                return false;
            }
            
        }
        
        /**
         * This is the function that retrieves the tool's stated price
         */
        public function getTheToolStatedPrice($tool_id){
            
                    $criteria = new CDbCriteria();
                    $criteria->select = '*';
                    $criteria->condition='id=:id';
                    $criteria->params = array(':id'=>$tool_id);
                    $tool= Resources::model()->find($criteria);
                    
                    return $tool['price'];
        }
        
        /**
         * This is the function that gets the derived price for a tool
         */
        public function getTheDerivePriceForThisTool($tool_id){
            
            //get all the task in this tool
            $tasks = $this->getTheTasksInThisTool($tool_id);
            $sum = 0.00;
            foreach($tasks as $task){
                $sum = $sum + (DOUBLE)$this->getThePriceForThisTask($task);
            }
            return $sum;
        }
        
        /**
         * This is the function that gets the price for this task
         */
        public function getThePriceForThisTask($task_id){
            
                    $criteria = new CDbCriteria();
                    $criteria->select = '*';
                    $criteria->condition='id=:id';
                    $criteria->params = array(':id'=>$task_id);
                    $task= Resources::model()->find($criteria);
                    
                    return $task['price'];
         }
         
         /**
          * This is the function that gets all tasks in a tool
          */
         public function getTheTasksInThisTool($tool_id){
             
                    $criteria = new CDbCriteria();
                    $criteria->select = '*';
                    $criteria->condition='parent_id=:id';
                    $criteria->params = array(':id'=>$tool_id);
                    $tasks= Resources::model()->findAll($criteria);
                    
                    $alltasks = [];
                    foreach($tasks as $task){
                        $alltasks[] = $task['id'];
                    }
                    return $alltasks;
             
         }
         
         /**
         * This is the function that retrieves all the tools in a toolbox
         */
        public function getAllTheToolsInThisToolbox($toolbox_id){
            
                    $criteria = new CDbCriteria();
                    $criteria->select = '*';
                    $criteria->condition='resourcegroup_id=:id';
                    $criteria->params = array(':id'=>$toolbox_id);
                    $tools= ResourceHasResourcegroups::model()->findAll($criteria);
                    
                   $alltools = [];
                   foreach($tools as $tool){
                       
                       $alltools[] = $tool['resource_id'];
                   }
                   return $alltools;
            
        }
        
        
        /**
         * This is the toolbox that updates the toolbox actual price data
         */
        public function updateThisToolboxPricing($toolbox_id,$actual_price){
            
            $cmd =Yii::app()->db->createCommand();
                $result = $cmd->update('resourcegroup',
                                  array(
                                   'actual_store_pricing'=>$actual_price   
                           ),
                        ("id=$toolbox_id")
                        );
        }
        
	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Stores the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Stores::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Stores $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='stores-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
