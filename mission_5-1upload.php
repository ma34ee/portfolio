<html>
<title>M.Tの部屋</title>
<head><h1>その日達成したことを書き込もう</h1></head>
<h2>習慣化、モチベーション維持に役立ててください</h2>
<?php
//データベースに接続
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//テーブルを作成する
$sql = "CREATE TABLE IF NOT EXISTS mysql3"
	."("
	."id INT AUTO_INCREMENT PRIMARY KEY,"
	."name char(32),"
	."comment TEXT,"
	."postdate TEXT,"
	."password TEXT"
	.");";
$stmt = $pdo->query($sql);//sqlの中身を実行

$name= @$_POST["name"];
$comment= @$_POST["comment"];
$delete= @$_POST["delete"];
$del_id= 0;//削除で用いるID 0を代入しておく
$edit= @$_POST["edit"];
$hidebefore= @$_POST["hidebefore"];
$pass1= @$_POST["pass1"];//コメントパスワード欄に入力されるパスワード
$pass2= @$_POST["pass2"];//削除パスワード欄に入力されるパスワード
$pass3= @$_POST["pass3"];//編集パスワード欄に入力されるパスワード
$datetime= date("Y/m/d H:i:s");//投稿した日付を変数に代入


//名前とコメントが入力されている時に実行	
if(!empty($name & $comment) && isset($_POST["sub_button"])){
	
	//パスワードが入力されているとき
	if(!empty($pass1)){
//①入力フォーム
//編集モードじゃない時=新規送信の場合
	if(empty($hidebefore)){
//作成したテーブルに、insertを行なってデータを入力する
	$sql = $pdo -> prepare("INSERT INTO mysql3 (name, comment, postdate, password) VALUES (:name, :comment, :postdate, :password)");
	$sql -> bindParam(':name', $name2, PDO::PARAM_STR);
	$sql -> bindParam(':comment', $comment2, PDO::PARAM_STR);
	$sql -> bindParam(':postdate', $postdate2, PDO::PARAM_STR);
	$sql -> bindParam(':password', $password2, PDO::PARAM_STR);
	$name2 = $name;
	$comment2 = $comment;
	$postdate2 = $datetime;
	$password2 =$pass1;//入力したパスワードを変数に代入
	$sql->execute();//$sqlの内容を実行
	}//新規送信モード処理終了地点

//編集モード
//パスワードが入力されているときに実行
elseif(!empty($hidebefore) && !empty($pass1)){
	$id = $hidebefore; //編集する投稿番号
	$name2=$name;
	$comment2=$comment;
	$postdate2 = $datetime;
	$password3=$pass1;
	
	$sql = 'update mysql3 set name=:name,comment=:comment,postdate=:postdate,password=:password where id=:id';
	$stmt = $pdo -> prepare($sql);
	$stmt -> bindParam(':name',$name2,PDO::PARAM_STR);
	$stmt -> bindParam(':comment',$comment2,PDO::PARAM_STR);
	$stmt -> bindParam(':postdate',$postdate2,PDO::PARAM_STR);
	$stmt -> bindParam(':password',$password3,PDO::PARAM_STR);
	$stmt -> bindParam(':id',$id,PDO::PARAM_INT);
	$stmt -> execute();
}
}//編集モード処理終了地点
//名前とコメントのみ入力　パスワードが入力されていない場合
else{
echo "パスワードを入力してください<br>";
}
}//入力フォーム処理終了地点

//②削除フォーム
//削除番号が空でない場合
	if(!empty($delete)){
		//パスワードが空でない場合
		if(!empty($pass2)){
			$sql="SELECT * from mysql3";
			$stmt=$pdo->query($sql);
			$results=$stmt->fetchAll();
			foreach($results as $row){
				//入力した削除番号とパスワードが、DB上のデータと一致したら実行
				if($delete == $row["id"] && $pass2 == $row["password"]){
				//削除番号を変数に代入
				$del_id=$row["id"];
				}
				//パスワードが一致しない時に実行
				elseif($delete == $row["id"] && $pass2 != $row["password"]){
				echo "パスワードが正しくありません<br>";
				}
			}//ループ終了地点		
		//入力したデータをdeleteによって削除する。できているかはselectによって確認
		$sql = 'delete from mysql3 where id=:id';
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		$id = $del_id;//削除する投稿番号
		$stmt->execute();
		}
		//パスワードが空の時に実行
		else{
		echo "パスワードを入力してください<br>";
		}
	}//削除フォーム処理終了地点

//③編集フォーム
//編集番号が空でなく、編集ボタンが押された時のみ実行
if(!empty($edit) && isset($_POST["edi_button"])){
	//パスワードが空でない時実行
	if(!empty($pass3)){
		$sql="SELECT * from mysql3";
		$stmt=$pdo->query($sql);
		$results=$stmt->fetchAll();
		foreach($results as $row){
			//入力した編集番号とパスワードが、DB上のデータと一致したら実行
			if($edit == $row["id"] && $pass3 == $row["password"]){
				$e_name=$row["name"];
				$e_comment=$row["comment"];
				$e_password=$row["password"];
				//変数の中身は送信フォームに表示される
			}
			//パスワードが一致しない場合
			elseif($edit == $row["id"] && $pass3 != $row["password"]){
				echo "パスワードが正しくありません<br>";
				$e_name="";
				$e_comment="";
				$e_password="";
			}
		}//ループ終了地点
	}
	//パスワードが入力されていない時実行
	else{
		echo "パスワードを入力してください<br>";
		$e_name="";
		$e_comment="";
		$e_password="";
	}
}//編集フォーム処理終了地点
?>

<form action="" method="post">
		<p>
		<div>
		<h3>入力フォーム</h3>
		<!--名前入力フォーム-->
		<input type="text" name="name" placeholder="名前" 
		value="<?php if(!empty($_POST["edit"]) && isset($_POST["edi_button"])){echo $e_name;}?>"><br>
		
		<!--コメント入力フォーム-->
		<input type="text" name="comment" placeholder="コメント" 
		value="<?php if(!empty($_POST["edit"]) && isset($_POST["edi_button"])){echo $e_comment;} ?>"><br>
		<input type="hidden" name="hidebefore" value="<?php if(!empty($_POST["edit"]) && isset($_POST["edi_button"])){echo $edit;} ?>">
		<!--コメントパスワードフォーム-->
		<input type="text" name="pass1" placeholder="パスワード" value="<?php if(!empty($_POST["edit"])&&isset($_POST["edi_button"])){echo $e_password;}?>"> 
		<input type="submit" name="sub_button" value="送信">
		</div>
		</p>
		
		<p>
		<h3>削除フォーム</h3>
		<!--削除番号指定フォーム-->
		<div>
		<input type="text" name="delete" placeholder="削除対象番号"><br>
		<!--削除パスワードフォーム-->
		<input type="text" name="pass2" placeholder="パスワード">
		<input type="submit" name="del_button"value="削除">
		</div>
		</p>
		
		<p>
		<h3>編集フォーム</h3>
		<!--編集番号指定フォーム-->
		<div>
		<input type="text" name="edit" placeholder="編集対象番号"><br>
		<!--編集パスワードフォーム-->
		<input type="text" name="pass3" placeholder="パスワード">
		<input type="submit" name="edi_button" value="編集">
		</div>
		</p>
	</form>
</html>

<?php
//入力したデータをselectによって表示する
$sql = 'SELECT * FROM mysql3';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['postdate'].'<br>';
		echo "<hr>";
	}
?>