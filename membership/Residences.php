<?php

namespace membership;

include_once $_SERVER['DOCUMENT_ROOT'] . '/Utils.php';

class Residences {

    /*
     * Obtains all available residences.
     * Includes name and id fields.
     *  {
     *  "name":"{name}",
     *  "id":"{id}"
     *  }
     */
    public static function getResidences() {
        exit(\Utils::getJSONObjects("Select * from Residences", ['name', 'id']));
    }

}

?>