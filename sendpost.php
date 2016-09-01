<?
$mailto = "artmalini@gmail.com";
$charset = "iso-8859-1";
$subject = "New message";
$content = "text/html";

global $status;

if (!empty($_POST)) {

       $name = htmlspecialchars(stripslashes($_POST['full_name']));
       $mail = htmlspecialchars(stripslashes($_POST['email']));
       $salary = htmlspecialchars(stripslashes($_POST['salary_expectation']));
       $postal = htmlspecialchars(stripslashes($_POST['postal_address']));
       $personal = htmlspecialchars(stripslashes($_POST['personal_statement']));
       $additional_information = htmlspecialchars(stripslashes($_POST['additional_information']));  
       $date = date('m/d/Y h:i:s a', time());
       $picture1 = "";
       $picture2 = "";
       $new_path = null;
       $new_path2 = null;
       $status = "";

    if (empty($_POST['email'])) {

      $status .= "empty E-mail! \\r\\n";

    } elseif (!preg_match("/^[0-9a-z_]+@[0-9a-z_^\.]+\.[a-z]{2,6}$/i", $mail)) {

      $status .= "wrong email \\r\\n";

    } else {

         $headers  = "MIME-Version: 1.0\r\n";
         $headers .= "From: \"".$name."\" <".$mail.">\r\n";
         $headers .= "Bcc: admin\r\n";
         $headers .= "X-Mailer: E-mail from my LINX America \r\n";
         $sendmessage = "<html><body>
             <p><b><strong>From:</strong> ".$name."</p>
          <p><b><strong>Mail:</strong> ".$mail."</p>
          <p><b><strong>Salary:</strong> ".$salary."</p>
          <p><b><strong>Postal:</strong> ".$postal."</p>
          <p><b><strong>Personal:</strong> ".$personal."</p>
          <p><b><strong>Additional Information:</strong> ".$additional_information."</p>
          <p><b>".$date."</p>
          </body></html>";

        if (!empty($_FILES['file_cv']['tmp_name'])) {
            $path = $_FILES['file_cv']['name'];

            ini_set('memory_limit', '32M'); 
            $maxsize = "10000000"; //10mb
            $extentions = array("gif","doc","docx","xls"); //extentions = ( "gif","txt","tpl","jpg","jpeg","png","zip", "rar","7z","tif","psd","swf","flv","avi","mpeg","mp4","mp3","wav", "ogg","ogm","doc","xls","ppt");
            $size = filesize($_FILES['file_cv']['tmp_name']); 
            $type = strtolower(substr($path, 1+strrpos($path,".")));
            $new_path = 'file-'.uniqid(rand(), true).'.'.$type;

            if($size > $maxsize) {           

              $status .= "$path File too large \\r\\n";
            
            } 

            if ( !in_array($type,$extentions) ) { 

              $status .= "$path File contains wrong extension \\r\\n";
            
            } else {
                
                if (copy($_FILES['file_cv']['tmp_name'], $new_path)) {

                    $picture1 = $new_path;

                } else {

                    $status = "Sorry, try again!\\r\\n";
                }
            }
        }

        if (!empty($_FILES['file_cover_letter']['tmp_name'])) {
            $path2 = $_FILES['file_cover_letter']['name'];

            ini_set('memory_limit', '32M'); 
            $maxsize2 = "10000000"; //10mb
            $extentions2 = array("gif","doc","docx","xls");
            $size2 = filesize($_FILES['file_cover_letter']['tmp_name']); 
            $type2 = strtolower(substr($path2, 1+strrpos($path2,".")));
            $new_path2 = 'file-'.uniqid(rand(), true).'.'.$type2;

            if($size2 > $maxsize2) {           

              $status .= "File too large \\r\\n";
            
            } 

            if ( !in_array($type2,$extentions2) ) { 

              $status .= "File contains wrong extension \\r\\n";
            
            } else {
                
                if (copy($_FILES['file_cover_letter']['tmp_name'], $new_path2)) {

                    $picture2 = $new_path2;

                } else {

                    $status = "Sorry, try again!\\r\\n";
                }
            }
        }

        if ( !empty($status) ) {

            echo '<script type="text/javascript">alert("You can\'t upload!\\r\\n'.$status.'"); window.history.back(); </script>';
          
        } elseif ( empty($picture1) && empty($picture2) ) {

            $headers .= "Content-Type: ".$content."; charset=".$charset."\r\n";

            if (mail($mailto,$subject,$sendmessage,$headers)) {

                echo '<script type="text/javascript">alert("Message sent! We will contact you soon."); window.location.href = "/recruitment.php";</script>';   

            } else {       

                echo '<script type="text/javascript">alert("Error! Try again."); window.history.back();</script>';
            
            }

        } else {

           $boundary = "--".md5(uniqid(time()));
           $headers .="Content-Type: multipart/mixed; boundary=\"".$boundary."\"\n";
           $multipart .= "--".$boundary."\n";
           $multipart .= "Content-Type: text/html; charset=$charset\n";
           $multipart .= "Content-Transfer-Encoding: Quot-Printed\n\n";
           $multipart .= "$sendmessage\n\n";
           if (!empty($new_path)) {
              $fp = fopen($new_path,"r");
              $file1 = fread($fp, filesize($new_path));
              fclose($fp);
              $message_part = "--".$boundary."\n";
              $message_part .= "Content-Type: application/octet-stream\n";
              $message_part .= "Content-Transfer-Encoding: base64\n";
              $message_part .= "Content-Disposition: attachment; filename = \"".$picture1."\"\r\n";       
              $message_part .= chunk_split(base64_encode($file1))."\r\n";
           }

           if (!empty($new_path2)) {
               $fp2 = fopen($new_path2,"r");
               $file2 = fread($fp2, filesize($new_path2));
               fclose($fp2);
               $message_part .= "--".$boundary."\n";
               $message_part .= "Content-Type: application/octet-stream\n";
               $message_part .= "Content-Transfer-Encoding: base64\n";       
               $message_part .= "Content-Disposition: attachment; filename = \"".$picture2."\"\r\n";
               $message_part .= chunk_split(base64_encode($file2))."\r\n";
           }

           
           $multipart .= $message_part."--".$boundary."--\n";

           if(mail($mailto, $subject, $multipart, $headers)) { 
                echo '<script type="text/javascript">alert("Message sent! We will contact you soon."); window.location.href = "/recruitment.php";</script>';
              
            } else {
                echo '<script type="text/javascript">alert("Error! Try again."); window.history.back();</script>';
            }

        }
    }
} 

?>