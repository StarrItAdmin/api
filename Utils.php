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
            $db = mysqli_connect($GLOBALS['ip'], 'woshadmin', '1g0taw0sh', 'woshmembership');
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
            $rows = Utils::buildObjects($query, null, $fields);
            $encoded = json_encode($rows);
            header('Content-type: application/json');
            return $encoded;
        }

        /*
         * Returns object from the database in JSON format.
         * @param $query the query that should be run to obtain objects.
         * @param $fields an array of fields that should be returned.
         */
        static public function getJSONObject($query, $id = null, $fields = null)
        {
            $rows = Utils::buildObjects($query, $id, $fields);
            $encoded = json_encode($rows[0]);
            header('Content-type: application/json');
            return $encoded;
        }

        static private function buildObjects($query, $id = null, $fields = null)
        {
            $con = Utils::getConnection();
            $stmt = $con->prepare($query);
            if ($id != null) {
                $stmt->bind_param('i', $id);
            }
            $stmt->execute();
            $row = Utils::bind_result_array($stmt);
            $rows = array();
            $count = 0;
            while ($stmt->fetch()) {
                if (is_null($fields)) {
                    $rows[$count] = $row;
                } else {
                    $dict = array();
                    foreach ($fields as $field) {
                        if (!(is_null($row[$field]))) {
                            $dict[$field] = $row[$field];
                        }
                    }
                    $rows[] = $dict;
                }
                $row = Utils::bind_result_array($stmt);
                $count++;
            }
            Utils::closeConnection($con);
            return $rows;
        }

        static function bind_result_array($stmt)
        {
            $meta = $stmt->result_metadata();
            $result = array();
            while ($field = $meta->fetch_field())
            {
                $result[$field->name] = NULL;
                $params[] = &$result[$field->name];
            }

            call_user_func_array(array($stmt, 'bind_result'), $params);
            return $result;
        }

        /**
         * Returns a copy of an array of references
         */
        static function getCopy($row)
        {
            return array_map(create_function('$a', 'return $a;'), $row);
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

        static public function updateObject($id, $type, $json, $fields) {
            $setPart = "";
            foreach($json as $key => $param) {
                if (in_array($key, $fields) && $key != "id") {
                    if ($key == 'password') {
                        $param = md5($param);
                    }
                    $setPart .= " `" . $key . "`='" . $param . "',";
                }
            }
            if (sizeof($setPart) == 0) {
                return;
            }
            $con = Utils::getConnection();
            $query = "update " . $type . " set " . substr($setPart, 0, strlen($setPart) - 1)
                . " where id = ?";
            $stmt = $con->prepare($query);
            if ($id != null) {
                $stmt->bind_param('i', $id);
            }
            $stmt->execute();
            \Utils::closeConnection($con);
        }
    }
}
?>
