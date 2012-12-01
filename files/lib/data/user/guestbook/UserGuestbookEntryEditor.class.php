<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/guestbook/UserGuestbookEntry.class.php');

/**
 * Provides functions to create and edit the data of an guestbook entry
 *
 * @author	Stefan Hahn
 * @copyright	2012 Stefan Hahn
 * @license	Simplified BSD License License <http://projects.swallow-all-lies.com/licenses/simplified-bsd-license.txt>
 * @package	com.leon.wcf.user.guestbook
 * @subpackage	data.user.guestbook
 * @category 	Community Framework
 */
class UserGuestbookEntryEditor extends UserGuestbookEntry {
	/**
	 * Creates a new guestbook entry.
	 * Returns a UserGuestbookEntryEditor object of the new entry.
	 * 
	 * @param	integer				$ownerID
	 * @param	integer				$userID
	 * @param	string				$username
	 * @param	string				$message
	 * @param	string				$ipAddress
	 * @param	bool				$enableSmilies
	 * @param	bool				$enableHtml
	 * @param	bool				$enableBBCodes
	 * @return	UserGuestbookEntryEditor
	 */
	public static function create($ownerID, $userID, $username, $message, $ipAddress, $enableSmilies = true, $enableHtml = false, $enableBBCodes = true) {
		$sql = "INSERT
			INTO	wcf".WCF_N."_user_guestbook_entry
				(ownerID, userID, username, message, time, ipAddress, enableSmilies, enableHtml, enableBBCodes)
			VALUES	(".$ownerID.",".$userID.",".escapeString($username).",".escapeString($message).",".TIME_NOW.",".escapeString($ipAddress).",".((int) $enableSmilies).",".((int) $enableHtml).",".((int) $enableBBCodes).")";
		WCF::getDB()->sendQuery($sql);
		
		return new UserGuestbookEntryEditor(WCF::getDB()->getInsertID());
	}
	
	public function update() {
		
	}
	
	public function mark() {
		
	}
	
	public function unmark() {
		
	}
	
	/**
	 * Moves this guestbook entry in recycle bin.
	 */
	public function trash($deletedByID, $deletedBy, $reason = '') {
		self::trashAll($this->postID, $deletedByID, $deletedBy, $reason);
	}
	
	/**
	 * Restores this guestbook entry.
	 */
	public function restore() {
		self::restoreAll(array($this->postID));
	}
	
	public function delete() {
		
	}
	
	public function deleteCompletely() {
		
	}
	
	/**
	 * Unmarks all marked guestbook entries.
	 */
	public static function unmarkAll() {
		WCF::getSession()->unregister('markedGuestbookEntries');
	}
	
	/**
	 * Moves all guestbook entries with the given IDs in recycle bin.
	 */
	public static function trashAll($postIDs, $deletedByID, $deletedBy, $reason = '') {
		if (!count($postIDs)) return;
		
		$sql = "UPDATE	wcf".WCF_N."_user_guestbook_entry
			SET	isDeleted = 1,
				deletedByID = ".$deletedByID.",
				deletedBy = '".escapeString($deletedBy)."',
				deleteTime = ".TIME_NOW.",
				deleteReason = '".escapeString($reason)."'
			WHERE	entryID IN (".implode(',', $postIDs).")";
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Restores all guestbook entries with the given IDs.
	 */
	public static function restoreAll($postIDs) {
		if (!count($postIDs)) return;
		
		$sql = "UPDATE	wcf".WCF_N."_user_guestbook_entry
			SET	isDeleted = 0,
				deletedByID = 0,
				deletedBy = '',
				deleteTime = 0,
				deleteReason = '',
			WHERE	entryID IN (".implode(',', $postIDs).")";
		WCF::getDB()->sendQuery($sql);
	}
	
	public static function deleteAll($postIDs) {
		
	}
	
	public static function deleteAllCompletely($postIDs) {
		
	}
	
	/**
	 * Returns all marked guestbook entries.
	 */
	public static function getMarkedEntries() {
		$sessionVars = WCF::getSession()->getVars();
		
		if (isset($sessionVars['markedGuestbookEntries'])) {
			return $sessionVars['markedGuestbookEntries'];
		}
		
		return null;
	}
}
