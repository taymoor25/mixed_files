<?php

include '../connect/connect-new.php';
if(session_id() == '' || !isset($_SESSION)) {
  session_start();
}
require('../inc/d-prefix.php');

if(!isset($_SESSION['logged-dash'])) {
  echo '<script>window.location.href="/dashboard/login-dash"</script>';exit; 
}
require_once("../classes/models/AdminCompanies.php");
$comp = new AdminCompanies("../inc/func.php");

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

function getRestrictedSections()
{
  global $db;
  $query = "SELECT * FROM `restricted_sections` ";
  $result = $db->query($query);
  $rows = $result->fetchAll(PDO::FETCH_OBJ);
  return $rows;
}

function getPosSettings($restricted_sections_ID)
{
  global $db;
  $query = "SELECT * FROM `position_sections` WHERE `Position` = '".$_GET['Position']."' AND `restricted_sections_ID` = '$restricted_sections_ID' ";
  $result = $db->query($query);
  $rows = $result->fetchAll(PDO::FETCH_OBJ);
  return $rows;
}

function getPositions()
{
  global $db;
  $query = "SELECT `PositionID`,`Position` FROM `positions` ";
  $result = $db->query($query);
  $rows = $result->fetchAll(PDO::FETCH_OBJ);
  return $rows;
}

if(isset($_POST["addSection"]))
{
  global $db;
  $restricted_sections_query = "INSERT INTO `restricted_sections`(`section`,`sectionURL`) VALUES('".$_POST['secName']."', '".$_POST['securl']."')";
  $restricted_sections_stmt = $db->prepare($restricted_sections_query);
  $restricted_sections_stmt->execute();
  $restricted_sections_ID = $db->lastInsertId();

  if (!empty($restricted_sections_ID))
  {
    $query = "INSERT INTO position_sections(`restricted_sections_ID`,`Position`) VALUES('$restricted_sections_ID', '".$_GET['Position']."')";
    $stmt = $db->prepare($query);
    $stmt->execute();
  }
}

?>

<div id="wrapper">
<style>
  [type="checkbox"]:not(:checked), [type="checkbox"]:checked {
    position: absolute;
    left: -9999px;
  }
  .loader {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999999;
    background: rgb(249,249,249);
    opacity: 0.8;
  }
