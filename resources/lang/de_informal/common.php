<?php
$de_formal = (include resource_path() . '/lang/de/' . basename(__FILE__));

$de_informal = [
    /**
     * Email Content
     */
    'email_action_help' => 'Sollte es beim Anklicken der Schaltfläche ":action_text" Probleme geben, öffne die folgende URL in Deinem Browser:',
];

return array_replace($de_formal, $de_informal);
