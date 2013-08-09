<?php
/**
 * Socms
 *
 * LICENSE
 *
 * This source file is subject to the Closed Source license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://socms.sointeractive.pl/license
 *
 * @category   Socms
 * @package    Socms_backend_controllers
 * @copyright  Copyright (c) 2008-2009 SoInteractive (http://www.sointeractive.pl)
 * @license    http://socms.sointeractive.pl/license    Closed Source License
 */

/** Application_Modules_Controllers_ModuleController */
require_once 'application/modules/controller/ModuleController.php';


/**
 * @category   Socms
 * @package    Socms_backend_controllers
 * @copyright  Copyright (c) 2008-2009 SoInteractive (http://www.sointeractive.pl)
 * @license    http://socms.sointeractive.pl/license    Closed Source License
 */
class Users_Module_Backend_BaseController extends ModuleController
{
    const DEFAULT_REDIRECT     = 'users/module/moduleName/users';
    const MYACCOUNT_REDIRECT   = 'users/module/moduleName/users/moduleAction/myaccount/';

    /**
     * @var string $redirectUrl
     */
    protected $redirectUrl;

    /**
     * @var Users_Backend_BaseModel $baseModel
     */
    protected $baseModel;


    public function init()
    {
        parent::init();

        $this->redirectUrl = self::DEFAULT_REDIRECT;
        $this->baseModel   = new Users_Backend_BaseModel();

        // only admin user is allowed to manage users
        if (!$this->checkIfModuleAllowed()) {
        	Socms_MongoLog::getInstance()->error('Module users not allowed. Only admin user is allowed to manage users.');
            $this->_redirect($this->redirectUrl . '/moduleAction/myaccount/');
        }
    }

    public function indexAction()
    {
        $showBlocked = false;
        if ($this->isAdmin) {
            $showBlocked = true;
        }

        $all = Users_Backend_BaseModel::isAdmin();
        $this->view->users        = Users_Backend_BaseModel::getUsers($all);
        $this->view->user_id      = Users_Backend_BaseModel::getCurrentUserId();
        $this->view->show_blocked = $showBlocked;
        Socms_MongoLog::getInstance()->info('Display users list.');
    }

