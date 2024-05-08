<?php
class news
{
var $db,$short,$page;





function item($id)
{
///  NEWS ITEM
$data = $this->db->row("SELECT * FROM news WHERE id = :id",array("id"=>$id));


///////// REGISTERED
if($data['type'] == 'registered')
{
$string .= $this->newsbox($data['owner'],$showphoto,'registered',$data['stamp'],'n').'';
}



///////// POST
else if($data['type'] == 'feed')
{
$string .= $this->newsbox($data['owner'],$showphoto,'made a post',$data['stamp'],'n').'<span class="is minidown"></span><div class="space10"></div>'.$this->short->feed($data['itemid'],'full').'';
}






///////// SUBSCRIBED
else if($data['type'] == 'follow')
{
$string .= $this->newsbox($data['owner'],$showphoto,'started following '.$this->short->user($data['itemid'],'text','n').'',$data['stamp'],'y').'<span class="is minidown"></span>'.$this->short->user($data['itemid'],'result','n').'';
}






///////// JOINED GROUP
else if($data['type'] == 'join')
{
$string .= $this->newsbox($data['owner'],$showphoto,'joined the group '.$this->short->group($data['itemid'],'text').'',$data['stamp'],'y').'<span class="is minidown"></span>'.$this->short->group($data['itemid'],'result').'';
}





///////// Created GROUP
else if($data['type'] == 'group')
{
$string .= $this->newsbox($data['owner'],$showphoto,'created the group '.$this->short->group($data['itemid'],'text').'',$data['stamp'],'y').'<span class="is minidown"></span>'.$this->short->group($data['itemid'],'result').'';
}





///////// POSTED IN FORUM
else if($data['type'] == 'forum')
{
$string .= $this->newsbox($data['owner'],$showphoto,'posted to forum topic '.$this->short->forumtopic($data['itemid'],'text','y').'',$data['stamp'],'y').'<span class="is minidown"></span><div class="space10"></div>'.$this->forumtopic($data['itemid']).'';
}






///////// CREATED A FORUM TOPIC
else if($data['type'] == 'newforum')
{
$string .= $this->newsbox($data['owner'],$showphoto,'created a forum topic '.$this->short->forumtopic($data['itemid'],'text','y').'',$data['stamp'],'y').'<span class="is minidown"></span><div class="space10"></div>'.$this->forumtopic($data['itemid']).'';
}








///////// Created a PERSONAL AD
else if($data['type'] == 'personalad')
{
$string .= $this->newsbox($data['owner'],$showphoto,'posted a personal ad: '.$this->short->personal($data['itemid'],'text','y').'',$data['stamp'],'y').'';
}








///////// Created a GALLERY
else if($data['type'] == 'gallery')
{
$string .= $this->newsbox($data['owner'],$showphoto,'created a photo gallery: '.$this->short->gallery($data['itemid'],'text','n').'',$data['stamp'],'y').'<span class="is minidown"></span>'.$this->short->gallery($data['itemid'],'result','n').'';
}










//////// VOTE
else if($data['type'] == 'vote')
{
$votedata = $this->db->row("SELECT * FROM votes WHERE id = :id",array("id"=>$data['itemid']));

if($votedata['type'] == 'photo')
{
$string .= $this->newsbox($data['owner'],$showphoto,'liked a photo: ',$data['stamp'],'y').'<span class="is minidown"></span>'.$this->newsphoto($votedata['itemid']);
}
else if($votedata['type'] == 'gallery')
{
$string .= $this->newsbox($data['owner'],$showphoto,'liked a gallery: ',$data['stamp'],'y').'<span class="is minidown"></span>'.$this->short->gallery($votedata['itemid'],'large','n');
}
else if($votedata['type'] == 'story')
{
$string .= $this->newsbox($data['owner'],$showphoto,'liked a story: '.$this->short->story($votedata['itemid'],'text','y').'',$data['stamp'],'y').'<span class="is minidown"></span>'.$this->storybox($votedata['itemid'],'result','y').'<div class="space-10"></div>';
}
else if($votedata['type'] == 'member')
{
$string .= $this->newsbox($data['owner'],$showphoto,'liked a profile: '.$this->short->user($votedata['itemid'],'text','n').'',$data['stamp'],'y').'<span class="is minidown"></span>'.$this->short->user($votedata['itemid'],'result','n').'';
}

else if($votedata['type'] == 'group')
{
$string .= $this->newsbox($data['owner'],$showphoto,'liked a group: '.$this->short->group($votedata['itemid'],'text').'',$data['stamp'],'y').'<span class="is minidown"></span>'.$this->short->group($votedata['itemid'],'result').'';
}
}





//////// VOTE
else if($data['type'] == 'comment')
{
$commentdata = $this->db->row("SELECT * FROM comments WHERE id = :id",array("id"=>$data['itemid']));

if($commentdata['type'] == 'photo')
{
$string .= $this->newsbox($data['owner'],$showphoto,'commented on a photo',$data['stamp'],'y').'<span class="is minidown"></span>'.$this->newsphoto($commentdata['itemid']);
}
else if($commentdata['type'] == 'gallery')
{
$string .= $this->newsbox($data['owner'],$showphoto,'commented on a gallery',$data['stamp'],'y').'<span class="is minidown"></span>'.$this->short->gallery($commentdata['itemid'],'large','n');
}
else if($commentdata['type'] == 'story')
{
$string .= $this->newsbox($data['owner'],$showphoto,'commented on a story: '.$this->short->story($commentdata['itemid'],'text','y').'',$data['stamp'],'y').'<span class="is minidown"></span>'.$this->storybox($commentdata['itemid'],'result','y').'<div class="space-10"></div>';
}
else if($commentdata['type'] == 'member')
{
$string .= $this->newsbox($data['owner'],$showphoto,'commented on a profile: '.$this->short->user($commentdata['itemid'],'text','n').'',$data['stamp'],'y').'<span class="is minidown"></span>'.$this->short->user($commentdata['itemid'],'result','n').'';
}
else if($commentdata['type'] == 'group')
{
$string .= $this->newsbox($data['owner'],$showphoto,'posted to the group: '.$this->short->group($commentdata['itemid'],'text').'',$data['stamp'],'y').'<span class="is minidown"></span>'.$this->short->group($commentdata['itemid'],'result').'';
}
}






else
{
$string = $data['type'];
}

return $string;
}













///////  NEWS USERBOX
function newsbox($id,$showphoto,$text,$time,$arrow)
{
$textname = $this->short->user($id,'text','n');
$imagename = $this->short->user($id,'image','n');
$darrow = ($arrow == 'y') ? '': '';
if($showphoto == 'n')
{
$data = ''.$textname.' '.$text.'.<div class="space5"></div><span class="font10 grey">'.$this->short->timeago($time).'</span><div class="space10"></div>';
}
else
{
$data = '<div class="fleft">'.$imagename.'</div>
<span class="disp70">
'.$textname.' '.$text.'.
<div class="space5"></div>
<span class="fs10 grey">'.$this->short->timeago($time).'</span>
<div class="space5"></div>
'.$darrow.'
</span>
<div class="clear"></div>
';
}
return $data;
}








///////////////  PHOTO
function newsphoto($id)
{
$photo = $this->db->row("SELECT * FROM galleryimages WHERE id = :id",array("id"=>$id));
$data = '<a href="https://www.theundergroundsexclub.com/?i='.$id.'"><img src="https://www.theundergroundsexclub.com/images/galleries/'.$photo['gallery'].'/'.$photo['image'].'-thumb.jpg" class="maxwidth100 mw103"/></a>';
return $data;
}








////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// USER DATA
function forumtopic($id)
{
$data = $this->db->row("SELECT * FROM forumtopics WHERE id = :id",array("id"=>$id));

$postplur = ($data['posts'] == '1') ? '': 's';
$viewplur = ($data['views'] == '1') ? '': 's';

$data = '<div class="forumright fright">
'.number_format($data['posts']).'&nbsp;<span class="grey">Post'.$postplur.'</span>
<div class="space5"></div>
'.number_format($data['views']).'&nbsp;<span class="grey">View'.$viewplur.'</span>
</div>
<div class="forumleft b0">
<span class="onelinetext"><a href="../?f='.$id.'">'.$this->short->clean($data['title']).'</a></span>
<div class="space5"></div>
<span class="onelinetext">by: '.$this->short->user($data['addedby'],'text','n').'</span>
</div>
<div class="clear"></div>';

return $data;
}




////////////////////////////////////////////////////////////////////////////// STORY
function storybox($id)
{
$story = $this->db->row("SELECT * FROM stories WHERE id = :id",array("id"=>$id));
if($story['id'] > 0)
{
$date = ($showdate == 'n') ? '': ' &nbsp; <span class="lightgrey">'.$this->short->timeago($story['stamp']).'</span>';
$likeplur = ($story['votesup'] == '1') ? '': 's';
$viewplur = ($story['views'] == '1') ? '': 's';
$data = '<div class="forumright fright">
'.number_format($story['views']).' <span class="grey">View'.$viesplur.'</span>
<div class="space5"></div>
'.number_format($story['votesup']).' <span class="grey">Like'.$likeplur.'</span>
</div>
<div class="forumleft b0">
<span class="onelinetext"><a href="../?s='.$story['id'].'">'.$this->short->clean($story['title']).'</a></span>
<div class="space5"></div>
<span class="onelinetext">By: '.$this->short->user($story['owner'],'text','n').''.$date.'</span>
</div>
<div class="clear"></div>';
}
return $data;
}






//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
$news = NEW news();
$news->page = &$page;
$news->short = &$short;
$news->db = &$db;
