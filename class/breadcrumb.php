<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * XnewsletterBreadcrumb Class
 *
 * @copyright   The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author      lucio <lucio.rota@gmail.com>
 * @package     xnewsletter
 * @since       1.3
 * @version     $Id: breadcrumb.php 12559 2014-06-02 08:10:39Z beckmi $
 *
 * Example:
 * $breadcrumb = new XnewsletterBreadcrumb();
 * $breadcrumb->addLink( 'bread 1', 'index1.php' );
 * $breadcrumb->addLink( 'bread 2', '' );
 * $breadcrumb->addLink( 'bread 3', 'index3.php' );
 * echo $breadcrumb->render();
 */
// defined("XOOPS_ROOT_PATH") || die("XOOPS root path not defined");

class XnewsletterBreadcrumb
{
    public $dirname;
    public $_bread = [];

    /**
     *
     */
    public function __construct()
    {
        $this->dirname =  basename(dirname(__DIR__));
    }

    /**
     * Add link to breadcrumb
     * @param string $title
     * @param string $link
     */
    public function addLink( $title='', $link='' )
    {
        $this->_bread[] = [
            'link'  => $link,
            'title' => $title
        ];
    }

    /**
     * Render xnewsletter BreadCrumb
     *
     */
    public function render()
    {
        if ( !isset($GLOBALS['xoTheme']) || !is_object($GLOBALS['xoTheme'])  ) {
            include_once $GLOBALS['xoops']->path('/class/theme.php');
            $GLOBALS['xoTheme'] = new xos_opal_Theme();
            }

        require_once $GLOBALS['xoops']->path('class/template.php');
        $breadcrumbTpl = new XoopsTpl();
        $breadcrumbTpl->assign('breadcrumb', $this->_bread);
        $html = $breadcrumbTpl->fetch('db:' . $this->dirname . '_common_breadcrumb.tpl');
        unset($breadcrumbTpl);

        return $html;
    }
}
