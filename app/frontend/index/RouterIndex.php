<?php

use Phalcon\Mvc\Router\Group;

/**
 * Class RouterIndex
 */
class RouterIndex extends Group
{
    public function initialize()
    {
        $this->setPaths([
            'module' => 'index',
            'namespace' => 'ZCMS\Frontend\Index\Controllers'
        ]);

        $this->setPrefix('/');

        $this->add('user/logout(/)?', [
            'controller' => 'logout',
            'action' => 'index',
        ]);

        $this->add('user/login(/)?', [
            'controller' => 'login',
            'action' => 'index',
        ]);

        $this->add('user/register(/)?', [
            'controller' => 'register',
            'action' => 'index',
        ]);

        $this->add('user/forgot-password(/)?', [
            'controller' => 'forgot-password',
            'action' => 'index',
        ]);
    }
}

