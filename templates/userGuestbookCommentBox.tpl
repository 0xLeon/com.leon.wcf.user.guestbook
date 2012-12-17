{if $comment|isset}
	{assign var=commentID value=$comment->commentID}
	{assign var=authorID value=$comment->getAuthor()->userID}
	{assign var=authorName value=$comment->username}
	{assign var=time value=$comment->time|time}
	{assign var=message value=$comment->message}
	
	{if $comment->getAuthor()->getAvatar()}
		{assign var=tmp value=$comment->getAuthor()->getAvatar()->setMaxSize(50, 50)}
		{assign var=avatarPath value=$comment->getAuthor()->getAvatar()->getURL()}
		{assign var=avatarWidth value=$comment->getAuthor()->getAvatar()->getWidth()}
		{assign var=avatarHeight value=$comment->getAuthor()->getAvatar()->getHeight()}
	{else}
		{assign var=avatarPath value=RELATIVE_WCF_DIR|concat:'images/avatars/avatar-default.png'}
		{assign var=avatarWidth value=50}
		{assign var=avatarHeight value=50}
	{/if}
{/if}

<div id="guestbookCommentContainer{$commentID}" class="message guestbookComment" style="clear: both; position: relative; display: block;">
	<div class="messageInner">
		<a id="guestbookComment{$commentID}"></a>
		
		<div class="guestbookCommentAvatar" style="float: left; height: 100%; margin: 0px; padding: 0px;">
			<div class="userAvatar" style="margin: 7px 13px; padding: 0px; float: left;">
				{if $authorID}<a href="{* profile link *}" title="{lang username=$authorName}wcf.user.viewProfile{/lang}" style="display: block;">{/if}<img src="{@$avatarPath}" alt="" style="width: {@$avatarWidth}px; height: {@$avatarHeight}px;" />{if $authorID}</a>{/if}
			</div>
		</div>
		
		<div class="guestbookCommentBody" style="border-left: 1px dotted; padding: 0px 15px; margin-left: 76px;">
			<div class="guestbookCommentCredits">
				<p class="username light smallFont">
					{if $authorID}<a href="{* profile link *}" title="{lang username=$authorName}wcf.user.viewProfile{/lang}">{/if}{$authorName}{if $authorID}</a>{/if}
				</p>
				<p class="time light smallFont>{@$time}</p>
			</div>
			
			<div id="guestbookCommentMessage{$commentID}" class="messageBody" style="border-top: 1px dotted; margin-top: 4px; padding-top: 2px;">
				<p>{@$message}</p>
			</div>
		</div>
	</div>
</div>
