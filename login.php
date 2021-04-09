<?php

//ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

include 'connect/connect-new.php';
require('inc/prefix.php');

//require('tpl/header.php');
if(isset($_SESSION['logged'])){
	echo '<script>window.parent.location.href="index"</script>';exit;
}
require_once("classes/models/Members.php");
$members  = new Members();

$CompanyID = $company->CompanyID;
$_SESSION['CompanyID'] = $CompanyID;

//Login Google
require_once('lib/Google/autoload.php');
require_once('inc/google.php');
$googleClient 	= new Google_Client;
$auth 			= new GoogleAuth($googleClient);
$oauth 			= new Google_Service_Oauth2($googleClient);

if($auth->checkRedirectCode())
{
	$userinfo = $oauth->userinfo;
	$google_email = $userinfo->get()->email;
	$google_fname = $userinfo->get()->givenName;
	$google_lname = $userinfo->get()->familyName;

	$super_admin_query = $db->prepare("SELECT `EmailAddress` FROM `super_admin` WHERE `EmailAddress` = :email AND `Inactiv` = '0' ");
	$super_admin_query->bindParam(':email', $google_email, PDO::PARAM_STR);
	$super_admin_query->execute();
	if($super_admin_query->rowCount() == 1)
	{
		//die("super_admin1");
		$google_email = $google_email;
	}
	else
	{
		$com_admin_query = $db->prepare("SELECT `EmailAddress` FROM `company_admins` WHERE `EmailAddress` = :email AND `Inactiv` = '0' AND `CompanyID` = :CompanyID");
		$com_admin_query->bindParam(':email', $google_email, PDO::PARAM_STR);
		$com_admin_query->bindParam(":CompanyID",$CompanyID);
		$com_admin_query->execute();
		if($com_admin_query->rowCount() == 1)
		{
			//die("company_admins1");
			$google_email = $google_email;
		}
		else
		{
			$query = $db->prepare("SELECT `EmailAddress` FROM `users` WHERE `EmailAddress` = :email AND `Inactiv` = '0' AND `CompanyID` = :CompanyID");
			$query->bindParam(':email', $google_email, PDO::PARAM_STR);	
			$query->bindParam(":CompanyID",$CompanyID);
			$query->execute();
			if($query->rowCount() == 1)
			{
				//die("users1");
				$google_email = $google_email;
			}
			else
			{
				$user = $members->AddNewUser($CompanyID,$google_fname,$google_lname,$google_email,"");
				$google_email = $google_email;
			}
		}
	}
}

// Add User
if(isset($_POST['add_user']))
{
	$query = $db->prepare("SELECT EmailAddress FROM `users` WHERE EmailAddress = :email AND Inactiv = 0 AND CompanyID =:CompanyID");
	$query->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
	$query->bindParam(":CompanyID",$CompanyID);
	$query->execute();
	if($query->rowCount() == 1)
	{
		$normal_email = "";
		$_SESSION['message-login'] = array("danger", "Error:", "Your email already exist. Please log in");
		echo '<script>window.location.href="/login"</script>';exit;	
	}
	else
	{
		$user = $members->AddNewUser($CompanyID,"","",$_POST["email"],$_POST['password']);
		$normal_email = $_POST['email'];
	}
}

//Normal login
if(isset($_POST["login"]))
{
	$super_admin_query = $db->prepare("SELECT `EmailAddress` FROM `super_admin` WHERE `EmailAddress` = :email AND Password = :password AND Inactiv = 0 ");
	$super_admin_query->bindParam(":email",$_POST["login_email"]);
	$super_admin_query->bindParam(":password",$_POST["login_password"]);
	$super_admin_query->execute();
	$super_admin_row = $super_admin_query->fetch(PDO::FETCH_OBJ);
	if(isset($super_admin_row->EmailAddress))
	{ 	//die("super_admin2");
		$normal_email = $super_admin_row->EmailAddress;
	}
	else
	{
		$com_admin_query = $db->prepare("SELECT `EmailAddress` FROM `company_admins` WHERE `EmailAddress` = :email AND `Password` = :password AND `Inactiv` = 0 AND `CompanyID` = :CompanyID ");
		$com_admin_query->bindParam(":email",$_POST["login_email"]);
		$com_admin_query->bindParam(":password",$_POST["login_password"]);
		$com_admin_query->bindParam(":CompanyID",$CompanyID);
		$com_admin_query->execute();
		$com_admin_row = $com_admin_query->fetch(PDO::FETCH_OBJ);
		if(isset($com_admin_row->EmailAddress))
		{	//die("company_admins2");
			$normal_email = $com_admin_row->EmailAddress;
		}
		else
		{
			$login = $db->prepare("SELECT `EmailAddress` FROM `users` WHERE (`EmailAddress` = :email AND `Password` = :password) OR (`EmailAddress` = :email AND `Password` = md5(:password)) AND `Inactiv` = 0 AND `CompanyID` = :CompanyID ");
			$login->bindParam(":email",$_POST["login_email"]);
			$login->bindParam(":password",$_POST["login_password"]);
			$login->bindParam(":CompanyID",$CompanyID);
			$login->execute();
			$login_row = $login->fetch(PDO::FETCH_OBJ);
			if(isset($login_row->EmailAddress))
			{ 	//die("users2");
				$normal_email = $login_row->EmailAddress;
			}
			else
			{
				//die("message-login");
				//$normal_email = "";
				$_SESSION['message-login'] = array("danger", "Error:", "Please try again.");
				$_SESSION['message-login-xs'] = array("danger", "Error:", "Incorrect Email Address. Please try again.");
				echo '<script>window.location.href="/login"</script>';exit;
			}
		}
	}
}

