<?php

class notice {

	private $_pri;
	
	function __construct($pri){
	
		$this->_pri = $pri;	
	
	}
	
	function display(){
	
		$local = new local();
		$sql = $local->selectQuery("`not_title` , `not_detail` , `not_date`", "notices", "not_prv <=".$this->_pri." ORDER BY not_date DESC");
		$html = '';
		
		while ($data = mysqli_fetch_array($sql)){
		
		//if ($data['not_prv'] < $this->_pri){		
			$html .= '<div class="message"><p><strong>'.$data['not_title'].'</strong> <em>'.date("jS M Y", strtotime($data['not_date'])).'</em></p>'.$data['not_detail'].'</div>'; 
		//}
		
		}

		return $html;
	
	}
	
	function listNotices(){
		
		$local = new local();
		
		$html = '<p>To view options, select the required notice below. 
		In this instance \'Access\' refers to the lowest access level 
		that will see the notice (Student is visible to all, for example)
		</p><p><table>
	    <tr>
	      <th><div class="th">Title</div></th>
		  <th><div class="th">Detail</div></th>
		  <th width="20%"><div class="th">Released</div></th>
	      <th width="20%"><div class="th">Access</div></th>
	    </tr>';
		
		$sql = $local->selectQuery("not_id, not_title, not_detail, not_date, not_prv, prv_name", "notices AS n
		INNER JOIN priviliges AS p ON n.not_prv = p.prv_id ORDER BY not_date DESC");
		
		while ($data = mysqli_fetch_array($sql)){
			//Trim the notice detail
			$detail = trim($data['not_detail'], "\<p>\</p>");
			$detail = str_split($detail, 20);
			$title = '<a href="./?not='.$data['not_id'].'&edit">'.$data['not_title'].'</a>';
			
			$html .= '<tr>
		      <td>'.$title.'</th>
			  <td>'.$detail[0].'...</th>
			  <td width="20%">'.date("jS M Y", strtotime($data['not_date'])).'</th>
		      <td width="20%">'.$data['prv_name'].'</th>
		    </tr>';
		}
		
		$html .= '</table></p>';
		
		if (inum_rows($sql) == 0){
			return '<p>No Notices found</p>';
		} else {		
			return $html;
		}
		
	}
	
	function editNotice($id){
			
		$local = new local();
		
		if ($_POST['Submit']){
		
		if (! $local->updateNotice($id, $_POST['title'], $detail, $_POST['prv'])){
			return '<p>Unable to edit notice. Please hit the back button on your 
			browser and try again, and if you are still unsuccessful please contact 
			an administrator for further assistance.</p>';
		} else {
			return '<p>Notice successfully edited.</p>';
		}
		
		} else {
		
			$sql = $local->selectQuery("*", "notices", "not_id = ".$id);
			while ($data = mysqli_fetch_array($sql)){
				$html = $this->formNotice($id);
			}
			
		}
		
		
		return $html;
	
	}
	
	function addNotice(){
	
	if ($_POST['Submit']){
	
		$local = new local();
	
		if (! $local->insertNotice($_POST['title'], $detail, $_POST['prv'])){
			return '<p>Unable to add notice. Please hit the back button on your 
			browser and try again, and if you are still unsuccessful please contact 
			an administrator for further assistance.</p>';
		} else {
			return '<p>Notice successfully added.</p>';
		}
	
	} else {
	
	$html = $this->formNotice();
	
	}
	
	return $html;
	
	}
	
	function removeNotice($id){
	
	
	
	}
	
	function formNotice($id = null){
	
	$local = new local();
	
	//Forgot to template this so it's hard-coded... :-P
	if ($id != null){
		$sql = $local->selectQuery("not_title, not_detail, not_prv", "notices", "not_id = ".$id);
		$data = mysqli_fetch_array($sql);
		$title = $data['not_title'];
		$detail = $data['not_detail'];
		$prv = $data['not_prv'];
		$submit = 'Submit Revisions';
		$deleteForm = '<br /><form name="notice_delete" method="post" action="./?not='.$id.'&del">
			<input type="submit" value="Delete Notice">
		</form>';
		$action = './?not='.$id.'&edit';
	} else {
		$title = '';
		$detail = '';
		$prv = '';
		$submit = 'Add Notice';
		$deleteForm = '';
		$action = './?not&add';
	}
	
	$html = '<p>Please enter the required details below.</p><form name="formNotice" method="post" action="'.$action.'">
	  <input type="text" name="title" value="'.$title.'" size="40" maxlength="128" /> <label>Title</label><br /><br />
	  <textarea name="detail" cols="80" rows="20">'.$detail.'</textarea><br /><br />
	  <select name="prv">';
		$sql_prv = $local->selectQuery("*", "priviliges");
		while ($data_prv = mysqli_fetch_array($sql_prv)){
			if ($data_prv['prv_id'] == $prv){
				$selected = ' selected';
			} else {
				$selected = '';
			}			
			$html .= '<option value="'.$data_prv['prv_id'].'"'.$selected.'>'.$data_prv['prv_name'].'</option>';
	    }
	    $html .= '</select> <label>Access</label><br /><br />
		<input type="submit" name="Submit" value="'.$submit.'" />
	</form>'.$deleteForm;
	
	return $html;
	
	}
	
}

?>