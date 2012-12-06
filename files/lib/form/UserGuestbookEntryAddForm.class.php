<?php
// wcf imports
require_once(WCF_DIR.'lib/form/MessageForm.class.php');
require_once(WCF_DIR.'lib/data/user/UserProfileFrame.class.php');
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
	public $templateName = 'userGuestbookEntryAdd';
	
	public $preview = false;
	// TODO: set default to false when done
	public $send = true;
	
	// TODO: flood controll? Have to set $messageTable
	
	/**
	 * User profile frame.
	 * 
	 * @var	UserProfileFrame
	 */
	public $frame = null;
	
	/**
	 * User ID of the user adding this entry.
	 * 
	 * @var integer
	 */
	public $authorID = 0;
	
	/**
	 * Username of the user adding this entry.
	 * 
	 * @var	string
	 */
	public $authorname = '';
	
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
		
		$this->frame = new UserProfileFrame($this);
	}
	
	/**
	 * @see	Page::readData()
	 */
	public function readData() {
		parent::readData();
		
		if (!count($_POST)) {
			$this->authorname = WCF::getSession()->username;
		}
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
		
		$this->newGuestbookEntry = UserGuestbookEntryEditor::create($this->frame->getUser()->userID, $this->authorID, $this->authorname, $this->text, $this->ipAddress, (bool) $options['enableSmilies'], (bool) $options['enableHtml'], (bool) $options['enableBBCodes']);
		
		$this->saved();
		
		HeaderUtil::redirect('index.php?page=UserGuestbook&userID='.$this->frame->getUser()->userID.'&entryID='.$this->newGuestbookEntry->entryID.SID_ARG_2ND_NOT_ENCODED.'#entry'.$this->newGuestbookEntry->entryID);
	}
	
	public function assignVariables() {
		parent::assignVariables();
		
		$this->frame->assignVariables();
	}
	
	/**
	 * @see	Form::validate()
	 */
	public function validate() {
		parent::validate();
		
		$this->validateUsername();
	}
	
	/**
	 * Validates the username
	 */
	protected function validateUsername() {
		if (!WCF::getUser()->userID) {
			if (empty($this->authorname)) {
				throw new UserInputException('authorname');
			}
			
			if (!UserUtil::isValidUsername($this->authorname)) {
				throw new UserInputException('authorname', 'notValid');
			}
			
			if (!UserUtil::isAvailableUsername($this->authorname)) {
				throw new UserInputException('authorname', 'notAvailable');
			}
			
			WCF::getSession()->setUsername($this->authorname);
		} 
		else {
			$this->authorname = WCF::getUser()->username;
		}
	}
	
	/**
	 * Does nothing.
	 */
	protected function validateSubject() { }
}
