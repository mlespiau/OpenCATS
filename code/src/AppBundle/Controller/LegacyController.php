<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class LegacyController extends Controller
{

    /**
     * @Route("/", name="_proxyIndex")
     */
    public function proxyIndexAction()
    {
        return $this->proxyAction('index');
    }

    /**
     * @Route("/{fileName}.php", name="_proxy", requirements={"fileName":"\w+"})
     */
    public function proxyAction($fileName)
    {
        ob_start();
        define('LEGACY_ROOT', dirname(dirname($this->get('kernel')->getRootDir())));
        if ($fileName == 'index')
        {
            // A properly formatted query string will look like this:
            // /index.php?m=candidates&a=edit&candidateID=55
            // Do we need to run the installer?
            if (!file_exists(LEGACY_ROOT . '/INSTALL_BLOCK') && !isset($_POST['performMaintenence']))
            {
                include(LEGACY_ROOT . '/modules/install/notinstalled.php');
                die();
            }

            // FIXME: Config file setting.
            @ini_set('memory_limit', '64M');

            /* Hack to make CATS work with E_STRICT. */
            if (function_exists('date_default_timezone_set'))
            {
                @date_default_timezone_set(date_default_timezone_get());
            }

            /* Start error handler if ASP error handler exists and this isn't a localhost
             * connection.
             */
            if (file_exists('modules/asp/lib/ErrorHandler.php') &&
                @$_SERVER['REMOTE_ADDR'] !== '127.0.0.1' &&
                @$_SERVER['REMOTE_ADDR'] !== '::1' &&
                substr(@$_SERVER['REMOTE_ADDR'], 0, 3) !== '10.')
            {
                include_once(LEGACY_ROOT . '/modules/asp/lib/ErrorHandler.php');
                $errorHandler = new ErrorHandler();
            }

            include_once(LEGACY_ROOT . '/config.php');
            include_once(LEGACY_ROOT . '/constants.php');
            include_once(LEGACY_ROOT . '/lib/CommonErrors.php');
            include_once(LEGACY_ROOT . '/lib/CATSUtility.php');
            include_once(LEGACY_ROOT . '/lib/DatabaseConnection.php');
            include_once(LEGACY_ROOT . '/lib/Template.php');
            include_once(LEGACY_ROOT . '/lib/Users.php');
            include_once(LEGACY_ROOT . '/lib/MRU.php');
            include_once(LEGACY_ROOT . '/lib/Hooks.php');
            include_once(LEGACY_ROOT . '/lib/Session.php'); /* Depends: MRU, Users, DatabaseConnection. */
            include_once(LEGACY_ROOT . '/lib/UserInterface.php'); /* Depends: Template, Session. */
            include_once(LEGACY_ROOT . '/lib/ModuleUtility.php'); /* Depends: UserInterface */
            include_once(LEGACY_ROOT . '/lib/TemplateUtility.php'); /* Depends: ModuleUtility, Hooks */


            /* Give the session a unique name to avoid conflicts and start the session. */
            @session_name(CATS_SESSION_NAME);
            session_start();

            /* Try to prevent caching. */
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

            // This function assures to strip the values from
            // request arrays even if as values are arrays not only values
            function stripslashes_deep($value)
            {
                $value = is_array($value) ?
                    array_map('stripslashes_deep', $value) :
                    stripslashes($value);

                return $value;
            }

            /* Make sure we aren't getting screwed over by magic quotes. */
            if (get_magic_quotes_runtime())
            {
                set_magic_quotes_runtime(0);
            }
            if (get_magic_quotes_gpc())
            {
                include_once(LEGACY_ROOT . '/lib/ArrayUtility.php');

                $_GET     = array_map('stripslashes_deep', $_GET);
                $_POST    = array_map('stripslashes_deep', $_POST);
                $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
                $_GET     = \ArrayUtility::arrayMapKeys('stripslashes_deep', $_GET);
                $_POST    = \ArrayUtility::arrayMapKeys('stripslashes_deep', $_POST);
                $_REQUEST = \ArrayUtility::arrayMapKeys('stripslashes_deep', $_REQUEST);
            }
            /* Objects can't be stored in the session if session.auto_start is enabled. */
            if (ini_get('session.auto_start') !== '0' &&
                ini_get('session.auto_start') !== 'Off')
            {
                die('CATS Error: session.auto_start must be set to 0 in php.ini.');
            }

            /* Proper extensions loaded?! */
            if (!function_exists('mysql_connect') || !function_exists('session_start'))
            {
                die('CATS Error: All required PHP extensions are not loaded.');
            }

            /* Make sure we have a Session object stored in the user's session. */
            if (!isset($_SESSION['CATS']) || empty($_SESSION['CATS']))
            {
                $_SESSION['CATS'] = new \CATSSession();
            }

            /* Start timer for measuring server response time. Displayed in footer. */
            $_SESSION['CATS']->startTimer();

            /* Check to see if the server went through a SVN update while the session
             * was active.
             */
            $_SESSION['CATS']->checkForcedUpdate();


            /* We would hook this, but the hooks aren't loaded by the time this code executes.
             * if ASP module exists (code is running on catsone.com), load the website by default
             * rather than the login page.
             */
            if (\ModuleUtility::moduleExists("asp") && \ModuleUtility::moduleExists("website"))
            {
                // FIXME: Can we optimize this a bit...?
                include_once(LEGACY_ROOT . '/modules/asp/lib/General.php');

                if (!(isset($careerPage) && $careerPage) &&
                    !(isset($rssPage) && $rssPage) &&
                    !(isset($xmlPage) && $xmlPage) &&
                    (!isset($_GET['m']) || empty($_GET['m'])) &&
                    (\Asp::getSubDomain() == '' || isset($_GET['a'])))
                {
                    \ModuleUtility::loadModule('website');
                    exit(1);
                }
            }


            /* Check to see if the user level suddenly changed. If the user was changed to disabled,
             * also log the user out.
             */
            // FIXME: This is slow!
            if ($_SESSION['CATS']->isLoggedIn())
            {
                $users = new \Users($_SESSION['CATS']->getSiteID());
                $forceLogoutData = $users->getForceLogoutData($_SESSION['CATS']->getUserID());

                if (!empty($forceLogoutData) && ($forceLogoutData['forceLogout'] == 1 ||
                        $_SESSION['CATS']->getRealAccessLevel() != $forceLogoutData['accessLevel']))
                {
                    $_SESSION['CATS']->setRealAccessLevel($forceLogoutData['accessLevel']);

                    if ($forceLogoutData['accessLevel'] == ACCESS_LEVEL_DISABLED ||
                        $forceLogoutData['forceLogout'] == 1)
                    {
                        /* Log the user out. */
                        $unixName = $_SESSION['CATS']->getUnixName();

                        $_SESSION['CATS']->logout();
                        unset($_SESSION['CATS']);
                        unset($_SESSION['modules']);

                        $URI = 'm=login';

                        if (!empty($unixName) && $unixName != 'demo')
                        {
                            $URI .= '&s=' . $unixName;
                        }

                        \CATSUtility::transferRelativeURI($URI);
                        die();
                    }
                }
            }

            /* Check to see if we are supposed to display the career page. */
            if (((isset($careerPage) && $careerPage) ||
                (isset($_GET['showCareerPortal']) && $_GET['showCareerPortal'] == '1')))
            {
                \ModuleUtility::loadModule('careers');
            }

            /* Check to see if we are supposed to display an rss page. */
            else if (isset($rssPage) && $rssPage)
            {
                \ModuleUtility::loadModule('rss');
            }

            else if (isset($xmlPage) && $xmlPage)
            {
                \ModuleUtility::loadModule('xml');
            }

            /* Check to see if the user was forcibly logged out (logged in from another browser). */
            else if ($_SESSION['CATS']->isLoggedIn() &&
                (!isset($_GET['m']) || \ModuleUtility::moduleRequiresAuthentication($_GET['m'])) &&
                $_SESSION['CATS']->checkForceLogout())
            {
                // FIXME: Unset session / etc.?
                \ModuleUtility::loadModule('login');
            }

            /* If user specified a module, load it; otherwise, load the home module. */
            else if (!isset($_GET['m']) || empty($_GET['m']))
            {
                if ($_SESSION['CATS']->isLoggedIn())
                {
                    $_SESSION['CATS']->logPageView();

                    if (!eval(\Hooks::get('INDEX_LOAD_HOME'))) return;

                    \ModuleUtility::loadModule('home');
                }
                else
                {
                    \ModuleUtility::loadModule('login');
                }
            }
            else
            {
                if ($_GET['m'] == 'logout')
                {
                    /* There isn't really a logout module. It's just a few lines. */
                    $unixName = $_SESSION['CATS']->getUnixName();

                    $_SESSION['CATS']->logout();
                    unset($_SESSION['CATS']);
                    unset($_SESSION['modules']);

                    $URI = 'm=login';
                    /* Local demo account doesn't relogin. */
                    if (!empty($unixName) && $unixName != 'demo')
                    {
                        $URI .= '&s=' . $unixName;
                    }

                    if (isset($_GET['message']))
                    {
                        $URI .= '&message=' . urlencode($_GET['message']);
                    }

                    if (isset($_GET['messageSuccess']))
                    {
                        $URI .= '&messageSuccess=' . urlencode($_GET['messageSuccess']);
                    }

                    /* catsone.com demo domain doesn't relogin. */
                    if (strpos(\CATSUtility::getIndexName(), '://demo.catsone.com') !== false)
                    {
                        \CATSUtility::transferURL('http://www.catsone.com');
                    }
                    else
                    {
                        \CATSUtility::transferRelativeURI($URI);
                    }
                }
                else if (!\ModuleUtility::moduleRequiresAuthentication($_GET['m']))
                {
                    /* No authentication required; load the module. */
                    \ModuleUtility::loadModule($_GET['m']);
                }
                else if (!$_SESSION['CATS']->isLoggedIn())
                {
                    /* User isn't logged in and authentication is required; send the user
                     * to the login page.
                     */
                    \ModuleUtility::loadModule('login');
                }
                else
                {
                    /* Everything's good; load the requested module. */
                    $_SESSION['CATS']->logPageView();
                    \ModuleUtility::loadModule($_GET['m']);
                }
            }

            if (isset($errorHandler))
            {
                $errorHandler->reportErrors();
            }
        }
        else if ($fileName == 'ajax')
        {
            /*
             * CATS
             * AJAX Delegation Module
             *
             * CATS Version: 0.9.3 Inferno
             *
             * Copyright (C) 2005 - 2007 Cognizo Technologies, Inc.
             *
             *
             * The contents of this file are subject to the CATS Public License
             * Version 1.1a (the "License"); you may not use this file except in
             * compliance with the License. You may obtain a copy of the License at
             * http://www.catsone.com/.
             *
             * Software distributed under the License is distributed on an "AS IS"
             * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
             * License for the specific language governing rights and limitations
             * under the License.
             *
             * The Original Code is "CATS Standard Edition".
             *
             * The Initial Developer of the Original Code is Cognizo Technologies, Inc.
             * Portions created by the Initial Developer are Copyright (C) 2005 - 2007
             * (or from the year in which this file was created to the year 2007) by
             * Cognizo Technologies, Inc. All Rights Reserved.
             *
             *
             * A properly formatted POST string will look like this:
             *
             *    f=myFunction&arg=myArgument&...
             *
             *
             * $Id: ajax.php 3431 2007-11-06 21:10:12Z will $
             */


            include_once(LEGACY_ROOT . '/config.php');
            include_once(LEGACY_ROOT . '/constants.php');
            include_once(LEGACY_ROOT . '/lib/DatabaseConnection.php');
            include_once(LEGACY_ROOT . '/lib/Session.php'); /* Depends: MRU, Users, DatabaseConnection. */
            include_once(LEGACY_ROOT . '/lib/AJAXInterface.php');
            include_once(LEGACY_ROOT . '/lib/CATSUtility.php');


            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

            /* Make sure we aren't getting screwed over by magic quotes. */
            if (get_magic_quotes_runtime())
            {
                set_magic_quotes_runtime(0);
            }
            if (get_magic_quotes_gpc())
            {
                $_GET     = array_map('stripslashes', $_GET);
                $_POST    = array_map('stripslashes', $_POST);
                $_REQUEST = array_map('stripslashes', $_REQUEST);
            }
            if (!isset($_REQUEST['f']) || empty($_REQUEST['f']))
            {
                header('Content-type: text/xml');
                echo '<?xml version="1.0" encoding="', AJAX_ENCODING, '"?>', "\n";
                echo(
                    "<data>\n" .
                    "    <errorcode>-1</errorcode>\n" .
                    "    <errormessage>No function specified.</errormessage>\n" .
                    "</data>\n"
                );

                die();
            }

            if (strpos($_REQUEST['f'], ':') === false)
            {
                $function = preg_replace("/[^A-Za-z0-9]/", "", $_REQUEST['f']);

                $filename = sprintf(LEGACY_ROOT . '/ajax/%s.php', $function);
            }
            else
            {
                /* Split function parameter into module name and function name. */
                $parameters = explode(':', $_REQUEST['f']);

                $module = preg_replace("/[^A-Za-z0-9]/", "", $parameters[0]);
                $function = preg_replace("/[^A-Za-z0-9]/", "", $parameters[1]);

                $filename = sprintf(LEGACY_ROOT . '/modules/%s/ajax/%s.php', $module, $function);
            }

            if (!is_readable($filename))
            {
                header('Content-type: text/xml');
                echo '<?xml version="1.0" encoding="', AJAX_ENCODING, '"?>', "\n";
                echo(
                    "<data>\n" .
                    "    <errorcode>-1</errorcode>\n" .
                    "    <errormessage>Invalid function name.</errormessage>\n" .
                    "</data>\n"
                );

                die();
            }

            $filters = array();

            if (!isset($_REQUEST['nobuffer']))
            {
                include_once(LEGACY_ROOT . '/lib/Hooks.php');

                ob_start();
                include($filename);
                $output = ob_get_clean();

                if (!eval(\Hooks::get('AJAX_HOOK'))) return;

                if (!isset($_REQUEST['nospacefilter']))
                {
                    $output = preg_replace('/^\s+/m', '', $output);
                }

                foreach ($filters as $filter)
                {
                    eval($filter);
                }

                echo($output);
            }
            else
            {
                include($filename);
            }

        }
        return new Response(ob_get_clean());
    }

}