if(isset($google_email))
{
	$email = $google_email;
}

if(isset($facebook_email))
{
	$email = $facebook_email;
}

if(isset($normal_email))
{
	$email = $normal_email;
}

if(isset($email))
{
	$super_admin_query = $db->prepare("SELECT * FROM `super_admin` WHERE `EmailAddress` = '$email' AND `Inactiv` = '0' ");
	$super_admin_query->execute();
	if($super_admin_query->rowCount() == 1)
	{	
		//die("super_admin3");
		$admin_row = $super_admin_query->fetch(PDO::FETCH_OBJ);
		$_SESSION['id']          	= $admin_row->AdminID;
		$_SESSION['rank']        	= $admin_row->Rank;
		$_SESSION['fname']  		= $admin_row->FirstName;
		$_SESSION['lname']   		= $admin_row->LastName;
		$_SESSION['departament']	= $admin_row->Departament;
		$_SESSION['isTraining']  	= $admin_row->isTraining;
		$_SESSION['authorized']  	= $admin_row->Authorized;
		$_SESSION['privilege']  	= $admin_row->Privilege;
		$_SESSION['CompanyID']  	= $CompanyID;
        $_SESSION['email']  	  	= $email;
        $_SESSION['employee']  	  	= "";
        $_SESSION['type']  	  		= "";
		$_SESSION['branch'] 	  	= "";
		$_SESSION['super_admin'] 	= "super_admin";
		$_SESSION['logged'] 	  	= "1";
		
		if(isset($_SESSION['logged']) && $_SESSION['logged'] == "1" )
		{
			if(isset($_SESSION['url']))
			{
				$url = $_SESSION['url'];
			}
			else
			{
				$url = "index";
			}
			echo '<script>window.location.href="'.$url.'"</script>';exit;
		}
	}
	else
	{
		$com_admin_query = $db->prepare("SELECT * FROM `company_admins` WHERE `EmailAddress` = '$email' AND `Inactiv` = '0' AND `CompanyID` = '$CompanyID' ");
		$com_admin_query->execute();
		if($com_admin_query->rowCount() == 1)
		{		
			//die("company_admins3");
			$com_row = $com_admin_query->fetch(PDO::FETCH_OBJ);
			$_SESSION['id']          	= $com_row->CompanyAdminID;
			$_SESSION['rank']        	= $com_row->Rank;
			$_SESSION['fname']  		= $com_row->FirstName;
			$_SESSION['lname']   		= $com_row->LastName;
			$_SESSION['departament']	= $com_row->Departament;
			$_SESSION['isTraining']  	= $com_row->isTraining;
			$_SESSION['authorized']  	= $com_row->Authorized;
	        $_SESSION['privilege']  	= $com_row->Privilege;
	        $_SESSION['CompanyID']  	= $com_row->CompanyID;
	        $_SESSION['email']  	  	= $email;
	        $_SESSION['type']  	  		= "";
		    $_SESSION['employee']  	  	= "";
			$_SESSION['branch'] 	  	= "";
			$_SESSION['super_admin'] 	= "";
			$_SESSION['company_admin'] 	= "company_admin";
			$_SESSION['logged'] 	  	= "1";
			
			if(isset($_SESSION['logged']) && $_SESSION['logged'] == "1" )
			{
				if(isset($_SESSION['url']))
				{
					$url = $_SESSION['url']; // holds url for last page visited.
				}
				else
				{
					$url = "index";
				}
				echo '<script>window.location.href="'.$url.'"</script>'; exit;
			}
		}
		else
		{
			$query = $db->prepare("SELECT * FROM `users` WHERE `EmailAddress` = '$email' AND `Inactiv` = '0' AND `CompanyID` = '$CompanyID' ");
			//echo "SELECT * FROM `users` WHERE `EmailAddress` = '$email' AND `Inactiv` = '0' AND `CompanyID` = '$CompanyID'"; exit;
			$query->execute();
			if($query->rowCount() == 1)
			{	
				//die("user3");
				$row = $query->fetch(PDO::FETCH_OBJ);
				$_SESSION['id']			  = $row->UserID;
				$_SESSION['member_id']	  = $row->EmployeeID;
				$_SESSION['employee']  	  = $row->EmployeeID;
				$_SESSION['email']  	  = $email;
				$_SESSION['branch'] 	  = $row->Branch;
				$_SESSION['rank']   	  = $row->Rank;
				$_SESSION['fname']  	  = $row->FirstName;
				$_SESSION['lname']  	  = $row->LastName;
				$_SESSION['type']   	  = $row->Type;
				$_SESSION['departament']  = $row->Departament;
				$_SESSION['isTraining']   = $row->isTraining;
				$_SESSION['authorized']   = $row->Authorized;
		        $_SESSION['privilege']    = $row->Privilege;
				$_SESSION['logged'] 	  = 1;
				$_SESSION['CompanyID'] 	  = $row->CompanyID;

				if(isset($_SESSION['logged']) && ($_SESSION['logged'] == "1"))
				{
					if(isset($_SESSION['url'])) {
						$url = $_SESSION['url'];
					} else {
						$filled = $members->getFilledOut($email);
						if($filled == 1) {
							$url = "index";
						} else {
							if(($CompanyID == 8) || ($row->CompanyID == 8) || ($link == 'rah.adamcex.co.uk') || ($link == 'rah.adambrands.co.uk')){
								$url = "application-form";
							} else {
								$url = "register";
							}
					    }
					}
					echo '<script>window.location.href="'.$url.'"</script>';exit;
				}
			}
			else
			{
				$_SESSION['message-login'] = array("danger", "Error:", "Please try again.");
				$_SESSION['message-login-xs'] = array("danger", "Error:", "Incorrect Email Address. Please try again.");
				echo '<script>window.location.href="/login"</script>';exit;
			}
		}
	}
}

