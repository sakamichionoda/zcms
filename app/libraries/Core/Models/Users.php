<?php

namespace ZCMS\Core\Models;

use Phalcon\Di;
use Phalcon\Mvc\Model\Validator\Email as ModelValidatorEmail;
use Phalcon\Mvc\Model\Message;
use Phalcon\Mvc\Model\Validator\Inclusionin;
use Phalcon\Mvc\Model\Validator\StringLength;
use Phalcon\Mvc\Model\Validator\Uniqueness;
use ZCMS\Core\ZModel;

/**
 * Class Users
 *
 * @package ZCMS\Core\Models
 */
class Users extends ZModel
{

    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var integer
     */
    public $role_id;

    /**
     *
     * @var string
     */
    public $first_name;

    /**
     *
     * @var string
     */
    public $last_name;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $password;

    /**
     *
     * @var string
     */
    public $salt;

    /**
     *
     * @var string
     */
    public $avatar;

    /**
     *
     * @var string
     */
    public $facebook_id;

    /**
     *
     * @var integer
     */
    public $is_active;

    /**
     *
     * @var integer
     */
    public $is_active_facebook;

    /**
     *
     * @var integer
     */
    public $is_active_google;

    /**
     *
     * @var string
     */
    public $language_code;

    /**
     *
     * @var string
     */
    public $reset_password_token;

    /**
     *
     * @var string
     */
    public $reset_password_token_at;

    /**
     *
     * @var string
     */
    public $active_account_at;

    /**
     *
     * @var string
     */
    public $active_account_token;

    /**
     *
     * @var string
     */
    public $active_account_type;

    /**
     *
     * @var string
     */
    public $coin;

    /**
     *
     * @var string
     */
    public $token;

    /**
     *
     * @var integer
     */
    public $gender;

    /**
     *
     * @var string
     */
    public $mobile;

    /**
     *
     * @var string
     */
    public $birthday;

    /**
     *
     * @var integer
     */
    public $default_bill_address;

    /**
     *
     * @var integer
     */
    public $default_ship_address;

    /**
     *
     * @var integer
     */
    public $default_payment;

    /**
     *
     * @var integer
     */
    public $country_id;

    /**
     *
     * @var integer
     */
    public $country_state_id;

    /**
     *
     * @var string
     */
    public $short_description;

    /**
     * Initialize method for model
     */
    public function initialize()
    {

    }

    /**
     * Validation user
     */
    public function validation()
    {
        //Validate email
        $this->validate(
            new ModelValidatorEmail(
                [
                    'field' => 'email',
                    'required' => true,
                ]
            )
        );

        //Check unique email
        $this->validate(new Uniqueness([
            'field' => 'email',
            'message' => __('Email already exist')
        ]));

        //Validate fist name
        $this->validate(new StringLength([
            'field' => 'first_name',
            'max' => 32,
            'min' => 1
        ]));

        //Validate last name
        $this->validate(new StringLength([
            'field' => 'last_name',
            'max' => 32,
            'min' => 1
        ]));

        //Validate gender
        if ($this->gender != null) {
            $this->validate(new Inclusionin([
                'field' => 'gender',
                'domain' => ['0', '1']
            ]));
        }

        //Validate country id
        if ($this->country_id != null) {
            $this->country_id = (int)$this->country_id;
            $country = Countries::findFirst([
                'conditions' => 'country_id = ?0',
                'bind' => [$this->country_id]
            ]);
            if (!$country) {
                $message = new Message(
                    'Country not exist',
                    'country_state_id',
                    'error'
                );
                $this->appendMessage($message);
                return false;
            }
        }

        //Validate country state id
        if ($this->country_state_id != null) {
            $this->country_state_id = (int)$this->country_state_id;
            $location = CountryStates::findFirst([
                'conditions' => 'country_state_id = ?0',
                'bind' => [$this->country_state_id]
            ]);
            if (!$location) {
                $message = new Message(
                    'State not exist',
                    'country_state_id',
                    'error'
                );
                $this->appendMessage($message);
                return false;
            }
        }

        //Validate short description
        if ($this->short_description != null) {
            $this->validate(new StringLength([
                'field' => 'short_description',
                'max' => 180,
                'min' => 15
            ]));
        }

        //Validate mobile
        if ($this->mobile != null) {
            $this->validate(new StringLength([
                'field' => 'mobile',
                'max' => 11,
                'min' => 10
            ]));
        }

        //Validation
        if ($this->validationHasFailed() == true) {
            return false;
        }

        return true;
    }