</style>
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <?php include('inc/header.php'); include('inc/sidebar.php'); ?>
    </nav>
    <div id="loading" class="loader" style="display:none;"><img src="../img/loader.GIF" width="120" style="top: 45%;left:45%;position: absolute;"/></div>
    <div id="page-wrapper">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <h1 class="page-header" style="margin: 70px 0 20px;">
              Settings <?php if(isset($_GET["Position"])) { echo"- ".$_GET["Position"];} ?>
              <button data-toggle="modal" data-target="#myModal" class="btn btn-success pull-right"><i class="fa fa-plus" aria-hidden="true"></i> Add New Section</button>
              <?php $getPositions = getPositions(); ?>
              <div class="pull-right">
                <?php 
                  echo"
                  <div class=\"btn-group col-lg-3\">
                    <button type=\"button\" class=\"btn btn-default dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
                      {$_GET["Position"]}<span class=\"caret\"></span>
                    </button>
                    <ul class=\"dropdown-menu\" style=\"margin:0;border: 1px solid #ccc;\">
                      <li>
                        <a href=\"position_sections?Position=Admin\" style=\"padding: 0 5px 0 0;\"><input type=\"checkbox\" id=\"position_Admin\" value=\"Admin\" /><label for=\"positionAdmin\">Admin</a>
                      </li>
                      <script>
                        var selected1 = '$_GET[Position]';
                        var val1 = $('#position_Admin').val();
                        if(selected1 == val1)
                        {
                          $('#position_Admin').attr('checked', true);
                        }				
                      </script>";

                      foreach($getPositions as $position)
                      {
                        $link = "position_sections?Position=$position->Position";
                        echo "
                          <li>
                            <a href=\"$link\" style=\"padding: 0 5px 0 0;\"><input type=\"checkbox\" id=\"position_{$position->PositionID}\" value=\"{$position->Position}\" /><label for=\"position{$position->PositionID}\">$position->Position </a>
                          </li>
                          <script>
                            var selected1 = '$_GET[Position]';
                            var val1 = $('#position_{$position->PositionID}').val();
                            if(selected1 == val1)
                            {
                              $('#position_{$position->PositionID}').attr('checked', true);
                            }				
                         </script>";
                      }
                    echo"
                    </ul>
                  </div>";
                ?>
              </div>
            </h1>
          </div>
        </div>
        <?php
            if(isset($_SESSION['message'])) { echo "
              <div class=\"row\">  
                <div class=\"col-lg-12 col-sm-12 col-xs-12\" style=\"\">
                  <div class=\"alert alert-{$_SESSION['message'][0]} alert-dismissible\" role=\"alert\" style=\"margin: 0px 0 10px 0;\">
                      <button type=\"button\" style=\"margin-top: 2px;right: 0px;\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
                      <strong>{$_SESSION['message'][1]} </strong> {$_SESSION['message'][2]} 
                  </div>
                </div>
              </div>
            "; }
            unset($_SESSION['message']);
          ?>
          <div class="row">
            <div class="col-lg-12 sectionSettings">
              <?php $getRestrictedSections = getRestrictedSections(); ?>
              <div class="secMultiCheckbox">
                <span class="secMultiHead">Choose section to show
                  <div class="checkboxAll" style="float: right;">
                    <label class="secCheckboxName col-lg-12">
                      <input type="checkbox" name="checkall" id="allSec" class="secMultiCheckboxInput">
                      <span class="checkmark"></span>
                      <span class="secMultiName">Select / Unselect All</span>
                    </label>
                  </div>
                </span>
                <form id="secMultiCheckboxForm" method="POST" name="secMultiCheckboxForm" target="_parent">
              <?php foreach($getRestrictedSections as $value) { ?>
                  <label for="checkbox_<?php echo $value->id; ?>" class="secCheckboxName col-lg-3 col-sm-12">
                    <input type="checkbox" id="checkbox_<?php echo $value->id; ?>" name="secMultiCheckbox[]" value="<?php echo $value->id; ?>" class="secMultiCheckboxInput"
                    <?php $getCompanySettings = getPosSettings($value->id);
                    if (!empty($getCompanySettings))
                    {
                      foreach($getCompanySettings as $CompanySettings)
                      {
                        if ($CompanySettings->allowed == '1')
                        {
                          echo"checked";
                        }
                      }
                    }
                    ?> >
                    <span class="checkmark"></span>
                    <span class="secMultiName"><?php echo $value->section; ?></span>
                  </label>
                <?php } ?>
                <div class="col-lg-12 submit-btn">
                  <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                </div>
                </form>
              </div>
              <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <form method="post">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" style="position: relative;" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Add New Section</h4>
                      </div>
                      <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label" for="secName">Section Name<span class="red">*</span></label>
                            <input type="text" class="form-control" placeholder="Eg. CRQ" id="secName" name="secName" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="securl">Section URL<span class="red">*</span></label>
                            <input type="text" class="form-control" placeholder="Eg. crq" id="securl" name="securl" required>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" name="addSection" id="addSection" class="btn btn-primary">Create</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
</div>

<script src="../lib/notify/bootstrap-notify.min.js"></script>

<script type="text/javascript">

  var notifySuccess = function ($message) {
    $.notify({
      message: $message,

    }, {
      type: "success",
      placement: {
        from: "top",
        align: "center",
      }
    });
  };
  var notifyAlert = function ($message) {
    $.notify({
      message: $message,

    }, {
      type: "danger",
      placement: {
        from: "top",
        align: "center",
      }
    });
  };

  $('#allSec').change(function () {
    if ($(this).prop('checked')) {
      $('.secMultiCheckbox input[type="checkbox"]').prop('checked', true);
    } else {
      $('.secMultiCheckbox input[type="checkbox"]').prop('checked', false);
    }
  });

  $(document).ready(function() {

    $("#secMultiCheckboxForm").on("submit", function(event) {
      event.preventDefault();
      var checked = new Array();
      var unchecked = new Array();
      var Position = '<?php echo $_GET['Position'] ?>';

      $(".secMultiCheckboxInput:checkbox:checked").each(function() {
        checked.push($(this).val());
      });

      $(".secMultiCheckboxInput:checkbox:unchecked").each(function() {
        unchecked.push($(this).val());
      });

      $('#loading').show();
      setTimeout(function()
      {
        $.ajax({
          url: "ajax.php",
          type: "POST",
          data: 'type=PosMultiSection&checked='+checked+'&unchecked='+unchecked+'&Position='+Position,
          async: false,
          dataType: 'JSON',
          success: function(response)
          {
            $('#loading').hide();
            if (checked == '')
            {
              notifySuccess('Section Disabled');
            }
            else
            {
              notifySuccess('Section Enabled');
            }
          },
          error: function(e){ 
            console.log(e.responseText);
          }
        });
      }, 3000);
    });
  });

</script>    