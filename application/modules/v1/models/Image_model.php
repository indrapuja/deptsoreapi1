<?php
ini_set('max_execution_time', 0); // for infinite time of execution 

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Description of Image_model
 *
 * @author Achmad Hafizh
 */
class Image_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    function convert_image_to_base64($file_path)
    {
        $type = pathinfo($file_path, PATHINFO_EXTENSION);
        $data = file_get_contents($file_path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }
 
    function convert_base64_to_image($base64_string, $output_file)
    {
        // open the output file for writing
        $ifp = fopen($output_file, 'wb');

        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode(',', $base64_string);

        // we could add validation here with ensuring count( $data ) > 1
        fwrite($ifp, base64_decode($data[1]));

        // clean up the file resource
        fclose($ifp);

        return $output_file;
    }
}