    public function addUserAction()
    {
        $form = new Module_Users_Backend_Form_User();
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            // check if Cancel button was clicked
            if ($form->checkCancel($post = $this->getRequest()->getParams())) {
                $this->_redirect($this->redirectUrl);
            }

            if ($form->isValid($post)) {
                $values = $form->getValues();
                $valid = true;

                $oUser = Users_Backend_BaseModel::checkIfUserExists($values['login']);
                $oUserUniqueChiefSector  = Roles_Backend_BaseModel::checkUniqueChiefSector($values['role']);
                if ($oUserUniqueChiefSector) {
                    // trying to update non existing user
                	Socms_MongoLog::getInstance()->warning('You can\'t be Chief of Section becouse another user is Chief of this section.');
                    $this->notifyFailure('You can\'t be Chief of Section becouse another user is Chief of this section');
               } else if ($oUser === false) {
                    // user not exists in database
                    $state = $this->baseModel->addUser($values);
                    if ($state) {
                    	Socms_MongoLog::getInstance()->info('New user account has been created. id: '.$oUser->id);
                        $this->notifySuccess('New user account has been created');
                    } else {
                    	Socms_MongoLog::getInstance()->error('New user was not created. Error occured');
                        $this->notifyFailure('Error occured');
                    }
                } else if ($oUser instanceof Zend_Db_Table_Row_Abstract && $oUser->disabled == 1) {
                    // user exists, but was deleted (disabled = 1)
                    $state = $this->baseModel->overrideDeletedUser($values);
                    if ($state) {
                    	Socms_MongoLog::getInstance()->info('A new user account has been created overwriting a previously removed account. id: '.$oUser->id);
                        $this->notifySuccess('A new user account has been created overwriting a previously removed account');
                    } else {
                    	Socms_MongoLog::getInstance()->error('New user was not created. Error occured');
                        $this->notifyFailure('Error occured');
                    }
                } else {
                    // user exists and is active (disabled = 0)
                    Socms_MongoLog::getInstance()->error('Specified login is already taken. Error occured');
                    $loginField = $form->getElement(Module_Users_Backend_Form_User::FIELD_LOGIN);
                    $loginField->addError('Specified login is already taken');
                    $valid = false;
                }

                if ($valid) {
                    $this->_redirect($this->redirectUrl);
                }
            } else {
                $form->populate($post);
            }
        }
    }

    public function editUserAction()
    {
        $userId = (int) $this->getRequest()->getParam('item', 0);
        $this->view->user_id   = $userId;
        
        if(Users_Backend_BaseModel::isSectionChief() && $userId == Users_Backend_BaseModel::getCurrentUserId()) {
            $this->_redirect(self::MYACCOUNT_REDIRECT);
        }

        $form = new Module_Users_Backend_Form_EditUser();
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getParams();
               
            // check if cancel button was clicked
            if ($form->checkCancel($post)) {
                $this->_redirect($this->redirectUrl);
            }

            // check if reset password was clicked
            if ($form->checkReset($post)) {
                $state = $this->baseModel->resetPassword($userId);
                if ($state) {
                	Socms_MongoLog::getInstance()->info('User password has been successfully reset. id: '.$userId);
                    $this->notifySuccess('User password has been successfully reset');
                } else {
                	Socms_MongoLog::getInstance()->error('Error occured during the password reseting. id: '.$userId);
                    $this->notifyFailure('Error occured during the password reseting');
                }

                $this->_redirect($this->redirectUrl);
            }

            if ($form->isValid($post)) {
                $values = $form->getValues();
                $valid = true;

                $oUser = Users_Backend_BaseModel::getUser($userId);
                $oUserUpdated   = Users_Backend_BaseModel::checkIfUserExists(null, $userId);
                $oUserWithLogin = Users_Backend_BaseModel::checkIfUserExists($values['login'], $userId);
                $oUserUniqueChiefSector  = Roles_Backend_BaseModel::checkUniqueChiefSector($values['role']);
                if ($oUserUniqueChiefSector && $values['role'] != $oUser['role_id']) {
                    // trying to update non existing user
                	Socms_MongoLog::getInstance()->error('You can\'t be Chief of Section becouse another user is Chief of this section. id: '.$userId);
                    $this->notifyFailure('You can\'t be Chief of Section becouse another user is Chief of this section');
               } else if ($oUserUpdated === false) {
                    // trying to update non existing user
               		Socms_MongoLog::getInstance()->warning('User not found.');
                    $this->notifyFailure('User not found');
                } else if ($oUserWithLogin === false) {
                    // we can update data because login belongs to current user or it is not taken
                    $state = $this->baseModel->updateUser($userId, $values);
                    if ($state) {
                    	Socms_MongoLog::getInstance()->info('User account has been successfully updated.', null, array_merge(array('userId'=>$userId), $values) );
                        $this->notifySuccess('User account has been successfully updated');
                    } else {
                    	Socms_MongoLog::getInstance()->error('User account not updated. Error occured during the saving changes in database. User id: '.$userId);
                        $this->notifyFailure('Error occured during the saving changes in database');
                        $valid = false;
                    }
                } else {
                    // user exists (login is taken by another user)
                    $loginField = $form->getElement(Module_Users_Backend_Form_User::FIELD_LOGIN);
                    Socms_MongoLog::getInstance()->warning('Specified login is already taken. User id: '.$userId);
                    $loginField->addError('Specified login is already taken');
                    $valid = false;
                }

                if ($valid) {
                    $this->_redirect($this->redirectUrl);
                }
            }
        } else {
            $values = Users_Backend_BaseModel::getUserForUpdate($userId);
            $form->populate($values);
        }
    }

    public function deleteUserAction()
    {
        $user_id = (int) $this->getParam('item', 0);
        
        if ($this->baseModel->deleteUser($user_id)) {
        	Socms_MongoLog::getInstance()->info('User account has been deleted. User id: '.$user_id);
            $this->notifySuccess('User account has been deleted');
        } else {
        	Socms_MongoLog::getInstance()->error('User account has not been deleted. Error occured.');
            $this->notifyFailure('Error occured');
        }

        $this->_redirect($this->redirectUrl);
    }

    public function blockUserAction()
    {
        $user_id = (int) $this->getParam('item', 0);
        
        if ($this->baseModel->blockUser($user_id)) {
        	Socms_MongoLog::getInstance()->info('User account has been blocked. User id: '.$user_id);
            $this->notifySuccess('User account has been blocked');
        } else {
        	Socms_MongoLog::getInstance()->error('Block user. Error occured.');
            $this->notifyFailure('Error occured');
        }

        $this->_redirect($this->redirectUrl);
    }

    public function unlockUserAction()
    {
        $user_id = (int) $this->getParam('item', 0);

        if ($this->baseModel->unlockUser($user_id)) {
        	Socms_MongoLog::getInstance()->info('User account has been unblocked. User id: '.$user_id);
            $this->notifySuccess('User account has been unlocked');
        } else {
        	Socms_MongoLog::getInstance()->error('Unlock user. Error occured.');
            $this->notifyFailure('Error occured');
        }

        $this->_redirect($this->redirectUrl);
    }

    public function myAccountAction()
    {
        $iUserId = (int) Users_Backend_BaseModel::getCurrentUserId();
        $this->view->user_id   = $iUserId;

        $formPersonalData = new Module_Users_Backend_Form_PersonalData();
        $this->view->form_personal_data = $formPersonalData;

        $formChangePassword = new Module_Users_Backend_Form_ChangePassword();
        $this->view->form_change_password = $formChangePassword;

        $populate = array(
            $formPersonalData->name => false,
            $formChangePassword->name => false
        );

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getParams();

            // check if Cancel button was clicked
            if ($formPersonalData->checkCancel($post)) {
                $this->_redirect($this->redirectUrl);
            }

            if ($formPersonalData->verifySending($post)) {
                // process form with personal data
                $state = false;

                if ($formPersonalData->isValid($post)) {
                    $password = $post[Module_Users_Backend_Form_PersonalData::FIELD_PASSWORD];

                    if (Users_Backend_BaseModel::checkPassword($iUserId, $password)) {
                        $values = $formPersonalData->getValues();

                        $oUserWithLogin = Users_Backend_BaseModel::checkIfUserExists($values['login'], $iUserId);
                        if ($oUserWithLogin === false) {
                            // we can update data because login belongs to current user or it is not taken
                            $login = $values['login'];
                            $realname = $values['realname'];

                            $state = $this->baseModel->updateProfile($iUserId, $login, $realname);

                            if ($state) {
                            	Socms_MongoLog::getInstance()->info('User profile has been successfully updated. User id: '.$iUserId);
                                $this->notifySuccess('Your profile has been successfully updated');
                            } else {
                            	Socms_MongoLog::getInstance()->error('User profile. Error occured during saving changes in database.');
                                $this->notifyFailure('Error occured during saving changes in database');
                            }
                        } else {
                            // user exists (login is taken by another user)
                            $loginField = $formPersonalData->getElement(Module_Users_Backend_Form_PersonalData::FIELD_LOGIN);
                            Socms_MongoLog::getInstance()->warning('Specified login is already taken.');
                            $loginField->addError('Specified login is already taken');
                        }
                    } else {
                        $oPassword = $formPersonalData->getElement(
                            Module_Users_Backend_Form_PersonalData::FIELD_PASSWORD
                        );
                        Socms_MongoLog::getInstance()->warning('Password is not correct.');
                        $oPassword->addError('Password is not correct');
                    }
                }

                if ($state) {
                    $this->_redirect(self::MYACCOUNT_REDIRECT);
                }
            } else {
                $populate[$formPersonalData->name] = true;
            }

            if ($formChangePassword->verifySending($post)) {
                // process form with password changing
                $state = false;

                $sOldPassword = $post[Module_Users_Backend_Form_ChangePassword::FIELD_PASSWORD_OLD];
                $sNewPassword = $post[Module_Users_Backend_Form_ChangePassword::FIELD_PASSWORD_NEW];

                // add validator
                $formChangePassword->addIdenticalValidator($sNewPassword);

                if ($formChangePassword->isValid($post)) {
                    // process form
                    $bCheckPassword = Users_Backend_BaseModel::checkPassword($iUserId, $sOldPassword);

                    if ($bCheckPassword && $sOldPassword !== $sNewPassword) {
                        // check password matching
                        $values = $formChangePassword->getValues();

                        $state = $this->baseModel->changePassword($iUserId, $sNewPassword);
                        if ($state) {
                        	Socms_MongoLog::getInstance()->info('User password has been successfully changed. User id: '.$iUserId);
                            $this->notifySuccess('Your password has been successfully changed');
                        } else {
                        	Socms_MongoLog::getInstance()->info('User password not changed. Error occured during saving changes in database' );
                            $this->notifyFailure('Error occured during saving changes in database');
                        }
                    } else if (!$bCheckPassword) {
                        $oPasswordOld = $formChangePassword->getElement(
                            Module_Users_Backend_Form_ChangePassword::FIELD_PASSWORD_OLD
                        );
                        Socms_MongoLog::getInstance()->warning('Password is not correct' );
                        $oPasswordOld->addError('Password is not correct');
                    } else {
                        $oPasswordOld = $formChangePassword->getElement(
                            Module_Users_Backend_Form_ChangePassword::FIELD_PASSWORD_NEW
                        );
                        Socms_MongoLog::getInstance()->warning('New password must be different than the old one' );
                        $oPasswordOld->addError('New password must be different than the old one');
                    }
                }

                if ($state) {
                    $this->_redirect(self::MYACCOUNT_REDIRECT);
                }

                $populate[$formPersonalData->name] = true;
            }
        } else {
            $populate[$formPersonalData->name] = true;
        }


        if ($populate[$formPersonalData->name]) {
            $values = Users_Backend_BaseModel::getUserForUpdate($iUserId, true);
            $formPersonalData->populate($values);
        }
    }

    public function changeRoleAction()
    {
        $roleId = (int) $this->getRequest()->getParam('item', 0);
        $state = Users_Backend_BaseModel::changeRole($roleId);
        if ($state) {
        	Socms_MongoLog::getInstance()->info('User role has been changed. Role id: '.$roleId );
            $this->notifySuccess('Your role has been changed');
        } else {
        	Socms_MongoLog::getInstance()->error('User role has not been changed. Error occured during saving changes in database.' );
            $this->notifyFailure('Error occured during saving changes in database');
        }

        $this->_redirect($this->redirectUrl);
    }

    public function userLogsAction()
    {
        $logs = Users_Backend_BaseModel::getUsersLogs();
        Socms_MongoLog::getInstance()->info('Dispaly users logs.' );
        $this->view->users_logs = $logs;
    }

    protected function getUsersEmails($type)
    {
        $this->_setupAjax();
        $response = array();

        $list = Users_Backend_BaseModel::getUsersEmails($type, true);
        if ($list !== false) {
            $response['result'] = true;
            $response['list']   = $list;
        } else {
            $response['result'] = $list;
        }
        echo Zend_Json_Encoder::encode($response);
    }

    public function getAllUsersEmailsAction()
    {
        $this->getUsersEmails(Users_Backend_BaseModel::LIST_TYPE_ALL);
    }

    public function getChiefUsersEmailsAction()
    {
        $this->getUsersEmails(Users_Backend_BaseModel::LIST_TYPE_CHIEFS);
    }

    protected function checkIfModuleAllowed()
    {
        return $this->isAdmin || $this->_request->getParam('moduleAction') == 'myaccount';
    }
}