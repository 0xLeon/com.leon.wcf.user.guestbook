<?xml version="1.0" encoding="{CHARSET}"?>
<guestbook>
	{if $comments|count}
		<comments count="{$comments|count}">
			{foreach from=$comments item=comment}
				<comment id="{@$comment->commentID}">
					<author>
						<userID>{@$comment->getAuthor()->userID}</userID>
						<username><![CDATA[{@$comment->username}]]></username>
						<ip><![CDATA[{@$comment->ipAddress}]]></ip>
						<avatar>
							{if $comment->getAuthor()->getAvatar()}
								{assign var=tmp value=$entry->getAuthor()->getAvatar()->setMaxSize(50, 50)}
								<path><![CDATA[{@$comment->getAuthor()->getAvatar()->getURL()}]]></path>
								<width>{@$entry->getAuthor->getAvatar()->getWidth()}</width>
								<height>{@$entry->getAuthor->getAvatar()->getHeight()}</height>
							{else}
								<path><![CDATA[{@RELATIVE_WCF_DIR}images/avatars/avatar-default.png]]></path>
								<width>50</width>
								<height>50</height>
							{/if}
						</avatar>
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
