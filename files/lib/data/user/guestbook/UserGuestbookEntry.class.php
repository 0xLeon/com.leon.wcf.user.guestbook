<?php
// wcf imports
require_once(WCF_DIR.'lib/data/message/Message.class.php');
require_once(WCF_DIR.'lib/data/user/UserProfile.class.php');
require_once(WCF_DIR.'lib/data/user/guestbook/UserGuestbookCommentList.class.php');

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
	 * Comment list object for this guestbook entry 
	 * 
	 * @var UserGuestbookCommentList
	 */
	protected $commentList = null;
	
	/**
	 * List of all comments on this entry
	 * 
	 * @var	array<UserGuestbookComment>
	 */
	protected $comments = null;
	
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
			$sql = "SELECT		entry.*,
						COUNT(comment.commentID) AS commentCount,
						GROUP_CONCAT(comment.commentID ORDER BY comment.commentID ASC SEPARATOR ',') AS commentIDs
				FROM		wcf".WCF_N."_user_guestbook_entry entry
				LEFT JOIN	wcf".WCF_N."_user_guestbook_comment comment
				ON		(entry.entryID = comment.entryID)
				WHERE		entry.entryID = ".$entryID;
			$row = WCF::getDB()->getFirstRow($sql);
		}
		
		$this->commentList = new UserGuestbookCommentList();
		
		parent::__construct($row);
	}
	
	/**
	 * @see DatabaseObject::handleData()
	 */
	protected function handleData($row) {
		parent::handleData($row);
		$this->messageID = $this->entryID;
		$this->commentList->sqlConditions .= 'comment.entryID = '.$this->entryID;
		$this->data['commentIDs'] = explode(',', $this->commentIDs);
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
	 * Gets the guestbook comment list object
	 * 
	 * @return	UserGuestbookCommentList
	 */
	public function getCommentList() {
		return $this->commentList();
	}
	
	/**
	 * Gets the list of guestbook comments for this guestbook entry.
	 * 
	 * @return	array<UserGuestbookComment>
	 */
	public function getComments() {
		if ($this->comments === null) {
			if ($this->commentCount > 0) {
				$this->getCommentList()->readObjects();
				$this->comments = $this->getCommentList()->getObjects();
			}
			else {
				$this->comments = array();
			}
		}
		
		return $this->comments;
	}
	
	/**
	 * Sets the list of guestbook comments for this guestbook entry.
	 * 
	 * @param	array<UserGuestbookComment>	$comments
	 */
	public function setComments($comments) {
		$this->comments = $comments;
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
