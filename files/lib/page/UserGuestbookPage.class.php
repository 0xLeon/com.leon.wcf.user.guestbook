<?php
// wcf imports
require_once(WCF_DIR.'lib/page/MultipleLinkPage.class.php');
require_once(WCF_DIR.'lib/data/user/UserProfileFrame.class.php');
require_once(WCF_DIR.'lib/data/user/guestbook/UserGuestbookEntryList.class.php');
require_once(WCF_DIR.'lib/system/event/EventHandler.class.php');

/**
 * Shows the user profile guestbook page.
 *
 * @author	Stefan Hahn
 * @copyright	2012 Stefan Hahn
 * @license	Simplified BSD License License <http://projects.swallow-all-lies.com/licenses/simplified-bsd-license.txt>
 * @package	com.leon.wcf.user.guestbook
 * @subpackage	page
 * @category 	Community Framework
 */
class UserGuestbookPage extends MultipleLinkPage {
	public $templateName = 'userGuestbook';
	public $itemsPerPage = USER_GUESTBOOK_ENTRIES_PER_PAGE;
	
	/**
	 * ID of the guestbook entry to jump to.
	 * 
	 * @var	integer
	 */
	public $entryID = 0;
	
	/**
	 * List object of guestbook entries. 
	 * 
	 * @var	UserGuestbookEntryList
	 */
	public $entryList = null;
	
	/**
	 * User profile frame.
	 * 
	 * @var	UserProfileFrame
	 */
	public $frame = null;
	
	/**
	 * User permissions
	 * 
	 * @var	array<bool>
	 */
	public $userPermissions = null;
	
	/**
	 * Moderator permissions
	 * 
	 * @var	array<bool>
	 */
	public $modPermissions = null;
	
	/**
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['entryID'])) $this->entryID = intval($_REQUEST['entryID']);
		
		$this->frame = new UserProfileFrame($this);
		
		if (WCF::getUser()->guestbookEntriesPerPage) $this->itemsPerPage = WCF::getUser()->guestbookEntriesPerPage;
	}
	
	/**
	 * @see	Page::readData()
	 */
	public function readData() {
		$this->entry = new UserGuestbookEntry((($this->entryID) ? $this->entryID : null), array());
		$this->entryList = new UserGuestbookEntryList();
		$this->userPermissions = UserGuestbookUtil::getUserPermissions($this->frame->getUser());
		$this->modPermissions = UserGuestbookUtil::getModeratorPermissions($this->frame->getUser());
		
		$this->entryList->sqlConditions = 'entry.ownerID = '.$this->frame->getUserID();
		$this->entryList->sqlOrderBy = 'entry.time DESC, entry.entryID DESC';
		
		$this->verifyData();
		$this->verifyPermissions();
		
		if ($this->entry->entryID) $this->calculatePageNo();
		
		parent::readData();
		
		$this->entryList->sqlOffset = $this->startIndex - 1;
		$this->entryList->sqlLimit = $this->itemsPerPage;
		$this->entryList->readObjects();
		$this->entryList->readOwners();
		$this->entryList->readAuthors();
	}
	
	/**
	 * @see	MultipleLinkPage::countItems()
	 */
	public function countItems() {
		parent::countItems();
		
		return $this->entryList->countObjects();
	}
	
	/**
	 * @see Page::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		$this->frame->assignVariables();
		WCF::getTPL()->assign(array(
			'commentID' => '#{commentID}',
			'authorID' => '#{authorID}',
			'authorName' => '#{authorname}',
			'time' => '#{time}',
			'message' => '#{message}',
			'avatarPath' => '#{avatarPath}',
			'avatarWidth' => '#{avatarWidth}',
			'avatarHeight' => '#{avatarHeight}'
		));
		WCF::getTPL()->assign(array(
			'userPermissions' => $this->userPermissions,
			'modPermissions' => $this->modPermissions,
			'entries' => $this->entryList->getObjects(),
			'jsTemplateComment' => StringUtil::replace("\n", '', WCF::getTPL()->fetch('userGuestbookCommentBox'))
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		UserProfileMenu::getInstance()->setActiveMenuItem('wcf.user.profile.menu.link.guestbook');
		
		if (!MODULE_USER_GUESTBOOK) {
			throw new IllegalLinkException();
		}
		
		parent::show();
	}
	
	/**
	 * Calculates the page of the entry if there's an entryID given.
	 */
	protected function calculatePageNo() {
		$sql = "SELECT	COUNT(*) AS count
			FROM	wcf".WCF_N."_user_guestbook_entry entry
			WHERE	".$this->entryList->sqlConditions."
				AND entry.time >= ".$this->entry->time;
		$row = WCF::getDB()->getFirstRow($sql);
		$this->pageNo = intval(ceil($row['count'] / $this->itemsPerPage));
	}
	
	public function verifyData() {
		if ($this->entryID && !$this->entry->entryID) {
			throw new IllegalLinkException();
		}
	}
	
	public function verifyPermissions() {
		try {
			if (!$this->userPermissions['canUseGuestbook']) {
				throw new NamedUserException(WCF::getLanguage()->get('wcf.user.profile.error.guestbook.permissionDenied'));
			}
			
			if (!$this->frame->getUser()->getPermission('user.guestbook.canUseGuestbook')) {
				throw new NamedUserException(WCF::getLanguage()->getDynamicVariable('wcf.user.profile.error.guestbook.notActivated', array('username' => $this->frame->getUser()->username)));
			}
			
			if (!$this->userPermissions['canViewGuestbook']) {
				throw new NamedUserException(WCF::getLanguage()->getDynamicVariable('wcf.user.profile.error.guestbook.protected', array('username' => $this->frame->getUser()->username)));
			}
		}
		catch (NamedUserException $e) {
			$this->frame->assignVariables();
			WCF::getTPL()->assign('errorMessage', $e->getMessage());
			WCF::getTPL()->display('userProfileAccessDenied');
			exit;
		}
	}
}
