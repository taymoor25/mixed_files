<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include '../connect/connect-new.php';
include '../inc/mysql.php';
include '../inc/functions.php';

$type = $_POST['type'];
global $db;

if ($type == 'multiSection') {
    $CompanyID = $_POST['CompanyID'];
    $checked = $_POST['checked'];
    $checkedPositionID = explode(',', $checked);
    $unchecked = $_POST['unchecked'];
    $uncheckedPositionID = explode(',', $unchecked);
    foreach ($checkedPositionID as $key) {
        $dup_query = "SELECT * FROM company_sections WHERE companyID = '$CompanyID' AND restricted_sections_ID = '$key' ";
        $dup_result = $db->query($dup_query);
        $rows = $dup_result->fetchAll(PDO::FETCH_OBJ);
        if (count($rows) > 0) {
            $update_query = "UPDATE company_sections SET allowed = '1' WHERE companyID = '$CompanyID' AND restricted_sections_ID = '$key' ";
            $updated = $db->query($update_query);
        } else {
            $insert_query = "INSERT INTO company_sections(restricted_sections_ID,companyID,allowed) VALUES('$key','$CompanyID',1)";
            $insert_stmt = $db->prepare($insert_query);
            $inserted = $insert_stmt->execute();
        }
    }

    foreach ($uncheckedPositionID as $key) {
        $query1 = "UPDATE company_sections SET allowed = '0' WHERE companyID = '$CompanyID' AND restricted_sections_ID = '$key' ";
        $update1 = $db->query($query1);
    }

    echo json_encode(array('status' => 'success'));
}

if ($type == 'PosMultiSection') {
    $Position = $_POST['Position'];
    $checked = $_POST['checked'];
    $checkedPositionID = explode(',', $checked);
    $unchecked = $_POST['unchecked'];
    $uncheckedPositionID = explode(',', $unchecked);
    foreach ($checkedPositionID as $key) {
        $dup_query = "SELECT * FROM position_sections WHERE Position = '$Position' AND restricted_sections_ID = '$key' ";
        $dup_result = $db->query($dup_query);
        $rows = $dup_result->fetchAll(PDO::FETCH_OBJ);
        if (count($rows) > 0) {
            $update_query = "UPDATE position_sections SET allowed = '1' WHERE Position = '$Position' AND restricted_sections_ID = '$key' ";
            $updated = $db->query($update_query);
        } else {
            $insert_query = "INSERT INTO position_sections(restricted_sections_ID,Position,allowed) VALUES('$key','$Position',1)";
            $insert_stmt = $db->prepare($insert_query);
            $inserted = $insert_stmt->execute();
        }
    }

    foreach ($uncheckedPositionID as $key) {
        $query1 = "UPDATE position_sections SET allowed = '0' WHERE Position = '$Position' AND restricted_sections_ID = '$key' ";
        $update1 = $db->query($query1);
    }

    echo json_encode(array('status' => 'success'));
}

// if($type == 'addStoreBudget')
// {
//     $storeBudget = $_POST['storeBudget'];
//     $location = $_POST['location'];
//     $query = "UPDATE stores SET store_budget = '".$storeBudget."' WHERE Location = '".$location."' ";
//     $update = $db->query($query);
//     echo json_encode(array('status'=>'success'));
// }

// if($type == 'getStoreBudget')
// {
//     $location = $_POST['location'];
//     $query = "SELECT * FROM stores WHERE Location = '".$location."' ";
//     $result = $db->query($query);
//     $rows = $result->fetchAll(PDO::FETCH_OBJ);
//     if (empty($rows[0]->store_budget)) {
//         $StoreBudget = '0';
//     }
//     else{
//         $StoreBudget = $rows[0]->store_budget;
//     }
//     echo json_encode(array('status'=>'success','StoreBudget'=>$StoreBudget));
// }

// if($type == 'getAllowedValue')
// {
//     $location = $_POST['location'];
//     $query = "SELECT * FROM stores WHERE location = '".$location."' ";
//     $result = $db->query($query);
//     $rows = $result->fetchAll(PDO::FETCH_OBJ);
//     if (empty($rows[0]->is_allowed)) {
//         $is_allowed = '0';
//     }
//     else{
//         $is_allowed = $rows[0]->is_allowed;
//     }
//     echo json_encode(array('status'=>'success','is_allowed'=>$is_allowed));
// }

// if($type == 'allowpublish')
// {
//     $checked = $_POST['checked'];
//     $location = $_POST['location'];

//     $query = "UPDATE stores SET `is_allowed` = '".$checked."' WHERE location = '".$location."' ";
//     $update = $db->query($query);

//     if($update){
//         echo json_encode(array('status'=>'success'));
//     }
//     else{
//         echo json_encode(array('status'=>'failed'));
//     }
// }