<?php
// wcf imports
require_once(WCF_DIR.'lib/data/message/Message.class.php');
require_once(WCF_DIR.'lib/data/user/UserProfile.class.php');

/**
 * Represents a guestbook comment.
 *
 * @author	Stefan Hahn
 * @copyright	2012 Stefan Hahn
 * @license	Simplified BSD License License <http://projects.swallow-all-lies.com/licenses/simplified-bsd-license.txt>
 * @package	com.leon.wcf.user.guestbook
 * @subpackage	data.user.guestbook
 * @category 	Community Framework
 */
class UserGuestbookComment extends Message {
	/**
	 * Editor object for this comment
	 * 
	 * @var UserGuestbookCommentEditor
	 */
	protected $editor = null;
	
	/**
	 * Guestbook entry object for this comment
	 * 
	 * @var	UserGuestbookEntry
	 */
	protected $entry = null;
	
	/**
	 * UserProfile object for the author of
	 * this guestbook comment.
	 * 
	 * @var	UserProfile
	 */
	protected $author = null;
	
	/**
	 * Creates a new guestbook comment object.
	 * 
	 * @param	integer		$commentID
	 * @param	array		$row
	 */
	public function __construct($entryID, $row = null) {
		if ($entryID !== null) {
			$sql = "SELECT		comment.*
				FROM		wcf".WCF_N."_user_guestbook_comment comment
				WHERE		comment.commentID = ".$commentID;
			$row = WCF::getDB()->getFirstRow($sql);
		}
		
		parent::__construct($row);
	}
	
	/**
	 * @see DatabaseObject::handleData()
	 */
	protected function handleData($row) {
		parent::handleData($row);
		$this->messageID = $this->commentID;
	}
	
	/**
	 * Gets editor object for this guestbook comment.
	 * 
	 * @return	UserGuestbookCommentEditor
	 */
	public function getEditor() {
		if ($this->editor === null) {
			require_once(WCF_DIR.'lib/data/user/guestbook/UserGuestbookCommentEditor.class.php');
			$this->editor = new UserGuestbookCommentEditor(null, $this->data);
		}
		
		return $this->editor;
	}
	
	/**
	 * Gets the guestbook entry object for this guestbook comment.
	 * 
	 * @return	UserGuestbookEntry
	 */
	public function getEntry() {
		if ($this->entry === null) {
			require_once(WCF_DIR.'lib/data/user/guestbook/UserGuestbookEntry.class.php');
			$this->entry = new UserGuestbookEntry($this->entryID);
		}
		
		return $this->entry;
	}
	
	/**
	 * Gets the author of this guestbook comment.
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
	 * Sets the author of this guestbook comment.
	 * 
	 * @param	UserProfile	$user
	 */
	public function setAuthor(UserProfile $user) {
		$this->author = $user;
	}
}
