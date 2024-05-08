<?php
class notifications
{
var $db,$short,$page;





function notifystring($type,$who,$otherstext,$itemid,$stamp)
{
if($type == 'follow')
{
$string = $this->short->user($who,'text','n').''.$otherstext.' followed you</br><span class="lightgrey">'.$this->short->timeago($stamp).'</span>';
}
else if($type == 'joinmygroup')
{
$string = $this->short->user($who,'text','n').''.$otherstext.' joined your group: '.$this->short->group($itemid,'text').'</br><span class="lightgrey">'.$this->short->timeago($stamp).'</span>';
}
else if($type == 'myforumreply')
{
$string = $this->short->user($who,'text','n').''.$otherstext.' replied to your forum topic: '.$this->short->forumtopic($itemid,'text','y').'</br><span class="lightgrey">'.$this->short->timeago($stamp).'</span>';
}
else if($type == 'forumreply')
{
$string = $this->short->user($who,'text','n').''.$otherstext.' replied to the forum topic: '.$this->short->forumtopic($itemid,'text','n').'</br><span class="lightgrey">'.$this->short->timeago($stamp).'</span>';
}
else if($type == 'storycomments')
{
$string = $this->short->user($who,'text','n').''.$otherstext.' commented on your sex story: '.$this->short->story($itemid,'text','y').'</br><span class="lightgrey">'.$this->short->timeago($stamp).'</span>';
}
else if($type == 'photocomments')
{
$string = $this->short->user($who,'text','n').''.$otherstext.' commented on your photo: <div class="space5"></div>'.$this->short->photo($itemid,'40').'<div class="space5"></div><span class="lightgrey">'.$this->short->timeago($stamp).'</span>';
}
else if($type == 'gallerycomments')
{
$string = $this->short->user($who,'text','n').''.$otherstext.' commented on your gallery: <div class="space5"></div>'.$this->short->gallery($itemid,'text','y').'<div class="space5"></div><span class="lightgrey">'.$this->short->timeago($stamp).'</span>';
}
else if($type == 'membercomments')
{
$string = $this->short->user($who,'text','n').''.$otherstext.' commented on your profile: '.$this->short->user($_SESSION['userid'],'text','n').'</br><span class="lightgrey">'.$this->short->timeago($stamp).'</span>';
}
else if($type == 'feedcomments')
{
$string = $this->short->user($who,'text','n').''.$otherstext.' commented on your post: '.$this->short->feed($itemid,'linktext').'<div class="space5"></div><span class="lightgrey">'.$this->short->timeago($stamp).'</span>';
}
else if($type == 'membervote')
{
$string = $this->short->user($who,'text','n').''.$otherstext.' liked your profile: '.$this->short->user($_SESSION['userid'],'text','n').'</br><span class="lightgrey">'.$this->short->timeago($stamp).'</span>';
}
else if($type == 'photovote')
{
$string = $this->short->user($who,'text','n').''.$otherstext.' liked your photo: <div class="space5"></div>'.$this->short->photo($itemid,'40').'<div class="space5"></div><span class="lightgrey">'.$this->short->timeago($stamp).'</span>';
}
else if($type == 'storyvote')
{
$string = $this->short->user($who,'text','n').''.$otherstext.' liked your sex story: '.$this->short->story($itemid,'text','y').'</br><span class="lightgrey">'.$this->short->timeago($stamp).'</span>';
}
else if($type == 'galleryvote')
{
$string = $this->short->user($who,'text','n').''.$otherstext.' liked your gallery: <div class="space5"></div>'.$this->short->gallery($itemid,'text','y').'<div class="space5"></div><span class="lightgrey">'.$this->short->timeago($stamp).'</span>';
}
else if($type == 'feedvote')
{
$string = $this->short->user($who,'text','n').''.$otherstext.' liked your post: '.$this->short->feed($itemid,'linktext').'<div class="space5"></div><span class="lightgrey">'.$this->short->timeago($stamp).'</span>';
}
else if($type == 'groupcomment')
{
$string = $this->short->user($who,'text','n').''.$otherstext.' posted to a group you are in: '.$this->short->group($itemid,'text').'</br><span class="lightgrey">'.$this->short->timeago($stamp).'</span>';
}
/////////////////////
return $string;
}






//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
$notifications = NEW notifications();
$notifications->page = &$page;
$notifications->short = &$short;
$notifications->db = &$db;
