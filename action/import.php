<?php

$appID="EINV2201711304894";
$DBNAME = "h_f";
$DBUSER = "root";
$DBPASSWD = "00000000";
$DBHOST = "localhost";
$memberID="1207496436018151";
$mysqli = new Mysqli($DBHOST, $DBUSER, $DBPASSWD, $DBNAME);
	if ($mysqli->connect_errno) {
		printf("Connect failed: %s\n", $mysqli->connect_error);
	 exit();
}
//$sql  = 'INSERT INTO `e_invoice_invnum` (`invID`, `MemberID`, `invNum`, `invYear`, `invMonth`, `invDate`, `sellerName`, `sellerAddress`) VALUES (NULL, \'1\', \'1\', \'1\', \'1\', \'1\', \'1\', \'1\')';

$sql  = "SELECT *  FROM `e_invoice_carrier` WHERE `MemberID` = '$memberID'";
$result = $mysqli->query($sql);
$row = $result->fetch_array(MYSQLI_NUM);
//$memberID=$row[0];
$cardNo=$row[1];
$cardEncrypt=$row[2];
$startDate="2017/11/01";//讀取開始月份
$endDate="2017/12/31";//結束月份
$uuid="123456";//手機的UUID碼
$e_api="https://api.einvoice.nat.gov.tw/";
$api=$e_api."/PB2CAPIVAN/invServ/InvServ?version=0.3&cardType=3J0002&cardNo=".$cardNo."&expTimeStamp=2147483647&action=carrierInvChk&timeStamp=2147483647&startDate=".$startDate."&endDate=".$endDate."&onlyWinningInv=N&uuid=".$uuid."&appID=".$appID."&cardEncrypt=".$cardEncrypt;
//載入API
$handle = fopen($api,"rb");

while (!feof($handle)) 
	$content .= fread($handle, 100000);

fclose($handle);
$data=json_decode($content);

foreach($data->{'details'} as $k)
{
	$invNum=$k->{'invNum'};
	$year=$k->{'invDate'}->{'year'}+1911;
	$date=$k->{'invDate'}->{'date'};
	if((int)$date<10)
		$date="0".$date;
	$month=$k->{'invDate'}->{'month'};
	if((int)$month<10)
		$month="0".$month;
	
	$sellerAddress=$k->{'sellerAddress'};
	$sellerName=$k->{'sellerName'};
	echo "發票編號".$k->{'invNum'}." ";
	$YMD=$year."/".$month."/".$date;
	echo ",日期".$YMD;
	echo ",販賣人:".$k->{'sellerName'};
	echo ",販賣人地址:".$k->{'sellerAddress'};
	$invID=$invNum.$year.$month.$date;
	//echo "ID:".$invID;
	$sql  = "INSERT INTO `e_invoice_invnum` (`invoicID`,`invNum`,`MemberID`,`invYear`, `invMonth`, `invDate`, `sellerName`, `sellerAddress`) VALUES ('$invID','$invNum','$memberID', '$year','$month','$date','$sellerName','$sellerAddress')";
	if($mysqli->query($sql))
		echo "<br>";
	else
		echo "<br>";
	$api=$e_api."PB2CAPIVAN/invServ/InvServ?version=0.3&cardType=3J0002&cardNo=".$cardNo."&expTimeStamp=2147483647&action=carrierInvDetail&timeStamp=2147483647&invNum=".$invNum."&invDate=".$YMD."&uuid=".$uuid."&appID=EINV2201711304894&cardEncrypt=".$cardEncrypt;
	$handle= fopen($api,"rb");
	//重製content2
	$content2="";
	while (!feof($handle)) {
		$content2 .= fread($handle, 100000);
	}
	fclose($handle);
	$p_data=json_decode($content2);
	//var_dump($p_data);
	//echo "<br>"."發票名稱:".$p_data->{'invNum'}."<br>";
	$array=$p_data->{'details'};
	$i=1;
	foreach($array as $k)
	{
		$ProductUnitPrice=$k->{'unitPrice'};
		if((int)$ProductUnitPrice>0)
		{
			//echo "編號".$p_data->{'invNum'}."_".$i.",";
			$detailsID=$p_data->{'invNum'}."_".$i;
			echo "商品(".$i.")名稱:".$k->{'description'}.",";
			$ProductName=$k->{'description'};
			
			echo "商品單價:".$k->{'unitPrice'}.",";
			echo "購買數量:".$k->{'quantity'}."<br>";
			$quantity=$k->{'quantity'};
			//匯入資料庫
			$sql  = "INSERT INTO `e_invoice_product_detail`(`detailsID`,`invoiceID`,`invNUM`, `ProductName`, `ProductUnitPrice`,`quantity`) VALUES ('$detailsID' , '$invID' , '$invNum' , '$ProductName' , '$ProductUnitPrice' , 'quantity')";
			if($mysqli->query($sql))
				echo "<br>";
			else
				echo "<br>";
			$i++;
		}
		
		
	}
	echo "<br>";
}


	

?>