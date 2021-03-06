<?php
/**
 * ****************************************************************************
 *  - A Project by Developers TEAM For Xoops - ( https://xoops.org )
 * ****************************************************************************
 *  XNEWSLETTER - MODULE FOR XOOPS
 *  Copyright (c) 2007 - 2012
 *  Goffy ( wedega.com )
 *
 *  You may not change or alter any portion of this comment or credits
 *  of supporting developers from this source code or any supporting
 *  source code which is considered copyrighted (c) material of the
 *  original comment or credit authors.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  ---------------------------------------------------------------------------
 *  @copyright  Goffy ( wedega.com )
 *  @license    GNU General Public License 2.0
 *  @package    xnewsletter
 *  @author     Goffy ( webmaster@wedega.com )
 *
 * ****************************************************************************
 */

$currentFile = basename(__FILE__);
include_once __DIR__ . '/admin_header.php';
xoops_cp_header();

// We recovered the value of the argument op in the URL$
$op             = XoopsRequest::getString('op', 'list');
$attachment_id  = XoopsRequest::getInt('attachment_id', 0);

switch ($op) {
    case 'list' :
    default :
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWATTACHMENT, '?op=new_attachment', 'add');
        echo $indexAdmin->renderButton();
        //
        $limit = $xnewsletter->getConfig('adminperpage');
        $attachmentCriteria = new CriteriaCompo();
        $attachmentCriteria->setSort('attachment_letter_id DESC, attachment_id');
        $attachmentCriteria->setOrder('DESC');
        $attachmentsCount = $xnewsletter->getHandler('attachment')->getCount();
        $start = XoopsRequest::getInt('start', 0);
        $attachmentCriteria->setStart($start);
        $attachmentCriteria->setLimit($limit);
        $attachmentObjs = $xnewsletter->getHandler('attachment')->getAll($attachmentCriteria);
        if ($attachmentsCount > $limit) {
            include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
            $pagenav = new XoopsPageNav($attachmentsCount, $limit, $start, 'start', 'op=list');
            $pagenav = $pagenav->renderNav(4);
        } else {
            $pagenav = '';
        }

        // View Table
        if ($attachmentsCount>0) {
            echo "
            <table class='outer width100' cellspacing='1'>
                <tr>
                    <th class='center width2'>" . _AM_XNEWSLETTER_ATTACHMENT_ID . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_ATTACHMENT_LETTER_ID . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_ATTACHMENT_NAME . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_ATTACHMENT_TYPE . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_ATTACHMENT_SUBMITTER . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_ATTACHMENT_CREATED . "</th>
                    <th class='center width5'>" . _AM_XNEWSLETTER_FORMACTION . '</th>
                </tr>
            ';

            $class = 'odd';

            foreach ($attachmentObjs as $attachment_id => $attachmentObj) {
                echo "<tr class='" . $class . "'>";
                $class = ($class === 'even') ? 'odd' : 'even';
                echo "<td class='center'>" . $attachment_id . '</td>';

                $letter = $xnewsletter->getHandler('letter')->get($attachmentObj->getVar('attachment_letter_id'));
                $title_letter = $letter->getVar('letter_title');
                echo "<td class='center'>" . $title_letter . '</td>';
                echo "<td class='center'>" . $attachmentObj->getVar('attachment_name') . '</td>';
                echo "<td class='center'>" . $attachmentObj->getVar('attachment_type') . '</td>';
                echo "<td class='center'>" . XoopsUser::getUnameFromId($attachmentObj->getVar('attachment_submitter'), 'S') . '</td>';
                echo "<td class='center'>" . formatTimestamp($attachmentObj->getVar('attachment_created'), 'S') . '</td>';

                echo "
                <td class='center width5' nowrap='nowrap'>
                    <a href='?op=edit_attachment&attachment_id=" . $attachment_id . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_edit.png alt='" . _EDIT . "' title='" . _EDIT . "' /></a>
                    &nbsp;
                    <a href='?op=delete_attachment&attachment_id=" . $attachment_id . "'><img src=" . XNEWSLETTER_ICONS_URL . "/xn_delete.png alt='" . _DELETE . "' title='" . _DELETE . "' /></a>
                </td>
                ";
                echo '</tr>';
            }
            echo '</table><br /><br />';
            echo "<br /><div class='center'>" . $pagenav . '</div><br />';
        } else {
            echo "
            <table class='outer width100' cellspacing='1'>
                <tr>
                    <th class='center width2'>" . _AM_XNEWSLETTER_ATTACHMENT_ID . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_ATTACHMENT_LETTER_ID . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_ATTACHMENT_NAME . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_ATTACHMENT_TYPE . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_ATTACHMENT_SUBMITTER . "</th>
                    <th class='center'>" . _AM_XNEWSLETTER_ATTACHMENT_CREATED . "</th>
                    <th class='center width5'>" . _AM_XNEWSLETTER_FORMACTION . '</th>
                </tr>
            </table><br /><br />
            ';
        }
        break;

    case 'new_attachment' :
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_ATTACHMENTLIST, '?op=list', 'list');
        echo $indexAdmin->renderButton();
        //
        $attachmentObj = $xnewsletter->getHandler('attachment')->create();
        $form = $attachmentObj->getForm();
        $form->display();
        break;

    case 'save_attachment' :
        if (!$GLOBALS['xoopsSecurity']->check()) {
           redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }

        $attachmentObj = $xnewsletter->getHandler('attachment')->get($attachment_id);
        $attachmentObj->setVar('attachment_letter_id', XoopsRequest::getInt('attachment_letter_id', 0));
        $attachmentObj->setVar('attachment_name', XoopsRequest::getString('attachment_name', ''));
        $attachmentObj->setVar('attachment_type', XoopsRequest::getInt('attachment_type', 0));
        $attachmentObj->setVar('attachment_submitter', XoopsRequest::getInt('attachment_submitter', 0));
        $attachmentObj->setVar('attachment_created', XoopsRequest::getInt('attachment_created', time()));

        if ($xnewsletter->getHandler('attachment')->insert($attachmentObj)) {
            redirect_header('?op=list', 2, _AM_XNEWSLETTER_FORMOK);
        }

        echo $attachmentObj->getHtmlErrors();
        $form = $attachmentObj->getForm();
        $form->display();
        break;

    case 'edit_attachment' :
        echo $indexAdmin->addNavigation($currentFile);
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_NEWATTACHMENT, '?op=new_attachment', 'add');
        $indexAdmin->addItemButton(_AM_XNEWSLETTER_ATTACHMENTLIST, '?op=list', 'list');
        echo $indexAdmin->renderButton();
        //
        $attachmentObj = $xnewsletter->getHandler('attachment')->get($attachment_id);
        $form = $attachmentObj->getForm();
        $form->display();
        break;

    case 'delete_attachment' :
        $attachmentObj = $xnewsletter->getHandler('attachment')->get($attachment_id);
        if (isset($_POST['ok']) && $_POST['ok'] == 1) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header($currentFile, 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($xnewsletter->getHandler('attachment')->delete($attachmentObj)) {
                redirect_header($currentFile, 3, _AM_XNEWSLETTER_FORMDELOK);
            } else {
                echo $attachmentObj->getHtmlErrors();
            }
        } else {
            xoops_confirm(['ok' => 1, 'attachment_id' => $attachment_id, 'op' => 'delete_attachment'], $_SERVER['REQUEST_URI'], sprintf(_AM_XNEWSLETTER_FORMSUREDEL, $attachmentObj->getVar('attachment_letter_id')));
        }
    break;
}
include_once __DIR__ . '/admin_footer.php';
