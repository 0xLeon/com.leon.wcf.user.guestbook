<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/guestbook/UserGuestbookComment.class.php');

/**
 * Provides functions to create and edit user guestbook comments.
 *
 * @author	Stefan Hahn
 * @copyright	2012 Stefan Hahn
 * @license	Simplified BSD License License <http://projects.swallow-all-lies.com/licenses/simplified-bsd-license.txt>
 * @package	com.leon.wcf.user.guestbook
 * @subpackage	data.user.guestbook
 * @category 	Community Framework
 */
class UserGuestbookCommentEditor extends UserGuestbookComment {
	/**
	 * Creates a new guestbook comment.
	 * Returns a UserGuestbookCommentEditor object of the new comment.
	 * 
	 * @param	integer				$entryID
	 * @param	integer				$userID
	 * @param	string				$username
	 * @param	string				$message
	 * @param	string				$ipAddress
	 * @param	bool				$enableSmilies
	 * @param	bool				$enableHtml
	 * @param	bool				$enableBBCodes
	 * @return	UserGuestbookCommentEditor
	 */
	public static function create($entryID, $userID, $username, $message, $ipAddress, $enableSmilies = true, $enableHtml = false, $enableBBCodes = true) {
		$sql = "INSERT
			INTO	wcf".WCF_N."_user_guestbook_comment
				(entryID, userID, username, message, time, ipAddress, enableSmilies, enableHtml, enableBBCodes)
			VALUES	(".$entryID.",".$userID.",".escapeString($username).",".escapeString($message).",".TIME_NOW.",".escapeString($ipAddress).",".((int) $enableSmilies).",".((int) $enableHtml).",".((int) $enableBBCodes).")";
		WCF::getDB()->sendQuery($sql);
		
		return new UserGuestbookCommentEditor(WCF::getDB()->getInsertID());
	}
	
	// TODO: implement UserGuestbookCommentEditor::update()
	public function update() {
		
	}
	
	/**
	 * Marks this guestbook comment.
	 */
	public function mark() {
		$markedComments = UserGuestbookCommentEditor::getMarkedComments();
		
		array_push($markedComments, $this->commentID);
		WCF::getSession()->register('markedGuestbookComments', $markedComments);
	}
	
	/**
	 * Unmarks this guestbook comment.
	 */
	public function unmark() {
		$markedComments = UserGuestbookCommentEditor::getMarkedPosts();
		
		if (in_array($this->commentID, $markedComments)) {
			$key = array_search($this->commentID, $markedComments);
			
			unset($markedComments[$key]);
			
			if (count($markedComments) === 0) {
				UserGuestbookCommentEditor::unmarkAll();
			}
			else {
				WCF::getSession()->register('markedGuestbookComments', $markedComments);
			}
		}
	}
	
	/**
	 * Moves this guestbook comment in recycle bin.
	 * 
	 * @param	integer			$deletedByID
	 * @param	string			$deletedBy
	 * @param	string			$reason
	 */
	public function trash($deletedByID, $deletedBy, $reason = '') {
		UserGuestbookCommentEditor::trashAll(array($this->commentID), $deletedByID, $deletedBy, $reason);
	}
	
	/**
	 * Restores this guestbook comment.
	 */
	public function restore() {
		UserGuestbookCommentEditor::restoreAll(array($this->commentID));
	}
	
	/**
	 * Deletes this guestbook comment completely.
	 */
	public function delete() {
		UserGuestbookCommentEditor::deleteAllCompletely(array($this->commentID));
	}
	
	/**
	 * Unmarks all marked guestbook comments.
	 */
	public static function unmarkAll() {
		WCF::getSession()->unregister('markedGuestbookComments');
	}
	
	/**
	 * Moves all guestbook comments with the given IDs in recycle bin.
	 * 
	 * @param	array<integer>		$commentIDs
	 * @param	integer			$deletedByID
	 * @param	string			$deletedBy
	 * @param	string			$reason
	 */
	public static function trashAll($commentIDs, $deletedByID, $deletedBy, $reason = '') {
		if (!count($commentIDs)) return;
		
		$sql = "UPDATE	wcf".WCF_N."_user_guestbook_comment
			SET	isDeleted = 1,
				deletedByID = ".$deletedByID.",
				deletedBy = '".escapeString($deletedBy)."',
				deleteTime = ".TIME_NOW.",
				deleteReason = '".escapeString($reason)."'
			WHERE	commentID IN (".implode(',', $commentIDs).")";
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Restores all guestbook comments with the given IDs.
	 * 
	 * @param	array<integer>		$commentIDs
	 */
	public static function restoreAll($commentIDs) {
		if (!count($commentIDs)) return;
		
		$sql = "UPDATE	wcf".WCF_N."_user_guestbook_comment
			SET	isDeleted = 0,
				deletedByID = 0,
				deletedBy = '',
				deleteTime = 0,
				deleteReason = '',
			WHERE	commentID IN (".implode(',', $commentIDs).")";
		WCF::getDB()->sendQuery($sql);
	}
	
	// TODO: Implement UserGuestbookCommentEditor::deleteAll()
	public static function deleteAll($commentIDs) {
		
	}
	
	/**
	 * Deletes all guestbook comments with the given IDs.
	 * 
	 * @param	array<integer>		$commentIDs
	 */
	public static function deleteAllCompletely($commentIDs) {
		$sql = "DELETE
			FROM	wcf".WCF_N."_user_guestbook_comment
			WHERE	commentID IN (".implode(',', $commentIDs).")";
		WCF::getDB()->sendQuery($sql);
	}
	
	/**
	 * Returns all marked guestbook comments.
	 * 
	 * @return	array<integer>
	 */
	public static function getMarkedComments() {
		$sessionVars = WCF::getSession()->getVars();
		
		if (isset($sessionVars['markedGuestbookComments'])) {
			return $sessionVars['markedGuestbookComments'];
		}
		
		return array();
	}
}
