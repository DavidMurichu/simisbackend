<?php

$noGet=[];
$noCreate=['users'];
$tables=[
    'sch_students',
    'users', 
    'clients',
    'audits', 
    'auth_branches',
    'sch_classes', 
    'sch_terms', 
    'sch_student_genders',
    'sys_years',
    'sch_academic_years',
    'sch_academic_year_terms',
    'sch_student_class_promotions',
    'sch_student_class_terms'

];

$allowedCreateTables=array_diff($tables, $noCreate);
$allowedGetTables=array_diff($tables, $noGet);

$data=[
    'create'=>$allowedCreateTables,
    'get'=>$allowedGetTables,
];


return $data;
