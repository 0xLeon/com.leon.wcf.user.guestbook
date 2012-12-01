<?php
// wcf imports
require_once(WCF_DIR.'lib/page/MultipleLinkPage.class.php');
require_once(WCF_DIR.'lib/data/user/UserProfileFrame.class.php');
require_once(WCF_DIR.'lib/data/user/guestbook/UserGuestbookEntryList.class.php');

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
	 * @see Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		$this->frame = new UserProfileFrame($this);
		
		if (WCF::getUser()->guestbookEntriesPerPage) $this->itemsPerPage = WCF::getUser()->guestbookEntriesPerPage;
	}
	
	/**
	 * @see	Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		$this->entryList = new UserGuestbookEntryList();
		$this->entryList->sqlOffset = ($this->pageNo - 1) * $this->itemsPerPage;
		$this->entryList->sqlLimit = $this->itemsPerPage;
		$this->entryList->sqlConditions = 'entry.ownerID = '.$this->frame->getUserID();
		$this->entryList->readObjects();
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
			'entries' => $this->entryList->getObjects()
		));
	}
	
	/**
	 * @see Page::show()
	 */
	public function show() {
		UserProfileMenu::getInstance()->setActiveMenuItem('wcf.user.profile.menu.link.guestbook');
		WCF::getUser()->checkPermission('user.guestbook.canViewGuestbook');
		
		if (!MODULE_USER_GUESTBOOK || !$this->frame->getUser()->getPermission('user.guestbook.canUseGuestbook')) {
			throw new IllegalLinkException();
		}
		
		parent::show();
	}
}
