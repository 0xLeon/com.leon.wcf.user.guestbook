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
	
	// TODO: implement UserGuestbookEditor::update()
	public function update() {
		
	}
	
	/**
	 * Marks this guestbook entry.
	 */
	public function mark() {
		$markedEntries = self::getMarkedEntries();
		
		array_push($markedEntries, $this->entryID);
		WCF::getSession()->register('markedGuestbookEntries', $markedEntries);
	}
	
	/**
	 * Unmarks this guestbook entry.
	 */
	public function unmark() {
		$markedEntries = self::getMarkedPosts();
		if (in_array($this->entryID, $markedEntries)) {
			$key = array_search($this->entryID, $markedEntries);
			
			unset($markedEntries[$key]);
			
			if (count($markedEntries) === 0) {
				self::unmarkAll();
			}
			else {
				WCF::getSession()->register('markedGuestbookEntries', $markedEntries);
			}
		}
	}
	
	/**
	 * Moves this guestbook entry in recycle bin.
	 */
	public function trash($deletedByID, $deletedBy, $reason = '') {
		self::trashAll($this->entryID, $deletedByID, $deletedBy, $reason);
	}
	
	/**
	 * Restores this guestbook entry.
	 */
	public function restore() {
		self::restoreAll(array($this->entryID));
	}
	
	/**
	 * Deletes this guestbook entry completely.
	 */
	public function delete() {
		self::deleteAllCompletely(array($this->entryID));
	}
	
	/**
	 * Unmarks all marked guestbook entries.
	 */
	public static function unmarkAll() {
		WCF::getSession()->unregister('markedGuestbookEntries');
	}
	
	/**
	 * Moves all guestbook entries with the given IDs in recycle bin.
	 * 
	 * @param	array		$entryIDs
	 * @param	integer		$deletedByID
	 * @param	string		$deletedBy
	 * @param	string		$reason
	 */
	public static function trashAll($entryIDs, $deletedByID, $deletedBy, $reason = '') {
		if (!count($entryIDs)) return;
		
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
	 * 
	 * @param	array		$entryIDs
	 */
	public static function restoreAll($entryIDs) {
		if (!count($entryIDs)) return;
		
		$sql = "UPDATE	wcf".WCF_N."_user_guestbook_entry
			SET	isDeleted = 0,
				deletedByID = 0,
				deletedBy = '',
				deleteTime = 0,
				deleteReason = '',
			WHERE	entryID IN (".implode(',', $entryIDs).")";
		WCF::getDB()->sendQuery($sql);
	}
	
	// TODO: Implement UserGuestbookEntryEditor::deleteAll()
	public static function deleteAll($entryIDs) {
		
	}
	
	/**
	 * Deletes all guestbook entries with the given IDs.
	 * 
	 * @param	array		$entryIDs
	 */
	public static function deleteAllCompletely($entryIDs) {
		$sql = "DELETE
			FROM	wcf".WCF_N."_user_guestbook_entry
			WHERE	entryID IN (".implode(',', $entryIDs).")";
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Returns all marked guestbook entries.
	 */
	public static function getMarkedEntries() {
		$sessionVars = WCF::getSession()->getVars();
		
		if (isset($sessionVars['markedGuestbookEntries'])) {
			return $sessionVars['markedGuestbookEntries'];
		}
		
		return array();
	}
}
