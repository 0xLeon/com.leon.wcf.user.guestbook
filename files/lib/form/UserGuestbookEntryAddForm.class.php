<?php
// wcf imports
require_once(WCF_DIR.'lib/form/MessageForm.class.php');
require_once(WCF_DIR.'lib/data/user/UserProfile.class.php');
require_once(WCF_DIR.'lib/data/user/guestbook/UserGuestbookEntryEditor.class.php');

/**
 * Shows the user guestbook entry add form.
 *
 * @author	Stefan Hahn
 * @copyright	2012 Stefan Hahn
 * @license	Simplified BSD License License <http://projects.swallow-all-lies.com/licenses/simplified-bsd-license.txt>
 * @package	com.leon.wcf.user.guestbook
 * @subpackage	form
 * @category 	Community Framework
 */
class UserGuestbookEntryAddForm extends MessageForm {
	public $showPoll = false;
	public $showSignatureSetting = false;
	public $permissionType = 'guestbook';
	
	public $preview = false;
	// TODO: set default to false when done
	public $send = true;
	
	// TODO: flood controll? Have to set $messageTable
	
	/**
	 * Guestbook owner user ID
	 * 
	 * @var	integer
	 */
	public $owenerID = 0;
	
	/**
	 * Guestbook owner user object
	 * 
	 * @var	UserProfile
	 */
	public $owner = null;
	
	/**
	 * User ID of the user adding this entry.
	 * 
	 * @var integer
	 */
	public $userID = 0;
	
	/**
	 * Username of the user adding this entry.
	 * 
	 * @var	string
	 */
	public $username = '';
	
	/**
	 * IP address of the user adding this entry.
	 * 
	 * @var	string
	 */
	public $ipAddress = '';
	
	/**
	 * Additional parameters for new guestbook entry.
	 * 
	 * @var	array<mixed>
	 */
	public $additionalParameters = array();
	
	/**
	 * New guestbook entry object
	 * 
	 * @var	UserGuestbookEntryEditor
	 */
	public $newGuestbookEntry = null;
	
	/**
	 * @see	Page::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['ownerID'])) $this->ownerID = intval($_REQUEST['ownerID']);
	}
	
	/**
	 * @see	Form::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		$this->userID = WCF::getUser()->userID;
		$this->ipAddress = WCF::getUser()->ipAddress;
		
		// TODO: preview
	}
	
	/**
	 * @see	Form::submit()
	 */
	public function submit() {
		parent::submit();
		
		// TODO: preview
	}
	
	/**
	 * @see	Form::save()
	 */
	public function save() {
		parent::save();
		
		$options = $this->getOptions();
		
		$this->newGuestbookEntry = UserGuestbookEntryEditor::create($this->ownerID, $this->userID, $this->username, $this->text, $this->ipAddress, (bool) $options['enableSmilies'], (bool) $options['enableHtml'], (bool) $options['enableBBCodes']);
		
		$this->saved();
		
		HeaderUtil::redirect('index.php?page=UserGuestbook&entryID='.$this->newGuestbookEntry->entryID.SID_ARG_2ND_NOT_ENCODED.'#entry'.$this->newGuestbookEntry->entryID);
	}
	
	/**
	 * Does nothing.
	 */
	protected function validateSubject() { }
}