if(isset($_GET["mode"]) && $_GET["mode"] == "register") {
	$tab1 = "nav-ui-unselected"; $a_tab1 = "ui-unsel";       $line1 ="line-unsel"; $display1 = "style=\"display: none;\"";
	$tab2 = "nav-ui-selected";   $a_tab2 = "tab-ui-chevron"; $line2 ="line";       $display2 = "style=\"display: block;\"";
} else {
	$tab1 = "nav-ui-selected"; $a_tab1 = "tab-ui-chevron";       $line1 ="line"; $display1 = "style=\"display: block;\"";
	$tab2 = "nav-ui-unselected";   $a_tab2 = "ui-unsel"; $line2 ="line-unsel";       $display2 = "style=\"display: none;\"";
}

//Set colors for Companies
$color = $comp->getCompanyColor($CompanyID);

?>
<div class="login-page">
	<div class="alert alert-warning text-center" style="z-index:10;display: none;">
		<b>*** Before you start the registration form please have your NI Number and address history of 5 years ready, including month/year for each address ***</b>
	</div>
	<div class="login-form">
		<div>
			<div>
				<img style="max-width:150px;margin-bottom: 10px;" src="<?php echo $logo->Path . "/" . $logo->Logo ?>" alt="Logo">
				<h1 style= "border: 1px solid <?php echo $color->LoginHeaderColor; ?>;background-color: <?php echo $color->LoginHeaderColor; ?>;">Employee Portal</h1>
				<?php if(isset($_SESSION['message-login'])) { echo "
				<div class=\"alert alert-{$_SESSION['message-login'][0]} alert-dismissible\" role=\"alert\" style=\"margin: 0 0 0 0;padding-top: 3px;padding-bottom: 3px;\">
					<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\" style=\"top: 0;margin-top: 0;right: 0\"><span aria-hidden=\"true\">&times;</span></button>
					<strong>{$_SESSION['message-login'][1]} </strong> {$_SESSION['message-login'][2]} 
				</div>"; } unset($_SESSION['message-login']);?>
				<div class="panel-body text-center" id="login_TB">
					<a id="login_TButton" class="btn btn-block btn-xl btn-primary hover-shadow">LOGIN</a>
					<div class="col-xs-12 ui-tabs-panel" id="login_TContent">
						<form method="post" id="form_login" autocomplete="off" class="form-group">
							<input type="email" id="login_email" class="form-control margin-bottom-1x" name="login_email" required placeholder="Email Address" autocomplete="off" onblur="fieldComplete('login_email', 'login_email_err', 0, 0, 0,'done_e','fail_e')">
							<input type="password" id="login_password" class="form-control margin-bottom-1x" name="login_password" required placeholder="Password" autocomplete="off" onblur="fieldComplete('login_password', 'login_password_err', 0, 0, 0,'done_p','fail_p')">
							<div class="row">
								<div class="col-xs-6">
									<button type="submit" class="btn btn-success btn-sm hover-shadow btn-block" id="login" name="login" title="Login" style= "border-color: <?php echo $color->LoginHeaderColor; ?>;background-color: <?php echo $color->LoginHeaderColor; ?>;">SUBMIT</button>
								</div>
								<div class="col-xs-6">
									<a class="btn btn-block btn-xl btn-primary hover-shadow google-btn" href="<?php echo $auth->getAuthUrl(); ?>"><img width="17px" src="img/google.svg"> Sign In</a>
								</div>
							</div>
						</form>
					</div>
					<div class="clear-both margin-top-1x"></div>
					<a id="register_TButton" class="btn btn-block btn-xl btn-primary hover-shadow">REGISTER</a>
					<div class="col-xs-12 ui-tabs-panel" id="register_TContent">
						<form method="post" id="form_register" autocomplete="off" onsubmit="return checkRegister();" class="form-group">
							<input type="email" required id="email" class="form-control margin-bottom-1x" name="email" placeholder="Email Address*" onblur="checkEmail('email', 'email_err','email_err2', 0,'done_e','fail_e',1)">
							<div class="pop-up-info" id="email_err">
								<div class="qtip-tip">
									<img alt="" src="img/canvas.png"/>
								</div>
								<span>Invalid field format!</span>
								<div class="clear-both"></div>
							</div>
							<div class="pop-up-info" id="email_err2">
								<div class="qtip-tip">
									<img alt="" src="img/canvas.png"/>
								</div>
								<span>Duplicate email detected!</span>
								<div class="clear-both"></div>
							</div>
							<div class="clear-both margin-top-1x"></div>
							<input type="password" required id="password" class="form-control margin-bottom-1x" name="password" placeholder="Password*" onblur="PassField('password','password_err', 0, 3, 0,'done_pass','fail_pass')">
							<div class="pop-up-info" id="password_err">
								<div class="qtip-tip">
									<img alt="" src="img/canvas.png"/>
								</div>
								<span>At least 3 characters!</span>
								<div class="clear-both"></div>
							</div>
							<div class="clear-both margin-top-1x"></div>
							<input type="password" required id="repass" class="form-control margin-bottom-1x" name="repass" placeholder="Repeat Password*" onblur="RePassField('password','repass','repass_err', 0,3,'done_pass','done_rpass','fail_pass','fail_rpass')">
							<div class="pop-up-info" id="repass_err">
								<div class="qtip-tip">
									<img alt="" src="img/canvas.png"/>
								</div>
								<span>Passwords do not match!</span></br style="clear: both;">
							</div>
							<div class="row">
								<div class="col-xs-6">
									<button type="submit" class="btn btn-success btn-sm hover-shadow btn-block" id="add_user" name="add_user" title="Submit" style= "border-color: <?php echo $color->LoginHeaderColor; ?>;background-color: <?php echo $color->LoginHeaderColor; ?>;">SUBMIT</button>
								</div>
								<div class="col-xs-6">
									<a class="btn btn-block btn-xl btn-primary hover-shadow google-btn" href="<?php echo $auth->getAuthUrl(); ?>"><img width="17px" src="img/google.svg"> Sign Up</a>
								</div>
							</div>
						</form>
					</div>
					<div class="col-xs-12 padding-top-1x">
						<a href="forgot_password" class="forgot_password_text hvr-grow">Forgot your password? Reset it here.</a>
					</div>
				</div>
				<div class="panel-body text-center margin-top-1x app-links-logos">
					<a href="https://play.google.com/store/apps/details?id=com.cuttingedge.adambrandsemployeelogin" target="_blank"><img src="img/logo/google-play-store.png"></a>
					<a href="https://apps.apple.com/app/id1470221489" target="_blank"><img src="img/logo/apple-app-store.png"></a>
				</div>
			</div>
		</div>
	</div>
</div>
<script> 
	$(document).ready(function(){
		$("#login_TButton").click(function(){
			$("#login_TContent").slideToggle("slow");
			$("#register_TContent").hide("slow");
			$(".alert-warning").hide("slow");
		});

		$("#register_TButton").click(function(){
			$(".alert-warning").slideToggle("slow");
			$("#register_TContent").slideToggle("slow");
			$("#login_TContent").hide("slow");
		});
	});
</script>
<?php require('inc/footer.php'); ?>