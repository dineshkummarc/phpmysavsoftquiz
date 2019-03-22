<?php
Class Qbank_model extends CI_Model
{
 
  function question_list($limit,$cid='0',$lid='0'){	//????????????
	if($this->input->post('search')){
		$search=$this->input->post('search');
		$this->db->or_where('savsoft_qbank.qid',$search);
		$this->db->or_like('savsoft_qbank.question',$search);
		$this->db->or_like('savsoft_qbank.description',$search);	//WHERE savsoft_qbank.qid = $search OR savsoft_qbank.question LIKE $search OR ...

	}
	if($cid!='0'){
		$this->db->where('savsoft_qbank.cid',$cid);
	}
	if($lid!='0'){
		$this->db->where('savsoft_qbank.lid',$lid);
	}
	$this->db->join('savsoft_category','savsoft_category.cid=savsoft_qbank.cid');	//SELECT savsoft_category.* JOIN savsoft_qbank ON savsoft_category.cid=savsoft_qbank.cid
	$this->db->join('savsoft_level','savsoft_level.lid=savsoft_qbank.lid');
	$this->db->limit($this->config->item('number_of_rows'),$limit);		//limit(n,m)->??m+1?????n????arg1->?????arg2->?????
	$this->db->order_by('savsoft_qbank.qid','desc');
	$query=$this->db->get('savsoft_qbank');		//FROM savsoft_qbank
	return $query->result_array();
		
 }
 
 
 function num_qbank(){
	 
	 $query=$this->db->get('savsoft_qbank');
		return $query->num_rows();
 }
 
 
 
 function get_question($qid){
	 $this->db->where('qid',$qid);
	 $query=$this->db->get('savsoft_qbank');
	 return $query->row_array();
	 
	 
 }
 function get_option($qid){
	 $this->db->where('qid',$qid);
	 $query=$this->db->get('savsoft_options');
	 return $query->result_array();
	 
	 
 }
 
 function remove_question($qid){	//?question list?????
	 
	 $this->db->where('qid',$qid);
	 if($this->db->delete('savsoft_qbank')){
		$this->db->where('qid',$qid);		//WHERE qid=$qid
		$this->db->delete('savsoft_options');	//DELETE FROM savsoft_options
		

		//???????????? ??? ?????

		//????????????????????????,?????????????,?????haystack????????????????				
		$qr=$this->db->query("select * from savsoft_quiz where FIND_IN_SET($qid, qids) ");	//qids?????????????????
	 
		foreach($qr->result_array() as $k =>$val){
		
			$quid=$val['quid'];		//??id
			$qids=explode(',',$val['qids']);	//explode?????????????js?split()
			$nqids=array();
			foreach($qids as $qk => $qv){
				if($qv != $qid){
					$nqids[]=$qv;	//$nqids???? ?????????????????id??
				}
			}
			$noq=count($nqids);		//?????
			$nqids=implode(',',$nqids);		//???js?join(),????????????????????????
			$this->db->query(" update savsoft_quiz set qids='$nqids', noq='$noq' where quid='$quid' ");		//????
		}		
		return true;
	 }else{
		return false;
	 }
	
 }
 
 function insert_question_1(){
	 
	 $userdata=array(
	 'question'=>$this->input->post('question'),
	 'description'=>$this->input->post('description'),
	 'question_type'=>$this->lang->line('multiple_choice_single_answer'),
	 'cid'=>$this->input->post('cid'),
	 'lid'=>$this->input->post('lid')	 
	 );
	 $this->db->insert('savsoft_qbank',$userdata);
	 $qid=$this->db->insert_id();
	 foreach($this->input->post('option') as $key => $val){
		 if($this->input->post('score')==$key){
			 $score=1;
		 }else{
			 $score=0;
		 }
	$userdata=array(
	 'q_option'=>$val,
	 'qid'=>$qid,
	 'score'=>$score,
	 );
	 $this->db->insert('savsoft_options',$userdata);	 
		 
	 }
	 
	 return true;
	 
 }

 function insert_question_2(){
	 
	 
	 $userdata=array(
	 'question'=>$this->input->post('question'),
	 'description'=>$this->input->post('description'),
	 'question_type'=>$this->lang->line('multiple_choice_multiple_answer'),
	 'cid'=>$this->input->post('cid'),
	 'lid'=>$this->input->post('lid')	 
	 );
	 $this->db->insert('savsoft_qbank',$userdata);
	 $qid=$this->db->insert_id();
	 foreach($this->input->post('option') as $key => $val){
		 if(in_array($key,$this->input->post('score'))){
			 $score=(1/count($this->input->post('score')));
		 }else{
			 $score=0;
		 }
	$userdata=array(
	 'q_option'=>$val,
	 'qid'=>$qid,
	 'score'=>$score,
	 );
	 $this->db->insert('savsoft_options',$userdata);	 
		 
	 }
	 
	 return true;
	 
 }
 
 
 function insert_question_3(){
	 
	 
	 $userdata=array(
	 'question'=>$this->input->post('question'),
	 'description'=>$this->input->post('description'),
	 'question_type'=>$this->lang->line('match_the_column'),
	 'cid'=>$this->input->post('cid'),
	 'lid'=>$this->input->post('lid')	 
	 );
	 $this->db->insert('savsoft_qbank',$userdata);
	 $qid=$this->db->insert_id();
	 foreach($this->input->post('option') as $key => $val){
	  $score=(1/count($this->input->post('option')));
	$userdata=array(
	 'q_option'=>$val,
	 'q_option_match'=>$_POST['option2'][$key],
	 'qid'=>$qid,
	 'score'=>$score,
	 );
	 $this->db->insert('savsoft_options',$userdata);	 
		 
	 }
	 
	 return true;
	 
 }
 
 
 
 
 function insert_question_4(){
	 
	 
	 $userdata=array(
	 'question'=>$this->input->post('question'),
	 'description'=>$this->input->post('description'),
	 'question_type'=>$this->lang->line('short_answer'),
	 'cid'=>$this->input->post('cid'),
	 'lid'=>$this->input->post('lid')	 
	 );
	 $this->db->insert('savsoft_qbank',$userdata);
	 $qid=$this->db->insert_id();
	 foreach($this->input->post('option') as $key => $val){
	  $score=1;
	$userdata=array(
	 'q_option'=>$val,
	 'qid'=>$qid,
	 'score'=>$score,
	 );
	 $this->db->insert('savsoft_options',$userdata);	 
		 
	 }
	 
	 return true;
	 
 }
 
 
 function insert_question_5(){
	 
	 
	 $userdata=array(
	 'question'=>$this->input->post('question'),
	 'description'=>$this->input->post('description'),
	 'question_type'=>$this->lang->line('long_answer'),
	 'cid'=>$this->input->post('cid'),
	 'lid'=>$this->input->post('lid')	 
	 );
	 $this->db->insert('savsoft_qbank',$userdata);
	 $qid=$this->db->insert_id();
	 
	 
	 return true;
	 
 }
 
 
 
  function update_question_1($qid){
	 
	 
	 $userdata=array(
	 'question'=>$this->input->post('question'),
	 'description'=>$this->input->post('description'),
	 'question_type'=>$this->lang->line('multiple_choice_single_answer'),
	 'cid'=>$this->input->post('cid'),
	 'lid'=>$this->input->post('lid')	 
	 );
	 $this->db->where('qid',$qid);
	 $this->db->update('savsoft_qbank',$userdata);
	 $this->db->where('qid',$qid);
	$this->db->delete('savsoft_options');
	 foreach($this->input->post('option') as $key => $val){
		 
		 
		 if($this->input->post('score')==$key){
			 $score=1;
		 }else{
			 $score=0;
		 }
	$userdata=array(
	 'q_option'=>$val,
	 'qid'=>$qid,
	 'score'=>$score,
	 );
	 $this->db->insert('savsoft_options',$userdata);	 
		 
	 }
	 
	 return true;
	 
 }

 
 
 
 
  function update_question_2($qid){
	 
	 
	 $userdata=array(
	 'question'=>$this->input->post('question'),
	 'description'=>$this->input->post('description'),
	 'question_type'=>$this->lang->line('multiple_choice_multiple_answer'),
	 'cid'=>$this->input->post('cid'),
	 'lid'=>$this->input->post('lid')	 
	 );
	 $this->db->where('qid',$qid);
	 $this->db->update('savsoft_qbank',$userdata);
	 $this->db->where('qid',$qid);
	$this->db->delete('savsoft_options');
	 foreach($this->input->post('option') as $key => $val){
		 if(in_array($key,$this->input->post('score'))){
			 $score=(1/count($this->input->post('score')));
		 }else{
			 $score=0;
		 }
	$userdata=array(
	 'q_option'=>$val,
	 'qid'=>$qid,
	 'score'=>$score,
	 );
	 $this->db->insert('savsoft_options',$userdata);	 
		 
	 }
	 
	 return true;
	 
 }
 
 
 function update_question_3($qid){
	 
	 
	 $userdata=array(
	 'question'=>$this->input->post('question'),
	 'description'=>$this->input->post('description'),
	 'question_type'=>$this->lang->line('match_the_column'),
	 'cid'=>$this->input->post('cid'),
	 'lid'=>$this->input->post('lid')	 
	 );
	 	 $this->db->where('qid',$qid);
	 $this->db->update('savsoft_qbank',$userdata);
	 $this->db->where('qid',$qid);
	$this->db->delete('savsoft_options');
	foreach($this->input->post('option') as $key => $val){
	  $score=(1/count($this->input->post('option')));
	$userdata=array(
	 'q_option'=>$val,
	 'q_option_match'=>$_POST['option2'][$key],
	 'qid'=>$qid,
	 'score'=>$score,
	 );
	 $this->db->insert('savsoft_options',$userdata);	 
		 
	 }
	 
	 return true;
	 
 }
 
 
 
 
 function update_question_4($qid){
	 
	 
	 $userdata=array(
	 'question'=>$this->input->post('question'),
	 'description'=>$this->input->post('description'),
	 'question_type'=>$this->lang->line('short_answer'),
	 'cid'=>$this->input->post('cid'),
	 'lid'=>$this->input->post('lid')	 
	 );
		 $this->db->where('qid',$qid);
	 $this->db->update('savsoft_qbank',$userdata);
	 $this->db->where('qid',$qid);
	$this->db->delete('savsoft_options');
 foreach($this->input->post('option') as $key => $val){
	  $score=1;
	$userdata=array(
	 'q_option'=>$val,
	 'qid'=>$qid,
	 'score'=>$score,
	 );
	 $this->db->insert('savsoft_options',$userdata);	 
		 
	 }
	 
	 return true;
	 
 }
 
 
 function update_question_5($qid){
	 
	 
	 $userdata=array(
	 'question'=>$this->input->post('question'),
	 'description'=>$this->input->post('description'),
	 'question_type'=>$this->lang->line('long_answer'),
	 'cid'=>$this->input->post('cid'),
	 'lid'=>$this->input->post('lid')	 
	 );
		 $this->db->where('qid',$qid);
	 $this->db->update('savsoft_qbank',$userdata);
	 $this->db->where('qid',$qid);
	$this->db->delete('savsoft_options');

	 
	 return true;
	 
 }
 
 
 
 
 // category function start ???????
 function category_list(){
	 $this->db->order_by('cid','desc');	//??
	 $query=$this->db->get('savsoft_category');	//SELECT * FROM savsoft_category
	 return $query->result_array();
	 
 }
 
 
 
 
 function update_category($cid){
	 
		$userdata=array(
		'category_name'=>$this->input->post('category_name'),
		 	
		);
	 
		 $this->db->where('cid',$cid);
		if($this->db->update('savsoft_category',$userdata)){
			
			return true;
		}else{
			
			return false;
		}
	 
 }
  
 
 
 function remove_category($cid){
	 
	 $this->db->where('cid',$cid);
	 if($this->db->delete('savsoft_category')){
		 return true;
	 }else{
		 
		 return false;
	 }
	 
	 
 }
 
  
 
 function insert_category(){
	 
	 	$userdata=array(
		'category_name'=>$this->input->post('category_name'),
			);
		
		if($this->db->insert('savsoft_category',$userdata)){
			
			return true;
		}else{
			
			return false;
		}
	 
 }
 
 // category function end
 
 
 

 
 
// level function start ?????????
 function level_list(){
	  $query=$this->db->get('savsoft_level');
	 return $query->result_array();
	 
 }
 
 
 
 
 function update_level($lid){
	 
		$userdata=array(
		'level_name'=>$this->input->post('level_name'),
		 	
		);
	 
		 $this->db->where('lid',$lid);
		if($this->db->update('savsoft_level',$userdata)){
			
			return true;
		}else{
			
			return false;
		}
	 
 }
  
 
 
 function remove_level($lid){
	 
	 $this->db->where('lid',$lid);
	 if($this->db->delete('savsoft_level')){
		 return true;
	 }else{
		 
		 return false;
	 }
	 
	 
 }
 
  
 
 function insert_level(){
	 
	 	$userdata=array(
		'level_name'=>$this->input->post('level_name'),
			);
		
		if($this->db->insert('savsoft_level',$userdata)){
			
			return true;
		}else{
			
			return false;
		}
	 
 }
 
 // level function end
 

 
 
 
 //閫氳繃xls鎵归噺寮曞叆闂
 function import_question($question){	//$question鏄暟缁勶紝鐢眅xcel琛ㄦ牸涓殑姣忚鏁版嵁缁勬垚
//echo "<pre>"; print_r($question);exit;
$questioncid=$this->input->post('cid');		//?? category_id
$questiondid=$this->input->post('did');		//lid select_level
foreach($question as $key => $singlequestion){
	//$ques_type= 
	
//echo $ques_type; 

if($key != 0){	//key=0???????
	echo "<pre>";print_r($singlequestion);		//key???????singlequestion???? ???????question?description?corrent_option_number?option1?option2...?
	//??$question?????????[1]?$description???????????[2]
	$question= str_replace('"','&#34;',$singlequestion['1']);

	$question= str_replace("`",'&#39;',$question);
	$question= str_replace("闂佺偨鍎婚敓锟�?",'&#39;',$question);
	$question= str_replace("闂佺偨鍎婚敓锟�?",'&#39;',$question);
	$question= str_replace("闁肩儤甯￠崺鈧柨鐔封偓鐕傛嫹??",'&#34;',$question);
	$question= str_replace("闁肩儤甯￠崺鈧柨鐔封偓鐔革紵",'&#39;',$question);
	$question= str_replace("闁肩儤甯￠崺鈧柨鐔剁矙閸╁嫰鏁撻敓锟�?",'&#39;',$question);
	$question= str_replace("闁肩儤甯￠崺鈧柨鐔封偓鐔剁床",'&#34;',$question);
	$question= str_replace("'","&#39;",$question);
	$question= str_replace("\n","<br>",$question);

	$description= str_replace('"','&#34;',$singlequestion['2']);

	$description= str_replace("'","&#39;",$description);
	$description= str_replace("\n","<br>",$description);

	$ques_type= $singlequestion['0'];	//????

	if($ques_type=="0" || $ques_type==""){
	$question_type=$this->lang->line('multiple_choice_single_answer');	
	}
	if($ques_type=="1"){
	$question_type=$this->lang->line('multiple_choice_multiple_answer');	
	}
	if($ques_type=="2"){
	$question_type=$this->lang->line('match_the_column');	
	}
	if($ques_type=="3"){
	$question_type=$this->lang->line('short_answer');	
	}
	if($ques_type=="4"){
	$question_type=$this->lang->line('long_answer');	
	}


	$insert_data = array(
	'cid' => $questioncid,
	'lid' => $questiondid,
	'question' =>$question,
	'description' => $description,
	'question_type' => $question_type
	);
	
	if($this->db->insert('savsoft_qbank',$insert_data)){
		$qid=$this->db->insert_id();	//??? INSERT ???????????????ID?
		$optionkeycounter = 4;


		//??
		if($ques_type=="0" || $ques_type==""){	
			for($i=1;$i<=10;$i++){	//??10???
				if($singlequestion[$optionkeycounter] != ""){	//$singlequestion ??????$singlequestion[4]??????
					if($singlequestion['3'] == $i){ $correctoption ='1'; }	//???????
					else{ $correctoption = 0; }
					$insert_options = array(
					"qid" =>$qid,
					"q_option" => $singlequestion[$optionkeycounter],	//??????
					"score" => $correctoption
					);
					$this->db->insert("savsoft_options",$insert_options);
					$optionkeycounter++;
				}
				
			}
		}


		//multiple type??
		if($ques_type=="1"){
			$correct_options=explode(",",$singlequestion['3']);		//split
			$no_correct=count($correct_options);	//正确答案的个数
			$correctoptionm=array();
			for($i=1;$i<=10;$i++){	//??10???
				if($singlequestion[$optionkeycounter] != ""){	//??????
					foreach($correct_options as $valueop){
						if($valueop == $i){ $correctoptionm[$i-1] =(1/$no_correct);		//?i????????? 1?/??????
							break;
						}
						else{ $correctoptionm[$i-1] = 0; }
					}
				}
			}
				
			//print_r($correctoptionm);
				
			for($i=1;$i<=10;$i++){
			
				if($singlequestion[$optionkeycounter] != ""){	//$optionkeycounter=4
				
					$insert_options = array(
						"qid" =>$qid,
						"q_option" => $singlequestion[$optionkeycounter],
						"score" => $correctoptionm[$i-1]
					);
					$this->db->insert("savsoft_options",$insert_options);
					$optionkeycounter++;
					
					
					}
				}
		}
		//multiple type end	
	

 		//match Answer
		if($ques_type=="2"){
			$qotion_match=0;	//匹配题目的选项个数s
			for($j=1;$j<=10;$j++){
			
				if($singlequestion[$optionkeycounter] != ""){
				
					$qotion_match+=1;
					$optionkeycounter++;
				}
				
			}
			///h
			$optionkeycounter=4;	
			for($i=1;$i<=10;$i++){
			
				if($singlequestion[$optionkeycounter] != ""){
					$explode_match=explode('=',$singlequestion[$optionkeycounter]);		//A=B -> [A,B]
					$correctoption =1/$qotion_match; 	//每道匹配选项的分值
					$insert_options = array(
						"qid" =>$qid,
						"q_option" =>$explode_match[0] ,
						"q_option_match" =>$explode_match[1] ,
						"score" => $correctoption
					);
					$this->db->insert("savsoft_options",$insert_options);
					$optionkeycounter++;
				}
				
			}
			
		}
		//end match answer
	

		//short Answer
		if($ques_type=="3"){
			for($i=1;$i<=1;$i++){
			
				if($singlequestion[$optionkeycounter] != ""){
					if($singlequestion['3'] == $i){ $correctoption ='1'; }
					$insert_options = array(
						"qid" =>$qid,
						"q_option" => $singlequestion[$optionkeycounter],
						"score" => $correctoption
					);
					$this->db->insert("savsoft_options",$insert_options);
					$optionkeycounter++;
				}
				
			}
			
		}
		//end Short answer


		//TODO閿涙氨宸辩亸鎲€ong answer閻ㄥ嫬鐡ㄩ崒銊х摕濡楀牓鈧槒绶敍宀€娲伴崜宥夌帛鐠侇槖ong answer濞屸剝婀侀弽鍥у櫙缁涙梹顢嶉敍灞惧娴犮儰绗夋导姘晙Option娑擃厽鏂侀崗銉х摕濡楋拷
		if($ques_type=="4"){
			for($i=1;$i<=1;$i++){

				if($singlequestion[$optionkeycounter] != ""){
					// if($singlequestion['3'] == $i){ $correctoption ='1'; }
					// $insert_options = array(
					// 	"qid" =>$qid,
					// 	"q_option" => $singlequestion[$optionkeycounter],
					// 	"score" => $correctoption
					// );
					// $this->db->insert("savsoft_options",$insert_options);
					// $optionkeycounter++;
				}
			}

		} 
	
	
	
	}//END OF: if($this->db->insert('savsoft_qbank',$insert_data))... ???qbank???????????????options???
	

}//END OF:if($key!=0){


}//END OF:foreach($allxlsdata as $key => $singlequestion){



}//END OF:the function import_question($allxlsdata)






 
}







 



?>
