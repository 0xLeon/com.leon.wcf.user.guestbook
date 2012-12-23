<?php
// wcf imports
require_once(WCF_DIR.'lib/page/AbstractPage.class.php');
require_once(WCF_DIR.'lib/data/user/guestbook/UserGuestbookEntry.class.php');

/**
 * Outputs an XML document containing all comment data needed for ajax processing.
 * 
 * @author	Stefan Hahn
 * @copyright	2012 Stefan Hahn
 * @license	Simplified BSD License License <http://projects.swallow-all-lies.com/licenses/simplified-bsd-license.txt>
 * @package	com.leon.wcf.user.guestbook
 * @subpackage	page
 * @category 	Community Framework
 */
class UserGuestbookCommentXMLListPage extends AbstractPage {
	/**
	 * Guestbook entry ID
	 * 
	 * @var	integer
	 */
	public $entryID = 0;
	
	/**
	 * Guestbook entry object
	 * 
	 * @var	UserGuestbookEntry
	 */
	public $entry = null;
	
	/**
	 * Guestbook comment list
	 * 
	 * @var	UserGuestbookCommentList
	 */
	public $commentList = null;
	
	/**
	 * Array of all comments
	 * 
	 * @var	array<UserGuestbookComment>
	 */
	public $comments = null;
	
	/**
	 * User permissions
	 * 
	 * @var	array<bool>
	 */
	public $userPermissions = null;
	
	/**
	 * @see	Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['entryID'])) $this->entryID = intval($_REQUEST['entryID']);
	}
	
	/**
	 * @see	Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		$this->entry = new UserGuestbookEntry($this->entryID);
		
		$this->verifyData();
		$this->verifyPermissions();
		
		if (!$this->modPermissions['canReadDeletedEntry']) $this->entry->getCommentList()->sqlConditions .= ', comment.isDeleted = 0';
		$this->entry->getCommentList()->sqlOrderBy .= "comment.time ASC, comment.commentID ASC";
		$this->comments = $this->entry->getComments();
		
	}
	
	/**
	 * @see	Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'comments' => $this->comments
		));
	}
	
	/**
	 * @see	Page::show()
	 */
	public function show() {
		if (!MODULE_USER_GUESTBOOK) {
			throw new IllegalLinkException();
		}
		
		parent::show();
		
		header('Content-type: text/xml');
		WCF::getTPL()->display('userGuestbookXML', false);
		exit;
	}
	
	/**
	 * Checks if the specified resources actually exists.
	 */
	public function verifyData() {
		if (!$this->entry->entryID) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * Checks if the user has the permission to view the specified resources.
	 */
	public function verifyPermissions() {
		$this->userPermissions = UserGuestbookUtil::getUserPermissions($this->entry->getOwner());
		
		if (!$this->userPermissions['canUseGuestbook'] || !$this->userPermissions['canViewGuestbook'] || !$this->entry->getOwner()->canViewProfile()) {
			throw new PermissionDeniedException();
		}
		
		if (!$this->entry->getOwner()->getPermission('user.guestbook.canUseGuestbook')) {
			throw new IllegalLinkException();
		}
		
		if ($this->entry->isDeleted && !$this->modPermissions['canReadDeletedEntry']) {
			throw new PermissionDeniedException();
		}
	}
} 
