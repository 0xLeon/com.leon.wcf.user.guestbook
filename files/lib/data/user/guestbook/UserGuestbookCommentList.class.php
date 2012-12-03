<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObjectList.class.php');
require_once(WCF_DIR.'lib/data/user/guestbook/UserGuestbookComment.class.php');

/**
 * Represents a list of user guestbook comments.
 * 
 * @author	Stefan Hahn
 * @copyright	2012 Stefan Hahn
 * @license	Simplified BSD License License <http://projects.swallow-all-lies.com/licenses/simplified-bsd-license.txt>
 * @package	com.leon.wcf.user.guestbook
 * @subpackage	data.user.guestbook
 * @category 	Community Framework
 */
class UserGuestbookCommentList extends DatabaseObjectList {
	/**
	 * list of comments
	 * 
	 * @var array<UserGuestbookComment>
	 */
	public $comments = array();
	
	/**
	 * Class name of the result object class
	 * 
	 * @var	string
	 */
	public $objectClassName = 'UserGuestbookComment';
	
	/**
	 * @see DatabaseObjectList::countObjects()
	 */
	public function countObjects() {
		$sql = "SELECT		COUNT(*) AS count
			FROM		wcf".WCF_N."_user_guestbook_comment comment
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
					comment.*
			FROM		wcf".WCF_N."_user_guestbook_comment comment
			".$this->sqlJoins."
			".(!empty($this->sqlConditions) ? "WHERE ".$this->sqlConditions : '')."
			".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
		$result = WCF::getDB()->sendQuery($sql, $this->sqlLimit, $this->sqlOffset);
		
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->comments[] = new $this->objectClassName(null, $row);
		}
	}
	
	/**
	 * @see DatabaseObjectList::getObjects()
	 */
	public function getObjects() {
		return $this->comments;
	}
}
