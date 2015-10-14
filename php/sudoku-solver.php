<?php

// Copyright [2015] [Josep Pon Farreny]
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//    http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $output = solveSudoku();
    $response = createJSONResponse($output);

    print_r($response);
}


function createJSONResponse($output) {
    $response = createResponse($output);
    return json_encode($response);
}


function createResponse($output) {
    $pattern = '/\s*Error:\s*([\s\S]*)\s*/';
    $match = preg_match($pattern, $output, $matches);

    if ($match) {
        return array('solution' => array(), 'error' => true, 
                     'error_msg' => $matches[1]);
    }

    $solution = array();
    foreach (array_filter(explode("\n", $output)) as $row) {
        $numbers = array_filter(explode(" ", $row));
        array_push($solution, $numbers);
    }

    return array('solution' => $solution, 'error' => false, 'error_msg' => "");
}


function solveSudoku() {
    $sudoku = $_POST['sudoku'];
    $sudoku_data = array();

    for ($r = 0; $r < 9; $r++) {
        $row = $sudoku[$r];
        for ($c = 0; $c < 9; $c++) {
            $val = $row[$c];
            if ($val !== '') {
                $coord_r = $r + 1;
                $coord_c = $c + 1;
                array_push($sudoku_data, "{$coord_r} {$coord_c} {$val}");
            }
        }
    }

    $output = executeSolver(implode("\n", $sudoku_data));

    /*$solution = array();
    foreach (array_filter(explode("\n", $output)) as $row) {
        $numbers = array_filter(explode(" ", $row));
        array_push($solution, $numbers);
    }*/

    return $output;
}


function executeSolver($sudoku_str) {
    $descriptors = array(
        0 => array("pipe", "r"), // stdin
        1 => array("pipe", "w"), // stdout
        2 => array("pipe", "w")  // stderr
    );

    $process = proc_open('../bin/sudoku-solver -s', $descriptors, $pipes);

    if (is_resource($process)) {
        fwrite($pipes[0], $sudoku_str);
        fclose($pipes[0]);  // The solver executes when the input is closed

        $sudoku_output = stream_get_contents($pipes[1]);
        
        fclose($pipes[1]);
        fclose($pipes[2]);
        $return_value = proc_close($process);

        if ($return_value != 0) {
            return "Error: Executing the sudoku solver (code: {$return_value})";
        }

        return $sudoku_output;
    }

    return "Error: Unknown error when calling proc_open";
}


?>
