/**
 * Handles guestbook entries and comments.
 * 
 * @author	Stefan Hahn
 * @copyright	2012 Stefan Hahn
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
var UserGuestbook = (function() {
	var entries = $H({});
	var Entry = Class.create({
		initialize: function(entryID) {
			this.entryID = entryID;
			this.entry = $('guestbookEntryContainer' + this.entryID);
			this.commentsLoaded = false;
			
			this.addListeners();
		},
		
		addListeners: function() {
			$('guestbookEntryCommentLink' + this.entryID).observe('click', function(event) {
				if (event.isLeftClick()) {
					event.stop();
					this.loadComments();
				}
			}.bind(this));
		},
		
		loadComments: function() {
			if (!this.commentsLoaded) {
				new Ajax.Request('index.php?page=UserGuestbookCommentXMLList' + SID_ARG_2ND, {
					parameters: $H({
						entryID: this.entryID,
						ajax: 1
					}),
					onSuccess: function(response) {
						this.displayComments(response);
					}.bind(this),
					onFailure: function(response) {
						this.displayCommentLoadError(response);
					}.bind(this)
				});
			}
			else {
				this.toggleComments();
			}
		},
		
		toggleComments: function() {
			
		},
		
		displayComments: function(response) {
			var xml = response.responseXML;
			var htmlString = '';
			
			if (parseInt(xml.querySelector('comments').getAttribute('count')) > 0) {
				var comments = $A(xml.querySelectorAll('comments > comment'));
				
				comments.each(function(item) {
					var tplData = {
						commentID:	parseInt(item.getAttribute('id')),
						authorID:	parseInt(item.querySelector('author > userID').firstChild.NodeValue),
						authorName:	item.querySelector('author > username').firstChild.nodeValue,
						time:		item.querySelector('time > formatted').firstChild.nodeValue,
						message:	item.querySelector('message').firstChild.nodeValue,
						avatarPath:	item.querySelector('author > avatar > path').firstChild.nodeValue,
						avatarWidth:	parseInt(item.querySelector('author > avatar > width').firstChild.nodeValue),
						avatarHeight:	parseInt(item.querySelector('author > avatar > height').firstChild.nodeValue)
					};
					
					htmlString += guestbookCommentTemplate.evaluate(tplData) + "\n";
				});
			}
			
			$('guestbookComments' + this.entryID).innerHTML = htmlString;
			
			this.showCommentForm();
			
			this.commentsLoaded = true;
		},
		
		showCommentForm: function() {
			
		},
		
		displayCommentLoadError: function(response) {
			
		}
	});
	var Comment = Class.create({
		initialize: function(commentID, entryID) {
			this.commentID = commentID;
			this.entryID = entryID;
		}
	});
	
	function pushEntry(entryID) {
		entries.set(entryID, new Entry(entryID));
	}
	
	function getEntry(entryID) {
		return entries.get(entryID);
	}
	
	return {
		Entry:		Entry,
		Comment:	Comment,
		
		pushEntry:	pushEntry,
		getEntry:	getEntry
	};
})();
