<?php
//$Id: function.php 5310 2009-01-10 07:57:56Z hami $
    function OpenTable() {
      global $templetdir ;
      echo "<table border=0 cellspacing=0 cellpadding=0 ><tr>\n" ;
      echo "<td width='5' ><img src='$templetdir/p_u_l.gif' border=0 width='5' height='4'  ></td> \n" ;
    
      echo "<td background='$templetdir/p.gif' width='99%' height='4'></td> \n";
      echo "<td width=5><img src='$templetdir/p_u_r.gif' border=0 width=5 height=4></td>\n";
    
      echo "<tr><td background='$templetdir/p.gif' width=5 ></td> \n" ;
      echo "<td background='$templetdir/p.gif' >\n";
    }

    function CloseTable() {
      global $templetdir ;
      echo "</td>
      <td background='$templetdir/p.gif'  width=5 ></td></tr>\n" ;
    
      echo "<tr>
          <td width=5><img src='$templetdir/p_d_l.gif' width=5 height=4></td>\n" ;
      echo "<td  background='$templetdir/p.gif'  height=4></td> \n" ;
      echo "<td width=5><img src='$templetdir/p_d_r.gif' width=5 height=4></td> \n";
      echo "</tr></table>\n" ;
    }

    function OpenTable2() {
      global $templetdir ;
      echo "<table border='0' cellspacing='0' cellpadding='0'  width = '100%'>
          <tr>" ;
      echo "<td width='29' ><img src='$templetdir/b_0.gif' border='0' width='29' height='35'  ></td>\n" ;
    
      echo "<td  background='$templetdir/b_1.gif' width='99%' ></td>\n";
      echo "<td width='25'><img src='$templetdir/b_2.gif' width='25' height='35' border='0' ></td>\n" ;
    
      echo "<tr><td background='$templetdir/b_3.gif'></td> \n";
      echo "<td background='$templetdir/b_4.gif'> \n";
    }

    function CloseTable2() {
      global $templetdir ;
      echo "</td> \n" ;
      echo "<td background='$templetdir/b_5.gif'></td></tr> \n" ;
    
      echo "<tr>\n" ;
      echo "<td ><img src='$templetdir/b_6.gif' border='0' width='29' height='23'  ></td>\n" ;
    
      echo "<td  background='$templetdir/b_7.gif'></td>\n";
      echo "<td width='25'><img src='$templetdir/b_8.gif' width='25' height='23' border='0' ></td>\n" ;
      echo "</tr></table>\n" ;
    }

    
    //取出該篇文章------------------------------------------------------------      
    function get_paper($book_num,$chap,$paper)
    {
        global  $CONN , $self_php , $htmlpath ,$basepath ;
        
        $sqlstr =  "select a.* ,b.chap_path , c.book_path 
                    from magazine_paper a , magazine_chap b , magazine c
                    where a.id=$paper and a.chap_num = b.id and b.book_num= c.id  " ;
        $result = $CONN->Execute( $sqlstr) ;   
        if ($result) 
           while ($row= $result->FetchRow() ) {  
             
             $paper_doc[author] = $row["author"] ;  
             $paper_doc[title] = $row["title"] ;  
             $paper_doc[cmode] = $row["tmode"] ;  
             $paper_doc[type_name] = $row["type_name"] ;           
             $paper_doc[judge] = nl2br($row["judge"]) ;    
             $paper_doc[teacher] = $row["teacher"] ;
             $paper_doc[parent] = $row["parent"] ;             
             $doc = $row["doc"] ;
             $chap_path = $row["chap_path"] ;
             $book_path = $row["book_path"] ;             
             $paper_doc[pic_name] = "$book_path/$chap_path/" . $row["pic_name"] ;

             $paper_doc[classname] =  $row["class_name"] ;        

             $doc =htmlspecialchars($doc) ;
             $doc = ereg_replace("\n","<br>",$doc) ;
             $paper_doc[doc] = ereg_replace("[[:space:]]","&nbsp;",$doc);               
           }     

           return $paper_doc ;          
  
    }    
    

    //右方頁面，作品列表------------------------------------------------------------      
    function showpaper($book_num,$chap=0,$paper=0) 
    {   //$book_num 期別, $chap 類別, $paper篇號
        global  $CONN , $self_php , $templetdir ,$htmlpath ,$basepath ;         

           if ($chap==0) {
                //未指定章節以第一節為准
                $sqlstr =  "select * from magazine_chap  where  book_num='$book_num' order by chap_sort " ;
                $result = $CONN->Execute( $sqlstr) ;   
                $chap = $result->fields["id"]; 
            }  
               
            //章節類型(0.文章、1.圖檔、2.班級資料3.網頁)
            $sqlstr =  "select c.* , m.book_path from magazine_chap c, magazine m
                     where c.id = $chap and c.book_num=$book_num  and c.book_num=m.id " ;
            //echo  $sqlstr ;        
            $result = $CONN->Execute( $sqlstr) ; 
            if ($result) 
                while ($row=   $result->FetchRow() ) {  
                    $paper_list[cmode] = $row["cmode"] ;   
                    $paper_list[chap_name] = $row["chap_name"] ;
                    $paper_list[small_pic] = $row["small_pic"] ;
                    $new_win = $row["new_win"] ;
                    $chap_path  = $row["chap_path"] ;
                    $book_path  = $row["book_path"] ; 
                    $paper_list[include_mode]  = $row["include_mode"] ;     
                    $paper_list[chap_path]  = $row["chap_path"] ;
                    $paper_list[book_path]  = $row["book_path"] ; 
                     
                }
                
            if ($new_win) {    //出現在新視窗
              $view_self_php= $self_php ."?new_win=$new_win&" ;
              $target  = " target=\"show_data " ;
            }else {
            	$view_self_php = "$self_php?"  ;
            }	

            if ($cmode ==3){  //顯示這個網頁
               //if ($paper_list.include_mode)  內含
               
            } else {  
             
            //各章節篇名
            $sqlstr =  "select * from magazine_paper  where chap_num = $chap  " ;
            $sqlstr .= " order by classnum  " ;
            $i=0 ;   
            $result = $CONN->Execute( $sqlstr) ;     
                    while ($row= $result->FetchRow()  ) {  

                       $item[$i][classname] =  $row["class_name"] ;
                       $item[$i][name] = $row["author"] ;
                       $item[$i][title] = $row["title"] ;
                       $item[$i][parent] = $row["parent"] ;
                       $item[$i][teacher] = $row["teacher"] ;
                       $paper_id = $row["id"] ;
                       if ($target)
                          $item[$i][link] = "$view_self_php"."book_num=$book_num&chap=$chap&paper=$paper_id\"  $target" ;
                       else    
			  $item[$i][link] = "$view_self_php"."book_num=$book_num&chap=$chap&paper=$paper_id" ;
                       $pic_name = "___".$row["pic_name"] ;
                       //要出現縮圖及有縮圖
                       if ($paper_list[small_pic] and (is_file($basepath."$book_path/$chap_path/$pic_name"))) 
                           $item[$i][pic_name] = "$book_path/$chap_path/$pic_name"  ;
                       $item[$i][doc] = nl2br($row["doc"]) ;
                       $i++ ;
                }                        
           }     
           $paper_list[item]= $item ;
           return $paper_list ;

    }    


?>