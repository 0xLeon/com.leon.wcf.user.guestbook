{if $entry|isset}
	{assign var=entryID value=$entry->entryID}
	{assign var=authorID value=$entry->getAuthor()->userID}
	{assign var=authorName value=$entry->username}
	{assign var=time value=$entry->time|time}
	{assign var=message value=$entry->message}
	{assign var=commentCount value=$entry->commentCount}
	
	{assign var=isDeleted value=$entry->isDeleted}
	{assign var=deletedByID value=$entry->deletedByID}
	{assign var=deletedBy value=$entry->deletedBy}
	{assign var=deletedReason value=$entry->deleteReason}
	
	{if $entry->getAuthor()->getAvatar()}
		{assign var=tmp value=$entry->getAuthor()->getAvatar()->setMaxSize(50, 50)}
		{assign var=avatarPath value=$entry->getAuthor()->getAvatar()->getURL()}
		{assign var=avatarWidth value=$entry->getAuthor()->getAvatar()->getWidth()}
		{assign var=avatarHeight value=$entry->getAuthor()->getAvatar()->getHeight()}
	{else}
		{assign var=avatarPath value=RELATIVE_WCF_DIR|concat:'images/avatars/avatar-default.png'}
		{assign var=avatarWidth value=50}
		{assign var=avatarHeight value=50}
	{/if}
{/if}

{if !$isDeleted || $modPermissions.canReadDeletedEntry}
<div id="guestbookEntryContainer{$entryID}" class="message guestbookEntry{if $isDeleted} deleted{/if}" style="clear: both; position: relative;{if $isDeleted} display: none;{/if}">
	<div class="messageInner">
		{if !$isDeleted}<a id="guestbookEntry{$entryID}"></a>{/if}
		
		<div class="container-2">
			<div class="guestbookEntryAvatar" style="float: left; height: 100%; margin: 0px; padding: 0px;">
				<div class="userAvatar" style="margin: 7px 13px; padding: 0px; float: left;">
					{if $authorID}<a href="index.php?page=User&amp;userID={$authorID}{@SID_ARG_2ND}" title="{lang username=$authorName}wcf.user.viewProfile{/lang}" style="display: block;">{/if}<img src="{@$avatarPath}" alt="" style="width: {@$avatarWidth}px; height: {@$avatarHeight}px;" />{if $authorID}</a>{/if}
				</div>
			</div>
			
			{* TODO: box doesn't use complete height, DOM Inspector shows error I think... *}
			<div class="guestbookEntryBody" style="height: 100%; border-left: 1px dotted; padding: 0px 15px; margin-left: 76px;">
				<div class="guestbookEntryCredits">
					<p class="username light smallFont">
						{if $authorID}<a href="index.php?page=User&amp;userID={$authorID}{@SID_ARG_2ND}" title="{lang username=$authorName}wcf.user.viewProfile{/lang}">{/if}{$authorName}{if $authorID}</a>{/if}
					</p>
					<p class="time light smallFont">{@$time}</p>
				</div>
				
				<div id="guestbookEntryMessage{$entryID}" class="messageBody" style="border-top: 1px dotted; margin-top: 4px; padding-top: 2px;">
					<p>{@$message}</p>
				</div>
			</div>
		</div>
		
		<div class="container-3" style="clear: both;">
			<div class="guestbookEntryComments" style="border-top: 1px dotted;">
				{*<a id="guestbookEntryCommentLink{$entryID}" title="{lang}wcf.user.guestbook.comment.load{/lang}" style="display: block; padding: 2px 15px 2px 13px;">{lang commentCount=$commentCount}wcf.user.guestbook.comment.comments{/lang}</a>*}
				<a id="guestbookEntryCommentLink{$entryID}" title="{lang}wcf.user.guestbook.comment.load{/lang}" style="display: block; padding: 2px 15px 2px 13px;">Kommentare ({$commentCount})</a>
				
				<div id="guestbookComments{$entryID}" class="guestbookComments"></div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	//<![CDATA[
	UserGuestbook.pushEntry({@$entryID|intval});
	//]]>
</script>
{/if}

{if $isDeleted}
	<div class="message messageMinimized" id="hiddenGuestbookEntryInfo{$entryID}">
		<div class="messageInner">
			<a id="guestbookEntry{$entryID}"></a>
			
			{* TODO: change icon, not a wcf icon *}
			<img src="{icon}postTrashM.png{/icon}" alt="" />
			
			<p class="smallFont light">
				{if $modPermissions.canReadDeletedEntry}
					<a onclick="showContent('guestbookEntryContainer{@$entryID}', 'hiddenGuestbookEntryInfo{@$entryID}')" title="{lang}wcf.user.guestbook.entry.showEntry{/lang}">
				{/if}
				
				<span>{lang}wcf.user.guestbook.entry.deletedEntry{/lang}</span>
				
				{if $modPermissions.canReadDeletedEntry}
					</a>
				{/if}
			</p>
		</div>
	</div>
{/if}
