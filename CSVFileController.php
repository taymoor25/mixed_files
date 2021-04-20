<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CSVFileController extends Controller
{
    public function getCsv($id, $colums ,$filename)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');
        $output = fopen("php://output", "w");
        fputcsv($output, $colums);
        $row =  DB::table('employees')->select($colums)->where('EmployeeID', '=', $id)->first();
        $list = array();
        foreach ($row as $arr) {
            $list[] = $arr;
        }
        fputcsv($output, $list);
        fclose($output);
    }
}
