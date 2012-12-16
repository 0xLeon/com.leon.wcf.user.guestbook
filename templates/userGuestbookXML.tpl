<?xml version="1.0" encoding="{CHARSET}"?>
<guestbook>
	{if $comments|count}
		<comments count="{$comments|count}">
			{foreach from=$comments item=comment}
				<comment id="{@$comment->commentID}">
					<author>
						<userID>{@$comment->getAuthor()->userID}</userID>
						<username><![CDATA[{@$comment->username}]]></username>
						{if $comment->getAuthor()->getAvatar()}
							<avatar><![CDATA[{@$comment->getAuthor()->getAvatar()->getURL()}]]></avatar>
						{else}
							<avatar><![CDATA[{@RELATIVE_WCF_DIR}images/avatars/avatar-default.png]]></avatar>
						{/if}
						<ip><![CDATA[{@$comment->ipAddress}]]></ip>
					</author>
					<time>
						<raw>{@$comment->time}</raw>
						<formatted><![CDATA[{@$comment->time|time}]]></formatted>
					</time>
					<message><![CDATA[{@$comment->message}]]></message>
					{if $comment->isDeleted}
						<deletion deleted="deleted">
							<user>
								<userID>{@$comment->deletedByID}</userID>
								<username><![CDATA[{@$comment->deletedBy}]]></username>
							</user>
							<time>
								<raw>{@$comment->deleteTime}</raw>
								<formatted><![CDATA[{@$comment->deleteTime|time}]]></formatted>
							</time>
							<reason><![CDATA[{@$comment->deleteReason}]]></reason>
						</deletion>
					{/if}
				</comment>
			{/foreach}
		</comments>
	{/if}
	
	{* TODO: entries? *}
</guestbook>
