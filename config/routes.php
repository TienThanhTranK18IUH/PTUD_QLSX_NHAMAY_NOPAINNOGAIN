<?php
$routes = array(
    'kehoach' => array(
        'controller' => 'KeHoachController',
        'action' => 'index'
    ),
    'kehoach_form_edit' => array(
        'controller' => 'KeHoachController',
        'action' => 'form_edit'
    ),
    'kehoach_update' => array(
        'controller' => 'KeHoachController',
        'action' => 'update'
    ),
    
    'kehoach_lapkehoach' => array(   
        'controller' => 'KeHoachController',
        'action' => 'lapKeHoach'
    )
    
);
?>