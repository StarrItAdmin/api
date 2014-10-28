<?php


namespace {
    include_once('ip.php');

    class Utils
    {
        /*
         * Gets a connection to the database.
         */
        static public function getConnection()
        {
            $db = mysqli_connect($GLOBALS[$ip], 'woshadmin', '1g0taw0sh', 'woshmembership');
            if (mysqli_connect_errno()) {
                echo("Connect failed: " . mysqli_connect_error());
            }
            return $db;
        }

        /*
         * Closes database connection.
         */
        static public function closeConnection($con)
        {
            mysqli_close($con);
        }

        /*
         * Returns objects from the database in JSON format.
         * @param $query the query that should be run to obtain objects.
         * @param $fields an array of fields that should be returned.
         */
        static public function getJSONObjects($query, $fields = null)
        {
            $rows = Utils::buildObjects($query, $fields);
            $encoded = json_encode($rows);
            header('Content-type: application/json');
            return $encoded;
        }

        /*
         * Returns object from the database in JSON format.
         * @param $query the query that should be run to obtain objects.
         * @param $fields an array of fields that should be returned.
         */
        static public function getJSONObject($query, $fields = null)
        {
            $rows = Utils::buildObjects($query, $fields);
            $encoded = json_encode($rows[0]);
            header('Content-type: application/json');
            return $encoded;
        }

        static private function buildObjects($query, $fields = null) {
            $con = Utils::getConnection();
            $result = mysqli_query($con, $query);
            $rows = array();
            while ($row = $result->fetch_assoc()) {
                if (is_null($fields)) {
                    $rows[] = $row;
                } else {
                    $dict = array();
                    foreach ($fields as $field) {
                        if (!(is_null($row[$field]))) {
                            $dict[$field] = $row[$field];
                        }
                    }
                    $rows[] = $dict;
                }
            }
            Utils::closeConnection($con);
            return $rows;
        }

        static public function checkForExists($query, $error)
        {
            $exists = \Utils::getJSONObjects($query);
            if (strlen($exists) < 3) {
                http_response_code(400);
                exit(json_encode(array("error" => $error)));
            }
        }

        static public function checkNotExists($query, $error)
        {
            $exists = \Utils::getJSONObjects($query);
            if (strlen($exists) > 2) {
                http_response_code(400);
                exit(json_encode(array("error" => $error)));
            }
        }
    }
}
?>
