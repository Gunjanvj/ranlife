<?php

defined('BASEPATH') OR exit('No direct script access allowed');
//ini_set('max_execution_time', 1000);

class Request extends CI_Controller
{
    public $data;
    public function __construct()
    {
        include APPPATH . 'third_party/fpdf/fpdf.php';
        include APPPATH . 'third_party/fpdi/fpdi.php';
        include APPPATH . 'third_party/PDFMerger.php';	
        parent::__construct();
	$this->load->helper('url');
    }
    
    public function index()
    {
        
        $this->load->view('index');
    }

    public function split() {
      
        $allowed = array('pdf');

        $filename = $_FILES['pdf_file']['name'];
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!in_array($ext, $allowed)) {
            echo 'Extension is not allowed';
            exit;
        } elseif($_FILES['pdf_file']['size'] > 2.5e+7) { //25 MB (size is also in bytes)
            echo 'File size is execeeded maximum limit 25MB';
            exit;
    	}
        $code = rand(1000, 100000);
        $file = $code . "-" . $_FILES['pdf_file']['name'];
        $file_loc = $_FILES['pdf_file']['tmp_name'];
        $file_size = $_FILES['pdf_file']['size'];
        $file_type = $_FILES['pdf_file']['type'];
        $folder = "uploads/";
        move_uploaded_file($file_loc, $folder . $file);
        //get version  
        $version = $this->pdfVersion($folder . $file);
        $new_file = $_FILES['pdf_file']['name'];
        if ($version > 1.4) {
            //ghostscript to convert php version .It converts upper version 1.5 etc to 1.4 version.
            shell_exec("gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=" . $folder . $new_file . " " . $folder . $file . "");
            $version = $this->pdfVersion($folder . $new_file);
            if ($version > 1.4) {
                echo 'PDF version is not supported';
                exit;
            }
        } else {
            $new_file = $file;
        }
        $page_arr = array();
        // How many pages?        
        $pdf = new FPDI();
        $count_val = $pdf->setSourceFile('uploads/' . $new_file);
        $pagecount = $this->input->post('pages');
        if ($pagecount == 0 || $pagecount == '' || $pagecount > $count_val) {
            $pagecount = $count_val;
        }

        // Split each page into a new PDF
        for ($i = 1; $i <= $pagecount; $i++) {
            $new_pdf = new FPDI();
            $new_pdf->AddPage();
            $new_pdf->setSourceFile('uploads/' . $new_file);
            $new_pdf->useTemplate($new_pdf->importPage($i));
            try {
                $new_filename = str_replace('.pdf', '', $new_file) . '_' . $i . ".pdf";
                $new_pdf->Output('uploads/' . $new_filename, "F");
                //echo "Page ".$i." split into ".$new_filename."<br />\n"; 
                array_push($page_arr, 'uploads/' . $new_filename);
            } catch (Exception $e) {
                echo 'Caught exception: ', $e->getMessage(), "\n";
            }
        }
        //print_r($page_arr);die;
        //if true, good; if false, zip creation failed
        # create new zip opbject
        $zip = new ZipArchive();
        # create a temp file & open it
        $tmp_file = tempnam('.', '');
        $zip->open($tmp_file, ZipArchive::CREATE);
        # loop through each file
        foreach ($page_arr as $file) {
            # download file
            $download_file = file_get_contents($file);
            #add it to the zip
            $zip->addFromString(basename($file), $download_file);
        }
        # close zip
        $zip->close();
	
        # send the file to the browser as a download
        header('Content-disposition: attachment; filename=split.zip');
        header('Content-type: application/zip');
        readfile($tmp_file);
	exit;
    }

    
    private function pdfVersion($filename)
    {
        
        $fp = @fopen($filename, 'rb');
        if (!$fp) {
            return 0;
        }
        /* Reset file pointer to the start */
        fseek($fp, 0);
        /* Read 20 bytes from the start of the PDF */
        preg_match('/\d\.\d/', fread($fp, 20), $match);
        fclose($fp);
        if (isset($match[0])) {
            return $match[0];
        } else {
            return 0;
        }
    }
    
    
    
    
}
