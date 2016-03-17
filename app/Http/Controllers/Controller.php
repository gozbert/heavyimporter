<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\Process\Process as Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Http\Request;
use Validator;
use Redirect;
use Config;
use Session;
use DB;
use App\Flag;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function import(Request $request)
    {
        $excel_file = $request->file('excel_file');

        $validator = Validator::make($request->all(), [
            'excel_file' => 'required'
        ]);

        $validator->after(function($validator) use ($excel_file) {
            if ($excel_file->guessClientExtension()!=='xlsx') {
                $validator->errors()->add('field', 'File type is invalid - only xlsx is allowed');
            }
        });

        if ($validator->fails()) {
            return Redirect::to(route('home'))
                        ->withErrors($validator);
        }

        try {
            $fname = md5(rand()) . '.xlsx';
            $full_path = Config::get('filesystems.disks.local.root');
            $excel_file->move( $full_path, $fname );
            $flag_table = Flag::firstOrNew(['file_name'=>$fname]);
            $flag_table->imported = 0; //file was not imported
            $flag_table->save();
        }catch(\Exception $e){
            return Redirect::to(route('home'))
                        ->withErrors($e->getMessage()); //don't use this in production ok ?
        }

        //and now the interesting part
        $process = new Process('php ../artisan import:excelfile');
        $process->start();

        Session::flash('message', 'Hold on tight. Your file is being processed');
        return Redirect::to(route('home'));
    }

    public function status(Request $request)
    {
        $flag_table = DB::table('flag_table')
                        ->orderBy('created_at', 'desc')
                        ->first();
        if(empty($flag)) {
            return response()->json(['msg' => 'done']); //nothing to do
        }
        if($flag_table->imported === 1) {
            return response()->json(['msg' => 'done']);
        } else {
            $status = $flag_table->rows_imported . ' excel rows have been imported out of a total of ' . $flag_table->total_rows;
            return response()->json(['msg' => $status]);
        }
    }
}
