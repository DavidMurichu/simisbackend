<?php

$noGet=[];
$noCreate=['users'];

$tables=[
    'sch_payment_terms',
    'sch_vote_heads',
    'incomes',
    'sch_services',
    'sch_student_services',
    'member_payable_arears',
    'sch_student_service_invoices',
    'sch_vote_head_invoice_details',
    'sch_fee_structure_vote_heads',


    'sch_fee_invoices',
    'sch_subjects',
    'sch_teachers',
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
    'sch_student_class_terms',
    'sch_service_durations',
    'sch_teacher_on_duties',
    'sch_visitor_types',
    'sys_genders',
    'sch_visitor_registers',
    'sch_class_daily_recordings',
    'sch_demoted_students',

    
   

];

$allowedCreateTables=array_diff($tables, $noCreate);
$allowedGetTables=array_diff($tables, $noGet);

$data=[
    'create'=>$allowedCreateTables,
    'get'=>$allowedGetTables,
];


return $data;
