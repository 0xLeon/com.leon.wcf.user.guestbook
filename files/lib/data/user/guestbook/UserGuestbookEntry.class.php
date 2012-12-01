<?php
// wcf imports
require_once(WCF_DIR.'lib/data/message/Message.class.php');
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
	 * List of all comments on this entry
	 * 
	 * @var UserGuestbookCommentList
	 */
	public $commentList = null;
	
	/**
	 * Editor object for this entry
	 * 
	 * @var UserGuestbookEntryEditor
	 */
	protected $editor = null;
	
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
	}
	
	/**
	 * Gets editor object for this guestbook entry
	 * 
	 * @return UserGuestbookEntryEditor
	 */
	public function getEditor() {
		if ($this->editor === null) {
			require_once(WCF_DIR.'lib/data/user/guestbook/UserGuestbookEntryEditor.class.php');
			$this->editor = new UserGuestbookEntryEditor(null, $this->data);
		}
		
		return $this->editor;
	}
}
