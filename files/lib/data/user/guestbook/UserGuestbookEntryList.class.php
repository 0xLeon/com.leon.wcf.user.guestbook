<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObjectList.class.php');
require_once(WCF_DIR.'lib/data/user/guestbook/UserGuestbookEntry.class.php');
require_once(WCF_DIR.'lib/data/user/UserProfileList.class.php');

/**
 * Represents a list of user guestbook entries.
 * 
 * @author	Stefan Hahn
 * @copyright	2012 Stefan Hahn
 * @license	Simplified BSD License License <http://projects.swallow-all-lies.com/licenses/simplified-bsd-license.txt>
 * @package	com.leon.wcf.user.guestbook
 * @subpackage	data.user.guestbook
 * @category 	Community Framework
 */
class UserGuestbookEntryList extends DatabaseObjectList {
	/**
	 * list of entries
	 * 
	 * @var array<UserGuestbookEntry>
	 */
	public $entries = array();
	
	/**
	 * Class name of the result object class
	 * 
	 * @var	string
	 */
	public $objectClassName = 'UserGuestbookEntry';
	
	protected $ownerIDs = null;
	protected $authorIDs = null;
	protected $commentIDs = null;
	
	/**
	 * @see DatabaseObjectList::countObjects()
	 */
	public function countObjects() {
		$sql = "SELECT		COUNT(*) AS count
			FROM		wcf".WCF_N."_user_guestbook_entry entry
			".$this->sqlJoins."
			".(!empty($this->sqlConditions) ? "WHERE ".$this->sqlConditions : '');
		$row = WCF::getDB()->getFirstRow($sql);
		
		return $row['count'];
	}
	
	/**
	 * @see DatabaseObjectList::readObjects()
	 */
	public function readObjects() {
		$sql = "SELECT		".(!empty($this->sqlSelects) ? $this->sqlSelects.',' : '')."
					entry.*,
					COUNT(comment.commentID) AS commentCount,
					GROUP_CONCAT(comment.commentID ORDER BY comment.commentID ASC SEPARATOR ',') AS commentIDs
			FROM		wcf".WCF_N."_user_guestbook_entry entry
			LEFT JOIN	wcf".WCF_N."_user_guestbook_comment comment
			ON		(entry.entryID = comment.entryID)
			".$this->sqlJoins."
			".(!empty($this->sqlConditions) ? "WHERE ".$this->sqlConditions : '')."
			".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '')."
			GROUP BY	entry.entryID";
		$result = WCF::getDB()->sendQuery($sql, $this->sqlLimit, $this->sqlOffset);
		
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->entries[$row['entryID']] = new $this->objectClassName(null, $row);
		}
	}
	
	public function readOwners() {
		if (!count($this->entries)) {
			return array();
		}
		
		$userList = new UserProfileList();
		$userList->sqlConditions .= " user.userID IN (0,".implode(',', $this->getOwnerIDs()).") ";
		$userList->readObjects();
		$users = $userList->getObjects();
		
		foreach ($this->entries as $entry) {
			$entry->setOwner($users[$entry->ownerID]);
		}
		
		unset($users, $userList);
	}
	
	public function readAuthors() {
		if (!count($this->entries)) {
			return array();
		}
		
		$userList = new UserProfileList();
		$userList->sqlConditions .= " user.userID IN (0,".implode(',', $this->getAuthorIDs()).") ";
		$userList->readObjects();
		$users = $userList->getObjects();
		
		foreach ($this->entries as $entry) {
			$entry->setAuthor($users[$entry->ownerID]);
		}
		
		unset($users, $userList);
	}
	
	public function readComments() {
		if (!count($this->entries)) {
			return array();
		}
		
		$commentList = new UserGuestbookCommentList();
		$commentList->sqlConditions .= " comment.commentID IN (0,".implode(',', $this->getCommentIDs()).") ";
		$commentList->readObjects();
		$commentsUnorderd = $commentList->getObjects();
		$comments = array();
		
		foreach ($commentsUnorderd as $comment) {
			if (!isset($comments[$comment->entryID])) {
				$comments[$comment->entryID] = array();
			}
			
			$comments[$comment->entryID][] = $comment;
		}
		
		foreach ($comments as $entryID => $entryComments) {
			$this->entries[$entryID]->setComments($entryComments);
		}
		
		unset($comments, $commentsUnordered, $commentList);
	}
	
	/**
	 * @see DatabaseObjectList::getObjects()
	 */
	public function getObjects() {
		return $this->entries;
	}
	
	protected function getOwnerIDs() {
		if (!count($this->entries)) {
			$this->ownerIDs = array();
			
			return $this->ownerIDs;
		}
		
		if ($this->ownerIDs === null) {
			$this->ownerIDs = array();
			
			foreach ($this->entries as $entry) {
				$this->ownerIDs[] = $entry->ownerID;
			}
		}
		
		return $this->ownerIDs;
	}
	
	protected function getAuthorIDs() {
		if (!count($this->entries)) {
			$this->authorIDs = array();
			
			return $this->authorIDs;
		}
		
		if ($this->authorIDs === null) {
			$this->authorIDs = array();
			
			foreach ($this->entries as $entry) {
				$this->authorIDs[] = $entry->userID;
			}
		}
		
		return $this->authorIDs;
	}
	
	protected function getCommentIDs() {
		if (!count($this->entries)) {
			$this->commentIDs = array();
			
			return $this->commentIDs;
		}
		if ($this->commentIDs === null) {
			$this->commentIDs = array();
			
			foreach ($this->entries as $entry) {
				$this->commentIDs = array_merge($this->commentIDs, $entry->commentIDs);
			}
			
			$this->commentIDs = array_unique($this->commentIDs);
		}
		
		return $this->commentIDs;
	}
}
