<?php
$de_formal = (include resource_path() . '/lang/de/' . basename(__FILE__));

$de_informal = [

    /*
    |--------------------------------------------------------------------------
    | Password Reminder Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

];

return array_replace($de_formal, $de_informal);
