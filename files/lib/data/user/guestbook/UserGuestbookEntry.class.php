<?php
// wcf imports
require_once(WCF_DIR.'lib/data/message/Message.class.php');
require_once(WCF_DIR.'lib/data/user/UserProfile.class.php');

/**
 * Represents a guestbook entry.
 *
 * @author	Stefan Hahn
 * @copyright	2012 Stefan Hahn
 * @license	Simplified BSD License License <http://projects.swallow-all-lies.com/licenses/simplified-bsd-license.txt>
 * @package	com.leon.wcf.user.guestbook
 * @subpackage	data.user.guestbook
 * @category 	Community Framework
 */
class UserGuestbookEntry extends Message {
	/**
	 * List of all comments on this entry
	 * 
	 * @var UserGuestbookCommentList
	 */
	protected $commentList = null;
	
	/**
	 * Editor object for this entry
	 * 
	 * @var UserGuestbookEntryEditor
	 */
	protected $editor = null;
	
	/**
	 * UserProfile object for the owner of
	 * the guestbook this entry was posted in.
	 * 
	 * @var	UserProfile
	 */
	protected $owner = null;
	
	/**
	 * UserProfile object for the author of
	 * this guestbook entry.
	 * 
	 * @var	UserProfile
	 */
	protected $author = null;
	
	/**
	 * Creates a new guestbook entry object.
	 * 
	 * @param	integer		$entryID
	 * @param	array		$row
	 */
	public function __construct($entryID, $row = null) {
		if ($entryID !== null) {
			$sql = "SELECT		entry.*
				FROM		wcf".WCF_N."_user_guestbook_entry entry
				WHERE		entry.entryID = ".$entryID;
			$row = WCF::getDB()->getFirstRow($sql);
		}
		
		parent::__construct($row);
	}
	
	/**
	 * @see DatabaseObject::handleData()
	 */
	protected function handleData($row) {
		parent::handleData($row);
		$this->messageID = $this->entryID;
	}
	
	/**
	 * Gets editor object for this guestbook entry.
	 * 
	 * @return	UserGuestbookEntryEditor
	 */
	public function getEditor() {
		if ($this->editor === null) {
			require_once(WCF_DIR.'lib/data/user/guestbook/UserGuestbookEntryEditor.class.php');
			$this->editor = new UserGuestbookEntryEditor(null, $this->data);
		}
		
		return $this->editor;
	}
	
	/**
	 * Gets the list of guestbook comments for this guestbook entry.
	 * 
	 * @return	UserGuestbookCommentList
	 */
	public function getComments() {
		if ($this->commentList === null) {
			// TODO: check if this would be better public because it's editable
			require_once(WCF_DIR.'lib/data/user/guestbook/UserGuestbookCommentList.class.php');
			$this->commentList = new UserGuestbookCommentList();
			$this->commentList->sqlConditions .= 'comment.entryID = '.$this->entryID;
			$this->commentList->readObjects();
		}
		
		return $this->commentList->getObjects();
	}
	
	/**
	 * Gets the owner for the guestbook this entry was posted in.
	 * 
	 * @return	UserProfile
	 */
	public function getOwner() {
		if ($this->owner === null) {
			$this->owner = new UserProfile($this->ownerID);
		}
		
		return $this->owner;
	}
	
	/**
	 * Sets the owner for the guestbook this entry was posted in.
	 * 
	 * @param	UserProfile	$user
	 */
	public function setOwner(UserProfile $user) {
		$this->owner = $user;
	}
	
	/**
	 * Gets the author of this guestbook entry.
	 * 
	 * @return	UserProfile
	 */
	public function getAuthor() {
		if ($this->author === null) {
			$this->author = new UserProfile($this->userID);
		}
		
		return $this->author;
	}
	
	/**
	 * Sets the author of this guestbook entry.
	 * 
	 * @param	UserProfile	$user
	 */
	public function setAuthor(UserProfile $user) {
		$this->author = $user;
	}
}
