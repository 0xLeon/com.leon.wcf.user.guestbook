{if $entry|isset}
	{assign var=entryID value=$entry->entryID}
	{assign var=authorID value=$entry->getAuthor()->userID}
	{assign var=authorName value=$entry->username}
	{assign var=time value=$entry->time|time}
	{assign var=message value=$entry->message}
	{assign var=commentCount value=$entry->commentCount}
	
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

<div id="guestbookEntryContainer{$entryID}" class="message border guestbookEntry" style="clear: both; position: relative; display: block;">
	<div class="messageInner">
		<a id="guestbookEntry{$entryID}"></a>
		
		<div class="container-1">
			<div class="guestbookEntryAvatar" style="float: left; height: 100%; margin: 0px; padding: 0px;">
				<div class="userAvatar" style="margin: 7px 13px; padding: 0px; float: left;">
					{if $authorID}<a href="{* profile link *}" title="{lang username=$authorName}wcf.user.viewProfile{/lang}" style="display: block;">{/if}<img src="{@$avatarPath}" alt="" style="width: {@$avatarWidth}px; height: {@$avatarHeight}px;" />{if $authorID}</a>{/if}
				</div>
			</div>
			
			{* TODO: box doesn't use complete height, DOM Inspector shows error I think... *}
			<div class="guestbookEntryBody" style="height: 100%; border-left: 1px dotted; padding: 0px 15px; margin-left: 76px;">
				<div class="guestbookEntryCredits">
					<p class="username light smallFont">
						{if $authorID}<a href="{* profile link *}" title="{lang username=$authorName}wcf.user.viewProfile{/lang}">{/if}{$authorName}{if $authorID}</a>{/if}
					</p>
					<p class="time light smallFont">{@$time}</p>
				</div>
				
				<div id="guestbookEntryMessage{$entryID}" class="messageBody" style="border-top: 1px dotted; margin-top: 4px; padding-top: 2px;">
					<p>{@$message}</p>
				</div>
			</div>
		</div>
		
		<div class="container-2" style="clear: both;">
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
