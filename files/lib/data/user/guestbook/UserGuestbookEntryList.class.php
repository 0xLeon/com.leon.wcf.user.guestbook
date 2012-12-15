<?php
// wcf imports
require_once(WCF_DIR.'lib/data/DatabaseObjectList.class.php');
require_once(WCF_DIR.'lib/data/user/guestbook/UserGuestbookEntry.class.php');

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
					entry.*
			FROM		wcf".WCF_N."_user_guestbook_entry entry
			".$this->sqlJoins."
			".(!empty($this->sqlConditions) ? "WHERE ".$this->sqlConditions : '')."
			".(!empty($this->sqlOrderBy) ? "ORDER BY ".$this->sqlOrderBy : '');
		$result = WCF::getDB()->sendQuery($sql, $this->sqlLimit, $this->sqlOffset);
		
		while ($row = WCF::getDB()->fetchArray($result)) {
			$this->entries[$row['entryID']] = new $this->objectClassName(null, $row);
		}
	}
	
	public function readOwners() {
		if (!count($this->entries)) {
			return array();
		}
		
		$users = array();
		
		$sql = "SELECT		session.requestURI,
					session.requestMethod,
					session.ipAddress,
					session.userAgent,
					rank.*,
					avatar.*,
					GROUP_CONCAT(DISTINCT groups.groupID ORDER BY groups.groupID ASC SEPARATOR ',') AS groupIDs,
					GROUP_CONCAT(DISTINCT languages.languageID ORDER BY languages.languageID ASC SEPARATOR ',') AS languageIDs,
					user_option.*,
					user.*
			FROM		wcf".WCF_N."_user user
			LEFT JOIN	wcf".WCF_N."_avatar avatar
			ON		(avatar.avatarID = user.avatarID)
			LEFT JOIN	wcf".WCF_N."_session session
			ON		(session.userID = user.userID
					AND session.packageID = ".PACKAGE_ID."
					AND session.lastActivityTime > ".(TIME_NOW - USER_ONLINE_TIMEOUT).")
			LEFT JOIN	wcf".WCF_N."_user_rank rank
			ON		(rank.rankID = user.rankID)
			LEFT JOIN	wcf".WCF_N."_user_to_groups groups
			ON		(groups.userID = user.userID)
			LEFT JOIN	wcf".WCF_N."_user_to_languages languages
			ON		(languages.userID = user.userID)
			LEFT JOIN	wcf".WCF_N."_user_option_value user_option
			ON		(user_option.userID = user.userID)
			WHERE		user.userID IN (0,".implode(',', $this->getOwnerIDs()).")
			GROUP BY	user.userID";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$users[$row['userID']] = new UserProfile(null, $row);
		}
		
		foreach ($this->entries as $entry) {
			$entry->setOwner($users[$entry->ownerID]);
		}
		
		unset($users);
	}
	
	public function readAuthors() {
		if (!count($this->entries)) {
			return array();
		}
		
		$users = array();
		
		$sql = "SELECT		session.requestURI,
					session.requestMethod,
					session.ipAddress,
					session.userAgent,
					rank.*,
					avatar.*,
					GROUP_CONCAT(DISTINCT groups.groupID ORDER BY groups.groupID ASC SEPARATOR ',') AS groupIDs,
					GROUP_CONCAT(DISTINCT languages.languageID ORDER BY languages.languageID ASC SEPARATOR ',') AS languageIDs,
					user_option.*,
					user.*
			FROM		wcf".WCF_N."_user user
			LEFT JOIN	wcf".WCF_N."_avatar avatar
			ON		(avatar.avatarID = user.avatarID)
			LEFT JOIN	wcf".WCF_N."_session session
			ON		(session.userID = user.userID
					AND session.packageID = ".PACKAGE_ID."
					AND session.lastActivityTime > ".(TIME_NOW - USER_ONLINE_TIMEOUT).")
			LEFT JOIN	wcf".WCF_N."_user_rank rank
			ON		(rank.rankID = user.rankID)
			LEFT JOIN	wcf".WCF_N."_user_to_groups groups
			ON		(groups.userID = user.userID)
			LEFT JOIN	wcf".WCF_N."_user_to_languages languages
			ON		(languages.userID = user.userID)
			LEFT JOIN	wcf".WCF_N."_user_option_value user_option
			ON		(user_option.userID = user.userID)
			WHERE		user.userID IN (0,".implode(',', $this->getAuthorIDs()).")
			GROUP BY	user.userID";
		$result = WCF::getDB()->sendQuery($sql);
		while ($row = WCF::getDB()->fetchArray($result)) {
			$users[$row['userID']] = new UserProfile(null, $row);
		}
		
		foreach ($this->entries as $entry) {
			$entry->setAuthor($users[$entry->userID]);
		}
		
		unset($users);
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
}
