<?php

class AuthitemController extends Controller
{
	 private $_id;
    private $_name;
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
				'actions'=>array('index','view', 'ListAllAuthitemRoles','ListAllAuthAssignments','ListAllAuthitemOperations',
                                   'ListAllAuthitemTasks'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update', 'ListAllAuthitemRoles', 'CreateNewRole','UpdateRole','DeleteOneAuthitem',
                                    'UpdateTaskItem', 'ListAllAuthitemTasks', 'UpdateTaskItem', 'CreateNewTaskItem', 'UpdateOperationItem',
                                    'CreateNewOperationItem', 'ListAllAuthitemOperations', 'ListAllRolesAndItsChildren', 'AssignAuthitemsToRoles',
                                    'AssignAuthitemsToTasks', 'ListAllTasksAndItsChildren', 'ListAllUserAuthitems', 'AssignAuthitemsToUser',
                                    'ListAllAuthAssignments', 'ObtainUserForItemEdit','UpdateAuthitemAssignment','DeleteOneAuthitemAssignment'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete', 'ListAllAuthitemRoles','CreateNewRole'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * list new roles
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionListAllAuthitemRoles()
	{
		
             $criteria = new CDbCriteria();
             $criteria->select = 'name, type, description, bizrule, data';
             $criteria->condition='type=2';
             //$criteria->params = array(':id'=>2);
             $result = Authitem::model()->findAll($criteria);   
               
                if($result===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"total" => $total,
                            "data" => $result)
                       );
                }  
	}
        
        
        /**
	 * list new task
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionListAllAuthitemTasks()
	{
		
             $criteria = new CDbCriteria();
             $criteria->select = 'name, type, description, bizrule, data';
             $criteria->condition='type=1';
             //$criteria->params = array(':id'=>2);
             $result = Authitem::model()->findAll($criteria);   
               
                if($result===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"total" => $total,
                            "data" => $result)
                       );
                }  
	}
        
        
        /**
	 * list new authAssignments
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionListAllAuthAssignments()
	{
		
             $criteria = new CDbCriteria();
             $criteria->select = 'itemname, userid, bizrule, data';
             //$criteria->condition='type=1';
             //$criteria->params = array(':id'=>2);
             $result = AuthAssignment::model()->findAll($criteria);   
               
                if($result===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"total" => $total,
                            "data" => $result)
                       );
                }  
	}
        
        
        /**
	 * list new operation
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionListAllAuthitemOperations()
	{
		
             $criteria = new CDbCriteria();
             $criteria->select = 'name, type, description, bizrule, data';
             $criteria->condition='type=0';
             //$criteria->params = array(':id'=>2);
             $result = Authitem::model()->findAll($criteria);   
               
                if($result===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            //"total" => $total,
                            "data" => $result)
                       );
                }  
	}
        
        
        
	/**
	 * Creates a new role.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionCreateNewRole()
	{
		$model=new Authitem;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
                $model->name = $_POST['name'];
                $model->type = 2;
                $model->description = $_POST['description'];
                if(isset($_POST['bizrule'])){
                    $model->bizrule = $_POST['bizrule'];
                } else {
                    $model->bizrule = 'null';
                }   
                if(isset( $_POST['data'])){
                    $model->data = $_POST['data'];
                } else {
                    $model->data = 'null';
                }  
                //$model->create_time = new CDbExpression('NOW()');
                //$model->create_user_id = Yii::app()->user->id;
                if($model->save()){
                          $result['success'] = 'true';
                          $result['msg'] = "'$model->name' role was successfully created";
                          header('Content-Type: application/json');
                          echo CJSON::encode($result);
                 }else {
                         $result['success'] = 'false';
                         $result['msg'] = "Attempt to create the '$model->name' role was not successful";
                         header('Content-Type: application/json');
                         echo CJSON::encode($result);
                  }  
	}
        
        
        /**
	 * Creates a new task.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionCreateNewTaskItem()
	{
		$model=new Authitem;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
                $model->name = $_POST['name'];
                $model->type = 1;
                $model->description = $_POST['description'];
                if(isset($_POST['bizrule'])){
                    $model->bizrule = $_POST['bizrule'];
                } else {
                    $model->bizrule = 'null';
                }   
                if(isset( $_POST['data'])){
                    $model->data = $_POST['data'];
                } else {
                    $model->data = 'null';
                }  
                //$model->create_time = new CDbExpression('NOW()');
                //$model->create_user_id = Yii::app()->user->id;
                if($model->save()){
                          $result['success'] = 'true';
                          $result['msg'] = "'$model->name' task was successfully created";
                          header('Content-Type: application/json');
                          echo CJSON::encode($result);
                 }else {
                         $result['success'] = 'false';
                         $result['msg'] = "Attempt to create the '$model->name' task was not successful";
                         header('Content-Type: application/json');
                         echo CJSON::encode($result);
                  }  
	}
        
        
        
        /**
	 * Creates a new role.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionCreateNewOperationItem()
	{
		$model=new AuthItem;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
                $model->name = $_POST['name'];
                $model->type = 0;
                $model->description = $_POST['description'];
                if(isset($_POST['bizrule'])){
                    $model->bizrule = $_POST['bizrule'];
                } else {
                    $model->bizrule = 'null';
                }   
                if(isset( $_POST['data'])){
                    $model->data = $_POST['data'];
                } else {
                    $model->data = 'null';
                }  
                //$model->create_time = new CDbExpression('NOW()');
                //$model->create_user_id = Yii::app()->user->id;
                if($model->save()){
                          $result['success'] = 'true';
                          $result['msg'] = "'$model->name' operation/privilege was successfully created";
                          header('Content-Type: application/json');
                          echo CJSON::encode($result);
                 }else {
                         $result['success'] = 'false';
                         $result['msg'] = "Attempt to create the '$model->name' operation/privilege was not successful";
                         header('Content-Type: application/json');
                         echo CJSON::encode($result);
                  }  
	}
        
       
        
        
        
        
        /**
	 * Creates a new role.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdateRole()
	{
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
                $_id = $_POST['name'];
                $model=Authitem::model()->findByPk($_id);
                //$model->name = $_POST['name'];
                $model->type = 2;
                $model->description = $_POST['description'];
               if(isset($_POST['bizrule'])){
                   $model->bizrule = $_POST['bizrule'];
               } 
                if(isset($_POST['data'])){
                    $model->data = $_POST['data'];
                }
                //$model->create_time = new CDbExpression('NOW()');
                //$model->create_user_id = Yii::app()->user->id;
                if($model->save()){
                          $result['success'] = 'true';
                          $result['msg'] = "'$_id' Role info was Successfully updated";
                          header('Content-Type: application/json');
                          echo CJSON::encode($result);
                 }else {
                         $result['success'] = 'false';
                         $result['msg'] = "Update of the '$_id' Role Information was not successful";
                         header('Content-Type: application/json');
                         echo CJSON::encode($result);
                  }  
	}
        
        
        
        /**
	 * Update task item.
	 * If update is successful, the browser will be remain on the same task page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdateTaskItem()
	{
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
                $_id = $_POST['name'];
                $model=Authitem::model()->findByPk($_id);
                //$model->name = $_POST['name'];
                $model->type = 1;
                $model->description = $_POST['description'];
                if(isset( $_POST['bizrule'])){
                    $model->bizrule = $_POST['bizrule'];
                }
                if(isset($_POST['data'])){
                    $model->data = $_POST['data'];
                }
                //$model->create_time = new CDbExpression('NOW()');
                //$model->create_user_id = Yii::app()->user->id;
                if($model->save()){
                          $result['success'] = 'true';
                          $result['msg'] = "'$_id' Task Info was Successfully updated";
                          header('Content-Type: application/json');
                          echo CJSON::encode($result);
                 }else {
                         $result['success'] = 'false';
                         $result['msg'] = " '$_id' Task update was not successful";
                         header('Content-Type: application/json');
                         echo CJSON::encode($result);
                  }  
	}
        
        
        /**
	 * Update task item.
	 * If update is successful, the browser will be remain on the same task page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdateOperationItem()
	{
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
                $_id = $_POST['name'];
                $model=Authitem::model()->findByPk($_id);
                //$model->name = $_POST['name'];
                $model->type = 0;
                $model->description = $_POST['description'];
                if(isset($_POST['bizrule'])){
                    $model->bizrule = $_POST['bizrule'];
                }
                if(isset($_POST['data'])){
                    $model->data = $_POST['data'];
                }
                //$model->create_time = new CDbExpression('NOW()');
                //$model->create_user_id = Yii::app()->user->id;
                if($model->save()){
                          $result['success'] = 'true';
                          $result['msg'] = "'$_id' Operation info was Successfully updated";
                          header('Content-Type: application/json');
                          echo CJSON::encode($result);
                 }else {
                         $result['success'] = 'false';
                         $result['msg'] = "'$_id' Operation Information was not successful";
                         header('Content-Type: application/json');
                         echo CJSON::encode($result);
                  }  
	}
        
        
        /**
	 * Update authassignment item.
	 * If update is successful, the browser will be remain on the same task page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdateAuthitemAssignment()
	{
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
                
                
                $_id = $_POST['userid'];
                $model = new AuthAssignment;
                
                $model->userid= $_id;
                $model->itemname = $_POST['itemname'];
                $model->bizrule = $_POST['bizrule'];
                $model->data = $_POST['data'];
                //verify if the row exist        
                $cmd =Yii::app()->db->createCommand();
                $cmd->select('COUNT(*)')
                    ->from('authassignment')
                    ->where('userid' == $_id AND 'itemname' == $model->itemname);
                $num = $cmd->queryScalar();
                
               // if row exist
                    if($num > 0){
                         $cmd->delete('authassignment', 'userid=:userid AND itemname=:itemname', array(':userid'=>$_id, ':itemname'=>$model->itemname ));
                    
                     }
                     //insert into the authassignment table
                     $cmd->insert('authassignment',
                                 array(
                                     'userid'=> $_id,
                                     'itemname'=>$model->itemname,
                                     'bizrule' => $model->bizrule,
                                     'data'=>$model->data
                                 ));
                
                     $result['success'] = 'true';
                     $result['msg'] = 'Operation Info Successfully updated';
                     header('Content-Type: application/json');
                    echo CJSON::encode($result);
                 
	}
        
        
        /**
	 * Deletes one authitem.
	 * If deletion is successful, the browser will remain on the same page.
	 * @param integer $_id the name of the authitem to be deleted
	 */
	public function actionDeleteOneAuthitem()
	{
            
            $_id = $_REQUEST['name'];
            $model=Authitem::model()->findByPk($_id);
            if($model === null){
                $data['success'] = 'undefined';
                $data['msg'] = 'No such record exist';
                header('Content-Type: application/json');
                echo CJSON::encode($data);
                                      
            }else if($model->delete()){
                    $data['success'] = 'true';
                    $data['msg'] = "'$_id' item was successfully deleted";
                     header('Content-Type: application/json');
                    echo CJSON::encode($data);
            } else {
                    $data['success'] = 'false';
                    $data['msg'] = "'$_id' item deletion was not successful";
                     header('Content-Type: application/json');
                    echo CJSON::encode($data);
                            
                }
	}
        
        
        
        /**
	 * Deletes one authitem.
	 * If deletion is successful, the browser will remain on the same page.
	 * @param integer $_id the name of the authitem to be deleted
	 */
	public function actionDeleteOneAuthitemAssignment()
	{
            
           // $model = new AuthAssignment;
            
            $_name = $_REQUEST['itemname'];
            $_id = $_REQUEST['userid'];
            
            //spool the user primary role from the user table
            
             $criteria = new CDbCriteria();
             $criteria->select = 'role';
             $criteria->condition='id=:userid';
             $criteria->params = array(':userid'=>$_id);
             $user= Members::model()->findAll($criteria);
             
             if($user[0]->role !== $_name){
                 $cmd =Yii::app()->db->createCommand();
                $result = $cmd->delete('authassignment', 'userid=:userid AND itemname=:itemname', array(':userid'=>$_id, ':itemname'=>$_name ));
                
                if($result === null){
                $data['success'] = 'undefined';
                $data['msg'] = 'No such record exist';
                header('Content-Type: application/json');
                echo CJSON::encode($data);
                                      
            }else if($result>=1){
                    $data['success'] = 'true';
                    $data['msg'] = 'The data was successfully deleted';
                     header('Content-Type: application/json');
                    echo CJSON::encode($data);
            } else {
                    $data['success'] = 'false';
                    $data['msg'] = 'deletion unsuccessful';
                     header('Content-Type: application/json');
                    echo CJSON::encode($data);
                            
                }
                 
             }else {
                   $data['success'] = 'false';
                    $data['msg'] = "You cannot delete a user's primary role";
                     header('Content-Type: application/json');
                    echo CJSON::encode($data);
                 
             }
                                  
           
            
            
	}

	/**
	 * list all the roles and the task and operations that make up the role
        * A window will be displayed showing the role and all its children.
	 */
	public function actionListAllRolesAndItsChildren()
	{
             
             $_name = $_REQUEST['role_name'];
             //$_name = 'admin';
             //$_id = 1;
            
             $criteria = new CDbCriteria();
             $criteria->select = 'name, type';
             $authitem = Authitem::model()->findAll($criteria);   
             
             $criteria2 = new CDbCriteria();
             $criteria2->select = 'child';
             $criteria2->condition='parent=:name';
             $criteria2->params = array(':name'=>$_name);
             $children= AuthItemChild::model()->findAll($criteria2);
               
                if($authitem===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "children" => $children,
                            "authitems" => $authitem)
                       );
                       
                } 
              
	}//end of the function
        
        
        
        /**
	 * list all the roles and the task and operations that make up the role
        * A window will be displayed showing the role and all its children.
	 */
	public function actionListAllTasksAndItsChildren()
	{
             
             $_name = $_REQUEST['task_name'];
             //$_name = 'admin';
             //$_id = 1;
            
             $criteria = new CDbCriteria();
             $criteria->select = 'name, type';
             $authitem = Authitem::model()->findAll($criteria);   
             
             $criteria2 = new CDbCriteria();
             $criteria2->select = 'child';
             $criteria2->condition='parent=:name';
             $criteria2->params = array(':name'=>$_name);
             $children= AuthItemChild::model()->findAll($criteria2);
               
                if($authitem===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "children" => $children,
                            "authitems" => $authitem)
                       );
                       
                } 
              
	}//end of the function

        
        /**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionAssignAuthitemsToRoles()
	{
		//$model=new GroupHasResourcegroup;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
                $_name = $_POST['name'];
                                             
                $cmd =Yii::app()->db->createCommand();
                $cmd->select('COUNT(*)')
                    ->from('authitemchild')
                    ->where('parent' == $_name);
                $result = $cmd->queryScalar();
                
                if(isset($_POST['child'])){
                    if($result > 0){
                         $cmd->delete('authitemchild', 'parent=:name', array(':name'=>$_name ));
                    
                     }
                    if (is_array($_POST['child'])) {
                            foreach($_POST['child'] as $child){                                                   
                                   $cmd->insert('authitemchild',
                                        array(
                                           'parent'=>$_POST['name'],
                                           'child'=>$child 
                                              
                                        ));
                                      
                               
                             }
                             
                       }else {
                           $child = $_POST['child'];
                           $cmd->insert('authitemchild',
                                   array(
                                        'parent'=>$_POST['name'],
                                        'child'=>$child 
                                              
                                    ));
                           
                       }
                      
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                           "msg"=>"Requested operation was successful"
                       ));
                        
                    } 
                  
                          
                                       
           
                    
            }
            
            
            
         /**
	 * list task and its children.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionAssignAuthitemsToTasks()
	{
		//$model=new GroupHasResourcegroup;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
                $_name = $_POST['name'];
                                             
                $cmd =Yii::app()->db->createCommand();
                $cmd->select('COUNT(*)')
                    ->from('authitemchild')
                    ->where('parent' == $_name);
                $result = $cmd->queryScalar();
                
                if(isset($_POST['child'])){
                    if($result > 0){
                         $cmd->delete('authitemchild', 'parent=:name', array(':name'=>$_name ));
                    
                     }
                    if (is_array($_POST['child'])) {
                            foreach($_POST['child'] as $child){                                                   
                                   $cmd->insert('authitemchild',
                                        array(
                                           'parent'=>$_POST['name'],
                                           'child'=>$child 
                                              
                                        ));
                                      
                               
                             }
                             
                       }else {
                           $child = $_POST['child'];
                           $cmd->insert('authitemchild',
                                   array(
                                        'parent'=>$_POST['name'],
                                        'child'=>$child 
                                              
                                    ));
                           
                       }
                      
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "msg"=>"Requested operation was successful"
                       ));
                        
                    } 
                  
                          
                                       
           
                    
            }
            
            
             /**
	 * list all the authitems, assignments, and details of a user
        * A window will be displayed showing the role and all its children.
	 */
	public function actionListAllUserAuthitems()
	{
             
             $_id = $_REQUEST['user_id'];
                                 
             //$_name = 'admin';
             //$_id = 1;
            
             //fetch the user detail from the user table
             $criteria = new CDbCriteria();
             $criteria->select = 'id, role, firstname, middlename, lastname, status';
             $criteria->condition='id=:userid';
             $criteria->params = array(':userid'=>$_id);
             $user = Members::model()->findAll($criteria);  
             
             //fetch the items in the authitem table
             $criteria2 = new CDbCriteria();
             $criteria2->select = 'name, type';
             //$criteria2->condition='id=:userid';
             //$criteria2->params = array(':userid'=>$_id);
             $authitems = Authitem::model()->findAll($criteria2);   
             
            //fetch all the authitems(roles, task, operations) that is assigned to a user
             $criteria3 = new CDbCriteria();
             $criteria3->select = 'itemname';
             $criteria3->condition='userid=:userid';
             $criteria3->params = array(':userid'=>$_id);
             $assignments= AuthAssignment::model()->findAll($criteria3);
               
                if($authitems===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "user" => $user,
                            "assignments" => $assignments,
                            "authitems" => $authitems)
                       );
                       
                } 
              
	}//end of the function
        
        
        
             /**
	 * obtain information of a user
        * A window will be displayed showing the role and all its children.
	 */
	public function actionObtainUserForItemEdit()
	{
             
             $_id = $_REQUEST['user_id'];
                                 
             //$_name = 'admin';
             //$_id = 1;
            
             //fetch the user detail from the user table
             $criteria = new CDbCriteria();
             $criteria->select = 'id, role, firstname, middlename, lastname, status';
             $criteria->condition='id=:userid';
             $criteria->params = array(':userid'=>$_id);
             $user = Members::model()->findAll($criteria);  
                     
                if($user===null) {
                    http_response_code(404);
                    $data['error'] ='No record found';
                    echo CJSON::encode($data);
                } else {
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                            "user" => $user)
                            
                       );
                       
                } 
              
	}//end of the function
        
        
        /**
	 * assign authitems(roles, task, and operations) to a user.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionAssignAuthitemsToUser()
	{
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
                $_id = $_REQUEST['userid'];
                $role = $_POST['role'];
                                             
                $cmd =Yii::app()->db->createCommand();
                $cmd->select('COUNT(*)')
                    ->from('authassignment')
                    ->where('userid' == $_id);
                $result = $cmd->queryScalar();
                
                if(isset($_POST['itemname'])){
                    if($result > 0){
                         $cmd->delete('authAssignment', 'userid=:id', array(':id'=>$_id ));
                          //insert the primary role value into the authassignment table too
                         $cmd->insert('authassignment',
                                 array(
                                     'userid'=> $_id,
                                     'itemname'=>$role
                                 ));
                     }
                    if (is_array($_POST['itemname'])) {
                            foreach($_POST['itemname'] as $item){                                                   
                                   $cmd->insert('authassignment',
                                        array(
                                           'userid'=>$_id,
                                           'itemname'=>$item
                                              
                                        ));
                                      
                               
                             }
                             
                       }else {
                           $item = $_POST['itemname'];
                           $cmd->insert('authassignment',
                                   array(
                                        'userid'=>$_POST['userid'],
                                        'itemname'=>$item 
                                              
                                    ));
                           
                       }
                                            
                      
                       header('Content-Type: application/json');
                       echo CJSON::encode(array(
                            "success" => mysql_errno() == 0,
                       ));
                        
                    } 
                  
                          
                                       
           
                    
            }
}
