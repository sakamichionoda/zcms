<?php

namespace ZCMS\Frontend\Index\Controllers;

use Phalcon\Validation;
use ZCMS\Core\Models\Users;
use ZCMS\Core\Social\ZFacebook;
use ZCMS\Core\ZFrontController;
use Phalcon\Validation\Validator\Email;

/**
 * Class LoginController
 *
 * @package ZCMS\Frontend\Index\Controllers
 */
class LoginController extends ZFrontController
{
    /**
     * User login
     */
    public function indexAction()
    {
        //User has login yet
        if ($this->_user) {
            $this->session->destroy();
        }

        $this->_addSocialLogin();

        //Regular login
        if ($this->request->isPost()) {
            $validation = new Validation();
            $validation->add('email', new Email());

            $messages = $validation->validate($this->request->getPost());
            if (count($messages)) {
                foreach ($messages as $message) {
                    $this->flashSession->error($message);
                }
                $this->response->redirect('/admin/user/login/');
                return;
            }

            $email = strtolower($this->request->getPost('email', 'email'));
            $password = $this->request->getPost('password', 'string');

            if (Users::login($email, $password)) {
                $user = Users::getCurrentUser();
                $this->flashSession->success('Hi, ' . $user['full_name']);
                $this->response->redirect('/');
            } else {
                $this->flashSession->error('User or password not match!');
                $this->response->redirect('/user/login/');
            }
        }
    }

    /**
     * Add social login
     */
    private function _addSocialLogin(){
        $isSocialLogin = false;
        if($this->config->social->facebook->appId){
            $fb = ZFacebook::getInstance();
            $helper = $fb->getRedirectLoginHelper();
            $permissions = $this->config->social->facebook->permissions->toArray();
            $this->view->setVar('facebookLoginUrl', $helper->getLoginUrl(BASE_URI . '/facebook/login-callback/', $permissions));
            $isSocialLogin = true;
        }

        if($this->config->social->google->appId){
            $this->view->setVar('googleLoginUrl', '#');
            $isSocialLogin = true;
        }

        $this->view->setVar('isSocialLogin', $isSocialLogin);
    }


}