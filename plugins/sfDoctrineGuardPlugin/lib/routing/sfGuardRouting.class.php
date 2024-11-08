<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id$
 */
class sfGuardRouting
{
    /**
     * Listens to the routing.load_configuration event.
     *
     * @param sfEvent An sfEvent instance
     * @static
     */
    public static function listenToRoutingLoadConfigurationEvent(sfEvent $event)
    {
        $r = $event->getSubject();

        // preprend our routes
        $r->prependRoute('sf_guard_signin', new sfRoute('/guard/login', ['module' => 'sfGuardAuth', 'action' => 'signin']));
        $r->prependRoute('sf_guard_signout', new sfRoute('/guard/logout', ['module' => 'sfGuardAuth', 'action' => 'signout']));
    }

    public static function addRouteForForgotPassword(sfEvent $event)
    {
        $r = $event->getSubject();

        $r->prependRoute('sf_guard_forgot_password', new sfRoute('/guard/forgot_password', ['module' => 'sfGuardForgotPassword', 'action' => 'index']));
        $r->prependRoute('sf_guard_forgot_password_change', new sfDoctrineRoute('/guard/forgot_password/:unique_key', [
            'module' => 'sfGuardForgotPassword',
            'action' => 'change',
        ], [
            'sf_method' => ['get', 'post'],
        ], [
            'model' => 'sfGuardForgotPassword',
            'type' => 'object',
        ]));
    }

    /**
     * Adds an sfDoctrineRouteCollection collection to manage users.
     *
     * @param sfEvent $event
     * @static
     */
    public static function addRouteForUser(sfEvent $event)
    {
        $event->getSubject()->prependRoute('sf_guard_user', new sfDoctrineRouteCollection([
            'name'                => 'sf_guard_user',
            'model'               => 'sfGuardUser',
            'module'              => 'sfGuardUser',
            'prefix_path'         => 'guard/users',
            'with_wildcard_routes' => true,
            'collection_actions'  => ['filter' => 'post', 'batch' => 'post'],
            'requirements'        => [],
        ]));
    }

    /**
     * Adds an sfDoctrineRouteCollection collection to manage groups.
     *
     * @param sfEvent $event
     * @static
     */
    public static function addRouteForGroup(sfEvent $event)
    {
        $event->getSubject()->prependRoute('sf_guard_group', new sfDoctrineRouteCollection([
            'name'                => 'sf_guard_group',
            'model'               => 'sfGuardGroup',
            'module'              => 'sfGuardGroup',
            'prefix_path'         => 'guard/groups',
            'with_wildcard_routes' => true,
            'collection_actions'  => ['filter' => 'post', 'batch' => 'post'],
            'requirements'        => [],
        ]));
    }

    /**
     * Adds an sfDoctrineRouteCollection collection to manage permissions.
     *
     * @param sfEvent $event
     * @static
     */
    public static function addRouteForPermission(sfEvent $event)
    {
        $event->getSubject()->prependRoute('sf_guard_permission', new sfDoctrineRouteCollection([
            'name'                => 'sf_guard_permission',
            'model'               => 'sfGuardPermission',
            'module'              => 'sfGuardPermission',
            'prefix_path'         => 'guard/permissions',
            'with_wildcard_routes' => true,
            'collection_actions'  => ['filter' => 'post', 'batch' => 'post'],
            'requirements'        => [],
        ]));
    }

    /**
     * Adds an sfRoute for registration.
     *
     * @param sfEvent $event
     * @static
     */
    public static function addRouteForRegister(sfEvent $event)
    {
        $event->getSubject()->prependRoute('sf_guard_register', new sfRoute('/guard/register', ['module' => 'sfGuardRegister', 'action' => 'index']));
    }
}
