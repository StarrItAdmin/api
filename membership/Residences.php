<?php

namespace membership;

include_once $_SERVER['DOCUMENT_ROOT'] . '/Utils.php';

class Residences {

    /*
     * Obtains all available residences.
     * Includes name and id fields.
     *  {
     *  "name":"{name}",
     *  "id":"{id}",
     *  "plan_id": {plan_id}
     *  }
     */
    public static function getResidences() {
        exit(\Utils::getJSONObjects(
            "select r.name as name, r.id, p.name as plan_name, p.price, rp.id as plan_id from Residences r join Residence_Plans rp on r.id = rp.rid join Plans p on rp.pid = p.id;"));
    }

}

?>