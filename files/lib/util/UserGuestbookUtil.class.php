<?php
// wcf imports
require_once(WCF_DIR.'lib/data/user/UserProfile.class.php');

/**
 * Contains user guestbook related functions.
 *
 * @author	Stefan Hahn
 * @copyright	2012 Stefan Hahn
 * @license	Simplified BSD License License <http://projects.swallow-all-lies.com/licenses/simplified-bsd-license.txt>
 * @package	com.leon.wcf.user.guestbook
 * @subpackage	util
 * @category 	Community Framework
 */
class UserGuestbookUtil {
	const ACCESS_NO_ONE = 1;
	const ACCESS_FRIENDS = 2;
	const ACCESS_EVERYONE = 4;
	
	/**
	 * Gets all the user permissions for the currently logged in user in 
	 * the guestbook owned by the given owner.
	 * 
	 * @var		UserProfile	$guestbookOwner
	 * @return	array<bool>
	 */
	public static function getUserPermissions(UserProfile $guestbookOwner) {
		$permissions = array();
		
		$permissions['isOwner'] = (WCF::getUser()->userID === $guestbookOwner->userID);
		$permissions['canUseGuestbook'] = (bool)WCF::getUser()->getPermission('user.guestbook.canUseGuestbook');
		$permissions['canViewGuestbook'] = ($permissions['canUseGuestbook'] && ($permissions['isOwner'] || (($guestbookOwner->guestbookAccess & UserGuestbookUtil::ACCESS_FRIENDS) ? UserProfile::isBuddy($guestbookOwner->userID) : ((bool)($guestbookOwner->guestbookAccess & UserGuestbookUtil::ACCESS_EVERYONE)))));
		$permissions['canWriteEntry'] =  (WCF::getUser()->getPermission('user.guestbook.canWriteEntry') && (($guestbookOwner->guestbookWriteEntryAccess & UserGuestbookUtil::ACCESS_FRIENDS) ? UserProfile::isBuddy($guestbookOwner->userID) : ((bool)($guestbookOwner->guestbookWriteEntryAccess & UserGuestbookUtil::ACCESS_EVERYONE))));
		$permissions['canWriteComment'] = (WCF::getUser()->getPermission('user.guestbook.canWriteComment') && (($guestbookOwner->guestbookWriteCommentAccess & UserGuestbookUtil::ACCESS_FRIENDS) ? UserProfile::isBuddy($guestbookOwner->userID) : ((bool)($guestbookOwner->guestbookWriteCommentAccess & UserGuestbookUtil::ACCESS_EVERYONE))));
		
		return $permissions;
	}
	
	/**
	 * Gets all the moderator permissions for the currently logged in user 
	 * in the guestbook owned by the given owner.
	 * 
	 * @var		UserProfile	$guestbookOwner
	 * @return	array<bool>
	 */
	public static function getModeratorPermissions(UserProfile $guestbookOwner) {
		$permissions = array();
		
		$permissions['isOwner'] = (WCF::getUser()->userID === $guestbookOwner->userID);
		$permissions['canDeleteEntry'] = ($permissions['isOwner'] || WCF::getUser()->getPermission('mod.guestbook.canDeleteEntry'));
		$permissions['canReadDeletedEntry'] = ($permissions['isOwner'] || WCF::getUser()->getPermission('mod.guestbook.canReadDeletedEntry'));
		$permissions['canDeleteEntryCompletely'] = ($permissions['canDeleteEntry'] && ($permissions['isOwner'] || WCF::getUser()->getPermission('mod.guestbook.canDeleteEntryCompletely')));
		$permissions['canEditEntry'] = (bool)WCF::getUser()->getPermission('mod.guestbook.canEditEntry');
		$permissions['canMarkEntry'] = ($permissions['canDeleteEntry'] || $permissions['canDeleteEntryCompletely']);
		$permissions['canHandleEntry'] = ($permissions['canEditEntry'] || $permissions['canMarkEntry']);
		
		return $permissions;
	}
}