    /**
     * Generate password
     *
     * @param string $password
     */
    public function generatePassword($password)
    {
        if ($password != '') {
            $this->password = $password;
            /**
             * @var \Phalcon\Security $security
             */
            $security = $this->getDI()->get('security');

            //Generate salt
            $this->salt = $security->getSaltBytes();
            //Hash password
            $this->password = $security->hash($this->password . $this->salt);
        }
    }

    /**
     * Check password
     *
     * @param string $password
     * @param string $salt
     * @param string $passwordHash
     * @return bool
     */
    public static function checkPassword($password, $salt, $passwordHash)
    {
        /**
         * @var \Phalcon\Security $security
         */
        $security = Di::getDefault()->get('security');

        if ($passwordHash != null && ($security->checkHash($password . $salt, $passwordHash) || md5($password) == $passwordHash)) {
            return true;
        }

        return false;
    }

    /**
     * Login current user
     *
     * @return bool
     */
    public function loginCurrentUSer()
    {
        /**
         * @var UserRoles $role
         */
        $role = UserRoles::findFirst($this->role_id);
        $acl = json_decode($role->acl, true);
        /**
         * @var \ZCMS\Core\ZSession $session
         */
        $session = Di::getDefault()->get('session');
        /**
         * @var \Phalcon\Security $security
         */
        $security = Di::getDefault()->get('security');
        $token = $security->getToken();
        $session->set('auth', [
            'full_name' => $this->first_name . ' ' . $this->last_name,
            'email' => $this->email,
            'id' => $this->user_id,
            'role' => $this->role_id,
            'rules' => $acl['rules'],
            'gender' => $this->gender,
            'linkAccess' => $acl['links'],
            'language' => $this->language_code,
            'avatar' => $this->avatar,
            'token' => $token,
            'coin' => (float)$this->coin,
            'created_at' => date('Y-m-d', strtotime($this->created_at)),
            'is_super_admin' => $role->is_super_admin,
            'last_use_admin' => time(),
        ]);
        return true;
    }

    /**
     * Login
     *
     * @param string $email
     * @param string $password
     * @return bool
     */
    public static function login($email, $password)
    {
        /**
         * @var Users $user
         */
        $user = Users::findFirst([
            'conditions' => 'is_active = 1 AND email = ?0',
            'bind' => [$email]
        ]);

        /**
         * @var \Phalcon\Security $security
         */
        $security = Di::getDefault()->get('security');

        if ($user && Users::checkPassword($password, $user->salt, $user->password)) {
            $token = $security->getToken();
            /**
             * @var UserRoles $role
             */
            $role = UserRoles::findFirst($user->role_id);
            $acl = json_decode($role->acl, true);
            /**
             * @var \ZCMS\Core\ZSession $session
             */
            $session = Di::getDefault()->get('session');
            $session->set('auth', [
                'full_name' => $user->first_name . ' ' . $user->last_name,
                'email' => $user->email,
                'id' => $user->user_id,
                'role' => $user->role_id,
                'rules' => $acl['rules'],
                'gender' => $user->gender,
                'linkAccess' => $acl['links'],
                'language' => $user->language_code,
                'avatar' => $user->avatar,
                'token' => $token,
                'coin' => (float)$user->coin,
                'created_at' => date('Y-m-d', strtotime($user->created_at)),
                'is_super_admin' => $role->is_super_admin,
                'last_use_admin' => time(),
            ]);
            return true;
        }
        return false;
    }

    /**
     * Get current user login
     *
     * @return array
     */
    public static function getCurrentUser()
    {
        /**
         * @var \ZCMS\Core\ZSession $session
         */
        $session = Di::getDefault()->get('session');
        return $session->get('auth');
    }

    /**
     * Get current user login
     * @return null|Users
     */
    public static function getInfoCurrentUser()
    {
        /**
         * @var \ZCMS\Core\ZSession $session
         */
        $session = Di::getDefault()->get('session');
        $auth = $session->get('auth');

        if ($auth) {
            return Users::findFirst($auth['id']);
        }
        return null;
    }

    /**
     * Check user logged in
     *
     * @return bool
     */
    public static function isLoggedIn()
    {
        return (bool)Users::getCurrentUser();
    }

    /**
     * Check user exits
     *
     * @param string $email
     * @return bool
     */
    public static function checkUserExists($email)
    {
        return (bool)self::findFirst([
            'conditions' => 'email = ?0',
            'bind' => [$email]
        ]);
    }
}
