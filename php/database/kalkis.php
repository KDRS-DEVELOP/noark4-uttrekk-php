if (($_REQUEST['tall1']=="")or($_REQUEST['tall2']==""))
	{
		echo "Du må fylle ut begge tallfeltene!";
	}
    else
	{
		if(isset($_REQUEST['sum']))
		{
			$sum = $_REQUEST['tall1'] + $_REQUEST['tall2'];
			echo "Summen av tallene er $sum";
		}
		elseif(isset($_REQUEST['diff']))
	{}
		elseif(isset($_REQUEST['produkt']))
	{}
		elseif(isset($_REQUEST['kvotient']))
	{}
	}